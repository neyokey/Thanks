<?php
$this->Html->script('plugins/iCheck/icheck.min', array('inline' => false));
$this->Html->css('plugins/iCheck/all', array('inline' => false));
$js = <<<EOF
$(function () {
	//iCheck for checkbox and radio inputs
	$('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
		checkboxClass: 'icheckbox_flat-green',
		radioClass   : 'iradio_flat-green'
	})
});

EOF;

echo $this->Html->scriptBlock($js, array('block' => 'script'));

$InfoFlgChecked = $data['TrnPointExchange']['push_status'] == 0 || $data['TrnPointExchange']['mail_status'] == 0 ? TRUE : FALSE;
?>
<div class="row">
	<div class="col-lg-12 col-md-12">
		<div class="box box-info">
			<div class="box-body table-responsive no-padding">
				<table class="table table-hover">
					<tr>
						<th width="140px">Id</th>
						<td><?php echo $data['TrnPointExchange']['exchange_id']; ?></td>
					</tr>
					<tr>
						<th>チーム名</th>
						<td><?php echo $this->Html->Link($data['MstAdminUser']['aname'], '/account/shopView/'.$data['MstAdminUser']['id']); ?></td>
					</tr>
					<tr>
						<th>メンバー名</th>
						<td><?php echo $this->Html->Link($data['TrnMembers']['member_name'], '/account/staffView/'.$data['TrnMembers']['member_id']); ?></td>
					</tr>
					<tr>
						<th>メールアドレス</th>
						<td><?php echo $data['TrnMembers']['email']; ?></td>
					</tr>
					<tr>
						<th>ステータス</th>
						<td><?php
						$str = $data['Status'];
						if (isset($data['ExchangeResult'])) {
							$str .= '／'.$data['ExchangeResult'];
						}
						switch ($data['TrnPointExchange']['status']) {
							case 0: echo '<span class="label label-success">'.$str.'</span>'; break;
							case 1: echo '<span class="label label-warning">'.$str.'</span>'; break;
							case 2: echo '<span class="label label-primary">'.$str.'</span>'; break;
						}
						?></td>
					</tr>
					<tr>
						<th>申請pt</th>
						<td><?php echo number_format($data['TrnPointExchange']['exchange_point']).'pt&nbsp;<code>（'.($data['TrnPointExchange']['exchange_point'] / $data['MstPointItem']['item_point'] * 500).'円分）</code>'; ?></td>
					</tr>
					<tr>
						<th>申請日時</th>
						<td><?php echo $data['TrnPointExchange']['request_datetime']; ?></td>
					</tr>
					<tr>
						<th>完了日時</th>
						<td><?php echo isset($data['TrnPointExchange']['exchange_execute']) ? $data['TrnPointExchange']['exchange_execute'] : '-'; ?></td>
					</tr>
					<tr>
						<th>メッセージ</th>
						<td><?php echo isset($data['TrnPointExchange']['exchange_resultdetail']) ? nl2br($data['TrnPointExchange']['exchange_resultdetail']) : '-'; ?></td>
					</tr>
					<tr>
						<th>Push通知</th>
						<td><?php echo $data['PushStatus']; ?></td>
					</tr>
					<tr>
						<th>Mail通知</th>
						<td><?php echo $data['MailStatus']; ?></td>
					</tr>
				</table>
			</div>
		</div>
	</div>
	
	<?php
	if ($data['TrnPointExchange']['status'] == 0 || ($data['TrnPointExchange']['status'] == 2 && $data['TrnPointExchange']['exchange_result'] == 1)) {
		echo $this->Form->create('TrnPointExchange', array('url' => '/point/amazonSuccess/'.$data['TrnPointExchange']['exchange_id'], 'inputDefaults' => array('div' => 'form-group', 'required' => false)));
	?>
	<div class="col-lg-6 col-md-6">
		<div class="box box-success">
			<div class="box-header">
				<h3 class="box-title">完了処理</h3>
			</div>
			<div class="box-body">
				<p><code>こちらの申込を「完了」に更新します。</code></p>
				<?php
				echo $this->Form->input('info_flg', array('id' => 'InfoFlgSuccess', 'type' => 'checkbox', 'class' => 'flat-red', 'label' => '&nbsp;Push／Mailでメンバーに通知する', 'checked' => $InfoFlgChecked));
				?>
			</div>
			<div class="box-footer">
				<?php echo $this->Form->button('更新', array('type' => 'submit', 'class' => 'btn btn-success')); ?>
			</div>
		</div>
	</div>
	<?php
		echo $this->Form->end();
	}
	?>

	<?php
	if ($data['TrnPointExchange']['status'] == 0 || ($data['TrnPointExchange']['status'] == 2 && $data['TrnPointExchange']['exchange_result'] == 0)) {
		echo $this->Form->create('TrnPointExchange', array('url' => '/point/amazonFailed/'.$data['TrnPointExchange']['exchange_id'], 'inputDefaults' => array('div' => 'form-group', 'required' => false)));
	?>
	<div class="col-lg-6 col-md-6">
		<div class="box box-danger">
			<div class="box-header">
				<h3 class="box-title">失敗処理（キャンセル）</h3>
			</div>
			<div class="box-body">
				<p><code>こちらの申込を「失敗」に更新します。</code></p>
				<?php
				echo $this->Form->input('exchange_resultdetail', array('type' => 'text', 'class' => 'form-control', 'label' => '失敗（キャンセル）理由', 'placeholder' => '申込情報の誤り、、等'));

				echo $this->Form->input('info_flg', array('id' => 'InfoFlgFailed', 'type' => 'checkbox', 'class' => 'flat-red', 'label' => '&nbsp;Push／Mailでメンバーに通知する', 'checked' => $InfoFlgChecked));

				echo $this->Form->input('used_flg', array('id' => 'UsedFlg', 'type' => 'checkbox', 'class' => 'flat-red', 'label' => '&nbsp;割り当てられたAmazonギフト券を「失効」とする'));
				?>
			</div>
			<div class="box-footer">
				<?php echo $this->Form->button('更新', array('type' => 'submit', 'class' => 'btn btn-danger')); ?>
			</div>
		</div>
	</div>
	<?php
		echo $this->Form->end();
	}
	?>
</div>
