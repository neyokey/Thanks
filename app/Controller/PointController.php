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
class PointController extends AppController {

	var $helpers = array('Html', 'Form', 'Csv');

	public $components = array(
		'Paginator',
		'Push'
	);

	public function beforeFilter() {
		parent::beforeFilter();
//		$this->Auth->allow();
		if ($this->Auth->user('acc_grant') != 0) {
			$this->redirect('/');
		}
	}


	public function beforeRender() {
		parent::beforeRender();
	}


	public function index($item_id = 1) {
		$conditions = array(
			'TrnPointExchange.item_id' => $item_id
		);
		
		if (isset($this->request->data['TrnPointExchange'])) {
			$this->Session->write('PointSearch.'.$item_id, $this->request->data['TrnPointExchange']);
		} elseif ($this->Session->check('PointSearch.'.$item_id)) {
			$this->request->data['TrnPointExchange'] = $this->Session->read('PointSearch.'.$item_id);
		}
		if (isset($this->request->data['TrnPointExchange'])) {
			$vl = $this->request->data['TrnPointExchange'];
			if ($vl['status'] !== '') {
				$conditions['TrnPointExchange.status'] = $vl['status'];
			}
			if ($vl['member_id'] != '') {
				$conditions['TrnMembers.member_id'] = $vl['member_id'];
			}
			if ($vl['member_name'] != '') {
				$conditions['TrnMembers.member_name LIKE'] = '%'.$vl['member_name'].'%';
			}
			if ($vl['email'] != '') {
				$conditions['TrnMembers.email LIKE'] = '%'.$vl['email'].'%';
			}
			if (isset($vl['request_datetime']) && $vl['request_datetime'] != '') {
				$ex = strtotime($vl['request_datetime']);
				$conditions['TrnPointExchange.request_datetime >='] = date('Y-m-d 00:00:00', $ex);
				$conditions['TrnPointExchange.request_datetime <='] = date('Y-m-d 23:59:59', $ex);
			}
			if (isset($vl['csv_output_date']) && $vl['csv_output_date'] != '') {
				$ex = strtotime($vl['csv_output_date']);
				$conditions['TrnPointExchange.csv_output_date >='] = date('Y-m-d 00:00:00', $ex);
				$conditions['TrnPointExchange.csv_output_date <='] = date('Y-m-d 23:59:59', $ex);
			}
		}
		$this->TrnPointExchange->recursive = 0;
		$this->TrnPointExchange->unbindModel(array('belongsTo' => array('MstPointItem', 'MstAdminUser')));
		$this->paginate = array(
			'TrnPointExchange' => array(
				'conditions' => $conditions
			)
		);
		$this->set('data', $this->Paginator->paginate('TrnPointExchange'));
		$this->set('status', $this->TrnPointExchange->readStatus());
		switch ($item_id) {
			case 1:
				# 残りアマゾンギフト券の枚数をカウント
				$this->set('stocks', $this->MstGift->find('count', array(
					'conditions' => array(
						'MstGift.git_kind' => 1,
						'MstGift.trade_id' => null,
						'MstGift.status' => 0
					)
				)));

				$this->title = 'Amazonギフト券：一覧';
				$this->render('amazon_index');
				break;
			case 2:
				$this->title = 'T-ポイント：一覧';
				$this->render('tpoint_index');
				break;
			case 3:
				$this->title = 'スターバックスギフトカード：一覧';
				$this->render('starbucks_index');
				break;
		}
	}


	public function amazonCharge() {
		if ($this->request->is(array('post', 'put'))) {
			$records = array();
			$err = array();
			$i = 0;
			$file = new SplFileObject($this->request->data['MstThanksStamp']['csv_file']['tmp_name']); 
			$file->setFlags(SplFileObject::READ_CSV); 
			foreach ($file as $line) {
				$i++;
				if ($i == 1) continue;
				if (count($line) != 5) continue;

				$num = $this->MstGift->find('count', array(
					'conditions' => array(
						'MstGift.git_kind' => 1,
						'MstGift.gift_code' => $line[1]
					)
				));
				if ($num == 0) {
					$records[] = $line;
				} else {
					$err[] = '・'.$i.'行目のギフトコードはすでに取込済みです【'.$line[1].'】';
				}
			}
			if (count($records) > 0 && empty($err)) {
				$sqldate = date('Y-m-d H:i:s');
				foreach ($records as $line) {
					$this->MstGift->create();
					$this->MstGift->save(array(
						'git_kind' => 1,
						'gift_name' => 'Amazonギフト券',
						'gift_code' => $line[1],
						'gift_amount' => $line[2],
						'expiration_date' => $line[3],
						'serial_number' => $line[4],
						'status' => 0,
						'insert_time' => $sqldate,
						'update_time' => $sqldate
					));
				}
				$this->Session->setFlash('Amazonギフト券の登録が完了しました。', 'default', array('class' => 'callout callout-info'));
				$this->redirect('/point/index/1');
			} else {
				$this->Session->setFlash('Amazonギフト券の登録に失敗しました。<br />'.implode('<br />', $err), 'default', array('class' => 'callout callout-danger'));
			}
		}
		$this->title = 'Amazonギフト券：補充';
	}


