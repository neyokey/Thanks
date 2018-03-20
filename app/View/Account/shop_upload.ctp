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

echo $this->Form->create('MstAdminUser', array('id' => 'AccountForm', 'type' => 'file', 'inputDefaults' => array('div' => 'form-group', 'required' => false)));
?>
<div class="row">
	<div class="col-lg-12 col-md-12">
		<div class="box box-info">
			<div class="box-header">
				<h3 class="box-title"><?php echo $title; ?></h3>
			</div>
			<div class="box-body table-responsive">
				<?php
				echo $this->Form->input('chain_id', array('label' => '<i class="fa fa-square"></i>&nbsp;企業名', 'class' => 'validate[required] form-control', 'options' => $companies, 'empty' => '所属企業を選択してください。'));

				echo '<p class="margin">CSVのファイルフォーマットは以下のようになります。</p><p class="margin"><code>チーム名、メールアドレス、パスワード、電話番号、郵便番号、住所、契約日</code></p>';
				echo $this->Form->input('csv_file', array('type' => 'file', 'label' => '<i class="fa fa-square"></i>&nbsp;CSVファイル', 'class' => 'validate[required]'));
				?>
			</div>
			<div class="box-footer">
				<?php echo $this->Form->button('一括登録', array('type' => 'submit', 'class' => 'btn btn-primary')); ?>
			</div>
		</div>
	</div>
</div>
<?php echo $this->Form->end(); ?>
