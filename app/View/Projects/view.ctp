<?
	$table_class = 'table table-condensed table-bordered table-hover table-striped';
?><div class="row">
	<div class="col-md-12">
    	<h2><?php  echo $project['Project']['name'] ?> <?php echo $this->Html->link(__('Edit project'), array('action' => 'edit', $project['Project']['id']), array('class' => 'btn btn-primary btn-sm ml-10')); ?> <?php echo $this->Html->link('<span class="glyphicon glyphicon-upload mr-5"></span> '.__('Import measured data'), array('action' => 'upload_import', $project['Project']['id']), array('escape' => false, 'class' => 'btn btn-primary btn-sm')); ?> <?php echo $this->Html->link('<span class="glyphicon glyphicon-stats mr-5"></span> '.__('Show data'), array('action' => 'data', $project['Project']['id']), array('escape' => false, 'class' => 'btn btn-primary btn-sm')); ?> 
        </h2>
	</div>
</div>
<div class="row">
    <div class="col-md-6">
        
        <table class="<?=$table_class?>">
        <tr>
        	<th width="30" align="center"><?php echo $this->Html->link(console::$icons['add'], array('controller' => 'values', 'action' => 'add', $project['Project']['id']), array('class' => 'ml-10',  'escape' => false)); ?></th>
            <th><h3><?php echo __('Values'); ?></h3></th>
            <?php if (!empty($project['Value'])): ?>
            <th><?php echo __('Unit'); ?></th>
            <th><?php echo __('Input'); ?></th>
            <th><?php echo __('Min.'); ?></th>
            <th><?php echo __('Max.'); ?></th>
            <? endif; ?>
        </tr>
        <?php if (!empty($project['Value'])): ?>
        <?php
            $i = 0;
            foreach ($project['Value'] as $i => $value): ?>
            <tr>
                <td width="30" align="center"><?php echo $i + 1; ?></td>
                <td>
					<div class="btn-group pull-right">
					<?php echo $this->Html->link(console::$icons['edit'], array('controller' => 'values', 'action' => 'edit', $value['id']), array('escape' => false)); ?>
                   	</div>
                    <?php echo $this->Html->link($value['name'], array('controller' => 'projects', 'action' => 'data', $value['project_id'], '?' => 'values[]='.$value['id'])); ?>
                </td>
                <td title="<?php echo console::$unit_prefixes[$value['prefix']].($value['Unit']['name']); ?>"><?php echo $value['prefix'].$value['Unit']['symbol']; ?></td>
                <td><?php 
					$lim = ''; 
					if(count($value['Input'])) {
						foreach($value['Input'] as $input) { 
							echo $lim.$this->Html->link($input['name'], array('controller' => 'inputs', 'action' => 'edit', $input['id'])); $lim = ', ';
						}
					} 
					if($value['Method']) {
						echo $lim;
						if($value['Method']['project_id'])
							echo $this->Html->link($value['execName'], array('controller' => 'methods', 'action' => 'edit', $value['Method']['id']));
						else
							echo $value['execName'];
					}
				?>	
                </td>
                <td><?php echo $value['minimum'] ?></td>
                <td><?php echo $value['maximum'] ?></td>
            </tr>
        <?php endforeach; ?>
		<?php endif; ?>
        </table>
    </div>
    <div class="col-md-6">
    	<div class="row">
            <div class="col-md-12">
                <h3 id="log-selector" class="section-headline">
                	<a data-log="import" title="<?=__('Imports')?>"><i class="glyphicon glyphicon-upload"></i></a>
                    <a data-log="data" title="<?=__('Data issues')?>"><i class="mr-10 glyphicon glyphicon-stats"></i></a>
                    <a data-log="user" title="<?=__('User actions')?>"><i class="mr-10 fa fa-user"></i></a>
					<?php echo __('Logs'); ?>
                </h3>
                <div id="log-container">
                    <table id="logs" class="<?=$table_class?> table-hover">
                        
                    </table>
                </div>
            </div>
        </div>
        <div class="row">
    		<div class="col-md-12">
                <table class="<?=$table_class?>">
                <tr>
                    <th colspan="2"><h3><?php echo __('Inputs'); ?></h3></th>
                    <?php if (!empty($project['Input'])): ?><th><?php echo __('Type'); ?></th><?php endif; ?>
                    <th class="actions">
                        <?php echo $this->Html->link(console::$icons['add'], array('controller' => 'inputs', 'action' => 'add', $project['Project']['id']), array('class' => 'ml-10',  'escape' => false)); ?>
                    </th>
                </tr>
                <?php
                    $i = 0;
                    foreach ($project['Input'] as $i => $input): ?>
                    <tr>
                        <td width="30" align="center"><?php echo $i + 1; ?></td>
                        <td><?php echo $this->Html->link($input['name'], array('controller' => 'inputs', 'action' => 'edit', $input['id'])); ?></td>
                        <td><?php echo $input['type']; ?></td>
                        <td class="actions">
                            <?php echo $this->Form->postLink(console::$icons['delete'], array('controller' => 'inputs', 'action' => 'delete', $input['id']),  array('escape' => false, 'title' => __('Delete')), __('Are you sure you want to delete the input %s?', $input['name'])); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </table>
            </div>
    	</div>
        <div class="row">
            <div class="col-md-12">
                    <table class="<?=$table_class?>">
                    <tr>
                        <th colspan="2"><h3><?php echo __('Exports'); ?></h3></th>
                        <?php if (!empty($project['Export'])): ?><th><?php echo __('Format'); ?></th><?php endif; ?>
                        <th class="actions">
                        	<?php echo $this->Html->link(console::$icons['add'], array('controller' => 'exports', 'action' => 'add', $project['Project']['id']), array('class' => 'ml-10',  'escape' => false)); ?>
                        </th>
                    </tr>
                    <?php
                        $i = 0;
                        foreach ($project['Export'] as $i => $export): ?>
                        <tr>
                            <td width="30" align="center"><?php echo $i + 1; ?></td>
                            <td><?php echo $this->Html->link($export['name'], array('controller' => 'exports', 'action' => 'view', $export['id'])); ?></td>
                            <td><?php echo $export['format']; ?></td>
                            <td class="actions">
                            	<?php echo $this->Html->link(console::$icons['edit'], array('controller' => 'exports', 'action' => 'edit', $export['id']),  array('escape' => false, 'title' => __('Edit'))); ?>
                                <?php echo $this->Form->postLink(console::$icons['delete'], array('controller' => 'exports', 'action' => 'delete', $export['id']), array('escape' => false, 'title' => __('Delete')), __('Are you sure you want to delete the export %s from the project?', $export['name'])); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </table>
            	
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <table class="<?=$table_class?>">
                <tr>
                    <th colspan="2"><h3><?php echo __('Project members'); ?></h3></th>
                    <th><?php echo __('Type'); ?></th>
                    <th class="actions"><?php echo $this->Html->link(console::$icons['add'], array('controller' => 'projects', 'action' => 'invite', $project['Project']['id']), array('class' => 'ml-10', 'escape' => false)); ?></th>
                </tr>
                <?php
                    $i = 0;
                    foreach ($project['Member'] as $i => $member): ?>
                    <tr>
                        <td width="30" align="center"><?php echo $i + 1; ?></td>
                        <td><?php echo $this->Html->link($member['name'], array('controller' => 'users', 'action' => 'view', $member['id'])); ?></td>
                        <td><?php echo console::$memberTypes[$member['ProjectsUser']['state']]; ?></td>
                        <td class="actions">
                            <?php 
                                if($member['id'] != $project['Project']['user_id'] && !$authUser['isGuest']) {
                                    echo $this->Form->postLink(__('Uninvite'), array('controller' => 'projects', 'action' => 'uninvite', $project['Project']['id'], $member['id']), 
                                        null,
                                         __('Are you sure you want to uninvite %s from the project?', $member['name'])
                                     ); 
                                } ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                    <table class="<?=$table_class?>">
                    <tr>
                        <th colspan="2"><h3><?php echo __('Methods'); ?></h3></th>
                        <?php if (!empty($project['Method'])): ?><th><?php echo __('Description'); ?></th><? endif; ?>
                        <th class="actions">
                        	<?php echo $this->Html->link(console::$icons['add'], array('controller' => 'methods', 'action' => 'add', $project['Project']['id']), array('class' => 'ml-10',  'escape' => false)); ?>
                        </th>
                    </tr>
                    <?php if (!empty($project['Method'])): ?>
                    <?php
                        $i = 0;
                        foreach ($project['Method'] as $i => $method): ?>
                        <tr>
                            <td width="30" align="center"><?php echo $i + 1; ?></td>
                            <td><?php echo $this->Html->link($method['name'], array('controller' => 'methods', 'action' => 'edit', $method['id'])); ?></td>
                            <td><?php echo $method['description']; ?></td>
                            <td class="actions">
                                <?php echo $this->Form->postLink(console::$icons['delete'], array('controller' => 'methods', 'action' => 'delete', $method['id']), array('escape' => false, 'title' => __('Delete')), __('Are you sure you want to delete the method %s from the project?', $method['name'])); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                    </table>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                    <table class="<?=$table_class?>">
                    <tr>
                        <th colspan="2"><h3><?php echo __('Units'); ?></h3></th>
                        <?php if (!empty($project['Unit'])): ?><th><?php echo __('Symbol'); ?></th><? endif; ?>
                        <th class="actions">
                        	<?php echo $this->Html->link(console::$icons['add'], array('controller' => 'units', 'action' => 'add', $project['Project']['id']), array('class' => 'ml-10',  'escape' => false)); ?>
                        </th>
                    </tr>
                    <?php if (!empty($project['Unit'])): ?>
                    <?php
                        $i = 0;
                        foreach ($project['Unit'] as $i => $unit): ?>
                        <tr>
                            <td width="30" align="center"><?php echo $i + 1; ?></td>
                            <td><?php echo $this->Html->link($unit['name'], array('controller' => 'units', 'action' => 'edit', $method['id'])); ?></td>
                            <td><?php echo $unit['symbol']; ?></td>
                            <td class="actions">
                                <?php echo $this->Form->postLink(console::$icons['delete'], array('controller' => 'units', 'action' => 'delete', $unit['id']), array('escape' => false, 'title' => __('Delete')), __('Are you sure you want to delete the unit %s from the project?', $unit['name'])); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
					<?php endif; ?>
                    </table>
            </div>
        </div>
    </div>
</div>

<div id="value-view-modal" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Value</h4>
      </div>
      <div class="modal-body">
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?=__('Close')?></button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="user-view-modal" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">User</h4>
      </div>
      <div class="modal-body">
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?=__('Close')?></button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="export-view-modal" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Export</h4>
      </div>
      <div class="modal-body">
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?=__('Close')?></button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="input-view-modal" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Input</h4>
      </div>
      <div class="modal-body">
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Close')?></button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="project-view-modal" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Project</h4>
      </div>
      <div class="modal-body">
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Close')?></button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

