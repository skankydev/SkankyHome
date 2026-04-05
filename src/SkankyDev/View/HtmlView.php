<?php 
/**
 * Copyright (c) 2025 SCHENCK Simon
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

use SkankyDev\Config\Config;
use SkankyDev\Core\MasterFactory;
use SkankyDev\Utilities\Traits\HtmlHelper;
use SkankyDev\Utilities\Traits\StringFacility;

/**
 * Renders PHP view templates with layout support, output buffering, and view parts.
 * Used as `$this` inside every template file — exposes helpers from HtmlHelper and StringFacility.
 */
class HtmlView {

	use HtmlHelper, StringFacility;

	public string $keywords      = '';
	public string $title         = '';
	public array  $meta          = [];
	public string $css           = '';
	public string $js            = '';
	public string $content       = '';
	public string $script        = '';
	public ?string $layout       = 'layout.default';
	public array  $helpers       = [];
	public array  $breadcrumbInfo = [];

	public function __construct(
		protected string $viewName = '',
		protected array  $data     = []
	) {}

	/**
	 * Resolves a dot-notation view name to an absolute file path.
	 * @throws \Exception (code 601) if the file does not exist
	 */
	public function makePath(string $name): string {
		$fileName = Config::get('view.folder') . DS . $this->dotToFolder($name) . '.php';
		if (!file_exists($fileName)) {
			throw new \Exception("the file : {$fileName} does not exist", 601);
		}
		return $fileName;
	}

	/** Returns the absolute path for the current view. */
	public function viewPath(): string {
		return $this->makePath($this->viewName);
	}

	/** Returns the absolute path for the current layout. */
	public function layoutPath(): string {
		return $this->makePath($this->layout);
	}

	/**
	 * Renders the view then wraps it in the layout (if set).
	 * The rendered view is available as `$content` inside the layout template.
	 */
	public function render(): string {
		$this->content = $this->renderView($this->viewName, $this->data);

		if ($this->layout) {
			return $this->renderLayout($this->layout, [
				'content' => $this->content,
				...$this->data
			]);
		}

		return $this->content;
	}

	/** Renders a view template in isolation and returns its output. */
	protected function renderView(string $view, array $data): string {
		$viewPath = $this->makePath($view);
		extract($data);
		ob_start();
		require $viewPath;
		return ob_get_clean();
	}

	/** Renders a layout template in isolation and returns its output. */
	protected function renderLayout(string $layout, array $data): string {
		$layoutPath = $this->makePath($layout);
		extract($data);
		ob_start();
		require $layoutPath;
		return ob_get_clean();
	}

	/** Overrides the layout. Pass null to render without a layout. */
	public function setLayout(?string $layout): self {
		$this->layout = $layout;
		return $this;
	}

	/**
	 * Renders a view part (sub-template) and returns its HTML.
	 * If a matching Part class exists in `App\View\Part\`, its data() method
	 * is called first to inject extra variables into the template.
	 * @param string $name   dot-notation path e.g. `module.part.scenario`
	 * @param array  $option variables to pass to the template
	 */
	public function part(string $name, array $option = []): string {
		$fileName  = $this->makePath($name);
		$partClass = 'App\\View\\Part\\' . implode('', array_map(fn($s) => $this->toCap($s), explode('.', $name))) . 'Part';

		if (class_exists($partClass)) {
			$part   = MasterFactory::_make($partClass);
			$option = array_merge($option, $part->data($option));
		}

		extract($option);
		ob_start();
		require $fileName;
		return ob_get_clean();
	}

	/**
	 * Echoes a named property of this view object.
	 * Used in layouts to output buffered sections (e.g. `$this->fetch('script')`).
	 */
	public function fetch(string $var): void {
		echo $this->{$var};
	}

	/**
	 * Appends a `<link>` stylesheet tag to the header buffer.
	 * @param string $path public path to the CSS file
	 */
	public function addCss(string $path): void {
		$this->css .= '<link href="' . $path . '" rel="stylesheet" type="text/css">' . PHP_EOL;
	}

	/**
	 * Appends a `<script>` tag to the header buffer.
	 * @param string $path public path to the JS file
	 * @param string $type MIME type (default `text/javascript`)
	 */
	public function addJs(string $path, string $type = 'text/javascript'): void {
		$this->js .= '<script src="' . $path . '" type="' . $type . '" ></script>' . PHP_EOL;
	}

	/**
	 * Returns the full `<head>` meta/CSS/JS block.
	 * Called inside the layout template: `<?= $this->getHeader() ?>`.
	 */
	public function getHeader(): string {
		$retour  = '<meta name="keywords" content="' . $this->keywords . '" />' . PHP_EOL;
		foreach ($this->meta as $name => $content) {
			$retour .= '<meta name="' . $name . '" content="' . $content . '" />' . PHP_EOL;
		}
		$retour .= $this->css;
		$retour .= $this->js;
		return $retour;
	}

	/** Starts output buffering for an inline `<script>` block. */
	public function startScript(): void {
		ob_start();
	}

	/** Stops buffering and appends the captured output to the script buffer. */
	public function stopScript(): void {
		$this->script .= ob_get_clean();
	}

	/** Returns the accumulated inline script content. */
	public function getScript(): string {
		return $this->script;
	}

	/** Sets the page title. */
	public function setTitle(string $title): void {
		$this->title = $title;
	}

	/** Returns the page title. */
	public function getTitle(): string {
		return $this->title;
	}

	/** Appends keywords to the meta keywords string. */
	public function addKeyWords(string $words): void {
		$this->keywords .= $words;
	}

	/**
	 * Adds a `<meta name="…">` tag to the header buffer.
	 * @param string $name    meta name attribute
	 * @param string $content meta content attribute
	 */
	public function addMeta(string $name, string $content): void {
		$this->meta[$name] = $content;
	}

	/**
	 * Appends a breadcrumb entry.
	 * @param string       $label display text
	 * @param string|array $url   URL string or route array passed to UrlBuilder
	 * @param string       $icon  optional icon class
	 */
	public function addCrumb(string $label, string|array $url, string $icon = ''): void {
		if (is_array($url)) {
			$url = $this->url($url);
		}
		$this->breadcrumbInfo[] = [
			'label' => $label,
			'url'   => $url,
			'icon'  => $icon,
		];
	}
}
