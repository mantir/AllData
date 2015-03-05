<div class="row">
	 <div class="col-md-12"><h2><?php echo h($export['Export']['name']); ?></h2></div>
	 <?= $this->Form->create(null); ?>
     <div class="col-md-6">
    	
        <?php
            echo $this->Form->input('start', array_merge(console::$htmlInput, array(
                'class' => console::$htmlInput['class'].' calendar-input', 
                'label' => __('Start date'),
                'value' => $this->request->query['start'],
                //'style' => 'width:96px !important',
                'placeholder' => __('Start date'), 
                'type' => 'text')));
        ?>
        <?php
            echo $this->Form->input('start_hour', array_merge(console::$htmlInput, array(
                'class' => '', 
                'options' => console::range(0, 23), 
                'div' => false, 
                'label' => false, 
                'value' => $this->request->query['start_hour'], 
                'type' => 'select'))).' : '.
            $this->Form->input('start_minute', array_merge(console::$htmlInput, array(
                'class' => '', 
                'options' => console::range(0, 59), 
                'div' => false, 'label' => false, 
                'value' => $this->request->query['start_minute'], 
                'type' => 'select')));
        ?>
    </div>
    <div class="col-md-6">
        <?php
            echo $this->Form->input('end', array_merge(console::$htmlInput, array(
                'class' => console::$htmlInput['class'].' calendar-input', 
                'value' => $this->request->query['end'], 
                'label' => __('End date'),
                //'style' => 'width:96px !important',
                'placeholder' => __('End date'), 
                'type' => 'text')));
        ?>
        <?php
            echo $this->Form->input('end_hour', array_merge(console::$htmlInput, array(
                'class' => '', 
                'options' => console::range(0, 23), 'div' => false, 
                'label' => false, 'value' => $this->request->query['end_hour'], 
                'type' => 'select'))).' : '.
            $this->Form->input('end_minute', array_merge(console::$htmlInput, array(
                'class' => '', 'options' => console::range(0, 59), 
                'div' => false, 
                'label' => false, 
                'value' => $this->request->query['end_minute'], 
                'type' => 'select')));
        ?>
    </div>
    <div class="col-md-12 mt-10">
    	<hr />
        <? 
         echo $this->Form->input('states', array_merge(console::$htmlInput, array(
                'class' => '', 
				'between' => ' <label for="ExportStates">'.__(' Export with data states').'</label> '.__('If checked the data will be exported with its states'), 
                'div' => false, 
                'label' => false, 
                'value' => $this->request->query['end_minute'], 
                'type' => 'checkbox')));
        ?>
    </div>
    <div class="col-md-12 mt-10">
        <? 
         echo $this->Form->input('deleted', array_merge(console::$htmlInput, array(
                'class' => '', 
				'between' => ' <label for="ExportDeleted">'.__(' Export all data').'</label> '.__('If checked, everything will be exported, even deleted and error flagged data.'), 
                'div' => false, 
                'label' => false, 
                'value' => $this->request->query['end_minute'], 
                'type' => 'checkbox')));
        ?>
    </div>
   <?= $this->Form->end(null); ?>
    <div class="col-md-12">
        <hr />
        <label><?=__('Export')?></label><br />
        <? 
        echo $this->Html->link(__('Show export'), $url, array(
			'id' => 'export-url', 
			'class' => 'btn btn-primary',
			'target' => '_blank'));
        $url['?'] = array_merge($url['?'], array('download' => 1));
         echo $this->Html->link(__('Download data'), $url, array(
			'id' => 'download-url', 
			'class' => 'ml-20 btn btn-primary',
			'target' => '_blank')
		);
        ?>
    </div>
    <div class="col-md-12">
        <hr />
        <label><?=__('API Key')?></label><br />
        <?=__('This key must be provided in the request url if another system fetches data from this export. To get an export url click on the "Show export" button.')?> 
		<h4>&api_key=<?=$export['Export']['auth']?></h4>
    </div>
    
    <div class="col-md-12">
	    <hr />
		<?php echo $this->Html->link(__('Edit Export'), array('action' => 'edit', $export['Export']['id'])); ?>
    </div>
</div>

