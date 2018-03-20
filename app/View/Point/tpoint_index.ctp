<?php
$this->Html->script('plugins/iCheck/icheck.min', array('inline' => false));
$this->Html->css('plugins/iCheck/all', array('inline' => false));

$this->Html->script('plugins/datepicker/bootstrap-datepicker', array('inline' => false));
$this->Html->css('plugins/datepicker/datepicker3', array('inline' => false));
$js = <<<EOF
$(function () {
	//iCheck for checkbox and radio inputs
	$('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
		checkboxClass: 'icheckbox_flat-green',
		radioClass   : 'iradio_flat-green'
	})

	jQuery('#RequestDatetime').datepicker({
		format: 'yy-mm-dd',
		autoclose: true
	});
	jQuery('#CsvOutputDate').datepicker({
		format: 'yy-mm-dd',
		autoclose: true
	});
});

EOF;

echo $this->Html->scriptBlock($js, array('block' => 'script'));
?>
<div class="row">
	<?php echo $this->Form->create('TrnPointExchange', array('inputDefaults' => array('div' => array('class' => 'form-group'), 'required' => false))); ?>
	<?php echo $this->Form->input('page', array('type' => 'hidden', 'value' => 1)); ?>
	<div class="col-lg-2 col-md-2">
		<div class="box box-info">
			<div class="box-header">
				<h3 class="box-title">検索</h3>
			</div>
			<div class="box-body">
				<?php
				echo $this->Form->input('status', array('type' => 'select', 'class' => 'form-control', 'label' => '利用状態', 'options' => $status, 'empty' => 'ALL'));

				echo $this->Form->input('member_id', array('type' => 'text', 'class' => 'form-control', 'label' => 'メンバーId', 'placeholder' => '123456'));

				echo $this->Form->input('member_name', array('type' => 'text', 'class' => 'form-control', 'label' => 'メンバー名', 'placeholder' => '田中太郎'));

				echo $this->Form->input('email', array('type' => 'text', 'class' => 'form-control', 'label' => 'メールアドレス', 'placeholder' => '39s@aruto.me'));

				echo '<div class="form-group"><label><i class="fa fa-square"></i>&nbsp;申請日</label><div class="input-group date"><div class="input-group-addon"><li class="fa fa-calendar"></li></div>'.$this->Form->input('request_datetime', array('id' => 'RequestDatetime', 'type' => 'text', 'label' => false, 'class' => 'form-control', 'div' => false)).'</div></div>';

				echo '<div class="form-group"><label><i class="fa fa-square"></i>&nbsp;CSV出力日時</label><div class="input-group date"><div class="input-group-addon"><li class="fa fa-calendar"></li></div>'.$this->Form->input('csv_output_date', array('id' => 'CsvOutputDate', 'type' => 'text', 'label' => false, 'class' => 'form-control', 'div' => false)).'</div></div>';
				?>
			</div>
			<div class="box-footer clearfix">
				<?php echo $this->Form->button('Search', array('type' => 'submit', 'class' => 'btn btn-primary')); ?>
			</div>
		</div>
	</div>
	<?php echo $this->Form->end(); ?>

	<?php echo $this->Form->create('TrnPointExchange', array('url' => '/point/tpointPush', 'inputDefaults' => array('div' => 'form-group', 'required' => false))); ?>
	<div class="col-lg-10 col-md-10">
		<div class="box box-info">
			<div class="box-header">
				<h3 class="box-title">一覧</h3>
			</div>
			<div class="box-body table-responsive">
				<p><b>※操作の流れについて</b></p>
				<ol>
					<li><code>「新規リクエストのCSVダウンロード」</code>をクリックして、T-ポイント側の管理画面にアップするCSVをダウンロードしてください。</li>

					<li>T-ポイント側管理画面からエラーCSVが出力された場合は、<code>「結果CSV（エラー）のアップロード」</code>からアップしてください。<br />
					該当のリクエストは「失敗」としてキャンセルされ、その旨がメンバーへPush／Mailにて通知されます。</li>

					<li>無事T-ポイント側の審査が通ったリクエストについては、チェックを入れ<code>「チェックした申請の完了処理」</code>をクリックして下さい。<br />
					該当のリクエストは「成功」として処理され、その旨がメンバーへPush／Mailにて通知されます。<br />
					※チェックできるのはステータスが「処理中」のリクエストのみです</li>
				</ol>

				<table cellpadding="0" cellspacing="0" class="table table-bordered table-hover dataTable">
				<thead>
				<tr>
					<th>&nbsp;</th>
					<th><?php echo $this->Paginator->sort('TrnPointExchange.exchange_id', 'Id'); ?></th>
					<th><?php echo $this->Paginator->sort('TrnMembers.member_name', 'メンバー名'); ?></th>
					<th><?php echo $this->Paginator->sort('TrnPointExchange.exchange_point', '申請pt'); ?></th>
					<th><?php echo $this->Paginator->sort('TrnPointExchange.request_datetime', '申請日時'); ?></th>
					<th><?php echo $this->Paginator->sort('TrnPointExchange.csv_output_date', 'CSV出力日時'); ?></th>
					<th><?php echo $this->Paginator->sort('TrnPointExchange.status', 'ステータス'); ?></th>
					<th class="actions" style="width: 116px;">&nbsp;</th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($data as $res): ?>
				<tr>
					<td><?php echo $this->Form->input('TrnPointExchange.exchange_id.'.$res['TrnPointExchange']['exchange_id'], array('type' => 'checkbox', 'class' => 'flat-red', 'label' => false, 'div' => false, 'disabled' => $res['TrnPointExchange']['status'] == 1 ? false : true)); ?></td>
					<td><?php echo $res['TrnPointExchange']['exchange_id']; ?>&nbsp;</td>
					<td><?php echo $this->Html->Link($res['TrnMembers']['member_name'], '/account/staffView/'.$res['TrnMembers']['member_id']); ?>&nbsp;</td>
					<td><?php echo number_format($res['TrnPointExchange']['exchange_point']).'pt'; ?>&nbsp;</td>
					<td><?php echo $res['TrnPointExchange']['request_datetime']; ?>&nbsp;</td>
					<td><?php echo !empty($res['TrnPointExchange']['csv_output_date']) ? $res['TrnPointExchange']['csv_output_date'] : '-'; ?>&nbsp;</td>
					<td><?php
					$str = $res['Status'];
					if (isset($res['ExchangeResult'])) {
						$str .= '／'.$res['ExchangeResult'];
					}
					switch ($res['TrnPointExchange']['status']) {
						case 0: echo '<span class="label label-success">'.$str.'</span>'; break;
						case 1: echo '<span class="label label-warning">'.$str.'</span>'; break;
						case 2: echo '<span class="label label-primary">'.$str.'</span>'; break;
					}
					?>&nbsp;</td>
					<td class="actions">
						<?php
						echo $this->Html->link('詳細', array('action' => 'tpointView', $res['TrnPointExchange']['exchange_id']), array('class' => 'btn btn-default btn-sm'));
						?>
					</td>
				</tr>
				<?php endforeach; ?>
				</tbody>
				</table>
				<p><?php
				echo $this->Paginator->counter(array(
				'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
				));
				?></p>
				<div class="paging">
				</div>
			</div>
			<div class="box-footer clearfix">
				<?php
				echo '<p>'.$this->Html->Link('新規リクエストのCSVダウンロード', '/point/tpointDownload', array('class' => 'btn btn-success', 'style' => 'width: 240px;')).'</p>';

				echo '<p>'.$this->Html->Link('結果CSV（エラー）のアップロード', '/point/tpointUpload', array('class' => 'btn btn-success', 'style' => 'width: 240px;')).'</p>';

				echo '<p>'.$this->Form->submit('チェックした申請の完了処理', array('class' => 'btn btn-success', 'style' => 'width: 240px;', 'div' => false)).'</p>';

				echo '<p>'.$this->Html->Link('＞＞CSV出力履歴', '/point/tpointHistory', array('class' => 'btn btn-info')).'</p>';
				?>

				<ul class="pagination pagination-sm no-margin pull-right">
					<?php
					echo '<li>'.$this->Paginator->prev('< ' . __('previous'), array('tag' => false), null, array('tag' => 'span')).'</li>';
					echo $this->Paginator->numbers(array('separator' => '', 'tag' => 'li', 'currentTag' => 'span'));
					echo '<li>'.$this->Paginator->next(__('next') . ' >', array('tag' => false), null, array('tag' => 'span')).'</li>';
					?>
				</ul>
			</div>
		</div>
	</div>
	<?php echo $this->Form->end(); ?>
</div>
