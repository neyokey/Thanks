<div class="row">
	<div class="col-lg-12 col-md-12">
		<div class="box box-info">
			<div class="box-header">
				<h3 class="box-title"><?php echo $title; ?></h3>
				<div class="pull-right box-tools">
					<?php echo $this->Html->Link('新規追加', array('action' => 'companyAdd'), array('class' => 'btn btn-info btn-sm')); ?>
				</div>
			</div>
			<div class="box-body table-responsive">
				<table cellpadding="0" cellspacing="0" class="table table-bordered table-hover dataTable">
				<thead>
				<tr>
					<th><?php echo $this->Paginator->sort('id', 'Id'); ?></th>
					<th><?php echo $this->Paginator->sort('aname', '企業名'); ?></th>
					<th><?php echo $this->Paginator->sort('atel', 'Tel'); ?></th>
					<th class="actions" style="width: 116px;">&nbsp;</th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($data as $res): ?>
				<tr>
					<td><?php echo h($res['MstAdminUser']['id']); ?>&nbsp;</td>
					<td><?php echo h($res['MstAdminUser']['aname']); ?>&nbsp;</td>
					<td><?php echo h($res['MstAdminUser']['atel']); ?>&nbsp;</td>
					<td class="actions">
						<?php
						echo $this->Html->link('詳細', array('action' => 'companyView', $res['MstAdminUser']['id']), array('class' => 'btn btn-default btn-sm'));
						if ($res["MstAdminUser"]['status'] != 2) {
							echo '&nbsp;'.$this->Html->link('編集', array('action' => 'companyEdit', $res['MstAdminUser']['id']), array('class' => 'btn btn-default btn-sm'));
//							echo $this->Form->postLink('削除', array('action' => 'companyDelete', $res['MstAdminUser']['id']), array('class' => 'btn btn-default btn-sm'), __('本当に削除してもよろしいですか # %s?', $res['MstAdminUser']['id']));
						} else {
							echo '&nbsp;'.$this->Form->button('編集', array('type' => 'button', 'class' => 'btn btn-default btn-sm', 'disabled' => true));
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
					?>
				</ul>
			</div>
		</div>
	</div>
</div>
