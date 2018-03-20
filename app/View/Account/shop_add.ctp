<?php
$this->Html->script('jquery.validationEngine', array('inline' => false));
$this->Html->script('jquery.validationEngine-ja', array('inline' => false));
$this->Html->css('validationEngine.jquery', array('inline' => false));

$this->Html->script('plugins/datepicker/bootstrap-datepicker', array('inline' => false));
$this->Html->css('plugins/datepicker/datepicker3', array('inline' => false));

$this->Html->script('plugins/iCheck/icheck.min', array('inline' => false));
$this->Html->css('plugins/iCheck/all', array('inline' => false));
$js = <<<EOF
$(function () {
	// jQuery Validation Engine
	jQuery("#AccountForm").validationEngine('attach', {
		promptPosition:"bottomLeft"
	});

	$('#contractDate').datepicker({
		format: "yyyy/mm/dd",
		autoclose: true
	});

	//iCheck for checkbox and radio inputs
	$('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
		checkboxClass: 'icheckbox_flat-green',
		radioClass   : 'iradio_flat-green'
	})
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
				echo $this->Form->input('chain_id', array('label' => '<i class="fa fa-square"></i>&nbsp;企業名', 'class' => 'validate[required] form-control', 'options' => $companies, 'empty' => '所属企業を選択してください。'));

				echo $this->Form->input('shop_AuthCode', array('label' => '<i class="fa fa-square"></i>&nbsp;チームコード', 'class' => 'form-control', 'readonly' => true));

				echo $this->Form->input('aname', array('label' => '<i class="fa fa-square"></i>&nbsp;チーム名', 'class' => 'validate[required] form-control', 'placeholder' => '株式会社Aruto'));

				echo $this->Form->input('amail', array('label' => '<i class="fa fa-square"></i>&nbsp;Email', 'class' => 'validate[required] form-control', 'placeholder' => 'sample@example.jp'));

				echo $this->Form->input('apass', array('type' => 'password', 'label' => '<i class="fa fa-square"></i>&nbsp;パスワード', 'class' => 'validate[required] form-control'));

				echo $this->Form->input('atel', array('label' => '<i class="fa fa-square"></i>&nbsp;Tel', 'class' => 'form-control', 'placeholder' => '03-6277-5885'));

				echo $this->Form->input('azip', array('label' => '<i class="fa fa-square"></i>&nbsp;郵便番号', 'class' => 'form-control', 'placeholder' => '150-0043'));

				echo $this->Form->input('aaddress', array('label' => '<i class="fa fa-square"></i>&nbsp;住所', 'class' => 'form-control', 'placeholder' => '東京都渋谷区道玄坂1-15-3 プリメーラ道玄坂512号'));

				if (version_compare(Configure::read('RELEASE_VERSION'), '3.1.1', '>=')) {
					
				echo $this->Form->input('point_status', array('type' => 'select', 'options' => $pointStatus, 'label' => '<i class="fa fa-square"></i>&nbsp;ポイント利用', 'class' => 'form-control'));

				echo $this->Form->input('point_charge_status', array('type' => 'select', 'options' => $pointChargeStatus, 'label' => '<i class="fa fa-square"></i>&nbsp;ポイント発行', 'class' => 'form-control'));

				echo $this->Form->input('point_exchange_status', array('type' => 'select', 'options' => $pointExchangeStatus, 'label' => '<i class="fa fa-square"></i>&nbsp;ポイント交換', 'class' => 'form-control'));

				echo '<div class="form-group"><label><i class="fa fa-square"></i>&nbsp;サンクス送信獲得ポイント</label><div class="input-group">'.$this->Form->input('point_thanks_send', array('type' => 'text', 'label' => false, 'class' => 'form-control', 'div' => false)).'<span class="input-group-addon">pt</span></div></div>';

				echo '<div class="form-group"><label><i class="fa fa-square"></i>&nbsp;サンクス受信獲得ポイント</label><div class="input-group">'.$this->Form->input('point_thanks_receive', array('type' => 'text', 'label' => false, 'class' => 'form-control', 'div' => false)).'<span class="input-group-addon">pt</span></div></div>';

				echo '<div class="form-group"><label><i class="fa fa-square"></i>&nbsp;毎月ポイント交換上限<br /><code>※上限を無効にする場合は「0」で登録してください</code></label><div class="input-group">'.$this->Form->input('point_exchange_limit', array('type' => 'text', 'label' => false, 'class' => 'form-control', 'div' => false)).'<span class="input-group-addon">ptまで</span></div></div>';

				echo '<div class="form-group"><label><i class="fa fa-square"></i>&nbsp;ポイント交換アイテム</label>';
				foreach ($pointItems as $item_id => $res) {
					echo $this->Form->input('MstPointItemRelation.'.$item_id.'.value', array('type' => 'checkbox', 'class' => 'flat-red', 'before' => '<label>', 'after' => '&nbsp;'.$res['MstPointItem']['item_name'].'</label>', 'value' => 1, 'label' => false, 'div' => array('class' => 'checkbox')));
				}
				echo '</div>';

				}

				echo '<div class="form-group"><label><i class="fa fa-square"></i>&nbsp;契約日</label><div class="input-group date"><div class="input-group-addon"><li class="fa fa-calendar"></li></div>'.$this->Form->input('contract_date', array('id' => 'contractDate', 'type' => 'text', 'label' => false, 'class' => 'form-control', 'div' => false)).'</div></div>';

				echo $this->Form->input('trial_flg', array('label' => '<i class="fa fa-square"></i>&nbsp;オプション設定', 'class' => 'form-control', 'options' => $trialFlgs));

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
