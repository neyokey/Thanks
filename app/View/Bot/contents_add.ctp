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

echo $this->Form->create('MstDiaryBotContents', array('id' => 'BotForm', 'type' => 'file', 'inputDefaults' => array('div' => 'form-group', 'required' => false)));
?>
<div class="row">
	<div class="col-lg-12 col-md-12">
		<div class="box box-info">
			<div class="box-body">
				<div class="form-group">
					<i class="fa fa-square"></i>&nbsp;スタンプの選択
				</div>
				<div class="row">
					<div class="col-lg-12 col-md-12">
						<div class="nav-tabs-custom">
							<ul class="nav nav-tabs">
<?php
foreach ($stamps as $category_id => $res) {
	$class = $category_id === $act_categoryId ? 'active' : '';
	echo '<li class="'.$class.'"><a href="#tab_'.$category_id.'" data-toggle="tab">'.$res['category_name'].'</a></li>';
}
?>
							</ul>
							<div class="tab-content">
<?php
foreach ($stamps as $category_id => $res) {
	$class = $category_id === $act_categoryId ? 'tab-pane active' : 'tab-pane';
	echo '<div class="'.$class.'" id="tab_'.$category_id.'">';
	echo '<div class="row">';
	foreach ($res['data'] as $val) {
		$stamp_id = $val['MstThanksStamp']['stamp_id'];
		$checked = $stamp_id == $this->request->data['MstDiaryBotContents']['stamp_id'] ? 'checked="checked"' : '';
		echo '<div class="col-lg-4 col-md-4">
			<div class="form-group">
				<label class="col-sm-2 control-label"><input type="radio" id="MstDiaryBotContentsStampId'.$stamp_id.'" name="data[MstDiaryBotContents][stamp_id]" value="'.$stamp_id.'" '.$checked.' /></label>
				<div class="col-sm-10">
					<label for="MstDiaryBotContentsStampId'.$stamp_id.'">
					'.$this->Html->Image('https://39s.work/api/'.$val['MstThanksStamp']['image_url'], array('alt' => $val['MstThanksStamp']['stamp_name'], 'width' => '200px', 'height' => '80px')).'
					</label>
				</div>
			</div>
			</div>';
	}
	echo '</div>';
	echo '</div>';
}
?>
							</div>
						</div>
					</div>
				</div>
				<?php
				echo $this->Form->input('contents', array('type' => 'textarea', 'label' => '<i class="fa fa-square"></i>&nbsp;内容', 'class' => 'validate[required] form-control', 'placeholder' => 'BOTで出力させたいメッセージを入力してください。'));

				echo $this->Form->input('img_url', array('type' => 'file', 'label' => '<i class="fa fa-square"></i>&nbsp;写真'));
				?>
			</div>
			<div class="box-footer">
				<?php echo $this->Form->button('登録', array('type' => 'submit', 'class' => 'btn btn-primary')); ?>
			</div>
		</div>
	</div>
</div>
<?php echo $this->Form->end(); ?>
