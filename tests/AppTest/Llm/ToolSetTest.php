<?php

namespace AppTest\Llm;

use PHPUnit\Framework\TestCase;
use App\Llm\ToolSet;
use App\Llm\Tools\MasterTool;

/** Tool factice pour les tests, sans dépendance externe. */
class FakeTool extends MasterTool {
	public function __construct(private string $toolName = 'fake', private mixed $result = 'ok') {}
	public function name(): string { return $this->toolName; }
	public function description(): string { return 'tool de test'; }
	public function parameters(): array { return ['type' => 'object', 'properties' => (object) []]; }
	public function execute(array $args): mixed { return $this->result; }
}

class ToolSetTest extends TestCase
{
    public function testEmptySet(): void {
        $set = new ToolSet();
        $this->assertTrue($set->isEmpty());
        $this->assertSame([], $set->definitions());
    }

    public function testDefinitionsFormat(): void {
        $set = new ToolSet(new FakeTool('list_things'));
        $defs = $set->definitions();

        $this->assertCount(1, $defs);
        $this->assertSame('function', $defs[0]['type']);
        $this->assertSame('list_things', $defs[0]['function']['name']);
        $this->assertArrayHasKey('parameters', $defs[0]['function']);
    }

    public function testHasAndExecuteDispatch(): void {
        $set = new ToolSet(new FakeTool('answer', 42));
        $this->assertTrue($set->has('answer'));
        $this->assertFalse($set->has('autre'));
        $this->assertSame(42, $set->execute('answer', []));
    }

    public function testExecuteUnknownToolThrows(): void {
        $set = new ToolSet(new FakeTool('answer'));
        $this->expectException(\RuntimeException::class);
        $set->execute('inconnu', []);
    }

    public function testForChatBuildsNonEmptySet(): void {
        $set = ToolSet::forChat();
        $this->assertFalse($set->isEmpty());
        foreach (['list_projects', 'list_tasks', 'add_task', 'update_task_status', 'add_project'] as $name) {
            $this->assertTrue($set->has($name), "Tool manquant dans forChat() : {$name}");
        }
    }
}
