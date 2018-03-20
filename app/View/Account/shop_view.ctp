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
	$('#stamp-data-table2').DataTable({
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
				<?php
				if (isset($data['TrialFlg']['id'])) {
					echo '<div class="box-tools"><span class="label label-success">'.$data['TrialFlg']['name'].'</span></div>';
				}
				?>
			</div>
			<div class="box-body no-padding">
				<table class="table table-hover">
					<tr>
						<th width="180px">Id</th>
						<td><?php echo h($data['MstAdminUser']['id']); ?></td>
					</tr>
					<tr>
						<th>企業名</th>
						<td><?php echo $this->Html->Link($data['Company']['aname'], array('action' => 'companyView', $data['Company']['id'])); ?></td>
					</tr>
					<tr>
						<th>チームコード</th>
						<td><?php echo h($data['MstAdminUser']['shop_AuthCode']); ?></td>
					</tr>
					<tr>
						<th>チーム名</th>
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

					<?php if (version_compare(Configure::read('RELEASE_VERSION'), '3.1.1', '>=')) { ?>

					<tr>
						<th>ポイント利用</th>
						<td><?php echo $data['PointStatus']; ?></td>
					</tr>
					<tr>
						<th>ポイント発行</th>
						<td><?php echo $data['PointChargeStatus']; ?></td>
					</tr>
					<tr>
						<th>ポイント交換</th>
						<td><?php echo $data['PointExchangeStatus']; ?></td>
					</tr>
					<tr>
						<th>サンクス送信獲得ポイント</th>
						<td><?php echo $data['MstAdminUser']['point_thanks_send'].'pt'; ?></td>
					</tr>
					<tr>
						<th>サンクス受信獲得ポイント</th>
						<td><?php echo $data['MstAdminUser']['point_thanks_receive'].'pt'; ?></td>
					</tr>
					<tr>
						<th>毎月ポイント交換上限</th>
						<td><?php echo $data['MstAdminUser']['point_exchange_limit'] == 0 ? '制限なし' : $data['MstAdminUser']['point_exchange_limit'].'ptまで'; ?></td>
					</tr>
					<tr>
						<th>ポイント交換アイテム</th>
						<td><ul><?php
						foreach ($pointItems as $item_id => $res) {
							if (isset($res['MstPointItemRelation']['id'])) {
								echo '<li>'.$res['MstPointItem']['item_name'].'</li>';
							}
						}
						?></ul></td>
					</tr>

					<?php } ?>

					<tr>
						<th>契約日時</th>
						<td><?php echo isset($data['MstAdminUser']['contract_date']) ? $data['MstAdminUser']['contract_date'] : '-'; ?></td>
					</tr>
					<tr>
						<th>解約日時</th>
						<td><?php echo isset($data['MstAdminUser']['cancellation_date']) ? $data['MstAdminUser']['cancellation_date'] : '-'; ?></td>
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
				echo $this->Html->Link('メンバー一覧', array('action' => 'staff', $data['MstAdminUser']['id']), array('class' => 'btn btn-primary'));
				if ($data['MstAdminUser']['status'] != 2) {
					echo '&nbsp;'.$this->Html->link('編集', array('action' => 'shopEdit', $data['MstAdminUser']['id']), array('class' => 'btn btn-info'));
					if ($userSession['acc_grant'] < 2) {
						switch ($data['MstAdminUser']['status']) {
							case 0:
								echo '&nbsp;'.$this->Form->postLink('一時停止処理', array('action' => 'shopStop', $data['MstAdminUser']['id']), array('class' => 'btn btn-default'), __('本当に一時停止してもよろしいですか # %s?', $data['MstAdminUser']['id']));
								break;
							case 1:
								echo '&nbsp;'.$this->Form->postLink('再稼働処理', array('action' => 'shopRestart', $data['MstAdminUser']['id']), array('class' => 'btn btn-default'), __('本当に再稼働してもよろしいですか # %s?', $data['MstAdminUser']['id']));
								break;
						}
//						echo '&nbsp;'.$this->Form->postLink('削除', array('action' => 'shopDelete', $data['MstAdminUser']['id']), array('class' => 'btn btn-default'), __('本当に削除してもよろしいですか # %s?', $data['MstAdminUser']['id']));
						echo '&nbsp;'.$this->Html->link('退会処理', array('action' => 'shopClose', $data['MstAdminUser']['id']), array('class' => 'btn btn-default'));
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
					<?php echo $this->Html->Link('新規追加', '/stamp/relationAdd/shop/'.$data['MstAdminUser']['id'], array('class' => 'btn btn-info btn-sm')); ?>
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
						echo $this->Form->postLink('固有スタンプの解除', '/stamp/relationDelOn/shop/'.$data['MstAdminUser']['id'].'/'.$res['MstThanksStamp']['stamp_id'], array('class' => 'btn btn-default btn-sm'), __('このスタンプを固有スタンプから解除してもよろしいですか # %s?', $res['MstThanksStamp']['stamp_id']));
						?>
					</td>
				</tr>
				<?php endforeach; ?>
				</tbody>
				</table>
			</div>
		</div>
	</div>

	<div class="col-lg-12 col-md-12">
		<div class="box box-info">
			<div class="box-header">
				<h3 class="box-title">BOT設定</h3>
				<div class="pull-right box-tools">
					<?php echo $this->Html->Link('新規追加', '/bot/relationChoice/'.$data['MstAdminUser']['id'], array('class' => 'btn btn-info btn-sm')); ?>
				</div>
			</div>
			<div class="box-body">
				<table cellpadding="0" cellspacing="0" id="stamp-data-table2" class="table table-bordered table-hover dataTable">
				<thead>
				<tr>
					<th>Bot-Id</th>
					<th>アカウント名</th>
					<th>配信頻度</th>
					<th>配信方法</th>
					<th>ループフラグ</th>
					<th>配信時間</th>
					<th class="actions" style="width: 160px;">&nbsp;</th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($bots as $res): ?>
				<tr>
					<td><?php echo $res['MstDiaryBotRelation']['bot_id']; ?>&nbsp;</td>
					<td><?php echo $res['TrnMembers']['member_name']; ?>&nbsp;</td>
					<td><?php echo $res['SendCycle']; ?>&nbsp;</td>
					<td><?php echo $res['SendMethod']; ?>&nbsp;</td>
					<td><?php echo $res['SendLoopFlg']; ?>&nbsp;</td>
					<td><?php echo $res['ExecuteTimeH']; ?>&nbsp;</td>
					<td class="actions">
						<?php
						echo $this->Html->link('詳細', '/bot/relationView/'.$data['MstAdminUser']['id'].'/'.$res['MstDiaryBotRelation']['bot_id'], array('class' => 'btn btn-success btn-sm'));

						echo '&nbsp;'.$this->Html->link('編集', '/bot/relationEdit/'.$data['MstAdminUser']['id'].'/'.$res['MstDiaryBotRelation']['bot_id'], array('class' => 'btn btn-default btn-sm'));

						echo '&nbsp;'.$this->Form->postLink('BOTの解除', '/bot/relationDelete/'.$data['MstAdminUser']['id'].'/'.$res['MstDiaryBotRelation']['bot_id'], array('class' => 'btn btn-default btn-sm'), __('このBOTを解除してもよろしいですか # %s?', $res['MstDiaryBotRelation']['bot_id']));
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
