<?php
/**
 * Static content controller.
 *
 * This file will render views from views/pages/
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('AppController', 'Controller');

/**
 * Static content controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers/pages-controller.html
 */
class ReportController extends AppController {

	public $components = array(
		'Paginator',
//		'Search.Prg'
	);


	public function beforeFilter() {
		parent::beforeFilter();
//		$this->Auth->allow();
	}


	public function beforeRender() {
		parent::beforeRender();
	}


	public function shop($from = null, $to = null) {
		if (isset($this->request->data['TrnThanks']['reservation'])) {
			$val = explode(' - ', $this->request->data['TrnThanks']['reservation']);
			$from = strtotime($val[0]);
			$to = strtotime($val[1]);
			$this->request->data['TrnThanks']['from'] = $from;
			$this->request->data['TrnThanks']['to'] = $to;
		} else {
			if ($from === null) {
				$from = mktime(0, 0, 0, date('m'), 1, date('y'));
			}
			if ($to === null) {
				$to = mktime(0, 0, 0, date('m') + 1, 0, date('y'));
			}
			$this->request->data['TrnThanks']['reservation'] = date('Y-m-d', $from).' - '.date('Y-m-d', $to);
			$this->request->data['TrnThanks']['from'] = $from;
			$this->request->data['TrnThanks']['to'] = $to;
		}
		$this->title = 'チームサンクス数：'.$this->request->data['TrnThanks']['reservation'];
		$conditions = array(
			'UNIX_TIMESTAMP(CONCAT(years,"-",months,"-",days)) >=' => $from,
			'UNIX_TIMESTAMP(CONCAT(years,"-",months,"-",days)) <=' => $to,
			'TrnMembers.bot_flg' => 0,
			'NOT' => array('TrnMembers.member_id' => 999999999)
		);
		switch ($this->Auth->user('acc_grant')) {
			case 1:	//代理店
				$bar = $this->MstAdminUser->find('list', array(
					'conditions' => array(
						'MstAdminUser.agency_id' => $this->Auth->user('id')
					)
				));
				if ($bar) {
					$conditions += array('TrnThanksCnt.shop_id' => array_keys($bar));
				}
				break;
			case 2:	//加盟店
				$bar = $this->MstAdminUser->find('list', array(
					'conditions' => array(
						'MstAdminUser.chain_id' => $this->Auth->user('id')
					)
				));
				if ($bar) {
					$conditions += array('TrnThanksCnt.shop_id' => array_keys($bar));
				}
				break;
			case 3:	//チーム
				$conditions += array('TrnThanksCnt.shop_id' => $this->Auth->user('id'));
				break;
		}
		$this->TrnThanksCnt->recursive = 0;
		$data = array();
		$sums['TOTA'] = 0;
		$sums['REPO'] = 0;
		$sums['_REPO'] = null;
		$sums['MENB'] = 0;
		$sums['DARY'] = 0;
		$sums['TWPT'] = 0;

		# 店舗ごとの総サンクス数を集計
		$data2 = $this->TrnThanksCnt->find('all', array(
			'fields' => array(
				'TrnThanksCnt.shop_id',
				'sum(TrnThanksCnt.thanks_sends) as sum_thanks_sends'
			),
			'conditions' => $conditions,
			'group' => array('TrnThanksCnt.shop_id')
		));
		if ($data2) {
			# 店舗リストを呼び出し
			$shops = $this->MstAdminUser->find('list', array(
				'conditions' => array(
					'MstAdminUser.acc_grant' => 3
				)
			));

			# 期間中の日記投稿数を集計
			$diaries = array();
			$data3 = $this->TrnDiary->find('all', array(
				'fields' => array(
					'TrnDiary.shop_id',
					'count(*) as nums'
				),
				'conditions' => array(
					'TrnDiary.insert_time >=' => date('Y-m-d 00:00:00', $from),
					'TrnDiary.insert_time <=' => date('Y-m-d 23:59:59', $to),
					'TrnDiary.del_flg' => 0
				),
				'group' => array('TrnDiary.shop_id')
			));
			if ($data3) {
				foreach ($data3 as $res) {
					$diaries += array($res['TrnDiary']['shop_id'] => $res[0]['nums']);
				}
			}

			foreach ($data2 as $res) {
				$shop_id = $res['TrnThanksCnt']['shop_id'];
				if (isset($shops[$shop_id])) {
					$this->TrnTeams->recursive = 0;
					$this->TrnTeams->unbindModel(array('belongsTo' => array('MstAdminUser')));
					$memberNum = $this->TrnTeams->find('count', array(
						'conditions' => array(
							'TrnTeams.shop_id' => $res['TrnThanksCnt']['shop_id'],
							'TrnTeams.del_flg' => 0,
							'TrnMembers.bot_flg' => 0
						)
					));
					$reportNum = $res[0]['sum_thanks_sends'] / $memberNum;
					$diaryNum = isset($diaries[$shop_id]) ? $diaries[$shop_id] : 0;
					$TWpoint = 0;

					$data[] = array(
						'MstAdminUser' => array(
							'id' => $shop_id,
							'aname' => $shops[$shop_id]
						),
						'TrnThanksSumShop' => array(
							'thanks_sends' => $res[0]['sum_thanks_sends']
						),
						'Report' => array(
							'memberNum' => $memberNum,
							'reportNum' => $reportNum,
							'label' => $this->TrnThanks->label4num($reportNum),
							'diaryNum' => $diaryNum,
							'TWpoint' => $TWpoint
						)
					);
					$sums['TOTA'] += $res[0]['sum_thanks_sends'];
					$sums['REPO'] += $reportNum;
					$sums['MENB'] += $memberNum;
					$sums['DARY'] += $diaryNum;
					$sums['TWPT'] += $TWpoint;
				}
			}
			$sums['REPO'] = $sums['REPO'] / count($data2);
			$sums['_REPO'] = $this->TrnThanks->label4num($sums['REPO']);

			$sums['TWPT'] = $sums['TWPT'] / count($data2);
		}

		$this->log($data);

		$this->set(compact(
			'data',
			'sums'
		));
	}


