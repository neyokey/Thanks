<div class="row">
	<div class="col-lg-12 col-md-12">
		<div class="box box-info">
			<div class="box-header">
				<h3 class="box-title">一覧</h3>
			</div>
			<div class="box-body table-responsive">
				<table cellpadding="0" cellspacing="0" class="table table-bordered table-hover dataTable">
				<thead>
				<tr>
					<th><?php echo $this->Paginator->sort('TrnPointExchange.csv_output_date', 'CSV出力日時'); ?></th>
					<th>出力件数</th>
					<th class="actions" style="width: 116px;">&nbsp;</th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($data as $res): ?>
				<tr>
					<td><?php echo $res['TrnPointExchange']['csv_output_date']; ?>&nbsp;</td>
					<td><?php echo number_format($res[0]['nums']).'件'; ?>&nbsp;</td>
					<td class="actions">
						<?php
						echo $this->Html->link('CSVダウンロード', '/point/tpointDownload/0/'.strtotime($res['TrnPointExchange']['csv_output_date']), array('class' => 'btn btn-success btn-sm'));
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
