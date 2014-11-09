<div class="floatL">
<?php echo $this->Form->create('User'); ?>
<fieldset>
<legend><?php echo __('Register'); ?></legend>
<?php
echo $this->Form->input('email');
echo $this->Form->input('password');
echo $this->Form->input('passwordRepeat', array('type' => 'password'));
/*
echo $this->Form->input('group_id', array(
	'options' => $groups
)); */
?>
</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>