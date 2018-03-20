<?php
if ($userSession['acc_grant'] == 0) {
	$this->Html->script('plugins/datatables/jquery.dataTables.min', array('inline' => false));
	$this->Html->script('plugins/datatables/dataTables.bootstrap.min', array('inline' => false));
	$this->Html->css('plugins/datatables/dataTables.bootstrap.min', array('inline' => false));
	$js = <<<EOF
$(function () {
	$('#data-table1').DataTable({
		'paging'        : true,
		'lengthChange'  : false,
		'searching'     : false,
		'ordering'      : true,
		'info'          : true,
		'autoWidth'     : false,
		'order'         : [[0, 'asc']],
		'displayLength' : 20
	});
	$('#data-table2').DataTable({
		'paging'        : true,
		'lengthChange'  : false,
		'searching'     : false,
		'ordering'      : true,
		'info'          : true,
		'autoWidth'     : false,
		'order'         : [[0, 'asc']],
		'displayLength' : 20
	});
})

EOF;
	echo $this->Html->scriptBlock($js, array('block' => 'script'));
}
?>
<div class="row">
	<div class="col-lg-12 col-md-12">
		<div class="box box-info">
			<div class="box-header">
				<h3 class="box-title">詳細</h3>
				<div class="pull-right box-tools">
					<?php
					echo $this->Html->Link('＜＜前月へ', '/report/stampCompany/'.$company_id.'/'.strtotime('-1 month', $datetime), array('class' => 'btn btn-default btn-sm'));
					echo '&nbsp;'.$this->Html->Link('次月へ＞＞', '/report/stampCompany/'.$company_id.'/'.strtotime('+1 month', $datetime), array('class' => 'btn btn-default btn-sm'));
					?>
				</div>
			</div>
			<div class="box-body table-responsive no-padding">
				<table class="table table-hover">
					<tr>
						<th width="140px">Id</th>
						<td><?php echo $data['MstAdminUser']['id']; ?></td>
					</tr>
					<tr>
						<th>企業名</th>
						<td><?php echo $data['MstAdminUser']['aname']; ?></td>
					</tr>
					<tr>
						<th>発行ポイント合計</th>
						<td><?php echo number_format($data['TrnPointTrade']['addition']).'pt'; ?></td>
					</tr>
					<tr>
						<th>交換ポイント合計</th>
						<td><?php echo number_format($data['TrnPointTrade']['exchange']).'pt'; ?></td>
					</tr>
				</table>
			</div>
		</div>
	</div>

	<div class="col-lg-12 col-md-12">
		<div class="box box-info">
			<div class="box-header">
				<h3 class="box-title">交換アイテム一覧</h3>
			</div>
			<div class="box-body">
				<table cellpadding="0" cellspacing="0" id="data-table1" class="table table-bordered table-hover dataTable">
				<thead>
				<tr>
					<th>Id</th>
					<th>アイテム名</th>
					<th>交換ポイント合計</th>
					<th>交換人数</th>
					<th>交換人数（述べ）</th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($data['Items'] as $res): ?>
				<tr>
					<td><?php echo $res['item_id']; ?>&nbsp;</td>
					<td><?php echo $res['item_name']; ?>&nbsp;</td>
					<td><?php echo number_format($res['sum_point']).'pt'; ?>&nbsp;</td>
					<td><?php echo number_format($res['num1']).'人'; ?>&nbsp;</td>
					<td><?php echo number_format($res['num2']).'人'; ?>&nbsp;</td>
				</tr>
				<?php endforeach; ?>
				</tbody>
				</table>
			</div>
		</div>
	</div>

	<div class="col-lg-12 col-md-12">
		<div class="box box-info">
			<div class="box-header">
				<h3 class="box-title">チーム一覧</h3>
			</div>
			<div class="box-body">
				<table cellpadding="0" cellspacing="0" id="data-table2" class="table table-bordered table-hover dataTable">
				<thead>
				<tr>
					<th>Id</th>
					<th>チーム名</th>
					<th>発行ポイント合計</th>
					<th>交換ポイント合計</th>
					<th class="actions" style="width: 140px;">&nbsp;</th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($data['Shops'] as $shop_id => $res): ?>
				<tr>
					<td><?php echo '<span class="hide">'.sprintf("%09d", $shop_id).'</span>'.$shop_id; ?>&nbsp;</td>
					<td><?php echo $this->Html->Link($res['shop_name'], '/account/shopView/'.$shop_id); ?>&nbsp;</td>
					<td><?php echo '<span class="hide">'.sprintf("%09d", $res['addition']).'</span>'.number_format($res['addition']).'pt'; ?>&nbsp;</td>
					<td><?php echo '<span class="hide">'.sprintf("%09d", $res['exchange']).'</span>'.number_format($res['exchange']).'pt'; ?>&nbsp;</td>
					<td class="actions">
						<?php
						echo $this->Html->link('詳細', '/report/stampShop/'.$shop_id.'/'.$datetime, array('class' => 'btn btn-info btn-sm'));
						?>
					</td>
				</tr>
				<?php endforeach; ?>
				</tbody>
				</table>
			</div>
			<div class="box-footer clearfix">
				<p><?php echo $this->Html->Link('CSVでダウンロード', '/report/stampCompanyDownload/'.$company_id.'/'.$datetime, array('class' => 'btn btn-success')); ?></p>
			</div>
		</div>
	</div>
</div>
