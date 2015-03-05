<div class="row">
    <div class="col-md-12">
    <?php echo $this->Form->create('Method', array('class' => 'form', 'url' => $this->request->relative)); ?>
    	<div class="row">
    		<div class="col-md-12">
       			<h3><?php echo __('%s Method', console::editOrAdd($this->request->relative)); ?></h3>
			</div>
        </div>
        <div class="row">
    		<div class="col-md-4">
                <div class="">
                    <? echo $this->Form->input('name', array_merge(console::$htmlInput, array('div' => array('class' => 'form-group')))); ?>
                </div>
                <div class="">
                    <? echo $this->Form->input('description', array_merge(console::$htmlInput, array('div' => array('class' => 'form-group')))); ?>
                </div>
                <div class="">
                    <? echo $this->Form->input('perTimestamp', array_merge(array(), array('type' => 'checkbox', 'label' => '', 'between' => ' <strong>'.__('Per timestamp').'</strong>'.__(' Check if this method shall only receive one input measure per execution, else it will get all measures of the current time interval.'), 'div' => array('class' => 'form-group')))); ?>
                </div>
<?php /*?>                <div class="">
                    <?= __('Is the method modifying data?'); ?>
                    <? echo $this->Form->input('modifying', array_merge(array('type' => 'checkbox', 'div' => array('class' => 'form-group')))); ?>
                </div><?php */?>
                <div class="">
                    <? echo $this->Form->input('params', array_merge(console::$htmlInput, array('type' => 'textarea', 'between' => __(' Define input variables of type val or num. A variable of the type val is one of the defined Values. Num is a real number. Define one param per line with syntax <pre>Variable name:Variable type:Description</pre> Example: <pre>value:val:The value from which the result is calculated</pre>'), 'div' => array('class' => 'form-group')))); ?>
                </div>
        	</div>

    		<div class="col-md-8">
            	<div class="row">
                    <div class="col-md-10">
                    	<? echo $this->Form->input('code', array_merge(console::$htmlInput, array('type' => 'textarea', 'div' => array('class' => 'form-group')))); ?>
                    </div>
                    <div class="col-md-2"><?=__('Variables')?><br />
                    	<ul id="vars-list" class="list-group">
                            <li class="list-group-item"><a class="add-variable" href="javascript:;">$start</a></li>
                            <li class="list-group-item"><a class="add-variable" href="javascript:;">$end</a></li>
                        </ul>
                        <hr />
                        <ul id="loop-list" class="list-group">
                       		          	
                        </ul>
                	</div>
                </div>
            	Structure of a Value:<br />
				<? $value_structure = array('minimum' => '//minimum threshold', 'maximum' => '//maximum threshold', 'max_variation' => '//Max. difference per hour between to measures',  'data' => array(array('data' => '//The measured data', 'timestamp' => '//The timestamp the data was measured','conflict_data' => '//Data for same timestamp in conflict with current data','state' => '//-2: User checked and wrong, -1:Wrong, 0:Not checked, 1:OK, 2:User checked and OK')));  
                ?><pre><? print_r($value_structure); ?></pre>
        	</div>
        </div>

    	<?php echo $this->Form->end(array('class' => 'btn btn-primary form-control', 'label' => __('Save'))); ?>
    </div>
</div>