<div class="logs form">
<?php echo $this->Form->create('Log'); ?>
	<fieldset>
		<legend><?php echo __('Add Log'); ?></legend>
	<?php
		echo $this->Form->input('type');
		echo $this->Form->input('title');
		echo $this->Form->input('time');
		echo $this->Form->input('info');
		echo $this->Form->input('link');
		echo $this->Form->input('dump');
		echo $this->Form->input('error');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Logs'), array('action' => 'index')); ?></li>
	</ul>
</div>
