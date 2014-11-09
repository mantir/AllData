<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="shortcut icon" href="<?=FULL_BASE_URL.$this->Html->url('/favicon.ico')?>" />
<? 	$this->Html->css('muzup', null, array('inline' => false));
	$this->Html->script('jquery-1.7.2.min', array('inline' => false));
	echo $this->fetch('css'); 
	echo $this->fetch('script');?>
<meta name="robots" content="index, follow" />
<meta name="keywords" content="" />

<title>MuzUp</title>
</head>
<body>    
    <div id="container">
        <div id="header">
        	<? echo $this->Html->link($this->Html->image('logo.png', array('id' => 'header-img')), array('addy' => false, 'controller' => 'pages', 'action' => 'display', 'home'), array('title' => __('home'), 'escape' => false)); 
			?>
            <? if($loggedIn): ?>
            	Logged in as: <? echo $return['vars']['authUser']['name']; ?> (<?=$return['vars']['authUser']['email']?>)
			<? endif; ?>
        </div>
        <div id="mainContent">
        	<div id="pageContent">