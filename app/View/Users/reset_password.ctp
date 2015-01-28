<? if($allowReset) : ?>
<div class="row">
	<h2 class="col-md-12"><?php echo __('Please enter your new password.'); ?></h2>
    <div class="col-sm-3 col-md-3 col-lg-2">
		<?php echo $this->Form->create('User'); ?>
        <?php
		echo $this->Form->input('password', console::$htmlInput);
		echo $this->Form->input('passwordRepeat', array_merge(console::$htmlInput, array('type' => 'password')));
        ?>
        <?php echo $this->Html->link(__('Login'), array('controller' => 'users', 'action' => 'login')); ?> - 
        <?php echo $this->Html->link(__('Register'), array('controller' => 'users', 'action' => 'register')); ?>
        <?php echo $this->Form->end(array('caption' => 'ddd', 'class' => 'btn btn-primary mt-10')); ?>
    </div>
</div><?
else : 
?>
<div class="row">
	<h2 class="col-md-12"><?php echo __('Please enter your email address');  ?></h2>
    <div class="col-sm-3 col-md-3 col-lg-2">
		<?php echo $this->Form->create('User'); ?>
        <?php
		echo $this->Form->input('email', console::$htmlInput);
        ?>
        <?php echo $this->Html->link(__('Login'), array('controller' => 'users', 'action' => 'login')); ?> - 
        <?php echo $this->Html->link(__('Register'), array('controller' => 'users', 'action' => 'register')); ?>
        <?php echo $this->Form->end(array('label' => __('Send reset password'), 'class' => 'btn btn-primary mt-10')); ?>
    </div>
</div>
<? endif; ?>