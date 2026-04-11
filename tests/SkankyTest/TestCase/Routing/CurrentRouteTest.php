<?php

namespace SkankyTest\TestCase\Routing;

use PHPUnit\Framework\TestCase;
use SkankyDev\Http\Routing\Route\CurrentRoute;
use SkankyDev\Http\Routing\Route\Route;

class CurrentRouteTest extends TestCase
{
    // ── Convention-based parsing (initFromUri) ────────────────────────────────

    public function testSimpleControllerAction(): void {
        $current = new CurrentRoute('/post/add');
        $this->assertEquals('App\Controller\PostController', $current->getController());
        $this->assertEquals('add', $current->getAction());
        $this->assertEquals('App', $current->getNamespace());
    }

    public function testDefaultActionWhenMissing(): void {
        $current = new CurrentRoute('/module');
        $this->assertEquals('App\Controller\ModuleController', $current->getController());
        $this->assertEquals('index', $current->getAction());
    }

    public function testParamsAreExtracted(): void {
        $current = new CurrentRoute('/module/show/abc123');
        $this->assertEquals('show', $current->getAction());
        $this->assertEquals(['abc123'], $current->getParams());
    }

    public function testMultipleParams(): void {
        $current = new CurrentRoute('/module/show/abc/def');
        $this->assertEquals(['abc', 'def'], $current->getParams());
    }

    public function testNoParamsReturnsEmptyArray(): void {
        $current = new CurrentRoute('/module/index');
        $this->assertEquals([], $current->getParams());
    }

    public function testDashCaseControllerConvertedToPascalCase(): void {
        $current = new CurrentRoute('/my-module/index');
        $this->assertEquals('App\Controller\MyModuleController', $current->getController());
    }

    // ── Declared route parsing (initFromRoute) ────────────────────────────────

    public function testInitFromRouteWithNamedSegment(): void {
        $route   = new Route('/article/:slug', ['controller' => 'Post', 'action' => 'view'], [
            'slug' => '[a-zA-Z0-9\-]+',
        ]);
        $current = new CurrentRoute('/article/my-post', $route);

        $this->assertEquals('App\Controller\PostController', $current->getController());
        $this->assertEquals('view', $current->getAction());
        $this->assertEquals(['slug' => 'my-post'], $current->getParams());
    }

    public function testInitFromRouteWithNoSegments(): void {
        $route   = new Route('/', ['controller' => 'Home', 'action' => 'index']);
        $current = new CurrentRoute('/', $route);

        $this->assertEquals('App\Controller\HomeController', $current->getController());
        $this->assertEquals('index', $current->getAction());
        $this->assertEquals([], $current->getParams());
    }

    // ── Link array ────────────────────────────────────────────────────────────

    public function testGetLinkReturnsFullArray(): void {
        $current = new CurrentRoute('/post/show/42');
        $link    = $current->getLink();
        $this->assertArrayHasKey('namespace',  $link);
        $this->assertArrayHasKey('controller', $link);
        $this->assertArrayHasKey('action',     $link);
    }

    public function testMiddlewaresFromRoute(): void {
        $route = new Route('/', ['controller' => 'Home', 'action' => 'index']);
        $route->setMiddlewares(['Auth', 'Session']);
        $current = new CurrentRoute('/', $route);
        $this->assertEquals(['Auth', 'Session'], $current->getMiddlewares());
    }
}
