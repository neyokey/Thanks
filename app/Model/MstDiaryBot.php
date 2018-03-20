<?php
App::uses('AppModel', 'Model');
/**
 * MstDiaryBot Model
 *
 */
class MstDiaryBot extends AppModel {

	public $useTable = 'mst_diary_bot';

	public $primaryKey = 'bot_id';
	public $displayField = 'bot_name';

	public $recursive = -1;
}
