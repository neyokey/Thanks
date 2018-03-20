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
class UserController extends AppController {

/**
 * This controller does not use a model
 *
 * @var array
 */
	public $uses = array();

	public function beforeFilter() {
		parent::beforeFilter();

//		$this->Auth->allow();
		$this->layout = 'user';
	}

	public function index() {
		$this->redirect(array('action' => 'remind'));
	}

	public function remind() {
		if ($this->request->is(array('post', 'put'))) {
			$params = array(
				'conditions' => array(
					'TrnMembers.email' => $this->request->data['TrnMembers']['email'],
					'TrnMembers.status' => array(0, 1, 2)
				)
			);
			if ($data = $this->TrnMembers->find('first', $params)) {
				$auth_code = md5(uniqid(rand(),true));
				$params = array(
					'member_id' => $data['TrnMembers']['member_id'],
					'auth_code' => $auth_code,
					'update_time' => date('Y-m-d H:i:s')
				);
				if ($this->TrnMembers->save($params)) {
					$autu_url = FULL_BASE_URL.'/user/forget/'.$auth_code;
					$obj = new CakeEmail('default');
					$mailRespons = $obj->template('forget')
						->viewVars(array('autu_url' => $autu_url))
						->emailFormat('text')
						->to($data['TrnMembers']['email'])
						->subject('【Thanks!】パスワード再発行の連絡')
						->send();
					$this->redirect(array('action' => 'reminded'));
				}
			} else {
				$this->Session->setFlash('入力されたメールアドレスは登録されておりません', 'default', array('class' => 'callout callout-danger'));
			}
		}
		$this->set('ContentHeaderFlg', false);
	}

	public function reminded() {
		$this->set('ContentHeaderFlg', false);
	}

	public function forget($auth_code = null) {
		if ($this->request->is(array('post', 'put'))) {
			$params = array(
				'member_id' => $this->request->data['TrnMembers']['member_id'],
				'password' => md5($this->request->data['TrnMembers']['password']),
				'auth_code' => null,
				'update_time' => date('Y-m-d H:i:s')
			);
			if ($this->TrnMembers->save($params)) {
				$this->redirect(array('action' => 'forgeted'));
			}
		}
		$params = array(
			'conditions' => array(
				'TrnMembers.auth_code' => $auth_code,
				'TrnMembers.status' => array(0, 1, 2)
			)
		);
		if ($data = $this->TrnMembers->find('first', $params)) {
			$this->request->data['TrnMembers']['member_id'] = $data['TrnMembers']['member_id'];
		} else {
			$this->Session->setFlash('アクセルされたURLはご利用いただけません', 'default', array('class' => 'callout callout-danger'));
			$this->redirect(array('action' => 'remind'));
		}
	}

	public function forgeted() {
	}

	public function point() {
		$this->set('ContentHeaderFlg', false);
	}
}
