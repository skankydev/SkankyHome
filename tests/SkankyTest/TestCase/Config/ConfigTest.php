<?php

namespace SkankyTest\TestCase\Config;

use PHPUnit\Framework\TestCase;
use SkankyDev\Config\Config;

class ConfigTest extends TestCase
{
    /** Snapshot of the bootstrap config so we can restore it after each test. */
    private static array $bootstrapConf;

    public static function setUpBeforeClass(): void {
        self::$bootstrapConf = Config::$conf;
    }

    protected function setUp(): void {
        // Restore bootstrap state before each test
        Config::$conf = self::$bootstrapConf;
    }

    // ── get / set ─────────────────────────────────────────────────────────────

    public function testGetReturnsSetValue(): void {
        Config::set('test.key', 'hello');
        $this->assertEquals('hello', Config::get('test.key'));
    }

    public function testGetReturnsNullForMissingKey(): void {
        $this->assertNull(Config::get('does.not.exist'));
    }

    public function testSetOverridesExistingValue(): void {
        Config::set('test.key', 'first');
        Config::set('test.key', 'second');
        $this->assertEquals('second', Config::get('test.key'));
    }

    public function testGetNestedDotNotation(): void {
        Config::set('a.b.c', 'deep');
        $this->assertEquals('deep', Config::get('a.b.c'));
    }

    // ── named getters ─────────────────────────────────────────────────────────

    public function testGetDefaultNamespace(): void {
        $this->assertEquals('App', Config::getDefaultNamespace());
    }

    public function testGetDefaultAction(): void {
        $this->assertEquals('index', Config::getDefaultAction());
    }

    public function testGetDebugReturnsNullWhenNotSet(): void {
        $this->assertNull(Config::getDebug());
    }

    public function testGetDebugReturnsValueWhenSet(): void {
        Config::set('debug', true);
        $this->assertTrue(Config::getDebug());
    }

    public function testGetDbConfReturnsNullWhenNotSet(): void {
        $this->assertNull(Config::getDbConf());
    }

    public function testGetDbConfReturnsConfiguredValue(): void {
        Config::set('db.default', ['host' => 'localhost']);
        $this->assertEquals(['host' => 'localhost'], Config::getDbConf());
    }

    public function testGetHelperReturnsNullWhenNotSet(): void {
        $this->assertNull(Config::getHelper());
    }

    // ── current namespace ─────────────────────────────────────────────────────

    public function testGetCurentNamespaceFallsBackToDefault(): void {
        $this->assertEquals('App', Config::getCurentNamespace());
    }

    public function testSetAndGetCurentNamespace(): void {
        Config::setCurentNamespace('Admin');
        $this->assertEquals('Admin', Config::getCurentNamespace());
    }
}
