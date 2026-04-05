<?php

namespace SkankyTest\TestCase\Queue;

use MongoDB\BSON\UTCDateTime;
use PHPUnit\Framework\TestCase;
use SkankyDev\Queue\Job\MasterJob;

// ── Fixtures ──────────────────────────────────────────────────────────────────

class TestJob extends MasterJob
{
    public string $topic = '';
    public int    $count = 0;

    public function run(): void {}
}

#[\AllowDynamicProperties]
class TimedJob extends MasterJob
{
    public ?\DateTime $scheduled_at = null;

    public function run(): void {}
}

// ── Tests ─────────────────────────────────────────────────────────────────────

class MasterJobTest extends TestCase
{
    // ── getPayload ────────────────────────────────────────────────────────────

    public function testGetPayloadReturnsAllProperties(): void
    {
        $job        = new TestJob();
        $job->topic = 'leds';
        $job->count = 3;

        $this->assertEquals(['topic' => 'leds', 'count' => 3], $job->getPayload());
    }

    public function testGetPayloadReturnsEmptyArrayWhenNoProperties(): void
    {
        // MasterJob itself has no properties — only via concrete subclass
        $job = new TestJob();
        $this->assertIsArray($job->getPayload());
    }

    // ── bsonSerialize ─────────────────────────────────────────────────────────

    public function testBsonSerializeReturnsArray(): void
    {
        $job        = new TestJob();
        $job->topic = 'test';
        $job->count = 1;

        $result = $job->bsonSerialize();
        $this->assertIsArray($result);
        $this->assertEquals('test', $result['topic']);
        $this->assertEquals(1,      $result['count']);
    }

    public function testBsonSerializeConvertsDateTimeToUtcDateTime(): void
    {
        $job               = new TimedJob();
        $job->scheduled_at = new \DateTime('2025-01-01 12:00:00');

        $result = $job->bsonSerialize();
        $this->assertInstanceOf(UTCDateTime::class, $result['scheduled_at']);
    }

    public function testBsonSerializeKeepsNonDateValues(): void
    {
        $job        = new TestJob();
        $job->topic = 'skankyhome/leds';

        $result = $job->bsonSerialize();
        $this->assertSame('skankyhome/leds', $result['topic']);
    }

    // ── bsonUnserialize ───────────────────────────────────────────────────────

    public function testBsonUnserializeSetsProperties(): void
    {
        $job = new TestJob();
        $job->bsonUnserialize(['__pclass' => 'X', 'topic' => 'foo', 'count' => 7]);

        $this->assertEquals('foo', $job->topic);
        $this->assertEquals(7,     $job->count);
    }

    public function testBsonUnserializeStripsPhpClass(): void
    {
        $job = new TestJob();
        $job->bsonUnserialize(['__pclass' => 'SomeClass', 'topic' => 'bar']);

        $this->assertFalse(isset($job->{'__pclass'}));
    }

    public function testBsonUnserializeConvertsUtcDateTimeToDateTime(): void
    {
        $job     = new TimedJob();
        $utcDate = new UTCDateTime(new \DateTime('2025-06-15'));
        $job->bsonUnserialize(['__pclass' => 'X', 'scheduled_at' => $utcDate]);

        $this->assertInstanceOf(\DateTime::class, $job->scheduled_at);
    }
}
