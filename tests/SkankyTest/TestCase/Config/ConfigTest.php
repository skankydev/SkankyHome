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

    // ── current namespace ─────────────────────────────────────────────────────

    public function testGetCurentNamespaceFallsBackToDefault(): void {
        $this->assertEquals('App', Config::getCurentNamespace());
    }

    public function testSetAndGetCurentNamespace(): void {
        Config::setCurentNamespace('Admin');
        $this->assertEquals('Admin', Config::getCurentNamespace());
    }

    // ── named getters (remaining) ─────────────────────────────────────────────

    public function testGetModuleListReturnsNullWhenNotSet(): void {
        $this->assertNull(Config::getModuleList());
    }

    public function testGetModuleListReturnsValue(): void {
        Config::set('Module', ['App', 'Admin']);
        $this->assertEquals(['App', 'Admin'], Config::getModuleList());
    }

    public function testGetBehaviorReturnsNullWhenNotSet(): void {
        Config::set('class.behavior', null);
        $this->assertNull(Config::getBehavior());
    }

    public function testGetBehaviorReturnsValue(): void {
        Config::set('class.behavior', ['Timed' => 'SkankyDev\\Model\\Behavior\\TimedBehavior']);
        $result = Config::getBehavior();
        $this->assertArrayHasKey('Timed', $result);
    }

    public function testGetVersionReturnsNullWhenNotSet(): void {
        $this->assertNull(Config::getVersion());
    }

    public function testGetVersionReturnsValue(): void {
        Config::set('skankydev.version', '2.0.0');
        $this->assertEquals('2.0.0', Config::getVersion());
    }

    // ── initConf ──────────────────────────────────────────────────────────────

    public function testInitConfLoadsConfigFromBasePath(): void {
        // Save and clear the current config so initConf() doesn't return early
        $saved = Config::$conf;
        Config::$conf = null;

        // Use the real project path — the config files exist there
        $basePath = str_replace('/', DIRECTORY_SEPARATOR, 'E:/Dev/SkankyHome');
        Config::initConf($basePath);

        // After initConf, the default namespace should be set
        $this->assertEquals('App', Config::getDefaultNamespace());

        // Restore
        Config::$conf = $saved;
    }

    public function testInitConfSkipsWhenAlreadyLoaded(): void {
        // Config is already set by bootstrap — initConf should be a no-op
        $before = Config::get('default.namespace');
        Config::initConf(); // should do nothing since conf is not empty
        $this->assertEquals($before, Config::get('default.namespace'));
    }
}
