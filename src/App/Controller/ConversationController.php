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

namespace App\Controller;

use App\Form\ConversationForm;
use App\Llm\ChatEngine;
use App\Llm\ToolSet;
use App\Model\ConversationCollection;
use App\Model\Document\Conversation;
use App\Model\Document\Message;
use App\Model\Document\Persona;
use SkankyDev\Controller\MasterController;
use SkankyDev\Http\Middleware\Attribute\Middleware;
use SkankyDev\Http\Request;

class ConversationController extends MasterController {

	/**
	 * Liste paginée des conversations.
	 */
	public function index(ConversationCollection $collection){
		$conversations = $collection->paginate([], Request::_paginateInfo());
		return view('conversation.index', ['conversations' => $conversations]);
	}

	/**
	 * Affiche le formulaire de création d'un conversation.
	 */
	public function create(){
		$form = new ConversationForm(['action' => 'store']);
		return view('conversation.create', ['form' => $form]);
	}

	/**
	 * Valide et enregistre un nouveau conversation, puis redirige vers son show.
	 * En cas d'échec de validation, retourne au formulaire avec erreurs et anciennes valeurs.
	 */
	#[Middleware('PostOnly')]
	public function store(Request $request){
		$input = $request->input();
		$form = new ConversationForm(['action' => 'store']);
		if(!$form->validate($input)){
			return redirect(['action' => 'create'])->withErrors($form->getErrors())->withInput($input);
		}
		$conversation = new Conversation($input);
		ConversationCollection::_save($conversation);
		return redirect(['action' => 'show', 'params' => [$conversation->_id]])->withFlash('success', 'Enregistrement réussi');
	}

	/**
	 * Démarre une nouvelle conversation pour un persona, puis redirige vers son chat.
	 */
	#[Middleware('PostOnly')]
	public function start(Persona $persona){
		$conversation = new Conversation();
		$conversation->persona_id = $persona->_id;
		$conversation->name = 'Discussion avec ' . $persona->name;
		ConversationCollection::_save($conversation);
		return redirect(['action' => 'show', 'params' => [$conversation->_id]]);
	}

	/**
	 * Affiche le chat d'une conversation (historique + saisie).
	 */
	public function show(Request $request, Conversation $conversation){
		return view('conversation.show', [
			'conversation' => $conversation,
			'persona'      => $conversation->persona,
		]);
	}

	/**
	 * Un tour de chat : ajoute le message de l'utilisateur, interroge llama via le
	 * moteur, persiste, et renvoie la réponse de l'assistant en JSON (AJAX).
	 */
	#[Middleware('PostOnly')]
	public function message(Request $request, Conversation $conversation){
		$text = trim($request->input()['message'] ?? '');
		if ($text === '') {
			return response(['error' => 'Message vide'])->status(422);
		}

		// Nomme la conversation d'après le 1er message (pour l'index).
		if ($conversation->messages === []) {
			$conversation->name = mb_substr($text, 0, 60);
		}
		$conversation->addMessage(Message::user($text));

		try {
			$reply = (new ChatEngine())->reply($conversation, ToolSet::forChat());
		} catch (\Throwable $e) {
			return response(['error' => $e->getMessage()])->status(502);
		}

		ConversationCollection::_save($conversation);

		return response(['message' => $reply->toApi()]);
	}

	/**
	 * Affiche le formulaire d'édition d'un conversation, pré-rempli avec ses données.
	 */
	public function edit(Conversation $conversation){
		$form = new ConversationForm(['action' => 'update', 'params' => [$conversation->_id]]);
		$form->setData($conversation);
		return view('conversation.edit', ['form' => $form, 'conversation' => $conversation]);
	}

	/**
	 * Valide et met à jour un conversation existant, puis redirige vers son show.
	 * En cas d'échec de validation, retourne au formulaire avec erreurs et anciennes valeurs.
	 */
	#[Middleware('PostOnly')]
	public function update(Request $request, Conversation $conversation){
		$input = $request->input();
		$form = new ConversationForm(['action' => 'update', 'params' => [$conversation->_id]]);
		if(!$form->validate($input)){
			return redirect(['action' => 'update', 'params' => [$conversation->_id]])->withErrors($form->getErrors())->withInput($input);
		}
		$conversation->fill($input);
		ConversationCollection::_save($conversation);
		return redirect(['action' => 'show', 'params' => [$conversation->_id]])->withFlash('success', 'Modification réussie');
	}

	/**
	 * Supprime un conversation puis redirige vers la liste.
	 */
	#[Middleware('PostOnly')]
	public function delete(Conversation $conversation){
		ConversationCollection::_deleteOne($conversation);
		return redirect(['action' => 'index'])->withFlash('success', 'Suppression réussie');
	}
}
