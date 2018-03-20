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
class AccountController extends AppController {

/**
 * This controller does not use a model
 *
 * @var array
 */
	public $uses = array();

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

	public function master() {
		if ($this->Auth->user('acc_grant') > 0) {
			$this->redirect(array('controller' => 'pages', 'action' => 'home'));
		}
		$this->title = '元売管理';
		$this->paginate = array(
			'MstAdminUser' => array(
				'conditions' => array(
					'MstAdminUser.acc_grant' => 0,
					'MstAdminUser.status' => array(0, 1)
				)
			)
		);
		$this->set('data', $this->Paginator->paginate());
		$this->render('masterIndex');
	}

	public function masterView($id = null) {
		if ($this->Auth->user('acc_grant') > 0) {
			$this->redirect(array('controller' => 'pages', 'action' => 'index'));
		}
		$this->title = '元売管理：詳細';
		$params = array(
			'conditions' => array(
				'MstAdminUser.id' => $id,
				'MstAdminUser.acc_grant' => 0,
				'MstAdminUser.status' => array(0, 1)
			)
		);
		if ($data = $this->MstAdminUser->find('first', $params)) {
			$this->set(compact(
				'data'
			));
		} else {
			$this->redirect(array('action' => 'master'));
		}
	}

	public function masterAdd() {
		if ($this->Auth->user('acc_grant') > 0) {
			$this->redirect(array('controller' => 'pages', 'action' => 'index'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->MstAdminUser->checkMail($this->request->data['MstAdminUser']['amail'])) {
				$this->request->data['MstAdminUser']['acc_grant'] = 0;
				$this->request->data['MstAdminUser']['status'] = 0;
				$this->request->data['MstAdminUser']['apass'] = AuthComponent::password($this->request->data['MstAdminUser']['apass']);
				$this->request->data['MstAdminUser']['insert_time'] = date('Y-m-d H:i:s');
				$this->request->data['MstAdminUser']['update_time'] = date('Y-m-d H:i:s');
				if ($this->MstAdminUser->save($this->request->data)) {
					$this->Session->setFlash('元売アカウントの登録が完了しました。', 'default', array('class' => 'callout callout-info'));
					$id = $this->MstAdminUser->getLastInsertId();
					$this->redirect(array('action' => 'masterView', $id));
				} else {
					$this->Session->setFlash('元売アカウントの登録に失敗しました。', 'default', array('class' => 'callout callout-danger'));
				}
			} else {
				$this->Session->setFlash('登録済みのメールアドレスです。', 'default', array('class' => 'callout callout-danger'));
			}
		}
		$this->title = '元売管理：新規登録';
	}

	public function masterEdit($id = null) {
		if ($this->Auth->user('acc_grant') > 0) {
			$this->redirect(array('controller' => 'pages', 'action' => 'index'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->MstAdminUser->checkMail($this->request->data['MstAdminUser']['amail'], $id)) {
				if (!empty($this->request->data['MstAdminUser']['apass'])) {
					$this->request->data['MstAdminUser']['apass'] = AuthComponent::password($this->request->data['MstAdminUser']['apass']);
				} else {
					unset($this->request->data['MstAdminUser']['apass']);
				}
				$this->request->data['MstAdminUser']['update_time'] = date('Y-m-d H:i:s');
				if ($this->MstAdminUser->save($this->request->data)) {
					$this->Session->setFlash('元売アカウントの編集が完了しました。', 'default', array('class' => 'callout callout-info'));
					$this->redirect(array('action' => 'masterView', $id));
				} else {
					$this->Session->setFlash('元売アカウントの編集に失敗しました。', 'default', array('class' => 'callout callout-danger'));
				}
			} else {
				$this->Session->setFlash('登録済みのメールアドレスです。', 'default', array('class' => 'callout callout-danger'));
			}
		} else {
			$params = array(
				'conditions' => array(
					'MstAdminUser.id' => $id,
					'MstAdminUser.acc_grant' => 0,
					'MstAdminUser.status' => array(0, 1)
				)
			);
			if ($data = $this->MstAdminUser->find('first', $params)) {
				$data['MstAdminUser']['apass'] = null;
				$this->request->data = $data;
			} else {
				$this->redirect(array('action' => 'master'));
			}
		}
		$this->title = '元売管理：編集';
	}

	public function masterDelete($id = null) {
		if ($this->Auth->user('acc_grant') > 0) {
			$this->redirect(array('controller' => 'pages', 'action' => 'index'));
		}
		$params = array(
			'conditions' => array(
				'MstAdminUser.id' => $id,
				'MstAdminUser.acc_grant' => 0,
				'MstAdminUser.status' => array(0, 1)
			)
		);
		if ($data = $this->MstAdminUser->find('first', $params)) {
			$params = array(
				'id' => $id,
				'status' => 2,
				'update_time' => date('Y-m-d H:i:s')
			);
			if ($this->MstAdminUser->save($params)) {
				$this->Session->setFlash('元売アカウントの削除が完了しました。', 'default', array('class' => 'callout callout-info'));
			} else {
				$this->Session->setFlash('元売アカウントの削除に失敗しました。', 'default', array('class' => 'callout callout-danger'));
			}
		}
		$this->redirect(array('action' => 'master'));
	}


	public function agency() {
		switch ($this->Auth->user('acc_grant')) {
			case 0:
				break;
			case 1:
				$this->redirect(array('action' => 'agencyView', $this->Auth->user('id')));
				break;
			default:
				$this->redirect(array('controller' => 'pages', 'action' => 'index'));
				break;
		}
		$this->title = '代理店管理';
		$this->paginate = array(
			'MstAdminUser' => array(
				'conditions' => array(
					'MstAdminUser.acc_grant' => 1,
//					'MstAdminUser.status' => array(0, 1)
				)
			)
		);
		$this->set('data', $this->Paginator->paginate());
		$this->render('agencyIndex');
	}

	public function agencyView($id = null) {
		if ($this->Auth->user('acc_grant') > 1) {
			$this->redirect(array('controller' => 'pages', 'action' => 'index'));
		}
		$this->title = '代理店管理：詳細';
		$params = array(
			'conditions' => array(
				'MstAdminUser.id' => $id,
				'MstAdminUser.acc_grant' => 1,
//				'MstAdminUser.status' => array(0, 1)
			)
		);
		if ($data = $this->MstAdminUser->find('first', $params)) {
			$this->set(compact(
				'data'
			));

			if ($this->Auth->user('acc_grant') == 0) {
				# 固有スタンプ設定
				$stamps = array();
				$params = array(
					'conditions' => array(
						'MstThanksStampRelation.agency_id' => $id
					)
				);
				if ($bar = $this->MstThanksStampRelation->find('list', $params)) {
					$stamps = $this->MstThanksStamp->find('all', array(
						'conditions' => array(
							'MstThanksStamp.stamp_id' => $bar,
							'MstThanksStamp.all_flg' => 0
						),
						'order' => array('MstThanksStamp.category_id' => 'asc', 'MstThanksStamp.sort' => 'asc')
					));
				}
				$this->set('stamps', $stamps);
			}
		} else {
			$this->redirect(array('action' => 'agency'));
		}
	}

	public function agencyAdd() {
		if ($this->Auth->user('acc_grant') > 0) {
			$this->redirect(array('controller' => 'pages', 'action' => 'index'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->MstAdminUser->checkMail($this->request->data['MstAdminUser']['amail'])) {
				$this->request->data['MstAdminUser']['acc_grant'] = 1;
				$this->request->data['MstAdminUser']['status'] = 0;
				$this->request->data['MstAdminUser']['apass'] = AuthComponent::password($this->request->data['MstAdminUser']['apass']);
				$this->request->data['MstAdminUser']['insert_time'] = date('Y-m-d H:i:s');
				$this->request->data['MstAdminUser']['update_time'] = date('Y-m-d H:i:s');
				if ($this->MstAdminUser->save($this->request->data)) {
					$this->Session->setFlash('代理店アカウントの登録が完了しました。', 'default', array('class' => 'callout callout-info'));
					$id = $this->MstAdminUser->getLastInsertId();
					$this->redirect(array('action' => 'agencyView', $id));
				} else {
					$this->Session->setFlash('代理店アカウントの登録に失敗しました。', 'default', array('class' => 'callout callout-danger'));
				}
			} else {
				$this->Session->setFlash('登録済みのメールアドレスです。', 'default', array('class' => 'callout callout-danger'));
			}
		}
		$this->title = '代理店管理：新規登録';
	}

	public function agencyEdit($id = null) {
		if ($this->Auth->user('acc_grant') > 0) {
			$this->redirect(array('controller' => 'pages', 'action' => 'index'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->MstAdminUser->checkMail($this->request->data['MstAdminUser']['amail'], $id)) {
				if (!empty($this->request->data['MstAdminUser']['apass'])) {
					$this->request->data['MstAdminUser']['apass'] = AuthComponent::password($this->request->data['MstAdminUser']['apass']);
				} else {
					unset($this->request->data['MstAdminUser']['apass']);
				}
				$this->request->data['MstAdminUser']['update_time'] = date('Y-m-d H:i:s');
				if ($this->MstAdminUser->save($this->request->data)) {
					$this->Session->setFlash('代理店アカウントの編集が完了しました。', 'default', array('class' => 'callout callout-info'));
					$this->redirect(array('action' => 'agencyView', $id));
				} else {
					$this->Session->setFlash('代理店アカウントの編集に失敗しました。', 'default', array('class' => 'callout callout-danger'));
				}
			} else {
				$this->Session->setFlash('登録済みのメールアドレスです。', 'default', array('class' => 'callout callout-danger'));
			}
		} else {
			$params = array(
				'conditions' => array(
					'MstAdminUser.id' => $id,
					'MstAdminUser.acc_grant' => 1,
//					'MstAdminUser.status' => array(0, 1)
				)
			);
			if ($data = $this->MstAdminUser->find('first', $params)) {
				$data['MstAdminUser']['apass'] = null;
				$this->request->data = $data;
			} else {
				$this->redirect(array('action' => 'agency'));
			}
		}
		$this->title = '代理店管理：編集';
	}

	public function agencyDelete($id = null) {
		if ($this->Auth->user('acc_grant') > 0) {
			$this->redirect(array('controller' => 'pages', 'action' => 'index'));
		}
		$params = array(
			'conditions' => array(
				'MstAdminUser.id' => $id,
				'MstAdminUser.acc_grant' => 1,
				'MstAdminUser.status' => array(0, 1)
			)
		);
		if ($data = $this->MstAdminUser->find('first', $params)) {
			$params = array(
				'id' => $id,
				'status' => 2,
				'update_time' => date('Y-m-d H:i:s')
			);
			if ($this->MstAdminUser->save($params)) {
				$this->Session->setFlash('代理店アカウントの削除が完了しました。', 'default', array('class' => 'callout callout-info'));
			} else {
				$this->Session->setFlash('代理店アカウントの削除に失敗しました。', 'default', array('class' => 'callout callout-danger'));
			}
		}
		$this->redirect(array('action' => 'agency'));
	}

	public function agencyStop($id = null) {
		$params = array(
			'conditions' => array(
				'MstAdminUser.id' => $id,
				'MstAdminUser.acc_grant' => 1,
				'MstAdminUser.status' => 0
			)
		);
		if ($data = $this->MstAdminUser->find('first', $params)) {
			$params = array(
				'id' => $id,
				'status' => 1,
				'update_time' => date('Y-m-d H:i:s')
			);
			if ($this->MstAdminUser->save($params)) {
				# 企業／チームアカウントの一時停止処理
				$params = array(
					'conditions' => array(
						'MstAdminUser.acc_grant' => array(2, 3),
						'MstAdminUser.agency_id' => $id,
						'MstAdminUser.status' => 0
					)
				);
				if ($list = $this->MstAdminUser->find('list', $params)) {
					foreach ($list as $a => $b) {
						$this->MstAdminUser->save(array(
							'id' => $a,
							'status' => 1,
							'update_time' => date('Y-m-d H:i:s')
						));
					}
				}
				$this->Session->setFlash('代理店アカウントの一時停止処理が完了しました。', 'default', array('class' => 'callout callout-info'));
			} else {
				$this->Session->setFlash('代理店アカウントの一時停止処理に失敗しました。', 'default', array('class' => 'callout callout-danger'));
			}
		}
		$this->redirect(array('action' => 'agencyView', $id));
	}

	public function agencyRestart($id = null) {
		$params = array(
			'conditions' => array(
				'MstAdminUser.id' => $id,
				'MstAdminUser.acc_grant' => 1,
				'MstAdminUser.status' => 1
			)
		);
		if ($data = $this->MstAdminUser->find('first', $params)) {
			$params = array(
				'id' => $id,
				'status' => 0,
				'update_time' => date('Y-m-d H:i:s')
			);
			if ($this->MstAdminUser->save($params)) {
				$this->Session->setFlash('代理店アカウントの再稼働が完了しました。', 'default', array('class' => 'callout callout-info'));
			} else {
				$this->Session->setFlash('代理店アカウントの再稼働に失敗しました。', 'default', array('class' => 'callout callout-danger'));
			}
		}
		$this->redirect(array('action' => 'agencyView', $id));
	}

	public function agencyClose($id = null) {
		$this->MstAdminUser->recursive = 0;
		$params = array(
			'conditions' => array(
				'MstAdminUser.id' => $id,
				'MstAdminUser.acc_grant' => 1,
				'MstAdminUser.status' => array(0, 1)
			)
		);
		if ($data = $this->MstAdminUser->find('first', $params)) {
			$data['MstAdminUser']['apass'] = null;
			$this->set('data', $data);
		} else {
			$this->redirect(array('action' => 'agency'));
		}
		if ($this->request->is(array('post', 'put'))) {
			$this->request->data['MstAdminUser']['status'] = 2;
			$this->request->data['MstAdminUser']['update_time'] = date('Y-m-d H:i:s');
			if ($this->MstAdminUser->save($this->request->data)) {
				# 企業／チームアカウントの一時停止処理
				$params = array(
					'conditions' => array(
						'MstAdminUser.acc_grant' => array(2, 3),
						'MstAdminUser.agency_id' => $id,
						'MstAdminUser.status' => array(0, 1)
					)
				);
				if ($list = $this->MstAdminUser->find('list', $params)) {
					$cancellation_date = $this->request->data['MstAdminUser']['cancellation_date'];
					foreach ($list as $a => $b) {
						$this->MstAdminUser->save(array(
							'id' => $a,
							'cancellation_date' => $cancellation_date,
							'status' => 2,
							'update_time' => date('Y-m-d H:i:s')
						));
					}
				}
				$this->Session->setFlash('代理店アカウントの解約処理が完了しました。', 'default', array('class' => 'callout callout-info'));
				$this->redirect(array('action' => 'agency'));
			} else {
				$this->Session->setFlash('代理店アカウントの解約処理に失敗しました。', 'default', array('class' => 'callout callout-danger'));
			}
		} else {
			$this->request->data = $data;
		}
		$this->title = '代理店管理：解約処理';
	}


	public function company($agency_id = null) {
		switch ($this->Auth->user('acc_grant')) {
			case 0:
				$conditions = array(
					'MstAdminUser.acc_grant' => 2,
//					'MstAdminUser.status' => array(0, 1)
				);
				if ($agency_id !== null) {
					$conditions += array('MstAdminUser.agency_id' => $agency_id);
				}
				break;
			case 1:
				$conditions = array(
					'MstAdminUser.acc_grant' => 2,
//					'MstAdminUser.status' => array(0, 1),
					'MstAdminUser.agency_id' => $this->Auth->user('id')
				);
				break;
			case 2:
				$this->redirect(array('action' => 'companyView', $this->Auth->user('id')));
				break;
			default:
				$this->redirect(array('controller' => 'pages', 'action' => 'index'));
				break;
		}
		$this->title = '企業管理';
		$this->paginate = array(
			'MstAdminUser' => array(
				'conditions' => $conditions
			)
		);
		$this->set('data', $this->Paginator->paginate());
		$this->render('companyIndex');
	}

	public function companyView($id = null) {
		if ($this->Auth->user('acc_grant') > 2) {
			$this->redirect(array('controller' => 'pages', 'action' => 'index'));
		}
		$this->title = '企業管理：詳細';
		$this->MstAdminUser->recursive = 0;
		$params = array(
			'conditions' => array(
				'MstAdminUser.id' => $id,
				'MstAdminUser.acc_grant' => 2,
//				'MstAdminUser.status' => array(0, 1)
			)
		);
		if ($data = $this->MstAdminUser->find('first', $params)) {
			$this->set(compact(
				'data'
			));

			if ($this->Auth->user('acc_grant') == 0) {
				# 固有スタンプ設定
				$stamps = array();
				$params = array(
					'conditions' => array(
						'MstThanksStampRelation.chain_id' => $id
					)
				);
				if ($bar = $this->MstThanksStampRelation->find('list', $params)) {
					$stamps = $this->MstThanksStamp->find('all', array(
						'conditions' => array(
							'MstThanksStamp.stamp_id' => $bar,
							'MstThanksStamp.all_flg' => 0
						),
						'order' => array('MstThanksStamp.category_id' => 'asc', 'MstThanksStamp.sort' => 'asc')
					));
				}
				$this->set('stamps', $stamps);
			}
		} else {
			$this->redirect(array('action' => 'company'));
		}
	}

	public function companyAdd() {
		switch ($this->Auth->user('acc_grant')) {
			case 0:
				$conditions = array(
					'MstAdminUser.acc_grant' => 1,
					'MstAdminUser.status' => array(0, 1)
				);
				break;
			case 1:
				$conditions = array(
					'MstAdminUser.acc_grant' => 1,
					'MstAdminUser.status' => array(0, 1),
					'MstAdminUser.agency_id' => $this->Auth->user('id')
				);
				break;
			default:
				$this->redirect(array('controller' => 'pages', 'action' => 'index'));
				break;
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->MstAdminUser->checkMail($this->request->data['MstAdminUser']['amail'])) {
				$this->request->data['MstAdminUser']['acc_grant'] = 2;
				$this->request->data['MstAdminUser']['status'] = 0;
				$this->request->data['MstAdminUser']['apass'] = AuthComponent::password($this->request->data['MstAdminUser']['apass']);
				$this->request->data['MstAdminUser']['payment_type'] = implode(',', $this->request->data['MstAdminUser']['payment_type']);
				$this->request->data['MstAdminUser']['insert_time'] = date('Y-m-d H:i:s');
				$this->request->data['MstAdminUser']['update_time'] = date('Y-m-d H:i:s');
				if ($this->MstAdminUser->save($this->request->data)) {
					$this->Session->setFlash('企業アカウントの登録が完了しました。', 'default', array('class' => 'callout callout-info'));
					$id = $this->MstAdminUser->getLastInsertId();
					$this->redirect(array('action' => 'companyView', $id));
				} else {
					$this->Session->setFlash('企業アカウントの登録に失敗しました。', 'default', array('class' => 'callout callout-danger'));
				}
			} else {
				$this->Session->setFlash('登録済みのメールアドレスです。', 'default', array('class' => 'callout callout-danger'));
			}
		}
		$this->title = '企業管理：新規登録';
		$this->set('agencies', $this->MstAdminUser->find('list', array('conditions' => $conditions)));
		$this->set('paymentTypes', $this->MstAdminUser->readPaymentTypes());
	}

	public function companyEdit($id = null) {
		switch ($this->Auth->user('acc_grant')) {
			case 0:
				$conditions = array(
					'MstAdminUser.acc_grant' => 1,
//					'MstAdminUser.status' => array(0, 1)
				);
				break;
			case 1:
				$conditions = array(
					'MstAdminUser.acc_grant' => 1,
//					'MstAdminUser.status' => array(0, 1),
					'MstAdminUser.agency_id' => $this->Auth->user('id')
				);
				break;
			default:
				$this->redirect(array('controller' => 'pages', 'action' => 'index'));
				break;
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->MstAdminUser->checkMail($this->request->data['MstAdminUser']['amail'], $id)) {
				if (!empty($this->request->data['MstAdminUser']['apass'])) {
					$this->request->data['MstAdminUser']['apass'] = AuthComponent::password($this->request->data['MstAdminUser']['apass']);
				} else {
					unset($this->request->data['MstAdminUser']['apass']);
				}
				$this->request->data['MstAdminUser']['payment_type'] = implode(',', $this->request->data['MstAdminUser']['payment_type']);
				$this->request->data['MstAdminUser']['update_time'] = date('Y-m-d H:i:s');
				if ($this->MstAdminUser->save($this->request->data)) {
					$this->Session->setFlash('企業アカウントの編集が完了しました。', 'default', array('class' => 'callout callout-info'));
					$this->redirect(array('action' => 'companyView', $id));
				} else {
					$this->Session->setFlash('企業アカウントの編集に失敗しました。', 'default', array('class' => 'callout callout-danger'));
				}
			} else {
				$this->Session->setFlash('登録済みのメールアドレスです。', 'default', array('class' => 'callout callout-danger'));
			}
		} else {
			$params = array(
				'conditions' => array(
					'MstAdminUser.id' => $id,
					'MstAdminUser.acc_grant' => 2,
//					'MstAdminUser.status' => array(0, 1)
				)
			);
			if ($data = $this->MstAdminUser->find('first', $params)) {
				$data['MstAdminUser']['apass'] = null;
				$this->request->data = $data;
			} else {
				$this->redirect(array('action' => 'company'));
			}
		}
		$this->title = '企業管理：編集';
		$this->set('agencies', $this->MstAdminUser->find('list', array('conditions' => $conditions)));
		$this->set('paymentTypes', $this->MstAdminUser->readPaymentTypes());
	}

	public function companyDelete($id = null) {
		if ($this->Auth->user('acc_grant') > 1) {
			$this->redirect(array('controller' => 'pages', 'action' => 'index'));
		}
		$params = array(
			'conditions' => array(
				'MstAdminUser.id' => $id,
				'MstAdminUser.acc_grant' => 2,
				'MstAdminUser.status' => array(0, 1)
			)
		);
		if ($data = $this->MstAdminUser->find('first', $params)) {
			$params = array(
				'id' => $id,
				'status' => 2,
				'update_time' => date('Y-m-d H:i:s')
			);
			if ($this->MstAdminUser->save($params)) {
				$this->Session->setFlash('企業アカウントの削除が完了しました。', 'default', array('class' => 'callout callout-info'));
			} else {
				$this->Session->setFlash('企業アカウントの削除に失敗しました。', 'default', array('class' => 'callout callout-danger'));
			}
		}
		$this->redirect(array('action' => 'company'));
	}

	public function companyStop($id = null) {
		$params = array(
			'conditions' => array(
				'MstAdminUser.id' => $id,
				'MstAdminUser.acc_grant' => 2,
				'MstAdminUser.status' => 0
			)
		);
		if ($data = $this->MstAdminUser->find('first', $params)) {
			$params = array(
				'id' => $id,
				'status' => 1,
				'update_time' => date('Y-m-d H:i:s')
			);
			if ($this->MstAdminUser->save($params)) {
				# チームアカウントの一時停止処理
				$params = array(
					'conditions' => array(
						'MstAdminUser.acc_grant' => 3,
						'MstAdminUser.chain_id' => $id,
						'MstAdminUser.status' => 0
					)
				);
				if ($list = $this->MstAdminUser->find('list', $params)) {
					foreach ($list as $a => $b) {
						$this->MstAdminUser->save(array(
							'id' => $a,
							'status' => 1,
							'update_time' => date('Y-m-d H:i:s')
						));
					}
				}
				$this->Session->setFlash('企業アカウントの一時停止処理が完了しました。', 'default', array('class' => 'callout callout-info'));
			} else {
				$this->Session->setFlash('企業アカウントの一時停止処理に失敗しました。', 'default', array('class' => 'callout callout-danger'));
			}
		}
		$this->redirect(array('action' => 'companyView', $id));
	}

	public function companyRestart($id = null) {
		$params = array(
			'conditions' => array(
				'MstAdminUser.id' => $id,
				'MstAdminUser.acc_grant' => 2,
				'MstAdminUser.status' => 1
			)
		);
		if ($data = $this->MstAdminUser->find('first', $params)) {
			# 上位の代理店が利用可能かチェック
			$params = array(
				'conditions' => array(
					'MstAdminUser.id' => $data['MstAdminUser']['agency_id'],
					'MstAdminUser.acc_grant' => 1,
					'MstAdminUser.status' => 0
				)
			);
			if ($this->MstAdminUser->find('count', $params) == 0) {
				$this->Session->setFlash('上位代理店アカウントが有効でないため再稼働に失敗しました。', 'default', array('class' => 'callout callout-danger'));
			} else {
				$params = array(
					'id' => $id,
					'status' => 0,
					'update_time' => date('Y-m-d H:i:s')
				);
				if ($this->MstAdminUser->save($params)) {
					$this->Session->setFlash('企業アカウントの再稼働が完了しました。', 'default', array('class' => 'callout callout-info'));
				} else {
					$this->Session->setFlash('企業アカウントの再稼働に失敗しました。', 'default', array('class' => 'callout callout-danger'));
				}
			}
		}
		$this->redirect(array('action' => 'companyView', $id));
	}

	public function companyClose($id = null) {
		$this->MstAdminUser->recursive = 0;
		$params = array(
			'conditions' => array(
				'MstAdminUser.id' => $id,
				'MstAdminUser.acc_grant' => 2,
				'MstAdminUser.status' => array(0, 1)
			)
		);
		if ($data = $this->MstAdminUser->find('first', $params)) {
			$data['MstAdminUser']['apass'] = null;
			$this->set('data', $data);
		} else {
			$this->redirect(array('action' => 'company'));
		}
		if ($this->request->is(array('post', 'put'))) {
			$this->request->data['MstAdminUser']['status'] = 2;
			$this->request->data['MstAdminUser']['update_time'] = date('Y-m-d H:i:s');
			if ($this->MstAdminUser->save($this->request->data)) {
				# チームアカウントの一時停止処理
				$params = array(
					'conditions' => array(
						'MstAdminUser.acc_grant' => 3,
						'MstAdminUser.chain_id' => $id,
						'MstAdminUser.status' => array(0, 1)
					)
				);
				if ($list = $this->MstAdminUser->find('list', $params)) {
					$cancellation_date = $this->request->data['MstAdminUser']['cancellation_date'];
					foreach ($list as $a => $b) {
						$this->MstAdminUser->save(array(
							'id' => $a,
							'cancellation_date' => $cancellation_date,
							'status' => 2,
							'update_time' => date('Y-m-d H:i:s')
						));
					}
				}
				$this->Session->setFlash('企業アカウントの解約処理が完了しました。', 'default', array('class' => 'callout callout-info'));
				$this->redirect(array('action' => 'company'));
			} else {
				$this->Session->setFlash('企業アカウントの解約処理に失敗しました。', 'default', array('class' => 'callout callout-danger'));
			}
		} else {
			$this->request->data = $data;
		}
		$this->title = '企業管理：解約処理';
	}

	public function companyBill($id = null, $year = null) {
		if ($this->Auth->user('acc_grant') > 2) {
			$this->redirect(array('controller' => 'pages', 'action' => 'index'));
		}
		if ($year === null) {
			$year = date('Y');
		}
		$dy1 = mktime(0, 0, 0, 12, 1, $year);
		$dy2 = mktime(0, 0, 0, 1, 1, $year);
		$this->set('data', $this->MstAdminUser->readBill($id, $dy1, $dy2));
		$this->set(compact(array(
			'id',
			'year'
		)));
		$this->title = '企業管理：支払金額確認';
	}

	public function shop($chain_id = null) {
		switch ($this->Auth->user('acc_grant')) {
			case 0:	//元売
				$conditions = array(
					'MstAdminUser.acc_grant' => 3,
					'MstAdminUser.status' => array(0, 1, 2)
				);
				if ($chain_id !== null) {
					$conditions += array('MstAdminUser.chain_id' => $chain_id);
				}
				break;
			case 1:	//代理店
				$conditions = array(
					'MstAdminUser.acc_grant' => 3,
					'MstAdminUser.status' => array(0, 1, 2),
					'MstAdminUser.agency_id' => $this->Auth->user('id')
				);
				if ($chain_id !== null) {
					$conditions += array('MstAdminUser.chain_id' => $chain_id);
				}
				break;
			case 2:	//企業
				$conditions = array(
					'MstAdminUser.acc_grant' => 3,
					'MstAdminUser.status' => array(0, 1, 2),
					'MstAdminUser.chain_id' => $this->Auth->user('id')
				);
				break;
			case 3:	//チーム
				$this->redirect(array('action' => 'shopView', $this->Auth->user('id')));
				break;
		}

		if (isset($this->request->data['MstAdminUser'])) {
			$this->Session->write('ShopSearch', $this->request->data['MstAdminUser']);
		} elseif ($this->Session->check('ShopSearch')) {
			$this->request->data['MstAdminUser'] = $this->Session->read('ShopSearch');
		}
		if (isset($this->request->data['MstAdminUser'])) {
			if ($this->request->data['MstAdminUser']['status'] !== '') {
				$conditions['MstAdminUser.status'] = $this->request->data['MstAdminUser']['status'];
			}
			if ($this->request->data['MstAdminUser']['trial_flg'] !== '') {
				$conditions['MstAdminUser.trial_flg'] = $this->request->data['MstAdminUser']['trial_flg'];
			}
			if (!empty($this->request->data['MstAdminUser']['id'])) {
				$conditions['MstAdminUser.id'] = $this->request->data['MstAdminUser']['id'];
			}
			if (!empty($this->request->data['MstAdminUser']['aname'])) {
				$conditions['MstAdminUser.aname LIKE'] = '%'.$this->request->data['MstAdminUser']['aname'].'%';
			}
		}

		$this->title = 'チーム管理';
		$this->paginate = array(
			'MstAdminUser' => array(
				'conditions' => $conditions
			)
		);
		$this->set('data', $this->Paginator->paginate());
		$this->set('status', $this->MstAdminUser->readStatus());
		$this->set('trialFlgs', $this->MstAdminUser->readTrialFlgs(1));
		$this->render('shopIndex');
	}

	public function shopView($id = null) {
		$this->title = 'チーム管理：詳細';
		$this->MstAdminUser->recursive = 0;
		$params = array(
			'conditions' => array(
				'MstAdminUser.id' => $id,
				'MstAdminUser.acc_grant' => 3,
				'MstAdminUser.status' => array(0, 1, 2)
			)
		);
		switch ($this->Auth->user('acc_grant')) {
			case 0:	//元売
				break;
			case 1:	//代理店
				$params['conditions']['MstAdminUser.agency_id'] = $this->Auth->user('id');
				break;
			case 2:	//企業
				$params['conditions']['MstAdminUser.chain_id'] = $this->Auth->user('id');
				break;
			case 3:	//チーム
				if ($this->Auth->user('id') != $id) {
					$this->redirect(array('action' => 'shopView', $this->Auth->user('id')));
				}
				break;
		}
		if ($data = $this->MstAdminUser->find('first', $params)) {
			$this->set(compact(
				'data'
			));
			if (version_compare(Configure::read('RELEASE_VERSION'), '3.1.1', '>=')) {
				$this->set('pointItems', $this->MstPointItemRelation->findRelationData($id));
			}

			if ($this->Auth->user('acc_grant') == 0) {
				# 固有スタンプ設定
				$stamps = array();
				$params = array(
					'conditions' => array(
						'MstThanksStampRelation.shop_id' => $id
					)
				);
				if ($data2 = $this->MstThanksStampRelation->find('list', $params)) {
					$stamps = $this->MstThanksStamp->find('all', array(
						'conditions' => array(
							'MstThanksStamp.stamp_id' => $data2,
							'MstThanksStamp.all_flg' => 0
						),
						'order' => array('MstThanksStamp.category_id' => 'asc', 'MstThanksStamp.sort' => 'asc')
					));
				}
				$this->set('stamps', $stamps);

				# BOT設定
				$bots = array();
				$params = array(
					'conditions' => array(
						'MstDiaryBotRelation.shop_id' => $id,
						'MstDiaryBotRelation.status' => 0
					),
					'group' => array('MstDiaryBotRelation.bot_id','MstDiaryBotRelation.id')
				);
				if ($data2 = $this->MstDiaryBotRelation->find('all', $params)) {
					foreach ($data2 as $res) {
						$bar = $this->TrnMembers->find('first', array(
							'conditions' => array(
								'TrnMembers.member_id' => $res['MstDiaryBotRelation']['bot_member_id'],
								'TrnMembers.status' => 9
							)
						));
						if ($bar) {
							$res['TrnMembers'] = $bar['TrnMembers'];
							$bots[] = $res;
						}
					}
				}
				$this->set('bots', $bots);
			}
		} else {
			$this->redirect(array('action' => 'shop'));
		}
	}

	public function shopAdd() {
		switch ($this->Auth->user('acc_grant')) {
			case 0:	#元売
				$conditions = array(
					'MstAdminUser.acc_grant' => 2,
					'MstAdminUser.status' => array(0, 1)
				);
				break;
			case 1:	#代理店
				$conditions = array(
					'MstAdminUser.acc_grant' => 2,
					'MstAdminUser.status' => array(0, 1),
					'MstAdminUser.agency_id' => $this->Auth->user('id')
				);
				break;
			default:
				$this->redirect(array('action' => 'shop'));
				break;
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->MstAdminUser->checkMail($this->request->data['MstAdminUser']['amail'])) {
				# 代理店idの取り出し
				$bar = $this->MstAdminUser->find('first', array('conditions' => array(
					'MstAdminUser.id' => $this->request->data['MstAdminUser']['chain_id']
				)));
				$this->request->data['MstAdminUser']['agency_id'] = $bar['MstAdminUser']['agency_id'];

				# 初期データを登録
				$this->request->data['MstAdminUser']['acc_grant'] = 3;
				$this->request->data['MstAdminUser']['status'] = 0;
				$this->request->data['MstAdminUser']['trial_flg'] = isset($this->request->data['MstAdminUser']['trial_flg']) ? 1 : 0;
				$this->request->data['MstAdminUser']['apass'] = AuthComponent::password($this->request->data['MstAdminUser']['apass']);
				$this->request->data['MstAdminUser']['insert_time'] = date('Y-m-d H:i:s');
				$this->request->data['MstAdminUser']['update_time'] = date('Y-m-d H:i:s');

				if ($this->MstAdminUser->save($this->request->data)) {
					$id = $this->MstAdminUser->getLastInsertId();

					# 交換ポイント交換商品のリレーション管理
					if (isset($this->request->data['MstPointItemRelation'])) {
						foreach ($this->request->data['MstPointItemRelation'] as $item_id => $res) {
							if ($res['value'] == '1') {
								# 有効にする
								if (!isset($res['id'])) {
									$this->MstPointItemRelation->create();
									$this->MstPointItemRelation->save(array(
										'item_id' => $item_id,
										'shop_id' => $id
									));
								}
							}
						}
					}
					$this->Session->setFlash('チームアカウントの登録が完了しました。', 'default', array('class' => 'callout callout-info'));
					$this->redirect(array('action' => 'shopView', $id));
				} else {
					$this->Session->setFlash('チームアカウントの登録に失敗しました。', 'default', array('class' => 'callout callout-danger'));
				}
			} else {
				$this->Session->setFlash('登録済みのメールアドレスです。', 'default', array('class' => 'callout callout-danger'));
			}
		} else {
			$this->request->data['MstAdminUser']['shop_AuthCode'] = mb_substr(md5(uniqid(rand(),true)), 0, 8);
			if (version_compare(Configure::read('RELEASE_VERSION'), '3.1.1', '>=')) {
				$this->request->data['MstAdminUser']['point_exchange_limit'] = 5000;
				$this->request->data['MstAdminUser']['point_thanks_send'] = 10;
				$this->request->data['MstAdminUser']['point_thanks_receive'] = 10;
			}
		}
		$this->title = 'チーム管理：新規登録';
		$this->set('companies', $this->MstAdminUser->find('list', array('conditions' => $conditions)));
		$this->set('trialFlgs', $this->MstAdminUser->readTrialFlgs());

		if (version_compare(Configure::read('RELEASE_VERSION'), '3.1.1', '>=')) {
			$this->set('pointStatus', $this->MstAdminUser->readPointStatus());
			$this->set('pointChargeStatus', $this->MstAdminUser->readPointChargeStatus());
			$this->set('pointExchangeStatus', $this->MstAdminUser->readPointExchangeStatus());
			$this->set('pointItems', $this->MstPointItemRelation->findRelationData());
		}
	}

	public function shopEdit($id = null) {
		switch ($this->Auth->user('acc_grant')) {
			case 0:	#元売
				$conditions = array(
					'MstAdminUser.acc_grant' => 2,
					'MstAdminUser.status' => array(0, 1, 2)
				);
				break;
			case 1:	#代理店
				$conditions = array(
					'MstAdminUser.acc_grant' => 2,
					'MstAdminUser.status' => array(0, 1, 2),
					'MstAdminUser.agency_id' => $this->Auth->user('id')
				);
				break;
			case 2:	#企業
				$conditions = array(
					'MstAdminUser.acc_grant' => 2,
					'MstAdminUser.status' => array(0, 1, 2),
					'MstAdminUser.id' => $this->Auth->user('id')
				);
				break;
			case 3:	#チーム
				if ($this->Auth->user('id') != $id) {
					$this->redirect(array('action' => 'shop'));
				}
				break;
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->MstAdminUser->checkMail($this->request->data['MstAdminUser']['amail'], $id)) {
				# 代理店idの取り出し
				$bar = $this->MstAdminUser->find('first', array('conditions' => array(
					'MstAdminUser.id' => $this->request->data['MstAdminUser']['chain_id']
				)));
				$this->request->data['MstAdminUser']['agency_id'] = $bar['MstAdminUser']['agency_id'];

				# パスワードの暗号化(入力されていた場合のみ)
				if (!empty($this->request->data['MstAdminUser']['apass'])) {
					$this->request->data['MstAdminUser']['apass'] = AuthComponent::password($this->request->data['MstAdminUser']['apass']);
				} else {
					unset($this->request->data['MstAdminUser']['apass']);
				}

				# 最終更新日時の更新
				$this->request->data['MstAdminUser']['update_time'] = date('Y-m-d H:i:s');

				# データの保存
				if ($this->MstAdminUser->save($this->request->data)) {

					# 交換ポイント交換商品のリレーション管理
					if (isset($this->request->data['MstPointItemRelation'])) {
						foreach ($this->request->data['MstPointItemRelation'] as $item_id => $res) {
							if ($res['value'] == '1') {
								# 有効にする
								if (!isset($res['id'])) {
									$this->MstPointItemRelation->create();
									$this->MstPointItemRelation->save(array(
										'item_id' => $item_id,
										'shop_id' => $id
									));
								}
							} else {
								# 無効にする
								if (isset($res['id'])) {
									$this->MstPointItemRelation->delete($res['id']);
								}
							}
						}
					}

					$this->Session->setFlash('チームアカウントの編集が完了しました。', 'default', array('class' => 'callout callout-info'));
					$this->redirect(array('action' => 'shopView', $id));
				} else {
					$this->Session->setFlash('チームアカウントの編集に失敗しました。', 'default', array('class' => 'callout callout-danger'));
				}
			} else {
				$this->Session->setFlash('登録済みのメールアドレスです。', 'default', array('class' => 'callout callout-danger'));
			}
		} else {
			$params = array(
				'conditions' => array(
					'MstAdminUser.id' => $id,
					'MstAdminUser.acc_grant' => 3,
					'MstAdminUser.status' => array(0, 1, 2)
				)
			);
			if ($data = $this->MstAdminUser->find('first', $params)) {
				$data['MstAdminUser']['apass'] = null;
				$this->request->data = $data;
			} else {
				$this->redirect(array('action' => 'shop'));
			}

			if (version_compare(Configure::read('RELEASE_VERSION'), '3.1.1', '>=')) {
				$pointItems = $this->MstPointItemRelation->findRelationData($id);
				foreach ($pointItems as $item_id => $res) {
					if (isset($res['MstPointItemRelation']['id'])) {
						$this->request->data['MstPointItemRelation'][$item_id]['value'] = 1;
						$this->request->data['MstPointItemRelation'][$item_id]['id'] = $res['MstPointItemRelation']['id'];
					}
				}
			}
		}
		$this->title = 'チーム管理：編集';
		$this->set('companies', $this->MstAdminUser->find('list', array('conditions' => $conditions)));
		$this->set('trialFlgs', $this->MstAdminUser->readTrialFlgs());

		if (version_compare(Configure::read('RELEASE_VERSION'), '3.1.1', '>=')) {
			$this->set('pointStatus', $this->MstAdminUser->readPointStatus());
			$this->set('pointChargeStatus', $this->MstAdminUser->readPointChargeStatus());
			$this->set('pointExchangeStatus', $this->MstAdminUser->readPointExchangeStatus());
			if (isset($pointItems)) {
				$this->set('pointItems', $pointItems);
			} else {
				$this->set('pointItems', $this->MstPointItemRelation->findRelationData());
			}
		}
	}

