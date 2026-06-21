<?php 

namespace App\View\Part;

use SkankyDev\View\Part\MasterPart;
use League\CommonMark\CommonMarkConverter;

class PartMarkdownPart extends MasterPart {

	public function data(array $options): array {

		$content = $options['content'] ?? '';
		$converter = new CommonMarkConverter([
			'html_input' => 'strip',
			'allow_unsafe_links' => false,
		]);
		$content =  $converter->convert($content);
		return ['content' => $content];
	}

}