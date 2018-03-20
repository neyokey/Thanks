<?php
if ($userSession['acc_grant'] == 0) {
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
}
?>
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
						<th>代理店名</th>
						<td><?php echo $this->Html->Link($data['Agency']['aname'], array('action' => 'agencyView', $data['Agency']['id'])); ?></td>
					</tr>
					<tr>
						<th width="140px">企業名</th>
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
						<th>郵便番号</th>
						<td><?php echo $data['MstAdminUser']['azip']; ?></td>
					</tr>
					<tr>
						<th>住所</th>
						<td><?php echo $data['MstAdminUser']['aaddress']; ?></td>
					</tr>
					<tr>
						<th>支払区分</th>
						<td><?php echo $data['PaymenType'][0]['name'].$data['PaymenType'][1]['name']; ?></td>
					</tr>
					<tr>
						<th>1店舗の利用料</th>
						<td><?php echo number_format($data['MstAdminUser']['price']).'円／店舗'; ?></td>
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
				echo $this->Html->Link('チーム一覧', array('action' => 'shop', $data['MstAdminUser']['id']), array('class' => 'btn btn-primary'));
				echo '&nbsp;'.$this->Html->Link('支払金額確認', array('action' => 'companyBill', $data['MstAdminUser']['id']), array('class' => 'btn btn-primary'));
				if ($data['MstAdminUser']['status'] != 2) {
					echo '&nbsp;'.$this->Html->link('編集', array('action' => 'companyEdit', $data['MstAdminUser']['id']), array('class' => 'btn btn-info'));
					if ($userSession['acc_grant'] < 2) {
						switch ($data['MstAdminUser']['status']) {
							case 0:
								echo '&nbsp;'.$this->Form->postLink('一時停止処理', array('action' => 'companyStop', $data['MstAdminUser']['id']), array('class' => 'btn btn-default'), __('本当に一時停止してもよろしいですか # %s?', $data['MstAdminUser']['id']));
								break;
							case 1:
								echo '&nbsp;'.$this->Form->postLink('再稼働処理', array('action' => 'companyRestart', $data['MstAdminUser']['id']), array('class' => 'btn btn-default'), __('本当に再稼働してもよろしいですか # %s?', $data['MstAdminUser']['id']));
								break;
						}
//						echo '&nbsp;'.$this->Form->postLink('削除', array('action' => 'companyDelete', $data['MstAdminUser']['id']), array('class' => 'btn btn-default'), __('本当に削除してもよろしいですか # %s?', $data['MstAdminUser']['id']));
					}
					if ($userSession['acc_grant'] < 1) {
						echo '&nbsp;'.$this->Html->link('退会処理', array('action' => 'companyClose', $data['MstAdminUser']['id']), array('class' => 'btn btn-default'));
					}
				}
				?>
			</div>
		</div>
	</div>

	<?php if ($userSession['acc_grant'] == 0) { ?>
	<div class="col-lg-12 col-md-12">
		<div class="box box-info">
			<div class="box-header">
				<h3 class="box-title">固有スタンプ設定</h3>
				<div class="pull-right box-tools">
					<?php echo $this->Html->Link('新規追加', '/stamp/relationAdd/company/'.$data['MstAdminUser']['id'], array('class' => 'btn btn-info btn-sm')); ?>
				</div>
			</div>
			<div class="box-body">
				<table cellpadding="0" cellspacing="0" id="stamp-data-table1" class="table table-bordered table-hover dataTable">
				<thead>
				<tr>
					<th>Id</th>
					<th>Sort</th>
					<th>スタンプ名</th>
					<th>スタンプ画像</th>
					<th class="actions" style="width: 140px;">&nbsp;</th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($stamps as $res): ?>
				<tr>
					<td><?php echo h($res['MstThanksStamp']['stamp_id']); ?></td>
					<td><?php echo h($res['MstThanksStamp']['sort']); ?></td>
					<td><?php echo h($res['MstThanksStamp']['stamp_name']); ?>&nbsp;</td>
					<td><?php echo $this->Html->Image('https://39s.work/api/'.$res['MstThanksStamp']['image_url'], array('class' => 'img-responsive', 'style' => 'width: 50%;')); ?>&nbsp;</td>
					<td class="actions">
						<?php
						echo $this->Form->postLink('固有スタンプの解除', '/stamp/relationDelOn/company/'.$data['MstAdminUser']['id'].'/'.$res['MstThanksStamp']['stamp_id'], array('class' => 'btn btn-danger btn-sm'), __('このスタンプを固有スタンプから解除してもよろしいですか # %s?', $res['MstThanksStamp']['stamp_id']));
						?>
					</td>
				</tr>
				<?php endforeach; ?>
				</tbody>
				</table>
			</div>
		</div>
	</div>
	<?php } ?>
</div>