	public function shopDelete($id = null) {
		$params = array(
			'conditions' => array(
				'MstAdminUser.id' => $id,
				'MstAdminUser.acc_grant' => 3,
				'MstAdminUser.status' => array(0, 1)
			)
		);
		if ($data = $this->MstAdminUser->find('first', $params)) {
			$params = array(
				'id' => $id,
				'status' => 2,
				'update_time' => date('Y-m-d H:i:s')
			);
			if ($this->MstAdminUser->save($params)) {
				$this->Session->setFlash('チームアカウントの削除が完了しました。', 'default', array('class' => 'callout callout-info'));
			} else {
				$this->Session->setFlash('チームアカウントの削除に失敗しました。', 'default', array('class' => 'callout callout-danger'));
			}
		}
		$this->redirect(array('action' => 'shop'));
	}

	public function shopStop($id = null) {
		$params = array(
			'conditions' => array(
				'MstAdminUser.id' => $id,
				'MstAdminUser.acc_grant' => 3,
				'MstAdminUser.status' => 0
			)
		);
		if ($data = $this->MstAdminUser->find('first', $params)) {
			$params = array(
				'id' => $id,
				'status' => 1,
				'update_time' => date('Y-m-d H:i:s')
			);
			if ($this->MstAdminUser->save($params)) {
				$this->Session->setFlash('チームアカウントの一時停止処理が完了しました。', 'default', array('class' => 'callout callout-info'));
			} else {
				$this->Session->setFlash('チームアカウントの一時停止処理に失敗しました。', 'default', array('class' => 'callout callout-danger'));
			}
		}
		$this->redirect(array('action' => 'shopView', $id));
	}