	public function amazonPush() {
		if ($this->request->is(array('post', 'put'))) {
			$sqldate = date('Y-m-d H:i:s');
			$sumNum = 0;
			$data = array();
			foreach ($this->request->data['TrnPointExchange']['exchange_id'] as $exchange_id => $val) {
				if ($val == '1') {
					# 対象の交換申請明細を取り出す
					$this->TrnPointExchange->recursive = 0;
					$this->TrnPointExchange->unbindModel(array('belongsTo' => array('MstPointItem', 'MstAdminUser')));
					$bar = $this->TrnPointExchange->find('first', array(
						'conditions' => array(
							'TrnPointExchange.exchange_id' => $exchange_id,
							'TrnPointExchange.status' => 0
						)
					));
					if ($bar) {
						# 必要なギフト券の枚数を確認
						$sumNum += ($num = ($bar['TrnPointExchange']['exchange_point'] / 5000));
						$bar['TrnPointExchange']['exchange_num'] = $num;
						$data[] = $bar;
					}
				}
			}
			if ($sumNum > 0) {
				# 必要なだけギフト券を取り出す、足りない場合はエラー
				$gifts = $this->MstGift->find('all', array(
					'conditions' => array(
						'MstGift.git_kind' => 1,
						'MstGift.trade_id' => null,
						'MstGift.status' => 0
					),
					'order' => array('MstGift.expiration_date' => 'asc'),
					'limit' => $sumNum
				));
				if (count($gifts) == $sumNum) {
					$x = 0;
					foreach ($data as $res) {
						$push_status = 0;
						$mail_status = 0;
						$data2 = array();

						# 必要枚数分、ギフト券を処理する
						for ($n = 1; $n <= $res['TrnPointExchange']['exchange_num']; $n++) {
							$data2[] = $gifts[$x];

							# ギフト券を使用済みに更新
							$this->MstGift->save(array(
								'gift_id' => $gifts[$x]['MstGift']['gift_id'],
								'trade_id' => $res['TrnPointExchange']['trade_id'],
								'status' => 1,
								'update_time' => $sqldate
							));
							$x++;
						}
						$txtVal = $this->MstGift->setAmazonVal($data2);

						# Push通知
						$message = '【thanks!】Amazonギフト券、発行完了の連絡';
						if ($this->Push->send_push($message, $res['TrnMembers']['device_id'], $res['TrnMembers']['device_type'])) {
							$push_status = 1;
						}

						# メールを送信
						$viewVars = array(
							'member_name' => $res['TrnMembers']['member_name'],
							'gift_data' => $txtVal['mail']
						);
						$obj = new CakeEmail('default');
						$mailRespons = $obj->template('point/amazon_success')
							->viewVars($viewVars)
							->emailFormat('text')
							->to($res['TrnMembers']['email'])
							->subject('【thanks!】Amazonギフト券、発行完了の連絡')
							->send();
						if ($mailRespons) {
							$mail_status = 1;
						}

						# 交換申請のステータスを「2.完了」に進める
						$this->TrnPointExchange->save(array(
							'exchange_id' => $res['TrnPointExchange']['exchange_id'],
							'exchange_execute' => $sqldate,
							'exchange_result' => 0,
							'exchange_resultdetail' => $txtVal['text'],
							'status' => 2,
							'push_status' => $push_status,
							'mail_status' => $mail_status
						));
					}
					$this->Session->setFlash('チェックした申請の処理が完了しました。', 'default', array('class' => 'callout callout-info'));
				} else {
					$this->Session->setFlash('Amazonギフト券が不足しております。', 'default', array('class' => 'callout callout-danger'));
				}
			} else {
				$this->Session->setFlash('処理対象となる申請をチェックしてください。', 'default', array('class' => 'callout callout-danger'));
			}
		}
		$this->redirect('/point/index/1');
	}


	public function amazonView($exchange_id = null) {
		$this->TrnPointExchange->recursive = 0;
		$data = $this->TrnPointExchange->find('first', array(
			'conditions' => array(
				'TrnPointExchange.exchange_id' => $exchange_id,
				'TrnPointExchange.item_id' => 1
			)
		));
		if ($data) {
			$this->set('data', $data);
		} else {
			$this->Session->setFlash('データの呼び出しに失敗しました。', 'default', array('class' => 'callout callout-danger'));
			$this->redirect('/point/index/1');
		}
		$this->title = 'Amazonギフト券：詳細';
	}


