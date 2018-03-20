<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
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

App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {

	public $components = array(
		'DebugKit.Toolbar',
		'Session',
		'Auth' => array(
			'authenticate' => array(
				'Form' => array(
					'userModel' => 'MstAdminUser',
					'scope' => array('MstAdminUser.status' => 0),
					'fields' => array('username' => 'amail', 'password' => 'apass')
				)
			),
			'loginRedirect' => array('controller' => 'pages', 'action' => 'home'),
			'logoutRedirect' => array('controller' => 'pages', 'action' => 'login'),
			'loginAction' => array('controller' => 'pages', 'action' => 'login')
		)
	);

	public $uses = array(
		'MstAdminUser',
		'MstThanksStampCategory',
		'MstThanksStamp',
		'MstThanksStampRelation',
		'MstPuchReserve',
		'TrnMembers',
		'TrnTeams',
		'TrnThanks',
		'TrnThanksCnt',
		'TrnThanksSumMember',
		'TrnThanksSumShop',
		'MstDiaryBot',
		'MstDiaryBotContents',
		'MstDiaryBotRelation',
		'MstPointItem',
		'MstPointItemRelation',
		'TrnPointExchange',
		'TrnPointTrade',
		'MstGift',
		'TrnDiary'
	);

	public function beforeFilter() {
		Configure::write('Config.language', 'jpn');
//		$this->log($this->name);
//		$this->log($this->action);
//		$this->log($this->Auth->user());
		$this->title = 'HOME';
	}

	public function beforeRender() {
		$this->set('title', $this->title);
		$this->set('userSession', $this->Auth->user());
		if (version_compare(Configure::read('RELEASE_VERSION'), '3.1.1', '>=')) {
			$this->set('PointExchanges', $this->TrnPointExchange->readNewEntry());
		}
	}
}
