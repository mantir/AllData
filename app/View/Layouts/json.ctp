<?
$message = $this->Session->flash();
$isAjax = true;
$return['content'] = $this->fetch('content');

unset($return['vars']['return']);
if(is_array($return['passedArgs']))
	$return['routeUrl'] = $this->Html->url(array_merge($return['passedArgs'], array('controller' => $return['controller'], 'action' => $return['action'])));
if($isAjax):		
	echo json_encode($return);
else:
	debug($return);
	echo $this->element('sql_dump');
endif; ?>
