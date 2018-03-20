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
class StampController extends AppController {

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

	public function index($category_id = null) {
		if ($category_id === null) {
			$this->set('data', $this->MstThanksStampCategory->find('all', array(
				'conditions' => array(
					'MstThanksStampCategory.status' => 0,
				),
				'order' => array(
					'MstThanksStampCategory.sort' => 'asc'
				)
			)));
			$this->title = 'スタンプ管理：カテゴリ一覧';
			$this->render('categories_index');
		} else {
			$categories = $this->MstThanksStampCategory->find('first', array(
				'conditions' => array(
					'MstThanksStampCategory.category_id' => $category_id
				),
				'order' => array(
					'MstThanksStampCategory.sort'
				)
			));
			$this->set('data1', $this->MstThanksStamp->find('all', array(
				'conditions' => array(
					'MstThanksStamp.category_id' => $category_id,
					'MstThanksStamp.status' => 0
				),
				'order' => array(
					'MstThanksStamp.sort' => 'asc',
					'MstThanksStamp.stamp_id' => 'asc'
				)
			)));
			$this->set('data2', $this->MstThanksStamp->find('all', array(
				'conditions' => array(
					'MstThanksStamp.category_id' => $category_id,
					'MstThanksStamp.status' => 1
				),
				'order' => array(
					'MstThanksStamp.sort' => 'asc',
					'MstThanksStamp.stamp_id' => 'asc'
				)
			)));
			$this->set('category_id', $category_id);
			$this->title = 'スタンプ管理：【'.$categories['MstThanksStampCategory']['category_name'].'】スタンプの一覧';
			$this->render('index');
		}
	}

	public function add($category_id = null) {
		if ($this->request->is(array('post', 'put'))) {
			$val = $this->request->data['MstThanksStamp']['image_url'];
			if (is_uploaded_file($val['tmp_name'])) {
				$bar = explode('.', $val['name']);
				$ext = array_pop($bar);
				$file_name = 'stamp/'.md5(uniqid(rand(),true)).'.'.$ext;
				$path = '/home/thanks/www/api-thanks.me/';
				if (move_uploaded_file($val['tmp_name'], $path.$file_name))	{
					chmod($path.$file_name, 0644);
					$this->request->data['MstThanksStamp']['image_url'] = $file_name;
				}
			} else {
				unset($this->request->data['MstThanksStamp']['image_url']);
			}

			# すぐ公開する場合、ソート順を最後の値にする
			if ($this->request->data['MstThanksStamp']['status'] == 0) {
				$bar = $this->MstThanksStamp->find('first', array(
					'fields' => array('max(MstThanksStamp.sort) as max_val'),
					'conditions' => array(
						'MstThanksStamp.category_id' => $this->request->data['MstThanksStamp']['category_id']
					)
				));
				$this->request->data['MstThanksStamp']['sort'] = !empty($bar[0]['max_val']) ? $bar[0]['max_val'] + 1 : 1;
			} else {
				$this->request->data['MstThanksStamp']['sort'] = 0;
			}

			$this->request->data['MstThanksStamp']['insert_time'] = date('Y-m-d H:i:s');
			$this->request->data['MstThanksStamp']['update_time'] = date('Y-m-d H:i:s');
			$this->MstThanksStamp->create();
			if ($this->MstThanksStamp->save($this->request->data)) {

				# 「MstThanksStampRelation」はトリガーによって自動的に発行される
				$stamp_id = $this->MstThanksStamp->getLastInsertID();

				# リレーションが正常に登録されているかチェック
				$bar = $this->MstThanksStampRelation->find('count', array(
					'conditions' => array(
						'MstThanksStampRelation.stamp_id' => $stamp_id
					)
				));
				if ($bar == 0) {
					$this->MstThanksStampRelation->create();
					$this->MstThanksStampRelation->save(array(
						'stamp_id' => $stamp_id,
						'all_flg' => 1
					));
				}
				if ($this->request->data['MstThanksStamp']['all_flg'] == 0) {
					# 「共有しない」とした場合、「MstThanksStampRelation」もそのように更新する
					$sql = 'UPDATE `mst_thanks_stamp_relation` AS `MstThanksStampRelation` SET `MstThanksStampRelation`.`all_flg`=0 WHERE `MstThanksStampRelation`.`stamp_id`='.$stamp_id;
					$this->MstThanksStampRelation->query($sql);
				}

				$this->Session->setFlash('スタンプの登録が完了しました。', 'default', array('class' => 'callout callout-info'));
				$this->redirect(array('action' => 'index', $this->request->data['MstThanksStamp']['category_id']));
			} else {
				$this->Session->setFlash('スタンプの登録に失敗しました。', 'default', array('class' => 'callout callout-danger'));
			}
		} else {
			$this->request->data['MstThanksStamp']['category_id'] = $category_id;
		}
		$this->set('categories', $this->MstThanksStampCategory->find('list', array(
			'conditions' => array(
				'MstThanksStampCategory.status' => 0
			),
			'order' => array('MstThanksStampCategory.sort' => 'asc')
		)));
		$this->set('status', $this->MstThanksStamp->readStatus());
		$this->set('allFlgs', $this->MstThanksStamp->readAddFlgs());
	}

