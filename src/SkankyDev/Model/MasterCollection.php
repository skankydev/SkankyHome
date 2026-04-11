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
	protected array $behaviorsName = ['Timed'];
	protected array $behaviors = [];
	
	/**
	 * Connects to the MongoDB collection and loads the declared behaviors.
	 */
	public function __construct() {
		$this->collection = MongoClient::getInstance()->getCollection($this->collectionName);
		$this->loadBehaviors();
	}

	/**
	 * Instantiates all behaviors listed in $behaviorsName via MasterFactory.
	 * Behavior classes are resolved from the `class.behavior` config map.
	 * @throws BehaviorNotFoundException if a behavior key is not registered in config
	 */
	protected function loadBehaviors(): void {
		$loadedBehaviors = [];
		$class = Config::get('class.behavior');
		foreach ($this->behaviorsName as $name) {
			if(!isset($class[$name])){
				throw new BehaviorNotFoundException("La classe {$name} n'est pas defini");
			}
			$loadedBehaviors[] = MasterFactory::_make($class[$name]);
		}
		
		$this->behaviors = $loadedBehaviors;
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

		$skip = ($page - 1) * $limit;
		
		$options = [
			'limit' => $limit,
			'skip' => $skip,

		];
		
		if (!empty($sort)) {
			$options['sort'] = $sort;
		}
		
		$items = $this->find($filter, $options);
		$paginateInfo['total'] = $this->count($filter);
		return new Paginator($items,$paginateInfo);
	}
	
	/**
	 * Creates a MongoDB ObjectId from a string, or a new one if no string is provided.
	 */
	public function createId(?string $id = null): ObjectId {
		return $id ? new ObjectId($id) : new ObjectId();
	}
}