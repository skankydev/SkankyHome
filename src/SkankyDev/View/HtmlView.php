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
namespace SkankyDev\View;

use SkankyDev\Utilities\Traits\HtmlHelper;
use SkankyDev\Utilities\Traits\StringFacility;




class HtmlView {
	
	use HtmlHelper, StringFacility;

	public $keywords = '';
	public $title = '';
	public $meta = [];
	public $css = '';
	public $js = '';
	public $content = '';

	public $script = '';

	public $layout = 'layout.default';
	public $helpers = [];

	function __construct(
		protected string $viewName = '',
		protected array $data = []
	){
		
	}

	public function makePath(string $name): string{
		$name = $this->dotToFolder($name);
		$fileName = VIEW_FOLDER.DS.$name.'.php';
		if(!file_exists($fileName)){
			throw new \Exception("the file : {$fileName} does not exist", 601);
		}
		return $fileName;
	}

	public function viewPath(){
		return $this->makePath($this->viewName);
	}

	public function layoutPath(){
		return $this->makePath($this->layout);
	}

	/**
	 * render the view
	 */
	public function render(): string {
		// Rendre la vue
		$this->content = $this->renderView($this->viewName, $this->data);
		
		// Rendre le layout si défini
		if ($this->layout) {
			return $this->renderLayout($this->layout, [
				'content' => $this->content,
				...$this->data
			]);
		}
		
		return $this->content;
	}
	
	protected function renderView(string $view, array $data): string {
		$viewPath = $this->makePath($view);
		extract($data);
		ob_start();
		require $viewPath;
		return ob_get_clean();
	}
	
	protected function renderLayout(string $layout, array $data): string {
		$layoutPath = $this->makePath($layout);
		extract($data);
		ob_start();
		require $layoutPath;
		return ob_get_clean();
	}
	
	public function setLayout(?string $layout): self {
		$this->layout = $layout;
		return $this;
	}

	/**
	 * render a part of view 
	 * @param string $element element name
	 * @param  array  $option  variable for view
	 * @return view element just say echo
	 */
	public function part($name,$option = []){
		$fileName = $this->makePath($name);

		extract($option);
		ob_start();
		require($fileName);
		return ob_get_clean();
	}

	/**
	 * pour afficher un element crée avant layout (la view du controller pour le momant) mais c pas fini 
	 * @param  string $name the name
	 * @return  view element just say echo
	 */
	public function fetch($var){
		echo $this->{$var};
	}

	/**
	 * add css file for header
	 * @param string $path the path to the file
	 */
	public function addCss($path){
		$this->css .= '<link href="'.$path.'" rel="stylesheet" type="text/css">'.PHP_EOL;
	}

	/**
	 * add js file for header
	 * @param string $path the path to the file
	 */
	public function addJs($path){
		$this->js .= '<script type="text/javascript" src="'.$path.'" ></script>'.PHP_EOL;
	}

	/**
	 * get header option 
	 * @return string html header option
	 */
	public function getHeader(){
		$retour = '';
		$retour .= '<meta name="keywords" content="'.$this->keywords.'" />'.PHP_EOL;
		foreach ($this->meta as $name => $content) {
			$retour .= '<meta name="'.$name.'" content="'.$content.'" />'.PHP_EOL;
		}
		$retour .= $this->css;
		$retour .= $this->js;
		return $retour;
	}

	/**
	 * start the buffuring view for script in end of the page
	 * @return void
	 */
	public function startScript(){
		ob_start();
	}
	
	/**
	 * stop the buffuring for script
	 * @return void
	 */
	public function stopScript(){
		$this->script .= ob_get_clean();
	}

	/**
	 * get the script 
	 * @return string the script
	 */
	public function getScript(){
		$retour = '';
		$retour .= $this->script;
		return $retour;
	}

	
	public function setTitle($title){
		$this->title = $title;
	}

	public function getTitle(){
		return $this->title;
	}

	public function addKeyWords($words){
		$this->keywords .= $words;
	}

	public function addMeta($name,$content){
		$this->meta[$name] = $content;
	}

}
