<?php

namespace App\View\Part;

use App\Model\ScenarioCollection;
use SkankyDev\Http\Request;
use SkankyDev\View\Part\MasterPart;

class ModulePartScenarioPart extends MasterPart {

	public function __construct(
		private ScenarioCollection $scenarios
	){}

	public function data(array $options): array {
		return [
			'scenarios' => $this->scenarios->paginate(
				['module_id' => $options['module']->_id],
				Request::_paginateInfo()
			),
		];
	}
}
