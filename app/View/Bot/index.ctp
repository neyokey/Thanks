<div class="row">
	<div class="col-lg-2 col-md-2">
		<div class="box box-info">
			<div class="box-body">
				<div class="form-group"><?php echo $this->Html->Link('BOT新規追加', array('action' => 'add'), array('class' => 'btn btn-block btn-info btn-sm')); ?></div>
			</div>
		</div>
	</div>
	<div class="col-lg-10 col-md-10">
		<div class="box box-info">
			<div class="box-body table-responsive">
				<p><code>※こちらで登録したBOTは、店舗一覧から各店舗に設定してください。</code></p>

				<table cellpadding="0" cellspacing="0" class="table table-bordered table-hover dataTable">
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
						echo $this->Html->link('詳細', array('action' => 'view', $res['MstDiaryBot']['bot_id']), array('class' => 'btn btn-success btn-sm'));
						echo '&nbsp;'.$this->Html->link('編集', array('action' => 'edit', $res['MstDiaryBot']['bot_id']), array('class' => 'btn btn-default btn-sm'));
						echo '&nbsp;'.$this->Form->postLink('削除', '/bot/delete/'.$res['MstDiaryBot']['bot_id'], array('class' => 'btn btn-default btn-sm'), __('本当に削除してもよろしいですか # %s?', $res['MstDiaryBot']['bot_id']));
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
