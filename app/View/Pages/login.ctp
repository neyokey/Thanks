<?php echo $this->Session->Flash('Auth'); ?>
<?php echo $this->Form->create('MstAdminUser', array('inputDefaults'=>array('label' => false, 'div' => false))); ?>
<div class="login-box">
	<div class="login-logo">
		<b>t</b>hanks!
	</div>
	<div class="login-box-body">
		<p class="login-box-msg">Sign in to start your session</p>

		<div class="form-group has-feedback">
			<?php echo $this->Form->input('amail', array('class' => 'form-control', 'placeholder' => 'Email')); ?>
			<span class="glyphicon glyphicon-envelope form-control-feedback"></span>
		</div>
		<div class="form-group has-feedback">
			<?php echo $this->Form->input('apass', array('type' => 'password', 'class' => 'form-control', 'placeholder' => 'Password')); ?>
			<span class="glyphicon glyphicon-lock form-control-feedback"></span>
		</div>
		<div class="row">
			<div class="col-xs-4">
				<?php echo $this->Form->submit('Sign In', array('class' => 'btn btn-primary btn-block btn-flat', 'div' => false)); ?>
			</div>
		</div>
	</div>
</div>
<?php echo $this->Form->end(); ?>
