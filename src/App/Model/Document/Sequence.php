<?php

namespace App\Model\Document;

use SkankyDev\Model\Document\MasterDocument;

class Sequence extends MasterDocument {

	public string $name = '';
	public string $color = '';
	public int $duration = 0;
	public bool $active = true;
	public string $effect = '';
	
}