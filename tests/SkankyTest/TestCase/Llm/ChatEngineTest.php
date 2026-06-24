<?php

namespace SkankyTest\TestCase\Llm;

use PHPUnit\Framework\TestCase;
use App\Llm\ChatEngine;
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
}
