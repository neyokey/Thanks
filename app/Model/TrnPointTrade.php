<?php
App::uses('AppModel', 'Model');
/**
 * TrnPointTrade Model
 *
 */
class TrnPointTrade extends AppModel {

	public $useTable = 'trn_point_trade';

	public $primaryKey = 'trade_id';
	public $displayField = 'member_id';

	public $recursive = -1;
}
