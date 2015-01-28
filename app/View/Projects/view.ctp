<div class="row">
	<div class="col-md-12">
    	<h2><?php  echo $project['Project']['name'] ?> <?php echo $this->Html->link(__('Edit project'), array('action' => 'edit', $project['Project']['id']), array('class' => 'btn btn-primary btn-sm ml-10')); ?> <?php echo $this->Html->link('<span class="glyphicon glyphicon-upload mr-5"></span> '.__('Import measured data'), array('action' => 'upload_import', $project['Project']['id']), array('escape' => false, 'class' => 'btn btn-primary btn-sm')); ?> <?php echo $this->Html->link('<span class="glyphicon glyphicon-stats mr-5"></span> '.__('Show data'), array('action' => 'data', $project['Project']['id']), array('escape' => false, 'class' => 'btn btn-primary btn-sm')); ?> 
        </h2>
	</div>
</div>
<div class="row">
    <div class="col-md-6">
        <h3><?php echo __('Values'); ?> <?php echo $this->Html->link(__('+ Create'), array('controller' => 'values', 'action' => 'add', $project['Project']['id']), array('class' => 'btn btn-primary btn-sm ml-10')); ?></h3>
        <?php if (!empty($project['Value'])): ?>
        <table class="table">
        <tr>
            <th><?php echo __('#'); ?></th>
            <th><?php echo __('Name'); ?></th>
            <th><?php echo __('Unit'); ?></th>
            <th><?php echo __('Input'); ?></th>
            <th><?php echo __('Minimum'); ?></th>
            <th><?php echo __('Maximum'); ?></th>
        </tr>
        <?php
            $i = 0;
            foreach ($project['Value'] as $i => $value): ?>
            <tr>
                <td><?php echo $i + 1; ?></td>
                <td><?php echo $this->Html->link($value['name'], array('controller' => 'values', 'action' => 'edit', $value['id'])); ?></td>
                <td title="<?php echo console::$unit_prefixes[$value['prefix']].($value['Unit']['name']); ?>"><?php echo $value['prefix'].$value['Unit']['symbol']; ?></td>
                <td><?php 
					$lim = ''; 
					if(count($value['Input'])) {
						foreach($value['Input'] as $input) { 
							echo $lim.$this->Html->link($input['name'], array('controller' => 'inputs', 'action' => 'edit', $input['id'])); $lim = ', ';
						}
					} else if($value['Method']) {
						echo $this->Html->link($value['execName'], array('controller' => 'methods', 'action' => 'edit', $value['Method']['id']));
					}
				?>	
                </td>
                <td><?php echo $value['minimum'] ?></td>
                <td><?php echo $value['maximum'] ?></td>
            </tr>
        <?php endforeach; ?>
        </table>
    <?php endif; ?>
    
    </div>
    <div class="col-md-6">
    	<div class="row">
    		<div class="col-md-12">
                <h3><?php echo __('Inputs'); ?> <?php echo $this->Html->link(__('+ Create'), array('controller' => 'inputs', 'action' => 'add', $project['Project']['id']), array('class' => 'btn btn-primary btn-sm ml-10')); ?></h3>
                <?php if (!empty($project['Input'])): ?>
                    <table class="table">
                    <tr>
                        <th><?php echo __('#'); ?></th>
                        <th><?php echo __('Name'); ?></th>
                        <th><?php echo __('Type'); ?></th>
                        <th class="actions"><?php echo __('Actions'); ?></th>
                    </tr>
                    <?php
                        $i = 0;
                        foreach ($project['Input'] as $i => $input): ?>
                        <tr>
                            <td><?php echo $i + 1; ?></td>
                            <td><?php echo $this->Html->link($input['name'], array('controller' => 'inputs', 'action' => 'edit', $input['id'])); ?></td>
                            <td><?php echo $input['type']; ?></td>
                            <td class="actions">
                                <?php echo $this->Form->postLink(__('Delete'), array('controller' => 'inputs', 'action' => 'delete', $input['id']), null, __('Are you sure you want to delete # %s?', $input['id'])); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </table>
            	<?php endif; ?>
            </div>
    	</div>
    	<div class="row">
            <div class="col-md-12">
                <h3><?php echo __('Logs'); ?></h3>
                <ul id="logs">
                	
                </ul>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <h3><?php echo __('Project Users'); ?> <?php echo $this->Html->link(__('+ Invite'), array('controller' => 'projects', 'action' => 'invite', $project['Project']['id']), array('class' => 'btn btn-primary btn-sm ml-10')); ?></h3>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <h3><?php echo __('Exports'); ?> <?php echo $this->Html->link(__('+ Create'), array('controller' => 'exports', 'action' => 'add', $project['Project']['id']), array('class' => 'btn btn-primary btn-sm ml-10')); ?></h3>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <h3><?php echo __('Methods'); ?> <?php echo $this->Html->link(__('+ Create'), array('controller' => 'methods', 'action' => 'add', $project['Project']['id']), array('class' => 'btn btn-primary btn-sm ml-10')); ?></h3>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <h3><?php echo __('Units'); ?> <?php echo $this->Html->link(__('+ Create'), array('controller' => 'units', 'action' => 'add', $project['Project']['id']), array('class' => 'btn btn-primary btn-sm ml-10')); ?></h3>
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

