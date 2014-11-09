<div class="row">
    <div class="col-md-12">
        <h2><?php echo 'Messdaten für '.$project['Project']['name']; ?></h2>
    </div>
</div>
<div class="row">
    <div class="col-md-8">
    	<? foreach($values as $id => $v) { 
			if(count($v['Measure'])) { ?>
            <div class="row">
                <div id="chart<?=$id?>" class="col-md-12">
                </div>
            </div>
        <? }
		} ?>
		<!--<canvas id="myChart" style="width:100%" height="400"></canvas>-->
    </div> 
    <div class="col-md-4">
    	<ul class="list-group" style="position:fixed; right:30px;">
		<? foreach($values as $v) {
			?><li class="list-group-item"><a href="javascript:;"><?=$v['Value']['name'] ?></a></li><?
		}?>
        </ul>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <?php
			//echo $this->Form->input('Value', console::$htmlInput);
			
			$self = $this;
			$paths = array();
			$counter = -1;
			$values[-1] = 'Timestamp';
			$inputReplace = function($matches) use ($limiter, $values, $self, &$paths, &$counter, $values, $saved_paths) {
				$v = $matches[1];
				$path = stripslashes($matches[2]);
				//debug($v);
				if($paths[$path]) return $v;
				$paths[$path] = true;
				$counter++;
				if(!$self->request->data['Value']['Value'][$counter] && $saved_paths[$path])
					$self->request->data['Value']['Value'][$counter] = $saved_paths[$path];
					
				return '<input type="hidden" name="data[Value][path]['.$counter.']" value="'.$path.'" />'.
						$self->Form->input('Value.Value.'.$counter, array(
							'type' => 'select', 'options' => $values, 'data-toggle' => "tooltip", 
							'title' => $v, 'empty' => '  --------', 'label' => false, 'div' => false, 
							'value' => $self->request->data['Value']['Value'][$counter], 'multiple' => false)
						);
			};
			if($skeleton) {
        	$skeleton = preg_replace_callback('!'.$marker.'(.*?)'.$limiter.'(.*?)'.$marker.'!', $inputReplace, $skeleton);
		?>
        <div class="row" >
        	<pre style="max-height:400px;" class="col-md-12"><?= $skeleton ?></pre>
        </div>
        <? } ?>
    </div>
</div>