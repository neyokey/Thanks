<?php
App::uses('AppModel', 'Model');
/**
 * MstDiaryBotContents Model
 *
 */
class MstDiaryBotContents extends AppModel {

	public $useTable = 'mst_diary_bot_contents';

	public $primaryKey = 'contents_id';
	public $displayField = 'stamp_id';

	public $recursive = -1;
}