	public function staff($from = null, $to = null, $shop_id = null) {
		if (isset($this->request->data['TrnThanks']['reservation'])) {
			$val = explode(' - ', $this->request->data['TrnThanks']['reservation']);
			$from = strtotime($val[0]);
			$to = strtotime($val[1]);
			$this->request->data['TrnThanks']['from'] = $from;
			$this->request->data['TrnThanks']['to'] = $to;
		} else {
			if ($from === null) {
				$from = mktime(0, 0, 0, date('m'), 1, date('y'));
			}
			if ($to === null) {
				$to = mktime(0, 0, 0, date('m') + 1, 0, date('y'));
			}
			$this->request->data['TrnThanks']['reservation'] = date('Y-m-d', $from).' - '.date('Y-m-d', $to);
			$this->request->data['TrnThanks']['from'] = $from;
			$this->request->data['TrnThanks']['to'] = $to;
		}
		$this->title = 'メンバーサンクス数：'.$this->request->data['TrnThanks']['reservation'];
		$conditions = array(
			'UNIX_TIMESTAMP(CONCAT(years,"-",months,"-",days)) >=' => $from,
			'UNIX_TIMESTAMP(CONCAT(years,"-",months,"-",days)) <=' => $to,
			'TrnMembers.bot_flg' => 0,
			'NOT' => array('TrnMembers.member_id' => 999999999)
		);

		if ($shop_id !== null) {
			$conditions += array('TrnThanksCnt.shop_id' => $shop_id);
			$bar = $this->MstAdminUser->find('first', array(
				'fields' => array('MstAdminUser.aname'),
				'conditions' => array('MstAdminUser.id' => $shop_id)
			));
			$this->title .= '&nbsp;'.$bar['MstAdminUser']['aname'];
		} else {
			switch ($this->Auth->user('acc_grant')) {
				case 1:	//代理店
					$bar = $this->MstAdminUser->find('list', array(
						'conditions' => array(
							'MstAdminUser.agency_id' => $this->Auth->user('id')
						)
					));
					if ($bar) {
						$conditions += array('TrnThanksCnt.shop_id' => array_keys($bar));
					}
					break;
				case 2:	//加盟店
					$bar = $this->MstAdminUser->find('list', array(
						'conditions' => array(
							'MstAdminUser.chain_id' => $this->Auth->user('id')
						)
					));
					if ($bar) {
						$conditions += array('TrnThanksCnt.shop_id' => array_keys($bar));
					}
					break;
				case 3:	//チーム
					$conditions += array('TrnThanksCnt.shop_id' => $this->Auth->user('id'));
					break;
			}
		}
		$this->TrnThanksCnt->recursive = 0;
		$data = array();
		$sums['RECE'] = 0;
		$sums['SEND'] = 0;

		$data2 = $this->TrnThanksCnt->find('all', array(
			'fields' => array(
				'TrnMembers.member_id',
				'TrnMembers.member_name',
				'TrnThanksCnt.shop_id',
				'TrnThanksCnt.menber_id',
				'sum(TrnThanksCnt.thanks_receives) as sum_thanks_receives',
				'sum(TrnThanksCnt.thanks_sends) as sum_thanks_sends'
			),
			'conditions' => $conditions,
			'group' => array('TrnThanksCnt.menber_id','TrnThanksCnt.shop_id')
		));
		if ($data2) {
			foreach ($data2 as $res) {
				$data[] = array(
					'TrnMembers' => array(
						'shop_id' => $res['TrnThanksCnt']['shop_id'],
						'member_id' => $res['TrnMembers']['member_id'],
						'member_name' => $res['TrnMembers']['member_name']
					),
					'TrnThanksSumMember' => array(
						'thanks_receives' => $res[0]['sum_thanks_receives'],
						'thanks_sends' => $res[0]['sum_thanks_sends']
					)
				);
				$sums['RECE'] += $res[0]['sum_thanks_receives'];
				$sums['SEND'] += $res[0]['sum_thanks_sends'];
			}
		}

		$this->set(compact(
			'shop_id',
			'data',
			'sums'
		));
	}


