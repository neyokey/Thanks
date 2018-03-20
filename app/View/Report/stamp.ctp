<div class="row">
	<div class="col-lg-12 col-md-12">
		<div class="box box-info">
			<div class="box-header">
				<h3 class="box-title">集計結果</h3>
				<div class="pull-right box-tools">
					<?php
					echo $this->Html->Link('＜＜前月へ', '/report/stamp/'.strtotime('-1 month', $datetime), array('class' => 'btn btn-default btn-sm'));
					echo '&nbsp;'.$this->Html->Link('次月へ＞＞', '/report/stamp/'.strtotime('+1 month', $datetime), array('class' => 'btn btn-default btn-sm'));
					?>
				</div>
			</div>
			<div class="box-body table-responsive">
				<table cellpadding="0" cellspacing="0" class="table table-bordered table-hover dataTable">
				<thead>
				<tr>
					<th>Id</th>
					<th>企業名</th>
					<th>発行ポイント合計</th>
					<th>交換ポイント合計</th>
					<th class="actions" style="width: 116px;">&nbsp;</th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($data as $res): ?>
				<tr>
					<td><?php echo $res['MstAdminUser']['id']; ?>&nbsp;</td>
					<td><?php echo $res['MstAdminUser']['aname']; ?>&nbsp;</td>
					<td><?php echo number_format($res['TrnPointTrade']['addition']).'pt'; ?>&nbsp;</td>
					<td><?php echo number_format($res['TrnPointTrade']['exchange']).'pt'; ?>&nbsp;</td>
					<td class="actions">
						<?php
						echo $this->Html->link('詳細', '/report/stampCompany/'.$res['MstAdminUser']['id'], array('class' => 'btn btn-info btn-sm'));
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
				<p><?php echo $this->Html->Link('CSVでダウンロード', '/report/stampDownload/'.$datetime, array('class' => 'btn btn-success')); ?></p>
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
				<?php foreach ($items as $res): ?>
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
</div>
