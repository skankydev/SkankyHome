<?php 

namespace SkankyDev\Command;

use DateTime;
use SkankyDev\Command\MasterCommand;
use SkankyDev\Model\JobCollection;
use SkankyDev\Queue\Queue;
use SkankyDev\Utilities\Log;

class QueueWork extends MasterCommand {


	static protected string $signature = 'queue-worker';
	static protected string $help = 'La description';

	public function run(array $arg = []): void {
		$this->info('🚀 Queue worker started');
		
		while (true) {
			$jobInfo = Queue::next();
			
			if (!$jobInfo) {
				sleep(1);
				continue;
			}
			
			$this->processJob($jobInfo);
		}
	}

	
	private function processJob(object $jobInfo): void {
		try {
			$jobInfo->status = Queue::PROCESSING;
			$jobInfo->started_at  = new DateTime;
			JobCollection::_save($jobInfo);

			$job = $jobInfo->payload;
			$job->run();
			
			$jobInfo->status = Queue::SUCCESS;
			$jobInfo->completed_at  = new DateTime;
			JobCollection::_save($jobInfo);
			
		} catch (\Exception $e) {
			Log::error($e);
			$jobInfo->attempts += 1;
			$jobInfo->status = Queue::PENDING;
			$jobInfo->error = $e->getMessage();
			if($jobInfo->attempts >= $jobInfo->max_attempts){
				$jobInfo->status = Queue::FAILED;
			}
			JobCollection::_save($jobInfo);
			
		}
	}
	
}