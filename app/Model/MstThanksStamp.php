<?php
App::uses('AppModel', 'Model');
/**
 * MstThanksStamp Model
 *
 */
class MstThanksStamp extends AppModel {

	public $useTable = 'mst_thanks_stamp';

	public $primaryKey = 'stamp_id';
	public $displayField = 'stamp_name';

	public $recursive = -1;

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'MstThanksStampCategory' => array(
			'className' => 'MstThanksStampCategory',
			'foreignKey' => 'category_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	public function readStatus() {
		return array(
			0 => '有効',
			1 => '無効'
		);
	}

	public function readAddFlgs() {
		return array(
			1 => '共有する',
			0 => '共有しない'
		);
	}

	public function findAllStamps() {
		$results = array();
		$categories = $this->MstThanksStampCategory->find('list', array(
			'conditions' => array(
				'MstThanksStampCategory.status' => 0
			),
			'order' => array('MstThanksStampCategory.sort' => 'asc')
		));
		$data = $this->find('all', array(
			'fields' => array(
				'MstThanksStamp.stamp_id',
				'MstThanksStamp.category_id',
				'MstThanksStamp.stamp_name',
				'MstThanksStamp.image_url'
			),
			'conditions'=> array(
				'MstThanksStamp.status' => 0,
				'MstThanksStamp.all_flg' => 1
			),
			'order' => array(
				'MstThanksStamp.category_id' => 'asc',
				'MstThanksStamp.sort' => 'asc'
			)
		));
		foreach ($data as $res) {
			$category_id = $res['MstThanksStamp']['category_id'];
			if (!isset($results[$category_id])) {
				$results[$category_id] = array(
					'category_name' => $categories[$category_id],
					'data' => array()
				);
			}
			$results[$category_id]['data'][] = $res;
		}
		return $results;
	}
}