	public function amazonSuccess($exchange_id = null) {
		$this->TrnPointExchange->recursive = 0;
		$data = $this->TrnPointExchange->find('first', array(
			'conditions' => array(
				'TrnPointExchange.exchange_id' => $exchange_id,
				'TrnPointExchange.item_id' => 1
			)
		));
		if ($data) {
			$info_flg = $this->request->data['TrnPointExchange']['info_flg'] == 1 ? TRUE : FALSE;
			$sqldate = date('Y-m-d H:i:s');

			$push_status = $data['TrnPointExchange']['push_status'];
			$mail_status = $data['TrnPointExchange']['mail_status'];

			$x = $data['TrnPointExchange']['exchange_point'] / 5000;

			# ギフト券を呼び出す（必要数なかった場合はエラー）
			$gifts = $this->MstGift->find('all', array(
				'conditions' => array(
					'MstGift.git_kind' => 1,
					'MstGift.status' => 0
				),
				'order' => array('MstGift.expiration_date' => 'asc'),
				'limit' => $x
			));
			if (count($gifts) == $x) {
				foreach ($gifts as $res) {
					# ギフト券を使用済みに更新
					$this->MstGift->save(array(
						'gift_id' => $res['MstGift']['gift_id'],
						'trade_id' => $data['TrnPointExchange']['trade_id'],
						'status' => 1,
						'update_time' => $sqldate
					));
				}
				$txtVal = $this->MstGift->setAmazonVal($gifts);

				if ($info_flg === TRUE) {
					# Push通知
					$message = '【thanks!】Amazonギフト券、交換完了の連絡';
					if ($this->Push->send_push($message, $data['TrnMembers']['device_id'], $data['TrnMembers']['device_type'])) {
						$push_status = 1;
					}

					# メールを送信
					$viewVars = array(
						'member_name' => $data['TrnMembers']['member_name'],
						'gift_data' => $txtVal['mail']
					);
					$obj = new CakeEmail('default');
					$mailRespons = $obj->template('point/amazon_success')
						->viewVars($viewVars)
						->emailFormat('text')
						->to($data['TrnMembers']['email'])
						->subject('【thanks!】Amazonギフト券、交換完了の連絡')
						->send();
					if ($mailRespons) {
						$mail_status = 1;
					}
				}

				# ポイント交換申請を更新する
				$this->TrnPointExchange->save(array(
					'exchange_id' => $exchange_id,
					'exchange_execute' => $sqldate,
					'exchange_result' => 0,
					'exchange_resultdetail' => $txtVal['text'],
					'status' => 2,
					'push_status' => $push_status,
					'mail_status' => $mail_status
				));

				# ポイント取引履歴がキャンセルされていた場合、復活させる
				$bar = $this->TrnPointTrade->find('first', array(
					'TrnPointTrade.trade_id' => $data['TrnPointExchange']['trade_id'],
					'TrnPointTrade.status' => 1
				));
				if ($bar) {
					$this->TrnPointTrade->save(array(
						'trade_id' => $data['TrnPointExchange']['trade_id'],
						'status' => 0
					));
				}

				$this->Session->setFlash('データの更新が完了しました。', 'default', array('class' => 'callout callout-info'));
			} else {
				$this->Session->setFlash('Amazonギフト券が不足しております。', 'default', array('class' => 'callout callout-danger'));
			}
		} else {
			$this->Session->setFlash('データの呼び出しに失敗しました。', 'default', array('class' => 'callout callout-danger'));
		}
		$this->redirect('/point/amazonView/'.$exchange_id);
	}


