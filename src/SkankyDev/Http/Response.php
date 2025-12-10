<?php 

namespace SkankyDev\Http;

use SkankyDev\View\HtmlView;
use SkankyDev\Utilities\Session;

class Response {

	protected array $headers = [];
	protected string $body = '';
	protected int $statusCode = 200;
	protected bool $built = false;
	
	public function __construct(
		protected string $viewName = '',
		protected array $data = []
		) {
	}
	
	public function header(string $name, string $value): self {
		$this->headers[$name] = $value;
		return $this;
	}

	public function status(int $code): self {
		$this->statusCode = $code;
		return $this;
	}
	
	public function viewName($viewName): self {
		$this->viewName = $viewName;
		return $this;
	}

	public function build(): self{
		//si requeter veux json 
		if(Request::_wantsJson()){
			$this->header('content-type', 'application/json');
			$this->body = json_encode($this->data);
		}else{
			$view = new HtmlView($this->viewName,$this->data);
			$this->header('Content-Type', 'text/html; charset=UTF-8');
			$this->body = $view->render();
		}
		$this->built = true;
		return $this;
	}

	public function send(): void {
		if (!$this->built && $this->statusCode >= 200 && $this->statusCode < 300) {
			$this->build();
		}
		
		if (!headers_sent()) {
			http_response_code($this->statusCode);
			
			foreach ($this->headers as $name => $value) {
				header("{$name}: {$value}");
			}
			
			// Si c'est une redirection, exit immédiatement
			if ($this->statusCode >= 300 && $this->statusCode < 400) {
				exit;
			}
		} else {
			// Headers déjà envoyés ET c'est une redirection = gros problème
			if ($this->statusCode >= 300 && $this->statusCode < 400) {
				// Fallback JS pour la redirection
				$location = $this->headers['Location'] ?? '/';
				echo "<script>window.location.href = '{$location}';</script>";
			}
		}
		echo $this->body;
	}

	/**
	* Ajouter les erreurs en session pour le prochain affichage
	*/
	public function withErrors(array $errors): self {
		Session::set('errors', $errors);
		return $this;
	}

	/**
	* Ajouter les anciennes données (old input) en session
	*/
	public function withInput(array $input): self {
		Session::set('old', $input);
		return $this;
	}

	/**
	* Ajouter un flash messsage données (old input) en session
	*/
	public function withFlash(string $type , string $message): self {
		flash($type,$message);
		return $this;
	}

}
