<?php echo $this->Form->create('TrnMembers', array('inputDefaults' => array('div' => 'form-group', 'label' => false, 'required' => false))); ?>
<div class="box box-success">
	<div class="box-body">
		<p>
			パスワード再設定用のメールを送信いたしますのでメールアドレスをご入力ください。
		</p>
		<?php echo $this->Form->input('email', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'Email', 'required' => true, 'div' => array('class' => 'form-group'))); ?>
	</div>
	<div class="box-footer"><input type="submit" value="送信" class="btn btn-success btn-block btn-flat" /></div>
</div>
<?php echo $this->Form->end(); ?>
