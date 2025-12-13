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
	
	public function __construct() {

		$this->collection = MongoClient::getInstance()->getCollection($this->collectionName);
		$this->loadBehaviors();
	}
	
	/**
	 * Charger les behaviors définis dans la classe enfant
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
	 * Appeler une méthode sur tous les behaviors
	 */
	protected function callBehaviors(string $method, object $document): void {
		foreach ($this->behaviors as $behavior) {
			if (method_exists($behavior, $method)) {
				$behavior->{$method}($document);
			}
		}
	}
	
	/**
	 * Find - récupérer plusieurs documents
	 * MongoDB retourne automatiquement des instances du bon Document grâce à Persistable
	 */
	public function find(array $filter = [], array $options = []): array {
	
		$cursor = $this->collection->find($filter, $options);
		
		return iterator_to_array($cursor, false);
	}
	
	/**
	 * FindOne - récupérer un seul document
	 */
	public function findOne(array $filter = [], array $options = []): ?object {
		
		$result = $this->collection->findOne($filter, $options);
		
		return $result;
	}
	
	/**
	 * FindById - récupérer par ID
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
	 * Insert - insérer un nouveau document
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
	 * Update - mettre à jour un document
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
	 * Save - insert ou update automatique
	 */
	public function save(object $document): bool {
		if (!empty($document->_id)) {
			return $this->update($document);
		} else {
			return $this->insert($document);
		}
	}
	
	/**
	 * Delete - supprimer un document par ID
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
	 * Delete - supprimer un document
	 */
	public function deleteOne(MasterDocument $document): bool {
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
	 * DeleteOne - supprimer un document par filtre
	 */
	public function delete(array $filter): bool {
		try {
			$result = $this->collection->delete($filter);
			return $result->getDeletedCount();
		} catch (\Exception $e) {
			throw $e;
		}
	}
	
	/**
	 * Count - compter les documents
	 */
	public function count(array $filter = []): int {
		return $this->collection->countDocuments($filter);
	}
	
	/**
	 * Aggregate - pipeline d'agrégation MongoDB
	 */
	public function aggregate(array $pipeline): array {
		$cursor = $this->collection->aggregate($pipeline);
		return iterator_to_array($cursor, false);
	}
	
	/**
	 * Paginate - pagination simple
	 */
	public function paginate(array $filter = [], array $paginateInfo = []): Paginator  {
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
	 * Créer un ObjectId MongoDB
	 */
	public function createId(?string $id = null): ObjectId {
		return $id ? new ObjectId($id) : new ObjectId();
	}
}