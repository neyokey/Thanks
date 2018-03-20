<div class="row">
	<div class="col-lg-12 col-md-12">
		<div class="box box-info">
			<div class="box-header">
				<h3 class="box-title"><?php echo $title; ?></h3>
			</div>
			<div class="box-body table-responsive no-padding">
				<table class="table table-hover">
					<tr>
						<th>Id</th>
						<td><?php echo h($data['MstAdminUser']['id']); ?></td>
					</tr>
					<tr>
						<th width="140px">元売名</th>
						<td><?php echo $data['MstAdminUser']['aname']; ?></td>
					</tr>
					<tr>
						<th>Email</th>
						<td><?php echo $data['MstAdminUser']['amail']; ?></td>
					</tr>
					<tr>
						<th>Tel</th>
						<td><?php echo $data['MstAdminUser']['atel']; ?></td>
					</tr>
					<tr>
						<th>メモ</th>
						<td><?php echo nl2br($data['MstAdminUser']['memo']); ?></td>
					</tr>
					<tr>
						<th>登録日時</th>
						<td><?php echo $data['MstAdminUser']['insert_time']; ?></td>
					</tr>
					<tr>
						<th>更新日時</th>
						<td><?php echo $data['MstAdminUser']['update_time']; ?></td>
					</tr>
				</table>
			</div>
			<div class="box-footer clearfix">
				<?php
				echo $this->Html->link('編集', array('action' => 'masterEdit', $data['MstAdminUser']['id']), array('class' => 'btn btn-info'));
				echo '&nbsp;'.$this->Form->postLink('削除', array('action' => 'masterDelete', $data['MstAdminUser']['id']), array('class' => 'btn btn-default'), __('本当に削除してもよろしいですか # %s?', $data['MstAdminUser']['id']));
				?>
			</div>
		</div>
	</div>
</div>
