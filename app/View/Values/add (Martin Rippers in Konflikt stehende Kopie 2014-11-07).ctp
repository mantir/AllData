<div class="row">
    <div class="col-md-12">
    <?php echo $this->Form->create('Value', array('class' => 'form', 'url' => $this->request->relative)); ?>
        <h3><?php echo __('Value '.console::editOrAdd($this->request->relative)); ?></h3>
        <?php
            echo $this->Form->input('name', console::$htmlInput);
			
            ?><div class="row">
            	<? echo $this->Form->input('prefix', array_merge(console::$htmlInput, array('type' => 'select', 'empty' => ' ---', 'options' => console::$unit_prefixes, 'div' => array('class' => 'form-group col-md-6')))); ?>
				<? echo $this->Form->input('unit_id', array_merge(console::$htmlInput, array('div' => array('class' => 'form-group col-md-6')))); ?>
            </div>

    	<?php echo $this->Form->end(array('class' => 'btn btn-primary form-control', 'label' => __('Speichern'))); ?>
    </div>
</div>
