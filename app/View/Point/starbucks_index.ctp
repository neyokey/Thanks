<?php
$this->Html->script('plugins/datepicker/bootstrap-datepicker', array('inline' => false));
$this->Html->css('plugins/datepicker/datepicker3', array('inline' => false));
$js = <<<EOF
$(function () {
	jQuery('#RequestDatetime').datepicker({
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
				?>
			</div>
			<div class="box-footer clearfix">
				<?php echo $this->Form->button('Search', array('type' => 'submit', 'class' => 'btn btn-primary')); ?>
			</div>
		</div>
	</div>
	<?php echo $this->Form->end(); ?>

	<div class="col-lg-10 col-md-10">
		<div class="box box-info">
			<div class="box-header">
				<h3 class="box-title">一覧</h3>
			</div>
			<div class="box-body table-responsive">
				<table cellpadding="0" cellspacing="0" class="table table-bordered table-hover dataTable">
				<thead>
				<tr>
					<th><?php echo $this->Paginator->sort('TrnPointExchange.exchange_id', 'Id'); ?></th>
					<th><?php echo $this->Paginator->sort('TrnMembers.member_name', 'メンバー名'); ?></th>
					<th><?php echo $this->Paginator->sort('TrnPointExchange.exchange_point', '申請pt'); ?></th>
					<th><?php echo $this->Paginator->sort('TrnPointExchange.request_datetime', '申請日時'); ?></th>
					<th><?php echo $this->Paginator->sort('TrnPointExchange.status', 'ステータス'); ?></th>
					<th class="actions" style="width: 116px;">&nbsp;</th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($data as $res): ?>
				<tr>
					<td><?php echo $res['TrnPointExchange']['exchange_id']; ?>&nbsp;</td>
					<td><?php echo $this->Html->Link($res['TrnMembers']['member_name'], '/account/staffView/'.$res['TrnMembers']['member_id']); ?>&nbsp;</td>
					<td><?php echo number_format($res['TrnPointExchange']['exchange_point']).'pt'; ?>&nbsp;</td>
					<td><?php echo $res['TrnPointExchange']['request_datetime']; ?>&nbsp;</td>
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
						echo $this->Html->link('詳細', array('action' => 'starbucksView', $res['TrnPointExchange']['exchange_id']), array('class' => 'btn btn-default btn-sm'));
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
</div>
