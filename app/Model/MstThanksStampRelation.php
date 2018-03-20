<?php
App::uses('AppModel', 'Model');
/**
 * MstThanksStampRelation Model
 *
 */
class MstThanksStampRelation extends AppModel {

	public $useTable = 'mst_thanks_stamp_relation';

	public $primaryKey = 'id';
	public $displayField = 'stamp_id';

	public $recursive = -1;
}