	public function detail($from = null, $to = null) {
		if (isset($this->request->data['TrnThanks']['reservation'])) {
			$val = explode(' - ', $this->request->data['TrnThanks']['reservation']);
			$from = strtotime($val[0]);
			$to = strtotime($val[1]);
		} else {
			if ($from === null) {
				$from = mktime(0, 0, 0, date('m'), 1, date('y'));
			}
			if ($to === null) {
				$to = mktime(0, 0, 0, date('m') + 1, 0, date('y'));
			}
			$this->request->data['TrnThanks']['reservation'] = date('Y-m-d', $from).' - '.date('Y-m-d', $to);
		}
		if (isset($this->request->data['TrnThanks']['agency_id'])) {
			$agencyId = !empty($this->request->data['TrnThanks']['agency_id']) ? $this->request->data['TrnThanks']['agency_id'] : null;
		} else {
			$agencyId = null;
		}
		if (isset($this->request->data['TrnThanks']['chain_id'])) {
			$chainId = !empty($this->request->data['TrnThanks']['chain_id']) ? $this->request->data['TrnThanks']['chain_id'] : null;
		} else {
			$chainId = null;
		}

		if (isset($this->request->data['TrnThanks']['status'])) {
			$status = $this->request->data['TrnThanks']['status'] !== '' ? $this->request->data['TrnThanks']['status'] : null;
		} else {
			$status = null;
		}
		if (isset($this->request->data['TrnThanks']['trial_flg'])) {
			$trialFlg = $this->request->data['TrnThanks']['trial_flg'] !== '' ? $this->request->data['TrnThanks']['trial_flg'] : null;
		} else {
			$trialFlg = null;
		}

		$data = array(
			0 => array(),	# チームアクセス数
			1 => array()	# メンバーごとのサンクス数
		);

		# ログイン権限に応じて、集計するチームのリストを作成する
		switch ($this->Auth->user('acc_grant')) {
			case 0: //元売
				$conditions = array(
					'MstAdminUser.acc_grant' => 3
				);
				$renderType = 'detail_type1';
				break;
			case 1:	//代理店
				$conditions = array(
					'MstAdminUser.acc_grant' => 3,
					'MstAdminUser.agency_id' => $this->Auth->user('id')
				);
				$renderType = 'detail_type2';
				break;
			case 2:	//加盟店
				$conditions = array(
					'MstAdminUser.acc_grant' => 3,
					'MstAdminUser.chain_id' => $this->Auth->user('id')
				);
				$renderType = 'detail_type3';
				break;
			case 3:	//チーム
				$this->redirect(array('action' => 'shopDetail', $this->Auth->user('id'), $from, $to));
				break;
		}

		## 日別のチームサンクス数を呼び出します
		# 先にボックスを用意
		$x = $from;
		for (;;) {
			$data[0] += array($x => 0);
			$x = strtotime('+1 day', $x);
			if ($x > $to) {
				break;
			}
		}

		# 集計用のチームIDを取り出す
		$x = count($conditions);
		$shop_ids = array_keys($this->MstAdminUser->find('list', array(
			'conditions' => $conditions
		)));
		if ($agencyId !== null) {
			$conditions['MstAdminUser.agency_id'] = $agencyId;
		}
		if ($chainId !== null) {
			$conditions['MstAdminUser.chain_id'] = $chainId;
		}
		if ($status !== null) {
			$conditions['MstAdminUser.status'] = $status;
		}
		if ($trialFlg !== null) {
			$conditions['MstAdminUser.trial_flg'] = $trialFlg;
		}
		if (count($conditions) != $x) {
			$shop_ids2 = array_keys($this->MstAdminUser->find('list', array('conditions' => $conditions)));
		} else {
			$shop_ids2 = $shop_ids;
		}

		# 集計対象外のBOTユーザを検索
		$data2 = $this->TrnMembers->find('list', array(
			'conditions' => array(
				'TrnMembers.bot_flg' => 1
			)
		));
		if ($data2) {
			$bots = array_keys($data2);
		} else {
			$bots = array();
		}

		# サンクス履歴から日別のサンクス数を集計
		$data2 = $this->TrnThanks->find('all', array(
			'fields' => array(
				'DATE_FORMAT(TrnThanks.send_time, "%Y-%m-%d") as days',
				'count(*) as nums'
			),
			'conditions' => array(
				'TrnThanks.shop_id' => $shop_ids2,
				'TrnThanks.send_time >=' => date('Y-m-d H:i:s', $from),
				'TrnThanks.send_time <=' => date('Y-m-d H:i:s', $to),
				'NOT' => array('TrnThanks.from_id' => $bots)
			),
			'group' => array(
				'DATE_FORMAT(TrnThanks.send_time, "%Y-%m-%d")'
			)
		));
		if ($data2) {
			foreach ($data2 as $res) {
				$key = strtotime($res[0]['days']);
				$data[0][$key] = $res[0]['nums'];
			}
		}

		# 集計可能なチームリスト
		$shops = array();
		$data2 = $this->MstAdminUser->find('all', array(
			'conditions' => array(
				'MstAdminUser.id' => $shop_ids2
			),
			'order' => array(
				'MstAdminUser.agency_id',
				'MstAdminUser.chain_id',
				'MstAdminUser.id'
			)
		));
		if ($data2) {
			$tmpAgencyId = null;
			$tmpChainId = null;
			foreach ($data2 as $res) {
				$agency_id = $res['MstAdminUser']['agency_id'];
				$chain_id = $res['MstAdminUser']['chain_id'];
				if (empty($agency_id) || empty($chain_id)) {
					continue;
				}
				if ($agency_id != $tmpAgencyId) {
					$tmp = $this->MstAdminUser->find('first', array('conditions' => array('MstAdminUser.id' => $agency_id)));
					$shops += array($agency_id => array(
						'name' => $tmp['MstAdminUser']['aname'],
						'data' => array()
					));
					$tmpAgencyId = $agency_id;
				}
				if ($chain_id != $tmpChainId) {
					$tmp = $this->MstAdminUser->find('first', array('conditions' => array('MstAdminUser.id' => $chain_id)));
					$shops[$tmpAgencyId]['data'] += array($chain_id => array(
						'name' => $tmp['MstAdminUser']['aname'],
						'data' => array()
					));

					$tmpChainId = $chain_id;
				}
				$shops[$tmpAgencyId]['data'][$tmpChainId]['data'] += array($res['MstAdminUser']['id'] => $res['MstAdminUser']['aname']);
			}
		}

		# 登録メンバーの総数
		$member_num = $this->TrnTeams->find('count', array(
			'conditions' => array(
				'TrnTeams.shop_id' => $shop_ids2,
				'TrnTeams.del_flg' => 0
			)
		));

		# スタンプごとの利用割合を取り出す
		$data2 = $this->TrnThanks->find('all', array(
			'fields' => array(
				'TrnThanks.stamp_id',
				'count(*) as nums'
			),
			'conditions' => array(
				'TrnThanks.shop_id' => $shop_ids2,
				'TrnThanks.send_time >=' => date('Y-m-d H:i:s', $from),
				'TrnThanks.send_time <=' => date('Y-m-d H:i:s', $to),
				'NOT' => array('TrnThanks.from_id' => $bots)
			),
			'group' => array('TrnThanks.stamp_id')
		));
		$data3 = array();
		$data4 = array('BEST' => array(), 'WRST' => array());
		$tmp = array();
		if ($data2) {
			foreach ($data2 as $res) {
				$tmp += array($res['TrnThanks']['stamp_id'] => $res[0]['nums']);
			}
		}
		$categories = $this->MstThanksStampCategory->find('list', array(
			'conditions' => array(
				'MstThanksStampCategory.status' => 0
			)
		));
		$stamps = array();
		$data2 = $this->MstThanksStamp->find('all', array(
			'conditions' => array(
				'MstThanksStamp.status' => 0
			),
			'order' => array('MstThanksStamp.category_id')
		));
		if ($data2) {
			$tmp2 = array();
			$tmp3 = array();
			foreach ($data2 as $res) {
				$category_id = $res['MstThanksStamp']['category_id'];
				$stamp_id = $res['MstThanksStamp']['stamp_id'];
				$tmp2[$category_id][$stamp_id] = isset($tmp[$stamp_id]) ? $tmp[$stamp_id] : 0;
				$tmp3[$stamp_id] = isset($tmp[$stamp_id]) ? $tmp[$stamp_id] : 0;

				$stamps[$stamp_id] = array(
					'stamp_name' => $res['MstThanksStamp']['stamp_name'],
					'image_url' => $res['MstThanksStamp']['image_url']
				);
			}
			foreach ($tmp2 as $category_id => $res) {
				$data3 += array($category_id => array(
					'name' => $categories[$category_id],
					'data' => array()
				));
				arsort($tmp2[$category_id]);
				$sum = array_sum($tmp2[$category_id]);
				foreach ($tmp2[$category_id] as $stamp_id => $nums) {
					$var = $stamps[$stamp_id];
					$var['nums'] = $nums;
					$var['rate'] = $nums > 0 ? round(($nums / $sum) * 100) : 0;
					$data3[$category_id]['data'][$stamp_id] = $var;
				}
			}

			arsort($tmp3);
			$n = 0;
			foreach ($tmp3 as $stamp_id => $nums) {
				$n++;
				$var = $stamps[$stamp_id];
				$var['nums'] = $nums;
				$data4['BEST'][$stamp_id] = $var;
			}
		}

		$this->title = '総サンクス数：'.date('Y/n/j', $from).'　～　'.date('Y/n/j', $to);
		$this->set(compact(
			'from',
			'to',
			'agencyId',
			'chainId',
			'data',
			'data3',
			'data4',
			'shops',
			'member_num'
		));
		$this->set('status', $this->MstAdminUser->readStatus());
		$this->set('trialFlgs', $this->MstAdminUser->readTrialFlgs(1));
		$this->render($renderType);
	}


