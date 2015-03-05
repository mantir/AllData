<?
foreach($logs as $l){
	switch($l['type']) {
		case 'import':
			$t = __('Imported data %s (%s measures%s) into %s %s via %s ', 
				$this->Html->link(
					date('d.m.Y', $l['start']).' - '.date('d.m.Y', $l['end']), 
					'/projects/data/'.$project_id.'?start='.date('d.m.Y', $l['start']).'&end='.date('d.m.Y', $l['end'])
				),
				$l['measure_count'],
				', '.$l['lines'].' lines',
				$this->Html->link('file', $l['filename'], array('target' => '_blank')), 
				$l['url'] ? 'from '.$this->Html->link('url', $l['url'], array('target' => '_blank')) : 'manually',
				$this->Html->link($inputs[$l['Input']]['name'], array('controller' => 'inputs', 'action' => 'edit', $l['Input']), array('target' => '_blank'))
				//'<div class="btn-group pull-right">'.$this->Html->link(console::$icons['refresh'], array('controller' => 'projects', 'action' => 'import', '?' => array('filename' => $l['filename']), $l['Input']), array('title' => __('Import file again'), 'escape' => false)).'</div>'
			);
		break;
		case 'user':
			$t = __('%s %s %s %s', 
				$this->Html->link($l['username'], '/users/view/'.$l['user']),
				$l['action'],
				Inflector::singularize($l['model']),
				$l['related_id'] ? $this->Html->link($l['name'], '/'.$l['model'].'/'.($l['model'] == 'users' ? 'view' : 'edit').'/'.$l['related_id']) : $l['name']
			);
		break;
	}
	?><tr><td><?= $t ?></td><td><?=date('H:i:s, d.m.Y', $l['time'])?></td></tr><?
}
?>