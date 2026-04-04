<?php

namespace SkankyTest\TestCase\Http;

use PHPUnit\Framework\TestCase;
use SkankyDev\Http\Request;

class RequestTest extends TestCase
{
    protected function setUp(): void {
        // Reset Singleton so each test gets a fresh instance
        $ref = new \ReflectionProperty(Request::class, '_instance');
        $ref->setValue(null, null);

        // Clean slate superglobals
        $_GET    = [];
        $_POST   = [];
        $_COOKIE = [];
        $_FILES  = [];
        $_SERVER = [
            'REQUEST_METHOD' => 'GET',
            'REQUEST_SCHEME' => 'http',
            'HTTP_HOST'      => 'skankyhome.local',
            'REQUEST_URI'    => '/',
            'REMOTE_ADDR'    => '127.0.0.1',
        ];
    }

    private function makeRequest(): Request {
        return new Request();
    }

    // ── Server info ───────────────────────────────────────────────────────────

    public function testMethod(): void {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->assertEquals('POST', $this->makeRequest()->method());
    }

    public function testMethodIsUppercased(): void {
        $_SERVER['REQUEST_METHOD'] = 'get';
        $this->assertEquals('GET', $this->makeRequest()->method());
    }

    public function testScheme(): void {
        $_SERVER['REQUEST_SCHEME'] = 'https';
        $this->assertEquals('https', $this->makeRequest()->sheme());
    }

    public function testHost(): void {
        $_SERVER['HTTP_HOST'] = 'skankyhome.local';
        $this->assertEquals('skankyhome.local', $this->makeRequest()->host());
    }

    public function testUri(): void {
        $_SERVER['REQUEST_URI'] = '/module/show/abc?page=2';
        $this->assertEquals('/module/show/abc', $this->makeRequest()->uri());
    }

    public function testFullUri(): void {
        $_SERVER['REQUEST_URI'] = '/module/show/abc?page=2';
        $this->assertEquals('/module/show/abc?page=2', $this->makeRequest()->fullUri());
    }

    public function testIp(): void {
        $_SERVER['REMOTE_ADDR'] = '192.168.1.100';
        $this->assertEquals('192.168.1.100', $this->makeRequest()->ip());
    }

    public function testIpFromProxy(): void {
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '10.0.0.1, 10.0.0.2';
        $this->assertEquals('10.0.0.1', $this->makeRequest()->ip());
    }

    public function testUserAgent(): void {
        $_SERVER['HTTP_USER_AGENT'] = 'TestBot/1.0';
        $this->assertEquals('TestBot/1.0', $this->makeRequest()->userAgent());
    }

    // ── GET / POST / input ────────────────────────────────────────────────────

    public function testQueryReturnsGetParam(): void {
        $_GET = ['page' => '3', 'search' => 'led'];
        $req  = $this->makeRequest();
        $this->assertEquals('3', $req->query('page'));
        $this->assertEquals('led', $req->query('search'));
        $this->assertNull($req->query('missing'));
        $this->assertEquals('default', $req->query('missing', 'default'));
    }

    public function testQueryWithNoKeyReturnsAll(): void {
        $_GET = ['a' => '1', 'b' => '2'];
        $this->assertEquals(['a' => '1', 'b' => '2'], $this->makeRequest()->query());
    }

    public function testPostReturnsPostParam(): void {
        $_POST = ['name' => 'Simon', 'type' => 'scenario'];
        $req   = $this->makeRequest();
        $this->assertEquals('Simon', $req->post('name'));
        $this->assertNull($req->post('missing'));
    }

    public function testInputMergesGetAndPost(): void {
        $_GET  = ['page' => '1'];
        $_POST = ['name' => 'Simon'];
        $req   = $this->makeRequest();
        $this->assertEquals('Simon', $req->input('name'));
        $this->assertEquals('1', $req->input('page'));
    }

    public function testInputPostTakesPriorityOverGet(): void {
        $_GET  = ['name' => 'from-get'];
        $_POST = ['name' => 'from-post'];
        $this->assertEquals('from-post', $this->makeRequest()->input('name'));
    }

    public function testMagicGetProxiesToInput(): void {
        $_POST = ['color' => '#ff0000'];
        $req   = $this->makeRequest();
        $this->assertEquals('#ff0000', $req->color);
    }

    // ── Headers ───────────────────────────────────────────────────────────────

    public function testHeaderParsedFromServer(): void {
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $req = $this->makeRequest();
        $this->assertEquals('XMLHttpRequest', $req->header('x-requested-with'));
    }

    public function testContentTypeHeaderParsed(): void {
        $_SERVER['CONTENT_TYPE'] = 'application/json';
        $req = $this->makeRequest();
        $this->assertEquals('application/json', $req->header('content-type'));
    }

    public function testIsAjax(): void {
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->assertTrue($this->makeRequest()->isAjax());

        $ref = new \ReflectionProperty(Request::class, '_instance');
        $ref->setValue(null, null);
        $_SERVER = ['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/'];
        $this->assertFalse($this->makeRequest()->isAjax());
    }

    public function testWantsJson(): void {
        $_SERVER['HTTP_ACCEPT'] = 'application/json';
        $this->assertTrue($this->makeRequest()->wantsJson());
    }

    // ── Cookies ───────────────────────────────────────────────────────────────

    public function testCookieAccess(): void {
        $_COOKIE = ['session_hint' => 'abc123'];
        $req = $this->makeRequest();
        $this->assertEquals('abc123', $req->cookie('session_hint'));
        $this->assertNull($req->cookie('missing'));
    }

    // ── Pagination ────────────────────────────────────────────────────────────

    public function testPaginateInfo(): void {
        $_GET = ['page' => '3', 'field' => 'name', 'order' => '-1'];
        $info = $this->makeRequest()->paginateInfo();
        $this->assertEquals(3, $info['page']);
        $this->assertEquals(['name' => -1], $info['sort']);
    }

    public function testPaginateInfoDefaultsToPageOne(): void {
        $_GET = [];
        $info = $this->makeRequest()->paginateInfo(['_id' => -1]);
        $this->assertEquals(1, $info['page']);
        $this->assertEquals(['_id' => -1], $info['sort']);
    }
}
