<ul style="float:left;" class="sidebar">
	<li><h3>Navigation</h3></li>
	<? foreach($documentations as $d) : ?>
	<li>
		<?=$this->Html->link($d['Documentation']['title'], array('controller' => 'documentations', 'action' => 'view', $d['Documentation']['id']));?>
        <? if(count($d['Child'])) : ?><ul><?
			foreach($d['Child'] as $doc){
				?><li><?= $this->Html->link($doc['title'], array('controller' => 'documentations', 'action' => 'view', $doc['id']));?></li><?
			}
		?></ul><? endif; ?>
        
    </li>
    <? endforeach; ?>
</ul>