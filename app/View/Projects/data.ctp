<div id="project-data" class="row">
    <div class="col-md-9">
    	<div id="charts" style="overflow:auto;" class="row">
    	<? foreach($values as $id => $v) { 
			if(count($v['Measure'])) { ?>
                <div id="chart<?=$id?>" class="chart-container">
                </div>
        <?  break; }
		} ?>
        </div>
        <div id="chart-controls" class="row">
        	<div class="col-md-3 col-lg-2">
				<?=$this->Form->input('diagram_type', array_merge(console::$htmlInput, array(
					'type' => 'select', 
					'label' => 'Diagram',
					'options' => array('area' => __('Area'), 'line' => __('Line'), 'spline' => __('Spline'), 'areaspline' => __('Area spline'), 'column' => __('Column')))));?>
            </div>
            <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-6">
                            <?php
                                echo $this->Form->input('method_id', array_merge(console::$htmlInput, array(
									'type' => 'select',
									'label' => 'Calculate with method '.$this->Html->link(__('+ New Method'), '/methods/add', array('target' => '_blank')), 
									'options' => $methods, 
									'empty' => '------'))
								);
								
								echo $this->Form->input(__('Run'), array(
									'id' => 'run-method-btn',
									'label' => false,
									'type' => 'submit', 
									'class' => 'btn btn-primary btn-sm pull-right', 
								));
                            ?>
                        </div>
                  </div>
            </div>
        </div>
    </div> 
    <div class="col-md-3 mt-10">
        	<div class="row">
   				<div class="col-md-12">
                    <div class="row">
                    <?php echo $this->Form->create(null, array('class' => 'form', 'type' => 'get', 'url' => $this->request->relative)); ?>
            			<div class="col-md-10">
                            <div class="row mt-10 mb-10">
                                <div class="col-md-6">
                                    <?php
                                        echo $this->Form->input('start', array_merge(console::$htmlInput, array(
                                            'class' => console::$htmlInput['class'].' calendar-input', 
                                            'label' => false,
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
                                            'type' => 'select'))).' <i class="make-null glyphicon glyphicon-remove-circle"></i>';
                                    ?>
                                </div>
                                <div class="col-md-6">
                                    <?php
                                        echo $this->Form->input('end', array_merge(console::$htmlInput, array(
                                            'class' => console::$htmlInput['class'].' calendar-input', 
                                            'value' => $this->request->query['end'], 
                                            'label' => false,
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
                                            'type' => 'select'))).' <i class="make-null glyphicon glyphicon-remove-circle"></i>';;
                                    ?>
                               </div>
                               
                               <div id="form-value-ids" class="hidden"></div>
                           </div>
                        </div>
                        <div class="col-md-2">
                            <?= $this->Form->Input(__('Go'), array('type' => 'submit', 'class' => 'btn btn-primary btn-sm mt-30', 'style' => 'margin-left:-10px', 'label' => false)) ?>
                        </div>
					<?php echo $this->Form->end(); ?>
                    </div>
                </div>
            </div>
            <!--End Filter-->
            <div class="row">
            	<div class="col-md-12">
                	<?php /*?><div class="row"><input checked="checked" type="checkbox" id="allInOne" />&nbsp;<label for="allInOne"><?=__('Show selected in 1 diagram');?></label></div><?php */?>
                	<ul id="value-list" style="overflow:auto" class="list-group row">
                    <li id="method-value" class="list-group-item" style="display:none">
                        	<input type="checkbox" id="value_-1" class="value-checkbox" data-value_id="-1">
                            <a id="method-caption" class="ml-10 value-link" href="javascript:;" data-value_id="-1"></a>
                    </li>
                	<? for($noMeasures = 0; $noMeasures < 2; $noMeasures++) {
					foreach($values as $v) {
						if(!$noMeasures && count($v['Measure']) || $noMeasures && !count($v['Measure'])) {
						?><li <?= $noMeasures ? 'style="opacity:0.5"' : '';?> class="list-group-item">
                        	<input <?= isset($form_values[$v['Value']['id']]) ? 'checked="checked"' : ''?> data-value_id="<?=$v['Value']['id'] ?>" class="value-checkbox" <?= $noMeasures ? 'disabled="disabled"' : ''; ?> type="checkbox" id="value_<?=$v['Value']['id'] ?>" />
                            <a data-value_id="<?=$v['Value']['id'] ?>" href="javascript:;" class="ml-10 value-link"><?=$v['Value']['name'] ?></a>
                            <div class="value-buttons pull-right">
                            	<?php echo $this->Html->link('<i class="fa fa-edit"></i>', array('controller' => 'values', 'action' => 'edit', $v['Value']['id']), array(
									'data-value_id' => $v['Value']['id'], 
									'title' => __('Edit'),
									'escape' => false, 
									'class' => 'ml-10 value-edit-link')); ?>
								<? if($v['Value']['method_id']) { ?>
									<a data-value_id="<?=$v['Value']['id'] ?>" href="javascript:;" title="<?=__('Calculate')?>" class="ml-10 value-method-link">
                                    	<i class="fa fa-superscript fa-fw"></i>
                                    </a>
								<? } ?>
                            </div>
                        </li><?
						}
					} 
					}?>
                    </ul>
                </div>
            </div>
    </div>
    <div id="method-view-modal" class="modal fade">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Execute Method</h4>
          </div>
          <div class="modal-body">
            Execute Method
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Close')?></button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    
    <div id="value-view-modal" class="modal fade">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Value</h4>
          </div>
          <div class="modal-body">
            
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?=__('Close')?></button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
</div>