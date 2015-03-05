<div class="exports index">
	<h2><?php echo __('Exports'); ?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id'); ?></th>
			<th><?php echo $this->Paginator->sort('name'); ?></th>
			<th><?php echo $this->Paginator->sort('project_id'); ?></th>
			<th><?php echo $this->Paginator->sort('value_ids'); ?></th>
			<th><?php echo $this->Paginator->sort('format'); ?></th>
			<th><?php echo $this->Paginator->sort('interval_type'); ?></th>
			<th><?php echo $this->Paginator->sort('interval_count'); ?></th>
			<th><?php echo $this->Paginator->sort('interval_start'); ?></th>
			<th><?php echo $this->Paginator->sort('start'); ?></th>
			<th><?php echo $this->Paginator->sort('end'); ?></th>
			<th><?php echo $this->Paginator->sort('dateformat'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
	foreach ($exports as $export): ?>
	<tr>
		<td><?php echo h($export['Export']['id']); ?>&nbsp;</td>
		<td><?php echo h($export['Export']['name']); ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($export['Project']['name'], array('controller' => 'projects', 'action' => 'view', $export['Project']['id'])); ?>
		</td>
		<td><?php echo h($export['Export']['value_ids']); ?>&nbsp;</td>
		<td><?php echo h($export['Export']['format']); ?>&nbsp;</td>
		<td><?php echo h($export['Export']['interval_type']); ?>&nbsp;</td>
		<td><?php echo h($export['Export']['interval_count']); ?>&nbsp;</td>
		<td><?php echo h($export['Export']['interval_start']); ?>&nbsp;</td>
		<td><?php echo h($export['Export']['start']); ?>&nbsp;</td>
		<td><?php echo h($export['Export']['end']); ?>&nbsp;</td>
		<td><?php echo h($export['Export']['dateformat']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $export['Export']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $export['Export']['id'])); ?>
			<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $export['Export']['id']), null, __('Are you sure you want to delete # %s?', $export['Export']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	));
	?>	</p>

	<div class="paging">
	<?php
		echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
	?>
	</div>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('New Export'), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__('List Projects'), array('controller' => 'projects', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project'), array('controller' => 'projects', 'action' => 'add')); ?> </li>
	</ul>
</div>
