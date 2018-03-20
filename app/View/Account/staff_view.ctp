<?php
$this->Html->script('plugins/datatables/jquery.dataTables.min', array('inline' => false));
$this->Html->script('plugins/datatables/dataTables.bootstrap.min', array('inline' => false));
$this->Html->css('plugins/datatables/dataTables.bootstrap.min', array('inline' => false));
$js = <<<EOF
$(function () {
	$('#point-data-table').DataTable({
		'paging'        : true,
		'lengthChange'  : false,
		'searching'     : false,
		'ordering'      : true,
		'info'          : true,
		'autoWidth'     : false,
		'order'         : [[0, 'desc']],
		'displayLength' : 20
	});
})

EOF;

echo $this->Html->scriptBlock($js, array('block' => 'script'));
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
						<td><?php echo h($data['TrnMembers']['member_id']); ?></td>
					</tr>
					<tr>
						<th>登録チーム</th>
						<td><ul class="list-unstyled"><?php
						if (version_compare(Configure::read('RELEASE_VERSION'), '3.1.1', '>=')) {
							foreach ($data['Shops'] as $res) {
								echo '<li>'.$this->Html->Link($res['MstAdminUser']['aname'], array('action' => 'shopView', $res['MstAdminUser']['id'])).'（保有ポイント：'.number_format($res['Point']).'pt）</li>';
							}
						} else {
							foreach ($data['Shops'] as $res) {
								echo '<li>'.$this->Html->Link($res['MstAdminUser']['aname'], array('action' => 'shopView', $res['MstAdminUser']['id'])).'</li>';
							}
						}
						?></ul></td>
					</tr>
					<tr>
						<th width="140px">メンバー名</th>
						<td><?php echo $data['TrnMembers']['member_name']; ?></td>
					</tr>
					<tr>
						<th>Email</th>
						<td><?php echo $data['TrnMembers']['email']; ?></td>
					</tr>
					<tr>
						<th>画像</th>
						<td><?php echo isset($data['TrnMembers']['profile_img_url']) ? $this->Html->Image(FULL_BASE_URL.'/api/'.$data['TrnMembers']['profile_img_url'], array('class' => 'img-responsive', 'style' => 'max-width: 240px;')) : '-'; ?></td>
					</tr>
					<tr>
						<th>生年月日</th>
						<td><?php echo $data['TrnMembers']['birthday']; ?></td>
					</tr>
					<tr>
						<th>勤務開始日</th>
						<td><?php echo $data['TrnMembers']['service_start_day']; ?></td>
					</tr>
					<tr>
						<th>自己紹介</th>
						<td><?php echo $data['TrnMembers']['self_introduction'] != null ? nl2br($data['TrnMembers']['self_introduction']) : '-'; ?></td>
					</tr>
					<tr>
						<th>趣味・好きなもの</th>
						<td><?php echo $data['TrnMembers']['interest'] != null ? nl2br($data['TrnMembers']['interest']) : '-'; ?></td>
					</tr>
					<tr>
						<th>デバイス区分</th>
						<td><?php echo $data['DeviceType']['name']; ?></td>
					</tr>
					<tr>
						<th>thanks通知フラグ</th>
						<td><?php echo $data['ThanksNoticeFlg']['name']; ?></td>
					</tr>
					<tr>
						<th>Birthday通知フラグ</th>
						<td><?php echo $data['BirthdayNoticeFlg']['name']; ?></td>
					</tr>
					<tr>
						<th>最終ログイン時間</th>
						<td><?php echo $data['TrnMembers']['final_login_time']; ?></td>
					</tr>
					<tr>
						<th>登録日時</th>
						<td><?php echo $data['TrnMembers']['insert_time']; ?></td>
					</tr>
					<tr>
						<th>更新日時</th>
						<td><?php echo $data['TrnMembers']['update_time']; ?></td>
					</tr>
				</table>
			</div>
			<div class="box-footer clearfix">
				<?php echo $this->Form->postLink('削除', array('action' => 'staffDelete', $data['TrnMembers']['member_id']), array('class' => 'btn btn-default'), __('本当に削除してもよろしいですか # %s?', $data['TrnMembers']['member_id'])); ?>
			</div>
		</div>
	</div>

	<?php if (version_compare(Configure::read('RELEASE_VERSION'), '3.1.1', '>=')) { ?>
	<div class="col-lg-12 col-md-12">
		<div class="box box-info">
			<div class="box-header">
				<h3 class="box-title">ポイント交換履歴</h3>
			</div>
			<div class="box-body">
				<table cellpadding="0" cellspacing="0" id="point-data-table" class="table table-bordered table-hover dataTable">
				<thead>
				<tr>
					<th>Id</th>
					<th>チーム</th>
					<th>アイテム</th>
					<th>申請pt</th>
					<th>申請日時</th>
					<th>ステータス</th>
					<th class="actions" style="width: 140px;">&nbsp;</th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($data['PointExchange'] as $res): ?>
				<tr>
					<td><?php echo $res['TrnPointExchange']['exchange_id']; ?>&nbsp;</td>
					<td><?php echo $this->Html->Link($res['MstAdminUser']['aname'], '/account/shopView/'.$res['MstAdminUser']['id']); ?>&nbsp;</td>
					<td><?php echo $res['MstPointItem']['item_name']; ?>&nbsp;</td>
					<td><?php echo number_format($res['TrnPointExchange']['exchange_point']).'pt'; ?>&nbsp;</td>
					<td><?php echo $res['TrnPointExchange']['request_datetime']; ?>&nbsp;</td>
					<td><?php
					$str = $res['Status'];
					if (isset($res['ExchangeResult'])) {
						$str .= '／'.$res['ExchangeResult'];
					}
					switch ($res['TrnPointExchange']['status']) {
						case 0: echo '<span class="label label-success">'.$str.'</span>'; break;
						case 1: echo '<span class="label label-warning">'.$str.'</span>'; break;
						case 2: echo '<span class="label label-primary">'.$str.'</span>'; break;
					}
					?>&nbsp;</td>
					<td class="actions">
						<?php
						switch ($res['MstPointItem']['item_id']) {
							case 1:
								echo $this->Html->Link('詳細', '/point/amazonView/'.$res['TrnPointExchange']['exchange_id'], array('class' => 'btn btn-default btn-sm'));
								break;
							case 2:
								echo $this->Html->Link('詳細', '/point/tpointView/'.$res['TrnPointExchange']['exchange_id'], array('class' => 'btn btn-default btn-sm'));
								break;
							case 3:
								echo $this->Html->Link('詳細', '/point/starbucksView/'.$res['TrnPointExchange']['exchange_id'], array('class' => 'btn btn-default btn-sm'));
								break;
						}
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