	public function shopRestart($id = null) {
		$params = array(
			'conditions' => array(
				'MstAdminUser.id' => $id,
				'MstAdminUser.acc_grant' => 3,
				'MstAdminUser.status' => 1
			)
		);
		if ($data = $this->MstAdminUser->find('first', $params)) {
			# 上位の企業が利用可能かチェック
			$params = array(
				'conditions' => array(
					'MstAdminUser.id' => $data['MstAdminUser']['chain_id'],
					'MstAdminUser.acc_grant' => 2,
					'MstAdminUser.status' => 0
				)
			);
			if ($this->MstAdminUser->find('count', $params) == 0) {
				$this->Session->setFlash('上位企業アカウントが有効でないため再稼働に失敗しました。', 'default', array('class' => 'callout callout-danger'));
			} else {
				$params = array(
					'id' => $id,
					'status' => 0,
					'update_time' => date('Y-m-d H:i:s')
				);
				if ($this->MstAdminUser->save($params)) {
					$this->Session->setFlash('チームアカウントの再稼働が完了しました。', 'default', array('class' => 'callout callout-info'));
				} else {
					$this->Session->setFlash('チームアカウントの再稼働に失敗しました。', 'default', array('class' => 'callout callout-danger'));
				}
			}
		}
		$this->redirect(array('action' => 'shopView', $id));
	}

