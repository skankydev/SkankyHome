<?php 

namespace App\Controller;

use SkankyDev\Config\Config;
use SkankyDev\Controller\MasterController;

class LiveController extends MasterController {

	public function index(){
		$effects = Config::get('leds.effects');
		return view('live.index',[
			'effects' => $effects,
		]);
	}
}