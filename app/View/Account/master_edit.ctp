<?php
$this->Html->script('jquery.validationEngine', array('inline' => false));
$this->Html->script('jquery.validationEngine-ja', array('inline' => false));
$this->Html->css('validationEngine.jquery', array('inline' => false));
$js = <<<EOF
$(function () {
	// jQuery Validation Engine
	jQuery("#AccountForm").validationEngine('attach', {
		promptPosition:"bottomLeft"
	});
});

EOF;
echo $this->Html->scriptBlock($js, array('block' => 'script'));

echo $this->Form->create('MstAdminUser', array('id' => 'AccountForm', 'inputDefaults' => array('div' => 'form-group', 'required' => false)));
echo $this->Form->input('id', array('type' => 'hidden'));
?>
<div class="row">
	<div class="col-lg-12 col-md-12">
		<div class="box box-info">
			<div class="box-header">
				<h3 class="box-title"><?php echo $title; ?></h3>
			</div>
			<div class="box-body table-responsive">
				<?php
				echo $this->Form->input('aname', array('label' => '<i class="fa fa-square"></i>&nbsp;元売名', 'class' => 'validate[required] form-control', 'placeholder' => '株式会社Aruto'));

				echo $this->Form->input('amail', array('label' => '<i class="fa fa-square"></i>&nbsp;Email', 'class' => 'validate[required] form-control', 'placeholder' => 'sample@example.jp'));

				echo $this->Form->input('apass', array('type' => 'password', 'label' => '<i class="fa fa-square"></i>&nbsp;パスワード', 'class' => 'form-control'));

				echo $this->Form->input('atel', array('label' => '<i class="fa fa-square"></i>&nbsp;Tel', 'class' => 'form-control', 'placeholder' => '03-1234-5678'));

				echo $this->Form->input('memo', array('label' => '<i class="fa fa-square"></i>&nbsp;メモ', 'class' => 'form-control'));
				?>
			</div>
			<div class="box-footer">
				<?php echo $this->Form->button('保存', array('type' => 'submit', 'class' => 'btn btn-primary')); ?>
			</div>
		</div>
	</div>
</div>
<?php echo $this->Form->end(); ?>
