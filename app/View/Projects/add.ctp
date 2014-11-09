<div class="row">
    <div class="col-md-12">
		<?php echo $this->Form->create('Project', array('class' => 'form', 'url' => $this->request->relative)); ?>
            <h3><?php echo __('Projekt '.console::editOrAdd($this->request->relative)); ?></h3>
        <?php
            echo $this->Form->input('name', console::$htmlInput);
        ?>
        <?php echo $this->Form->end(array('class' => 'btn btn-primary mb-20', 'label' => __('Speichern'))); ?>
    </div>
</div>
