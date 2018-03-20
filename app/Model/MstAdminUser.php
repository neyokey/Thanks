<?php
App::uses('AppModel', 'Model');
/**
 * MstAdminUser Model
 *
 */
class MstAdminUser extends AppModel {

	public $useTable = 'mst_admin_user';

	public $primaryKey = 'id';
	public $displayField = 'aname';

	public $recursive = -1;

	public function afterFind($results, $primary = false) {
		$status = self::readStatus();
		$pointStatus = self::readPointStatus();
		$pointChargeStatus = self::readPointChargeStatus();
		$pointExchangeStatus = self::readPointExchangeStatus();
		$paymentTypes = self::readPaymentTypes();
		$trialFlgs = self::readTrialFlgs();
		foreach ($results as $key => $value) {
			if (isset($value['MstAdminUser']['apass'])) {
//				$results[$key]['MstAdminUser']['apass'] = null;
			}
			if (isset($value['MstAdminUser']['payment_type'])) {
				$bar = explode(',', $value['MstAdminUser']['payment_type'], 2);
				$results[$key]['MstAdminUser']['payment_type'] = $bar;
				$results[$key]['PaymenType'] = array(
					0 => array(
						'id' => $bar[0],
						'name' => $paymentTypes[0][$bar[0]]
					),
					1 => array(
						'id' => $bar[1],
						'name' => $paymentTypes[1][$bar[1]]
					)
				);
			}
			if (isset($value['MstAdminUser']['status'])) {
				$id = $value['MstAdminUser']['status'];
				$results[$key]['Status'] = array(
					'id' => $id,
					'name' => $status[$id]
				);
			}
			if (isset($value['MstAdminUser']['trial_flg'])) {
				$id = $value['MstAdminUser']['trial_flg'];
				$results[$key]['TrialFlg'] = array(
					'id' => $id,
					'name' => $trialFlgs[$id]
				);
			}
			if (isset($value['MstAdminUser']['point_status'])) {
				$results[$key]['PointStatus'] = $pointStatus[$value['MstAdminUser']['point_status']];
			}
			if (isset($value['MstAdminUser']['point_charge_status'])) {
				$results[$key]['PointChargeStatus'] = $pointChargeStatus[$value['MstAdminUser']['point_charge_status']];
			}
			if (isset($value['MstAdminUser']['point_exchange_status'])) {
				$results[$key]['PointExchangeStatus'] = $pointExchangeStatus[$value['MstAdminUser']['point_exchange_status']];
			}
			if ($this->recursive >= 0) {
				if (isset($value['MstAdminUser']['agency_id'])) {
					$params = array(
						'fields' => array(
							'MstAdminUser.id',
							'MstAdminUser.aname'
						),
						'conditions' => array(
							'MstAdminUser.id' => $value['MstAdminUser']['agency_id'],
							'MstAdminUser.acc_grant' => 1
						)
					);
					if ($bar = $this->find('first', $params)) {
						$results[$key]['Agency'] = $bar['MstAdminUser'];
					}
				}
				if (isset($value['MstAdminUser']['chain_id'])) {
					$params = array(
						'fields' => array(
							'MstAdminUser.id',
							'MstAdminUser.aname'
						),
						'conditions' => array(
							'MstAdminUser.id' => $value['MstAdminUser']['chain_id'],
							'MstAdminUser.acc_grant' => 2
						)
					);
					if ($bar = $this->find('first', $params)) {
						$results[$key]['Company'] = $bar['MstAdminUser'];
					}
				}
			}
		}
		return $results;
	}

	public function readGrantTypes() {
		return array(
			0 => '元売',
			1 => '代理店',
			2 => '加盟店',
			3 => '店舗'
		);
	}

	public function readStatus() {
		return array(
			0 => '利用中',
			1 => '一時停止',
			2 => '解約'
		);
	}

	public function readPaymentTypes() {
		return array(
			0 => array(
				0 => '10日締め',
				1 => '15日締め',
				2 => '20日締め',
				3 => '25日締め',
				4 => '月末締め'
			),
			1 => array(
				0 => '翌月末払い',
				1 => '翌々月末払い',
				2 => '翌々々月末払い'
			),
		);
	}

	public function readTrialFlgs($mode = 0) {
		if ($mode == 1) {
			return array(
				0 => '有料アカウント',
				1 => 'トライアル',
				2 => 'テスト'
			);
		} else {
			return array(
				0 => 'なし',
				1 => 'トライアル',
				2 => 'テスト'
			);
		}
	}

