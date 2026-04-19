<?php

namespace App\Controller;

use SkankyDev\Config\Config;
use SkankyDev\Controller\MasterController;

class EffectPreviewController extends MasterController {

	public function index(){
		$effects = Config::get('leds.effects');
		return view('effect-preview.index', ['effects' => $effects]);
	}
}
