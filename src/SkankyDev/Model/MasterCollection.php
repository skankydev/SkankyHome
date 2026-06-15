<?php
/**
 * Copyright (c) 2025 SCHENCK Simon
 * 
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 * @copyright     Copyright (c) SCHENCK Simon
 *
 */

namespace SkankyDev\Model;

use MongoDB\BSON\ObjectId;
use MongoDB\Collection as MongoCollection;
use SkankyDev\Config\Config;
use SkankyDev\Core\MasterFactory;
use SkankyDev\Database\MongoClient;
use SkankyDev\Utilities\Paginator;
use SkankyDev\Utilities\Traits\Singleton;

abstract class MasterCollection {

	use Singleton;
	
	protected MongoCollection $collection;
	protected string $collectionName;
	protected string $documentClass;
	protected array $behaviors = [];
	
	/**
	 * Connects to the MongoDB collection and loads the declared behaviors.
	 */
	public function __construct() {
		$this->collection = MongoClient::getInstance()->getCollection($this->collectionName);
		$this->loadBehaviors();
	}

	/**
	 * Instantiates the behaviors attached to the document class.
	 * Behaviors are declared on the document by using their companion trait
	 * (e.g. `use TimedTrait`), and resolved here by convention:
	 * a trait `...\Model\Document\Traits\FooTrait` maps to the behavior class
	 * `...\Model\Behavior\FooBehavior` (same top namespace, so it works in `App\` too).
	 * This keeps the document as the single source of truth — the trait both declares
	 * the managed properties and signals which behavior should run.
	 */
	protected function loadBehaviors(): void {
		$loadedBehaviors = [];
		foreach ($this->documentTraits($this->documentClass) as $trait) {
			$behaviorClass = str_replace('\\Document\\Traits\\', '\\Behavior\\', $trait);
			$behaviorClass = preg_replace('/Trait$/', 'Behavior', $behaviorClass);
			if ($behaviorClass !== $trait && class_exists($behaviorClass)) {
				$loadedBehaviors[] = MasterFactory::_make($behaviorClass);
			}
		}

		$this->behaviors = $loadedBehaviors;
	}

	/**
	 * Collects every trait used by the document class, including those used by
	 * its parent classes and traits nested inside other traits.
	 * @return string[] fully qualified trait names, keyed by themselves
	 */
	private function documentTraits(string $class): array {
		$traits = [];
		foreach (array_merge([$class], class_parents($class) ?: []) as $current) {
			$traits += class_uses($current) ?: [];
		}

		$stack = $traits;
		while ($stack) {
			foreach (class_uses(array_shift($stack)) ?: [] as $nested) {
				if (!isset($traits[$nested])) {
					$traits[$nested] = $nested;
					$stack[$nested]  = $nested;
				}
			}
		}

		return $traits;
	}
	
	/**
	 * Calls a hook method on every loaded behavior that implements it.
	 * @param string $method   hook name e.g. `beforeInsert`, `afterUpdate`
	 * @param object $document the document being processed
	 */
	protected function callBehaviors(string $method, object $document): void {
		foreach ($this->behaviors as $behavior) {
			if (method_exists($behavior, $method)) {
				$behavior->{$method}($document);
			}
		}
	}
	
	/**
	 * Returns all documents matching the filter.
	 * MongoDB auto-hydrates them into the correct Document class via Persistable.
	 * @param array $filter  MongoDB filter
	 * @param array $options MongoDB options (limit, skip, sort, projection, etc.)
	 */
	public function find(array $filter = [], array $options = []): array {
	
		$cursor = $this->collection->find($filter, $options);
		
		return iterator_to_array($cursor, false);
	}
	
	/**
	 * Returns the first document matching the filter, or null if none found.
	 */
	public function findOne(array $filter = [], array $options = []): ?object {
		
		$result = $this->collection->findOne($filter, $options);
		
		return $result;
	}
	
	/**
	 * Returns a document by its string ID, or null if not found or ID is invalid.
	 */
	public function findById(string $id, array $options = []): ?object {
		try {
			$objectId = new ObjectId($id);
			return $this->findOne(['_id' => $objectId]);
		} catch (\Exception $e) {
			return null;
		}
	}
	
	/**
	 * Inserts a new document into the collection.
	 * Fires beforeInsert and afterInsert behavior hooks.
	 * Populates $document->_id with the inserted ObjectId.
	 */
	public function insert(object $document): bool {
		try {

			$this->callBehaviors('beforeInsert', $document);
			$result = $this->collection->insertOne($document);
			if ($result->getInsertedId()) {
				$document->_id = $result->getInsertedId();
			}
			$this->callBehaviors('afterInsert', $document);
			
			return true;
		} catch (\Exception $e) {
			throw $e;
		}
	}
	
