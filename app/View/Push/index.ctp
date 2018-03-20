<div class="row">
	<div class="col-lg-12 col-md-12">
		<div class="box box-info">
			<div class="box-header">
				<h3 class="box-title"><?php echo $title; ?></h3>
				<div class="pull-right box-tools">
					<?php echo $this->Html->Link('新規予約', array('action' => 'add'), array('class' => 'btn btn-info btn-sm')); ?>
				</div>
			</div>
			<div class="box-body table-responsive">
				<table cellpadding="0" cellspacing="0" class="table table-bordered table-hover dataTable">
				<thead>
				<tr>
					<th><?php echo $this->Paginator->sort('id', 'Id'); ?></th>
					<th><?php echo $this->Paginator->sort('status', '状態'); ?></th>
					<th><?php echo $this->Paginator->sort('reserve_time', '送信日時'); ?></th>
					<th><?php echo $this->Paginator->sort('message', '送信メッセージ'); ?></th>
					<th class="actions" style="width: 140px;">&nbsp;</th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($data as $res): ?>
				<tr>
					<td><?php echo $res['MstPuchReserve']['id']; ?>&nbsp;</td>
					<td><?php echo $res['Status']['id'] == 1 ? '<span class="label label-success">'.$res['Status']['name'].'</span>' : '<span class="label label-default">'.$res['Status']['name'].'</span>'; ?>&nbsp;</td>
					<td><?php echo date('Y-m-d H:i', strtotime($res['MstPuchReserve']['reserve_time'])); ?>&nbsp;</td>
					<td><?php echo mb_substr($res['MstPuchReserve']['message'], 0, 20).'..'; ?>&nbsp;</td>
					<td class="actions">
						<?php
						if ($res['MstPuchReserve']['status'] == 1) {
							echo $this->Html->link('編集', array('action' => 'edit', $res['MstPuchReserve']['id']), array('class' => 'btn btn-default btn-sm')).'&nbsp;';
							if ($res['MstPuchReserve']['flg'] == 1) {
								echo $this->Form->postLink('一時停止', array('action' => 'stop', $res['MstPuchReserve']['id']), array('class' => 'btn btn-default btn-sm'), __('本当に無効にしてもよろしいですか # %s?', $res['MstPuchReserve']['id']));
							} else {
								echo $this->Form->postLink('送信再開', array('action' => 'restart', $res['MstPuchReserve']['id']), array('class' => 'btn btn-default btn-sm'), __('本当に有効にしてもよろしいですか # %s?', $res['MstPuchReserve']['id']));
							}
						} else {
							echo '&nbsp;';
						}
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

					$this->log($data);
					?>
				</ul>
			</div>
		</div>
	</div>
</div>
