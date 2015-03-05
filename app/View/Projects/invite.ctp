<div class="row">
    <div class="col-md-12">
		<?php echo $this->Form->create('Project', array('class' => 'form', 'url' => $this->request->relative)); ?>
            <h3><?php echo __('Invite user to '.$project['Project']['name']); ?></h3>
        <?php
			echo $this->Form->input('id', array('value' => $project['Project']['id'], 'type' => 'hidden'));
            echo $this->Form->input('Member.Member.0.email', console::$htmlInput);
			echo $this->Form->input('Member.Member.0.state', array_merge(console::$htmlInput, array('type' => 'select', 'options' => array_splice(console::$memberTypes, 0, 2))));
        ?>
        <?php echo $this->Form->end(array('class' => 'btn btn-primary mb-20', 'label' => __('Invite'))); ?>
    </div>
</div>

