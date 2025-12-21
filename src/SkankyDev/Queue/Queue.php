<?php

namespace SkankyDev\Queue;

use SkankyDev\Model\Document\JobDoc;
use SkankyDev\Model\JobCollection;
use SkankyDev\Utilities\Log;

class Queue {
	
	const PENDING = 'pending';
	const PROCESSING = 'processing';
	const SUCCESS = 'success';
	const FAILED = 'failed';

	public static function push(object $job): void {
		$jobInfo = new  JobDoc([
			'payload' => $job,
			'status' => Queue::PENDING,
			'attempts' => 0,
			'max_attempts' => 3
		]);
		
		JobCollection::_save($jobInfo);
		Log::job(get_class($job), 'queued');
	}
	


	public static function next(): ?object {
		return JobCollection::_findOne(['status' => 'pending'], ['sort' => ['created_at' => 1]]);
	}
}