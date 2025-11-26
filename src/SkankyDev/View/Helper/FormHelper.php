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
namespace SkankyDev\View\Helper;

use SkankyDev\Config\Config;
use SkankyDev\Factory;
use SkankyDev\Model\Document\DocumentInterface;
use SkankyDev\Request;
use SkankyDev\Routing\UrlBuilder;
use SkankyDev\Utilities\Session;
use SkankyDev\Utilities\Token;
use SkankyDev\View\Helper\Htmlhelper;
use SkankyDev\View\Helper\MasterHelper;
/**
* 
*/
class FormHelper extends MasterHelper {

	use HtmlHelper;

	public $data;
	public $token;
	private $dClass;
	private $formAttr = ['accept-charset'=>"UTF-8"];
	private $submitBtn = false;
	private $calledElement = [];

	/**
	 * create the form object 
	 * @param array $data the data to put in input
	 */
	function __construct(){
		//$request = Request::getInstance();
		//$this->data = $request->data;
		$this->dClass = Config::get('form.class');
		$this->elementList = Config::get('class.formElement');
		$this->elementName = array_keys($this->elementList);

	}

	/**
	 * check if i have value for input
	 * @param  string $name the name of the value
	 * @return mixed        the value
	 */
	private function checkValue($name){
		return (isset($this->data->{$name})?$this->data->{$name}:null);
	}

	/**
	 * check if a checkbox is checked
	 * @param  string $name  the name of the checkbox
	 * @param  string $value the value of the checkbox
	 * @return string        checked or null
	 */
	private function checkChecked($name,$value=''){
		$retour = '';
		$name = str_replace('[]', '', $name);
		if(!empty($this->data->{$name})){
			if(is_array($this->data->{$name})){
				$retour = in_array($value, $this->data->{$name})?'checked':'';
			}else{
				$retour = isset($this->data->{$name})?'checked':'';
			}
		}
		return $retour;
	}

	/**
	 * check the selected value for a select
	 * @param  string $name  the name of the select
	 * @param  string $value the value of the select
	 * @return string        selected or empty string
	 */
	private function checkSelected($name,$value=''){
		$retour = '';
		if(isset($this->data->{$name})){
			if(is_array($this->data->{$name})){
				$retour = in_array($value, $this->data->{$name})?'selected':'';
			}else{
				if (isset($this->data->{$name})) {
					$retour = $this->data->{$name}===$value?'selected':'';
				}
			}
		}
		return $retour;
	}

	/**
	 * check the selected value for a radio button
	 * @param  string $name  the name of the select
	 * @param  string $value the value of the select
	 * @return string        selected or empty string
	 */
	private function checkRadio($name,$value=''){
		$retour = '';
		if(isset($this->data->{$name})){
			$retour = $this->data->{$name}===$value?'checked':'';
		}
		return $retour;
	}

	/**
	 * create the form balise
	 * @param  DocumentInterface  $document   default value for form
	 * @param  array              $link       the link for send form
	 * @param  array              $attr       the attribute
	 * @param  string             $method     the method of form (default POST)
	 * @param  string             $csrf       active CSRF protection
	 * @return string                         the balise form
	 */
	public function start(DocumentInterface $document = null, $link = [],$attr = [],$method='POST',$csrf = true){
		$this->data = $document;
		if(empty($link)){
			$action = UrlBuilder::_buildCurrent();
		}else{
			$action = UrlBuilder::_build($link);			
		}
		$retour = '<form action="'.$action.'" ';
		$attr =  array_merge($this->formAttr,$attr);
		$retour .= $this->createAttr($attr);
		$retour .= 'method="'.$method.'">';
		if($csrf){
			$this->token = new Token();
			Session::set('skankydev.form.csrf',$this->token);
			$retour .= $this->input('_token',['type'=>'hidden','value'=>$this->token->value]);
		}
		return $retour;
	}

	/**
	 * close the form add the submit button if not exsiste
	 * @return string the html
	 */
	public function end($submit = true){
		$retour = '';
		if(!$this->submitBtn && $submit){
			$retour .= $this->submit('Send');
		}
		$retour .= '</form>';
		return $retour;
	}

