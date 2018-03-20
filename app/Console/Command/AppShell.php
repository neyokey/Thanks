<?php
/**
 * AppShell file
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
 * @since         CakePHP(tm) v 2.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Shell', 'Console');

App::uses('Folder', 'Utility');
App::uses('File', 'Utility');

App::import('Vendor', 'push');

App::uses('ComponentCollection', 'Controller');
App::uses('PushComponent', 'Controller/Component'); 

/**
 * Application Shell
 *
 * Add your application-wide methods in the class below, your shells
 * will inherit them.
 *
 * @package       app.Console.Command
 */
class AppShell extends Shell {

	var $uses = array(
		'MstAdminUser',
		'TrnTeams',
		'TrnThanks',
		'TrnThanksCnt',
		'TrnThanksSumMember',
		'TrnThanksSumShop',
		'MstGift',
		'MstThemeRelation'
	);

	public function startup() {
		$collection = new ComponentCollection();
		$this->Push = new PushComponent($collection);
		parent::startup();
	}

	public function sumupThunks() {
		$xday = strtotime('-1 day');
//		$xday = time();
		$years = date('Y', $xday);
		$months = date('m', $xday);
		$result = array(
			0 => array(),
			1 => array()
		);
		$params = array(
			'conditions' => array(
				'years' => $years,
				'months' => $months
			)
		);
		if ($data = $this->TrnThanksSumMember->find('all', $params)) {
			foreach ($data as $res) {
				$result[0] += array($res['TrnThanksSumMember']['menber_id'] => array(
					'id' => $res['TrnThanksSumMember']['id'],
					'thanks_receives' => 0,
					'thanks_sends' => 0
				));
			}
		}
		if ($data = $this->TrnThanksSumShop->find('all', $params)) {
			foreach ($data as $res) {
				$result[1] += array($res['TrnThanksSumShop']['shop_id'] => array(
					'id' => $res['TrnThanksSumShop']['id'],
					'thanks_receives' => 0,
					'thanks_sends' => 0
				));
			}
		}

		$this->TrnThanksCnt->recursive = 0;
		$params = array(
			'fields' => array(
				'sum(TrnThanksCnt.thanks_receives) as sum_thanks_receives',
				'sum(TrnThanksCnt.thanks_sends) as sum_thanks_sends',
				'TrnMembers.member_id',
				'TrnMembers.shop_id'
			),
			'conditions' => array(
				'TrnThanksCnt.years' => $years,
				'TrnThanksCnt.months' => $months
			),
			'group' => array(
				'TrnThanksCnt.menber_id',
				'TrnThanksCnt,shop_id'
			)
		);
		if ($data = $this->TrnThanksCnt->find('all', $params)) {
			foreach ($data as $res) {
				$id = $res['TrnMembers']['member_id'];
				if ($id !== null) {
					if (isset($result[0][$id])) {
						$result[0][$id]['thanks_receives'] = $res[0]['sum_thanks_receives'];
						$result[0][$id]['thanks_sends'] = $res[0]['sum_thanks_sends'];
					} else {
						$result[0][$id] = array(
							'menber_id' => $id,
							'shop_id' => $res['TrnMembers']['shop_id'],
							'years' => $years,
							'months' => $months,
							'thanks_receives' => $res[0]['sum_thanks_receives'],
							'thanks_sends' => $res[0]['sum_thanks_sends']
						);
					}
				}

				$id = $res['TrnMembers']['shop_id'];
				if ($id !== null) {
					if (isset($result[1][$id])) {
						$result[1][$id]['thanks_receives'] += $res[0]['sum_thanks_receives'];
						$result[1][$id]['thanks_sends'] += $res[0]['sum_thanks_sends'];
					} else {
						$result[1][$id] = array(
							'shop_id' => $id,
							'years' => $years,
							'months' => $months,
							'thanks_receives' => $res[0]['sum_thanks_receives'],
							'thanks_sends' => $res[0]['sum_thanks_sends']
						);
					}
				}
			}
		}

		if ($key = count($result[0])) {
			foreach ($result[0] as $res) {
				if (!isset($res['id'])) {
					$this->TrnThanksSumMember->create();
					$this->TrnThanksSumMember->save($res);
				} else {
					$this->TrnThanksSumMember->save($res);
				}
			}
			echo $key." user recode update..\n";
		}
		if ($key = count($result[1])) {
			foreach ($result[1] as $res) {
				if (!isset($res['id'])) {
					$this->TrnThanksSumShop->create();
					$this->TrnThanksSumShop->save($res);
				} else {
					$this->TrnThanksSumShop->save($res);
				}
			}
			echo $key." shop recode update..\n";
		}
		exit;
	}


