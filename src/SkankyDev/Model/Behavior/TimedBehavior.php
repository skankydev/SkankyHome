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

namespace SkankyDev\Model\Behavior;

use SkankyDev\Model\Behavior\MasterBehavior;
use MongoDB\BSON\UTCDateTime as MongoDate;
use DateTime;

class TimedBehavior extends MasterBehavior {
	
	public function __construct(){
	}

	public function beforeInsert($data,$entity=null){
		$data->created_at = new DateTime;
		$data->updated_at = new DateTime;
	}


	public function afterInsert($data,$entity=null){

	}

	public function beforeUpdate($data,$entity=null){
		$data->updated_at = new DateTime;
	}

	public function afterUpdate($data,$entity=null){

	}

	public function beforeCreateEntity($data,$entity=null){

	}

	public function afterCreateEntity($data,$entity=null){
		
	}
	
}
