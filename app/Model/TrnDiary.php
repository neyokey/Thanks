<?php
App::uses('AppModel', 'Model');
/**
 * TrnDiary Model
 *
 */
class TrnDiary extends AppModel {

	public $useTable = 'trn_diary';

	public $primaryKey = 'diary_id';
	public $displayField = 'shop_id';

	public $recursive = -1;
}
