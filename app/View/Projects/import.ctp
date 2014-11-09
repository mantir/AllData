<div class="row">
    <div class="col-md-12">
    <?php echo $this->Form->create(false, array('type' => 'file', 'class' => 'form', 'url' => $this->request->relative)); ?>
        <h3><?php echo __('Daten importieren'); ?></h3>
        <?php
            echo $this->Form->input('Input', array_merge(console::$htmlInput, array('label' => 'Welchem Input-Format entspricht der Upload?')));
			echo $this->Form->input('import_file', array_merge(console::$htmlInput, array('label' => 'Datei mit Messdaten', 'class' => 'btn btn-default btn-file', 'type' => 'file')));
			//echo $this->Form->input('Value', console::$htmlInput);
			?>
    	<?php echo $this->Form->end(array('class' => 'btn btn-primary mb-20', 'label' => __('Speichern'))); ?>
    </div>
</div>