	public function amazonFailed($exchange_id = null) {
		$this->TrnPointExchange->recursive = 0;
		$data = $this->TrnPointExchange->find('first', array(
			'conditions' => array(
				'TrnPointExchange.exchange_id' => $exchange_id,
				'TrnPointExchange.item_id' => 1
			)
		));
		if ($data) {
			$info_flg = $this->request->data['TrnPointExchange']['info_flg'] == 1 ? TRUE : FALSE;
			$used_flg = $this->request->data['TrnPointExchange']['used_flg'] == 1 ? TRUE : FALSE;
			$exchange_resultdetail = $this->request->data['TrnPointExchange']['exchange_resultdetail'];
			$sqldate = date('Y-m-d H:i:s');

			$push_status = $data['TrnPointExchange']['push_status'];
			$mail_status = $data['TrnPointExchange']['mail_status'];

			if ($info_flg === TRUE) {
				# Push通知
				$message = '【thanks!】Amazonギフト券、交換失敗の連絡';
				if ($this->Push->send_push($message, $data['TrnMembers']['device_id'], $data['TrnMembers']['device_type'])) {
					$push_status = 1;
				}

				# メールを送信
				$viewVars = array(
					'member_name' => $data['TrnMembers']['member_name'],
					'failed_value' => $exchange_resultdetail
				);
				$obj = new CakeEmail('default');
				$mailRespons = $obj->template('point/amazon_failed')
					->viewVars($viewVars)
					->emailFormat('text')
					->to($data['TrnMembers']['email'])
					->subject('【thanks!】Amazonギフト券、交換失敗の連絡')
					->send();
				if ($mailRespons) {
					$mail_status = 1;
				}
			}

			# ポイント交換申請を更新する
			$this->TrnPointExchange->save(array(
				'exchange_id' => $exchange_id,
				'exchange_execute' => $sqldate,
				'exchange_result' => 1,
				'exchange_resultdetail' => $exchange_resultdetail,
				'status' => 2,
				'push_status' => $push_status,
				'mail_status' => $mail_status
			));

			# ポイント取引履歴をキャンセルする
			$bar = $this->TrnPointTrade->find('first', array(
				'TrnPointTrade.trade_id' => $data['TrnPointExchange']['trade_id'],
				'TrnPointTrade.status' => 0
			));
			if ($bar) {
				$this->TrnPointTrade->save(array(
					'trade_id' => $data['TrnPointExchange']['trade_id'],
					'status' => 1
				));
			}

			# ギフト券が割り振られていた場合、解除する
			$gifts = $this->MstGift->find('all', array(
				'conditions' => array(
					'MstGift.trade_id' => $data['TrnPointExchange']['trade_id'],
					'MstGift.status' => 1
				)
			));
			if ($gifts) {
				foreach ($gifts as $res) {
					if ($used_flg === TRUE) {
						# このギフト券が他で利用されないよう、「失効」に更新する
						$this->MstGift->save(array(
							'gift_id' => $res['MstGift']['gift_id'],
							'trade_id' => null,
							'status' => 2,
							'update_time' => $sqldate
						));
					} else {
						# このギフト券が他で利用できるよう、「利用可能」に更新する
						$this->MstGift->save(array(
							'gift_id' => $res['MstGift']['gift_id'],
							'trade_id' => null,
							'status' => 0,
							'update_time' => $sqldate
						));
					}
				}
			}

			$this->Session->setFlash('データの更新が完了しました。', 'default', array('class' => 'callout callout-info'));
		} else {
			$this->Session->setFlash('データの呼び出しに失敗しました。', 'default', array('class' => 'callout callout-danger'));
		}
		$this->redirect('/point/amazonView/'.$exchange_id);
	}


	public function tpointDownload($exchange_id = 0, $csv_output_date = null) {
		if ($exchange_id != 0) {
			$data = $this->TrnPointExchange->find('all', array(
				'conditions' => array(
					'TrnPointExchange.exchange_id' => $exchange_id,
					'TrnPointExchange.item_id' => 2
				)
			));
		} else {
			if ($csv_output_date === null) {
				$data = $this->TrnPointExchange->find('all', array(
					'conditions' => array(
						'TrnPointExchange.item_id' => 2,
						'TrnPointExchange.status' => 0
					)
				));
			} else {
				$data = $this->TrnPointExchange->find('all', array(
					'conditions' => array(
						'TrnPointExchange.csv_output_date' => date('Y-m-d H:i:s', $csv_output_date)
					)
				));
			}
		}
		if ($data) {
			$sqldate = date('Y-m-d H:i:s');
			$this->layout = false;
			$filename = 'Tポイント交換申請Csvダウンロード_'.date('YmdHis');
			// 表の一行目を作成
			$th = array('exchange_id', 'exchange_point', 'target_value', 'request_datetime');
			// 表の内容を取得
			$td = array();
			foreach ($data as $res) {
				$td[] = array(
					$res['TrnPointExchange']['exchange_id'],
					$res['TrnPointExchange']['exchange_point'] / 10,
					$res['TrnPointExchange']['target_value'],
					$res['TrnPointExchange']['request_datetime'],
				);
				if ($csv_output_date === null && empty($res['TrnPointExchange']['csv_output_date'])) {
					$this->TrnPointExchange->save(array(
						'exchange_id' => $res['TrnPointExchange']['exchange_id'],
						'csv_output_date' => $sqldate,
						'status' => 1
					));
				}
			}
			$this->set(compact(
				'filename',
				'th',
				'td'
			));
			$this->render('/Pages/download');
		} else {
			$this->Session->setFlash('現在、新規リクエストはございません。', 'default', array('class' => 'callout callout-danger'));
			$this->redirect('/point/index/2');
		}
	}