	public function shopClose($id = null) {
		$this->MstAdminUser->recursive = 0;
		$params = array(
			'conditions' => array(
				'MstAdminUser.id' => $id,
				'MstAdminUser.acc_grant' => 3,
				'MstAdminUser.status' => array(0, 1)
			)
		);
		if ($data = $this->MstAdminUser->find('first', $params)) {
			$data['MstAdminUser']['apass'] = null;
			$this->set('data', $data);
		} else {
			$this->redirect(array('action' => 'shop'));
		}
		if ($this->request->is(array('post', 'put'))) {
			$this->request->data['MstAdminUser']['status'] = 2;
			$this->request->data['MstAdminUser']['update_time'] = date('Y-m-d H:i:s');
			if ($this->MstAdminUser->save($this->request->data)) {
				$this->Session->setFlash('チームアカウントの解約処理が完了しました。', 'default', array('class' => 'callout callout-info'));
				$this->redirect(array('action' => 'shop'));
			} else {
				$this->Session->setFlash('チームアカウントの解約処理に失敗しました。', 'default', array('class' => 'callout callout-danger'));
			}
		} else {
			$this->request->data = $data;
		}
		$this->title = 'チーム管理：解約処理';
	}

	public function shopUpload() {
		if ($this->request->is(array('post', 'put'))) {
			# チームid
			$chain_id = $this->request->data['MstAdminUser']['chain_id'];

			# 代理店id
			$bar = $this->MstAdminUser->find('first', array('conditions' => array('MstAdminUser.id' => $chain_id)));
			$agency_id = $bar['MstAdminUser']['agency_id'];

			$records = array();
			$i = 0;
			$filepath = $this->request->data['MstAdminUser']['csv_file']['tmp_name'];
			if (($fp = fopen($filepath, "r"))) {
				$data = array();
				$Err = '';
				setlocale(LC_ALL, 'ja_JP');
				while (($line = fgetcsv($fp)) !== FALSE) {
					mb_convert_variables('UTF-8', 'sjis-win', $line);
					$i++;
					if ($i == 1) {
						continue;
					}
					foreach ($line as $a => $b) {
						$line[$a] = trim($b);
					}
					$data[] = $line;
					if (!$this->MstAdminUser->checkMail($line[1])) {
						$Err .= '・'.$i.'行目のメールアドレスは登録済みです。<br />';
					}
				}
				fclose($fp);

				if ($Err == '') {
					foreach ($data as $line) {
						$line[6] = !empty($line[6]) ? date('Y-m-d', strtotime($line[6])) : date('Y-m-d');

						$this->request->data['MstAdminUser'] = array(
							'acc_grant' => 3,
							'apass' => AuthComponent::password($line[2]),
							'aname' => $line[0],
							'amail' => $line[1],
							'atel' => $line[3],
							'azip' => $line[4],
							'aaddress' => $line[5],
							'agency_id' => $agency_id,
							'chain_id' => $chain_id,
							'shop_AuthCode' => mb_substr(md5(uniqid(rand(),true)), 0, 8),
							'contract_date' => $line[6],
							'status' => 0,
							'trial_flg' => 0,
							'insert_time' => date('Y-m-d H:i:s'),
							'update_time' => date('Y-m-d H:i:s')
						);
						$this->MstAdminUser->create();
						$this->MstAdminUser->save($this->request->data);
					}
					$this->Session->setFlash('チームアカウントの一括登録が完了しました。', 'default', array('class' => 'callout callout-info'));
					$this->redirect(array('action' => 'shop'));
				} else {
					$this->Session->setFlash($Err, 'default', array('class' => 'callout callout-danger'));
				}
			}
		}
		$this->title = 'チーム管理：一括登録';
		$this->set('companies', $this->MstAdminUser->find('list', array('conditions' => array(
			'MstAdminUser.acc_grant' => 2,
			'MstAdminUser.status' => array(0, 1)
		))));
	}

