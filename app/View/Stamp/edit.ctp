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

echo $this->Form->create('MstThanksStamp', array('id' => 'StampForm', 'type' => 'file', 'inputDefaults' => array('div' => 'form-group', 'required' => false)));
echo $this->Form->input('stamp_id', array('type' => 'hidden'));
?>
<div class="row">
	<div class="col-lg-12 col-md-12">
		<div class="box box-info">
			<div class="box-header">
				<h3 class="box-title"><?php echo $title; ?></h3>
			</div>
			<div class="box-body table-responsive">
				<p><code>※スタンプ画像の仕様<br />【SIZE:720pxＸ280px】<br />【容量の目安:30K～50K（画像が劣化しない程度に圧縮してください）】<br />【画像ファイル形式:JPGE（拡張子はJPG）】</code></p>
				<?php
				echo $this->Form->input('stamp_name', array('label' => '<i class="fa fa-square"></i>&nbsp;スタンプ名', 'class' => 'validate[required] form-control', 'placeholder' => 'thanks!'));

				echo $this->Form->input('image_url', array('type' => 'file', 'label' => '<i class="fa fa-square"></i>&nbsp;スタンプ画像'));

				echo $this->Form->input('category_id', array('label' => '<i class="fa fa-square"></i>&nbsp;カテゴリ', 'class' => 'validate[required] form-control', 'options' => $categories));

				echo $this->Form->input('status', array('label' => '<i class="fa fa-square"></i>&nbsp;状態', 'class' => 'validate[required] form-control', 'options' => $status));
				?>
			</div>
			<div class="box-footer">
				<?php echo $this->Form->button('保存', array('type' => 'submit', 'class' => 'btn btn-primary')); ?>
			</div>
		</div>
	</div>
</div>
<?php echo $this->Form->end(); ?>
