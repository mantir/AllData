<div class="row">
	<h2 class="col-md-12"><?php echo __('Register'); ?></h2>
    <div class="col-sm-3 col-md-3 col-lg-2">
		<?php echo $this->Form->create('User'); ?>
        
        <?php
		echo $this->Form->input('name', console::$htmlInput);
        echo $this->Form->input('email', console::$htmlInput);
		echo $this->Form->input('password', console::$htmlInput);
		echo $this->Form->input('passwordRepeat', array_merge(console::$htmlInput, array('type' => 'password')));
        ?>
        <?php echo $this->Html->link(__('Login'), array('controller' => 'users', 'action' => 'login')); ?> - 
        <?php echo $this->Html->link(__('Forgot password'), array('controller' => 'users', 'action' => 'resetPassword')); ?>
        <?php echo $this->Form->end(array('class' => 'btn btn-primary mt-10')); ?>
    </div>
</div>