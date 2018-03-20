<?php
App::uses('AppModel', 'Model');
/**
 * MstAdminUser Model
 *
 */
class TrnMembers extends AppModel {

	public $useTable = 'trn_members';

	public $primaryKey = 'member_id';
	public $displayField = 'member_name';

	public $recursive = -1;

	public $belongsTo = array(
		'MstAdminUser' => array(
			'className' => 'MstAdminUser',
			'foreignKey' => 'shop_id',
			'conditions' => array('MstAdminUser.acc_grant' => 3),
			'fields' => '',
			'order' => ''
		)
	);

	public function afterFind($results, $primary = false) {
		$deviceTypes = self::readDeviceTypes();
		$noticeFlgs = self::readNoticeFlgs();
		$status = self::readMemberStatus();
		foreach ($results as $key => $value) {
			if (isset($value['TrnMembers']['device_type'])) {
				$id = $value['TrnMembers']['device_type'];
				$results[$key]['DeviceType'] = array(
					'id' => $id,
					'name' => $deviceTypes[$id]
				);
			}
			if (isset($value['TrnMembers']['thanks_notice_flg'])) {
				$id = $value['TrnMembers']['thanks_notice_flg'];
				$results[$key]['ThanksNoticeFlg'] = array(
					'id' => $id,
					'name' => $noticeFlgs[$id]
				);
			}
			if (isset($value['TrnMembers']['birthday_notice_flg'])) {
				$id = $value['TrnMembers']['birthday_notice_flg'];
				$results[$key]['BirthdayNoticeFlg'] = array(
					'id' => $id,
					'name' => $noticeFlgs[$id]
				);
			}
			if (isset($value['TrnMembers']['status'])) {
				$id = $value['TrnMembers']['status'];
				$results[$key]['Status'] = array(
					'id' => $id,
					'name' => $status[$id]
				);
			}
		}
		return $results;
	}

	public function readDeviceTypes() {
		return array(
			1 => 'iOS',
			2 => 'Android'
		);
	}

	public function readNoticeFlgs() {
		return array(
			0 => '通知する',
			1 => '通知しない'
		);
	}
}
