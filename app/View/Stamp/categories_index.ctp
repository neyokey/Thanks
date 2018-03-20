<div class="row">
	<div class="col-lg-2 col-md-2">
		<div class="box box-info">
			<div class="box-body">
				<div class="form-group"><?php echo $this->Html->Link('カテゴリを追加', array('action' => 'categories_add'), array('class' => 'btn btn-block btn-info btn-sm')); ?></div>
				<div class="form-group"><?php echo $this->Html->Link('スタンプを追加', array('action' => 'add'), array('class' => 'btn btn-block btn-info btn-sm')); ?></div>
			</div>
		</div>
	</div>
	<div class="col-lg-10 col-md-10">
		<div class="box box-info">
			<div class="box-body table-responsive">
				<table cellpadding="0" cellspacing="0" class="table table-bordered table-hover dataTable">
				<thead>
				<tr>
					<th>Id</th>
					<th>カテゴリ名</th>
					<th class="actions" style="width: 270px;">&nbsp;</th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($data as $res): ?>
				<tr>
					<td><?php echo $res['MstThanksStampCategory']['category_id']; ?>&nbsp;</td>
					<td><?php echo $res['MstThanksStampCategory']['category_name']; ?>&nbsp;</td>
					<td class="actions">
						<?php
						echo $this->Html->link('スタンプ一覧を見る', array('action' => 'index', $res['MstThanksStampCategory']['category_id']), array('class' => 'btn btn-success btn-sm'));
						echo '&nbsp;'.$this->Html->link('編集', array('action' => 'categories_edit', $res['MstThanksStampCategory']['category_id']), array('class' => 'btn btn-default btn-sm'));
#						echo '&nbsp;'.$this->Form->postLink('削除', array('action' => 'categories_del', $res['MstThanksStampCategory']['category_id']), array('class' => 'btn btn-default btn-sm'), __('本当に削除してもよろしいですか # %s?', $res['MstThanksStampCategory']['category_id']));
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
