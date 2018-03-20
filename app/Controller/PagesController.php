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
class PagesController extends AppController {

/**
 * This controller does not use a model
 *
 * @var array
 */
	public $uses = array();

	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('login', 'logout');
//		$this->Auth->allow();
	}

	public function beforeRender() {
		parent::beforeRender();
	}

	public function home($from = null, $to = null) {
		$this->title = 'HOME';

		# レポート用のデータ収集
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
			0 => array(),	# サンクス数、推移
			1 => array(),	# サンクス割合のトップ10
			2 => array()	# サンクス割合のワースト10
		);
		$shops = array();
		$shop_ids = array();

		$chains = $this->MstAdminUser->find('list', array(
			'conditions' => array(
				'MstAdminUser.acc_grant' => 2
			)
		));

		$limit = 10;	# 表示するトップ／ワースト順位

		$this->TrnTeams->recursive = 0;
		$this->TrnTeams->unbindModel(array('belongsTo' => array('MstAdminUser')), false);

		switch ($this->Auth->user('acc_grant')) {
			case 0: //元売
				# 店舗一覧の取り出し
				$data2 = $this->MstAdminUser->find('all', array(
					'fields' => array(
						'MstAdminUser.id',
						'MstAdminUser.aname',
						'MstAdminUser.chain_id'
					),
					'conditions' => array(
						'MstAdminUser.acc_grant' => 3,
						'MstAdminUser.status' => 0
					)
				));
				if ($data2) {
					foreach ($data2 as $res) {
						$shops += array($res['MstAdminUser']['id'] => array(
							'aname' => $res['MstAdminUser']['aname'],
							'chain_id' => $res['MstAdminUser']['chain_id'],
							'memberNum' => $this->TrnTeams->find('count', array(
								'conditions' => array(
									'TrnTeams.shop_id' => $res['MstAdminUser']['id'],
									'TrnTeams.del_flg' => 0,
									'TrnMembers.status' => 2
								)
							)),
							'thanksNum' => 0
						));
						$shop_ids[] = $res['MstAdminUser']['id'];
					}
				}

				# 集計条件
				$conditions = array(
					'TrnThanksCnt.shop_id' => $shop_ids,
					'UNIX_TIMESTAMP(CONCAT(years,"-",months,"-",days)) >=' => $from,
					'UNIX_TIMESTAMP(CONCAT(years,"-",months,"-",days)) <=' => $to
				);
				$renderType = 'homeType1';
				break;
			case 1:	//代理店
				# 店舗一覧の取り出し
				$data2 = $this->MstAdminUser->find('all', array(
					'fields' => array(
						'MstAdminUser.id',
						'MstAdminUser.aname',
						'MstAdminUser.chain_id'
					),
					'conditions' => array(
						'MstAdminUser.agency_id' => $this->Auth->user('id'),
						'MstAdminUser.acc_grant' => 3,
						'MstAdminUser.status' => 0
					)
				));
				if ($data2) {
					foreach ($data2 as $res) {
						$shops += array($res['MstAdminUser']['id'] => array(
							'aname' => $res['MstAdminUser']['aname'],
							'chain_id' => $res['MstAdminUser']['chain_id'],
							'memberNum' => $this->TrnTeams->find('count', array(
								'conditions' => array(
									'TrnTeams.shop_id' => $res['MstAdminUser']['id'],
									'TrnTeams.del_flg' => 0,
									'TrnMembers.status' => 2
								)
							)),
							'thanksNum' => 0
						));
						$shop_ids[] = $res['MstAdminUser']['id'];
					}
				}

				# 集計条件
				$conditions = array(
					'TrnThanksCnt.shop_id' => $shop_ids,
					'UNIX_TIMESTAMP(CONCAT(years,"-",months,"-",days)) >=' => $from,
					'UNIX_TIMESTAMP(CONCAT(years,"-",months,"-",days)) <=' => $to
				);
				$renderType = 'homeType1';
				break;
			case 2:	//加盟店
				# 店舗一覧の取り出し
				$data2 = $this->MstAdminUser->find('all', array(
					'fields' => array(
						'MstAdminUser.id',
						'MstAdminUser.aname'
					),
					'conditions' => array(
						'MstAdminUser.chain_id' => $this->Auth->user('id'),
						'MstAdminUser.acc_grant' => 3,
						'MstAdminUser.status' => 0
					)
				));
				if ($data2) {
					foreach ($data2 as $res) {
						$shops += array($res['MstAdminUser']['id'] => array(
							'aname' => $res['MstAdminUser']['aname'],
							'memberNum' => $this->TrnTeams->find('count', array(
								'conditions' => array(
									'TrnTeams.shop_id' => $res['MstAdminUser']['id'],
									'TrnTeams.del_flg' => 0,
									'TrnMembers.status' => 2
								)
							)),
							'thanksNum' => 0
						));
						$shop_ids[] = $res['MstAdminUser']['id'];
					}
				}

				# 集計条件
				$conditions = array(
					'TrnThanksCnt.shop_id' => $shop_ids,
					'UNIX_TIMESTAMP(CONCAT(years,"-",months,"-",days)) >=' => $from,
					'UNIX_TIMESTAMP(CONCAT(years,"-",months,"-",days)) <=' => $to
				);
				$renderType = 'homeType2';
				$limit = 5;
				break;
			case 3:	//店舗
				$shop_ids[] = $this->Auth->user('id');

				# 集計条件
				$conditions = array(
					'TrnThanksCnt.shop_id' => $shop_ids,
					'UNIX_TIMESTAMP(CONCAT(years,"-",months,"-",days)) >=' => $from,
					'UNIX_TIMESTAMP(CONCAT(years,"-",months,"-",days)) <=' => $to
				);
				$renderType = 'homeType3';
				$limit = 5;
				break;
		}

		## 日別の店舗サンクス数を呼び出します
		# 先にボックスを用意
		$x = $from;
		for (;;) {
			$data[0] += array($x => 0);
			$x = strtotime('+1 day', $x);
			if ($x > $to) {
				break;
			}
		}
		$data2 = $this->TrnThanksCnt->find('all', array(
			'fields' => array(
				'TrnThanksCnt.years',
				'TrnThanksCnt.months',
				'TrnThanksCnt.days',
				'sum(TrnThanksCnt.thanks_receives) as nums'
			),
			'conditions' => $conditions,
			'group' => array(
				'TrnThanksCnt.years',
				'TrnThanksCnt.months',
				'TrnThanksCnt.days'
			)
		));
		if ($data2) {
			foreach ($data2 as $res) {
				$key = mktime(0, 0, 0, $res['TrnThanksCnt']['months'], $res['TrnThanksCnt']['days'], $res['TrnThanksCnt']['years']);
				$data[0][$key] = $res[0]['nums'];
			}
		}

		if ($renderType == 'homeType3') {
			# ユーザ別のサンクス数を呼び出します
			$members = array();
			$data2 = $this->TrnTeams->find('all', array(
				'conditions' => array(
					'TrnTeams.shop_id' => $shop_ids,
					'TrnTeams.del_flg' => 0,
					'TrnMembers.status' => 2
				)
			));
			if ($data2) {
				foreach ($data2 as $res) {
					$members += array($res['TrnTeams']['member_id'] => array(
						'name' => $res['TrnMembers']['member_name'],
						'thanks_receives' => 0,
						'thanks_sends' => 0
					));
				}
			}

			$data2 = array();
			$data3 = $this->TrnThanksCnt->find('all', array(
				'fields' => array(
					'TrnThanksCnt.menber_id',
					'sum(TrnThanksCnt.thanks_receives) as num1',
					'sum(TrnThanksCnt.thanks_sends) as num2'
				),
				'conditions' => $conditions,
				'group' => array('TrnThanksCnt.menber_id')
			));
			if ($data3) {
				foreach ($data3 as $res) {
					$id = $res['TrnThanksCnt']['menber_id'];
					if (isset($members[$id])) {
						$data2 += array($id => $res[0]['num1'] + $res[0]['num2']);
						$members[$id]['thanks_receives'] = $res[0]['num1'];
						$members[$id]['thanks_sends'] = $res[0]['num2'];
					}
				}
			}

			$memberDetails = $this->TrnThanks->adjustmentData($members);

			# トップ10を取り出す
			$tmp = $data2;
			arsort($tmp);
			$n = 0;
			foreach ($tmp as $id => $num) {
				$n++;
				$data[1] += array($id => $members[$id]);
				if ($n >= $limit) break;
			}

			# ワースト10を取り出す
			$tmp = $data2;
			asort($tmp);
			$n = 0;
			foreach ($tmp as $id => $num) {
				$n++;
				$data[2] += array($id => $members[$id]);
				if ($n >= $limit) break;
			}
		} else {
			# 店舗別のサンクス数を呼び出します
			$data2 = $this->TrnThanksCnt->find('all', array(
				'fields' => array(
					'TrnThanksCnt.shop_id',
					'sum(TrnThanksCnt.thanks_receives) as nums'
				),
				'conditions' => $conditions,
				'group' => array('TrnThanksCnt.shop_id')
			));
			if ($data2) {
				foreach ($data2 as $res) {
					$id = $res['TrnThanksCnt']['shop_id'];
					if (isset($shops[$id])) {
						$shops[$id]['thanksNum'] = $res[0]['nums'];
					}
				}
			}
			$data2 = array();
			foreach ($shops as $shop_id => $res) {
				if ($res['memberNum'] >= 2) {
					$data2 += array($shop_id => $res['thanksNum'] / $res['memberNum']);
				}
			}

			# トップ10を取り出す
			$tmp = $data2;
			arsort($tmp);
			$n = 0;
			foreach ($tmp as $shop_id => $num) {
				$n++;
				$data[1] += array($shop_id => array(
					'chain_id' => isset($shops[$shop_id]['chain_id']) ? $shops[$shop_id]['chain_id'] : null,
					'aname' => $shops[$shop_id]['aname'],
					'memberNum' => $shops[$shop_id]['memberNum'],
					'thanksNum' => $shops[$shop_id]['thanksNum'],
					'num' => $num,
					'label' => $this->TrnThanks->label4num($num)
				));
				if ($n >= $limit) break;
			}

			# ワースト10を取り出す
			$tmp = $data2;
			asort($tmp);
			$n = 0;
			foreach ($tmp as $shop_id => $num) {
				$n++;
				$data[2] += array($shop_id => array(
					'chain_id' => isset($shops[$shop_id]['chain_id']) ? $shops[$shop_id]['chain_id'] : null,
					'aname' => $shops[$shop_id]['aname'],
					'memberNum' => $shops[$shop_id]['memberNum'],
					'thanksNum' => $shops[$shop_id]['thanksNum'],
					'num' => $num,
					'label' => $this->TrnThanks->label4num($num)
				));
				if ($n >= $limit) break;
			}
		}

		$this->set(compact(
			'data',
			'from',
			'to',
			'chains',
			'memberDetails'
		));
		$this->render($renderType);
	}

	public function login() {
		if ($this->Auth->user()) {
			$this->redirect(array('action' => 'home'));
		}
		if ($this->request->is('post')) {
			if ($this->Auth->login()) {
				$this->redirect(array('action' => 'home'));
			} else {
				$this->Session->setFlash('メールアドレスかパスワードが間違っています。', 'default', array('class' => 'callout callout-danger'));
			}
		}
		$this->layout = 'login';
	}

	public function logout() {
		$this->redirect($this->Auth->logout());
	}
}
