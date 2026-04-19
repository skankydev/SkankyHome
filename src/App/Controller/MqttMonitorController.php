<?php

namespace App\Controller;

use SkankyDev\Controller\MasterController;

class MqttMonitorController extends MasterController {

	public function index(){
		return view('mqtt-monitor.index');
	}
}