	/**
	 * create a fildset 
	 * @param  array  $option the list of option for the fieldset 
	 * example [
	 *  	'fieldset'=> list of attr for balise fieldset ,
	 *		'legend'  => if is set create the legende balise,
	 *		'input'   => the list of input in the fieldset balise,
	 *	]
	 * @return string        the html 
	 */
	public function fieldset($option = array()){
		$fAttr = [];
		if(isset($option['fieldset'])){
			$fAttr = $option['fieldset'];
		}
		$retour = '';
		if(isset($option['legend'])){
			$content = $option['legend']['content'];
			$attr = $option['legend'];
			unset($attr['content']);
			$retour .= $this->legend($content,$attr);
		}

		$methods = get_class_methods($this);
		
		foreach ($option['input'] as $key => $value) {
			$message = '';
			$contentDiv = '';
			if(!isset($value['type'])){
				$value['type'] = 'text';
			}
			$class = $value['type'];
			if(isset($value['multiple'])){
				if (($value['multiple']==='checkbox')) {
					$class .= ' checkbox';
				}elseif($value['multiple']){
					$class .= '-multiple' ;
				}
				
			}
			if(isset($this->data->messageValidate[$key])){
				$class .= ' not-valid';
				$message = '<span class="valid-message">'.$this->data->messageValidate[$key].'</span>';
			}
			//$retour .= '<div class="input '.$class.'">';
			$label = '';
			if(isset($value['label'])){
				$lAttr = ['for'=>$key];
				if(isset($value['labelAttr'])){
					$lAttr = array_merge($lAttr,$value['labelAttr']);
					unset($value['labelAttr']);
				}
				$label .= $this->label($value['label'],$lAttr);
				unset($value['label']);
			}
	
			$input ='';
			if(in_array($value['type'],$methods)){
				$name = $value['type'];
				$input .= $this->{$name}($key,$value);
			}else if (in_array($value['type'],$this->elementName)) {
				$input .= $this->callElement($key,$value);
			}else{
				$input .= $this->input($key,$value);
			}
			$contentDiv .= ($value['type']==='checkbox')? $input.$label:$label.$input;
			$contentDiv .= $message;

			$retour .= $this->surround($contentDiv,'div',['class'=>$class]);

		}
		$retour = $this->surround($retour,'fieldset',$fAttr);
		return $retour;
	}

	/**
	 * create the legend balise
	 * @param  string $content the content of the balise
	 * @param  array  $attr    the attribute for the balise
	 * @return string          the html
	 */
	public function legend($content,$attr = []){
		$retour = '<legend ';
		$retour .= $this->createAttr($attr);
		$retour .= '>'.$content.'</legend>';
		return $retour;
	}
	
	/**
	 * create the label balise
	 * @param  string $content the content of the balise
	 * @param  array  $attr    the attribute
	 * @return string          the html
	 */
	public function label($content, $attr = []){
		$retour = $this->surround($content,'label',$attr);
		return $retour;
	}
	
	/**
	 * create input balise
	 * @param  string $name the name
	 * @param  array  $attr the attribute 
	 * @return string       the html
	 */
	public function input($name,$attr = []){
		if(!isset($attr['type'])){
			$attr['type'] = 'text';
		}
		if(!isset($attr['value'])){
			$attr['value'] = $this->checkValue($name);
		}
		if(!isset($attr['name'])){
			$attr['name'] = $name;
		}
		if(!isset($attr['id'])){
			$attr['id'] = $name;
		}
		$this->addDefaultClass('input',$attr);
		$retour = '<input '.$this->createAttr($attr).' >';
		return $retour;
	}

	/**
	 * create input type password
	 * @param  string $name the name
	 * @param  array  $attr the attribute
	 * @return string       the html
	 */
	public function password($name,$attr = []){
		$attr['type'] = 'password';
		$attr['autocomplete'] = 'off';
		return $this->input($name,$attr);
	}

	/**
	 * create select form
	 * @param  string  $name     the name
	 * @param  array   $attr     the attribute
	 * @param  string  $multiple type of multiple selection
	 * @return string            the html
	 */
	public function select($name,$attr = []){
		$retour ='';
		$multiple = isset($attr['multiple'])?$attr['multiple']:false;
		if($multiple==='checkbox'){
			$retour .= $this->multipleCheckbox($name,$attr);
		}else{
			$option = $attr['option'];
			unset($attr['option']);
			unset($attr['type']);
			//var_dump($attr);
			$multiple = $multiple?'multiple':'';
			$sName = $multiple?$name.'[]':$name;
			$retour .= '<select id="'.$name.'" name="'.$sName.'"';
			$retour .= $this->createAttr($attr);
			$retour .= $multiple.'>';
			foreach ($option as $key => $value) {
				$val = $value;
				if(is_string($key)){
					$val = $key;
				}
				$retour .= '<option value="'.$val.'" '.$this->checkSelected($name,$val).'>';
				$retour .= $value;
				$retour .= '</option>';
			}
			$retour .= '</select>';
		}
		return $retour;
	}

