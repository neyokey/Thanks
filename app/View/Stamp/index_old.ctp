<div class="row">
	<div class="col-lg-12 col-md-12">
		<div class="box box-info">
			<div class="box-header">
				<h3 class="box-title"><?php echo $title; ?></h3>
				<div class="pull-right box-tools">
					<?php echo $this->Html->Link('新規追加', array('action' => 'add'), array('class' => 'btn btn-info btn-sm')); ?>
					<?php echo $this->Html->Link('並び替え', array('action' => 'sort'), array('class' => 'btn btn-default btn-sm')); ?>
				</div>
			</div>
			<div class="box-body table-responsive">
				<table cellpadding="0" cellspacing="0" class="table table-bordered table-hover dataTable">
				<thead>
				<tr>
					<th><?php echo $this->Paginator->sort('stamp_id', 'Id'); ?></th>
					<th><?php echo $this->Paginator->sort('status', '状態'); ?></th>
					<th><?php echo $this->Paginator->sort('stamp_name', 'スタンプ名'); ?></th>
					<th>スタンプ画像</th>
					<th class="actions" style="width: 140px;">&nbsp;</th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($data as $res): ?>
				<tr>
					<td><?php echo h($res['MstThanksStamp']['stamp_id']); ?>&nbsp;</td>
					<td><?php echo $res['MstThanksStamp']['status'] == 0 ? '<span class="label pull-right bg-green">有効</span>' : '<span class="label label-primary pull-right">無効</span>'; ?></td>
					<td><?php echo h($res['MstThanksStamp']['stamp_name']); ?>&nbsp;</td>
					<td><?php echo $this->Html->Image('https://39s.work/api/'.$res['MstThanksStamp']['image_url'], array('class' => 'img-responsive')); ?>&nbsp;</td>
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
