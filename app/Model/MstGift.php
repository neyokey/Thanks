<?php
App::uses('AppModel', 'Model');
/**
 * MstGift Model
 *
 */
class MstGift extends AppModel {

	public $useTable = 'mst_gift';

	public $primaryKey = 'gift_id';
	public $displayField = 'gift_name';

	public $recursive = -1;

	public function setAmazonVal($data) {
		$result = array();
		$txt = '';
		$code = array();
		foreach ($data as $res) {
			$gift_code = $res['MstGift']['gift_code'];
			$gift_amount = number_format($res['MstGift']['gift_amount']);
			$expiration_date = date('Y/m/d', strtotime($res['MstGift']['expiration_date']));
			$txt .= <<<EOF
ギフト券番号：{$gift_code}（＊ハイフンも含みます）
金額： {$gift_amount}円
有効期限： {$expiration_date}
--------------------------------------------------------

EOF;
			$code[] = $gift_code;
		}
		$result['mail'] = $txt;
		$result['text'] = count($code) == 1 ? 'ギフトコード：'.$code[0] : 'ギフトコード：'."\r\n".implode("\r\n", $code);
		return $result;
	}
}