	public function readPointStatus() {
		return array(
			0 => '利用中（UI表示）',
			1 => '利用しない（UI表示しない）'
		);
	}

	public function readPointChargeStatus() {
		return array(
			0 => '発行する',
			1 => '発行しない'
		);
	}

	public function readPointExchangeStatus() {
		return array(
			0 => '交換する',
			1 => '交換しない'
		);
	}

	public function checkMail($email, $id = null) {
		if ($id === null) {
			$params = array(
				'conditions' => array(
					'MstAdminUser.amail' => $email,
					'MstAdminUser.status' => array(0, 1)
				)
			);
		} else {
			$params = array(
				'conditions' => array(
					'MstAdminUser.amail' => $email,
					'MstAdminUser.status' => array(0, 1),
					'NOT' => array('MstAdminUser.id' => $id)
				)
			);
		}
		if ($this->find('count', $params) == 0) {
			return true;
		} else {
			return false;
		}
	}

	public function readBill($company_id = null, $dy1 = null, $dy2 = null) {
		if ($dy1 === null) {
			$dy1 = mktime(0, 0, 0, date('m'), 1, date('Y'));
		}
		if ($dy2 === null) {
			$dy2 = strtotime('-6 month', $dy1);
		}
		$results = array();
		$n = $dy2;
		for (;;) {
			$results += array($n => array(
				'value' => date('Y年n月', $n),
				'coun' => 0,
				'data' => array()
			));
			$n = strtotime('+1 month', $n);
			if ($n > $dy1) {
				break;
			}
		}

		# 企業情報
		$params = array(
			'conditions' => array(
				'MstAdminUser.id' => $company_id
			)
		);
		$val = $this->find('first', $params);
		switch ($val['MstAdminUser']['payment_type'][0]) {
			case 0: # 10日締め
				$key = 10;
				break;
			case 1: # 15日締め
				$key = 15;
				break;
			case 2: # 20日締め
				$key = 20;
				break;
			case 3: # 25日締め
				$key = 25;
				break;
			default: # 月末締め
				$key = 0;
				break;
		}

		# 登録店舗一覧
		$params = array(
			'fields' => array(
				'MstAdminUser.id',
				'MstAdminUser.contract_date',
				'MstAdminUser.cancellation_date'
			),
			'conditions' => array(
				'MstAdminUser.chain_id' => $company_id,
				'MstAdminUser.acc_grant' => 3
			)
		);
		if ($data = $this->find('all', $params)) {
			foreach ($data as $res) {
				$n = $dy2;
				$coDay = strtotime($res['MstAdminUser']['contract_date']);
				$caDay = $res['MstAdminUser']['cancellation_date'] != null ? strtotime($res['MstAdminUser']['cancellation_date']) : time();
				for (;;) {
					if ($key != 0) {
						$a = mktime(0, 0, 0, date('m', $n), $key, date('Y', $n));
					} else {
						$a = mktime(0, 0, 0, date('m', $n), 1, date('Y', $n));
					}
					if ($coDay <= $a && $caDay > $a) {
						$results[$n]['coun']++;
//						$results[$n]['data'][] = $res['MstAdminUser'];
					}

					$n = strtotime('+1 month', $n);
					if ($n > $dy1) {
						break;
					}
				}
			}
			foreach ($results as $n => $res) {
				$results[$n]['pric'] = number_format($res['coun'] * $val['MstAdminUser']['price']);
				if ($res['coun'] > 0) {
					if ($key != 0) {
						$a = mktime(0, 0, 0, date('m', $n) + 1, $key, date('Y', $n));
					} else {
						$a = mktime(0, 0, 0, date('m', $n) + 1, 0, date('Y', $n));
					}
					switch ($val['MstAdminUser']['payment_type'][1]) {
						case 0:	# 翌月末払い
							$b = mktime(0, 0, 0, date('m', $n) + 2, 0, date('Y', $n));
							break;
						case 1:	# 翌々月末払い
							$b = mktime(0, 0, 0, date('m', $n) + 3, 0, date('Y', $n));
							break;
						case 2:	# 翌々々月末払い
							$b = mktime(0, 0, 0, date('m', $n) + 4, 0, date('Y', $n));
							break;
					}
					$results[$n]['dy1'] = date('Y年n月j日', $a);
					$results[$n]['dy2'] = date('Y年n月j日', $b);
				} else {
					$results[$n]['dy1'] = '-';
					$results[$n]['dy2'] = '-';
				}
			}
		}
		$val['Bill'] = $results;
		return $val;
	}
}
