<div class="users view">
<h2><?php  echo __('User'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($user['User']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Url'); ?></dt>
		<dd>
			<?php echo h($user['User']['url']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Name'); ?></dt>
		<dd>
			<?php echo h($user['User']['name']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Claim'); ?></dt>
		<dd>
			<?php echo h($user['User']['claim']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Email'); ?></dt>
		<dd>
			<?php echo h($user['User']['email']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Password'); ?></dt>
		<dd>
			<?php echo h($user['User']['password']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Plz'); ?></dt>
		<dd>
			<?php echo h($user['User']['plz']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('RealName'); ?></dt>
		<dd>
			<?php echo h($user['User']['realName']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Address'); ?></dt>
		<dd>
			<?php echo h($user['User']['address']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Website'); ?></dt>
		<dd>
			<?php echo h($user['User']['website']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Tel'); ?></dt>
		<dd>
			<?php echo h($user['User']['tel']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('AboutMe'); ?></dt>
		<dd>
			<?php echo h($user['User']['aboutMe']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('AboutMeEN'); ?></dt>
		<dd>
			<?php echo h($user['User']['aboutMeEN']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('IsArtist'); ?></dt>
		<dd>
			<?php echo h($user['User']['isArtist']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Image'); ?></dt>
		<dd>
			<?php echo $this->Html->link($user['Image']['id'], array('controller' => 'images', 'action' => 'view', $user['Image']['id'])); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
    	<li><?php echo $this->Form->postLink(__('Logout'), array('action' => 'logout')); ?> </li>
		<li><?php echo $this->Html->link(__('Edit User'), array('action' => 'edit', $user['User']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete User'), array('action' => 'delete', $user['User']['id']), null, __('Are you sure you want to delete # %s?', $user['User']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Users'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New User'), array('action' => 'add')); ?> </li>
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
<div class="related">
	<h3><?php echo __('Related Messages'); ?></h3>
	<?php if (!empty($user['Message'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Sender Id'); ?></th>
		<th><?php echo __('Text'); ?></th>
		<th><?php echo __('Time'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($user['Message'] as $message): ?>
		<tr>
			<td><?php echo $message['id']; ?></td>
			<td><?php echo $message['sender_id']; ?></td>
			<td><?php echo $message['text']; ?></td>
			<td><?php echo $message['time']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'messages', 'action' => 'view', $message['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'messages', 'action' => 'edit', $message['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'messages', 'action' => 'delete', $message['id']), null, __('Are you sure you want to delete # %s?', $message['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Message'), array('controller' => 'messages', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php echo __('Related Groups'); ?></h3>
	<?php if (!empty($user['Group'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Url'); ?></th>
		<th><?php echo __('Name'); ?></th>
		<th><?php echo __('Claim'); ?></th>
		<th><?php echo __('Creator Id'); ?></th>
		<th><?php echo __('CreateTime'); ?></th>
		<th><?php echo __('About'); ?></th>
		<th><?php echo __('AboutEN'); ?></th>
		<th><?php echo __('Open'); ?></th>
		<th><?php echo __('Plz'); ?></th>
		<th><?php echo __('Address'); ?></th>
		<th><?php echo __('Website'); ?></th>
		<th><?php echo __('Image Id'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($user['Group'] as $group): ?>
		<tr>
			<td><?php echo $group['id']; ?></td>
			<td><?php echo $group['url']; ?></td>
			<td><?php echo $group['name']; ?></td>
			<td><?php echo $group['claim']; ?></td>
			<td><?php echo $group['creator_id']; ?></td>
			<td><?php echo $group['createTime']; ?></td>
			<td><?php echo $group['about']; ?></td>
			<td><?php echo $group['aboutEN']; ?></td>
			<td><?php echo $group['open']; ?></td>
			<td><?php echo $group['plz']; ?></td>
			<td><?php echo $group['address']; ?></td>
			<td><?php echo $group['website']; ?></td>
			<td><?php echo $group['image_id']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'groups', 'action' => 'view', $group['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'groups', 'action' => 'edit', $group['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'groups', 'action' => 'delete', $group['id']), null, __('Are you sure you want to delete # %s?', $group['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Group'), array('controller' => 'groups', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php echo __('Related Languages'); ?></h3>
	<?php if (!empty($user['Language'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Name'); ?></th>
		<th><?php echo __('Short'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($user['Language'] as $language): ?>
		<tr>
			<td><?php echo $language['id']; ?></td>
			<td><?php echo $language['name']; ?></td>
			<td><?php echo $language['short']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'languages', 'action' => 'view', $language['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'languages', 'action' => 'edit', $language['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'languages', 'action' => 'delete', $language['id']), null, __('Are you sure you want to delete # %s?', $language['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Language'), array('controller' => 'languages', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php echo __('Related Places'); ?></h3>
	<?php if (!empty($user['RecommendedPlace'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Url'); ?></th>
		<th><?php echo __('Name'); ?></th>
		<th><?php echo __('Plz'); ?></th>
		<th><?php echo __('Address'); ?></th>
		<th><?php echo __('Lat'); ?></th>
		<th><?php echo __('Lng'); ?></th>
		<th><?php echo __('About'); ?></th>
		<th><?php echo __('AboutEN'); ?></th>
		<th><?php echo __('Type'); ?></th>
		<th><?php echo __('Creator Id'); ?></th>
		<th><?php echo __('Image Id'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($user['RecommendedPlace'] as $recommendedPlaces): ?>
		<tr>
			<td><?php echo $recommendedPlaces['id']; ?></td>
			<td><?php echo $recommendedPlaces['url']; ?></td>
			<td><?php echo $recommendedPlaces['name']; ?></td>
			<td><?php echo $recommendedPlaces['plz']; ?></td>
			<td><?php echo $recommendedPlaces['address']; ?></td>
			<td><?php echo $recommendedPlaces['lat']; ?></td>
			<td><?php echo $recommendedPlaces['lng']; ?></td>
			<td><?php echo $recommendedPlaces['about']; ?></td>
			<td><?php echo $recommendedPlaces['aboutEN']; ?></td>
			<td><?php echo $recommendedPlaces['type']; ?></td>
			<td><?php echo $recommendedPlaces['creator id']; ?></td>
			<td><?php echo $recommendedPlaces['image_id']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'places', 'action' => 'view', $recommendedPlaces['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'places', 'action' => 'edit', $recommendedPlaces['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'places', 'action' => 'delete', $recommendedPlaces['id']), null, __('Are you sure you want to delete # %s?', $recommendedPlaces['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Recommended Places'), array('controller' => 'places', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php echo __('Related Users'); ?></h3>
	<?php if (!empty($user['RecommendedUser'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Url'); ?></th>
		<th><?php echo __('Name'); ?></th>
		<th><?php echo __('Claim'); ?></th>
		<th><?php echo __('Email'); ?></th>
		<th><?php echo __('Password'); ?></th>
		<th><?php echo __('Plz'); ?></th>
		<th><?php echo __('RealName'); ?></th>
		<th><?php echo __('Address'); ?></th>
		<th><?php echo __('Website'); ?></th>
		<th><?php echo __('Tel'); ?></th>
		<th><?php echo __('AboutMe'); ?></th>
		<th><?php echo __('AboutMeEN'); ?></th>
		<th><?php echo __('IsArtist'); ?></th>
		<th><?php echo __('Image Id'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($user['RecommendedUser'] as $recommendedUsers): ?>
		<tr>
			<td><?php echo $recommendedUsers['id']; ?></td>
			<td><?php echo $recommendedUsers['url']; ?></td>
			<td><?php echo $recommendedUsers['name']; ?></td>
			<td><?php echo $recommendedUsers['claim']; ?></td>
			<td><?php echo $recommendedUsers['email']; ?></td>
			<td><?php echo $recommendedUsers['password']; ?></td>
			<td><?php echo $recommendedUsers['plz']; ?></td>
			<td><?php echo $recommendedUsers['realName']; ?></td>
			<td><?php echo $recommendedUsers['address']; ?></td>
			<td><?php echo $recommendedUsers['website']; ?></td>
			<td><?php echo $recommendedUsers['tel']; ?></td>
			<td><?php echo $recommendedUsers['aboutMe']; ?></td>
			<td><?php echo $recommendedUsers['aboutMeEN']; ?></td>
			<td><?php echo $recommendedUsers['isArtist']; ?></td>
			<td><?php echo $recommendedUsers['image_id']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'users', 'action' => 'view', $recommendedUsers['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'users', 'action' => 'edit', $recommendedUsers['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'users', 'action' => 'delete', $recommendedUsers['id']), null, __('Are you sure you want to delete # %s?', $recommendedUsers['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Recommended Users'), array('controller' => 'users', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php echo __('Related Groups'); ?></h3>
	<?php if (!empty($user['RecommendedGroup'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Url'); ?></th>
		<th><?php echo __('Name'); ?></th>
		<th><?php echo __('Claim'); ?></th>
		<th><?php echo __('Creator Id'); ?></th>
		<th><?php echo __('CreateTime'); ?></th>
		<th><?php echo __('About'); ?></th>
		<th><?php echo __('AboutEN'); ?></th>
		<th><?php echo __('Open'); ?></th>
		<th><?php echo __('Plz'); ?></th>
		<th><?php echo __('Address'); ?></th>
		<th><?php echo __('Website'); ?></th>
		<th><?php echo __('Image Id'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($user['RecommendedGroup'] as $recommendedGroups): ?>
		<tr>
			<td><?php echo $recommendedGroups['id']; ?></td>
			<td><?php echo $recommendedGroups['url']; ?></td>
			<td><?php echo $recommendedGroups['name']; ?></td>
			<td><?php echo $recommendedGroups['claim']; ?></td>
			<td><?php echo $recommendedGroups['creator_id']; ?></td>
			<td><?php echo $recommendedGroups['createTime']; ?></td>
			<td><?php echo $recommendedGroups['about']; ?></td>
			<td><?php echo $recommendedGroups['aboutEN']; ?></td>
			<td><?php echo $recommendedGroups['open']; ?></td>
			<td><?php echo $recommendedGroups['plz']; ?></td>
			<td><?php echo $recommendedGroups['address']; ?></td>
			<td><?php echo $recommendedGroups['website']; ?></td>
			<td><?php echo $recommendedGroups['image_id']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'groups', 'action' => 'view', $recommendedGroups['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'groups', 'action' => 'edit', $recommendedGroups['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'groups', 'action' => 'delete', $recommendedGroups['id']), null, __('Are you sure you want to delete # %s?', $recommendedGroups['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Recommended Groups'), array('controller' => 'groups', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
