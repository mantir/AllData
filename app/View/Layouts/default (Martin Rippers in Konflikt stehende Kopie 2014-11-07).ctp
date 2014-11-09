 <? //debug($return);
unset($return['vars']['return']); //clear the variable in $return that include $return again (cakephp stuff)
$this->Session->flash(); //Just clear the flash-messages they are already in $return 
?><!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <? 
			echo $this->Html->Css('bootstrap.min.united'); 
			echo $this->Html->Css('style');
			echo $this->Html->Css('font-awesome/css/font-awesome.min');
			echo $this->Html->Script('jquery-1.11.0.min', array('inline' => true));
			$this->Html->Script('bootstrap.min', array('inline' => false));
			$this->Html->Script('underscore-min', array('inline' => false));
			$this->Html->Script('backbone-min', array('inline' => false));
			$this->Html->Script('backbone.queryparams', array('inline' => false));
			$this->Html->Script('backbone-mvc', array('inline' => false));
			$this->Html->Script('require-min', array('inline' => false));
			$this->Html->Script('q.min', array('inline' => false));	
			$this->Html->Script('../app', array('inline' => false));
		?>
        <title><?=console::$systemName?></title>
        <?
        $return['routeUrl'] = $this->Html->url(array_merge($return['passedArgs'], array('controller' => $return['controller'], 'action' => $return['action'])));
        ?>
        <style type="text/css">
			.overflow-auto{overflow:auto !important}
		</style>
    </head>
    <body>
        <nav class="navbar navbar-trans navbar-default navbar-fixed-top" role="navigation">
          <div class="container-fluid">
            <div class="navbar-header">
              <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapsible">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
              </button>
            </div>
            <div class="navbar-collapse collapse" id="navbar-main">
              <ul class="nav navbar-nav navbar-middle">
              	<li>
                    <a href="<?=$this->Html->url("/projects/index")?>"><strong>AllData</strong></a>
                </li>
                <li>
                	<? if($p = $this->Session->read('Project')) { ?>
                    	<a id="project-link" href="<?=$this->Html->url("/projects/view/".$p['id'])?>"><i class="fa fa-dashboard fa-fw"></i> <?=$p['name']?></a>
                    <? } else { ?>
                    	<a id="project-link" href="<?=$this->Html->url("/projects/index")?>"><i class="fa fa-dashboard fa-fw"></i> Projekte</a>
                    <? } ?>
                </li>
                <li>
                    <a href="<?=$this->Html->url("/documentations/index")?>"><i class="fa fa-edit fa-fw"></i> Dokumentation</a>
                </li>
                <li>
                    <a href="<?=$this->Html->url("/units/index")?>"><i class="fa fa-gear fa-fw"></i> Einheiten</a>
                </li>
              </ul>
              
            </div>
          </div>
        </nav>
        <div id="page-wrapper" class="container-fluid">
        	<? $message =  $this->Session->flash();
			if($message) {  ?>
                <div class="alert alert-dismissable alert-danger">
                  <?= $message ?>
                </div>
            <? } ?>
        	<div class='page'><?= $this->fetch('content'); ?></div>
        </div>
        <script type="text/javascript"> var baseUrl = '<?=$return['base']?>'; </script>
        <?= $this->fetch('script'); ?>
        <script type="text/javascript">
			var data = <? echo json_encode($return); ?>;
			$(document).ready(function(e) {
			   /*if($('.cake-debug-output').length)
					$('html').addClass('overflow-auto');*/
				app.init('<?=$return['base']?>', data, $('.page').html());
			});
        </script>
    </body>
</html>