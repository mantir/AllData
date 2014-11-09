<div class="row">
	<div class="col-md-12">
    	<h2><?php  echo $project['Project']['name'] ?> <?php echo $this->Html->link(__('Projekt bearbeiten'), array('action' => 'edit', $project['Project']['id']), array('class' => 'btn btn-primary btn-sm')); ?> <?php echo $this->Html->link('<span class="glyphicon glyphicon-upload mr-5"></span> '.__('Messdaten hochladen'), array('action' => 'import', $project['Project']['id']), array('escape' => false, 'class' => 'btn btn-primary btn-sm')); ?></h2>
	</div>
</div>
<div class="row">
    <div class="col-md-6">
        <h3><?php echo __('Definierte Values'); ?> <?php echo $this->Html->link(__('+ Neues Value anlegen'), array('controller' => 'values', 'action' => 'add', $project['Project']['id']), array('class' => 'btn btn-primary btn-sm')); ?></h3>
        <?php if (!empty($project['Value'])): ?>
        <table class="table">
        <tr>
            <th><?php echo __('Id'); ?></th>
            <th><?php echo __('Name'); ?></th>
            <th><?php echo __('Einheit'); ?></th>
            <th class="actions"></th>
        </tr>
        <?php
            $i = 0;
            foreach ($project['Value'] as $value): ?>
            <tr>
                <td><?php echo $value['id']; ?></td>
                <td><?php echo $value['name']; ?></td>
                <td title="<?php echo console::$unit_prefixes[$value['prefix']].($value['Unit']['name']); ?>"><?php echo $value['prefix'].$value['Unit']['symbol']; ?></td>
                <td class="actions">
                    <?php echo $this->Html->link(__('Edit'), array('controller' => 'values', 'action' => 'edit', $value['id'])); ?>
                    <?php echo $this->Form->postLink(__('Delete'), array('controller' => 'values', 'action' => 'delete', $value['id']), null, __('Are you sure you want to delete # %s?', $value['id'])); ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </table>
    <?php endif; ?>
    
    </div>
    <div class="col-md-6">
        <h3><?php echo __('Definierte Inputs'); ?> <?php echo $this->Html->link(__('+ Neuen Input anlegen'), array('controller' => 'inputs', 'action' => 'add', $project['Project']['id']), array('class' => 'btn btn-primary btn-sm')); ?></h3>
        <?php if (!empty($project['Input'])): ?>
        <table class="table">
        <tr>
            <th><?php echo __('Id'); ?></th>
            <th><?php echo __('Name'); ?></th>
            <th><?php echo __('Typ'); ?></th>
            <th class="actions"><?php echo __('Actions'); ?></th>
        </tr>
        <?php
            $i = 0;
            foreach ($project['Input'] as $input): ?>
            <tr>
                <td><?php echo $input['id']; ?></td>
                <td><?php echo $input['name']; ?></td>
                <td><?php echo $input['type']; ?></td>
                <td class="actions">
                    <?php echo $this->Html->link(__('Edit'), array('controller' => 'inputs', 'action' => 'edit', $input['id'])); ?>
                    <?php echo $this->Form->postLink(__('Delete'), array('controller' => 'inputs', 'action' => 'delete', $input['id']), null, __('Are you sure you want to delete # %s?', $input['id'])); ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </table>
    <?php endif; ?>
    
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
        <button type="button" class="btn btn-default" data-dismiss="modal">Schließen</button>
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
        <button type="button" class="btn btn-default" data-dismiss="modal">Schließen</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="project-view-modal" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Projekt</h4>
      </div>
      <div class="modal-body">
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Schließen</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

