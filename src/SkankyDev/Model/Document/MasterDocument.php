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
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\Persistable;
use MongoDB\BSON\UTCDateTime;
use MongoDB\BSON\Document;
use MongoDB\Model\BSONArray;
use MongoDB\Model\BSONDocument;
use SkankyDev\Utilities\Traits\StringFacility;



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
		return null;
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
	 * Returns the declared type name of a property (single named type), or null
	 * if the property is untyped, has a union/intersection type, or doesn't exist.
	 * Drives type-aware (de)serialization without relying on naming conventions.
	 */
	private function propertyType(string $key): ?string {
		if (!property_exists($this, $key)) {
			return null;
		}
		$type = (new \ReflectionProperty($this, $key))->getType();
		return $type instanceof \ReflectionNamedType ? $type->getName() : null;
	}

	/**
	 * Fills document properties from an array, only for declared class properties.
	 * Conversion is driven by the property's declared type (Reflection):
	 * `ObjectId` properties cast strings to ObjectId, `BackedEnum` properties cast
	 * strings via tryFrom(). Scalars rely on PHP's coercive typing. `_id` is never
	 * mass-assignable.
	 */
	public function fill(array $data): static {
		foreach ($data as $key => $value) {
			if ($key === '_id' || !property_exists($this, $key)) {
				continue;
			}
			$type = $this->propertyType($key);

			if ($type === ObjectId::class) {
				if ($value instanceof ObjectId) {
					$this->{$key} = $value;
				} elseif (!empty($value)) {
					$this->{$key} = new ObjectId($value);
				}
				// valeur vide → on laisse le défaut, pas de FK bidon générée
			} elseif ($type !== null && is_subclass_of($type, \BackedEnum::class)) {
				$enum = $value instanceof \BackedEnum ? $value : $type::tryFrom($value);
				if ($enum !== null) {
					$this->{$key} = $enum;
				}
			} elseif ($type === DateTime::class) {
				if ($value instanceof DateTime) {
					$this->{$key} = $value;
				} elseif (!empty($value)) {
					try {
						// new DateTime parse l'ISO du navigateur (date "2026-06-20"
						// comme datetime-local "2026-06-20T14:30") et la plupart des formats.
						$this->{$key} = new DateTime($value);
					} catch (\Exception $e) {
						// chaîne de date non parsable → on laisse le défaut
					}
				}
			} else {
				$this->{$key} = $value;
			}
		}
		return $this;
	}

	/**
	 * Serializes the document for MongoDB storage.
	 * Converts DateTime to UTCDateTime and BackedEnum to its scalar value.
	 * ObjectId-typed properties already hold an ObjectId and are stored as-is.
	 * An empty `_id` (new document) is dropped so MongoDB generates one natively;
	 * MasterCollection::insert() then back-fills it from getInsertedId().
	 * Called automatically by the MongoDB driver on insert/update.
	 */
	public function bsonSerialize(): stdClass|Document|array {
		$prop = get_object_vars($this);
		if (empty($prop['_id'])) {
			unset($prop['_id']);
		}
		foreach ($prop as $key=>$value) {
			if($value instanceof DateTime){
				$prop[$key] = new UTCDateTime($value);
			}else if($value instanceof \BackedEnum){
				$prop[$key] = $value->value;
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
			$value = $this->convertBsonValue($value);
			$type = $this->propertyType($key);
			if ($type !== null && is_subclass_of($type, \BackedEnum::class) && !($value instanceof \BackedEnum)) {
				$value = $type::tryFrom($value);
				if ($value === null) {
					continue; // valeur stockée invalide → on garde le défaut du document
				}
			}
			$this->{$key} = $value;
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
	 * ObjectId fields are converted to strings and BackedEnum to their scalar value.
	 */
	public function jsonSerialize(): mixed {
		$data = get_object_vars($this);

		foreach ($data as $key => $value) {
			if($value instanceof ObjectId){
				$data[$key] = (string) $value;
			}else if($value instanceof \BackedEnum){
				$data[$key] = $value->value;
			}
		}

		return $data;
	}

}