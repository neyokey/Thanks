<?php
App::uses('AppModel', 'Model');
/**
 * MstDiaryBotRelation Model
 *
 */
class MstDiaryBotRelation extends AppModel {

	public $useTable = 'mst_diary_bot_relation';

	public $primaryKey = 'id';
	public $displayField = 'bot_id';

	public $recursive = -1;

	public $hasOne = array(
		'MstDiaryBotContents' => array(
			'className' => 'MstDiaryBotContents',
			'foreignKey' => '',
			'conditions' => 'MstDiaryBotRelation.contents_id=MstDiaryBotContents.contents_id',
			'fields' => '',
			'order' => ''
		)
	);

	public function afterFind($results, $primary = false) {
		$type1 = self::readSendCycleMonthDate();
		$type2 = self::readSendCycleWeekDay();
		$type3 = self::readSendCycleDayOnce();
		$type4 = self::readSendMethod();
		$type5 = self::readSendLoopFlg();
		$type6 = self::readExecuteTimeH();
		foreach ($results as $key => $value) {
			if (isset($value['MstDiaryBotRelation']['send_cycle_month_date'])) {
				if ($value['MstDiaryBotRelation']['send_cycle_month_date'] != 0) {
					$results[$key]['SendCycle'] = $type1[$value['MstDiaryBotRelation']['send_cycle_month_date']];
				}
			}
			if (isset($value['MstDiaryBotRelation']['send_cycle_week_day'])) {
				if ($value['MstDiaryBotRelation']['send_cycle_week_day'] != 9) {
					$results[$key]['SendCycle'] = $type2[$value['MstDiaryBotRelation']['send_cycle_week_day']];
				}
			}
			if (isset($value['MstDiaryBotRelation']['send_cycle_day_once'])) {
				if ($value['MstDiaryBotRelation']['send_cycle_day_once'] != 0) {
					$results[$key]['SendCycle'] = $type3[$value['MstDiaryBotRelation']['send_cycle_day_once']];
				}
			}
			if (isset($value['MstDiaryBotRelation']['send_method'])) {
				$results[$key]['SendMethod'] = $type4[$value['MstDiaryBotRelation']['send_method']];
			}
			if (isset($value['MstDiaryBotRelation']['send_loop_flg'])) {
				$results[$key]['SendLoopFlg'] = $type5[$value['MstDiaryBotRelation']['send_loop_flg']];
			}
			if (isset($value['MstDiaryBotRelation']['execute_timeh'])) {
				$results[$key]['ExecuteTimeH'] = $type6[$value['MstDiaryBotRelation']['execute_timeh']];
			}
		}
		return $results;
	}

	public function readSendCycleMonthDate() {
		$results = array();
		for ($n = 1; $n <= 31; $n++) {
			$results[$n] = '毎月'.$n.'日';
		}
		return $results;
	}

	public function readSendCycleWeekDay() {
		$results = array(
			0 => '毎週日曜日',
			1 => '毎週月曜日',
			2 => '毎週火曜日',
			3 => '毎週水曜日',
			4 => '毎週木曜日',
			5 => '毎週金曜日',
			6 => '毎週土曜日'
		);
		return $results;
	}

	public function readSendCycleDayOnce() {
		$results = array();
		for ($n = 1; $n <= 14; $n++) {
			$results[$n] = $n.'日に1回';
		}
		return $results;
	}

	public function readSendMethod() {
		$results = array(
			0 => '順序',
			1 => 'ランダム'
		);
		return $results;
	}

	public function readSendLoopFlg() {
		$results = array(
			0 => 'ON',
			1 => 'OFF'
		);
		return $results;
	}

	public function readExecuteTimeH() {
		$results = array(
			'06' => '6時',
			'07' => '7時',
			'08' => '8時',
			'09' => '9時',
			'10' => '10時',
			'11' => '11時',
			'12' => '12時',
			'13' => '13時',
			'14' => '14時',
			'15' => '15時',
			'16' => '16時',
			'17' => '17時',
			'18' => '18時',
			'19' => '19時',
			'20' => '20時',
			'21' => '21時',
			'22' => '22時',
			'23' => '23時',
			'00' => '0時',
		);
		return $results;
	}
}