	public function staff($shop_id = null) {
		switch ($this->Auth->user('acc_grant')) {
			case 0:	//元売
				$conditions = array(
					'TrnTeams.del_flg' => 0,
					'TrnMembers.status' => array(2, 9)
				);
				if ($shop_id !== null) {
					$conditions += array('TrnTeams.shop_id' => $shop_id);
#					$conditions += array('TrnMembers.shop_id' => $shop_id);
				}
				break;
			case 1:	//代理店
				if ($shop_id !== null) {
					$conditions = array(
						'TrnTeams.del_flg' => 0,
						'TrnTeams.shop_id' => $shop_id,
#						'TrnMembers.shop_id' => $shop_id,
						'TrnMembers.status' => 2,
					);
				} else {
					$shop = $this->MstAdminUser->find('list', array('conditions' => array(
						'MstAdminUser.acc_grant' => 3,
						'MstAdminUser.status' => array(0, 1),
						'MstAdminUser.agency_id' => $this->Auth->user('id')
					)));
					$conditions = array(
						'TrnTeams.del_flg' => 0,
						'TrnTeams.shop_id' => array_keys($shop),
#						'TrnMembers.shop_id' => array_keys($shop),
						'TrnMembers.status' => 2
					);
				}
				break;
			case 2:	//企業
				if ($shop_id !== null) {
					$conditions = array(
						'TrnTeams.del_flg' => 0,
						'TrnTeams.shop_id' => $shop_id,
#						'TrnMembers.shop_id' => $shop_id,
						'TrnMembers.status' => 2
					);
				} else {
					$shop = $this->MstAdminUser->find('list', array('conditions' => array(
						'MstAdminUser.acc_grant' => 3,
						'MstAdminUser.status' => array(0, 1),
						'MstAdminUser.chain_id' => $this->Auth->user('id')
					)));
					$conditions = array(
						'TrnTeams.del_flg' => 0,
						'TrnTeams.shop_id' => array_keys($shop),
#						'TrnMembers.shop_id' => array_keys($shop),
						'TrnMembers.status' => 2
					);
				}
				break;
			case 3:	//チーム
				$conditions = array(
					'TrnTeams.del_flg' => 0,
					'TrnTeams.shop_id' => $this->Auth->user('id'),
#					'TrnMembers.shop_id' => $this->Auth->user('id'),
					'TrnMembers.status' => 2
				);
				break;
		}
		if (isset($this->request->data['MstAdminUser'])) {
			$this->Session->write('StaffSearch', $this->request->data['MstAdminUser']);
		} elseif ($this->Session->check('StaffSearch')) {
			$this->request->data['MstAdminUser'] = $this->Session->read('StaffSearch');
		}
		if (isset($this->request->data['MstAdminUser'])) {
			if ($this->request->data['MstAdminUser']['status'] !== '') {
				$conditions += array('MstAdminUser.status' => $this->request->data['MstAdminUser']['status']);
			}
			if ($this->request->data['MstAdminUser']['trial_flg'] !== '') {
				$conditions += array('MstAdminUser.trial_flg' => $this->request->data['MstAdminUser']['trial_flg']);
			}
			if (!empty($this->request->data['MstAdminUser']['member_id'])) {
				$conditions['TrnMembers.member_id'] = $this->request->data['MstAdminUser']['member_id'];
			}
			if (!empty($this->request->data['MstAdminUser']['member_name'])) {
				$conditions['TrnMembers.member_name LIKE'] = '%'.$this->request->data['MstAdminUser']['member_name'].'%';
			}
		}
		$conditions['NOT'] = array('TrnMembers.member_id' => 999999999, 'TrnMembers.bot_flg' => 1);

		$this->title = 'メンバー管理';
		$this->TrnTeams->recursive = 0;
#		$this->TrnMembers->recursive = 0;
		$this->paginate = array(
#			'TrnMembers' => array(
#				'conditions' => $conditions,
#				'order' => array('TrnMembers.final_login_time' => 'desc')
#			)
			'TrnTeams' => array(
				'conditions' => $conditions,
				'order' => array('TrnMembers.final_login_time' => 'desc')
			)
		);
		$this->set('data', $this->Paginator->paginate('TrnTeams'));
#		$this->set('data', $this->Paginator->paginate('TrnMembers'));
		$this->set('status', $this->MstAdminUser->readStatus());
		$this->set('trialFlgs', $this->MstAdminUser->readTrialFlgs(1));
		$this->render('staffIndex');
	}

