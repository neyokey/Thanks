<?php
App::uses('AppModel', 'Model');
/**
 * MstAdminUser Model
 *
 */
class TrnThanksSumShop extends AppModel {

	public $useTable = 'trn_thanks_sum_shop';

	public $recursive = -1;

	public $belongsTo = array(
		'MstAdminUser' => array(
			'className' => 'MstAdminUser',
			'foreignKey' => false,
			'conditions' => array(
				'MstAdminUser.id = TrnThanksSumShop.shop_id',
			),
			'fields' => '',
			'order' => ''
		)
	);
}
