<?php
$this->Html->script('plugins/datatables/jquery.dataTables.min', array('inline' => false));
$this->Html->script('plugins/datatables/dataTables.bootstrap.min', array('inline' => false));
$this->Html->css('plugins/datatables/dataTables.bootstrap.min', array('inline' => false));
$js = <<<EOF
$(function () {
	$('#stamp-data-table1').DataTable({
		'paging'        : true,
		'lengthChange'  : false,
		'searching'     : false,
		'ordering'      : true,
		'info'          : true,
		'autoWidth'     : false,
		'order'         : [[1, 'asc']],
		'displayLength' : 20
	});
	$('#stamp-data-table2').DataTable({
		'paging'        : true,
		'lengthChange'  : false,
		'searching'     : false,
		'ordering'      : true,
		'info'          : true,
		'autoWidth'     : false,
		'displayLength' : 20
	})
})

EOF;

echo $this->Html->scriptBlock($js, array('block' => 'script'));


$this->log($data1);
?>
<div class="row">
	<div class="col-lg-2 col-md-2">
		<div class="box box-info">
			<div class="box-body">
				<div class="form-group"><?php echo $this->Html->Link('新規追加', array('action' => 'add', $category_id), array('class' => 'btn btn-block btn-info btn-sm')); ?></div>
				<div class="form-group"><?php echo $this->Html->Link('並び替え', array('action' => 'sort', $category_id), array('class' => 'btn btn-block btn-default btn-sm')); ?></div>
			</div>
		</div>
	</div>
	<div class="col-lg-10 col-md-10">
		<div class="nav-tabs-custom">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#tab_1" data-toggle="tab">有効</a></li>
				<li><a href="#tab_2" data-toggle="tab">無効</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="tab_1">
					<table cellpadding="0" cellspacing="0" id="stamp-data-table1" class="table table-bordered table-hover dataTable">
					<thead>
					<tr>
						<th>Id</th>
						<th>Sort</th>
						<th>スタンプ名</th>
						<th>スタンプ画像</th>
						<th style="width: 40px;">&nbsp;</th>
						<th class="actions" style="width: 140px;">&nbsp;</th>
					</tr>
					</thead>
					<tbody>
					<?php foreach ($data1 as $res): ?>
					<tr>
						<td><?php echo h($res['MstThanksStamp']['stamp_id']); ?></td>
						<td><?php echo h($res['MstThanksStamp']['sort']); ?></td>
						<td><?php echo h($res['MstThanksStamp']['stamp_name']); ?>&nbsp;</td>
						<td><?php echo $this->Html->Image('https://39s.work/api/'.$res['MstThanksStamp']['image_url'], array('class' => 'img-responsive', 'style' => 'width: 50%;')); ?>&nbsp;</td>
						<td class="actions">
							<?php
							if ($res['MstThanksStamp']['all_flg'] == 1) {
								echo '<button class="btn btn-info btn-sm">共有</button>';
							} else {
								echo $this->Html->Link('限定', '/stamp/view/'.$res['MstThanksStamp']['stamp_id'], array('class' => 'btn btn-warning btn-sm'));
							}
							?>
						</td>
						<td class="actions">
							<?php
							echo $this->Html->link('編集', array('action' => 'edit', $res['MstThanksStamp']['stamp_id']), array('class' => 'btn btn-default btn-sm')).'&nbsp;';
							if ($res['MstThanksStamp']['status'] == 0) {
								echo $this->Form->postLink('無効化', array('action' => 'delete', $res['MstThanksStamp']['stamp_id']), array('class' => 'btn btn-default btn-sm'), __('本当に無効にしてもよろしいですか # %s?', $res['MstThanksStamp']['stamp_id']));
							} else {
								echo $this->Form->postLink('有効化', array('action' => 'revival', $res['MstThanksStamp']['stamp_id']), array('class' => 'btn btn-default btn-sm'), __('本当に有効にしてもよろしいですか # %s?', $res['MstThanksStamp']['stamp_id']));
							}
							?>
						</td>
					</tr>
					<?php endforeach; ?>
					</tbody>
					</table>
				</div>
				<div class="tab-pane" id="tab_2">
					<table cellpadding="0" cellspacing="0" id="stamp-data-table2" class="table table-bordered table-hover dataTable">
					<thead>
					<tr>
						<th>Id</th>
						<th>スタンプ名</th>
						<th>スタンプ画像</th>
						<th style="width: 40px;">&nbsp;</th>
						<th class="actions" style="width: 140px;">&nbsp;</th>
					</tr>
					</thead>
					<tbody>
					<?php foreach ($data2 as $res): ?>
					<tr>
						<td><?php echo h($res['MstThanksStamp']['stamp_id']); ?></td>
						<td><?php echo h($res['MstThanksStamp']['stamp_name']); ?>&nbsp;</td>
						<td><?php echo $this->Html->Image('https://39s.work/api/'.$res['MstThanksStamp']['image_url'], array('class' => 'img-responsive', 'style' => 'width: 50%;')); ?>&nbsp;</td>
						<td class="actions">
							<?php
							if ($res['MstThanksStamp']['all_flg'] == 1) {
								echo '<button class="btn btn-info btn-sm">共有</button>';
							} else {
								echo $this->Html->Link('限定', '/stamp/view/'.$res['MstThanksStamp']['stamp_id'], array('class' => 'btn btn-warning btn-sm'));
							}
							?>
						</td>
						<td class="actions">
							<?php
							echo $this->Html->link('編集', array('action' => 'edit', $res['MstThanksStamp']['stamp_id']), array('class' => 'btn btn-default btn-sm')).'&nbsp;';
							if ($res['MstThanksStamp']['status'] == 0) {
								echo $this->Form->postLink('無効化', array('action' => 'delete', $res['MstThanksStamp']['stamp_id']), array('class' => 'btn btn-default btn-sm'), __('本当に無効にしてもよろしいですか # %s?', $res['MstThanksStamp']['stamp_id']));
							} else {
								echo $this->Form->postLink('有効化', array('action' => 'revival', $res['MstThanksStamp']['stamp_id']), array('class' => 'btn btn-default btn-sm'), __('本当に有効にしてもよろしいですか # %s?', $res['MstThanksStamp']['stamp_id']));
							}
							?>
						</td>
					</tr>
					<?php endforeach; ?>
					</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
