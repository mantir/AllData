<div class="row">
	<h2 class="col-md-12"><?php echo __('Change your password'); ?></h2>
    <div class="col-sm-3 col-md-3 col-lg-2">
		<?php echo $this->Form->create('User'); ?>
        <?php
		echo $this->Form->input('old_password', array_merge(console::$htmlInput, array('autocomplete' => 'off', 'value' => '','type' => 'password', 'label' => __('Old password'))));
		?><hr /><?
		echo $this->Form->input('password', array_merge(console::$htmlInput, array('value' => '', 'type' => 'password', 'label' => __('New password'))));
		echo $this->Form->input('password_repeat', array_merge(console::$htmlInput, array('value' => '','type' => 'password', 'label' => __('Repeat new password'))));
        ?>
        <?php echo $this->Form->end(array('class' => 'btn btn-primary mt-10')); ?>
    </div>
</div>