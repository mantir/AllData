<div class="row">
	<h2 class="col-md-12"><?php echo __('Settings'); ?></h2>
    <div class="col-sm-3 col-md-3 col-lg-2">
		<?php echo $this->Form->create('User'); 
        ?>
        <?php
		echo $this->Form->input('name', console::$htmlInput);
		echo $this->Form->input('description', console::$htmlInput);
		/*?>?><h3>Change Password</h3><?
		echo $this->Form->input('old_password', array_merge(console::$htmlInput, array('type' => 'password')));
		echo $this->Form->input('password', array_merge(console::$htmlInput, array('type' => 'password')));
		echo $this->Form->input('passwordRepeat', array_merge(console::$htmlInput, array('type' => 'password')));
        ?><?php */?>
        <?= $this->Html->link(__('Change password'), array('action' => 'changePassword')); ?>
        <?php echo $this->Form->end(array('class' => 'btn btn-primary mt-10')); ?>
    </div>
</div>