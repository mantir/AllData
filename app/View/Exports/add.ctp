<div class="row">
    <div class="col-md-12">
	<?php echo $this->Form->create('Export'); ?>
        <h3><?php echo __('%s Export', console::editOrAdd($this->request->relative)); ?></h3>
        <div class="row">
			<div class="col-md-6"><?php
				echo $this->Form->input('name', array_merge(console::$htmlInput, array('div' => array('class' => 'col-md-12 form-group'))));
				echo $this->Form->input('format', array_merge(console::$htmlInput, array(
					'div' => array('class' => 'col-md-12 form-group'),
					'options' => array('csv' => 'CSV'))));
				echo $this->Form->input('dateformat', array_merge(console::$htmlInput, array(
					'div' => array('class' => 'col-md-12 form-group'),
					'between' => $this->Html->link(__(' Possible formats.'), 'javascript:;', array('data-toggle' => "modal", 'data-target' => "#format-view-modal")).__(' Seperate multiple dateformats by comma. '),
					'value' => $this->request->data['Export']['dateformat'] ? $this->request->data['Export']['dateformat'] : console::$defaultDateformat)));
				?><div class="col-md-12"><label for="ExportIntervalCount"><?=__('Export by default the last')?></label></div><?
				echo $this->Form->input('interval_count', array_merge(console::$htmlInput, array(
					//'type' => 'select',
					//'options' => console::range(1,59),
					'div' => array('class' => 'col-md-4'),
					'label' => false)));
				echo $this->Form->input('interval_type', array_merge(console::$htmlInput, array(
					'label' => false, 
					'div' => array('class' => 'col-md-8'), 
					'options' => console::$intervalTypes))); 
				?>
            </div>
            <div class="col-md-6"><?
				echo $this->Form->input('value_ids', array_merge(array('class' => 'ml-0'), array(
					'type' => 'select', 
					'label' => 'Values',
					'selected' => $selected_values,
					'options' => $value_ids, 
					'multiple' => 'checkbox', 
					'style' => 'height:400px',
					'div' => array('class' => 'col-md-12 form-group'))));
				?>
            </div>
       </div>
    <?php echo $this->Form->end(array('class' => 'btn btn-primary mb-20 pull-right', 'label' => __('Save'))); ?>
      <? if($this->request->action == 'edit') { ?>
        <div class="row mt-20">
            <div class="col-md-12">
               <?php echo $this->Form->postLink(__('Delete export'), array('action' => 'delete', $this->Form->value('Export.id')), null, __('Are you sure you want to delete the export?', $this->Form->value('Export.id'))); ?>
            </div>
        </div>
        <? } ?>
    </div>
</div>

<div id="format-view-modal" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Timestamp-Formate</h4>
      </div>
      <div class="modal-body">
      	<? echo $this->element('phpnetdate'); ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?=__('Close')?></button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