	public function edit($stamp_id = null) {
		if ($this->request->is(array('post', 'put'))) {
			$val = $this->request->data['MstThanksStamp']['image_url'];
			if (is_uploaded_file($val['tmp_name'])) {
				$bar = explode('.', $val['name']);
				$ext = array_pop($bar);
				$file_name = 'stamp/'.md5(uniqid(rand(),true)).'.'.$ext;
				$path = '/home/thanks/www/api-thanks.me/';
				if (move_uploaded_file($val['tmp_name'], $path.$file_name))	{
					chmod($path.$file_name, 0644);
					$this->request->data['MstThanksStamp']['image_url'] = $file_name;
				}
			} else {
				unset($this->request->data['MstThanksStamp']['image_url']);
			}
			$this->request->data['MstThanksStamp']['update_time'] = date('Y-m-d H:i:s');
			if ($this->MstThanksStamp->save($this->request->data)) {
				$this->Session->setFlash('スタンプの登録が完了しました。', 'default', array('class' => 'callout callout-info'));
				$this->redirect(array('action' => 'index', $this->request->data['MstThanksStamp']['category_id']));
			} else {
				$this->Session->setFlash('スタンプの登録に失敗しました。', 'default', array('class' => 'callout callout-danger'));
			}
		} else {
			$params = array(
				'conditions' => array(
					'MstThanksStamp.stamp_id' => $stamp_id,
					'MstThanksStamp.status' => array(0, 1)
				)
			);
			if ($data = $this->MstThanksStamp->find('first', $params)) {
				$this->request->data = $data;
			} else {
				$this->Session->setFlash('スタンプの呼び出しに失敗しました。', 'default', array('class' => 'callout callout-danger'));
				$this->redirect(array('action' => 'index'));
			}
		}
		$this->set('categories', $this->MstThanksStampCategory->find('list', array(
			'conditions' => array(
				'MstThanksStampCategory.status' => 0
			),
			'order' => array('MstThanksStampCategory.sort' => 'asc')
		)));
		$this->set('status', $this->MstThanksStamp->readStatus());
	}

	public function delete($stamp_id = null) {
		$params = array(
			'conditions' => array(
				'MstThanksStamp.stamp_id' => $stamp_id,
				'MstThanksStamp.status' => 0
			)
		);
		if ($data = $this->MstThanksStamp->find('first', $params)) {
			$params = array(
				'stamp_id' => $stamp_id,
				'status' => 1,
				'update_time' => date('Y-m-d H:i:s')
			);
			if ($this->MstThanksStamp->save($params)) {
				$this->Session->setFlash('スタンプの無効化が完了しました。', 'default', array('class' => 'callout callout-info'));
			} else {
				$this->Session->setFlash('スタンプの無効化に失敗しました。', 'default', array('class' => 'callout callout-danger'));
			}
		}
		$this->redirect(array('action' => 'index'));
	}

