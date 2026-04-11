<?php 

namespace SkankyDev\Queue\Job;

use DateTime;
use stdClass;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\Persistable;
use MongoDB\BSON\UTCDateTime;
use MongoDB\BSON\Document;
use MongoDB\Model\BSONArray;
use MongoDB\Model\BSONDocument;

/**
 * Base class for all queue jobs.
 * Implements Persistable so the job and its properties are stored in MongoDB
 * and reconstructed without going through the constructor on retrieval.
 * Concrete jobs set their payload as typed properties in their constructor,
 * then implement run() with the actual business logic.
 */
abstract class MasterJob implements Persistable {

	/** Executes the job. Called by QueueWork when the job is processed. */
	abstract public function run(): void;

	/**
	 * Returns all job properties as an array.
	 * Useful for debugging or logging the job payload.
	 */
	public function getPayload(): array {
		$data = get_object_vars($this);
		return $data;
	}

	/**
	 * Serializes the job for MongoDB storage.
	 * Converts DateTime properties to UTCDateTime.
	 * Called automatically by the MongoDB driver.
	 */
	public function bsonSerialize(): stdClass|Document|array {
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
	 * Reconstructs the job from MongoDB data without calling the constructor.
	 * Called automatically by the MongoDB driver when the JobDoc is read.
	 */
	public function bsonUnserialize(array $data): void {
		unset($data['__pclass']); 
		foreach ($data as $key => $value) {
			$this->{$key} = $this->convertBsonValue($value);
		}
	}

	/**
	 * Recursively converts BSON types to native PHP types.
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
}