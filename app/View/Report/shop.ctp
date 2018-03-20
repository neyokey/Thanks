<?php
## 集計用JS
$this->Html->css('plugins/daterangepicker/daterangepicker.css', array('inline' => false));
$this->Html->script('//cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js', array('inline' => false));
$this->Html->script('//cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/locale/ja.js', array('inline' => false));
$this->Html->script('plugins/daterangepicker/daterangepicker.js', array('inline' => false));
$js = <<<EOF
$(function () {
	//Date range picker
	$('#reservation').daterangepicker({
		showDropdowns: false,
		opens: 'left',
		locale: {
			format: 'YYYY/MM/DD',
			applyLabel: '選択',
			cancelLabel: 'クリア',
			fromLabel: '開始日',
			toLabel: '終了日',
			weekLabel: 'W',
			customRangeLabel: '自分で指定',
			daysOfWeek: moment.weekdaysMin(),
			monthNames: moment.monthsShort(),
			firstDay: moment.localeData()._week.dow
		}
	});
});

EOF;

$this->Html->script('plugins/datatables/jquery.dataTables.min', array('inline' => false));
$this->Html->script('plugins/datatables/dataTables.bootstrap.min', array('inline' => false));
$this->Html->script('//cdn.datatables.net/plug-ins/1.10.6/sorting/currency.js', array('inline' => false));
$this->Html->css('plugins/datatables/dataTables.bootstrap.min', array('inline' => false));
$js .= <<<EOF
$(function () {
	$('#report-data-table').DataTable({
		'paging'        : true,
		'lengthChange'  : false,
		'searching'     : false,
		'ordering'      : true,
		'info'          : true,
		'autoWidth'     : false,
		'order'         : [[0, 'asc']],
		'displayLength' : 20,
		'columnDefs': [
			{ type: 'currency', targets: [0,2,3,4,5,6] }
        ]
	});
})

EOF;

echo $this->Html->scriptBlock($js, array('block' => 'script'));
?>
<div class="row">
	<?php echo $this->Form->create('TrnThanks'); ?>
	<div class="col-md-6">
		<div class="box box-info">
			<div class="box-body">
				<div class="form-group">
					<label>集計期間</label>
					<div class="input-group">
						<div class="input-group-addon"><i class="fa fa-calendar"></i></div>
						<?php echo $this->Form->input('reservation', array('id' => 'reservation', 'type' => 'text', 'class' => 'form-control pull-right', 'div' => false, 'label' => false, 'readonly' => true)); ?>
						<div class="input-group-btn">
							<?php echo $this->Form->submit('再集計', array('class' => 'btn btn-info pull-right', 'div' => false)); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php echo $this->Form->end(); ?>

	<div class="col-lg-12 col-md-12">
		<div class="box box-info">
			<div class="box-header">
				<h3 class="box-title">集計結果</h3>
			</div>
			<div class="box-body table-responsive">
				<table cellpadding="0" cellspacing="0" id="report-data-table" class="table table-bordered table-hover dataTable">
				<thead>
				<tr>
					<th>Id</th>
					<th>チーム名</th>
					<th>総サンクス数</th>
					<th>thanks数/人・週</th>
					<th>登録人数</th>
					<th style="width: 120px;">&nbsp;</th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($data as $res): ?>
				<tr>
					<td><?php echo $res['MstAdminUser']['id']; ?>&nbsp;</td>
					<td><?php echo $this->Html->Link($res['MstAdminUser']['aname'], array('controller' => 'account', 'action' => 'shopView', $res['MstAdminUser']['id'])); ?>&nbsp;</td>
					<td><?php echo number_format($res['TrnThanksSumShop']['thanks_sends']).'件'; ?>&nbsp;</td>
					<td><?php echo '<span class="label '.$res['Report']['label'].'">'.sprintf('%.1f', round($res['Report']['reportNum'], 1)).'</span>'; ?>&nbsp;</td>
					<td><?php echo $res['Report']['memberNum'].'人'; ?>&nbsp;</td>
					<td class="actions">
						<?php
						echo $this->Html->link('詳細', array('action' => 'staff', $this->request->data['TrnThanks']['from'], $this->request->data['TrnThanks']['to'], $res['MstAdminUser']['id']), array('class' => 'btn btn-default btn-sm'));

						echo '&nbsp;'.$this->Html->Link('レポート', array('action' => 'shopDetail', $res['MstAdminUser']['id'], $this->request->data['TrnThanks']['from'], $this->request->data['TrnThanks']['to']), array('class' => 'btn btn-info btn-sm'));
						?>
					</td>
				</tr>
				<?php endforeach; ?>
				</tbody>
				<tfoot>
				<tr>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
					<th><?php echo number_format($sums['TOTA']).'件'; ?></th>
					<th><?php echo '<span class="label '.$sums['_REPO'].'">'.sprintf('%.1f', round($sums['REPO'])).'</span>'; ?></th>
					<th><?php echo number_format($sums['MENB']).'人'; ?></th>
					<th>&nbsp;</th>
				</tr>
				</tfoot>
				</table>
			</div>
		</div>
	</div>
</div>
