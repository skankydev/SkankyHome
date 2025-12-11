<?php 

namespace SkankyDev\Model\Document;

use DateTime;
use stdClass;
use MongoDB\BSON\ObjectID;
use MongoDB\BSON\Persistable;
use MongoDB\BSON\UTCDateTime;
use MongoDB\BSON\Document;
use MongoDB\Model\BSONArray;
use SkankyDev\Utilities\Traits\StringFacility;



#[\AllowDynamicProperties]
class MasterDocument implements Persistable {

	use StringFacility;

	public $_id;
	/*public ?DateTime $created_at = null;
	public ?DateTime $updated_at = null;*/

	static public function collectionName() : string{
		$name = get_called_class();
		$name = str_replace('Document\\', '', $name);
		$name .= 'Collection';
		return $name;
	}

	public static function find(string $id): ?static {
		$collectionClass = static::collectionName();
		if (!class_exists($collectionClass)) {
			throw new \Exception("Collection {$collectionClass} introuvable pour " . static::class);
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
	 * convert the document to be saved in database
	 */
	public function __construct(array $data = []){
		if(!empty($data)){
			$this->fill($data);
		}
	}


	/**
	 * 
	 * @return the document
	 */
	public function fill(array $data){
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
	 * convert the document to be saved in database
	 * @return array the document
	 */
	public function bsonSerialize(): stdClass|Document|array{
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
	 * convert the result to the database in document
	 * @param  array  $data the data form database
	 * @return void
	 */
	public function bsonUnserialize(array $data) : void{
		unset($data['__pclass']); 
		foreach ($data as $key => $value) {
			if($value instanceof UTCDateTime ){
				//debug($value);
				$this->{$key} = $value->toDateTime();
			}elseif($value instanceof BSONArray){
				$this->{$key} = $value->getArrayCopy();
			}else{
				$this->{$key} = $value;
			}
		}
	}	

}