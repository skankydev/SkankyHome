<?php 

namespace App\Model;

use SkankyDev\Model\MasterCollection;
//use SkankyDev\Model\Behavior\TimedBehavior;
use App\Model\Document\Sequence;

class SequenceCollection extends MasterCollection {

	protected string $collectionName = 'sequences';
	protected string $documentClass = Sequence::class;
	/*protected array $behaviors = [
		TimedBehavior::class
	];*/
	
	public function findActive(): array {
		return $this->find(['active' => true]);
	}

}