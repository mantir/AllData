<div class="users form">
<?php echo $this->Form->create('User'); ?>
	<fieldset>
		<legend><?php echo __('Edit User'); ?></legend>
	<?php
		echo $this->Form->input('Setting.object_id', array('type' => 'hidden', 'value' => $this->request->data['User']['id']));
		echo $this->Form->input('id'); 
		echo $this->Form->input('url');
		echo $this->Form->input('name');
		echo $this->Form->input('claim');
		echo $this->Form->input('email');
		echo $this->Form->input('plz');
		echo $this->Form->input('Setting.plzPublic', array('type' => 'checkbox'));
		echo $this->Form->input('realName');
		echo $this->Form->input('Setting.realNamePublic', array('type' => 'checkbox'));
		echo $this->Form->input('address');
		echo $this->Form->input('Setting.addressPublic', array('type' => 'checkbox'));	
		echo $this->Form->input('website');
		echo $this->Form->input('Setting.websitePublic', array('type' => 'checkbox'));
		echo $this->Form->input('tel');
		echo $this->Form->input('Setting.telPublic', array('type' => 'checkbox'));
		echo $this->Form->input('aboutMe');
		echo $this->Form->input('aboutMeEN');
		echo $this->Form->input('Tag');
		echo $this->Form->input('New.Tag', array('type' => 'text', 'value' => ''));
		echo $this->Form->input('New.Genre.0.topgenre_id', array('label' => 'Top Genre', 'empty' => __('All')));
		echo $this->Form->input('New.Genre.0.name', array('label' => 'Sub Genre'));
		echo $this->Form->input('New.Genre.1.topgenre_id', array('label' => 'Top Genre', 'empty' => __('All')));
		echo $this->Form->input('New.Genre.1.name', array('label' => 'Sub Genre'));
		echo $this->Form->input('Setting.tagsPublic', array('type' => 'checkbox'));
		echo $this->Form->input('Setting.disableMessages', array('type' => 'checkbox'));
		echo $this->Form->input('Setting.genresPublic', array('type' => 'checkbox'));
		echo $this->Form->input('Language');
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
