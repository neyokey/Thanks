<div class="row">
	<div class="col-lg-2 col-md-2">
		<div class="box box-info">
			<div class="box-header">
				<h3 class="box-title">検索フォーム</h3>
			</div>
			<?php echo $this->Form->create('MstAdminUser', array('url' => '/account/shop', 'inputDefaults' => array('div' => array('class' => 'form-group'), 'required' => false))); ?>
			<?php echo $this->Form->input('page', array('type' => 'hidden', 'value' => 1)); ?>
			<div class="box-body">
				<?php
				echo $this->Form->input('status', array('type' => 'select', 'class' => 'form-control', 'label' => '利用状態', 'options' => $status, 'empty' => 'ALL'));

				echo $this->Form->input('trial_flg', array('type' => 'select', 'class' => 'form-control', 'label' => '利用形態', 'options' => $trialFlgs, 'empty' => 'ALL'));

				echo $this->Form->input('id', array('type' => 'text', 'class' => 'form-control', 'label' => 'Id', 'placeholder' => 'Idから検索'));

				echo $this->Form->input('aname', array('type' => 'text', 'class' => 'form-control', 'label' => 'チーム名', 'placeholder' => 'チーム名から検索'));
				?>
			</div>
			<div class="box-footer">
				<?php echo $this->Form->button('Search', array('type' => 'submit', 'class' => 'btn btn-primary')); ?>
			</div>
			<?php echo $this->Form->end() ?>
		</div>
	</div>
	<div class="col-lg-10 col-md-10">
		<div class="box box-info">
			<div class="box-header">
				<h3 class="box-title"><?php echo $title; ?></h3>
				<div class="pull-right box-tools">
				<?php
				if ($userSession['acc_grant'] < 2) {
					echo $this->Html->Link('新規追加', array('action' => 'shopAdd'), array('class' => 'btn btn-info btn-sm'));
				}
				if ($userSession['acc_grant'] == 0) {
					echo '&nbsp;'.$this->Html->Link('一括登録', array('action' => 'shopUpload'), array('class' => 'btn btn-default btn-sm'));
				}
				?>
				</div>
			</div>
			<div class="box-body table-responsive">
				<table cellpadding="0" cellspacing="0" class="table table-bordered table-hover dataTable">
				<thead>
				<tr>
					<th><?php echo $this->Paginator->sort('id', 'Id'); ?></th>
					<th><?php echo $this->Paginator->sort('aname', 'チーム名'); ?></th>
					<th><?php echo $this->Paginator->sort('atel', 'Tel'); ?></th>
					<th><?php echo $this->Paginator->sort('status', '状態'); ?></th>
					<th class="actions" style="width: 116px;">&nbsp;</th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($data as $res): ?>
				<tr>
					<td><?php echo h($res['MstAdminUser']['id']); ?>&nbsp;</td>
					<td><?php echo h($res['MstAdminUser']['aname']); ?>&nbsp;</td>
					<td><?php echo h($res['MstAdminUser']['atel']); ?>&nbsp;</td>
					<td><?php echo h($res['Status']['name']); ?>&nbsp;</td>
					<td class="actions">
						<?php
						echo $this->Html->link('詳細', array('action' => 'shopView', $res['MstAdminUser']['id']), array('class' => 'btn btn-default btn-sm'));
						if ($res["MstAdminUser"]['status'] != 2) {
							echo '&nbsp;'.$this->Html->link('編集', array('action' => 'shopEdit', $res['MstAdminUser']['id']), array('class' => 'btn btn-default btn-sm'));
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
