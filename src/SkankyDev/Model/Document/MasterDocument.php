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

namespace SkankyDev\Model\Document;

use DateTime;
use stdClass;
use JsonSerializable;
use MongoDB\BSON\ObjectID;
use MongoDB\BSON\Persistable;
use MongoDB\BSON\UTCDateTime;
use MongoDB\BSON\Document;
use MongoDB\Model\BSONArray;
use MongoDB\Model\BSONDocument;
use SkankyDev\Utilities\Traits\StringFacility;



#[\AllowDynamicProperties]
class MasterDocument implements JsonSerializable, Persistable {

	use StringFacility;

	public $_id;
	

	/**
	 * Derives the fully qualified Collection class name from the Document class name.
	 * e.g. `App\Model\Document\Module` → `App\Model\ModuleCollection`
	 */
	static public function collectionName(): string {
		$name = get_called_class();
		$name = str_replace('Document\\', '', $name);
		$name .= 'Collection';
		return $name;
	}

	/**
	 * Shortcut to find this document by ID via its Collection.
	 * Used by MasterFactory for automatic model binding in controllers.
	 * @throws \Exception if the corresponding Collection class does not exist
	 */
	public static function find(string $id): ?static {
		$collectionClass = static::collectionName();
		if (!class_exists($collectionClass)) {
			throw new \Exception("Collection {$collectionClass} introuvable pour " . static::class,404);
		}
		return $collectionClass::_findById($id);
	}

	/**
	 * Magic Methods user for get mutable 
	 * @param  string $name the name of the property
	 * @return mixed        the property
	 */
	public function __get($name){
		if(isset($this->$name)){
			return $this->$name; 
		}
		$methods = get_class_methods($this);
		$name = 'get'.$this->toCap($name,'_');
		if(in_array($name,$methods) !== false){
			return $this->$name();
		}
		return false;
	}

	/**
	 * Optionally fills the document from an array on construction.
	 * Note: Persistable documents are reconstructed via bsonUnserialize(), bypassing this constructor.
	 */
	public function __construct(array $data = []) {
		if(!empty($data)){
			$this->fill($data);
		}
	}


	/**
	 * Fills document properties from an array, only for declared class properties.
	 * Fields matching `*_id` are automatically cast to ObjectId.
	 */
	public function fill(array $data): static {
		$properties = get_class_vars(get_class($this));
		foreach ($properties as $key=>$value){
			if(isset($data[$key])){
				$this->{$key} = $data[$key];
				if(preg_match('/[a-zA-Z0-9_-]*_id/', $key)){
					if(empty($data[$key])){
						$this->{$key} = new ObjectID();
					}else{
						$this->{$key} = new ObjectID($data[$key]);
					}
				}
			}
		}
		return $this;
	}

	/**
	 * Serializes the document for MongoDB storage.
	 * Converts DateTime to UTCDateTime and *_id fields to ObjectId.
	 * Called automatically by the MongoDB driver on insert/update.
	 */
	public function bsonSerialize(): stdClass|Document|array {
		$prop = get_object_vars($this);
		foreach ($prop as $key=>$value) {
			$prop[$key] = $this->{$key};
			if($prop[$key] instanceof DateTime){
				$prop[$key] = new UTCDateTime($this->{$key});
			}else if(preg_match('/[a-zA-Z0-9_-]*_id/', $key)){
				if(empty($value)){
					$prop[$key] = new ObjectID();
				}else{
					$prop[$key] = new ObjectID($value);
				}
			}
		}
		return $prop;
	}

	/**
	 * Reconstructs the document from MongoDB data without going through the constructor.
	 * Called automatically by the MongoDB driver when reading documents.
	 * Converts BSON types (UTCDateTime, BSONArray, BSONDocument) to native PHP types.
	 */
	public function bsonUnserialize(array $data): void {
		unset($data['__pclass']); 
		foreach ($data as $key => $value) {
			$this->{$key} = $this->convertBsonValue($value);
		}
	}

	/**
	 * Recursively converts a BSON value to its native PHP equivalent.
	 * UTCDateTime → DateTime, BSONArray/BSONDocument → array.
	 */
	private function convertBsonValue(mixed $value): mixed {
		if ($value instanceof UTCDateTime) {
			return $value->toDateTime();
		}
		
		if ($value instanceof BSONArray || $value instanceof BSONDocument) {
			$array = $value->getArrayCopy();
			return array_map([$this, 'convertBsonValue'], $array);
		}

		return $value;
	}

	/**
	 * Serializes the document for JSON output.
	 * ObjectId fields are converted to strings for API responses.
	 */
	public function jsonSerialize(): mixed {
		$data = get_object_vars($this);
		

		foreach ($data as $key => $value) {
			if(preg_match('/[a-zA-Z0-9_-]*_id/', $key) && $value instanceof ObjectId){
				$data[$key] = (string) $value;
			}
		}
				
		return $data;
	}

}