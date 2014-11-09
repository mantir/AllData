<? if($allowReset) : ?>
<div><?
	echo $this->Form->create('User'); ?>
    <fieldset>
    <legend><?php echo __('Please enter your new password.'); ?></legend>
    <?php
    	echo $this->Form->input('password');
		echo $this->Form->input('passwordRepeat', array('type' => 'password', 'label' => __('Repeat password')));
		
    ?>
    </fieldset>
    <?php echo $this->Html->link(__('Register'), array('controller' => 'users', 'action' => 'register')); ?> - 
    <?php echo $this->Html->link(__('Login'), array('controller' => 'users', 'action' => 'login')); ?>
    <?php echo $this->Form->end(__('Reset password')); 
?></div><?
else : 
?>
<div>
	<?php echo $this->Form->create('User'); ?>
    <fieldset>
    <legend><?php echo __('Please enter your email address'); ?></legend>
    <?php
    echo $this->Form->input('email', array('label' => 'email address'));
    ?>
    </fieldset>
    <?php echo $this->Html->link(__('Register'), array('controller' => 'users', 'action' => 'register')); ?> - 
    <?php echo $this->Html->link(__('Login'), array('controller' => 'users', 'action' => 'login')); ?>
    <?php echo $this->Form->end(__('Send activation email')); ?>
</div>
<? endif; ?>