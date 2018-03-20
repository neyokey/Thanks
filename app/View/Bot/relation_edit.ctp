<?php
$this->Html->script('jquery.validationEngine', array('inline' => false));
$this->Html->script('jquery.validationEngine-ja', array('inline' => false));
$this->Html->css('validationEngine.jquery', array('inline' => false));
$js = <<<EOF
$(function () {
	// jQuery Validation Engine
	jQuery("#BotForm").validationEngine('attach', {
		promptPosition:"bottomLeft"
	});
});

EOF;
echo $this->Html->scriptBlock($js, array('block' => 'script'));

echo $this->Form->create('MstDiaryBotRelation', array('id' => 'BotForm', 'type' => 'file', 'inputDefaults' => array('div' => 'form-group', 'required' => false)));
echo $this->Form->input('TrnMembers.member_id', array('type' => 'hidden'));
?>
<div class="row">
	<div class="col-lg-12 col-md-12">
		<div class="box box-info">
			<div class="box-body">
				<?php
				echo $this->Form->input('TrnMembers.member_name', array('type' => 'text', 'label' => '<i class="fa fa-square"></i>&nbsp;アカウント名', 'class' => 'validate[required] form-control'));

				echo $this->Form->input('TrnMembers.profile_img_url', array('type' => 'file', 'label' => '<i class="fa fa-square"></i>&nbsp;写真'))
					.$this->Form->input('TrnMembers.hidden_profile_img_url', array('type' => 'hidden'));
				if (!empty($this->request->data['TrnMembers']['hidden_profile_img_url'])) {
					echo '<p><code>※写真を指定しない場合は、下記のモノが利用されます</code><br />'.$this->Html->Image(Configure::read('IMG_URL').$this->request->data['TrnMembers']['hidden_profile_img_url'], array('class' => 'img-circle', 'style' => 'max-width: 120px;')).'</p>';
				}

				echo $this->Form->input('TrnMembers.self_introduction', array('type' => 'textarea', 'label' => '<i class="fa fa-square"></i>&nbsp;自己紹介', 'class' => 'validate[required] form-control'));
				?>
				<div class="row" style="margin-bottom: 15px;">
					<div class="col-lg-12 col-md-12">
						<i class="fa fa-square"></i>&nbsp;投稿頻度
						<p><code>※Botを投稿する頻度について<br />
						　・毎月n日<br />
						　・毎週n曜日<br />
						　・n日に1回<br />
						のいづれかから選択してください。</code></p>

						<div class="input-group">
							<span class="input-group-addon"><input type="radio" name="data[MstDiaryBotRelation][type]" value="1" <?php if ($this->request->data['MstDiaryBotRelation']['type'] == 1) echo 'checked="checked"'; ?> /></span>
							<?php echo $this->Form->input('MstDiaryBotRelation.send_cycle_month_date', array('type' => 'select', 'options' => $type1, 'empty' => '毎月n日', 'class' => 'form-control', 'div' => false, 'label' => false)); ?>
						</div>
						<div class="input-group">
							<span class="input-group-addon"><input type="radio" name="data[MstDiaryBotRelation][type]" value="2" <?php if ($this->request->data['MstDiaryBotRelation']['type'] == 2) echo 'checked="checked"'; ?> /></span>
							<?php echo $this->Form->input('MstDiaryBotRelation.send_cycle_week_day', array('type' => 'select', 'options' => $type2, 'empty' => '毎週n曜日', 'class' => 'form-control', 'div' => false, 'label' => false)); ?>
						</div>
						<div class="input-group">
							<span class="input-group-addon"><input type="radio" name="data[MstDiaryBotRelation][type]" value="3" <?php if ($this->request->data['MstDiaryBotRelation']['type'] == 3) echo 'checked="checked"'; ?> /></span>
							<?php echo $this->Form->input('MstDiaryBotRelation.send_cycle_day_once', array('type' => 'select', 'options' => $type3, 'empty' => 'n日に1回', 'class' => 'form-control', 'div' => false, 'label' => false)); ?>
						</div>
					</div>
				</div>
				<?php
				echo $this->Form->input('MstDiaryBotRelation.send_method', array('type' => 'select', 'options' => $type4, 'label' => '<i class="fa fa-square"></i>&nbsp;配信方法', 'class' => 'validate[required] form-control'));

				echo $this->Form->input('MstDiaryBotRelation.send_loop_flg', array('type' => 'select', 'options' => $type5, 'label' => '<i class="fa fa-square"></i>&nbsp;ループ設定', 'class' => 'validate[required] form-control'));

				echo $this->Form->input('MstDiaryBotRelation.execute_timeh', array('type' => 'select', 'options' => $type6, 'label' => '<i class="fa fa-square"></i>&nbsp;配信時間', 'class' => 'validate[required] form-control'));
				?>
			</div>
			<div class="box-footer">
				<?php echo $this->Form->button('更新', array('type' => 'submit', 'class' => 'btn btn-primary')); ?>
			</div>
		</div>
	</div>
</div>
<?php echo $this->Form->end(); ?>
