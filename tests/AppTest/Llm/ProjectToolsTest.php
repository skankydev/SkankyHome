<?php

namespace AppTest\Llm;

use PHPUnit\Framework\TestCase;
use App\Llm\Tools\AddProjectTool;
use App\Llm\Tools\AddTaskTool;
use App\Llm\Tools\ListTasksTool;
use App\Llm\Tools\UpdateTaskStatusTool;

/**
 * Couvre la validation des arguments des tools (chemins d'erreur), qui passe
 * avant tout accès Mongo — donc testable sans base. Les chemins nominaux
 * (lecture/écriture réelle) sont validés en bout à bout avec llama.
 */
class ProjectToolsTest extends TestCase
{
    public function testAddProjectRequiresName(): void {
        $this->assertArrayHasKey('error', (new AddProjectTool())->execute(['name' => '  ']));
    }

    public function testAddProjectRejectsInvalidStatus(): void {
        $this->assertArrayHasKey('error', (new AddProjectTool())->execute(['name' => 'X', 'status' => 'bidon']));
    }

    public function testAddTaskRequiresProjectId(): void {
        $this->assertArrayHasKey('error', (new AddTaskTool())->execute(['project_id' => '', 'name' => 'X']));
    }

    public function testAddTaskRequiresName(): void {
        $this->assertArrayHasKey('error', (new AddTaskTool())->execute(['project_id' => 'abc', 'name' => '']));
    }

    public function testUpdateTaskStatusRequiresTaskId(): void {
        $this->assertArrayHasKey('error', (new UpdateTaskStatusTool())->execute(['task_id' => '', 'status' => 'done']));
    }

    public function testUpdateTaskStatusRejectsInvalidStatus(): void {
        $this->assertArrayHasKey('error', (new UpdateTaskStatusTool())->execute(['task_id' => 'abc', 'status' => 'bidon']));
    }

    public function testListTasksRequiresProjectId(): void {
        $this->assertArrayHasKey('error', (new ListTasksTool())->execute(['project_id' => '']));
    }

    public function testStatusEnumExposedInDefinition(): void {
        $params = (new UpdateTaskStatusTool())->definition()['function']['parameters'];
        $this->assertContains('task_id', $params['required']);
        $this->assertContains('done', $params['properties']['status']['enum']);
    }
}