	public function sendEmail(){
		exit();

		$subject = '【Thanks!】iPhoneアプリ不具合のお知らせ';
		$body = <<<EOF
当メールはThanks!をご利用いただいているユーザー様に送りしております。

いつもThanks!をご利用いただき有難うございます。
Thanks!運営サポートチームよりご連絡いたします。

この度アプリのアップデートを行いましたが、
アップデートを行ったiPhoneをお使いの一部のお客様のアプリで、Thanks!を送る画面でエラーが発生してアプリが落ちてしまう現状が発生しております。

同様の現象が発生いたしましたら、下記手順にてアップデートを行って頂けますようお願い申し上げます。

----------------------
＜エラー解消手順＞
１）一度アプリをアンインストールしてください。
２）再度AppStoreからアプリをインストールしてください。
AppStoreのURL
https://itunes.apple.com/jp/app/id1163984912?mt=8
----------------------

お手数をおかけして大変申し訳ございませんが、よろしくお願い申し上げます。

ご不明点等がございましたら下記までお問い合わせください。
今後とも、Thanks!アプリをご愛顧賜りますようお願い申し上げます。

--
Thanks!運営チーム
39s@aruto.me

EOF;
		$from = array('39s@aruto.me' => 'Thanks! 運営チーム');

		$sql = 'select A.member_id, A.email, A.member_name from trn_members as A, trn_team as B where A.member_id=B.member_id and B.shop_id in ("21","22","115","116","119","124","125","126","127","130") and A.device_type=1 and A.status=2 group by A.email';
		$data = $this->TrnThanksCnt->query($sql);

		$data[] = array('A' => array(
			'member_id' => 68,
			'email' => 'miyagawa@ismweb.jp',
			'member_name' => '宮さん'
		));
		$data[] = array('A' => array(
			'member_id' => 86,
			'email' => 'shiratama.0010@gmail.com',
			'member_name' => 'オソノイ'
		));

		foreach ($data as $res) {
			$to = $res['A']['email'];
#			$to = 'shiratama.0010@gmail.com';

			if (!self::checkEmail($to)) {
				continue;
			}
			echo $to."\n";

			$email = new CakeEmail('default');
			$email->from($from);
			$email->to($to);
			$email->subject($subject);
			$email->send($body);

#			break;
		}

/*
		$subject = "【thanks!】TSUTAYAにて撮影したコンセプトドラマが完成しました！";
		$body = <<<EOF
thanks!をご利用の皆さま

日頃より弊社のコミュニケーションアプリthanks!をご利用くださいまして誠にありがとうございます。
この度、サービスのコンセプトを紹介するドラマムービーを作成しましたので、ご案内いたします。

スタッフの視点で「あるある」という感覚を反映した短いドラマになっておりますので、ぜひご覧ください。

動画はコチラ
https://www.youtube.com/embed/NYw5XUN0m3o

今後ともよろしくお願いします。

株式会社アルト
thanks!運営事務局

EOF;
		$sql = "select b.aname,a.member_id,a.member_name,a.email from trn_members as a, mst_admin_user as b  where a.shop_id=b.id and a.status=2 and b.status=0 and a.shop_id not in (1,33,52) order by a.shop_id,a.member_id";
		$data = $this->TrnThanksCnt->query($sql);
		$flg = 0;
		foreach ($data as $res) {
			if (!self::checkEmail($res["a"]["email"])) {
				continue;
			}
			echo $res["a"]["email"]."\n";

#			$email = new CakeEmail('default');
#			$email->from(array('39s@aruto.me' => 'Thanks! 運営チーム'));
##			$email->to('shiratama.0010@gmail.com');
##			$email->to('miyagawa@ismweb.jp');
#			$email->to($res["a"]["email"]);
#			$email->subject($subject);
#			$email->send($body);
		}
*/
		exit;
	}

