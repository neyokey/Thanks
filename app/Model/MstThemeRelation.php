<?php
App::uses('AppModel', 'Model');
/**
 * MstThemeRelation Model
 *
 */
class MstThemeRelation extends AppModel {

	public $useTable = 'mst_theme_relation';

	public $primaryKey = 'id';
	public $displayField = 'theme_id';

	public $recursive = -1;
}
