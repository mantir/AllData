<div class="row">
    <div class="col-md-12">
    <?php echo $this->Form->create('Input', array('type' => 'file', 'class' => 'form', 'url' => $this->request->relative)); ?>
        <h3><?php echo __('%s Input', console::editOrAdd($this->request->relative)); ?></h3>
        <?php
            echo $this->Form->input('name', console::$htmlInput);
			echo $this->Form->input('template_file', array_merge(console::$htmlInput, array('class' => 'btn btn-default btn-file', 'type' => 'file')));
			if($input['Input']['type'] == 'text'){
				?><div class="row"><?
					echo $this->Form->input('delimiter', array_merge(console::$htmlInput, array('label' => 'Trennzeichen<br />in einer Zeile', 'div' => array('class' => 'form-group col-lg-1 col-md-3'))));
					echo $this->Form->input('data_row', array_merge(console::$htmlInput, array('label' => 'Zeile, ab der die Daten beginnen','div' => array('class' => 'form-group col-lg-1 col-md-3'))));
					echo $this->Form->input('head_row', array_merge(console::$htmlInput, array('label' => 'Zeile, mit Spaltenüberschriften','div' => array('class' => 'form-group col-lg-1 col-md-3'))));
					echo $this->Form->input('timestamp_format', array_merge(console::$htmlInput, array(
						'label' => 'Timestamp-Format<br />'.
						$this->Html->link('Mögliche Formate', 'javascript:;', array('data-toggle' => "modal", 'data-target' => "#format-view-modal")), 
						'div' => array('class' => 'form-group col-lg-1 col-md-3')))
					);
					?><div class="col-lg-9 col-md-3 form-group">
                    	<br /><br />
						<div class="bs-component">
							<input type="submit" name="refresh" class="btn btn-primary" value="Aktualisieren" />
							<input type="submit" class="btn btn-primary" value="Speichern" />								
						</div>
                    </div>
				</div><?
			}
			//echo $this->Form->input('Value', console::$htmlInput);
			$self = $this;
			$paths = array();
			$counter = -1;
			$values[-1] = 'Timestamp';
			$inputReplace = function($matches) use ($limiter, $values, $self, &$paths, &$counter, $values, $saved_paths, $title_limiter) {
				$v = $matches[1];
				$v = split($title_limiter, $v);
				$title = $v[1] ? $v[0].' / '.$v[1] : $v[0];
				
				$path = stripslashes($matches[2]);
				if($paths[$path]) {
					return $v[1] ? $v[1] : $v[0];
				}
				$paths[$path] = true;
				$counter++;
				if(!$self->request->data['Value']['Value'][$counter] && $saved_paths[$path])
					$self->request->data['Value']['Value'][$counter] = $saved_paths[$path];
					
				return '<input type="hidden" name="data[Value][path]['.$counter.']" value="'.$path.'" />'.
						$self->Form->input('Value.Value.'.$counter, array(
							'type' => 'select', 'options' => $values, 'data-toggle' => "tooltip", 
							'title' => $title, 'empty' => '  --------', 'label' => false, 'div' => false, 
							'value' => $self->request->data['Value']['Value'][$counter], 'multiple' => false)
						);
			};
			if($skeleton) {
        	$skeleton = preg_replace_callback('!'.$marker.'(.*?)'.$limiter.'(.*?)'.$marker.'!', $inputReplace, $skeleton);
		?>
        <div class="row" >
        	<pre style="max-height:400px;" class="col-md-12"><?= $skeleton ?></pre>
        </div>
         <div class="row">
         	<?
				echo $this->Form->input('list_url', array_merge(console::$htmlInput, array(
						'label' => __('List URL: A webpage or something similar where to check for new data files.'), 
						'div' => array('class' => 'form-group col-lg-12'))));
				echo $this->Form->input('link_regex', array_merge(console::$htmlInput, array(
						'label' => __('Regular expression: To search for file links under the list URL.'),
						'div' => array('class' => 'form-group col-md-12'))));
				echo $this->Form->input('page_container', array_merge(console::$htmlInput, array(
						'label' => 'Page Container: Can be a regular expression or a jQuery selector to extract the file content from a container.',
						'div' => array('class' => 'form-group col-md-12')))
					); ?>
        </div>
        <? } ?>
    	<?php echo $this->Form->end(array('class' => 'btn btn-primary mb-20', 'label' => __('Speichern'))); ?>
    </div>
</div>

<div id="format-view-modal" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Timestamp-Formate</h4>
      </div>
      <div class="modal-body">
      	<? echo $this->element('phpnetdate'); ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Schließen</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->