	public function tpointUpload() {
		if ($this->request->is(array('post', 'put'))) {
			$records = array();
			$err = array();
			$i = 0;
			$file = new SplFileObject($this->request->data['TrnPointExchange']['csv_file']['tmp_name']); 
			$file->setFlags(SplFileObject::READ_CSV); 
			foreach ($file as $line) {
				$i++;
				if ($i == 1) continue;
				if (count($line) != 5) continue;
				if (empty($line[0])) continue;
				foreach ($line as $key => $val) {
					$line[$key] = mb_convert_encoding($val, 'UTF-8', 'SJIS');
				}
				$this->TrnPointExchange->recursive = 0;
				$this->TrnPointExchange->unbindModel(array('belongsTo' => array('MstPointItem', 'MstAdminUser')));
				$var = $this->TrnPointExchange->find('first', array(
					'fields' => array(
						'TrnPointExchange.exchange_id',
						'TrnPointExchange.trade_id',
						'TrnMembers.member_id',
						'TrnMembers.email',
						'TrnMembers.member_name',
						'TrnMembers.device_type',
						'TrnMembers.device_id'
					),
					'conditions' => array(
						'TrnPointExchange.exchange_id' => $line[0],
						'TrnPointExchange.status' => 1
					)
				));
				if ($var) {
					$var['TrnPointExchange']['exchange_resultdetail'] = $line[4];
					$records[] = $var;
				} else {
					$err[] = '・'.$i.'行目に該当するデータが見つかりませんでした。';
				}
			}
			if (empty($err)) {
				$info_flg = $this->request->data['TrnPointExchange']['info_flg'] == 1 ? TRUE : FALSE;
				$sqldate = date('Y-m-d H:i:s');
				foreach ($records as $res) {
					$push_status = 0;
					$mail_status = 0;

					if ($info_flg === TRUE) {
						# Push通知
						$message = '【thanks!】T-ポイント、交換失敗の連絡';
						if ($this->Push->send_push($message, $res['TrnMembers']['device_id'], $res['TrnMembers']['device_type'])) {
							$push_status = 1;
						}

						# メールを送信
						$viewVars = array(
							'member_name' => $res['TrnMembers']['member_name'],
							'failed_value' => $res['TrnPointExchange']['exchange_resultdetail']
						);
						$obj = new CakeEmail('default');
						$mailRespons = $obj->template('point/tpoint_failed')
							->viewVars($viewVars)
							->emailFormat('text')
							->to($res['TrnMembers']['email'])
							->subject('【thanks!】T-ポイント、交換失敗の連絡')
							->send();
						if ($mailRespons) {
							$mail_status = 1;
						}
					}

					# 交換テーブルの更新
					$this->TrnPointExchange->save(array(
						'exchange_id' => $res['TrnPointExchange']['exchange_id'],
						'exchange_execute' => $sqldate,
						'exchange_result' => 1,
						'exchange_resultdetail' => $res['TrnPointExchange']['exchange_resultdetail'],
						'status' => 2,
						'push_status' => $push_status,
						'mail_status' => $mail_status
					));

					# 取引テーブルの更新（キャンセルとする）
					$num = $this->TrnPointTrade->find('count', array(
						'conditions' => array(
							'TrnPointTrade.trade_id' => $res['TrnPointExchange']['trade_id'],
							'TrnPointTrade.trade_kind' => 50,
							'TrnPointTrade.status' => 0
						)
					));
					if ($num > 0) {
						$this->TrnPointTrade->save(array(
							'trade_id' => $res['TrnPointExchange']['trade_id'],
							'status' => 1
						));
					}
				}
				$this->Session->setFlash('アップロードされたCSVの更新が完了しました。', 'default', array('class' => 'callout callout-info'));
				$this->redirect('/point/index/2');
			} else {
				$this->Session->setFlash('Amazonギフト券の登録に失敗しました。<br />'.implode('<br />', $err), 'default', array('class' => 'callout callout-danger'));
			}
		}
		$this->title = 'T-ポイント：結果CSV（エラー）のアップロード';
	}


