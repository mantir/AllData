<div class="users form">
<?php echo $this->Form->create('User'); ?>
	<fieldset>
		<legend><?php echo __('Add/Edit User'); ?></legend>
	<?php
		echo $this->Form->input('id'); 
		echo $this->Form->input('url');
		echo $this->Form->input('name');
		echo $this->Form->input('claim');
		echo $this->Form->input('email');
		echo $this->Form->input('plz');
		echo $this->Form->input('Setting.plzPublic');
		echo $this->Form->input('realName');
		echo $this->Form->input('Setting.realNamePublic');
		echo $this->Form->input('address');
		echo $this->Form->input('Setting.addressPublic');	
		echo $this->Form->input('website');
		echo $this->Form->input('Setting.websitePublic');
		echo $this->Form->input('tel');
		echo $this->Form->input('Setting.telPublic');
		echo $this->Form->input('aboutMe');
		echo $this->Form->input('aboutMeEN');
		echo $this->Form->input('isArtist');
		echo $this->Form->input('image_id');
		echo $this->Form->input('Message');
		echo $this->Form->input('Group');
		echo $this->Form->input('Language');
		echo $this->Form->input('RecommendedPlaces');
		echo $this->Form->input('RecommendedUsers');
		echo $this->Form->input('RecommendedGroups');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('User.id')), null, __('Are you sure you want to delete # %s?', $this->Form->value('User.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Users'), array('action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('List Images'), array('controller' => 'images', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Image'), array('controller' => 'images', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Messages'), array('controller' => 'messages', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Message'), array('controller' => 'messages', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Groups'), array('controller' => 'groups', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Group'), array('controller' => 'groups', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Languages'), array('controller' => 'languages', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Language'), array('controller' => 'languages', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Places'), array('controller' => 'places', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Recommended Places'), array('controller' => 'places', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Users'), array('controller' => 'users', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Recommended Users'), array('controller' => 'users', 'action' => 'add')); ?> </li>
	</ul>
</div>
