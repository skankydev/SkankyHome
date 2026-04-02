<?php

namespace SkankyTest\TestCase\Routing;

use PHPUnit\Framework\TestCase;
use SkankyDev\Http\Routing\Router;
use SkankyDev\Http\Routing\Route\Route;
use SkankyDev\Http\UrlBuilder;

class UrlBuilderTest extends TestCase
{
    protected function setUp(): void {
        // Reset both Singletons — UrlBuilder depends on Router::_getCurrentRoute()
        $refRouter = new \ReflectionProperty(Router::class, '_instance');
        $refRouter->setValue(null, null);

        $refBuilder = new \ReflectionProperty(UrlBuilder::class, '_instance');
        $refBuilder->setValue(null, null);

        // Establish a current route so completLink() has something to inherit from
        Router::_findCurrentRoute('/post/index');
    }

    public function testCompletLinkInheritsCurrentRoute(): void {
        $link = UrlBuilder::_completLink(['action' => 'add']);
        $this->assertEquals('App',  $link['namespace']);
        $this->assertEquals('Post', $link['controller']);
        $this->assertEquals('add',  $link['action']);
    }

    public function testMatchWithDeclaredRoute(): void {
        Router::_add('/article/:slug', ['controller' => 'Post', 'action' => 'view'], [
            'slug' => '[a-zA-Z0-9\-]*',
        ]);

        $route = UrlBuilder::_matcheWithRoute([
            'namespace'  => 'App',
            'controller' => 'Post',
            'action'     => 'view',
        ]);
        $this->assertInstanceOf(Route::class, $route);
        $this->assertEquals('/article/:slug', $route->getShema());
    }

    public function testNoMatchReturnsNull(): void {
        $result = UrlBuilder::_matcheWithRoute([
            'namespace'  => 'App',
            'controller' => 'Unknown',
            'action'     => 'index',
        ]);
        $this->assertNull($result);
    }

    public function testBuildFromMatchingRoute(): void {
        Router::_add('/article/:slug', ['controller' => 'Post', 'action' => 'view'], [
            'slug' => '[a-zA-Z0-9\-]*',
        ]);
        $url = UrlBuilder::_build(['controller' => 'Post', 'action' => 'view', 'params' => ['youpi-test']]);
        $this->assertEquals('/article/youpi-test', $url);
    }

    public function testBuildFromDefaultConvention(): void {
        $url = UrlBuilder::_build(['controller' => 'Message', 'action' => 'view', 'params' => ['youpi-test']]);
        $this->assertEquals('/message/view/youpi-test', $url);
    }

    public function testBuildOmitsDefaultAction(): void {
        $url = UrlBuilder::_build(['controller' => 'Module', 'action' => 'index']);
        $this->assertEquals('/module', $url);
    }

    public function testAddGetAppendsQueryString(): void {
        $url = UrlBuilder::_addGet('/article/youpi-test', ['page' => 1, 'order' => 'field']);
        $this->assertEquals('/article/youpi-test?page=1&order=field', $url);
    }
}