	public function revival($stamp_id = null) {
		$params = array(
			'conditions' => array(
				'MstThanksStamp.stamp_id' => $stamp_id,
				'MstThanksStamp.status' => 1
			)
		);
		if ($data = $this->MstThanksStamp->find('first', $params)) {
			$params = array(
				'stamp_id' => $stamp_id,
				'status' => 0,
				'update_time' => date('Y-m-d H:i:s')
			);
			if ($this->MstThanksStamp->save($params)) {
				$this->Session->setFlash('スタンプの有効化が完了しました。', 'default', array('class' => 'callout callout-info'));
			} else {
				$this->Session->setFlash('スタンプの有効化に失敗しました。', 'default', array('class' => 'callout callout-danger'));
			}
		}
		$this->redirect(array('action' => 'index'));
	}

	public function sort($category_id = null) {
		if ($this->request->is(array('post', 'put'))) {
			$bar = explode(',', $this->request->data['MstThanksStamp']['result']);
			if (!empty($bar)) {
				$sql = 'UPDATE `mst_thanks_stamp` AS `MstThanksStamp` SET `MstThanksStamp`.`sort`=0 WHERE `MstThanksStamp`.`category_id`='.$category_id;
				$this->MstThanksStamp->query($sql);

				$n = 0;
				foreach ($bar as $id) {
					$n++;
					$sql = 'UPDATE `mst_thanks_stamp` AS `MstThanksStamp` SET `MstThanksStamp`.`sort`='.$n.'  WHERE `MstThanksStamp`.`stamp_id`='.$id;
					$this->MstThanksStamp->query($sql);
				}
				$this->Session->setFlash('スタンプの並び替えが完了しました。', 'default', array('class' => 'callout callout-info'));
				$this->redirect(array('action' => 'index', $category_id));
			}
		}
		$categories = $this->MstThanksStampCategory->find('first', array(
			'conditions' => array(
				'MstThanksStampCategory.category_id' => $category_id
			)
		));
		$this->set('data', $this->MstThanksStamp->find('all', array(
			'conditions' => array(
				'MstThanksStamp.category_id' => $category_id,
				'MstThanksStamp.status' => 0
			),
			'order' => array(
				'MstThanksStamp.status',
				'MstThanksStamp.sort'
			)
		)));
		$this->title = 'スタンプ管理：【'.$categories['MstThanksStampCategory']['category_name'].'】スタンプの並び替え';
	}

	public function view($stamp_id = null) {
		$this->MstThanksStamp->recursive = 0;
		$data = $this->MstThanksStamp->find('first', array(
			'conditions' => array(
				'MstThanksStamp.stamp_id' => $stamp_id
			)
		));
		if ($data) {
			$data['MstAdminUser'] = array();
			$data2 = $this->MstThanksStampRelation->find('all', array(
				'conditions' => array(
					'MstThanksStampRelation.stamp_id' => $stamp_id,
					'MstThanksStampRelation.all_flg' => 0
				)
			));
			if ($data2) {
				foreach ($data2 as $res) {
					if ($res['MstThanksStampRelation']['agency_id'] != null) {
						$key = $res['MstThanksStampRelation']['agency_id'];
						$type = '代理店';
					}
					elseif ($res['MstThanksStampRelation']['chain_id'] != null) {
						$key = $res['MstThanksStampRelation']['chain_id'];
						$type = '加盟店';
					}
					else {
						$key = $res['MstThanksStampRelation']['shop_id'];
						$type = '店舗';
					}
					$bar = $this->MstAdminUser->find('first', array(
						'conditions' => array(
							'MstAdminUser.id' => $key
						)
					));
					if ($bar) {
						$bar['MstAdminUser']['type'] = $type;
						$data['MstAdminUser'][] = $bar['MstAdminUser'];
					}
				}
			}
			$this->log($data);
			$this->set('data', $data);
		} else {
			$this->redirect('/stamp');
		}
		$this->title = 'スタンプ管理：詳細';
	}