	public function staffView($member_id = null) {
		$this->title = 'メンバー管理';
#		$this->TrnMembers->recursive = 0;
		$params = array(
			'conditions' => array(
				'TrnMembers.member_id' => $member_id,
				'TrnMembers.status' => 2
#				'TrnMembers.status' => array(2, 9)
			)
		);
		if ($data = $this->TrnMembers->find('first', $params)) {

			$conditions = array();
			$conditions['TrnTeams.member_id'] = $member_id;
			$conditions['TrnTeams.del_flg'] = 0;

			switch ($this->Auth->user('acc_grant')) {
				case 0:	//元売
					break;
				case 1:	//代理店
					$shops = $this->MstAdminUser->find('list', array('conditions' => array(
						'MstAdminUser.acc_grant' => 3,
						'MstAdminUser.status' => array(0, 1, 2),
						'MstAdminUser.agency_id' => $this->Auth->user('id')
					)));
					$conditions['TrnTeams.shop_id'] = array_keys($shops);
					break;
				case 2:	//企業
					$shops = $this->MstAdminUser->find('list', array('conditions' => array(
						'MstAdminUser.acc_grant' => 3,
						'MstAdminUser.status' => array(0, 1, 2),
						'MstAdminUser.chain_id' => $this->Auth->user('id')
					)));
					$conditions['TrnTeams.shop_id'] = array_keys($shops);
					break;
				case 3:	//チーム
					$conditions['TrnTeams.shop_id'] = $this->Auth->user('id');
					break;
			}

			# 参加しているチーム一覧を呼び出す
			$this->TrnTeams->recursive = 0;
			$this->TrnTeams->unbindModel(array('belongsTo' => array('TrnMembers')));
			$data['Shops'] = $this->TrnTeams->find('all', array('conditions' => $conditions));
			if ($data['Shops']) {
				if (version_compare(Configure::read('RELEASE_VERSION'), '3.1.1', '>=')) {
					$ids = array();
					foreach ($data['Shops'] as $key => $res) {
						# このチームでの保有ポイントを呼び出す
						$bar = $this->TrnPointTrade->find('first', array(
							'fields' => array(
								'sum(TrnPointTrade.trade_point) as sum_trade_point'
							),
							'conditions' => array(
								'TrnPointTrade.member_id' => $member_id,
								'TrnPointTrade.shop_id' => $res['TrnTeams']['shop_id'],
								'TrnPointTrade.status' => 0
							)
						));
						$data['Shops'][$key]['Point'] = $bar[0]['sum_trade_point'];
						$ids[] = $res['TrnTeams']['shop_id'];
					}

					# ポイント交換履歴を呼び出す
					$this->TrnPointExchange->recursive = 0;
					$this->TrnPointExchange->unbindModel(array('belongsTo' => array('TrnMembers')));
					$data['PointExchange'] = $this->TrnPointExchange->find('all', array(
						'fields' => array(
							'TrnPointExchange.exchange_id',
							'TrnPointExchange.exchange_point',
							'TrnPointExchange.request_datetime',
							'TrnPointExchange.exchange_result',
							'TrnPointExchange.status',
							'MstPointItem.item_id',
							'MstPointItem.item_name',
							'MstAdminUser.id',
							'MstAdminUser.aname'
						),
						'conditions' => array(
							'TrnPointExchange.member_id' => $member_id,
							'TrnPointExchange.shop_id' => $ids,
						)
					));
				}
			} else {
				$this->redirect(array('action' => 'staff'));
			}
			$this->set(compact(
				'data'
			));
		} else {
			$this->redirect(array('action' => 'staff'));
		}
	}

	public function staffDelete($member_id = null) {
		$this->title = 'メンバー管理';
		$params = array(
			'conditions' => array(
				'TrnMembers.member_id' => $member_id,
				'TrnMembers.status' => 2
			)
		);
		if ($data = $this->TrnMembers->find('first', $params)) {
			$params = array(
				'member_id' => $member_id,
				'status' => 9,
				'update_time' => date('Y-m-d H:i:s')
			);
			if ($this->TrnMembers->save($params)) {
				$this->TrnTeams->save(array(
					'member_id' => $member_id,
					'del_flg' => 1,
					'update_time' => date('Y-m-d H:i:s')
				));
				$this->Session->setFlash('メンバーアカウントの削除が完了しました。', 'default', array('class' => 'callout callout-info'));
			} else {
				$this->Session->setFlash('メンバーアカウントの削除に失敗しました。', 'default', array('class' => 'callout callout-danger'));
			}
		}
		$this->redirect(array('action' => 'staff'));
	}
}
