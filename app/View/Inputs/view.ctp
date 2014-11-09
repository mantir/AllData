<div class="inputs view">
<h2><?php  echo __('Input'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($input['Input']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Name'); ?></dt>
		<dd>
			<?php echo h($input['Input']['name']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Project'); ?></dt>
		<dd>
			<?php echo $this->Html->link($input['Project']['name'], array('controller' => 'projects', 'action' => 'view', $input['Project']['id'])); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Input'), array('action' => 'edit', $input['Input']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Input'), array('action' => 'delete', $input['Input']['id']), null, __('Are you sure you want to delete # %s?', $input['Input']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Inputs'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Input'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Projects'), array('controller' => 'projects', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project'), array('controller' => 'projects', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Values'), array('controller' => 'values', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Value'), array('controller' => 'values', 'action' => 'add')); ?> </li>
	</ul>
</div>
<div class="related">
	<h3><?php echo __('Related Values'); ?></h3>
	<?php if (!empty($input['Value'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Name'); ?></th>
		<th><?php echo __('Project Id'); ?></th>
		<th><?php echo __('Unit'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($input['Value'] as $value): ?>
		<tr>
			<td><?php echo $value['id']; ?></td>
			<td><?php echo $value['name']; ?></td>
			<td><?php echo $value['project_id']; ?></td>
			<td><?php echo $value['unit']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'values', 'action' => 'view', $value['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'values', 'action' => 'edit', $value['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'values', 'action' => 'delete', $value['id']), null, __('Are you sure you want to delete # %s?', $value['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Value'), array('controller' => 'values', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
