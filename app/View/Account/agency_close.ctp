<?php
$this->Html->script('jquery.validationEngine', array('inline' => false));
$this->Html->script('jquery.validationEngine-ja', array('inline' => false));
$this->Html->css('validationEngine.jquery', array('inline' => false));

$this->Html->script('plugins/datepicker/bootstrap-datepicker', array('inline' => false));
$this->Html->css('plugins/datepicker/datepicker3', array('inline' => false));
$js = <<<EOF
$(function () {
	// jQuery Validation Engine
	jQuery("#AccountForm").validationEngine('attach', {
		promptPosition:"bottomLeft"
	});

	$('#cancellationDate').datepicker({
		format: "yyyy/mm/dd",
		autoclose: true
	});
});

EOF;
echo $this->Html->scriptBlock($js, array('block' => 'script'));

echo $this->Form->create('MstAdminUser', array('id' => 'AccountForm', 'onsubmit' => 'return confirm("本当に退会処理を行ってよろしいですか？")', 'inputDefaults' => array('div' => 'form-group', 'required' => false)));
echo $this->Form->input('id', array('type' => 'hidden'));
?>
<div class="row">
	<div class="col-lg-12 col-md-12">
		<div class="box box-info">
			<div class="box-header">
				<h3 class="box-title"><?php echo $title; ?></h3>
			</div>
			<div class="box-body table-responsive no-padding">
				<table class="table table-hover">
					<tr>
						<th>Id</th>
						<td><?php echo h($data['MstAdminUser']['id']); ?></td>
					</tr>
					<tr>
						<th width="140px">代理店名</th>
						<td><?php echo $data['MstAdminUser']['aname']; ?></td>
					</tr>
					<tr>
						<th>Email</th>
						<td><?php echo $data['MstAdminUser']['amail']; ?></td>
					</tr>
					<tr>
						<th>Tel</th>
						<td><?php echo $data['MstAdminUser']['atel']; ?></td>
					</tr>
					<tr>
						<th>郵便番号</th>
						<td><?php echo $data['MstAdminUser']['azip']; ?></td>
					</tr>
					<tr>
						<th>住所</th>
						<td><?php echo $data['MstAdminUser']['aaddress']; ?></td>
					</tr>
					<tr>
						<th>メモ</th>
						<td><?php echo nl2br($data['MstAdminUser']['memo']); ?></td>
					</tr>
					<tr>
						<th>登録日時</th>
						<td><?php echo $data['MstAdminUser']['insert_time']; ?></td>
					</tr>
					<tr>
						<th>更新日時</th>
						<td><?php echo $data['MstAdminUser']['update_time']; ?></td>
					</tr>
				</table>
			</div>
		</div>
	</div>

	<div class="col-lg-12 col-md-12">
		<div class="box box-info">
			<div class="box-body table-responsive">
				<?php
				echo '<div class="form-group"><label><i class="fa fa-square"></i>&nbsp;解約日</label><div class="input-group date"><div class="input-group-addon"><li class="fa fa-calendar"></li></div>'.$this->Form->input('cancellation_date', array('id' => 'cancellationDate', 'type' => 'text', 'label' => false, 'class' => 'validate[required] form-control', 'div' => false)).'</div></div>';
				?>
			</div>
			<div class="box-footer">
				<?php echo $this->Form->button('解約処理を行う', array('type' => 'submit', 'class' => 'btn btn-primary')); ?>
			</div>
		</div>
	</div>
</div>
<?php echo $this->Form->end(); ?>
