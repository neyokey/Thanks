<?php
$this->Html->script('jquery.validationEngine', array('inline' => false));
$this->Html->script('jquery.validationEngine-ja', array('inline' => false));
$this->Html->css('validationEngine.jquery', array('inline' => false));

$this->Html->script('jquery.datetimepicker.full.min', array('inline' => false));
$this->Html->css('jquery.datetimepicker.min', array('inline' => false));
$js = <<<EOF
$(function () {
	// jQuery Validation Engine
	jQuery("#PushForm").validationEngine('attach', {
		promptPosition:"bottomLeft"
	});

	$.datetimepicker.setLocale('ja');
	jQuery('#ReserveTime').datetimepicker({
		format: 'Y-m-d H:i',
		step:30
	});
});

EOF;
echo $this->Html->scriptBlock($js, array('block' => 'script'));

echo $this->Form->create('MstPuchReserve', array('id' => 'PushForm', 'inputDefaults' => array('div' => 'form-group', 'required' => false)));
?>
<div class="row">
	<div class="col-lg-12 col-md-12">
		<div class="box box-info">
			<div class="box-header">
				<h3 class="box-title"><?php echo $title; ?></h3>
			</div>
			<div class="box-body table-responsive">
				<?php
				echo '<div class="form-group"><label><i class="fa fa-square"></i>&nbsp;送信日時</label><div class="input-group date"><div class="input-group-addon"><li class="fa fa-calendar"></li></div>'.$this->Form->input('reserve_time', array('id' => 'ReserveTime', 'type' => 'text', 'label' => false, 'class' => 'validate[required] form-control', 'div' => false)).'</div></div>';

				echo $this->Form->input('message', array('label' => '<i class="fa fa-square"></i>&nbsp;送信メッセージ', 'class' => 'validate[required] form-control', 'placeholder' => 'thanks!'));
				?>
			</div>
			<div class="box-footer">
				<?php echo $this->Form->button('予約', array('type' => 'submit', 'class' => 'btn btn-primary')); ?>
			</div>
		</div>
	</div>
</div>
<?php echo $this->Form->end(); ?>
