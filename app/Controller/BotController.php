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
class BotController extends AppController {

	public $components = array(
		'Paginator',
//		'Search.Prg'
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


	public function index() {
		$this->paginate = array(
			'MstDiaryBot' => array(
				'conditions' => array(
					'MstDiaryBot.status' => 0
				)
			)
		);
		$this->set('data', $this->Paginator->paginate('MstDiaryBot'));
		$this->set('system_user', $this->TrnMembers->find('first', array(
			'conditions' => array(
				'TrnMembers.member_id' => 999999999
			)
		)));
		$this->title = 'BOT管理：一覧';
	}


	public function view($bot_id = null) {
		$params = array(
			'conditions' => array(
				'MstDiaryBot.bot_id' => $bot_id,
				'MstDiaryBot.status' => 0
			)
		);
		if ($data = $this->MstDiaryBot->find('first', $params)) {
			$this->MstDiaryBotRelation->recursive = 0;
			$data2 = $this->MstDiaryBotRelation->find('all', array(
				'conditions' => array(
					'MstDiaryBotRelation.shop_id' => 0,
					'MstDiaryBotRelation.bot_id' => $bot_id,
					'MstDiaryBotRelation.status' => 0
				),
				'order' => array('MstDiaryBotRelation.id' => 'asc')
			));
			if ($data2) {
				foreach ($data2 as $key => $res) {
					$bar = $this->MstThanksStamp->find('first', array(
						'conditions' => array(
							'MstThanksStamp.stamp_id' => $res['MstDiaryBotContents']['stamp_id']
						)
					));
					$data2[$key]['MstThanksStamp'] = $bar['MstThanksStamp'];
				}
			}
			$this->set('data', $data);
			$this->set('data2', $data2);
			$this->title = 'BOT管理：詳細表示';
		} else {
			$this->redirect('/bot/index/');
		}
	}


	public function add() {
		if ($this->request->is(array('post', 'put'))) {
			$val = $this->request->data['MstDiaryBot']['profile_img_url'];
			if (isset($val['tmp_name']) && is_uploaded_file($val['tmp_name'])) {
				$bar = explode('.', $val['name']);
				$ext = array_pop($bar);
				$file_name = 'upload/'.md5(uniqid(rand(),true)).'.'.$ext;
				$path = Configure::read('IMG_PATH');
				if (move_uploaded_file($val['tmp_name'], $path.$file_name))	{
					chmod($path.$file_name, 0644);
					$this->request->data['MstDiaryBot']['profile_img_url'] = $file_name;
				}
			} else {
				$this->request->data['MstDiaryBot']['profile_img_url'] = '';
			}
			$this->MstDiaryBot->create();
			if ($this->MstDiaryBot->save($this->request->data)) {
				$bot_id = $this->MstDiaryBot->getLastInsertID();

				$this->Session->setFlash('BOTの登録が完了しました。', 'default', array('class' => 'callout callout-info'));
				$this->redirect('/bot/view/'.$bot_id);
			} else {
				$this->Session->setFlash('BOTの登録に失敗しました。', 'default', array('class' => 'callout callout-danger'));
			}
		}
		$this->title = 'BOT管理：追加';
	}


	public function edit($bot_id = null) {
		if ($this->request->is(array('post', 'put'))) {
			$val = $this->request->data['MstDiaryBot']['profile_img_url'];
			if (isset($val['tmp_name']) && is_uploaded_file($val['tmp_name'])) {
				$bar = explode('.', $val['name']);
				$ext = array_pop($bar);
				$file_name = 'upload/'.md5(uniqid(rand(),true)).'.'.$ext;
				$path = Configure::read('IMG_PATH');
				if (move_uploaded_file($val['tmp_name'], $path.$file_name))	{
					chmod($path.$file_name, 0644);
					$this->request->data['MstDiaryBot']['profile_img_url'] = $file_name;
				}
			} else {
				unset($this->request->data['MstDiaryBot']['profile_img_url']);
			}
			if ($this->MstDiaryBot->save($this->request->data)) {
				$this->Session->setFlash('BOTの編集が完了しました。', 'default', array('class' => 'callout callout-info'));
				$this->redirect('/bot/view/'.$bot_id);
			} else {
				$this->Session->setFlash('BOTの編集に失敗しました。', 'default', array('class' => 'callout callout-danger'));
			}
		} else {
			$data = $this->MstDiaryBot->find('first', array(
				'conditions' => array(
					'MstDiaryBot.bot_id' => $bot_id,
					'MstDiaryBot.status' => 0
				)
			));
			if ($data) {
				$this->request->data = $data;
			} else {
				$this->Session->setFlash('BOTの呼び出しに失敗しました。', 'default', array('class' => 'callout callout-danger'));
				$this->redirect('/bot/index/');
			}
		}
		$this->title = 'BOT管理：編集';
	}


	public function delete($bot_id = null) {
		$data = $this->MstDiaryBot->find('first', array(
			'conditions' => array(
				'MstDiaryBot.bot_id' => $bot_id,
				'MstDiaryBot.status' => 0
			)
		));
		if ($data) {
			# BOTを無効にする
			$this->MstDiaryBot->save(array(
				'bot_id' => $bot_id,
				'status' => 1
			));

			# リレーションを無効にする
			$sql = 'UPDATE `mst_diary_bot_relation` AS `MstDiaryBotRelation` SET `MstDiaryBotRelation`.`status`=1 WHERE `MstDiaryBotRelation`.`bot_id`='.$bot_id.' AND `MstDiaryBotRelation`.`status`=0';
			$this->MstDiaryBotRelation->query($sql);

			$this->Session->setFlash('BOTの削除が完了しました。', 'default', array('class' => 'callout callout-info'));
		} else {
			$this->Session->setFlash('BOTの呼び出しに失敗しました。', 'default', array('class' => 'callout callout-danger'));
		}
		$this->redirect('/bot/index/');
	}


	public function contentsAdd($bot_id = null) {
		if ($this->request->is(array('post', 'put'))) {
			

			$val = $this->request->data['MstDiaryBotContents']['img_url'];
			if (isset($val['tmp_name']) && is_uploaded_file($val['tmp_name'])) {
				$bar = explode('.', $val['name']);
				$ext = array_pop($bar);
				$file_name = 'upload/'.md5(uniqid(rand(),true)).'.'.$ext;
				$path = Configure::read('IMG_PATH');
				if (move_uploaded_file($val['tmp_name'], $path.$file_name))	{
					chmod($path.$file_name, 0644);
					$this->request->data['MstDiaryBotContents']['img_url'] = $file_name;
				}
			} else {
				$this->request->data['MstDiaryBotContents']['img_url'] = '';
			}

			$this->MstDiaryBotContents->create();
			if ($this->MstDiaryBotContents->save($this->request->data)) {
				$contents_id = $this->MstDiaryBotContents->getLastInsertID();

				# リレーションテーブルへ登録
				$this->MstDiaryBotRelation->save(array(
					'shop_id' => 0,
					'bot_id' => $bot_id,
					'contents_id' => $contents_id,
					'status' => 0
				));

				# 既に当BOTを利用済みの店舗があれば、そのリレーションも追加
				$data2 = $this->MstDiaryBotRelation->find('all', array(
					'conditions' => array(
						'MstDiaryBotRelation.bot_id' => $bot_id,
						'MstDiaryBotRelation.status' => 0,
						'NOT' => array('MstDiaryBotRelation.shop_id' => 0)
					),
					'group' => array('MstDiaryBotRelation.shop_id')
				));
				if ($data2) {
					foreach ($data2 as $res) {
						$res['MstDiaryBotRelation']['id'] = null;
						$res['MstDiaryBotRelation']['contents_id'] = $contents_id;
#						$res['MstDiaryBotRelation']['last_send_contents_id'] = 0;
#						$res['MstDiaryBotRelation']['last_send_time'] = '0000-00-00 00:00:00';

						$this->MstDiaryBotRelation->create();
						$this->MstDiaryBotRelation->save($res['MstDiaryBotRelation']);
					}
				}

				$this->Session->setFlash('BOTコンテンツの登録が完了しました。', 'default', array('class' => 'callout callout-info'));
				$this->redirect('/bot/view/'.$bot_id);
			} else {
				$this->Session->setFlash('BOTコンテンツの登録に失敗しました。', 'default', array('class' => 'callout callout-danger'));
			}
		} else {
			$this->request->data['MstDiaryBotContents']['stamp_id'] = 0;
		}
		$stamps = $this->MstThanksStamp->findAllStamps();
		$act_categoryId = null;
		foreach ($stamps as $category_id => $res) {
			foreach ($res['data'] as $val) {
				if ($this->request->data['MstDiaryBotContents']['stamp_id'] == 0) {
					$this->request->data['MstDiaryBotContents']['stamp_id'] = $val['MstThanksStamp']['stamp_id'];
					$act_categoryId = $category_id;
					break;
				}
				elseif ($val['MstThanksStamp']['stamp_id'] == $this->request->data['MstDiaryBotContents']['stamp_id']) {
					$act_categoryId = $category_id;
					break;
				}
			}
		}
		$this->set('stamps', $stamps);
		$this->set('act_categoryId', $act_categoryId);
		$this->title = 'BOT管理：コンテンツの登録';
	}


	public function contentsEdit($bot_id = null, $contents_id = null) {
		if ($this->request->is(array('post', 'put'))) {
			$val = $this->request->data['MstDiaryBotContents']['img_url'];
			if (isset($val['tmp_name']) && is_uploaded_file($val['tmp_name'])) {
				$bar = explode('.', $val['name']);
				$ext = array_pop($bar);
				$file_name = 'upload/'.md5(uniqid(rand(),true)).'.'.$ext;
				$path = Configure::read('IMG_PATH');
				if (move_uploaded_file($val['tmp_name'], $path.$file_name))	{
					chmod($path.$file_name, 0644);
					$this->request->data['MstDiaryBotContents']['img_url'] = $file_name;
				}
			} else {
				unset($this->request->data['MstDiaryBotContents']['img_url']);
			}
			if ($this->MstDiaryBotContents->save($this->request->data)) {
				$this->Session->setFlash('BOTコンテンツの登録が完了しました。', 'default', array('class' => 'callout callout-info'));
				$this->redirect('/bot/view/'.$bot_id);
			} else {
				$this->Session->setFlash('BOTコンテンツの登録に失敗しました。', 'default', array('class' => 'callout callout-danger'));
			}
		} else {
			$data = $this->MstDiaryBotContents->find('first', array(
				'conditions' => array(
					'MstDiaryBotContents.contents_id' => $contents_id,
					'MstDiaryBotContents.status' => 0
				)
			));
			if ($data) {
				$this->request->data = $data;
				$this->request->data['MstDiaryBotContents']['hidden_img_url'] = $data['MstDiaryBotContents']['img_url'];
			} else {
				$this->Session->setFlash('BOTコンテンツの呼び出しに失敗しました。', 'default', array('class' => 'callout callout-danger'));
				$this->redirect('/bot/view/'.$bot_id);
			}
		}
		$stamps = $this->MstThanksStamp->findAllStamps();
		$act_categoryId = null;
		foreach ($stamps as $category_id => $res) {
			foreach ($res['data'] as $val) {
				if ($val['MstThanksStamp']['stamp_id'] == $this->request->data['MstDiaryBotContents']['stamp_id']) {
					$act_categoryId = $category_id;
					break;
				}
			}
		}
		$this->set('stamps', $stamps);
		$this->set('act_categoryId', $act_categoryId);
		$this->title = 'BOT管理：コンテンツの編集';
	}


	public function contentsDelete($bot_id = null, $contents_id = null) {
		$data = $this->MstDiaryBotContents->find('first', array(
			'conditions' => array(
				'MstDiaryBotContents.contents_id' => $contents_id,
				'MstDiaryBotContents.status' => 0
			)
		));
		if ($data) {
			# コンテンツを無効にする
			$this->MstDiaryBotContents->save(array(
				'contents_id' => $contents_id,
				'status' => 1
			));

			# リレーションを無効にする
			$sql = 'UPDATE `mst_diary_bot_relation` AS `MstDiaryBotRelation` SET `MstDiaryBotRelation`.`status`=1 WHERE `MstDiaryBotRelation`.`contents_id`='.$contents_id.' AND `MstDiaryBotRelation`.`status`=0';
			$this->MstDiaryBotRelation->query($sql);

			$this->Session->setFlash('BOTコンテンツの削除が完了しました。', 'default', array('class' => 'callout callout-info'));
		} else {
			$this->Session->setFlash('BOTコンテンツの削除に失敗しました。', 'default', array('class' => 'callout callout-danger'));
		}
		$this->redirect('/bot/view/'.$bot_id);
	}


	public function relationView($shop_id = null, $bot_id = null) {
		$data = $this->MstDiaryBotRelation->find('first', array(
			'conditions' => array(
				'MstDiaryBotRelation.shop_id' => $shop_id,
				'MstDiaryBotRelation.bot_id' => $bot_id,
				'MstDiaryBotRelation.status' => 0
			)
		));
		if ($data) {
			$bar = $this->TrnMembers->find('first', array(
				'conditions' => array(
					'TrnMembers.member_id' => $data['MstDiaryBotRelation']['bot_member_id'],
					'TrnMembers.status' => 9
				)
			));
			$data['TrnMembers'] = $bar['TrnMembers'];
		} else {
			$this->Session->setFlash('BOTの呼び出しに失敗しました。', 'default', array('class' => 'callout callout-danger'));
			$this->redirect('/account/shopView/'.$shop_id);
		}
		$this->set('data', $data);
		$this->title = '店舗管理：Botの詳細';
	}


	public function relationChoice($shop_id = null) {
		$bar = $this->MstDiaryBotRelation->find('list', array(
			'conditions' => array(
				'MstDiaryBotRelation.shop_id' => $shop_id,
				'MstDiaryBotRelation.status' => 0
			),
			'group' => array('MstDiaryBotRelation.bot_id')
		));
		if ($bar) {
			$params = array(
				'conditions' => array(
					'MstDiaryBot.status' => 0,
					'NOT' => array('MstDiaryBot.bot_id' => $bar)
				)
			);
		} else {
			$params = array(
				'conditions' => array(
					'MstDiaryBot.status' => 0
				)
			);
		}
		$this->set('shop_id', $shop_id);
		$this->set('data', $this->MstDiaryBot->find('all', $params));
		$this->title = '店舗管理：Botの一覧';
	}


	public function relationAdd($shop_id = null, $bot_id = null) {
		if ($this->request->is(array('post', 'put'))) {
			$err = 0;

			# 画像を処理
			$val = $this->request->data['TrnMembers']['profile_img_url'];
			if (isset($val['tmp_name']) && is_uploaded_file($val['tmp_name'])) {
				$bar = explode('.', $val['name']);
				$ext = array_pop($bar);
				$file_name = 'upload/'.md5(uniqid(rand(),true)).'.'.$ext;
				$path = Configure::read('IMG_PATH');
				if (move_uploaded_file($val['tmp_name'], $path.$file_name))	{
					chmod($path.$file_name, 0644);
					$this->request->data['TrnMembers']['profile_img_url'] = $file_name;
				}
			} else {
				$this->request->data['TrnMembers']['profile_img_url'] = $this->request->data['TrnMembers']['hidden_profile_img_url'];
			}

			# 投稿頻度を調整
			# ※チェックが入っていない項目はディフォルトの値にする
			switch ($this->request->data['MstDiaryBotRelation']['type']) {
				case 1:
					if ($this->request->data['MstDiaryBotRelation']['send_cycle_month_date'] === '') {
						$err = 1;
					} else {
						$this->request->data['MstDiaryBotRelation']['send_cycle_week_day'] = 9;
						$this->request->data['MstDiaryBotRelation']['send_cycle_day_once'] = 0;
					}
					break;
				case 2:
					if ($this->request->data['MstDiaryBotRelation']['send_cycle_week_day'] === '') {
						$err = 1;
					} else {
						$this->request->data['MstDiaryBotRelation']['send_cycle_month_date'] = 0;
						$this->request->data['MstDiaryBotRelation']['send_cycle_day_once'] = 0;
					}
					break;
				case 3:
					if ($this->request->data['MstDiaryBotRelation']['send_cycle_day_once'] === '') {
						$err = 1;
					} else {
						$this->request->data['MstDiaryBotRelation']['send_cycle_month_date'] = 0;
						$this->request->data['MstDiaryBotRelation']['send_cycle_week_day'] = 9;
					}
					break;
			}
			if ($err === 0) {
				# Bot用ユーザの登録
				$this->request->data['TrnMembers']['shop_id'] = $shop_id;
				$this->request->data['TrnMembers']['thanks_notice_flg'] = 1;
				$this->request->data['TrnMembers']['diary_notice_flg'] = 1;
				$this->request->data['TrnMembers']['birthday_notice_flg'] = 1;
				$this->request->data['TrnMembers']['status'] = 9;
				$this->request->data['TrnMembers']['bot_flg'] = 1;
				$this->request->data['TrnMembers']['insert_time'] = date('Y-m-d H:i:s');
				$this->request->data['TrnMembers']['update_time'] = date('Y-m-d H:i:s');
				$this->TrnMembers->create();
				if ($this->TrnMembers->save($this->request->data)) {
					$member_id = $this->TrnMembers->getLastInsertID();

					# チームへ登録
					$this->request->data['TrnTeams']['member_id'] = $member_id;
					$this->request->data['TrnTeams']['shop_id'] = $shop_id;
					$this->request->data['TrnTeams']['active_flg'] = 1;
					$this->request->data['TrnTeams']['del_flg'] = 0;
					$this->request->data['TrnTeams']['insert_time'] = date('Y-m-d H:i:s');
					$this->request->data['TrnTeams']['update_time'] = date('Y-m-d H:i:s');
					$this->TrnTeams->create();
					$this->TrnTeams->save($this->request->data);

					# Botコンテンツ一覧を呼び出す
					$data = $this->MstDiaryBotRelation->find('all', array(
						'conditions' => array(
							'MstDiaryBotRelation.shop_id' => 0,
							'MstDiaryBotRelation.bot_id' => $bot_id,
							'MstDiaryBotRelation.status' => 0
						),
						'order' => array('MstDiaryBotRelation.id' => 'asc')
					));
					if ($data) {
						$this->request->data['MstDiaryBotRelation']['shop_id'] = $shop_id;
						$this->request->data['MstDiaryBotRelation']['bot_member_id'] = $member_id;
						foreach ($data as $res) {
							$this->request->data['MstDiaryBotRelation']['contents_id'] = $res['MstDiaryBotRelation']['contents_id'];
							$this->MstDiaryBotRelation->create();
							$this->MstDiaryBotRelation->save($this->request->data);
						}
						$this->Session->setFlash('BOTの新規設定が完了しました。', 'default', array('class' => 'callout callout-info'));
						$this->redirect('/account/shopView/'.$shop_id);
					}
				}
			}
			$this->Session->setFlash('BOTの新規設定に失敗しました。', 'default', array('class' => 'callout callout-danger'));
		} else {
			$this->request->data['MstDiaryBotRelation']['type'] = 1;
			$this->request->data['MstDiaryBotRelation']['execute_timeh'] = '09';
		}

		# Bot情報の呼び出し
		$bot = $this->MstDiaryBot->find('first', array(
			'conditions' => array(
				'MstDiaryBot.bot_id' => $bot_id,
				'MstDiaryBot.status' => 0
			)
		));
		if ($bot) {
			$this->request->data['MstDiaryBotRelation']['bot_id'] = $bot_id;
			$this->request->data['TrnMembers']['member_name'] = $bot['MstDiaryBot']['bot_name'];
			$this->request->data['TrnMembers']['hidden_profile_img_url'] = $bot['MstDiaryBot']['profile_img_url'];
			$this->request->data['TrnMembers']['self_introduction'] = $bot['MstDiaryBot']['self_introduction'];
		} else {
			$this->Session->setFlash('BOTの呼び出しに失敗しました。', 'default', array('class' => 'callout callout-danger'));
			$this->redirect('/account/shopView/'.$shop_id);
		}
		$this->set('type1', $this->MstDiaryBotRelation->readSendCycleMonthDate());
		$this->set('type2', $this->MstDiaryBotRelation->readSendCycleWeekDay());
		$this->set('type3', $this->MstDiaryBotRelation->readSendCycleDayOnce());
		$this->set('type4', $this->MstDiaryBotRelation->readSendMethod());
		$this->set('type5', $this->MstDiaryBotRelation->readSendLoopFlg());
		$this->set('type6', $this->MstDiaryBotRelation->readExecuteTimeH());
		$this->title = '店舗管理：Botの登録';
	}


	public function relationEdit($shop_id = null, $bot_id = null) {
		if ($this->request->is(array('post', 'put'))) {
			$err = 0;

			# 画像を処理
			$val = $this->request->data['TrnMembers']['profile_img_url'];
			if (isset($val['tmp_name']) && is_uploaded_file($val['tmp_name'])) {
				$bar = explode('.', $val['name']);
				$ext = array_pop($bar);
				$file_name = 'upload/'.md5(uniqid(rand(),true)).'.'.$ext;
				$path = Configure::read('IMG_PATH');
				if (move_uploaded_file($val['tmp_name'], $path.$file_name))	{
					chmod($path.$file_name, 0644);
					$this->request->data['TrnMembers']['profile_img_url'] = $file_name;
				}
			} else {
				$this->request->data['TrnMembers']['profile_img_url'] = $this->request->data['TrnMembers']['hidden_profile_img_url'];
			}

			# 投稿頻度を調整
			# ※チェックが入っていない項目はディフォルトの値にする
			switch ($this->request->data['MstDiaryBotRelation']['type']) {
				case 1:
					if ($this->request->data['MstDiaryBotRelation']['send_cycle_month_date'] === '') {
						$err = 1;
					} else {
						$this->request->data['MstDiaryBotRelation']['send_cycle_week_day'] = 9;
						$this->request->data['MstDiaryBotRelation']['send_cycle_day_once'] = 0;
					}
					break;
				case 2:
					if ($this->request->data['MstDiaryBotRelation']['send_cycle_week_day'] === '') {
						$err = 1;
					} else {
						$this->request->data['MstDiaryBotRelation']['send_cycle_month_date'] = 0;
						$this->request->data['MstDiaryBotRelation']['send_cycle_day_once'] = 0;
					}
					break;
				case 3:
					if ($this->request->data['MstDiaryBotRelation']['send_cycle_day_once'] === '') {
						$err = 1;
					} else {
						$this->request->data['MstDiaryBotRelation']['send_cycle_month_date'] = 0;
						$this->request->data['MstDiaryBotRelation']['send_cycle_week_day'] = 9;
					}
					break;
			}
			if ($err === 0) {
				# Botユーザの更新
				$this->request->data['TrnMembers']['update_time'] = date('Y-m-d H:i:s');
				$this->TrnMembers->save($this->request->data);

				# リレーションの一括更新
				$bar = $this->request->data['MstDiaryBotRelation'];
				$this->MstDiaryBotRelation->unbindModel(array('hasOne' => array('MstDiaryBotContents')));
				$this->MstDiaryBotRelation->updateAll(
					array(
						'MstDiaryBotRelation.send_cycle_month_date' => $bar['send_cycle_month_date'],
						'MstDiaryBotRelation.send_cycle_week_day' => $bar['send_cycle_week_day'],
						'MstDiaryBotRelation.send_cycle_day_once' => $bar['send_cycle_day_once'],
						'MstDiaryBotRelation.send_method' => $bar['send_method'],
						'MstDiaryBotRelation.send_loop_flg' => $bar['send_loop_flg'],
						'MstDiaryBotRelation.execute_timeh' => "'".$bar['execute_timeh']."'"
					),
					array(
						'MstDiaryBotRelation.shop_id' => $shop_id,
						'MstDiaryBotRelation.bot_id' => $bot_id,
						'MstDiaryBotRelation.status' => 0
					)
				);

				$this->Session->setFlash('BOTの更新が完了しました。', 'default', array('class' => 'callout callout-info'));
				$this->redirect('/account/shopView/'.$shop_id);
			} else {
				$this->Session->setFlash('BOTの更新に失敗しました。', 'default', array('class' => 'callout callout-danger'));
			}
		} else {
			$this->request->data = $this->MstDiaryBotRelation->find('first', array(
				'conditions' => array(
					'MstDiaryBotRelation.shop_id' => $shop_id,
					'MstDiaryBotRelation.bot_id' => $bot_id,
					'MstDiaryBotRelation.status' => 0
				)
			));
			if ($this->request->data) {
				if ($this->request->data['MstDiaryBotRelation']['send_cycle_month_date'] != 0) {
					$this->request->data['MstDiaryBotRelation']['type'] = 1;
				} elseif ($this->request->data['MstDiaryBotRelation']['send_cycle_week_day'] != 9) {
					$this->request->data['MstDiaryBotRelation']['type'] = 2;
				} else {
					$this->request->data['MstDiaryBotRelation']['type'] = 3;
				}
				$bar = $this->TrnMembers->find('first', array(
					'conditions' => array(
						'TrnMembers.member_id' => $this->request->data['MstDiaryBotRelation']['bot_member_id'],
						'TrnMembers.status' => 9
					)
				));
				$this->request->data['TrnMembers'] = $bar['TrnMembers'];
				$this->request->data['TrnMembers']['hidden_profile_img_url'] = $bar['TrnMembers']['profile_img_url'];
			} else {
				$this->Session->setFlash('BOTの呼び出しに失敗しました。', 'default', array('class' => 'callout callout-danger'));
				$this->redirect('/account/shopView/'.$shop_id);
			}
		}
		$this->set('type1', $this->MstDiaryBotRelation->readSendCycleMonthDate());
		$this->set('type2', $this->MstDiaryBotRelation->readSendCycleWeekDay());
		$this->set('type3', $this->MstDiaryBotRelation->readSendCycleDayOnce());
		$this->set('type4', $this->MstDiaryBotRelation->readSendMethod());
		$this->set('type5', $this->MstDiaryBotRelation->readSendLoopFlg());
		$this->set('type6', $this->MstDiaryBotRelation->readExecuteTimeH());
		$this->title = '店舗管理：Botの編集';
	}


	public function relationDelete($shop_id = null, $bot_id = null) {
		$data = $this->MstDiaryBotRelation->find('first', array(
			'conditions' => array(
				'MstDiaryBotRelation.shop_id' => $shop_id,
				'MstDiaryBotRelation.bot_id' => $bot_id,
				'MstDiaryBotRelation.status' => 0
			)
		));
		if ($data) {
			# チームから解除する
			$this->TrnTeams->unbindModel(array('belongsTo' => array('TrnMembers', 'MstAdminUser')));
			$this->TrnTeams->updateAll(
				array(
					'TrnTeams.active_flg' => 0,
					'TrnTeams.del_flg' => 1,
					'TrnTeams.update_time' => "'".date('Y-m-d H:i:s')."'"
				),
				array(
					'TrnTeams.member_id' => $data['MstDiaryBotRelation']['bot_member_id'],
					'TrnTeams.shop_id' => $shop_id,
					'TrnTeams.del_flg' => 0
				)
			);

			# リレーションを無効にする
			$this->MstDiaryBotRelation->unbindModel(array('hasOne' => array('MstDiaryBotContents')));
			$this->MstDiaryBotRelation->updateAll(
				array(
					'MstDiaryBotRelation.status' => 1
				),
				array(
					'MstDiaryBotRelation.shop_id' => $shop_id,
					'MstDiaryBotRelation.bot_id' => $bot_id,
					'MstDiaryBotRelation.status' => 0
				)
			);
			$this->Session->setFlash('BOTの解除が完了しました。', 'default', array('class' => 'callout callout-info'));
		} else {
			$this->Session->setFlash('BOTの解除に失敗しました。', 'default', array('class' => 'callout callout-danger'));
		}
		$this->redirect('/account/shopView/'.$shop_id);
	}
}
