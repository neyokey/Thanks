<?php
$this->Html->script('jquery.validationEngine', array('inline' => false));
$this->Html->script('jquery.validationEngine-ja', array('inline' => false));
$this->Html->css('validationEngine.jquery', array('inline' => false));
$js = <<<EOF
$(function () {
	// jQuery Validation Engine
	jQuery("#PointForm").validationEngine('attach', {
		promptPosition:"bottomLeft"
	});
});

EOF;
echo $this->Html->scriptBlock($js, array('block' => 'script'));

echo $this->Form->create('MstThanksStamp', array('id' => 'PointForm', 'type' => 'file', 'inputDefaults' => array('div' => 'form-group', 'required' => false)));
?>
<div class="row">
	<div class="col-lg-12 col-md-12">
		<div class="box box-info">
			<div class="box-body table-responsive">
				<p><code>※ご購入いただいた、Amazonギフト券（コードタイプ）のCSVファイルをこちらにアップロードしてください。</code></p>

				<p><code>※CSVファイルのフォーマットは下記のようになります</code></p>
				<dl class="dl-horizontal">
					<dt>Seq#</dt>
					<dd>シーケンスナンバー</dd>

					<dt>Claim Code</dt>
					<dd>ギフト券番号</dd>

					<dt>Amount(JPY)</dt>
					<dd>ギフト金額</dd>

					<dt>Expiraton Data(YYYY/MM/DD)</dt>
					<dd>有効期限</dd>

					<dt>SERIAL NUMBER</dt>
					<dd>シリアル番号</dd>
				</dl>

				<?php
				echo $this->Form->input('csv_file', array('type' => 'file', 'label' => '<i class="fa fa-square"></i>&nbsp;CSVファイル', 'class' => 'validate[required]'));
				?>
			</div>
			<div class="box-footer">
				<?php echo $this->Form->button('登録', array('type' => 'submit', 'class' => 'btn btn-primary')); ?>
			</div>
		</div>
	</div>
</div>
<?php echo $this->Form->end(); ?>
