<div class="row">
    <div class="col-md-12">
		<?php echo $this->Form->create('Project', array('class' => 'form', 'url' => $this->request->relative)); ?>
            <h3><?php echo __('Projekt '.console::editOrAdd($this->request->relative)); ?></h3>
        <?php
            echo $this->Form->input('name', console::$htmlInput);
        ?>
        <?php echo $this->Form->end(array('class' => 'btn btn-primary mb-20', 'label' => __('Save'))); ?>
    </div>
</div>
<? if($this->request->action == 'edit') { ?>
<div class="row">
    <div class="col-md-12">
		<?php echo $this->Form->postLink(__('Delete project'), array('action' => 'delete', $this->Form->value('Project.id')), null, __('Are you sure you want to delete the project and all related data?', $this->Form->value('Project.id'))); ?>
    </div>
</div>
<? } ?>

