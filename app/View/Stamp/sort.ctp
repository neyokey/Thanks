<?php
$js = <<<EOF
$(function() {
	$(".sortable").sortable();
	$(".sortable").disableSelection();
	$("#submit").click(function() {
		var result = $(".sortable").sortable("toArray");
		$("#result").val(result);
		$("form").submit();
	});
});

EOF;
echo $this->Html->script('https://code.jquery.com/ui/1.11.4/jquery-ui.min.js', array('inline' => false));
echo $this->Html->scriptBlock($js, array('block' => 'script'));
?>
<div class="row">
	<div class="col-lg-12 col-md-12">
		<div class="box box-info">
			<?php echo $this->Form->create('MstThanksStamp'); ?>
			<?php echo $this->Form->input('result', array('type' => 'hidden', 'value' => null, 'id' => 'result')); ?>
			<div class="box-body">
				<p>
					スタンプの並び順を更新します。<br />
					ドラッグ＆ドラップで並び替えた順番を「<code>登録</code>」ボタンを押下で保存します。
				</p>
				<ul class="sortable list-unstyled">
					<?php
					foreach ($data as $res){
						echo '<li id="'.$res['MstThanksStamp']['stamp_id'].'">'.$this->Html->Image('https://39s.work/api/'.$res['MstThanksStamp']['image_url'], array('class' => 'img-responsive', 'style' => 'width: 30%;')).'</li>';
					}
					?>
				</ul>
			</div>
			<div class="box-footer">
				<?php echo $this->Form->button('登録', array('class' => 'btn btn-success btn-lg', 'id' => 'submit')); ?>
			</div>
			<?php echo $this->Form->end(); ?>
		</div>
	</div>
</div>
