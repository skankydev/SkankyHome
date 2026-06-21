<?php

namespace SkankyTest\TestCase\Model;

use DateTime;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;
use PHPUnit\Framework\TestCase;
use SkankyDev\Model\Document\MasterDocument;

// ── Fixtures ──────────────────────────────────────────────────────────────────

enum FixtureStatus: string
{
    case A = 'a';
    case B = 'b';
}

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
    public string   $name = '';
    public ObjectId $module_id; // FK typée ObjectId (convention SkankyDev)
}

#[\AllowDynamicProperties]
class DocWithTypes extends MasterDocument
{
    public string        $title  = '';
    public FixtureStatus $status = FixtureStatus::A;
    public DateTime      $due;
}

#[\AllowDynamicProperties]
class DocWithRelationGetter extends MasterDocument
{
    public ObjectId $module_id;

    // Reproduit le pattern généré par le CrudMaker : garde isset() puis résolution.
    // Ici on renvoie un stand-in au lieu d'appeler une Collection (pas de DB en unitaire).
    public function getModule(): ?object
    {
        if (!isset($this->module_id)) {
            return null;
        }
        return (object) ['id' => (string) $this->module_id];
    }
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

    public function testFillDoesNotMassAssignId(): void
    {
        $doc = new DocFixture(['_id' => (string) new ObjectId(), 'name' => 'x']);
        // _id n'est jamais remplissable via fill (sécurité), il reste null.
        $this->assertNull($doc->_id);
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

    public function testMagicGetNonExistentPropertyReturnsNull(): void
    {
        $doc = new DocFixture();
        $this->assertNull($doc->nonExistentProperty);
    }

    public function testMagicGetCallsGetterMethod(): void
    {
        $doc             = new DocWithGetter();
        $doc->first_name = 'Simon';
        $doc->last_name  = 'S';
        $this->assertEquals('Simon S', $doc->full_name);
    }

    // ── fill : ObjectId (par type, pas par nom) ─────────────────────────────────

    public function testFillCastsStringToObjectIdForTypedProperty(): void
    {
        $id  = (string) new ObjectId();
        $doc = new DocWithRelation(['name' => 'test', 'module_id' => $id]);

        $this->assertInstanceOf(ObjectId::class, $doc->module_id);
        $this->assertEquals($id, (string) $doc->module_id);
    }

    public function testFillAcceptsObjectIdInstanceDirectly(): void
    {
        $oid = new ObjectId();
        $doc = new DocWithRelation(['module_id' => $oid]);

        $this->assertSame($oid, $doc->module_id);
    }

    public function testFillLeavesObjectIdPropertyUnsetWhenEmpty(): void
    {
        // Nouveau comportement : une FK vide n'est PAS auto-générée, on laisse le défaut (ici unset).
        $doc = new DocWithRelation(['name' => 'test', 'module_id' => '']);
        $this->assertFalse(isset($doc->module_id));
    }

    // ── fill : BackedEnum ───────────────────────────────────────────────────────

    public function testFillCastsStringToBackedEnum(): void
    {
        $doc = new DocWithTypes(['status' => 'b']);
        $this->assertSame(FixtureStatus::B, $doc->status);
    }

    public function testFillKeepsDefaultForInvalidEnumValue(): void
    {
        $doc = new DocWithTypes(['status' => 'nope']);
        $this->assertSame(FixtureStatus::A, $doc->status); // tryFrom null → défaut conservé
    }

    public function testFillAcceptsEnumInstanceDirectly(): void
    {
        $doc = new DocWithTypes(['status' => FixtureStatus::B]);
        $this->assertSame(FixtureStatus::B, $doc->status);
    }

    // ── fill : DateTime ─────────────────────────────────────────────────────────

    public function testFillCastsDatetimeLocalStringToDateTime(): void
    {
        $doc = new DocWithTypes(['due' => '2026-06-20T14:30']);
        $this->assertInstanceOf(DateTime::class, $doc->due);
        $this->assertEquals('2026-06-20 14:30', $doc->due->format('Y-m-d H:i'));
    }

    public function testFillCastsDateStringToDateTime(): void
    {
        $doc = new DocWithTypes(['due' => '2026-06-20']);
        $this->assertEquals('2026-06-20 00:00', $doc->due->format('Y-m-d H:i'));
    }

    public function testFillLeavesDateTimeUnsetWhenEmpty(): void
    {
        $doc = new DocWithTypes(['due' => '']);
        $this->assertFalse(isset($doc->due));
    }

    // ── relation accessor (pattern getXxx généré) ──────────────────────────────

    public function testRelationGetterReturnsNullWhenFkUnset(): void
    {
        $doc = new DocWithRelationGetter();
        $this->assertNull($doc->module); // __get → getModule() → garde isset → null
    }

    public function testRelationGetterResolvesWhenFkSet(): void
    {
        $doc            = new DocWithRelationGetter();
        $doc->module_id = new ObjectId();
        $this->assertNotNull($doc->module);
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

    public function testBsonSerializeConvertsEnumToScalarValue(): void
    {
        $doc    = new DocWithTypes(['status' => 'b']);
        $result = $doc->bsonSerialize();
        $this->assertSame('b', $result['status']);
    }

    public function testBsonSerializeKeepsObjectIdForFkProperty(): void
    {
        $id     = (string) new ObjectId();
        $doc    = new DocWithRelation(['name' => 'test', 'module_id' => $id]);
        $result = $doc->bsonSerialize();

        $this->assertInstanceOf(ObjectId::class, $result['module_id']);
        $this->assertEquals($id, (string) $result['module_id']);
    }

    public function testBsonSerializeOmitsUnsetFkProperty(): void
    {
        // module_id non fourni → propriété typée non initialisée → absente du payload.
        $doc    = new DocWithRelation(['name' => 'test']);
        $result = $doc->bsonSerialize();
        $this->assertArrayNotHasKey('module_id', $result);
    }

    public function testBsonSerializeDropsEmptyId(): void
    {
        // _id null (doc neuf) → retiré pour laisser MongoDB générer l'_id nativement.
        $doc    = new DocFixture(['name' => 'x']);
        $result = $doc->bsonSerialize();
        $this->assertArrayNotHasKey('_id', $result);
    }

    public function testBsonSerializeKeepsSetId(): void
    {
        $doc      = new DocFixture(['name' => 'x']);
        $doc->_id = new ObjectId();
        $result   = $doc->bsonSerialize();

        $this->assertArrayHasKey('_id', $result);
        $this->assertInstanceOf(ObjectId::class, $result['_id']);
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

    public function testBsonUnserializeCastsStringToEnum(): void
    {
        $doc = new DocWithTypes();
        $doc->bsonUnserialize(['__pclass' => 'X', 'status' => 'b']);
        $this->assertSame(FixtureStatus::B, $doc->status);
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

    public function testJsonSerializeConvertsEnumToScalarValue(): void
    {
        $doc    = new DocWithTypes(['status' => 'b']);
        $result = $doc->jsonSerialize();
        $this->assertSame('b', $result['status']);
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
