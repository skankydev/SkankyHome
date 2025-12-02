<?php 
/**
 * Copyright (c) 2015 SCHENCK Simon
 * 
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 * @copyright     Copyright (c) SCHENCK Simon
 *
 */

namespace SkankyDev\Database;

use MongoDB\Client;
use MongoDB\Collection;
use MongoDB\Database;
use MongoDB\Driver\Manager;
use SkankyDev\Config\Config;
use SkankyDev\Utilities\Traits\Singleton;

class MongoClient {

	
	use Singleton;

	private $manager;
	private $client;
	private $dbName;

	public function __construct(){
		
		$dbConnect = Config::getDbConf('MongoDB');
		$uri = 'mongodb://';
		if(!empty($dbConnect['username'])){
			$uri .= $dbConnect['username'].':'.$dbConnect['password'].'@';
		}
		$uri .= $dbConnect['host'].':'.$dbConnect['port'];

		$this->manager = new Manager($uri);
		$this->client = new Client($uri);
		$this->dbName = $dbConnect['database'];
	}

	public function getDbName(){
		return $this->dbName;
	}

	public function getDatabase($name =''){
		if (empty($name)) {
			$name = $this->dbName;
		}
		return $this->client->selectDatabase($name);
	}

	public function getCollection($name){
		return $this->client->selectCollection($this->dbName,$name);
	}

	public function getManager(){
		return $this->manager;
	}

	public function createCollection($name,$option){
		$database = new Database($this->manager,$this->dbName);
		$result = $database->createCollection($name,$option);
		return $result;
	}

	public function createIndex($name,$index){
		$collection = $this->getCollection($name);
		return $collection->createIndexes($index);
	}
}