	public function shopDetail($shop_id = null, $from = null, $to = null) {
		if ($shop_id === null) {
			$this->redirect(array('action' => 'shop'));
		}
		if (isset($this->request->data['TrnThanks']['reservation'])) {
			$val = explode(' - ', $this->request->data['TrnThanks']['reservation']);
			$from = strtotime($val[0]);
			$to = strtotime($val[1]);
		} else {
			if ($from === null) {
				$from = mktime(0, 0, 0, date('m'), 1, date('y'));
			}
			if ($to === null) {
				$to = mktime(0, 0, 0, date('m') + 1, 0, date('y'));
			}
			$this->request->data['TrnThanks']['reservation'] = date('Y-m-d', $from).' - '.date('Y-m-d', $to);
		}
		$data = array(
			0 => array(),	# チームアクセス数
			1 => array()	# メンバーごとのサンクス数
		);

		# チーム情報
		$shopData = $this->MstAdminUser->find('first', array(
			'conditions' => array(
				'MstAdminUser.id' => $shop_id,
				'MstAdminUser.acc_grant' => 3
			)
		));

		# スタッフリスト
		$bar = $this->TrnTeams->find('list', array(
			'conditions' => array(
				'TrnTeams.shop_id' => $shop_id,
				'TrnTeams.del_flg' => 0,
			)
		));
		$members = $this->TrnMembers->find('list', array('conditions' => array(
			'TrnMembers.member_id' => array_keys($bar),
#			'TrnMembers.shop_id' => $shop_id,
			'TrnMembers.status' => 2,
			'TrnMembers.bot_flg' => 0,
		)));
		
		$_from = date('Y-m-d 00:00:00', $from);
		$_to = date('Y-m-d 23:59:59', $to);

		## 日別のチームサンクス数を呼び出します
		# 先にボックスを用意
		$x = $from;
		for (;;) {
			$data[0] += array($x => 0);
			$x = strtotime('+1 day', $x);
			if ($x > $to) {
				break;
			}
		}

		# サンクス履歴から日別のサンクス数を集計
		$data2 = $this->TrnThanks->find('all', array(
			'fields' => array(
				'DATE_FORMAT(TrnThanks.send_time, "%Y-%m-%d") AS send_date',
				'count(*) as nums'
			),
			'conditions' => array(
				'TrnThanks.send_time >=' => $_from,
				'TrnThanks.send_time <=' => $_to,
				'TrnThanks.from_id' => array_keys($members)
			),
			'group' => array('send_date')
		));
		if ($data2) {
			foreach ($data2 as $res) {
				$key = strtotime($res[0]['send_date']);
				$data[0][$key] = $res[0]['nums'];
			}
		}

		## スタッフごとのサンクス数を呼び出します
		# 先にボックスを用意
		$a = 0;
		$b = 0;
		foreach ($members as $id => $name) {
			$data[1][$id] = array(
				'name' => mb_strimwidth(str_replace(array("\r\n", "\r", "\n"), '', $name), 0, 12, '..', 'UTF-8'),
				'thanks_receives' => 0,
				'thanks_sends' => 0,
				'profile_img_url'
			);
		}
		$data2 = $this->TrnMembers->find('all', array(
			'fields' => array(
				'TrnMembers.profile_img_url',
				'TrnMembers.member_id'
			),
			'conditions' => array(
				'TrnMembers.member_id' => array_keys($members)
			)));
		if ($data2) {
			foreach ($data2 as $res) {

				$id = $res['TrnMembers']['member_id'];
				if (isset($data[1][$id])) {
					$data[1][$id]['profile_img_url'] = $res['TrnMembers']['profile_img_url'];
				}
			}
		}
		# サンクス履歴から獲得数を集計
		$data2 = $this->TrnThanks->find('all', array(
			'fields' => array(
				'TrnThanks.target_id',
				'count(*) as nums'
			),
			'conditions' => array(
				'TrnThanks.send_time >=' => $_from,
				'TrnThanks.send_time <=' => $_to,
				'TrnThanks.target_id' => array_keys($members)
			),
			'group' => array('TrnThanks.target_id')
		));
		
		if ($data2) {
			foreach ($data2 as $res) {
				$id = $res['TrnThanks']['target_id'];
				if (isset($data[1][$id])) {
					$data[1][$id]['thanks_receives'] = $res[0]['nums'];
					$a += $res[0]['nums'];
				}
			}
		}
		# サンクス履歴から送信数を集計
		$data2 = $this->TrnThanks->find('all', array(
			'fields' => array(
				'TrnThanks.from_id',
				'count(*) as nums'
			),
			'conditions' => array(
				'TrnThanks.send_time >=' => $_from,
				'TrnThanks.send_time <=' => $_to,
				'TrnThanks.from_id' => array_keys($members)
			),
			'group' => array('TrnThanks.from_id')
		));

		if ($data2) {
			foreach ($data2 as $res) {
				$id = $res['TrnThanks']['from_id'];
				if (isset($data[1][$id])) {
					$data[1][$id]['thanks_sends'] = $res[0]['nums'];
					$b += $res[0]['nums'];
				}
			}
		}

		$data2 = $this->TrnThanks->adjustmentData($data[1]);
		if ($this->Auth->user('acc_grant') <= 1) {
			# スタンプごとの利用割合を取り出す
			$data3 = array();
			$data4 = array('BEST' => array(), 'WRST' => array());
			$tmp = array();
			$var = $this->TrnThanks->find('all', array(
				'fields' => array(
					'TrnThanks.stamp_id',
					'count(*) as nums'
				),
				'conditions' => array(
					'TrnThanks.shop_id' => $shop_id,
					'TrnThanks.send_time >=' => $_from,
					'TrnThanks.send_time <=' => $_to
				),
				'group' => array('TrnThanks.stamp_id')
			));
			if ($var) {
				foreach ($var as $res) {
					$tmp += array($res['TrnThanks']['stamp_id'] => $res[0]['nums']);
				}
			}
			$categories = $this->MstThanksStampCategory->find('list', array(
				'conditions' => array(
					'MstThanksStampCategory.status' => 0
				)
			));
			$stamps = array();
			$var = $this->MstThanksStamp->find('all', array(
				'conditions' => array(
					'MstThanksStamp.status' => 0
				),
				'order' => array('MstThanksStamp.category_id')
			));
			if ($var) {
				$tmp2 = array();
				$tmp3 = array();
				foreach ($var as $res) {
					$category_id = $res['MstThanksStamp']['category_id'];
					$stamp_id = $res['MstThanksStamp']['stamp_id'];
					$tmp2[$category_id][$stamp_id] = isset($tmp[$stamp_id]) ? $tmp[$stamp_id] : 0;
					$tmp3[$stamp_id] = isset($tmp[$stamp_id]) ? $tmp[$stamp_id] : 0;

					$stamps[$stamp_id] = array(
						'stamp_name' => $res['MstThanksStamp']['stamp_name'],
						'image_url' => $res['MstThanksStamp']['image_url']
					);
				}
				foreach ($tmp2 as $category_id => $res) {
					$data3 += array($category_id => array(
						'name' => $categories[$category_id],
						'data' => array()
					));
					arsort($tmp2[$category_id]);
					$sum = array_sum($tmp2[$category_id]);
					foreach ($tmp2[$category_id] as $stamp_id => $nums) {
						$var = $stamps[$stamp_id];
						$var['nums'] = $nums;
						$var['rate'] = $nums > 0 ? round(($nums / $sum) * 100) : 0;
						$data3[$category_id]['data'][$stamp_id] = $var;
					}
				}

				arsort($tmp3);
				$n = 0;
				foreach ($tmp3 as $stamp_id => $nums) {
					$n++;
					$var = $stamps[$stamp_id];
					$var['nums'] = $nums;
					$data4['BEST'][$stamp_id] = $var;
				}
			}
			$render_file = 'shop_detail';
		} else {
			$render_file = 'shop_detail2';
		}

		#tab-report
		$sumThanksSends = 0;
		$sumThanksReceives = 0;
		$countUser= 0;
		$countUserThanked = 0;
		foreach ($data[1] as $var) {
			$sumThanksSends += $var['thanks_sends'];
			$sumThanksReceives += $var['thanks_receives'];
			$countUser ++;
			if($var['thanks_sends'] != null)
				$countUserThanked ++;
		}
		$datediff = abs(strtotime($_from) - strtotime($_to));
		$day= floor($datediff / (60*60*24));
		$sumAll = $sumThanksSends + $sumThanksReceives;
		# X,Y,Z is radar chart data
		# 平均thanks!頻度
		$X = round((($sumAll*7) / ($day * $countUser)) * 4 / 3,1); 	
		# 起動アクティブ率
		$Y = 3.5;
		# 送信アクティブ率
		$Z = round ($countUserThanked/$countUser*5,1);	
		# Max value = 5	
		if($X > 5)
			$X = 5;
		if($Y > 5)
			$Y = 5;
		if($Z > 5)
			$Z = 5;
		$XYZ = round((($X*$Y + $Y*$Z + $Z*$X)/(5*5*3))*100) ;
		if($XYZ > 100)
			$XYZ = 100;
		$this->title = $shopData['MstAdminUser']['aname'].'のサンクス数：'.date('Y/n/j', $from).'　～　'.date('Y/n/j', $to);
		$this->set(compact(
			'shop_id',
			'from',
			'to',
			'data',
			'data2',
			'data3',
			'data4',
			'X',
			'Y',
			'Z',
			'XYZ'			
		));
		$this->render($render_file);
	}


