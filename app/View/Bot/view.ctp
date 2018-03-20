<?php
$this->Html->script('plugins/datatables/jquery.dataTables.min', array('inline' => false));
$this->Html->script('plugins/datatables/dataTables.bootstrap.min', array('inline' => false));
$this->Html->css('plugins/datatables/dataTables.bootstrap.min', array('inline' => false));
$js = <<<EOF
$(function () {
	$('#stamp-data-table1').DataTable({
		'paging'        : true,
		'lengthChange'  : false,
		'searching'     : false,
		'ordering'      : true,
		'info'          : true,
		'autoWidth'     : false,
		'order'         : [[1, 'asc']],
		'displayLength' : 20
	});
})

EOF;
echo $this->Html->scriptBlock($js, array('block' => 'script'));
?>
<div class="row">
	<div class="col-lg-12 col-md-12">
		<div class="box box-info">
			<div class="box-body table-responsive no-padding">
				<table class="table table-hover">
					<tr>
						<th width="140px">BOT-ID</th>
						<td><?php echo h($data['MstDiaryBot']['bot_id']); ?></td>
					</tr>
					<tr>
						<th>BOT名</th>
						<td><?php echo $data['MstDiaryBot']['bot_name']; ?></td>
					</tr>
					<tr>
						<th>画像</th>
						<td><?php
						$profile_img_url = !empty($data['MstDiaryBot']['profile_img_url']) ? Configure::read('IMG_URL').$data['MstDiaryBot']['profile_img_url'] : 'noimg.jpg';
						echo  $this->Html->Image($profile_img_url, array('class' => 'img-circle', 'style' => 'max-width: 120px;'));
						 ?></td>
					</tr>
					<tr>
						<th>自己紹介</th>
						<td><?php echo nl2br($data['MstDiaryBot']['self_introduction']); ?></td>
					</tr>
				</table>
			</div>
			<div class="box-footer clearfix">
				<?php
				echo $this->Html->Link('編集', '/bot/edit/'.$data['MstDiaryBot']['bot_id'], array('class' => 'btn btn-default'));
				?>
			</div>
		</div>
	</div>
	<div class="col-lg-12 col-md-12">
		<div class="box box-info">
			<div class="box-header">
				<h3 class="box-title">コンテンツ一覧</h3>
			</div>
			<div class="box-body">
				<table cellpadding="0" cellspacing="0" id="stamp-data-table1" class="table table-bordered table-hover dataTable">
				<thead>
				<tr>
					<th>Id</th>
					<th>スタンプ</th>
					<th>内容</th>
					<th>写真</th>
					<th class="actions" style="width: 140px;">&nbsp;</th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($data2 as $res): ?>
				<tr>
					<td><?php echo h($res['MstDiaryBotContents']['contents_id']); ?></td>
					<td><?php echo $this->Html->Image('https://39s.work/api/'.$res['MstThanksStamp']['image_url'], array('class' => 'img-responsive', 'width' => '200px')); ?>&nbsp;</td>
					<td><?php echo nl2br($res['MstDiaryBotContents']['contents']); ?>&nbsp;</td>
					<td><?php echo !empty($res['MstDiaryBotContents']['img_url']) ? $this->Html->Image(Configure::read('IMG_URL').$res['MstDiaryBotContents']['img_url'], array('style' => 'max-width: 200px; max-height: 100px;')) : ''; ?>&nbsp;</td>
					<td class="actions">
						<?php
						echo $this->Html->link('編集', '/bot/contentsEdit/'.$data['MstDiaryBot']['bot_id'].'/'.$res['MstDiaryBotContents']['contents_id'], array('class' => 'btn btn-default btn-sm'));
						echo '&nbsp;'.$this->Form->postLink('削除', '/bot/contentsDelete/'.$data['MstDiaryBot']['bot_id'].'/'.$res['MstDiaryBotContents']['contents_id'], array('class' => 'btn btn-default btn-sm'), __('本当に削除してもよろしいですか # %s?', $res['MstDiaryBotContents']['contents_id']));
						?>
					</td>
				</tr>
				<?php endforeach; ?>
				</tbody>
				</table>
			</div>
			<div class="box-footer clearfix">
				<?php
				echo $this->Html->Link('コンテンツ追加', '/bot/contentsAdd/'.$data['MstDiaryBot']['bot_id'], array('class' => 'btn btn-info'));
				?>
			</div>
		</div>
	</div>
</div>
