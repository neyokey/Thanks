<?php
App::uses('AppModel', 'Model');
/**
 * MstAdminUser Model
 *
 */
class TrnThanksSumMember extends AppModel {

	public $useTable = 'trn_thanks_sum_member';

	public $recursive = -1;

	public $belongsTo = array(
		'TrnMembers' => array(
			'className' => 'TrnMembers',
			'foreignKey' => false,
			'conditions' => array(
				'TrnMembers.member_id = TrnThanksSumMember.menber_id'
			),
			'fields' => '',
			'order' => ''
		)
	);
}