	public function staffDetail($shop_id = null, $staff_id = null, $from = null, $to = null) {
		if ($shop_id === null || $staff_id === null) {
			$this->redirect(array('action' => 'shop'));
		}
		if ($from === null) {
			$from = mktime(0, 0, 0, date('m'), 1, date('y'));
		}
		if ($to === null) {
			$to = mktime(0, 0, 0, date('m') + 1, 0, date('y'));
		}
		if (isset($this->request->data['TrnThanks']['reservation'])) {
			$val = explode(' - ', $this->request->data['TrnThanks']['reservation']);
			$from = strtotime($val[0]);
			$to = strtotime($val[1]);
		} else {
			$this->request->data['TrnThanks']['reservation'] = date('Y-m-d', $from).' - '.date('Y-m-d', $to);
		}
		$data = array(
			0 => array(),	# 日別のサンクス数
			1 => array()	# メンバーとのサンクス数
		);

		# スタッフリスト
#		$members = $this->TrnMembers->find('list', array('conditions' => array(
#			'TrnMembers.shop_id' => $shop_id,
#			'TrnMembers.status' => 2
#		)));
		$members = array();
		$this->TrnTeams->recursive = 0;
		$this->TrnTeams->unbindModel(array('belongsTo' => array('MstAdminUser')));
		$data2 = $this->TrnTeams->find('all', array(
			'conditions' => array(
				'TrnTeams.shop_id' => $shop_id,
				'TrnMembers.bot_flg' => 0
			)
		));
		if ($data2) {
			foreach ($data2 as $res) {
				$members += array($res['TrnMembers']['member_id'] => $res['TrnMembers']['member_name']);
			}
		}

		$_from = date('Y-m-d 00:00:00', $from);
		$_to = date('Y-m-d 23:59:59', $to);

		## 日別のチームサンクス数を呼び出します
		# 先にボックスを用意
		$x = $from;
		for (;;) {
			$data[0] += array($x => array(0 => 0, 1 => 0));
			$x = strtotime('+1 day', $x);
			if ($x > $to) {
				break;
			}
		}
		# サンクス履歴から日別の獲得サンクス数を集計
		$data2 = $this->TrnThanks->find('all', array(
			'fields' => array(
				'DATE_FORMAT(TrnThanks.send_time, "%Y-%m-%d") AS send_date',
				'count(*) as nums'
			),
			'conditions' => array(
				'TrnThanks.send_time >=' => $_from,
				'TrnThanks.send_time <=' => $_to,
				'TrnThanks.target_id' => $staff_id,
				'TrnThanks.from_id' => array_keys($members)
			),
			'group' => array('send_date')
		));
		if ($data2) {
			foreach ($data2 as $res) {
				$key = strtotime($res[0]['send_date']);
				$data[0][$key][0] = $res[0]['nums'];
			}
		}
		# サンクス履歴から日別の送信サンクス数を集計
		$data2 = $this->TrnThanks->find('all', array(
			'fields' => array(
				'DATE_FORMAT(TrnThanks.send_time, "%Y-%m-%d") AS send_date',
				'count(*) as nums'
			),
			'conditions' => array(
				'TrnThanks.send_time >=' => $_from,
				'TrnThanks.send_time <=' => $_to,
				'TrnThanks.from_id' => $staff_id
			),
			'group' => array('send_date')
		));
		if ($data2) {
			foreach ($data2 as $res) {
				$key = strtotime($res[0]['send_date']);
				$data[0][$key][1] = $res[0]['nums'];
			}
		}

		## 他スタッフとのサンクス数を呼び出します
		# 先にボックスを用意
		foreach ($members as $id => $name) {
			if ($id == $staff_id) {
				$staff_name = $name;
			} else {
				$data[1] += array($id => array(
					'name' => mb_strimwidth(str_replace(array("\r\n", "\r", "\n"), '', $name), 0, 12, '..', 'UTF-8'),
					'thanks_receives' => 0,
					'thanks_sends' => 0
				));
			}
		}
		# サンクス履歴から獲得数を集計
		$data2 = $this->TrnThanks->find('all', array(
			'fields' => array(
				'TrnThanks.from_id',
				'count(*) as nums'
			),
			'conditions' => array(
				'TrnThanks.send_time >=' => $_from,
				'TrnThanks.send_time <=' => $_to,
				'TrnThanks.target_id' => $staff_id
			),
			'group' => array('TrnThanks.from_id')
		));
		if ($data2) {
			foreach ($data2 as $res) {
				$id = $res['TrnThanks']['from_id'];
				if (isset($data[1][$id])) {
					$data[1][$id]['thanks_receives'] = $res[0]['nums'];
				}
			}
		}
		# サンクス履歴から送信数を集計
		$data2 = $this->TrnThanks->find('all', array(
			'fields' => array(
				'TrnThanks.target_id',
				'count(*) as nums'
			),
			'conditions' => array(
				'TrnThanks.send_time >=' => $_from,
				'TrnThanks.send_time <=' => $_to,
				'TrnThanks.from_id' => $staff_id
			),
			'group' => array('TrnThanks.target_id')
		));
		if ($data2) {
			foreach ($data2 as $res) {
				$id = $res['TrnThanks']['target_id'];
				if (isset($data[1][$id])) {
					$data[1][$id]['thanks_sends'] = $res[0]['nums'];
				}
			}
		}

		$this->title = $staff_name.'のサンクス数：'.date('Y/n/j', $from).'　～　'.date('Y/n/j', $to);
		$this->set(compact(
			'shop_id',
			'staff_id',
			'from',
			'to',
			'data'
		));
	}