	public function checkEmail($email) {
		$pattern = '/@([\w.-]++)\z/';
		return filter_var($email, FILTER_VALIDATE_EMAIL) &&
			preg_match($pattern, $email, $matches) &&
			(checkdnsrr($matches[1], 'MX') || checkdnsrr($matches[1], 'A') || checkdnsrr($matches[1], 'AAAA'));
	}


	public function createGiftCode() {
/*
		$num = 100;
		$mktime = date('Y-m-d H:i:s');
		for ($n=1; $n<=$num; $n++) {
			$data = array(
				'git_kind' => 1,
				'gift_name' => 'Amazonギフト券',
				'gift_code' => 'AAAA-1123-FF00-'.sprintf("%04d", $n),
				'gift_amount' => 500,
				'expiration_date' => '2027-09-22 00:00:00',
				'serial_number' => '5001000000051281'.sprintf("%04d", $n).'-090',
				'trade_id' => null,
				'status' => 0,
				'insert_time' => $mktime,
				'update_time' => $mktime
			);
			$this->MstGift->create();
			$this->MstGift->save($data);

#			print_r($data);
#			break;
		}
*/
	}



	# あるといぷー送信数通知
	# 毎週金曜の20時に配信
	# 金～木までの送信サンクスを集計して配信（本日分は含めない）
	public function reportArutoypoo() {
		$sqldate = date('Y-m-d H:i:s');
		$yy = date('Y');
		$mm = date('m');
		$dd = date('d');

#		$start = mktime(0, 0, 0, 5, 12, 2017);
#		$end = strtotime('+1 week', $start);

		$end = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
#		$end = mktime(23, 59, 59, date('m'), date('d'), date('Y'));
		$start = strtotime('-1 week', $end);

		$file = new File(WWW_ROOT.'files/arutoypoo.txt', true);
		if ($file) {
			$key = $file->read();
			if ($key) {
				$key = (int)$key + 1;
				if ($key > 4) $key = 1;
			} else {
				$key = 1;
			}
			$file->write($key);
			$file->close();
		} else {
			echo 'key file not open!!';
		}

		$arutoypoo_id = 1502;
		$shop_list = array(
#			1,
			10,
			21,
			22,
			115,
			116,
			119
		);
		foreach ($shop_list as $shop_id) {
			$members = array();
			$member_count = 0;

			# 店舗データ
			$data = $this->MstAdminUser->find('first', array(
				'conditions' => array(
					'MstAdminUser.id' => $shop_id,
					'MstAdminUser.acc_grant' => 3
				)
			));
			if ($data) {
				$shop_name = $data['MstAdminUser']['aname'];
			} else {
				break;
			}

			# レポート用メンバーがチームに参加しているか？
			$data = $this->TrnTeams->find('count', array(
				'conditions' => array(
					'TrnTeams.member_id' => $arutoypoo_id,
					'TrnTeams.shop_id' => $shop_id,
					'TrnTeams.del_flg' => 0
				)
			));
			if ($data == 0) {
				# もし参加していなければ、ここで登録
				$sql = 'insert into trn_team set member_id='.$arutoypoo_id.',shop_id='.$shop_id.',active_flg=0,del_flg=0,insert_time="'.$sqldate.'",update_time="'.$sqldate.'"';
				$this->TrnTeams->query($sql);
			}
			# メンバーデータ
			$this->TrnTeams->recursive = 0;
			$this->TrnTeams->unbindModel(array('belongsTo' => array('MstAdminUser')));
			$data = $this->TrnTeams->find('all', array(
				'fields' => array(
					'TrnMembers.member_id',
					'TrnMembers.member_name',
					'TrnMembers.email',
					'TrnMembers.device_type',
					'TrnMembers.device_id',
					'TrnMembers.thanks_notice_flg'
				),
				'conditions' => array(
					'TrnTeams.shop_id' => $shop_id,
					'TrnTeams.del_flg' => 0,
					'NOT' => array('TrnMembers.member_id' => $arutoypoo_id),
					'TrnMembers.status' => 2,
#					'TrnMembers.email' => 'shiratama.0010@gmail.com'
				)
			));
			if ($data) {
				foreach ($data as $res) {
					$members += array($res['TrnMembers']['member_id'] => array(
						'name' => $res['TrnMembers']['member_name'],
						'email' => $res['TrnMembers']['email'],
						'device_type' => $res['TrnMembers']['device_type'],
						'device_id' => $res['TrnMembers']['device_id'],
						'thanks_notice_flg' => $res['TrnMembers']['thanks_notice_flg'],
						'target' => 0,
						'target_user' => 0,
						'from' => 0,
						'from_user' => 0
					));
				}
			}

			# 獲得数
			$data = $this->TrnThanks->find('all', array(
				'fields' => array(
					'TrnThanks.target_id',
					'count(*) as num1',
					'count(distinct TrnThanks.from_id) as num2'
				),
				'conditions' => array(
					'TrnThanks.shop_id' => $shop_id,
					'TrnThanks.send_time >=' => date('Y-m-d H:i:s', $start),
					'TrnThanks.send_time <=' => date('Y-m-d H:i:s', $end)
				),
				'group' => array('TrnThanks.target_id')
			));
			if ($data) {
				foreach ($data as $res) {
					$member_id = $res['TrnThanks']['target_id'];
					if (isset($members[$member_id])) {
						$members[$member_id]['target'] = $res[0]['num1'];
						$members[$member_id]['target_user'] = $res[0]['num2'];
					}
				}
			}

			# 送信数
			$data = $this->TrnThanks->find('all', array(
				'fields' => array(
					'TrnThanks.from_id',
					'count(*) as num1',
					'count(distinct TrnThanks.target_id) as num2'
				),
				'conditions' => array(
					'TrnThanks.shop_id' => $shop_id,
					'TrnThanks.send_time >=' => date('Y-m-d H:i:s', $start),
					'TrnThanks.send_time <=' => date('Y-m-d H:i:s', $end)
				),
				'group' => array('TrnThanks.from_id')
			));
			if ($data) {
				foreach ($data as $res) {
					$member_id = $res['TrnThanks']['from_id'];
					if (isset($members[$member_id])) {
						$members[$member_id]['from'] = $res[0]['num1'];
						$members[$member_id]['from_user'] = $res[0]['num2'];
					}
				}
			}

			# 送信処理のループを開始
			foreach ($members as $member_id => $res) {
				if ($res['from'] > 0) {
					# 今週、thanks!を送信している
					switch ($key) {
						case 1:
							$stamp_id = 258;
							$thanks_msg = <<<EOF
{$res["name"]}さん、いつもお仕事お疲れ様です！☆
今週は{$res["from_user"]}人にthanks!を贈りましたね！
お仕事頑張りながらチームに気配りもできる{$res["name"]}さんにthanks!
EOF;
							break;

						case 2:
							$stamp_id = 273;
							$thanks_msg = <<<EOF
今週{$res["from_user"]}人が{$res["name"]}さんのthanks!を受け取りました！！
これからもどんどん周りにthanks!を贈ってくださいね☆
EOF;
							break;

						case 3:
							$stamp_id = 154;
							$thanks_msg = <<<EOF
★週刊thanks!★
{$res["from_user"]}名のメンバーが{$res["name"]}さんから thanks! を獲得しました。{$res["name"]}さんに thanks!
〜 ありがとうでチームは変わる！ 〜
EOF;
							break;

						case 4:
							$stamp_id = 249;
							$thanks_msg = <<<EOF
{$res["name"]}さん、いつもお仕事お疲れ様です！☆
{$res["name"]}さんの{$res["from"]}件のthanks!が{$shop_name}のチームワークを高めました！！
EOF;
							break;
						
					}
				} else {
					# 今週、thanks!を送信していない
					switch ($key) {
						case 1:
							$stamp_id = 270;
							$thanks_msg = <<<EOF
{$res["name"]}さん、お疲れ様です！お会いできてとても嬉しいです。{$res["name"]}さんに thanks!
またお会いできることを楽しみにしてます。
☆★「ありがとう！」から始めよう★☆
EOF;
							break;

						case 2:
							$stamp_id = 285;
							$thanks_msg = <<<EOF
{$res["name"]}さん、お疲れさまです！今週も{$shop_name}の皆さんにより、沢山の thanks! が贈られました。
{$res["name"]}さんに thanks!
〜 ありがとうでチームは変わる！ 〜
EOF;
							break;

						case 3:
							$stamp_id = 235;
							$thanks_msg = <<<EOF
いつもお仕事頑張っている{$res["name"]}さんへ、お疲れthanks!
{$res["name"]}さんもチームのみんなにもthanks!してみませんか？
☆★「ありがとう！」から始めよう★☆
EOF;
							break;

						case 4:
							$stamp_id = 232;
							$thanks_msg = <<<EOF
{$res["name"]}さん、いつもお仕事お疲れ様です！☆
いつもお仕事頑張っている仲間にも、thanks!を贈ってみましょう！☆
〜 ありがとうでチームは変わる！ 〜
EOF;
							break;
						
					}
				}

				# thanks!送信
				$this->TrnThanks->create();
				$this->TrnThanks->save(array(
					'shop_id' => $shop_id,
					'stamp_id' => $stamp_id,
					'thanks_msg' => $thanks_msg,
					'target_id' => $member_id,
					'from_id' => $arutoypoo_id,
					'send_time' => $sqldate,
					'status' => 0
				));

				# 送信件数の更新（受信側）
				$data = $this->TrnThanksCnt->find('first', array(
					'conditions' => array(
						'TrnThanksCnt.shop_id' => $shop_id,
						'TrnThanksCnt.menber_id' => $member_id,
						'TrnThanksCnt.years' => $yy,
						'TrnThanksCnt.months' => $mm,
						'TrnThanksCnt.days' => $dd
					)
				));
				if ($data) {
					$this->TrnThanksCnt->save(array(
						'id' => $data['TrnThanksCnt']['id'],
						'thanks_receives' => $data['TrnThanksCnt']['thanks_receives'] + 1
					));
				} else {
					$this->TrnThanksCnt->create();
					$this->TrnThanksCnt->save(array(
						'shop_id' => $shop_id,
						'menber_id' => $member_id,
						'years' => $yy,
						'months' => $mm,
						'days' => $dd,
						'thanks_receives' => 1,
						'thanks_sends' => 0
					));
				}

				# Push通知
				if ($res['thanks_notice_flg'] == 0) {
					$this->Push->send_push('あるといぷーが、今週のあなたのthanks!数をお知らせします！', $res['device_id'], $res['device_type']);
				}

				# この店舗での送信数をカウント
				$member_count++;
			}

			# 送信件数の更新（送信側）
			if ($member_count > 0) {
				$data = $this->TrnThanksCnt->find('first', array(
					'conditions' => array(
						'TrnThanksCnt.shop_id' => $shop_id,
						'TrnThanksCnt.menber_id' => $arutoypoo_id,
						'TrnThanksCnt.years' => $yy,
						'TrnThanksCnt.months' => $mm,
						'TrnThanksCnt.days' => $dd
					)
				));
				if ($data) {
					$this->TrnThanksCnt->save(array(
						'id' => $data['TrnThanksCnt']['id'],
						'thanks_sends' => $data['TrnThanksCnt']['thanks_sends'] + $member_count
					));
				} else {
					$this->TrnThanksCnt->create();
					$this->TrnThanksCnt->save(array(
						'shop_id' => $shop_id,
						'menber_id' => $arutoypoo_id,
						'years' => $yy,
						'months' => $mm,
						'days' => $dd,
						'thanks_receives' => 0,
						'thanks_sends' => $member_count
					));
				}
			}
		}
	}

