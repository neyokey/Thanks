<div class="row">
	<div class="col-lg-2 col-md-2">
		<div class="box box-info">
			<div class="box-header">
				<h3 class="box-title">検索フォーム</h3>
			</div>
			<?php echo $this->Form->create('MstAdminUser', array('url' => '/account/staff', 'inputDefaults' => array('div' => array('class' => 'form-group'), 'required' => false))); ?>
			<?php echo $this->Form->input('page', array('type' => 'hidden', 'value' => 1)); ?>
			<div class="box-body">
				<?php
				echo $this->Form->input('status', array('type' => 'select', 'class' => 'form-control', 'label' => '利用状態', 'options' => $status, 'empty' => 'ALL'));

				echo $this->Form->input('trial_flg', array('type' => 'select', 'class' => 'form-control', 'label' => '利用形態', 'options' => $trialFlgs, 'empty' => 'ALL'));

				echo $this->Form->input('member_id', array('type' => 'text', 'class' => 'form-control', 'label' => 'Id', 'placeholder' => 'Idから検索'));

				echo $this->Form->input('member_name', array('type' => 'text', 'class' => 'form-control', 'label' => 'メンバー名', 'placeholder' => 'メンバー名から検索'));
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
			</div>
			<div class="box-body table-responsive">
				<table cellpadding="0" cellspacing="0" class="table table-bordered table-hover dataTable">
				<thead>
				<tr>
					<th><?php echo $this->Paginator->sort('TrnTeams.member_id', 'Id'); ?></th>
					<th><?php echo $this->Paginator->sort('TrnTeams.shop_id', 'チーム名'); ?></th>
					<th><?php echo $this->Paginator->sort('TrnMembers.member_name', 'メンバー名'); ?></th>
					<th><?php echo $this->Paginator->sort('TrnMembers.email', 'Email'); ?></th>
					<?php if ($userSession['acc_grant'] == 0) { ?>
					<th><?php echo $this->Paginator->sort('TrnMembers.status', '状態'); ?></th>
					<?php } ?>
					<th><?php echo $this->Paginator->sort('TrnMembers.final_login_time', '最終<br />ログイン日時', array('escape' => false)); ?></th>
					<th class="actions" style="width: 116px;">&nbsp;</th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($data as $res): ?>
				<tr>
					<td><?php echo h($res['TrnMembers']['member_id']); ?>&nbsp;</td>
					<td><?php echo $this->Html->Link(h($res['MstAdminUser']['aname']), array('action' => 'shopView', $res['MstAdminUser']['id'])); ?>&nbsp;</td>
					<td><?php echo h($res['TrnMembers']['member_name']); ?>&nbsp;</td>
					<td><?php echo h($res['TrnMembers']['email']); ?>&nbsp;</td>
					<?php if ($userSession['acc_grant'] == 0) { ?>
					<td><?php echo h($res['Status']['name']); ?>&nbsp;</td>
					<?php } ?>
					<td><?php echo h($res['TrnMembers']['final_login_time']); ?>&nbsp;</td>
					<td class="actions">
						<?php echo $this->Html->link('詳細', array('action' => 'staffView', $res['TrnMembers']['member_id']), array('class' => 'btn btn-default btn-sm')); ?>
						<?php echo $this->Form->postLink('削除', array('action' => 'staffDelete', $res['TrnMembers']['member_id']), array('class' => 'btn btn-default btn-sm'), __('本当に削除してもよろしいですか # %s?', $res['TrnMembers']['member_id'])); ?>
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