	public function categories_add() {
		if ($this->request->is(array('post', 'put'))) {

			# ソート番号を取り出す
			$bar = $this->MstThanksStampCategory->find('first', array(
				'fields' => array(
					'max(MstThanksStampCategory.sort) as max_val'
				)
			));
			$this->request->data['MstThanksStampCategory']['sort'] = !empty($bar[0]['max_val']) ? $bar[0]['max_val'] + 1 : 1;

			$this->MstThanksStampCategory->create();
			if ($this->MstThanksStampCategory->save($this->request->data)) {
				$this->Session->setFlash('スタンプカテゴリの登録が完了しました。', 'default', array('class' => 'callout callout-info'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('スタンプカテゴリの登録に失敗しました。', 'default', array('class' => 'callout callout-danger'));
			}
		}
		$this->title = 'スタンプ管理：カテゴリの追加';
	}

	public function categories_edit($category_id = null) {
		if ($this->request->is(array('post', 'put'))) {
			if ($this->MstThanksStampCategory->save($this->request->data)) {
				$this->Session->setFlash('スタンプカテゴリの編集が完了しました。', 'default', array('class' => 'callout callout-info'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('スタンプカテゴリの編集に失敗しました。', 'default', array('class' => 'callout callout-danger'));
			}
		} else {
			$params = array(
				'conditions' => array(
					'MstThanksStampCategory.category_id' => $category_id
				)
			);
			if ($data = $this->MstThanksStampCategory->find('first', $params)) {
				$this->request->data = $data;
			} else {
				$this->Session->setFlash('スタンプカテゴリの呼び出しに失敗しました。', 'default', array('class' => 'callout callout-danger'));
				$this->redirect(array('action' => 'index'));
			}
		}
		$this->title = 'スタンプ管理：カテゴリの編集';
	}

	public function categories_delete($stamp_id = null) {
		$params = array(
			'conditions' => array(
				'MstThanksStamp.stamp_id' => $stamp_id,
				'MstThanksStamp.status' => 0
			)
		);
		if ($data = $this->MstThanksStamp->find('first', $params)) {
			$params = array(
				'stamp_id' => $stamp_id,
				'status' => 1,
				'update_time' => date('Y-m-d H:i:s')
			);
			if ($this->MstThanksStamp->save($params)) {
				$this->Session->setFlash('スタンプの無効化が完了しました。', 'default', array('class' => 'callout callout-info'));
			} else {
				$this->Session->setFlash('スタンプの無効化に失敗しました。', 'default', array('class' => 'callout callout-danger'));
			}
		}
		$this->redirect(array('action' => 'index'));
	}

	public function relationAdd($mode = 'agency', $id = null) {
		$conditions = array(
			'MstThanksStamp.all_flg' => 0,
			'MstThanksStamp.status' => 0
		);
		switch ($mode) {
			case 'agency':
				$params = array(
					'conditions' => array(
						'MstThanksStampRelation.agency_id' => $id
					)
				);
				if ($bar = $this->MstThanksStampRelation->find('list', $params)) {
					$conditions += array('NOT' => array('MstThanksStamp.stamp_id' => $bar));
				}
				break;
			case 'company':
				$params = array(
					'conditions' => array(
						'MstThanksStampRelation.chain_id' => $id
					)
				);
				if ($bar = $this->MstThanksStampRelation->find('list', $params)) {
					$conditions += array('NOT' => array('MstThanksStamp.stamp_id' => $bar));
				}
				break;
			case 'shop':
				$params = array(
					'conditions' => array(
						'MstThanksStampRelation.shop_id' => $id
					)
				);
				if ($bar = $this->MstThanksStampRelation->find('list', $params)) {
					$conditions += array('NOT' => array('MstThanksStamp.stamp_id' => $bar));
				}
				break;
		}
		$this->set('data', $this->MstThanksStamp->find('all', array(
			'conditions' => $conditions,
			'order' => array('MstThanksStamp.category_id' => 'asc', 'MstThanksStamp.sort' => 'asc')
		)));
		$this->set('mode', $mode);
		$this->set('id', $id);

		$bar = $this->MstAdminUser->find('first', array(
			'conditions' => array(
				'MstAdminUser.id' => $id
			)
		));
		$this->title = 'スタンプ管理：【'.$bar['MstAdminUser']['aname'].'】の固有スタンプ登録';
	}

	public function relationAddOn($mode = 'agency', $id = null, $stamp_id = null) {
		$this->request->data['MstThanksStampRelation'] = array();
		$params = array(
			'conditions' => array(
				'MstThanksStampRelation.stamp_id' => $stamp_id,
				'MstThanksStampRelation.agency_id' => null,
				'MstThanksStampRelation.chain_id' => null,
				'MstThanksStampRelation.shop_id' => null,
				'MstThanksStampRelation.member_id' => null,
				'MstThanksStampRelation.all_flg' => 0
			)
		);
		if ($bar = $this->MstThanksStampRelation->find('first', $params)) {
			$this->request->data['MstThanksStampRelation']['id'] = $bar['MstThanksStampRelation']['id'];
		} else {
			$this->MstThanksStampRelation->create();
			$this->request->data['MstThanksStampRelation']['stamp_id'] = $stamp_id;
			$this->request->data['MstThanksStampRelation']['all_flg'] = 0;
		}
		switch ($mode) {
			case 'agency':
				$this->request->data['MstThanksStampRelation']['agency_id'] = $id;
				$return = 'agencyView';
				break;
			case 'company':
				$this->request->data['MstThanksStampRelation']['chain_id'] = $id;
				$return = 'companyView';
				break;
			case 'shop':
				$this->request->data['MstThanksStampRelation']['shop_id'] = $id;
				$return = 'shopView';
				break;
		}
		if ($this->MstThanksStampRelation->save($this->request->data)) {
			$this->Session->setFlash('固有スタンプの登録が完了しました。', 'default', array('class' => 'callout callout-info'));
			$this->redirect('/account/'.$return.'/'.$id);
		} else {
			$this->Session->setFlash('固有スタンプの登録に失敗しました。', 'default', array('class' => 'callout callout-danger'));
			$this->redirect('/stamp/relationAdd/'.$mode.'/'.$id);
		}
	}

	public function relationDelOn($mode = 'agency', $id = null, $stamp_id = null) {
		switch ($mode) {
			case 'agency':
				$params = array(
					'conditions' => array(
						'MstThanksStampRelation.stamp_id' => $stamp_id,
						'MstThanksStampRelation.agency_id' => $id,
						'MstThanksStampRelation.all_flg' => 0
					)
				);
				$return = 'agencyView';
				break;
			case 'company':
				$params = array(
					'conditions' => array(
						'MstThanksStampRelation.stamp_id' => $stamp_id,
						'MstThanksStampRelation.chain_id' => $id,
						'MstThanksStampRelation.all_flg' => 0
					)
				);
				$return = 'companyView';
				break;
			case 'shop':
				$params = array(
					'conditions' => array(
						'MstThanksStampRelation.stamp_id' => $stamp_id,
						'MstThanksStampRelation.shop_id' => $id,
						'MstThanksStampRelation.all_flg' => 0
					)
				);
				$return = 'shopView';
				break;
		}
		if ($bar = $this->MstThanksStampRelation->find('first', $params)) {
			$params = array(
				'conditions' => array(
					'MstThanksStampRelation.stamp_id' => $stamp_id,
					'MstThanksStampRelation.all_flg' => 0,
					'NOT' => array('MstThanksStampRelation.id' => $bar['MstThanksStampRelation']['id'])
				)
			);
			if ($this->MstThanksStampRelation->find('count', $params) == 0) {
				$params = array(
					'id' => $bar['MstThanksStampRelation']['id'],
					'agency_id' => null,
					'chain_id' => null,
					'shop_id' => null,
				);
				$this->MstThanksStampRelation->save($params);
			} else {
				$this->MstThanksStampRelation->delete($bar['MstThanksStampRelation']['id']);
			}
			$this->Session->setFlash('固有スタンプの削除が完了しました。', 'default', array('class' => 'callout callout-info'));
		} else {
			$this->Session->setFlash('固有スタンプの削除に失敗しました。', 'default', array('class' => 'callout callout-danger'));
		}
		$this->redirect('/account/'.$return.'/'.$id);
	}
}
