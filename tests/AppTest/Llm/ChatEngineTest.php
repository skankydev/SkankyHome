<?php

namespace AppTest\Llm;

use PHPUnit\Framework\TestCase;
use App\Llm\ChatEngine;
use App\Llm\ToolSet;
use App\Llm\Tools\MasterTool;
use App\Model\Document\Conversation;
use App\Model\Document\Message;
use SkankyDev\Utilities\HttpClient;
use SkankyDev\Utilities\HttpResult;

class ChatEngineTest extends TestCase
{
    /** HttpClient stub : renvoie un HttpResult fixe sans réseau. */
    private function fakeHttp(HttpResult $result): HttpClient {
        return new class($result) extends HttpClient {
            public function __construct(private HttpResult $result) {}
            public function post(string $url, array $data = []): HttpResult {
                return $this->result;
            }
        };
    }

    private function conversationWithUserMessage(): Conversation {
        $conv = new Conversation();              // pas de persona_id → pas d'accès Mongo
        $conv->addMessage(Message::user('salut'));
        return $conv;
    }

    public function testReplyAppendsAssistantMessage(): void {
        $body = json_encode(['choices' => [['message' => ['content' => 'Bonjour 🌿']]]]);
        $engine = new ChatEngine($this->fakeHttp(new HttpResult(200, $body)), 'http://test');

        $conv = $this->conversationWithUserMessage();
        $reply = $engine->reply($conv);

        $this->assertSame('assistant', $reply->role);
        $this->assertSame('Bonjour 🌿', $reply->content);
        $this->assertCount(2, $conv->messages);            // user + assistant
        $this->assertSame($reply, $conv->messages[1]);
    }

    public function testReplyHandlesEmptyContent(): void {
        $engine = new ChatEngine($this->fakeHttp(new HttpResult(200, '{}')), 'http://test');
        $reply = $engine->reply($this->conversationWithUserMessage());
        $this->assertSame('', $reply->content);
    }

    public function testReplyThrowsOnTransportError(): void {
        $engine = new ChatEngine($this->fakeHttp(new HttpResult(0, '', [], 'refused')), 'http://test');

        $this->expectException(\RuntimeException::class);
        $engine->reply($this->conversationWithUserMessage());
    }

    public function testReplyThrowsOnHttpError(): void {
        $engine = new ChatEngine($this->fakeHttp(new HttpResult(500, 'oops')), 'http://test');

        $this->expectException(\RuntimeException::class);
        $engine->reply($this->conversationWithUserMessage());
    }

    /** HttpClient stub renvoyant une réponse différente par appel (boucle d'agent). */
    private function sequenceHttp(array $results): HttpClient {
        return new class($results) extends HttpClient {
            private int $i = 0;
            public function __construct(private array $results) {}
            public function post(string $url, array $data = []): HttpResult {
                return $this->results[$this->i++] ?? end($this->results);
            }
        };
    }

    /** ToolSet à un seul tool anonyme renvoyant $result. */
    private function toolSet(string $name, mixed $result): ToolSet {
        return new ToolSet(new class($name, $result) extends MasterTool {
            public function __construct(private string $n, private mixed $r) {}
            public function name(): string { return $this->n; }
            public function description(): string { return 'tool de test'; }
            public function parameters(): array { return ['type' => 'object', 'properties' => (object) []]; }
            public function execute(array $args): mixed { return $this->r; }
        });
    }

    private function toolCallResponse(string $name): string {
        return json_encode(['choices' => [['message' => [
            'role'       => 'assistant',
            'content'    => null,
            'tool_calls' => [['id' => 'c1', 'function' => ['name' => $name, 'arguments' => '{}']]],
        ]]]]);
    }

    public function testReplyExecutesToolThenAnswers(): void {
        $final = json_encode(['choices' => [['message' => ['role' => 'assistant', 'content' => 'Tu as 2 projets.']]]]);

        $engine = new ChatEngine($this->sequenceHttp([
            new HttpResult(200, $this->toolCallResponse('get_projects')), // 1er tour : demande le tool
            new HttpResult(200, $final),                                  // 2e tour : répond
        ]), 'http://test');

        $conv = $this->conversationWithUserMessage();
        $reply = $engine->reply($conv, $this->toolSet('get_projects', [['name' => 'A'], ['name' => 'B']]));

        $this->assertSame('Tu as 2 projets.', $reply->content);
        // Seuls user + réponse finale sont persistés (intermédiaires éphémères).
        $this->assertCount(2, $conv->messages);
        $this->assertSame('user', $conv->messages[0]->role);
        $this->assertSame('assistant', $conv->messages[1]->role);
    }

    public function testReplyStopsAfterTooManyToolCalls(): void {
        // llama redemande toujours un tool → garde-fou maxIterations.
        $engine = new ChatEngine($this->sequenceHttp([
            new HttpResult(200, $this->toolCallResponse('loop')),
        ]), 'http://test');

        $this->expectException(\RuntimeException::class);
        $engine->reply($this->conversationWithUserMessage(), $this->toolSet('loop', 'x'));
    }
}
