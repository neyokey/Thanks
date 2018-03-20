<?php
$this->Html->script('plugins/datatables/jquery.dataTables.min', array('inline' => false));
$this->Html->script('plugins/datatables/dataTables.bootstrap.min', array('inline' => false));
$this->Html->css('plugins/datatables/dataTables.bootstrap.min', array('inline' => false));
$js = <<<EOF
$(function () {
	$('#bot-data-table1').DataTable({
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
	<div class="col-lg-10 col-md-10">
		<div class="box box-info">
			<div class="box-body table-responsive">
				<p><code>※登録したいBotを選択してください</code></p>

				<table cellpadding="0" cellspacing="0" id="bot-data-table1" class="table table-bordered table-hover dataTable">
				<thead>
				<tr>
					<th><?php echo $this->Paginator->sort('bot_id', 'BOT-ID'); ?></th>
					<th><?php echo $this->Paginator->sort('bot_name', 'BOT名'); ?></th>
					<th class="actions" style="width: 200px;">&nbsp;</th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($data as $res): ?>
				<tr>
					<td><?php echo h($res['MstDiaryBot']['bot_id']); ?>&nbsp;</td>
					<td><?php echo h($res['MstDiaryBot']['bot_name']); ?>&nbsp;</td>
					<td class="actions">
						<?php
						echo $this->Html->link('このBotを登録する', '/bot/relationAdd/'.$shop_id.'/'.$res['MstDiaryBot']['bot_id'], array('class' => 'btn btn-info btn-sm'));

						echo '&nbsp;'.$this->Html->link('詳細', '/bot/view/'.$res['MstDiaryBot']['bot_id'], array('class' => 'btn btn-success btn-sm', 'target' => '_blank'));
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
