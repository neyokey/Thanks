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
?>
<div class="row">
	<div class="col-lg-12 col-md-12">
		<div class="box box-info">
			<div class="box-header">
				<h3 class="box-title"><?php echo $title; ?></h3>
			</div>
			<div class="box-body table-responsive">
				<?php
				echo $this->Form->input('agency_id', array('label' => '<i class="fa fa-square"></i>&nbsp;代理店名', 'class' => 'validate[required] form-control', 'options' => $agencies, 'empty' => '所属代理店を選択してください。'));

				echo $this->Form->input('aname', array('label' => '<i class="fa fa-square"></i>&nbsp;企業名', 'class' => 'validate[required] form-control', 'placeholder' => '株式会社Aruto'));

				echo $this->Form->input('amail', array('label' => '<i class="fa fa-square"></i>&nbsp;Email', 'class' => 'validate[required] form-control', 'placeholder' => 'sample@example.jp'));

				echo $this->Form->input('apass', array('type' => 'password', 'label' => '<i class="fa fa-square"></i>&nbsp;パスワード', 'class' => 'validate[required] form-control'));

				echo $this->Form->input('atel', array('label' => '<i class="fa fa-square"></i>&nbsp;Tel', 'class' => 'form-control', 'placeholder' => '03-6277-5885'));

				echo $this->Form->input('azip', array('label' => '<i class="fa fa-square"></i>&nbsp;郵便番号', 'class' => 'form-control', 'placeholder' => '150-0043'));

				echo $this->Form->input('aaddress', array('label' => '<i class="fa fa-square"></i>&nbsp;住所', 'class' => 'form-control', 'placeholder' => '東京都渋谷区道玄坂1-15-3 プリメーラ道玄坂512号'));

				echo '<div><i class="fa fa-square"></i>&nbsp;支払区分<div class="row"><div class="col-lg-6">'.$this->Form->input('MstAdminUser.payment_type.0', array('label' => false, 'class' => 'validate[required] form-control', 'options' => $paymentTypes[0], 'empty' => '選択してください')).'</div><div class="col-lg-6">'.$this->Form->input('MstAdminUser.payment_type.1', array('label' => false, 'class' => 'validate[required] form-control', 'options' => $paymentTypes[1], 'empty' => '選択してください')).'</div></div></div>';

				echo $this->Form->input('price', array('label' => '<i class="fa fa-square"></i>&nbsp;1店舗の利用料', 'class' => 'validate[required,custom[integer]] form-control', 'placeholder' => '1000'));

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
