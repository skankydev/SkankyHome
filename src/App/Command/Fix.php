<?php 

namespace App\Command;

use App\Model\ScenarioCollection;
use SkankyDev\Command\MasterCommand;

class Fix extends MasterCommand {

	static protected string $signature = 'fix';
	static protected string $help = 'Petit command pour faire des correction en ca de besoin';


	public function run(array $arg = []): void {
		$this->info('🫏 ta encor fait une boulette simon 🤣');
		$count = ScenarioCollection::_count(['_id' => null]);
		ScenarioCollection::_delete(['_id' => null]);
		$this->text('Scenario (_id null) supprimés : '.orange($count));
	}

}