<?php 

namespace SkankyDev\Queue\Job;

use DateTime;
use stdClass;
use MongoDB\BSON\ObjectID;
use MongoDB\BSON\Persistable;
use MongoDB\BSON\UTCDateTime;
use MongoDB\BSON\Document;
use MongoDB\Model\BSONArray;
use MongoDB\Model\BSONDocument;

abstract class MasterJob implements Persistable {


	abstract public function run(): void;

	public function getPayload(): array {
		$data = get_object_vars($this);
		return $data;
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
			$this->{$key} = $this->convertBsonValue($value);
		}
	}

	private function convertBsonValue($value) {
		if ($value instanceof UTCDateTime) {
			return $value->toDateTime();
		}
		
		if ($value instanceof BSONArray || $value instanceof BSONDocument) {
			$array = $value->getArrayCopy();
			return array_map([$this, 'convertBsonValue'], $array);
		}

		return $value;
	}
}