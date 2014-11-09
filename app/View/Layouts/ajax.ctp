<?
$message = $this->Session->flash();
if(!$isAjax):
	$cakeDescription = __d('cake_dev', 'Console');
	?>
	<?=$this->element('header', compact('cakeDescription', 'titleForLayout')); ?>
		<?php
					echo $message;
		?>
		<?php echo $this->fetch('content'); ?>
	
	<? echo $this->element('footer');
endif;

if($loadTemplate)
	$return['content'] = $this->fetch('content');
//unset($return['vars']);
if(is_array($return['passedArgs']))
	$return['routeUrl'] = $this->Html->url(array_merge($return['passedArgs'], array('controller' => $return['controller'], 'action' => $return['action'])));
if($isAjax):		
	echo json_encode($return);
else:
	debug($return);
	echo $this->element('sql_dump');
endif; ?>