	public function stamp($datetime = null){
		if ($datetime === null) {
			$datetime = mktime(0, 0, 0, date('m'), 1, date('Y'));
		}
		$trade_year = date('Y', $datetime);
		$trade_month = date('m', $datetime);

		# 加盟店一覧を取る
		switch ($this->Auth->user('acc_grant')) {
			case 0:	//元売
				$this->paginate = array(
					'MstAdminUser' => array(
						'conditions' => array(
							'MstAdminUser.acc_grant' => 2,
							'MstAdminUser.status' => 0
						)
					)
				);
				break;
			case 1:	//代理店
				$this->paginate = array(
					'MstAdminUser' => array(
						'conditions' => array(
							'MstAdminUser.acc_grant' => 2,
							'MstAdminUser.agency_id' => $this->Auth->user('id'),
							'MstAdminUser.status' => 0
						)
					)
				);
				break;
			case 2:	//加盟店
				$this->redirect('/report/stampCompany/'.$this->Auth->user('id'));
				break;
			case 3:	//チーム
				$this->redirect('/report/stampShop/'.$this->Auth->user('id'));
				break;
		}
		$data = $this->Paginator->paginate('MstAdminUser');
		foreach ($data as $key => $res) {
			# 店舗一覧を取り出す
			$shops = $this->MstAdminUser->find('list', array(
				'conditions' => array(
					'MstAdminUser.acc_grant' => 3,
					'MstAdminUser.chain_id' => $res['MstAdminUser']['id']
				)
			));

			# 加盟店全体での配付・交換ポイントを履歴から集計
			$trades = array(
				'addition' => 0,
				'exchange' => 0
			);
			if ($shops) {
				$data2 = $this->TrnPointTrade->find('all', array(
					'fields' => array(
						'TrnPointTrade.trade_kind',
						'sum(trade_point) as nums'
					),
					'conditions' => array(
						'TrnPointTrade.shop_id' => array_keys($shops),
						'TrnPointTrade.trade_year' => $trade_year,
						'TrnPointTrade.trade_month' => $trade_month,
						'TrnPointTrade.status' => 0
					),
					'group' => array('TrnPointTrade.trade_kind')
				));
				if ($data2) {
					foreach ($data2 as $res2) {
						switch ($res2['TrnPointTrade']['trade_kind']) {
							case 1: case 2: case 3: case 4: case 5:
								$trades['addition'] += $res2[0]['nums'];
								break;
							case 50:
								$trades['exchange'] += abs($res2[0]['nums']);
								break;
						}
					}
				}
			}
			$data[$key]['TrnPointTrade'] = $trades;
		}
		$this->title = date('Y年m月', $datetime).'の加盟店別、ポイントレポート';
		$this->set(compact(
			'data',
			'datetime'
		));
	}


	public function stampDownload($datetime = null) {
		if ($datetime === null) {
			$datetime = mktime(0, 0, 0, date('m'), 1, date('Y'));
		}
		$trade_year = date('Y', $datetime);
		$trade_month = date('m', $datetime);

		# 加盟店一覧を取り出す
		switch ($this->Auth->user('acc_grant')) {
			case 0:	//元売
				$params = array(
					'conditions' => array(
						'MstAdminUser.acc_grant' => 2,
						'MstAdminUser.status' => 0
					)
				);
				break;
			case 1:	//代理店
				$params = array(
					'conditions' => array(
						'MstAdminUser.acc_grant' => 2,
						'MstAdminUser.agency_id' => $this->Auth->user('id'),
						'MstAdminUser.status' => 0
					)
				);
				break;
			case 2:	//加盟店
				$this->redirect('/report/stampCompany/'.$this->Auth->user('id'));
				break;
			case 3:	//チーム
				$this->redirect('/report/stampShop/'.$this->Auth->user('id'));
				break;
		}
		$data = $this->MstAdminUser->find('all', $params);
		if ($data) {
			# CSV出力の準備
			$this->layout = false;
			$filename = 'ポイントレポート_'.date('YmdHis');
			# 表の一行目を作成
			$th = array('Id', '加盟店名', '配付ポイント合計', '交換ポイント合計');
			# 表の内容を取得
			$td = array();
			foreach ($data as $key => $res) {
				# 店舗一覧を取り出す
				$shops = $this->MstAdminUser->find('list', array(
					'conditions' => array(
						'MstAdminUser.acc_grant' => 3,
						'MstAdminUser.chain_id' => $res['MstAdminUser']['id']
					)
				));

				# 加盟店全体での配付・交換ポイントを履歴から集計
				$trades = array(
					'addition' => 0,
					'exchange' => 0
				);
				if ($shops) {
					$data2 = $this->TrnPointTrade->find('all', array(
						'fields' => array(
							'TrnPointTrade.trade_kind',
							'sum(trade_point) as nums'
						),
						'conditions' => array(
							'TrnPointTrade.shop_id' => array_keys($shops),
							'TrnPointTrade.trade_year' => $trade_year,
							'TrnPointTrade.trade_month' => $trade_month,
							'TrnPointTrade.status' => 0
						),
						'group' => array('TrnPointTrade.trade_kind')
					));
					if ($data2) {
						foreach ($data2 as $res2) {
							switch ($res2['TrnPointTrade']['trade_kind']) {
								case 1: case 2: case 3: case 4: case 5:
									$trades['addition'] += $res2[0]['nums'];
									break;
								case 50:
									$trades['exchange'] += abs($res2[0]['nums']);
									break;
							}
						}
					}
				}
				$td[] = array(
					$res['MstAdminUser']['id'],
					$res['MstAdminUser']['aname'],
					$trades['addition'],
					$trades['exchange']
				);
			}
			$this->set(compact(
				'filename',
				'th',
				'td'
			));
			$this->render('/Pages/download');
		} else {
			$this->Session->setFlash('対象となるデータが見つかりませんでした。', 'default', array('class' => 'callout callout-danger'));
			$this->redirect('/report/stamp/'.$datetime);
		}
	}


