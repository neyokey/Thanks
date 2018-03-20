<?php
App::uses('AppModel', 'Model');
/**
 * MstThanksStampCategory Model
 *
 */
class MstThanksStampCategory extends AppModel {

	public $useTable = 'mst_thanks_stamp_category';

	public $primaryKey = 'category_id';
	public $displayField = 'category_name';

	public $recursive = -1;
}
