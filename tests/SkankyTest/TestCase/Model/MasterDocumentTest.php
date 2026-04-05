<?php

namespace SkankyTest\TestCase\Model;

use DateTime;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;
use PHPUnit\Framework\TestCase;
use SkankyDev\Model\Document\MasterDocument;

// ── Fixtures ──────────────────────────────────────────────────────────────────

#[\AllowDynamicProperties]
class DocFixture extends MasterDocument
{
    public string    $name       = '';
    public int       $value      = 0;
    public ?DateTime $created_at = null;
}

#[\AllowDynamicProperties]
class DocWithRelation extends MasterDocument
{
    public string  $name      = '';
    public ?string $module_id = null;
}

#[\AllowDynamicProperties]
class DocWithGetter extends MasterDocument
{
    public string $first_name = '';
    public string $last_name  = '';

    public function getFullName(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }
}

// ── Tests ─────────────────────────────────────────────────────────────────────

class MasterDocumentTest extends TestCase
{
    // ── __construct / fill ────────────────────────────────────────────────────

    public function testConstructWithDataCallsFill(): void
    {
        $doc = new DocFixture(['name' => 'Simon', 'value' => 7]);
        $this->assertEquals('Simon', $doc->name);
        $this->assertEquals(7, $doc->value);
    }

    public function testConstructEmptyLeavesDefaults(): void
    {
        $doc = new DocFixture();
        $this->assertEquals('', $doc->name);
        $this->assertEquals(0,  $doc->value);
    }

    public function testFillSetsProperties(): void
    {
        $doc = new DocFixture();
        $doc->fill(['name' => 'test', 'value' => 99]);
        $this->assertEquals('test', $doc->name);
        $this->assertEquals(99,     $doc->value);
    }

    public function testFillIgnoresUnknownKeys(): void
    {
        $doc = new DocFixture();
        $doc->fill(['unknown_key' => 'ignored']);
        $this->assertFalse(isset($doc->unknown_key));
    }

    public function testFillReturnsSelf(): void
    {
        $doc = new DocFixture();
        $this->assertSame($doc, $doc->fill([]));
    }

    // ── collectionName ────────────────────────────────────────────────────────

    public function testCollectionNameDerivedFromClass(): void
    {
        $name = DocFixture::collectionName();
        $this->assertStringEndsWith('DocFixtureCollection', $name);
        $this->assertStringNotContainsString('Document', $name);
    }

    // ── __get ─────────────────────────────────────────────────────────────────

    public function testMagicGetExistingProperty(): void
    {
        $doc       = new DocFixture();
        $doc->name = 'hello';
        $this->assertEquals('hello', $doc->name);
    }

    public function testMagicGetNonExistentPropertyReturnsFalse(): void
    {
        $doc = new DocFixture();
        $this->assertFalse($doc->nonExistentProperty);
    }

    public function testMagicGetCallsGetterMethod(): void
    {
        $doc             = new DocWithGetter();
        $doc->first_name = 'Simon';
        $doc->last_name  = 'S';
        $this->assertEquals('Simon S', $doc->full_name);
    }

    // ── bsonSerialize ─────────────────────────────────────────────────────────

    public function testBsonSerializeReturnsArray(): void
    {
        $doc        = new DocFixture();
        $doc->name  = 'test';
        $doc->value = 5;

        $result = $doc->bsonSerialize();
        $this->assertIsArray($result);
        $this->assertEquals('test', $result['name']);
        $this->assertEquals(5,      $result['value']);
    }

    public function testBsonSerializeConvertsDateTimeToUtcDateTime(): void
    {
        $doc             = new DocFixture();
        $doc->created_at = new DateTime('2025-01-01');

        $result = $doc->bsonSerialize();
        $this->assertInstanceOf(UTCDateTime::class, $result['created_at']);
    }

    // ── bsonUnserialize ───────────────────────────────────────────────────────

    public function testBsonUnserializeSetsProperties(): void
    {
        $doc = new DocFixture();
        $doc->bsonUnserialize(['__pclass' => 'X', 'name' => 'foo', 'value' => 3]);

        $this->assertEquals('foo', $doc->name);
        $this->assertEquals(3,     $doc->value);
    }

    public function testBsonUnserializeConvertsUtcToDateTime(): void
    {
        $doc = new DocFixture();
        $doc->bsonUnserialize([
            '__pclass'   => 'X',
            'name'       => '',
            'value'      => 0,
            'created_at' => new UTCDateTime(new DateTime('2025-06-01')),
        ]);

        $this->assertInstanceOf(DateTime::class, $doc->created_at);
    }

    // ── jsonSerialize ─────────────────────────────────────────────────────────

    public function testJsonSerializeReturnsArray(): void
    {
        $doc        = new DocFixture();
        $doc->name  = 'json';
        $doc->value = 42;

        $result = $doc->jsonSerialize();
        $this->assertIsArray($result);
        $this->assertEquals('json', $result['name']);
    }

    public function testJsonSerializeIsJsonEncodable(): void
    {
        $doc       = new DocFixture(['name' => 'encodable', 'value' => 1]);
        $json      = json_encode($doc);
        $this->assertJson($json);
        $this->assertStringContainsString('encodable', $json);
    }

    public function testJsonSerializeConvertsObjectIdToString(): void
    {
        $doc      = new DocFixture();
        $doc->_id = new ObjectId();

        $result = $doc->jsonSerialize();
        $this->assertIsString($result['_id']);
    }

    // ── fill with *_id fields ─────────────────────────────────────────────────

    public function testFillConvertsIdFieldToObjectId(): void
    {
        $id  = (string) new ObjectId();
        $doc = new DocWithRelation(['name' => 'test', 'module_id' => $id]);

        $this->assertIsString(ObjectId::class, $doc->module_id);
    }

    public function testFillCreatesNewObjectIdForEmptyIdField(): void
    {
        $doc = new DocWithRelation(['name' => 'test', 'module_id' => '']);
        $this->assertIsString(ObjectId::class, $doc->module_id);
    }

    // ── bsonSerialize with *_id fields ────────────────────────────────────────

    public function testBsonSerializeConvertsIdFieldToObjectId(): void
    {
        $id  = (string) new ObjectId();
        $doc = new DocWithRelation(['name' => 'test', 'module_id' => $id]);

        $result = $doc->bsonSerialize();
        $this->assertIsString(ObjectId::class, $result['module_id']);
    }

    public function testBsonSerializeCreatesNewObjectIdForEmptyIdField(): void
    {
        $doc       = new DocWithRelation();
        $doc->module_id = ''; // empty string triggers new ObjectId

        $result = $doc->bsonSerialize();
        $this->assertIsString(ObjectId::class, $result['module_id']);
    }

    // ── find() ────────────────────────────────────────────────────────────────

    public function testFindThrowsWhenCollectionDoesNotExist(): void
    {
        // DocFixture::collectionName() → something like '...DocFixtureCollection' which doesn't exist
        $this->expectException(\Exception::class);
        $this->expectExceptionCode(404);
        DocFixture::find('507f1f77bcf86cd799439011');
    }
}
