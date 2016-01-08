<?php
class NoteDetail extends AppModel {

	function getByNote($note_id) {
		return	$this->find('all', array(
			'fields' => 'NoteDetail.*, User.name',
			'joins' => array(
				array(
					'table' => 'users',
					'alias' => 'User',
					'type' => 'LEFT',
					'conditions' => 'User.id = NoteDetail.assigned_to'
				)
			),
			'conditions' => array(
				'note_id' => $note_id
			),
			'order' => 'id asc'
		));
	}
}