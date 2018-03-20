<?php
$this->Html->script('jquery.validationEngine', array('inline' => false));
$this->Html->script('jquery.validationEngine-ja', array('inline' => false));
$this->Html->css('validationEngine.jquery', array('inline' => false));

$this->Html->script('plugins/iCheck/icheck.min', array('inline' => false));
$this->Html->css('plugins/iCheck/all', array('inline' => false));
$js = <<<EOF
$(function () {
	// jQuery Validation Engine
	jQuery("#PointForm").validationEngine('attach', {
		promptPosition:"bottomLeft"
	});

	//iCheck for checkbox and radio inputs
	$('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
		checkboxClass: 'icheckbox_flat-green',
		radioClass   : 'iradio_flat-green'
	})
});

EOF;
echo $this->Html->scriptBlock($js, array('block' => 'script'));

echo $this->Form->create('TrnPointExchange', array('id' => 'PointForm', 'type' => 'file', 'inputDefaults' => array('div' => 'form-group', 'required' => false)));
?>
<div class="row">
	<div class="col-lg-12 col-md-12">
		<div class="box box-info">
			<div class="box-body table-responsive">
				<p><code>※ダウンロードしてきたエラーCSVをこちらにアップロードしてください。</code></p>

				<?php
				echo $this->Form->input('csv_file', array('type' => 'file', 'label' => '<i class="fa fa-square"></i>&nbsp;CSVファイル', 'class' => 'validate[required]'));

				echo $this->Form->input('info_flg', array('type' => 'checkbox', 'class' => 'flat-red', 'label' => '&nbsp;Push／Mailでメンバーに通知する', 'checked' => true));
				?>
			</div>
			<div class="box-footer">
				<?php echo $this->Form->button('更新', array('type' => 'submit', 'class' => 'btn btn-primary')); ?>
			</div>
		</div>
	</div>
</div>
<?php echo $this->Form->end(); ?>
