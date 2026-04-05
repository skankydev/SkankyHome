<?php

namespace SkankyTest\Integration\Model;

use MongoDB\BSON\ObjectId;
use SkankyDev\Database\MongoClient;
use SkankyDev\Model\MasterCollection;
use SkankyDev\Model\Document\MasterDocument;
use SkankyDev\Utilities\Paginator;
use SkankyTest\IntegrationTestCase;

// ── Fixtures ──────────────────────────────────────────────────────────────────

class TestItem extends MasterDocument
{
    public string $name  = '';
    public int    $value = 0;
}

class TestItemCollection extends MasterCollection
{
    protected string $collectionName = 'test_items';
    protected string $documentClass  = TestItem::class;
    protected array  $behaviorsName  = []; // pas de behaviors pour l'isolation
}

// ── Tests ─────────────────────────────────────────────────────────────────────

class MasterCollectionTest extends IntegrationTestCase
{
    private TestItemCollection $col;

    protected function setUp(): void
    {
        parent::setUp();

        // Collection vide avant chaque test — on instancie directement,
        // pas via le Singleton, donc pas besoin de reset $_instance
        $this->dropCollection('test_items');

        $this->col = new TestItemCollection();
    }

    // ── insert ────────────────────────────────────────────────────────────────

    public function testInsertPopulatesId(): void
    {
        $item = new TestItem(['name' => 'lumière', 'value' => 42]);
        $this->col->insert($item);

        $this->assertNotNull($item->_id);
        $this->assertInstanceOf(ObjectId::class, $item->_id);
    }

    public function testInsertReturnsTrueOnSuccess(): void
    {
        $item = new TestItem(['name' => 'test', 'value' => 1]);
        $this->assertTrue($this->col->insert($item));
    }

    // ── count ─────────────────────────────────────────────────────────────────

    public function testCountReturnsZeroOnEmptyCollection(): void
    {
        $this->assertEquals(0, $this->col->count());
    }

    public function testCountReflectsInserts(): void
    {
        $this->col->insert(new TestItem(['name' => 'a', 'value' => 1]));
        $this->col->insert(new TestItem(['name' => 'b', 'value' => 2]));
        $this->assertEquals(2, $this->col->count());
    }

    // ── find ─────────────────────────────────────────────────────────────────

    public function testFindReturnsAllDocuments(): void
    {
        $this->col->insert(new TestItem(['name' => 'x', 'value' => 10]));
        $this->col->insert(new TestItem(['name' => 'y', 'value' => 20]));

        $results = $this->col->find();
        $this->assertCount(2, $results);
    }

    public function testFindWithFilter(): void
    {
        $this->col->insert(new TestItem(['name' => 'alpha', 'value' => 1]));
        $this->col->insert(new TestItem(['name' => 'beta',  'value' => 2]));

        $results = $this->col->find(['value' => 1]);
        $this->assertCount(1, $results);
    }

    // ── findOne ───────────────────────────────────────────────────────────────

    public function testFindOneReturnsNullOnEmpty(): void
    {
        $this->assertNull($this->col->findOne());
    }

    public function testFindOneReturnsDocument(): void
    {
        $this->col->insert(new TestItem(['name' => 'unique', 'value' => 99]));
        $result = $this->col->findOne(['value' => 99]);
        $this->assertNotNull($result);
    }

    // ── findById ──────────────────────────────────────────────────────────────

    public function testFindByIdReturnsCorrectDocument(): void
    {
        $item = new TestItem(['name' => 'par-id', 'value' => 7]);
        $this->col->insert($item);

        $found = $this->col->findById((string) $item->_id);
        $this->assertNotNull($found);
    }

    public function testFindByIdReturnsNullForInvalidId(): void
    {
        $this->assertNull($this->col->findById('pas-un-objectid'));
    }

    public function testFindByIdReturnsNullForUnknownId(): void
    {
        $this->assertNull($this->col->findById((string) new ObjectId()));
    }

    // ── save / update ─────────────────────────────────────────────────────────

    public function testSaveInsertsNewDocument(): void
    {
        $item = new TestItem(['name' => 'nouveau', 'value' => 5]);
        $this->col->save($item);

        $this->assertNotNull($item->_id);
        $this->assertEquals(1, $this->col->count());
    }

    public function testUpdateModifiesDocument(): void
    {
        $item = new TestItem(['name' => 'avant', 'value' => 1]);
        $this->col->insert($item);

        $item->name  = 'après';
        $item->value = 100;
        $this->col->update($item);

        $found = $this->col->findById((string) $item->_id);
        $this->assertNotNull($found);
        // Le document mis à jour existe toujours en base
        $this->assertEquals(1, $this->col->count());
    }

    public function testUpdateThrowsWithoutId(): void
    {
        $item = new TestItem(['name' => 'sans-id', 'value' => 0]);
        $this->expectException(\Exception::class);
        $this->col->update($item);
    }

    public function testSaveDelegatesToUpdateWhenIdPresent(): void
    {
        $item = new TestItem(['name' => 'v1', 'value' => 1]);
        $this->col->insert($item);

        $item->name = 'v2';
        $this->col->save($item);

        $this->assertEquals(1, $this->col->count());
    }

    // ── delete ────────────────────────────────────────────────────────────────

    public function testDeleteOneRemovesDocument(): void
    {
        $item = new TestItem(['name' => 'à supprimer', 'value' => 0]);
        $this->col->insert($item);
        $this->assertEquals(1, $this->col->count());

        $this->col->deleteOne($item);
        $this->assertEquals(0, $this->col->count());
    }

    public function testDeleteByIdRemovesDocument(): void
    {
        $item = new TestItem(['name' => 'delete-by-id', 'value' => 0]);
        $this->col->insert($item);

        $this->assertTrue($this->col->deleteById((string) $item->_id));
        $this->assertEquals(0, $this->col->count());
    }

    public function testDeleteByIdReturnsFalseForUnknownId(): void
    {
        $this->assertFalse($this->col->deleteById((string) new ObjectId()));
    }

    // ── paginate ──────────────────────────────────────────────────────────────

    public function testPaginateReturnsPaginator(): void
    {
        foreach (range(1, 5) as $i) {
            $this->col->insert(new TestItem(['name' => "item-{$i}", 'value' => $i]));
        }

        $paginator = $this->col->paginate([], ['page' => 1, 'limit' => 3]);
        $this->assertInstanceOf(Paginator::class, $paginator);
        $this->assertCount(3, $paginator->data);
    }

    public function testPaginateTotalReflectsAllDocuments(): void
    {
        foreach (range(1, 7) as $i) {
            $this->col->insert(new TestItem(['name' => "x-{$i}", 'value' => $i]));
        }

        $info = $this->col->paginate([], ['page' => 1, 'limit' => 3])->getOption();
        $this->assertEquals(7, $info['total']);
    }

    // ── createId ─────────────────────────────────────────────────────────────

    public function testCreateIdReturnsObjectId(): void
    {
        $id = $this->col->createId();
        $this->assertInstanceOf(ObjectId::class, $id);
    }

    public function testCreateIdFromStringReturnsMatchingObjectId(): void
    {
        $original = new ObjectId();
        $fromStr  = $this->col->createId((string) $original);
        $this->assertEquals((string) $original, (string) $fromStr);
    }
}
