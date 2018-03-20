<?php echo $this->Form->create('TrnMembers', array('inputDefaults' => array('div' => 'form-group', 'label' => false, 'required' => false))); ?>
<?php echo $this->Form->input('member_id', array('type' => 'hidden')); ?>
<div class="box box-success">
	<div class="box-header with-border">パスワードの再発行</div>
	<div class="box-body">
		<p>
			パスワードの再発行を行います。<br />
			新しいパスワードを入力してください。
		</p>
		<?php echo $this->Form->input('password', array('type' => 'password', 'class' => 'form-control', 'placeholder' => 'Password', 'required' => true, 'div' => array('class' => 'form-group'))); ?>
	</div>
	<div class="box-footer"><input type="submit" value="保存" class="btn btn-success btn-block btn-flat" /></div>
</div>
<?php echo $this->Form->end(); ?>