	public function tpointPush() {
		if ($this->request->is(array('post', 'put'))) {
			$sqldate = date('Y-m-d H:i:s');
			$x = 0;
			foreach ($this->request->data['TrnPointExchange']['exchange_id'] as $exchange_id => $val) {
				if ($val == '1') {
					$x++;

					# 対象の交換申請明細を取り出す
					$this->TrnPointExchange->recursive = 0;
					$this->TrnPointExchange->unbindModel(array('belongsTo' => array('MstPointItem', 'MstAdminUser')));
					$bar = $this->TrnPointExchange->find('first', array(
						'conditions' => array(
							'TrnPointExchange.exchange_id' => $exchange_id,
							'TrnPointExchange.status' => 1
						)
					));
					if ($bar) {
						$push_status = 0;
						$mail_status = 0;

						$add_point = $bar['TrnPointExchange']['exchange_point'] / 10;
						$exchange_resultdetail = <<<EOF
Tカード番号
{$bar['TrnPointExchange']['target_value']}
ポイント付与数
{$add_point}

EOF;

						# Push通知
						$message = '【thanks!】T-ポイント、交換完了の連絡';
						if ($this->Push->send_push($message, $bar['TrnMembers']['device_id'], $bar['TrnMembers']['device_type'])) {
							$push_status = 1;
						}

						# メールを送信
						$viewVars = array(
							'member_name' => $bar['TrnMembers']['member_name'],
							'tcard_number' => $bar['TrnPointExchange']['target_value'],
							'add_point' => $add_point.'pt'
						);
						$obj = new CakeEmail('default');
						$mailRespons = $obj->template('point/tpoint_success')
							->viewVars($viewVars)
							->emailFormat('text')
							->to($bar['TrnMembers']['email'])
							->subject('【thanks!】T-ポイント、交換完了の連絡')
							->send();
						if ($mailRespons) {
							$mail_status = 1;
						}

						# 交換申請を更新する
						$this->TrnPointExchange->save(array(
							'exchange_id' => $exchange_id,
							'exchange_execute' => $sqldate,
							'exchange_result' => 0,
							'exchange_resultdetail' => $exchange_resultdetail,
							'status' => 2,
							'push_status' => $push_status,
							'mail_status' => $mail_status
						));
					}
				}
			}
			if ($x > 0) {
				$this->Session->setFlash('チェックした申請の処理が完了しました。', 'default', array('class' => 'callout callout-info'));
			} else {
				$this->Session->setFlash('処理対象となる申請をチェックしてください。', 'default', array('class' => 'callout callout-danger'));
			}
		}
		$this->redirect('/point/index/2');
	}


	public function tpointHistory() {
		$this->TrnPointExchange->recursive = 0;
		$this->paginate = array(
			'TrnPointExchange' => array(
				'fields' => array(
					'TrnPointExchange.csv_output_date',
					'count(*) as nums'
				),
				'conditions' => array(
					'NOT' => array('TrnPointExchange.csv_output_date' => NULL)
				),
				'group' => array('TrnPointExchange.csv_output_date')
			)
		);
		if ($data = $this->Paginator->paginate('TrnPointExchange')) {
			$this->set('data', $data);
			$this->title = 'T-ポイント：CSV出力履歴';
		} else {
			$this->Session->setFlash('CSV出力履歴が見つかりません。', 'default', array('class' => 'callout callout-danger'));
			$this->redirect('/point/index/1');
		}
	}


	public function tpointView($exchange_id = null) {
		$this->TrnPointExchange->recursive = 0;
		$data = $this->TrnPointExchange->find('first', array(
			'conditions' => array(
				'TrnPointExchange.exchange_id' => $exchange_id,
				'TrnPointExchange.item_id' => 2
			)
		));
		if ($data) {
			$this->set('data', $data);
		} else {
			$this->Session->setFlash('データの呼び出しに失敗しました。', 'default', array('class' => 'callout callout-danger'));
			$this->redirect('/point/index/2');
		}
		$this->title = 'T-ポイント：詳細';
	}


	public function tpointSuccess($exchange_id = null) {
		$this->TrnPointExchange->recursive = 0;
		$data = $this->TrnPointExchange->find('first', array(
			'conditions' => array(
				'TrnPointExchange.exchange_id' => $exchange_id,
				'TrnPointExchange.item_id' => 2
			)
		));
		if ($data) {
			$info_flg = $this->request->data['TrnPointExchange']['info_flg'] == 1 ? TRUE : FALSE;
			$sqldate = date('Y-m-d H:i:s');

			$push_status = $data['TrnPointExchange']['push_status'];
			$mail_status = $data['TrnPointExchange']['mail_status'];

			$add_point = $data['TrnPointExchange']['exchange_point'] / 10;
			$exchange_resultdetail = <<<EOF
Tカード番号
{$data['TrnPointExchange']['target_value']}
ポイント付与数
{$add_point}

EOF;

			if ($info_flg === TRUE) {
				# Push通知
				$message = '【thanks!】T-ポイント、交換完了の連絡';
				if ($this->Push->send_push($message, $data['TrnMembers']['device_id'], $data['TrnMembers']['device_type'])) {
					$push_status = 1;
				}

				# メールを送信
				$viewVars = array(
					'member_name' => $data['TrnMembers']['member_name'],
					'tcard_number' => $data['TrnPointExchange']['target_value'],
					'add_point' => $add_point.'pt'
				);
				$obj = new CakeEmail('default');
				$mailRespons = $obj->template('point/tpoint_success')
					->viewVars($viewVars)
					->emailFormat('text')
					->to($data['TrnMembers']['email'])
					->subject('【thanks!】T-ポイント、交換完了の連絡')
					->send();
				if ($mailRespons) {
					$mail_status = 1;
				}
			}

			# ポイント交換申請を更新する
			$this->TrnPointExchange->save(array(
				'exchange_id' => $exchange_id,
				'exchange_execute' => $sqldate,
				'exchange_result' => 0,
				'exchange_resultdetail' => $exchange_resultdetail,
				'status' => 2,
				'push_status' => $push_status,
				'mail_status' => $mail_status
			));

			# ポイント取引履歴がキャンセルされていた場合、復活させる
			$bar = $this->TrnPointTrade->find('first', array(
				'TrnPointTrade.trade_id' => $data['TrnPointExchange']['trade_id'],
				'TrnPointTrade.status' => 1
			));
			if ($bar) {
				$this->TrnPointTrade->save(array(
					'trade_id' => $data['TrnPointExchange']['trade_id'],
					'status' => 0
				));
			}

			$this->Session->setFlash('データの更新が完了しました。', 'default', array('class' => 'callout callout-info'));
		} else {
			$this->Session->setFlash('データの呼び出しに失敗しました。', 'default', array('class' => 'callout callout-danger'));
		}
		$this->redirect('/point/tpointView/'.$exchange_id);
	}


