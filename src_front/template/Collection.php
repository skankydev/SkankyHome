%?php
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

namespace App\Model;

use SkankyDev\Model\MasterCollection;
use App\Model\Document\<?= $name ?>;

class <?= $name ?>Collection extends MasterCollection {

	protected string $collectionName = '<?= $collection ?>';
	protected string $documentClass = <?= $name ?>::class;
	
}