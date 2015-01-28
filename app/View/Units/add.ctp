<div class="row">
    <div class="col-md-12">
    <?php echo $this->Form->create('Unit', array('class' => 'form', 'url' => $this->request->relative)); ?>
        <h3><?php echo __('Unit '.console::editOrAdd($this->request->relative)); ?></h3>
        <?php
            echo $this->Form->input('name', console::$htmlInput);
            echo $this->Form->input('symbol', console::$htmlInput);
        ?>
    	<?php echo $this->Form->end(array('class' => 'btn btn-primary form-control', 'label' => __('Save'))); ?>
    </div>
</div>
