<?php

namespace SkankyTest\TestCase\Http;

use App\Middlewares\BiduleMiddleware;
use App\Middlewares\TrucMiddleware;
use PHPUnit\Framework\TestCase;
use SkankyDev\Http\Middleware\Attribute\Middleware;
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

// Fixture : un controller dont les middlewares sont déclarés par attributs.
// #[Middleware] sur la classe → tout le controller ; sur la méthode → cette action.
#[Middleware(TrucMiddleware::class)]
class AnnotatedController {

    #[Middleware(BiduleMiddleware::class)]
    public function edit(): string {
        return 'edit-ok';
    }

    public function index(): string {
        return 'index-ok';
    }
}

// Fixtures dédiées au test d'exécution : middlewares avec trace PARTAGÉE, pour
// vérifier l'ordre oignon entrelacé indépendamment des middlewares POC de l'app.
class OnionTrace {
    public static array $steps = [];
}

class AlphaMiddleware implements MiddlewareInterface {
    public function handle(Request $request, callable $next): mixed {
        OnionTrace::$steps[] = 'Alpha:before';
        $response = $next($request);
        OnionTrace::$steps[] = 'Alpha:after';
        return $response;
    }
}

class BetaMiddleware implements MiddlewareInterface {
    public function handle(Request $request, callable $next): mixed {
        OnionTrace::$steps[] = 'Beta:before';
        $response = $next($request);
        OnionTrace::$steps[] = 'Beta:after';
        return $response;
    }
}

#[Middleware(AlphaMiddleware::class)]
class OnionController {
    #[Middleware(BetaMiddleware::class)]
    public function edit(): string {
        return 'edit-ok';
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

    // ── Middlewares déclarés par attributs sur le controller/action ─────────────

    public function testAttributeCollectsClassThenMethodMiddlewares(): void {
        $manager = new MiddlewareManager();

        // Classe d'abord (garde tout le controller), puis l'action
        $this->assertSame(
            [TrucMiddleware::class, BiduleMiddleware::class],
            $manager->attributeMiddlewares(AnnotatedController::class, 'edit')
        );
    }

    public function testAttributeCollectsOnlyClassWhenActionHasNone(): void {
        $manager = new MiddlewareManager();

        $this->assertSame(
            [TrucMiddleware::class],
            $manager->attributeMiddlewares(AnnotatedController::class, 'index')
        );
    }

    public function testAttributeReturnsEmptyForUnknownController(): void {
        $manager = new MiddlewareManager();

        $this->assertSame([], $manager->attributeMiddlewares('App\\Controller\\Nope', 'index'));
    }

    public function testAttributeMiddlewaresRunInOnionOrder(): void {
        OnionTrace::$steps = [];

        // CurrentRoute stub pointant sur la fixture annotée (hors convention de routing)
        $route = new class('/onion/edit') extends CurrentRoute {
            public function getController(): string { return OnionController::class; }
            public function getAction(): string     { return 'edit'; }
            public function getMiddlewares(): array  { return []; }
        };

        $manager  = new MiddlewareManager();
        $response = $manager->run($this->makeRequest(), $route, function ($req) {
            OnionTrace::$steps[] = 'controller';
            return 'controller-response';
        });

        $this->assertSame('controller-response', $response);

        // Ordre oignon entrelacé : Alpha (classe) enveloppe Beta (action) qui
        // enveloppe le controller.
        $this->assertSame(
            ['Alpha:before', 'Beta:before', 'controller', 'Beta:after', 'Alpha:after'],
            OnionTrace::$steps
        );
    }
}
