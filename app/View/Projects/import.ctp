<? if(!$links) { ?>
<div class="row">
    <div class="col-md-12">
    <?php echo $this->Form->create(false, array('type' => 'file', 'class' => 'form', 'url' => $this->request->relative)); ?>
        <h3><?php echo __('Import data'); ?></h3>
        <?php
            echo $this->Form->input('Input', array_merge(console::$htmlInput, array('label' => __('Please select input format'))));
			echo $this->Form->input('import_file', array_merge(console::$htmlInput, array('label' => __('Upload file with measured data'), 'class' => 'btn btn-default btn-file', 'type' => 'file')));
			echo $this->Form->input('import_source', array_merge(console::$htmlInput, array('label' => __('or import file(s) from input source.'), 'class' => 'pull-left mr-10', 'type' => 'checkbox')));
			//echo $this->Form->input('Value', console::$htmlInput);
			?>
    	<?php echo $this->Form->end(array('class' => 'btn btn-primary mb-20', 'label' => __('Import'))); ?>
    </div>
</div>
<? } else { ?>
<div class="row">
    <div class="col-md-12">
    <?php echo $this->Form->create(false, array('class' => 'form', 'id' => 'import_links', 'url' => $this->request->relative)); ?>
        <h3><?php echo __('Import links from source for input ').$input['Input']['name']; ?></h3>
        <p><?=__('Found %s current links, %s imported', count($links), count($imported_links)) ?></p>
        <div id="source-link-container" class="col-md-12 mb-10" style="height:400px; overflow:auto">
            <table class="table" height="400">
            <tr>
                <th><?php echo __('Link'); ?></th>
                <th><?php echo __('Imported'); ?></th>
            </tr>
            <?php foreach($links as $i => $l) { ?>
            <tr><td><?
                echo $this->Form->input('links_import.'.$i, array_merge(console::$htmlInput, array('label' => $l.' '.$this->Html->link(__('Show'), $l, array('target' => '_blank')), 'checked' => !$imported_links[$l], 'class' => 'pull-left mr-10', 'type' => 'checkbox')));
                echo $this->Form->input('links_url.'.$i, array('value' => $l, 'type' => 'hidden'));
                ?></td><td><?= ($imported_links[$l] ? ''.date('d.m.Y, H:i', $imported_links[$l]['time']) : '') ?>
            </td></tr><? } ?>
            </table>
        </div>
    	<?php  
		echo $this->Form->input('Input', array('type' => 'hidden'));
		echo $this->Form->end(array('id' => 'submit_links', 'class' => 'btn btn-primary mb-20', 'label' => __('Import selected links'))); 
        echo $this->Html->link('<span class="glyphicon glyphicon-stats mr-5"></span> '.__('Show data'), array('action' => 'data', $project['Project']['id']), array('escape' => false, 'class' => 'btn btn-primary btn-sm', 'id' => 'show-data-btn', 'style' => 'display:none')); ?>
    </div>
</div>
<? } ?>