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

namespace App\Controller;

use App\Form\<?= $name ?>Form;
use App\Model\Document\<?= $name ?>;
use App\Model\<?= $name ?>Collection;
use SkankyDev\Controller\MasterController;
use SkankyDev\Http\Request;

class <?= $name ?>Controller extends MasterController {

	public function index(<?= $name ?>Collection $collection){
		$<?= $pluralCamel ?> = $collection->paginate([], Request::_paginateInfo());
		return view('<?= $dashed ?>.index', ['<?= $pluralCamel ?>' => $<?= $pluralCamel ?>]);
	}

	public function create(){
		$form = new <?= $name ?>Form(['action' => 'store']);
		return view('<?= $dashed ?>.create', ['form' => $form]);
	}

	public function store(Request $request){
		$input = $request->input();
		$form = new <?= $name ?>Form(['action' => 'store']);
		if(!$form->validate($input)){
			return redirect(['action' => 'create'])->withErrors($form->getErrors())->withInput($input);
		}
		$<?= $singularCamel ?> = new <?= $name ?>($input);
		<?= $name ?>Collection::_save($<?= $singularCamel ?>);
		return redirect(['action' => 'show', 'params' => [$<?= $singularCamel ?>->_id]])->withFlash('success', 'Enregistrement réussi');
	}

	public function show(Request $request, <?= $name ?> $<?= $singularCamel ?>){
		return view('<?= $dashed ?>.show', ['<?= $singularCamel ?>' => $<?= $singularCamel ?>]);
	}

	public function edit(<?= $name ?> $<?= $singularCamel ?>){
		$form = new <?= $name ?>Form(['action' => 'update', 'params' => [$<?= $singularCamel ?>->_id]]);
		$form->setData($<?= $singularCamel ?>);
		return view('<?= $dashed ?>.edit', ['form' => $form, '<?= $singularCamel ?>' => $<?= $singularCamel ?>]);
	}

	public function update(Request $request, <?= $name ?> $<?= $singularCamel ?>){
		$input = $request->input();
		$form = new <?= $name ?>Form(['action' => 'update', 'params' => [$<?= $singularCamel ?>->_id]]);
		if(!$form->validate($input)){
			return redirect(['action' => 'update', 'params' => [$<?= $singularCamel ?>->_id]])->withErrors($form->getErrors())->withInput($input);
		}
		$<?= $singularCamel ?>->fill($input);
		<?= $name ?>Collection::_save($<?= $singularCamel ?>);
		return redirect(['action' => 'show', 'params' => [$<?= $singularCamel ?>->_id]])->withFlash('success', 'Modification réussie');
	}

	public function delete(<?= $name ?> $<?= $singularCamel ?>){
		<?= $name ?>Collection::_deleteOne($<?= $singularCamel ?>);
		return redirect(['action' => 'index'])->withFlash('success', 'Suppression réussie');
	}
}