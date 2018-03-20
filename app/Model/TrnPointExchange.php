<?php
App::uses('AppModel', 'Model');
/**
 * TrnPointExchange Model
 *
 */
class TrnPointExchange extends AppModel {

	public $useTable = 'trn_point_exchange';

	public $primaryKey = 'exchange_id';
	public $displayField = 'exchange_point';

	public $recursive = -1;

	public $belongsTo = array(
		'MstPointItem' => array(
			'className' => 'MstPointItem',
			'foreignKey' => 'item_id',
			'fields' => '',
			'order' => ''
		),
		'TrnMembers' => array(
			'className' => 'TrnMembers',
			'foreignKey' => 'member_id',
			'fields' => '',
			'order' => ''
		),
		'MstAdminUser' => array(
			'className' => 'MstAdminUser',
			'foreignKey' => 'shop_id',
			'conditions' => array('MstAdminUser.acc_grant' => 3),
			'fields' => '',
			'order' => ''
		)
	);

	public function afterFind($results, $primary = false) {
		$exchangeResults = self::readExchangeResults();
		$status = self::readStatus();
		$pushStatus = self::readPushStatus();
		$mailStatus = self::readMailStatus();
		foreach ($results as $key => $value) {
			if (isset($value['TrnPointExchange']['exchange_result'])) {
				$results[$key]['ExchangeResult'] = $exchangeResults[$value['TrnPointExchange']['exchange_result']];
			}
			if (isset($value['TrnPointExchange']['status'])) {
				$results[$key]['Status'] = $status[$value['TrnPointExchange']['status']];
			}
			if (isset($value['TrnPointExchange']['push_status'])) {
				$results[$key]['PushStatus'] = $pushStatus[$value['TrnPointExchange']['push_status']];
			}
			if (isset($value['TrnPointExchange']['mail_status'])) {
				$results[$key]['MailStatus'] = $mailStatus[$value['TrnPointExchange']['mail_status']];
			}
		}
		return $results;
	}

	public function readExchangeResults() {
		return array(
			0 => '成功',
			1 => '失敗'
		);
	}

	public function readStatus() {
		return array(
			0 => '新規',
			1 => '処理中',
			2 => '完了'
		);
	}

	public function readPushStatus() {
		return array(
			0 => '未通知',
			1 => '通知済'
		);
	}

	public function readMailStatus() {
		return array(
			0 => '未通知',
			1 => '通知済'
		);
	}

	public function readNewEntry() {
		$new = array();
		$data = $this->find('all', array(
			'fields' => array(
				'TrnPointExchange.item_id',
				'count(*) as nums'
			),
			'conditions' => array(
				'TrnPointExchange.status' => 0
			),
			'group' => array('TrnPointExchange.item_id')
		));
		if ($data) {
			foreach ($data as $res) {
				$new += array($res['TrnPointExchange']['item_id'] => $res[0]['nums']);
			}
		}

		$results = array();
		$data = $this->MstPointItem->find('list', array(
			'conditions' => array(
				'MstPointItem.status' => 0
			)
		));
		if ($data) {
			foreach ($data as $item_id => $item_name) {
				$results += array($item_id => array(
					'name' => $item_name,
					'num' => isset($new[$item_id]) ? $new[$item_id] : 0
				));
			}
		}
		return $results;
	}
}
