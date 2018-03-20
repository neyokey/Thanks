<?php
App::uses('AppModel', 'Model');
/**
 * MstPointItem Model
 *
 */
class MstPointItem extends AppModel {

	public $useTable = 'mst_point_item';

	public $primaryKey = 'item_id';
	public $displayField = 'item_name';

	public $recursive = -1;
}
