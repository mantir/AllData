<span><?=$method['Method']['description']?></span>
<h4>Params</h4>
<? if(is_array($params)) 
foreach($params as $p) { 
	if($p['type'] == 'val') {
		echo $this->Form->input('params.'.$p['name'], array_merge(console::$htmlInput, array('type' => 'select', 'between' => ' '.$p['description'], 'options' => $values, 'selected' => $this->request->query[$p['name']])));
	} else { 
		echo $this->Form->input('params.'.$p['name'], array_merge(console::$htmlInput, array('type' => 'text', 'between' => ' '.$p['description']))); 
	} ?>
<? } ?>