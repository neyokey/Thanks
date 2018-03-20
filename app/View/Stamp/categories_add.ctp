<?php
$this->Html->script('jquery.validationEngine', array('inline' => false));
$this->Html->script('jquery.validationEngine-ja', array('inline' => false));
$this->Html->css('validationEngine.jquery', array('inline' => false));
$js = <<<EOF
$(function () {
	// jQuery Validation Engine
	jQuery("#StampForm").validationEngine('attach', {
		promptPosition:"bottomLeft"
	});
});

EOF;
echo $this->Html->scriptBlock($js, array('block' => 'script'));

echo $this->Form->create('MstThanksStampCategory', array('id' => 'StampForm', 'type' => 'file', 'inputDefaults' => array('div' => 'form-group', 'required' => false)));
?>
<div class="row">
	<div class="col-lg-12 col-md-12">
		<div class="box box-info">
			<div class="box-body">
				<?php
				echo $this->Form->input('category_name', array('label' => '<i class="fa fa-square"></i>&nbsp;カテゴリ名', 'class' => 'validate[required] form-control', 'placeholder' => 'カテゴリ名を入力してください。'));
				?>
			</div>
			<div class="box-footer">
				<?php echo $this->Form->button('登録', array('type' => 'submit', 'class' => 'btn btn-primary')); ?>
			</div>
		</div>
	</div>
</div>
<?php echo $this->Form->end(); ?>
