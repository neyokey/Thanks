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
})

EOF;

echo $this->Html->scriptBlock($js, array('block' => 'script'));
?>
<div class="row">
	<div class="col-lg-12 col-md-12">
		<div class="box box-info">
			<div class="box-body table-responsive no-padding">
				<table class="table table-hover">
					<tr>
						<th width="140px">Id</th>
						<td><?php echo $data['MstThanksStamp']['stamp_id']; ?></td>
					</tr>
					<tr>
						<th>スタンプ名</th>
						<td><?php echo $data['MstThanksStamp']['stamp_name']; ?></td>
					</tr>
					<tr>
						<th>スタンプ画像</th>
						<td><?php echo $this->Html->Image('https://39s.work/api/'.$data['MstThanksStamp']['image_url'], array('class' => 'img-responsive', 'style' => 'width: 50%;')); ?></td>
					</tr>
					<tr>
						<th>カテゴリー</th>
						<td><?php echo $this->Html->Link($data['MstThanksStampCategory']['category_name'], '/stamp/index/'.$data['MstThanksStampCategory']['category_id']); ?></td>
					</tr>
					<tr>
						<th>登録日時</th>
						<td><?php echo $data['MstThanksStamp']['insert_time']; ?></td>
					</tr>
					<tr>
						<th>更新日時</th>
						<td><?php echo $data['MstThanksStamp']['update_time']; ?></td>
					</tr>
				</table>
			</div>
		</div>
	</div>
	<div class="col-lg-12 col-md-12">
		<div class="box box-info">
			<div class="box-header">
				<h3 class="box-title">登録アカウント一覧</h3>
			</div>
			<div class="box-body table-responsive">
				<table cellpadding="0" cellspacing="0" id="stamp-data-table1" class="table table-bordered table-hover dataTable">
				<thead>
				<tr>
					<th>Id</th>
					<th>種類</th>
					<th>名前</th>
					<th class="actions" style="width: 116px;">&nbsp;</th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($data['MstAdminUser'] as $res): ?>
				<tr>
					<td><?php echo $res['id']; ?></td>
					<td><?php echo $res['type'] ?></td>
					<td><?php echo $res['aname'] ?></td>
					<td><?php
					switch ($res['acc_grant']) {
						case '1':
							$link = '/account/agencyView/'.$res['id'];
							break;
						case '2':
							$link = '/account/companyView/'.$res['id'];
							break;
						case '3':
							$link = '/account/shopView/'.$res['id'];
							break;
					}
					echo $this->Html->Link('詳細', $link, array('class' => 'btn btn-default btn-sm'));
					?></td>
				</tr>
				<?php endforeach; ?>
				</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
