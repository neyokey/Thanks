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
class PushController extends AppController {

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
		$this->title = 'Push通知履歴';
		$this->paginate = array(
			'MstPuchReserve' => array(
				'conditions' => array(
				),
				'order' => array(
					'MstPuchReserve.reserve_time' => 'desc'
				)
			)
		);
		$this->set('data', $this->Paginator->paginate('MstPuchReserve'));
	}

	public function add() {
		if ($this->request->is(array('post', 'put'))) {
			$this->request->data['MstPuchReserve']['flg'] = 1;
			$this->request->data['MstPuchReserve']['status'] = 1;
			$this->request->data['MstPuchReserve']['insert_time'] = date('Y-m-d H:i:s');
			$this->request->data['MstPuchReserve']['update_time'] = date('Y-m-d H:i:s');
			$this->MstPuchReserve->create();
			if ($this->MstPuchReserve->save($this->request->data)) {
				$this->Session->setFlash('Push通知の予約が完了しました。', 'default', array('class' => 'callout callout-info'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('Push通知の予約に失敗しました。', 'default', array('class' => 'callout callout-danger'));
			}
		}
		$this->title = 'Push通知予約';
	}

	public function edit($id = null) {
		if ($this->request->is(array('post', 'put'))) {
			$this->request->data['MstPuchReserve']['update_time'] = date('Y-m-d H:i:s');
			if ($this->MstPuchReserve->save($this->request->data)) {
				$this->Session->setFlash('Push通知の更新が完了しました。', 'default', array('class' => 'callout callout-info'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('Push通知の更新に失敗しました。', 'default', array('class' => 'callout callout-danger'));
			}
		} else {
			$params = array(
				'conditions' => array(
					'MstPuchReserve.id' => $id,
					'MstPuchReserve.status' => 1
				)
			);
			if ($data = $this->MstPuchReserve->find('first', $params)) {
				$data['MstPuchReserve']['reserve_time'] = date('Y-m-d H:i', strtotime($data['MstPuchReserve']['reserve_time']));
				$this->request->data = $data;
			} else {
				$this->Session->setFlash('Push通知の更新に失敗しました。', 'default', array('class' => 'callout callout-danger'));
				$this->redirect(array('action' => 'index'));
			}
		}
		$this->title = 'Push通知編集';
	}

	public function stop($id = null) {
		$params = array(
			'conditions' => array(
				'MstPuchReserve.id' => $id,
				'MstPuchReserve.flg' => 1,
				'MstPuchReserve.status' => 1
			)
		);
		if ($data = $this->MstPuchReserve->find('first', $params)) {
			$sql = 'UPDATE  `mst_puch_reserve` AS `MstPuchReserve` SET `MstPuchReserve`.`flg` = 0 WHERE `MstPuchReserve`.`id`='.$id;
			$this->MstPuchReserve->query($sql);
			$this->Session->setFlash('Push通知の緊急停止が完了しました。', 'default', array('class' => 'callout callout-info'));
			$this->redirect(array('action' => 'index'));
		} else {
			$this->Session->setFlash('Push通知の緊急停止に失敗しました。', 'default', array('class' => 'callout callout-danger'));
		}
		$this->redirect(array('action' => 'index'));
	}

	public function restart($id = null) {
		$params = array(
			'conditions' => array(
				'MstPuchReserve.id' => $id,
				'MstPuchReserve.flg' => 0,
				'MstPuchReserve.status' => 1
			)
		);
		if ($data = $this->MstPuchReserve->find('first', $params)) {
			$sql = 'UPDATE  `mst_puch_reserve` AS `MstPuchReserve` SET `MstPuchReserve`.`flg` = 1 WHERE `MstPuchReserve`.`id`='.$id;
			$this->MstPuchReserve->query($sql);
			$this->Session->setFlash('Push通知の送信予約が完了しました。', 'default', array('class' => 'callout callout-info'));
		} else {
			$this->Session->setFlash('Push通知の送信予約に失敗しました。', 'default', array('class' => 'callout callout-danger'));
		}
		$this->redirect(array('action' => 'index'));
	}
}
