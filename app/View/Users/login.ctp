<div class="row">
	<h2 class="col-md-12"><?php echo __('Login'); ?></h2>
    <div class="col-sm-3 col-md-3 col-lg-2">
		<?php echo $this->Form->create('User'); ?>
        
        <?php
        echo $this->Form->input('email', console::$htmlInput);
        echo $this->Form->input('password', console::$htmlInput);
        if($goto) echo $this->Form->input('goto', array('type' => 'hidden', 'value' => $goto));
        ?>
        <?php echo $this->Html->link(__('Register'), array('controller' => 'users', 'action' => 'register')); ?> - 
        <?php echo $this->Html->link(__('Forgot password'), array('controller' => 'users', 'action' => 'resetPassword')); ?>
        <?php echo $this->Form->end(array('class' => 'btn btn-primary mt-10')); ?>
    </div>
</div>