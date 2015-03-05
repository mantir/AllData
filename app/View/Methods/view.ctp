<div class="methods-view">
<h2><?php  echo $method['Method']['name']; ?></h2>
<div><?=$method['Method']['description']?></div>
	<?= $this->Form->create(false, array('type' => 'GET', 'action' => 'execute/'.$method['Method']['id'], 'id' => 'calculate-form', 'class' => "row mt-10 mb-10")); ?>
    	<div class="col-md-6">
        <h3>Params</h3>
		<? foreach($params as $p) { 
            if($p['type'] == 'val') {
                echo $this->Form->input($p['name'], array_merge(console::$htmlInput, array('type' => 'select', 'between' => ' '.$p['description'], 'options' => $values, 'selected' => $this->request->query[$p['name']])));
            } else { 
                echo $this->Form->input($p['name'], array_merge(console::$htmlInput, array('type' => 'text', 'between' => ' '.$p['description']))); 
            } ?>
        <? } ?>
        </div>
        <div class="col-md-6">
        	<div class="row">
                <h3 class="col-md-12">Time</h3>
                <div class="col-md-6">
                    <?php
                        echo $this->Form->input('start', array_merge(console::$htmlInput, array(
                            'class' => console::$htmlInput['class'].' calendar-input', 
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
                            'type' => 'select'))).' <i class="make-null glyphicon glyphicon-remove-circle"></i>';;
                    ?>
                </div>
                <div class="col-md-6">
                    <?php
                        echo $this->Form->input('end', array_merge(console::$htmlInput, array(
                            'class' => console::$htmlInput['class'].' calendar-input', 
                            'value' => $this->request->query['end'], 
                            //'style' => 'width:96px !important',
                            'placeholder' => __('End date'), 
                            'type' => 'text')));
                    ?>
                    <?php
                        echo $this->Form->input('end_hour', array_merge(console::$htmlInput, array(
                            'class' => '', 
                            'options' => console::range(0, 23), 'div' => false, 
                            'label' => false, 
							'value' => $this->request->query['end_hour'], 
                            'type' => 'select'))).' : '.
                        $this->Form->input('end_minute', array_merge(console::$htmlInput, array(
                            'class' => '', 
							'options' => console::range(0, 59), 
                            'div' => false, 
                            'label' => false, 
                            'value' => $this->request->query['end_minute'], 
                            'type' => 'select'))).' <i class="make-null glyphicon glyphicon-remove-circle"></i>';;
                    ?>
               </div>
           </div>
           <? if( $method['Method']['perTimestamp'] ) { 
           			echo $this->Form->input('interval_count', array( 'value' => 1, 'type' => 'hidden'));
                    echo $this->Form->input('interval_type', array( 'value' => 0, 'type' => 'hidden'));
           } else {?>
           <div class="row">
           		<h3 class="col-md-12">Interval</h3>
                <div class="row">
                    <div class="col-md-12">
                        <?
                        echo $this->Form->input('interval_count', array_merge(console::$htmlInput, array(
                                'div' => array('class' => 'col-md-6'),
                                'options' => console::range(1,59),
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
           <? } ?>
       </div>
       <div class="col-md-2 pull-right mr-20">
			<?= $this->Form->Input(__('Calculate'), array('type' => 'submit', 'id' => 'calculate-method-btn', 'class' => 'btn btn-primary mt-30', 'label' => false)) ?>
        </div>
    <?=$this->Form->end()?>
</div>
