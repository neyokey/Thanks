<?php
App::uses('AppModel', 'Model');
/**
 * MstAdminUser Model
 *
 */
class TrnThanksCnt extends AppModel {

	public $useTable = 'trn_thanks_cnt';

	public $primaryKey = 'id';
	public $displayField = 'days';

	public $recursive = -1;

	public $belongsTo = array(
		'TrnMembers' => array(
			'className' => 'TrnMembers',
			'foreignKey' => false,
			'conditions' => array(
				'TrnMembers.member_id = TrnThanksCnt.menber_id'
			),
			'fields' => '',
			'order' => ''
		)
	);
}
