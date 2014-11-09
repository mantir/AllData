<h1>Blog users</h1>
<?php echo $this->Html->link('Add user', array('controller' => 'users', 'action' => 'register')); ?>
<table>
	<? //var_dump($session); ?>
    <tr>
        <th>Id</th>
        <th>Username</th>
        <th>Actions</th>
        <th>Created</th>
    </tr>
    <?php foreach ($users as $user): ?>
    <tr>
        <td><?php echo $user['User']['id']; ?></td>
        <td>
        	<?php echo $this->Html->link($user['User']['name'].' ('.$user['User']['email'].')', array('action' => 'view', $user['User']['id'])); ?>
        </td>
        <td>
			<?=$this->Form->postLink('Login as', array('controller' => 'users', 'action' => 'loginAs', 'addy' => true, $user['User']['id'])) ?>
        </td>
        <td>
        	<?php echo $user['User']['created']; ?>
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
</p>