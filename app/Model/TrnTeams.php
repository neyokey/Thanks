<?php
App::uses('AppModel', 'Model');
/**
 * MstAdminUser Model
 *
 */
class TrnTeams extends AppModel {

	public $useTable = 'trn_team';

	public $primaryKey = 'member_id';
	public $displayField = 'active_flg';

	public $recursive = -1;

	public $belongsTo = array(
		'TrnMembers' => array(
			'className' => 'TrnMembers',
			'foreignKey' => 'member_id',
			'fields' => '',
			'order' => ''
		),
		'MstAdminUser' => array(
			'className' => 'MstAdminUser',
			'foreignKey' => 'shop_id',
			'conditions' => array('MstAdminUser.acc_grant' => 3),
			'fields' => '',
			'order' => ''
		)
	);

	public function afterFind($results, $primary = false) {
		$status = self::readMemberStatus();
		foreach ($results as $key => $value) {
			if (isset($value['TrnMembers']['status'])) {
				$id = $value['TrnMembers']['status'];
				$results[$key]['Status'] = array(
					'id' => $id,
					'name' => $status[$id]
				);
			}
		}
		return $results;
	}
}
