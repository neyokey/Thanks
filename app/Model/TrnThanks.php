<?php
App::uses('AppModel', 'Model');
/**
 * MstAdminUser Model
 *
 */
class TrnThanks extends AppModel {

	public $useTable = 'trn_thanks';

	public $primaryKey = 'thanks_id';
	public $displayField = 'send_time';

	public $recursive = -1;

	public function adjustmentData($data) {
		$results = array();
		$n = 0;
		$re = 0;
		$se = 0;
		$arr = array();
		foreach ($data as $member_id => $res) {
			$n++;
			$re += $res['thanks_receives'];
			$se += $res['thanks_sends'];
			$arr[] = $res['thanks_receives'] + $res['thanks_sends'];
		}
		$_re = ($re / $n);	# 平均獲得サンクス数
		$_se = ($se / $n);	# 平均送信サンクス数
		$_to = (array_sum($arr) / $n);			# 平均合計サンクス数
		$_he = self::stats_standard_deviation($arr);	# 標準偏差サンクス数
		foreach ($data as $member_id => $res) {
			$xre = $res['thanks_receives'] - $_re;
			$xse = $res['thanks_sends'] - $_se;

			if (($xre - $xse * 0.7 >= 0) && ($xre - $xse * 1.3 <= 0)) {
				$type_id = 1;
			} elseif (($xre - $xse * 0.7 < 0) && ($xre - $xse * 1.3 < 0)) {
				$type_id = 2;
			} elseif (($xre - $xse * 0.7 > 0) && ($xre - $xse * 1.3 > 0)) {
				$type_id = 3;
			} else {
				$type_id = 4;
			}

			switch ($type_id) {
				case 1: $type_name = '人気者'; break;
				case 2: $type_name = '世話焼き'; break;
				case 3: $type_name = 'クール'; break;
				case 4: $type_name = '孤立'; break;
			}

			$to = $res['thanks_receives'] + $res['thanks_sends'];
			if ($to >= $_to) {
				$turnover_type_id = 1;
			} elseif ($to >= ($_to - $_he)) {
				$turnover_type_id = 2;
			} else {
				$turnover_type_id = 3;
			}

			switch ($turnover_type_id) {
				case 1:
					$turnover_type_name = '低';
					break;
				case 2:
					$turnover_type_name = '中';
					break;
				case 3:
					$turnover_type_name = '高';
					break;
			}

			$results += array($member_id => array(
				$res['thanks_receives'],
				$res['thanks_sends'],
				$res['thanks_receives'] + $res['thanks_sends'],
				$xre,
				$xse,
				$type_id,
				$type_name,
				$turnover_type_id,
				$turnover_type_name
			));
		}
		return $results;
	}

	private function stats_standard_deviation($ary) {
		// 平均取得
		$avg = array_sum($ary)/count($ary);

		// 各値の平均値との差の二乗【(値-平均値)^2】を算出
		$diff_ary = array();
		foreach ($ary as $val) {
			$diff = $val-$avg;
			$diff_ary[] = pow($diff,2);
		}

		// 上記差の二乗の合計を算出
		$diff_total = array_sum($diff_ary);
		// 平均を算出
		$diff_avg   = $diff_total/count($diff_ary);

		// 平方根を取る(標準偏差)
		$stdev = sqrt($diff_avg);

		// 標準偏差を返す
		return $stdev;
	}

	public function label4num($num) {
		if ($num >= 3) {
			return 'bg-green';
		} elseif ($num >= 2) {
			return 'bg-blue';
		} elseif ($num >= 1) {
			return 'bg-yellow';
		} else {
			return 'bg-red';
		}
	}
}
