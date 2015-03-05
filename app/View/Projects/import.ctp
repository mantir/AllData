<? if(!$links) { ?>
<div class="row">
    <div class="col-md-12">
    <?php echo $this->Form->create(false, array('type' => 'file', 'class' => 'form', 'url' => $this->request->relative)); ?>
        <h2><?php echo __('Import data'); ?></h2>
        <?php
            echo $this->Form->input('Input', array_merge(console::$htmlInput, array('label' => __('Please select input format'))));
			?><h3><?=__('Import manually by upload')?></h3><?
			echo $this->Form->input('import_file', array_merge(console::$htmlInput, array('label' => __('Upload file with measured data'), 'class' => 'btn btn-default btn-file', 'type' => 'file')));
			?><hr />
            <h3><?=__('Import manually from input source')?></h3><?
			echo $this->Form->input('import_source', array_merge(console::$htmlInput, array('label' => __('Import files from input source.'), 'class' => 'pull-left mr-10', 'type' => 'checkbox')));
			//echo $this->Form->input('Value', console::$htmlInput);
			?><hr />
            <h3><?=__('Import automatically from all input sources')?></h3>
            <?=__('If you have set source URLs for the inputs you can create an automatic import of all inputs by creating a cronjob to the url:')?><br />
            <strong><?=$this->Html->url(array('controller' => 'projects', 'action' => 'update_all_imports', $project_id), true)?></strong>
            <hr />
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
                <th><?php echo __('Data range'); ?></th>
                <th><?php echo __('Imported'); ?></th>
            </tr>
            <?php foreach($links as $i => $l) { ?>
            <tr>
            <td><?
                echo $this->Form->input('links_import.'.$i, array_merge(console::$htmlInput, array('label' => $l.' '.$this->Html->link(__('Show'), $l, array('target' => '_blank')), 'checked' => !$imported_links[$l], 'class' => 'pull-left mr-10', 'type' => 'checkbox')));
                echo $this->Form->input('links_url.'.$i, array('value' => $l, 'type' => 'hidden'));
                ?>
            </td>
            <td>
            	<? //echo Debugger::dump($imported_links[$l]); ?>
            	<?= ($imported_links[$l] ? ''.date('d.m.Y', $imported_links[$l]['data_4']).' - '.date('d.m.Y', $imported_links[$l]['data_5']) : '') ?>
            </td>
            <td>
				<?= ($imported_links[$l] ? ''.date('d.m.Y, H:i', $imported_links[$l]['time']) : '') ?>
            </td>
            </tr><? } ?>
            </table>
        </div>
    	<?php  
		echo $this->Form->input('Input', array('type' => 'hidden'));
		echo $this->Form->end(array('id' => 'submit_links', 'class' => 'btn btn-primary mb-20', 'label' => __('Import selected links'))); 
        echo $this->Html->link('<span class="glyphicon glyphicon-stats mr-5"></span> '.__('Show data'), array('action' => 'data', $project['Project']['id']), array('escape' => false, 'class' => 'btn btn-primary btn-sm', 'id' => 'show-data-btn', 'style' => 'display:none')); ?>
    </div>
</div>
<? } ?>