	public function reportArutoypooPut() {
		$arutoypoo_id = 1502;
#		$date = '2018-01-05';
		$date = date('Y-m-d', strtotime('last Friday'));

		$this->TrnThanks->recursive = 0;
		$this->TrnThanks->bindModel(array('belongsTo' => array(
			'TrnMembers' => array(
				'className' => 'TrnMembers',
				'foreignKey' => false,
				'conditions' => array(
					'TrnMembers.member_id = TrnThanks.target_id',
				),
				'fields' => '',
				'order' => ''
			),
			'MstAdminUser' => array(
				'className' => 'MstAdminUser',
				'foreignKey' => false,
				'conditions' => array(
					'MstAdminUser.id = TrnThanks.shop_id',
				),
				'fields' => '',
				'order' => ''
			)
		)));
		$data = $this->TrnThanks->find('all', array(
			'fields' => array(
				'TrnThanks.thanks_id',
				'TrnThanks.stamp_id',
				'TrnThanks.thanks_msg',
				'TrnThanks.send_time',
				'TrnThanks.status',
				'TrnThanks.read_time',
				'MstAdminUser.aname',
				'TrnMembers.member_id',
				'TrnMembers.member_name'
			),
			'conditions' => array(
				'TrnThanks.from_id' => $arutoypoo_id,
				'TrnThanks.send_time >=' => $date.' 00:00:00',
				'TrnThanks.send_time <=' => $date.' 23:59:59'
			),
			'order' => array(
				'TrnThanks.shop_id',
				'TrnThanks.target_id'
			),
#			'limit' => 20
		));
		if ($data) {
			$file = new File(WWW_ROOT.'files/arutoypoo'.$date.'.csv', true);
			if ($file) {
				$fields = array(
					'店舗名',
					'メンバーId',
					'メンバー名',
					'送信メッセージ',
					'送信時間',
					'状態',
					'既読時間'
				);
				$file->write(self::chopArrayData($fields)."\r\n");

				foreach ($data as $res) {
					$fields = array(
						$res['MstAdminUser']['aname'],
						$res['TrnMembers']['member_id'],
						$res['TrnMembers']['member_name'],
						$res['TrnThanks']['thanks_msg'],
						$res['TrnThanks']['send_time'],
						$res['TrnThanks']['status'] == 1 ? '既読' : '未読',
						$res['TrnThanks']['read_time'] != '0000-00-00 00:00:00' ? $res['TrnThanks']['read_time'] : ''
					);
					$file->write(self::chopArrayData($fields)."\r\n");
				}
				$file->close();
			} else {
				echo 'key file not open!!';
			}
		}
		echo 'Finish!!';
	}

	private function chopArrayData($fields) {
		foreach ($fields as $key => $val) {
			$fields[$key] = '"'.mb_convert_encoding($val, 'SJIS-win', 'UTF-8').'"';
		}
		return implode(',', $fields);
	}

	public function saveMstThemeRelation() {
		$theme_id = 1;
		$shop_id = 119;

		$data = $this->MstThemeRelation->find('count', array(
			'conditions' => array(
				'MstThemeRelation.theme_id' => $theme_id,
				'MstThemeRelation.shop_id' => $shop_id
			)
		));
		if ($data == 0) {
			$params = array(
				'theme_id' => $theme_id,
				'agency_id' => null,
				'chain_id' => null,
				'shop_id' => $shop_id,
				'member_id' => null
			);
			if ($this->MstThemeRelation->save($params)) {
				echo 'ok';
			} else {
				echo 'ng';
			}
		} else {
			echo 'none';
		}
	}
}
