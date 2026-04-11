<?php

namespace SkankyTest\TestCase\Http;

use PHPUnit\Framework\TestCase;
use SkankyDev\Http\Middleware\MiddlewareInterface;
use SkankyDev\Http\Middleware\MiddlewareManager;
use SkankyDev\Http\Routing\Route\CurrentRoute;
use SkankyDev\Http\Routing\Router;
use SkankyDev\Http\Request;
use SkankyDev\Config\Config;

// Simple pass-through middleware with no constructor dependencies
class PassMiddleware implements MiddlewareInterface {
    public function handle(Request $request, callable $next): mixed {
        $_SERVER['_mw_ran'] = true;
        return $next($request);
    }
}

class MiddlewareManagerTest extends TestCase
{
    protected function setUp(): void {
        $ref = new \ReflectionProperty(Router::class, '_instance');
        $ref->setValue(null, null);
        $ref = new \ReflectionProperty(Request::class, '_instance');
        $ref->setValue(null, null);

        $_GET = $_POST = $_COOKIE = $_FILES = [];
        $_SERVER = [
            'REQUEST_METHOD' => 'GET',
            'REQUEST_SCHEME' => 'http',
            'HTTP_HOST'      => 'skankyhome.local',
            'REQUEST_URI'    => '/',
            'REMOTE_ADDR'    => '127.0.0.1',
            '_mw_log'        => '',
        ];

        // No default middlewares for these tests
        Config::set('middlewares',       []);
        Config::set('class.middlewares', [
            'Pass' => PassMiddleware::class,
        ]);
    }

    private function makeRequest(): Request {
        return new Request();
    }

    private function makeRoute(array $middlewares = []): CurrentRoute {
        Router::_findCurrentRoute('/test/index');
        $current = Router::_getCurrentRoute();
        $current->setMiddlewares($middlewares);
        return $current;
    }

    public function testPipelineCallsDestinationWithNoMiddlewares(): void {
        $manager  = new MiddlewareManager();
        $request  = $this->makeRequest();
        $route    = $this->makeRoute([]);
        $called   = false;

        $manager->run($request, $route, function($req) use (&$called) {
            $called = true;
            return 'response';
        });

        $this->assertTrue($called);
    }

    public function testMiddlewareIsExecutedBeforeDestination(): void {
        Config::set('class.middlewares', ['Pass' => PassMiddleware::class]);
        $_SERVER['_mw_ran'] = false;

        $manager = new MiddlewareManager();
        $request = $this->makeRequest();
        $route   = $this->makeRoute(['Pass']);

        $manager->run($request, $route, fn($req) => 'ok');

        $this->assertTrue($_SERVER['_mw_ran']);
    }

    public function testPipelineReturnsDestinationReturnValue(): void {
        $manager = new MiddlewareManager();
        $request = $this->makeRequest();
        $route   = $this->makeRoute([]);

        $result = $manager->run($request, $route, fn($req) => 'my-response');
        $this->assertEquals('my-response', $result);
    }

    public function testRouteMiddlewaresMergedWithDefaults(): void {
        Config::set('middlewares', ['Pass']);
        Config::set('class.middlewares', ['Pass' => PassMiddleware::class]);

        $manager = new MiddlewareManager();
        $request = $this->makeRequest();
        $route   = $this->makeRoute([]); // no extra middlewares

        $reached = false;
        $manager->run($request, $route, function($req) use (&$reached) {
            $reached = true;
            return 'ok';
        });
        $this->assertTrue($reached);
    }
}
