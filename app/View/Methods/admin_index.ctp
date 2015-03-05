<div class="methods index">
	<h2><?php echo __('Methods'); ?> <?php echo $this->Html->link(__('+ new Method'), array('action' => 'add', 'admin' => false), array('class' => 'btn btn-primary btn-sm')); ?></h2>
	<table class="table table-condensed table-bordered table-hover" cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id'); ?></th>
			<th><?php echo $this->Paginator->sort('name'); ?></th>
			<th><?php echo $this->Paginator->sort('description'); ?></th>
			<th><?php echo $this->Paginator->sort('Project'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
	foreach ($methods as $method): ?>
	<tr>
		<td><?php echo h($method['Method']['id']); ?>&nbsp;</td>
		<td><?php echo $this->Html->link($method['Method']['name'], array('action' => 'edit', 'admin' => false, $method['Method']['id']),  array('escape' => false)); ?>&nbsp;</td>
		<td><?php echo h($method['Method']['description']); ?>&nbsp;</td>
		<td><?php echo h($method['Project']['name']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(console::$icons['edit'], array('action' => 'edit', 'admin' => false, $method['Method']['id']),  array('escape' => false)); ?>
			<?php echo $this->Form->postLink(console::$icons['delete'], array('action' => 'delete', 'admin' => false, $method['Method']['id']), array('escape' => false), __('Are you sure you want to delete # %s?', $method['Method']['id'])); ?>
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
