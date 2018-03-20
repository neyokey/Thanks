<?php
App::uses('AppModel', 'Model');
/**
 * MstPuchReserve Model
 *
 */
class MstPuchReserve extends AppModel {

	public $useTable = 'mst_puch_reserve';

	public $primaryKey = 'id';
	public $displayField = 'reserve_time';

	public $recursive = -1;


	public function afterFind($results, $primary = false) {
		foreach ($results as $key => $value) {
			if ($value['MstPuchReserve']['status'] == 0) {
				$statusArray = array('id' => 0, 'name' => '送信済み');
			} else {
				$statusArray = $value['MstPuchReserve']['flg'] == 1 ? array('id' => 1, 'name' => '送信待ち') : array('id' => 0, 'name' => '一時停止');
			}
			$results[$key]['Status'] = $statusArray;
		}
		return $results;
	}
}