	public function tpointFailed($exchange_id = null) {
		$this->TrnPointExchange->recursive = 0;
		$data = $this->TrnPointExchange->find('first', array(
			'conditions' => array(
				'TrnPointExchange.exchange_id' => $exchange_id,
				'TrnPointExchange.item_id' => 2
			)
		));
		if ($data) {
			$info_flg = $this->request->data['TrnPointExchange']['info_flg'] == 1 ? TRUE : FALSE;
			$exchange_resultdetail = $this->request->data['TrnPointExchange']['exchange_resultdetail'];
			$sqldate = date('Y-m-d H:i:s');

			$push_status = $data['TrnPointExchange']['push_status'];
			$mail_status = $data['TrnPointExchange']['mail_status'];

			if ($info_flg === TRUE) {
				# Push通知
				$message = '【thanks!】T-ポイント、交換失敗の連絡';
				if ($this->Push->send_push($message, $data['TrnMembers']['device_id'], $data['TrnMembers']['device_type'])) {
					$push_status = 1;
				}

				# メールを送信
				$viewVars = array(
					'member_name' => $data['TrnMembers']['member_name'],
					'failed_value' => $exchange_resultdetail
				);
				$obj = new CakeEmail('default');
				$mailRespons = $obj->template('point/tpoint_failed')
					->viewVars($viewVars)
					->emailFormat('text')
					->to($data['TrnMembers']['email'])
					->subject('【thanks!】T-ポイント、交換失敗の連絡')
					->send();
				if ($mailRespons) {
					$mail_status = 1;
				}
			}

			# ポイント交換申請を更新する
			$this->TrnPointExchange->save(array(
				'exchange_id' => $exchange_id,
				'exchange_execute' => $sqldate,
				'exchange_result' => 1,
				'exchange_resultdetail' => $exchange_resultdetail,
				'status' => 2,
				'push_status' => $push_status,
				'mail_status' => $mail_status
			));

			# ポイント取引履歴をキャンセルする
			$bar = $this->TrnPointTrade->find('first', array(
				'TrnPointTrade.trade_id' => $data['TrnPointExchange']['trade_id'],
				'TrnPointTrade.status' => 0
			));
			if ($bar) {
				$this->TrnPointTrade->save(array(
					'trade_id' => $data['TrnPointExchange']['trade_id'],
					'status' => 1
				));
			}

			$this->Session->setFlash('データの更新が完了しました。', 'default', array('class' => 'callout callout-info'));
		} else {
			$this->Session->setFlash('データの呼び出しに失敗しました。', 'default', array('class' => 'callout callout-danger'));
		}
		$this->redirect('/point/tpointView/'.$exchange_id);
	}


	public function starbucksView($exchange_id = null) {
		$this->TrnPointExchange->recursive = 0;
		$data = $this->TrnPointExchange->find('first', array(
			'conditions' => array(
				'TrnPointExchange.exchange_id' => $exchange_id,
				'TrnPointExchange.item_id' => 3
			)
		));
		if ($data) {
			$this->set('data', $data);
		} else {
			$this->Session->setFlash('データの呼び出しに失敗しました。', 'default', array('class' => 'callout callout-danger'));
			$this->redirect('/point/index/3');
		}
		$this->title = 'スターバックスギフトカード：詳細';
	}