	public function stampCompany($company_id = null, $datetime = null){
		if ($company_id === null) {
			if ($this->Auth->user('acc_grant') == 2) {
				$company_id = $this->Auth->user('id');
			} else {
				$this->redirect('/');
			}
		}
		if ($datetime === null) {
			$datetime = mktime(0, 0, 0, date('m'), 1, date('Y'));
		}
		$trade_year = date('Y', $datetime);
		$trade_month = date('m', $datetime);

		# 加盟店データを取り出す
		$data = $this->MstAdminUser->find('first', array(
			'conditions' => array(
				'MstAdminUser.id' => $company_id,
				'MstAdminUser.acc_grant' => 2
			)
		));
		if ($data) {
			# 店舗一覧を取り出す
			$shops = $this->MstAdminUser->find('list', array(
				'conditions' => array(
					'MstAdminUser.acc_grant' => 3,
					'MstAdminUser.chain_id' => $data['MstAdminUser']['id']
				)
			));
			if ($shops) {
				# アイテム別の交換数を集計する
				$data['Items'] = array();
				$this->TrnPointExchange->recursive = 0;
				$this->TrnPointExchange->unbindModel(array('belongsTo' => array('TrnMembers', 'MstAdminUser')));
				$data2 = $this->TrnPointExchange->find('all', array(
					'fields' => array(
						'MstPointItem.item_id',
						'MstPointItem.item_name',
						'sum(TrnPointExchange.exchange_point) as sum_point',
						'count(*) as num1',
						'count(distinct TrnPointExchange.member_id) as num2'
					),
					'conditions' => array(
						'TrnPointExchange.shop_id' => array_keys($shops),
						'TrnPointExchange.request_datetime >=' => date('Y-m-d H:i:s', $datetime),
						'TrnPointExchange.request_datetime <=' => date('Y-m-d H:i:s', mktime(23, 59, 59, $trade_month + 1, 0, $trade_year)),
						'NOT' => array('TrnPointExchange.exchange_result' => 1)
					),
					'group' => array(
						'TrnPointExchange.item_id'
					)
				));
				if ($data2) {
					foreach ($data2 as $res2) {
						$data['Items'][] = array(
							'item_id' => $res2['MstPointItem']['item_id'],
							'item_name' => $res2['MstPointItem']['item_name'],
							'sum_point' => $res2[0]['sum_point'],
							'num1' => $res2[0]['num1'],
							'num2' => $res2[0]['num2']
						);
					}
				}

				# チーム別の配付・交換数を集計する
				$data['TrnPointTrade'] = array(
					'addition' => 0,
					'exchange' => 0
				);
				$data['Shops'] = array();
				foreach ($shops as $shop_id => $shop_name) {
					$data['Shops'] += array($shop_id => array(
						'shop_name' => $shop_name,
						'addition' => 0,
						'exchange' => 0
					));
				}
				$data2 = $this->TrnPointTrade->find('all', array(
					'fields' => array(
						'TrnPointTrade.shop_id',
						'TrnPointTrade.trade_kind',
						'sum(trade_point) as nums'
					),
					'conditions' => array(
						'TrnPointTrade.shop_id' => array_keys($shops),
						'TrnPointTrade.trade_year' => $trade_year,
						'TrnPointTrade.trade_month' => $trade_month,
						'TrnPointTrade.status' => 0
					),
					'group' => array(
						'TrnPointTrade.shop_id',
						'TrnPointTrade.trade_kind'
					)
				));
				if ($data2) {
					foreach ($data2 as $res2) {
						$shop_id = $res2['TrnPointTrade']['shop_id'];
						if (isset($data['Shops'][$shop_id])) {
							switch ($res2['TrnPointTrade']['trade_kind']) {
								case 1: case 2: case 3: case 4: case 5:
									$data['Shops'][$shop_id]['addition'] += $res2[0]['nums'];
									$data['TrnPointTrade']['addition'] += $res2[0]['nums'];
									break;
								case 50:
									$data['Shops'][$shop_id]['exchange'] += abs($res2[0]['nums']);
									$data['TrnPointTrade']['exchange'] += abs($res2[0]['nums']);
									break;
							}
						}
					}
				}
			}
		} else {
			$this->redirect('/');
		}
		$this->title = date('Y年m月', $datetime).'の加盟店別、ポイントレポート';
		$this->set(compact(
			'data',
			'company_id',
			'datetime'
		));
	}


	public function stampCompanyDownload($company_id = null, $datetime = null) {
		if ($company_id === null) {
			if ($this->Auth->user('acc_grant') == 2) {
				$company_id = $this->Auth->user('id');
			} else {
				$this->redirect('/');
			}
		}
		if ($datetime === null) {
			$datetime = mktime(0, 0, 0, date('m'), 1, date('Y'));
		}
		$trade_year = date('Y', $datetime);
		$trade_month = date('m', $datetime);

		# 加盟店データを取り出す
		$data = $this->MstAdminUser->find('first', array(
			'conditions' => array(
				'MstAdminUser.id' => $company_id,
				'MstAdminUser.acc_grant' => 2
			)
		));
		if ($data) {
			# CSV出力の準備
			$this->layout = false;
			$filename = '加盟店ポイントレポート_'.date('YmdHis');
			# 表の一行目を作成
			$th = array('Id', 'チーム名', '配付ポイント合計', '交換ポイント合計');
			# 表の内容を取得
			$td = array();

			# 店舗一覧を取り出す
			$shops = $this->MstAdminUser->find('list', array(
				'conditions' => array(
					'MstAdminUser.acc_grant' => 3,
					'MstAdminUser.chain_id' => $data['MstAdminUser']['id']
				)
			));
			if ($shops) {
				# チーム別の配付・交換数を集計する
				$data['Shops'] = array();
				foreach ($shops as $shop_id => $shop_name) {
					$data['Shops'] += array($shop_id => array(
						'shop_name' => $shop_name,
						'addition' => 0,
						'exchange' => 0
					));
				}
				$data2 = $this->TrnPointTrade->find('all', array(
					'fields' => array(
						'TrnPointTrade.shop_id',
						'TrnPointTrade.trade_kind',
						'sum(trade_point) as nums'
					),
					'conditions' => array(
						'TrnPointTrade.shop_id' => array_keys($shops),
						'TrnPointTrade.trade_year' => $trade_year,
						'TrnPointTrade.trade_month' => $trade_month,
						'TrnPointTrade.status' => 0
					),
					'group' => array(
						'TrnPointTrade.shop_id',
						'TrnPointTrade.trade_kind'
					)
				));
				if ($data2) {
					foreach ($data2 as $res2) {
						$shop_id = $res2['TrnPointTrade']['shop_id'];
						if (isset($data['Shops'][$shop_id])) {
							switch ($res2['TrnPointTrade']['trade_kind']) {
								case 1: case 2: case 3: case 4: case 5:
									$data['Shops'][$shop_id]['addition'] += $res2[0]['nums'];
									break;
								case 50:
									$data['Shops'][$shop_id]['exchange'] += abs($res2[0]['nums']);
									break;
							}
						}
					}
				}
				foreach ($data['Shops'] as $shop_id => $res) {
					$td[] = array(
						$shop_id,
						$res['shop_name'],
						$res['addition'],
						$res['exchange']
					);
				}
			}
			$this->set(compact(
				'filename',
				'th',
				'td'
			));
			$this->render('/Pages/download');
		} else {
			$this->Session->setFlash('対象となるデータが見つかりませんでした。', 'default', array('class' => 'callout callout-danger'));
			$this->redirect('/report/stampCompany/'.$company_id.'/'.$datetime);
		}
	}