	/**
	 * Updates an existing document matched by its _id.
	 * Fires beforeUpdate and afterUpdate behavior hooks.
	 * @throws \Exception if the document has no _id
	 */
	public function update(object $document): bool {
		try {
			if (empty($document->_id)) {
				throw new \Exception("Cannot update document without _id");
			}

			$this->callBehaviors('beforeUpdate', $document);

			$result = $this->collection->updateOne(
				['_id' => $document->_id],
				['$set' => $document]
			);
			
			$this->callBehaviors('afterUpdate', $document);
			
			return $result->getModifiedCount() > 0;
		} catch (\Exception $e) {
			throw $e;
		}
	}
	
	/**
	 * Inserts or updates the document depending on whether _id is set.
	 */
	public function save(object $document): bool {
		if (!empty($document->_id)) {
			return $this->update($document);
		} else {
			return $this->insert($document);
		}
	}
	
	/**
	 * Deletes a document by its string ID.
	 */
	public function deleteById(string $id): bool {
		try {
			$result = $this->collection->deleteOne([
				'_id' => new ObjectId($id)
			]);
			
			return $result->getDeletedCount() > 0;
		} catch (\Exception $e) {
			throw $e;
		}
	}

	/**
	 * Deletes a document matched by its _id property.
	 */
	public function deleteOne(object $document): bool {
		try {
			$result = $this->collection->deleteOne([
				'_id' => $document->_id
			]);
			
			return $result->getDeletedCount() > 0;
		} catch (\Exception $e) {
			throw $e;
		}
	}
	
	/**
	 * Deletes documents matching a filter.
	 */
	public function delete(array $filter): bool {
		try {
			$result = $this->collection->deleteMany($filter);
			return $result->getDeletedCount();
		} catch (\Exception $e) {
			throw $e;
		}
	}
	
	/**
	 * Returns the number of documents matching the filter.
	 */
	public function count(array $filter = []): int {
		return $this->collection->countDocuments($filter);
	}
	
	/**
	 * Runs a MongoDB aggregation pipeline and returns the results as an array.
	 */
	public function aggregate(array $pipeline): array {
		$cursor = $this->collection->aggregate($pipeline);
		return iterator_to_array($cursor, false);
	}
	
	/**
	 * Returns a paginated result set wrapped in a Paginator.
	 * Merges default paginator config with the provided info (page, limit, sort).
	 * @param array $filter       MongoDB filter
	 * @param array $paginateInfo page, limit and sort — typically from Request::paginateInfo()
	 */
	public function paginate(array $filter = [], array $paginateInfo = []): Paginator {
		$paginateInfo = array_merge(Config::get('paginator'),$paginateInfo);

		$page = $paginateInfo['page'] ?? 1;
		$limit = $paginateInfo['limit'] ?? 10;
		$sort = $paginateInfo['sort'] ?? [];
		$display = $this->getDisplayField();

		// Le tri n'est accepté que sur un champ déclaré triable dans getDisplayField :
		// la définition d'affichage fait office de whitelist (champ venant de l'URL).
		// L'ordre est normalisé à 1/-1 pour éviter un sort MongoDB invalide.
		if (!empty($sort)) {
			$field = array_key_first($sort);
			if (empty($display[$field]['sort'])) {
				$sort = [];
			} else {
				$sort = [$field => ($sort[$field] < 0 ? -1 : 1)];
			}
		}

		// Un tri stable est toujours nécessaire : sans lui, skip/limit peut renvoyer
		// des résultats incohérents entre deux pages (doublons / oublis). `_id` est le
		// seul champ présent sur tout document, donc le défaut universel.
		if (empty($sort)) {
			$sort = ['_id' => -1];
		}
		$paginateInfo['sort'] = $sort;

		$skip = ($page - 1) * $limit;

		$options = [
			'limit' => $limit,
			'skip'  => $skip,
			'sort'  => $sort,
		];

		$items = $this->find($filter, $options);
		$paginateInfo['total'] = $this->count($filter);

		$paginator = new Paginator($items, $paginateInfo);
		$paginator->setDisplayField($display);
		$paginator->setDocumentClass($this->documentClass);
		return $paginator;
	}

	/**
	 * Describes the columns to render in a generic table (the `part.table` view).
	 * Default: every public field of the document (except `_id`), all sortable.
	 * Override in a concrete Collection to customise labels, sort, fake fields
	 * (resolved via the Document `__get`), or per-cell `render`/`after` callbacks.
	 *
	 * Shape: `['field' => ['label' => string, 'sort' => bool, 'render'? => callable, 'after'? => callable]]`
	 * @return array<string, array>
	 */
	public function getDisplayField(): array {
		$fields = [];
		$reflection = new \ReflectionClass($this->documentClass);
		foreach ($reflection->getProperties(\ReflectionProperty::IS_PUBLIC) as $prop) {
			$name = $prop->getName();
			if ($name === '_id') {
				continue;
			}
			$fields[$name] = [
				'label' => ucfirst(str_replace('_', ' ', $name)),
				'sort'  => true,
			];
		}
		return $fields;
	}
	
	/**
	 * Creates a MongoDB ObjectId from a string, or a new one if no string is provided.
	 */
	public function createId(?string $id = null): ObjectId {
		return $id ? new ObjectId($id) : new ObjectId();
	}
}