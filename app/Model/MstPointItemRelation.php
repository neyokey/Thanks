<?php
App::uses('AppModel', 'Model');
/**
 * MstPointItemRelation Model
 *
 */
class MstPointItemRelation extends AppModel {

	public $useTable = 'mst_point_item_relation';

	public $primaryKey = 'id';
	public $displayField = 'item_id';

	public $recursive = -1;

	public $belongsTo = array(
		'MstPointItem' => array(
			'className' => 'MstPointItem',
			'foreignKey' => 'item_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	public function findRelationData($shop_id = null) {
		$results = array();
		if ($shop_id !== null) {
			$relation_data = $this->find('list', array(
				'conditions' => array(
					'MstPointItemRelation.shop_id' => $shop_id
				)
			));
		} else {
			$relation_data = array();
		}
		$items = $this->MstPointItem->find('all', array(
			'conditions' => array(
				'MstPointItem.status' => 0
			),
			'order' => array('MstPointItem.sort' => 'asc')
		));
		foreach ($items as $val) {
			$item_id = $val['MstPointItem']['item_id'];
			if ($key = array_search($item_id, $relation_data)) {
				$val['MstPointItemRelation'] = array(
					'id' => $key
				);
			}
			$results[$item_id] = $val;
		}
		return $results;
	}
}
