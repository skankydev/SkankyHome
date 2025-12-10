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

namespace SkankyDev\Controller;

use SkankyDev\Request;
use SkankyDev\View\HtmlView;
use SkankyDev\Config\Config;
use SkankyDev\Controller\MasterController;
use SkankyDev\I18n\Localization;
use Exception;

class ErrorController extends MasterController{

	public $request;
	/**
	 * construct
	 * @param Exception $exception the exception
	 */
	public function __construct($exception){
		$this->request = Request::getInstance();
		$save['controller'] =  $this->request->controller;
		$this->request->controller = 'ErrorController';
		$save['action'] =  $this->request->action;
		$this->request->params = ['error'=>$exception];
		$this->view = new HtmlView();
		$this->view->set($save);

		if(Config::getDebug()){
			$this->request->action = 'error';
			$this->error($exception);
		}else{
			$this->request->action = 'error404';
			$this->error404($exception);
		}
		$this->view->render();
		
	}
	
	public function error(Exception $error){
		$fv['error'] = $error;
		$fv['class'] = get_class($error);
		$fv['traces'] = $error->getTrace();
		$fv['message'] = $error->getCode().': '.$error->getMessage();
		if($error instanceof \MongoDB\Driver\Exception\RuntimeException){
			$data = [];
			switch ($fv['class']) {
				case 'MongoDB\Driver\Exception\BulkWriteException':
					$data = [];
					$result = $error->getWriteResult();
					$data['error'] = $result->getWriteConcernError();
					$data['Inserted'] = $result->getInsertedCount();
					$data['Deleted'] = $result->getDeletedCount();
					$data['Modified'] = $result->getModifiedCount();
					$data['Upserted'] = $result->getUpsertedCount();
					$tmp = $error->getWriteResult()->getWriteErrors();
					$tmp = current($tmp);
					$fv['message'] = $tmp->getMessage();
					$fv['info'] = $data;
				break;
				
				default:
				break;
			}
			
		}
		$this->view->set($fv);
	}
	public function error404(Exception $error){

	}

}