	public function stampShop($shop_id = null, $datetime = null){
		if ($shop_id === null) {
			if ($this->Auth->user('acc_grant') == 3) {
				$shop_id = $this->Auth->user('id');
			} else {
				$this->redirect('/');
			}
		}
		if ($datetime === null) {
			$datetime = mktime(0, 0, 0, date('m'), 1, date('Y'));
		}
		$trade_year = date('Y', $datetime);
		$trade_month = date('m', $datetime);

		# チームデータを取り出す
		$data = $this->MstAdminUser->find('first', array(
			'conditions' => array(
				'MstAdminUser.id' => $shop_id,
				'MstAdminUser.acc_grant' => 3
			)
		));
		if ($data) {
			# アイテム別の交換数を集計する
			$data['Items'] = array();
			$this->TrnPointExchange->recursive = 0;
			$this->TrnPointExchange->unbindModel(array('belongsTo' => array('TrnMembers', 'MstAdminUser')));
			$data2 = $this->TrnPointExchange->find('all', array(
				'fields' => array(
					'MstPointItem.item_id',
					'MstPointItem.item_name',
					'sum(TrnPointExchange.exchange_point) as sum_point',
					'count(*) as num1',
					'count(distinct TrnPointExchange.member_id) as num2'
				),
				'conditions' => array(
					'TrnPointExchange.shop_id' => $shop_id,
					'TrnPointExchange.request_datetime >=' => date('Y-m-d H:i:s', $datetime),
					'TrnPointExchange.request_datetime <=' => date('Y-m-d H:i:s', mktime(23, 59, 59, $trade_month + 1, 0, $trade_year)),
					'NOT' => array('TrnPointExchange.exchange_result' => 1)
				),
				'group' => array(
					'TrnPointExchange.item_id'
				)
			));
			if ($data2) {
				foreach ($data2 as $res2) {
					$data['Items'][] = array(
						'item_id' => $res2['MstPointItem']['item_id'],
						'item_name' => $res2['MstPointItem']['item_name'],
						'sum_point' => $res2[0]['sum_point'],
						'num1' => $res2[0]['num1'],
						'num2' => $res2[0]['num2']
					);
				}
			}

			# メンバー別の配付・交換数を集計する
			$data['TrnPointTrade'] = array(
				'addition' => 0,
				'exchange' => 0
			);
			$members = array();
			$this->TrnTeams->recursive = 0;
			$this->TrnTeams->unbindModel(array('belongsTo' => array('MstAdminUser')));
			$data2 = $this->TrnTeams->find('all', array(
				'conditions' => array(
					'TrnTeams.shop_id' => $shop_id,
					'TrnTeams.del_flg' => 0,
					'TrnMembers.status' => 2
				)
			));
			if ($data2) {
				foreach ($data2 as $res) {
					$nowpoint = $this->TrnPointTrade->find('first', array(
						'fields' => array('sum(TrnPointTrade.trade_point) as nums'),
						'conditions' => array(
							'TrnPointTrade.member_id' => $res['TrnMembers']['member_id'],
							'TrnPointTrade.status' => 0
						)
					));
					$members += array($res['TrnMembers']['member_id'] => array(
						'member_name' => $res['TrnMembers']['member_name'],
						'addition' => 0,
						'exchange' => 0,
						'nowpoint' => !empty($nowpoint[0]['nums']) ? $nowpoint[0]['nums'] : 0
					));
				}
			}
			$data2 = $this->TrnPointTrade->find('all', array(
				'fields' => array(
					'TrnPointTrade.member_id',
					'TrnPointTrade.trade_kind',
					'sum(trade_point) as nums'
				),
				'conditions' => array(
					'TrnPointTrade.shop_id' => $shop_id,
					'TrnPointTrade.trade_year' => $trade_year,
					'TrnPointTrade.trade_month' => $trade_month,
					'TrnPointTrade.status' => 0
				),
				'group' => array(
					'TrnPointTrade.member_id',
					'TrnPointTrade.trade_kind'
				)
			));
			if ($data2) {
				foreach ($data2 as $res2) {
					$member_id = $res2['TrnPointTrade']['member_id'];
					if (isset($members[$member_id])) {
						switch ($res2['TrnPointTrade']['trade_kind']) {
							case 1: case 2: case 3: case 4: case 5:
								$members[$member_id]['addition'] += $res2[0]['nums'];
								$data['TrnPointTrade']['addition'] += $res2[0]['nums'];
								break;
							case 50:
								$members[$member_id]['exchange'] += abs($res2[0]['nums']);
								$data['TrnPointTrade']['exchange'] += abs($res2[0]['nums']);
						}
					}
				}
			}
			$data['Members'] = $members;

			$this->set(compact(
				'data',
				'shop_id',
				'datetime'
			));
		} else {
			$this->redirect('/');
		}
	}


	public function stampShopDownload($shop_id = null, $datetime = null){
		if ($shop_id === null) {
			if ($this->Auth->user('acc_grant') == 3) {
				$shop_id = $this->Auth->user('id');
			} else {
				$this->redirect('/');
			}
		}
		if ($datetime === null) {
			$datetime = mktime(0, 0, 0, date('m'), 1, date('Y'));
		}
		$trade_year = date('Y', $datetime);
		$trade_month = date('m', $datetime);

		# チームデータを取り出す
		$data = $this->MstAdminUser->find('first', array(
			'conditions' => array(
				'MstAdminUser.id' => $shop_id,
				'MstAdminUser.acc_grant' => 3
			)
		));
		if ($data) {
			# CSV出力の準備
			$this->layout = false;
			$filename = '店舗ポイントレポート_'.date('YmdHis');
			# 表の一行目を作成
			$th = array('Id', 'チーム名', '配付ポイント合計', '交換ポイント合計');
			# 表の内容を取得
			$td = array();

			# メンバー別の配付・交換数を集計する
			$members = array();
			$this->TrnTeams->recursive = 0;
			$this->TrnTeams->unbindModel(array('belongsTo' => array('MstAdminUser')));
			$data2 = $this->TrnTeams->find('all', array(
				'conditions' => array(
					'TrnTeams.shop_id' => $shop_id,
					'TrnTeams.del_flg' => 0,
					'TrnMembers.status' => 2
				)
			));
			if ($data2) {
				foreach ($data2 as $res) {
					$nowpoint = $this->TrnPointTrade->find('first', array(
						'fields' => array('sum(TrnPointTrade.trade_point) as nums'),
						'conditions' => array(
							'TrnPointTrade.member_id' => $res['TrnMembers']['member_id'],
							'TrnPointTrade.status' => 0
						)
					));
					$members += array($res['TrnMembers']['member_id'] => array(
						'member_name' => $res['TrnMembers']['member_name'],
						'addition' => 0,
						'exchange' => 0,
						'nowpoint' => !empty($nowpoint[0]['nums']) ? $nowpoint[0]['nums'] : 0
					));
				}
			}
			$data2 = $this->TrnPointTrade->find('all', array(
				'fields' => array(
					'TrnPointTrade.member_id',
					'TrnPointTrade.trade_kind',
					'sum(trade_point) as nums'
				),
				'conditions' => array(
					'TrnPointTrade.shop_id' => $shop_id,
					'TrnPointTrade.trade_year' => $trade_year,
					'TrnPointTrade.trade_month' => $trade_month,
					'TrnPointTrade.status' => 0
				),
				'group' => array(
					'TrnPointTrade.member_id',
					'TrnPointTrade.trade_kind'
				)
			));
			if ($data2) {
				foreach ($data2 as $res2) {
					$member_id = $res2['TrnPointTrade']['member_id'];
					if (isset($members[$member_id])) {
						switch ($res2['TrnPointTrade']['trade_kind']) {
							case 1: case 2: case 3: case 4: case 5:
								$members[$member_id]['addition'] += $res2[0]['nums'];
								break;
							case 50:
								$members[$member_id]['exchange'] += abs($res2[0]['nums']);
						}
					}
				}
			}
			foreach ($members as $member_id => $res) {
				$td[] = array(
					$member_id,
					$res['member_name'],
					$res['addition'],
					$res['exchange'],
					$res['nowpoint']
				);
			}

			$this->set(compact(
				'filename',
				'th',
				'td'
			));
			$this->render('/Pages/download');
		} else {
			$this->Session->setFlash('対象となるデータが見つかりませんでした。', 'default', array('class' => 'callout callout-danger'));
			$this->redirect('/');
		}
	}
}
