<?php 

use SkankyDev\Http\Response;
use SkankyDev\Http\UrlBuilder;
use SkankyDev\Utilities\Session;
use SkankyDev\Utilities\Token;

function redirect(array $link){
	$url = UrlBuilder::_build($link);
	debug($url);
	$response = new Response();
	$response->status(302)->header('Location', $url);
	debug($response);
	return $response;
}

function view(string $name,array $data = []){
	$response = new Response($name,$data);
	return $response;
}


/**
 * Échapper du HTML
 */
function e($value) {
	return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Afficher un asset
 */
function asset($path) {
	return '/assets/' . ltrim($path, '/');
}

/**
 * Générer une URL
 */
function url(array $params) {
	return UrlBuilder::build($params);
}

/**
 * Ancien timestamp en format lisible
 */
 function since(DateTime $date, $full = false){
	//TO DO ca fait le taf mais c'est pas tip top 
	$now = new DateTime();
	$diff = $now->diff($date);

	$diff->w = floor($diff->d / 7);
	$diff->d -= $diff->w * 7;

	$string = [
		'y' => _('year'),
		'm' => _('month'),
		'w' => _('week'),
		'd' => _('day'),
		'h' => _('hour'),
		'i' => _('minute'),
		's' => _('second'),
	];
	
	foreach ($string as $k => &$v) {
		if ($diff->$k) {
			$v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
		} else {
			unset($string[$k]);
		}
	}

	if(!$full){
		$string = array_slice($string, 0, 1);
	}
	return $string ? implode(', ', $string) : _('just now');    
}

/**
 * Flash message
 */
function flash($type = 'success',$message = null) {
	if ($message === null) {
		return Session::getAndClean('flash');
	}
	Session::insert('flash', ['type' => $type,'message' => $message,]);
}

function csrf_field() {
	$token = new Token();
	Session::set('csrf_token', $token);
	return '<input type="hidden" name="_token" value="' . $token->getToken() . '">';
}

function old($key, $default = '') {
	return Session::get('old.' . $key, $default);
}

function error($key) {
	$errors = Session::get('errors', []);
	if (isset($errors[$key])) {
		return '<span class="error">' . e($errors[$key]) . '</span>';
	}
	return '';
}