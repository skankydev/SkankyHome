<?php

namespace SkankyTest\TestCase;

use PHPUnit\Framework\TestCase;

class DebugTest extends TestCase
{
    // ── debug() ───────────────────────────────────────────────────────────────

    public function testDebugOutputsDiv(): void {
        ob_start();
        debug('hello');
        $html = ob_get_clean();

        $this->assertStringContainsString('<div class="debug-message">', $html);
        $this->assertStringContainsString('</div>', $html);
    }

    public function testDebugOutputsValue(): void {
        ob_start();
        debug('hello world');
        $html = ob_get_clean();

        $this->assertStringContainsString('hello world', $html);
    }

    public function testDebugWithCustomMessage(): void {
        ob_start();
        debug(['key' => 'val'], 'My label');
        $html = ob_get_clean();

        $this->assertStringContainsString('My label', $html);
        $this->assertStringContainsString('val', $html);
    }

    public function testDebugWithArray(): void {
        ob_start();
        debug(['a' => 1, 'b' => 2]);
        $html = ob_get_clean();

        $this->assertStringContainsString('Array', $html);
        $this->assertStringContainsString('<pre>', $html);
    }

    public function testDebugWithBoolTrue(): void {
        ob_start();
        debug(true);
        $html = ob_get_clean();

        $this->assertStringContainsString('true', $html);
    }

    public function testDebugWithBoolFalse(): void {
        ob_start();
        debug(false);
        $html = ob_get_clean();

        $this->assertStringContainsString('false', $html);
    }

    public function testDebugWithInteger(): void {
        ob_start();
        debug(42);
        $html = ob_get_clean();

        $this->assertStringContainsString('42', $html);
    }

    // ── dump() ────────────────────────────────────────────────────────────────

    public function testDumpOutputsSameAsDebug(): void {
        ob_start();
        dump('test-value');
        $html = ob_get_clean();

        $this->assertStringContainsString('<div class="debug-message">', $html);
        $this->assertStringContainsString('test-value', $html);
    }

    public function testDumpDoesNotDie(): void {
        ob_start();
        dump('alive');
        ob_get_clean();

        // If we reach this assertion, dump() did not call die()
        $this->assertTrue(true);
    }
}
