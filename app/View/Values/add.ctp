<div class="row">
    <div class="col-md-12">
    <?php echo $this->Form->create('Value', array('class' => 'form', 'url' => $this->request->uri)); ?>
        <h3><?php echo __('Value '.console::editOrAdd($this->request->relative)); ?></h3>
        <?php
            echo $this->Form->input('name', console::$htmlInput);
			
            ?><div class="row">
            	<? echo $this->Form->input('prefix', array_merge(console::$htmlInput, array('type' => 'select', 'empty' => ' ---', 'options' => console::$unit_prefixes, 'div' => array('class' => 'form-group col-md-6')))); ?>
				<? echo $this->Form->input('unit_id', array_merge(console::$htmlInput, array('div' => array('class' => 'form-group col-md-6')))); ?>
            </div>
            <div class="row mb-20">
            	<? echo $this->Form->input('minimum', array_merge(console::$htmlInput, array('div' => array('class' => 'form-group col-md-6')))); ?>
				<? echo $this->Form->input('maximum', array_merge(console::$htmlInput, array('div' => array('class' => 'form-group col-md-6')))); ?>
                <? echo $this->Form->input('error_codes', array_merge(console::$htmlInput, array('between' => __(' (Error code:description) 1 per line'), 'div' => array('class' => 'form-group col-md-6')))); ?>
                <? echo $this->Form->input('max_variation', array_merge(console::$htmlInput, array('between' => ' Measures musn\'t change more than this per hour.', 'div' => array('class' => 'form-group col-md-6')))); ?>
            	<? echo $this->Form->input('method_id', array_merge(console::$htmlInput, array('between' => ' Set Method if Value shall be calculated.', 'type' => 'select', 'options' => $methods, 'empty' => '--- '.__('Calculate with Method').' ---', 'div' => array('class' => 'form-group col-md-6')))); ?>
                
                <div id="value-method" <?= $this->request->data['Value']['method_id'] ? '' : 'style="display:none"' ?> class="col-md-6">
                	<div id="method-params">
                    <?
						echo $this->element('get_params');
						
					?>
                    </div>
                	<div class="row"><?
                            echo $this->Form->input('interval_count', array_merge(console::$htmlInput, array(
                                    'div' => array('class' => 'col-md-6'),
                                    'options' => console::range(1, 59),
                                    'label' => 'Execute every', 
                                    'value' => $this->request->query['interval_count'], 
                                    'type' => 'select')));
                            echo $this->Form->input('interval_type', array_merge(console::$htmlInput, array(
                                    'div' => array('class' => 'col-md-6'),
                                    'options' => console::$intervalTypes,
                                    'label' => '&nbsp;',
                                    'encode' => false, 
                                    'value' => $this->request->query['interval_type'], 
                                    'type' => 'select')));
                            ?>
                    </div>
                </div>
            </div>
            

    	<?php echo $this->Form->end(array('class' => 'btn btn-primary form-control', 'label' => __('Save'))); ?>
        <? if($this->request->action == 'edit') { ?>
        <div class="row mt-20">
            <div class="col-md-12">
               <?php echo $this->Form->postLink(__('Delete value'), array('action' => 'delete', $this->Form->value('Value.id')), null, __('Are you sure you want to delete the value and all related data?', $this->Form->value('Value.id'))); ?>
            </div>
        </div>
        <? } ?>
    </div>
</div>