	public function starbucksSuccess($exchange_id = null) {
		$this->TrnPointExchange->recursive = 0;
		$data = $this->TrnPointExchange->find('first', array(
			'conditions' => array(
				'TrnPointExchange.exchange_id' => $exchange_id,
				'TrnPointExchange.item_id' => 3
			)
		));
		if ($data) {
			$info_flg = $this->request->data['TrnPointExchange']['info_flg'] == 1 ? TRUE : FALSE;
			$sqldate = date('Y-m-d H:i:s');

			$push_status = $data['TrnPointExchange']['push_status'];
			$mail_status = $data['TrnPointExchange']['mail_status'];

			if ($info_flg === TRUE) {
				# Push通知
				$message = '【thanks!】スターバックスギフトカード、交換完了の連絡';
				if ($this->Push->send_push($message, $data['TrnMembers']['device_id'], $data['TrnMembers']['device_type'])) {
					$push_status = 1;
				}

				# メールを送信
				$viewVars = array(
					'member_name' => $data['TrnMembers']['member_name']
				);
				$obj = new CakeEmail('default');
				$mailRespons = $obj->template('point/starbucks_success')
					->viewVars($viewVars)
					->emailFormat('text')
					->to($data['TrnMembers']['email'])
					->subject('【thanks!】スターバックスギフトカード、交換完了の連絡')
					->send();
				if ($mailRespons) {
					$mail_status = 1;
				}
			}

			# ポイント交換申請を更新する
			$this->TrnPointExchange->save(array(
				'exchange_id' => $exchange_id,
				'exchange_execute' => $sqldate,
				'exchange_result' => 0,
				'exchange_resultdetail' => 'スターバックスギフトカードをメールに送信しました！',
				'status' => 2,
				'push_status' => $push_status,
				'mail_status' => $mail_status
			));

			# ポイント取引履歴がキャンセルされていた場合、復活させる
			$bar = $this->TrnPointTrade->find('first', array(
				'TrnPointTrade.trade_id' => $data['TrnPointExchange']['trade_id'],
				'TrnPointTrade.status' => 1
			));
			if ($bar) {
				$this->TrnPointTrade->save(array(
					'trade_id' => $data['TrnPointExchange']['trade_id'],
					'status' => 0
				));
			}

			$this->Session->setFlash('データの更新が完了しました。', 'default', array('class' => 'callout callout-info'));
		} else {
			$this->Session->setFlash('データの呼び出しに失敗しました。', 'default', array('class' => 'callout callout-danger'));
		}
		$this->redirect('/point/starbucksView/'.$exchange_id);
	}


	public function starbucksFailed($exchange_id = null) {
		$this->TrnPointExchange->recursive = 0;
		$data = $this->TrnPointExchange->find('first', array(
			'conditions' => array(
				'TrnPointExchange.exchange_id' => $exchange_id,
				'TrnPointExchange.item_id' => 3
			)
		));
		if ($data) {
			$info_flg = $this->request->data['TrnPointExchange']['info_flg'] == 1 ? TRUE : FALSE;
			$exchange_resultdetail = $this->request->data['TrnPointExchange']['exchange_resultdetail'];
			$sqldate = date('Y-m-d H:i:s');

			$push_status = $data['TrnPointExchange']['push_status'];
			$mail_status = $data['TrnPointExchange']['mail_status'];

			if ($info_flg === TRUE) {
				# Push通知
				$message = '【thanks!】スターバックスギフトカード、交換失敗の連絡';
				if ($this->Push->send_push($message, $data['TrnMembers']['device_id'], $data['TrnMembers']['device_type'])) {
					$push_status = 1;
				}

				# メールを送信
				$viewVars = array(
					'member_name' => $data['TrnMembers']['member_name'],
					'failed_value' => $exchange_resultdetail
				);
				$obj = new CakeEmail('default');
				$mailRespons = $obj->template('point/starbucks_failed')
					->viewVars($viewVars)
					->emailFormat('text')
					->to($data['TrnMembers']['email'])
					->subject('【thanks!】スターバックスギフトカード、交換失敗の連絡')
					->send();
				if ($mailRespons) {
					$mail_status = 1;
				}
			}

			# ポイント交換申請を更新する
			$this->TrnPointExchange->save(array(
				'exchange_id' => $exchange_id,
				'exchange_execute' => $sqldate,
				'exchange_result' => 1,
				'exchange_resultdetail' => $exchange_resultdetail,
				'status' => 2,
				'push_status' => $push_status,
				'mail_status' => $mail_status
			));

			# ポイント取引履歴をキャンセルする
			$bar = $this->TrnPointTrade->find('first', array(
				'TrnPointTrade.trade_id' => $data['TrnPointExchange']['trade_id'],
				'TrnPointTrade.status' => 0
			));
			if ($bar) {
				$this->TrnPointTrade->save(array(
					'trade_id' => $data['TrnPointExchange']['trade_id'],
					'status' => 1
				));
			}

			$this->Session->setFlash('データの更新が完了しました。', 'default', array('class' => 'callout callout-info'));
		} else {
			$this->Session->setFlash('データの呼び出しに失敗しました。', 'default', array('class' => 'callout callout-danger'));
		}
		$this->redirect('/point/starbucksView/'.$exchange_id);
	}
}
