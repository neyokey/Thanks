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
		<div class="box">
			<div class="box-body">
				<p><code>※スタンプ本体の登録は「スタンプ管理」より行ってください。</code></p>
				
				<table cellpadding="0" cellspacing="0" id="stamp-data-table1" class="table table-bordered table-hover dataTable">
				<thead>
				<tr>
					<th>Id</th>
					<th>Sort</th>
					<th>スタンプ名</th>
					<th>スタンプ画像</th>
					<th class="actions" style="width: 140px;">&nbsp;</th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($data as $res): ?>
				<tr>
					<td><?php echo h($res['MstThanksStamp']['stamp_id']); ?></td>
					<td><?php echo h($res['MstThanksStamp']['sort']); ?></td>
					<td><?php echo h($res['MstThanksStamp']['stamp_name']); ?>&nbsp;</td>
					<td><?php echo $this->Html->Image('https://39s.work/api/'.$res['MstThanksStamp']['image_url'], array('class' => 'img-responsive', 'style' => 'width: 50%;')); ?>&nbsp;</td>
					<td class="actions">
						<?php
						echo $this->Form->postLink('固有スタンプへ登録', '/stamp/relationAddOn/'.$mode.'/'.$id.'/'.$res['MstThanksStamp']['stamp_id'], array('class' => 'btn btn-success btn-sm'), __('このスタンプを固有スタンプに登録します # %s?', $res['MstThanksStamp']['stamp_id']));
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