	/**
	 * create checkbox 
	 * @param  string $name the name
	 * @param  array  $attr the attribute
	 * @param  string $value the checkbox value
	 * @return string        the html
	 */
	public function checkbox($name,$attr = [],$value=''){
		if (empty($value)) {
			$value = $name;
		}
		$retour ='<input id="'.$value.'" type="checkbox" name="'.$name.'" value="'.$value.'"';
		$retour .= $this->checkChecked($name,$value).' ';
		$retour .= $this->createAttr($attr).' />';
		return $retour;
	}

	/**
	 * create multiple checkbox 
	 * @param  string $name the name
	 * @param  array  $attr the attribute
	 * @return string       the html
	 */
	public function multipleCheckbox($name,$attr = []){
		$option = $attr['option'];
		unset($attr['option']);
		$myAttr = [];
		if(isset($attr['attr'])){
			$myAttr = $attr['attr'];
		}
		$retour = '';
		foreach ($option as $key => $value) {
			$tmp = $this->checkbox($name.'[]',$myAttr,$value);
			$tmp .= $this->label(is_string($key)?$key:$value,['for'=>$value]);
			$retour .= $this->surround($tmp,'div',['class'=>'checkbox-multiple'],false);
		}
		return $retour;
	}

	/**
	 * create textarea
	 * @param  string $name the name
	 * @param  array  $attr the attribute
	 * @return string       the html
	 */
	public function textarea($name,$attr = []){
		unset($attr['type']);
		if(!isset($attr['name'])){
			$attr['name'] = $name;
		}
		if(!isset($attr['id'])){
			$attr['id'] = $name;
		}
		$retour = $this->surround($this->checkValue($name),'textarea',$attr);
		return $retour;
	}

	/**
	 * create radio button
	 * @param  string $name the name
	 * @param  array  $attr the attributr
	 * @return string       the html
	 */
	public function radio($name,$attr=[]){
		$option = $attr['option'];
		unset($attr['option']);
		$myAttr = [];
		if(isset($attr['attr'])){
			$myAttr = $attr['attr'];
		}
		$retour = '';
		foreach ($option as $key => $value) {
			$tmp = '<input id="'.$name.'.'.$value.'" type="radio" name="'.$name.'" value="'.$value.'"';
			$tmp .= $this->checkRadio($name,$value).' ';
			$tmp .= $this->createAttr($myAttr).' />';
			$tmp .= $this->label($value,['for'=>$name.'.'.$value]);
			$retour .= $this->surround($tmp,'div',['class'=>'radio'],false);
			
		}
		return $retour;
	}

	/**
	 * create submit button
	 * @param  string $content the content
	 * @param  array  $attr    the attribute
	 * @return string          the html
	 */
	public function submit($content, $attr = []){
		$this->submitBtn = true;
		$attr['type'] = 'submit';
		$retour = $this->surround($content,'button',$attr);
		return $this->surround($retour,'div',['class'=>'submit'],false);
	}


	/**
	 * call a custome form element 
	 * @param  string $name the name
	 * @param  array  $attr the attribute must be containe the name of the element
	 * @return string       the html
	 */
	public function callElement($name,$attr){
		$obj = $attr['type'];
		$params = isset($attr['construct'])?$attr['construct']:[];
		$value = $this->checkValue($name);
		$elem = Factory::load($this->elementList[$obj],['form'=>&$this,'param'=>$params]);

		return $elem->input($name,$attr,$value);
	}

	/**
	 * create input files balise
	 * @param  string $name the name
	 * @param  array  $attr the attribute 
	 * @return string       the html
	 */
	public function file($name,$attr = []){
		if(!isset($attr['name'])){
			$attr['name'] = $name;
		}
		if(!isset($attr['id'])){
			$attr['id'] = $name;
		}
		if(isset($attr['multiple']) && $attr['multiple']){
			$attr['name'] .= '[]';
			//unset($attr['id']);
		}
		$this->addDefaultClass('input',$attr);
		$retour = '<input '.$this->createAttr($attr).' >';
		return $retour;
	}
}
