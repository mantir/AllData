<div class="floatL">
<?php echo $this->Form->create('User'); ?>
<fieldset>
<legend><?php echo __('Please enter your username and password'); ?></legend>
<?php
echo $this->Form->input('email');
echo $this->Form->input('password');
if($goto) echo $this->Form->input('goto', array('type' => 'hidden', 'value' => $goto));
?>
</fieldset>
<?php echo $this->Html->link(__('Register'), array('controller' => 'users', 'action' => 'register')); ?> - 
<?php echo $this->Html->link(__('Forgot password'), array('controller' => 'users', 'action' => 'resetPassword')); ?>
<?php echo $this->Form->end(__('Login')); ?>
</div>