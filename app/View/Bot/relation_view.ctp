<div class="row">
	<div class="col-lg-12 col-md-12">
		<div class="box box-info">
			<div class="box-body table-responsive no-padding">
				<table class="table table-hover">
					<tr>
						<th width="140px">BOT-ID</th>
						<td><?php echo $data['MstDiaryBotRelation']['bot_id']; ?></td>
					</tr>
					<tr>
						<th>アカウント名</th>
						<td><?php echo $data['TrnMembers']['member_name']; ?></td>
					</tr>
					<tr>
						<th>写真</th>
						<td><?php
						$profile_img_url = !empty($data['TrnMembers']['profile_img_url']) ? Configure::read('IMG_URL').$data['TrnMembers']['profile_img_url'] : 'noimg.jpg';
						echo  $this->Html->Image($profile_img_url, array('class' => 'img-circle', 'style' => 'max-width: 120px;'));
						 ?></td>
					</tr>
					<tr>
						<th>自己紹介</th>
						<td><?php echo nl2br($data['TrnMembers']['self_introduction']); ?></td>
					</tr>
					<tr>
						<th>配信頻度</th>
						<td><?php echo $data['SendCycle']; ?></td>
					</tr>
					<tr>
						<th>配信方法</th>
						<td><?php echo $data['SendMethod']; ?></td>
					</tr>
					<tr>
						<th>ループフラグ</th>
						<td><?php echo $data['SendLoopFlg']; ?></td>
					</tr>
					<tr>
						<th>配信時間</th>
						<td><?php echo $data['ExecuteTimeH']; ?></td>
					</tr>
				</table>
			</div>
			<div class="box-footer clearfix">
				<?php
				echo $this->Html->link('Botの詳細', '/bot/view/'.$data['MstDiaryBotRelation']['bot_id'], array('class' => 'btn btn-info'));

				echo '&nbsp;'.$this->Html->link('編集', '/bot/relationEdit/'.$data['MstDiaryBotRelation']['shop_id'].'/'.$data['MstDiaryBotRelation']['bot_id'], array('class' => 'btn btn-default'));

				echo '&nbsp;'.$this->Form->postLink('解除', '/bot/relationDelete/'.$data['MstDiaryBotRelation']['shop_id'].'/'.$data['MstDiaryBotRelation']['bot_id'], array('class' => 'btn btn-default'), __('このBOTを解除してもよろしいですか # %s?', $data['MstDiaryBotRelation']['bot_id']));
				?>
			</div>
		</div>
	</div>
</div>
