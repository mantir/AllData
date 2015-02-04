 <? //debug($return);
unset($return['vars']['return']); //clear the variable in $return that include $return again (cakephp stuff)
?><!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Content-Type" content="text/html;charset=utf-8" / >
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
			$this->Html->Script('utils', array('inline' => false));		
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
                   <a id="project-link" <?=$currentProject ? '' : 'style="display:none"'?> href="<?=$currentProject ? $this->Html->url("/projects/view/".$currentProject['id']) : '' ?>"><i class="fa fa-dashboard fa-fw"></i> <span id="project-name-link"><?=$currentProject ? $currentProject['name'] : '' ?></a></span>
                   <a id="projects-link" <?=!$currentProject ? '' : 'style="display:none"'?> href="<?=$this->Html->url("/projects/index")?>"><i class="fa fa-dashboard fa-fw"></i> <?=__('Projects')?></a>
                </li>
                <li>
                    <a id="data-link" href="<?=$currentProject ? $this->Html->url("/projects/data/".$currentProject['id']) : ''?>"><i class="glyphicon glyphicon-stats"></i> <?=__('Data')?></a>
                </li>
                <li>
                	<a id="import-link" href="<?= $currentProject ? $this->Html->url(array('action' => 'upload_import', $currentProject['id'])) : ''; ?>"><span class="glyphicon glyphicon-upload mr-5"></span> <?=__('Import')?></a>
                </li>
              </ul>
              <ul class="nav navbar-nav navbar-right">
              	<li class="dropdown">
                	<? if($loggedIn) { ?>
                    <a id="user-link" data-toggle="dropdown" href="javascript:;"><i class="fa fa-user"></i> <?=$authUser['name']?> <span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu" aria-labelledby="user-link">
                        <li><a href="<?=$this->Html->url("/users/settings")?>"><i class="fa fa-cog"></i> <?=__('Settings')?></a></li>
                        <li><a href="<?=$this->Html->url("/users/logout")?>"><?=__('Logout') ?></a></li>
                    </ul>
                    <? } else { ?>
                    <a href="<?=$this->Html->url("/users/login")?>"><i class="fa fa-user"></i> <?=__('Login')?></a>
                    <? } ?>
                </li>
                <li>
                    <a href="<?=$this->Html->url("/documentations/index")?>"><i class="glyphicon glyphicon-question-sign"></i> <?=__('Help')?></a>
                </li>
                <? if($authUser && $authUser['isAdmin']) { ?>
                <li>
                    <a href="<?=$this->Html->url("/admin/methods/index")?>"><i class="fa fa-superscript fa-fw"></i> <?=__('Methods')?></a>
                </li>
                <li>
                    <a href="<?=$this->Html->url("/admin/units/index")?>"><i class="fa fa-link"></i> <?=__('Units')?></a>
                </li>
                <? } ?>
              </ul>
            </div>
          </div>
        </nav>
        <div id="page-wrapper" class="container-fluid">
        	<div class='page'>
            	<? $message =  $this->Session->flash();
				if($message) {  ?>
					<div style="position:absolute; z-index:1000" class="alert alert-danger">
					  <?= $message ?>
					</div>
				<? } ?>
				<?= $this->fetch('content'); ?>
            </div>
        </div>
        <script type="text/javascript"> var baseUrl = '<?=$return['base']?>'; </script>
        <?= $this->fetch('script'); ?>
        <script type="text/javascript">
			var data = <? echo json_encode($return); ?>;
			$(document).ready(function(e) {
				app.clearAlerts();
			   /*if($('.cake-debug-output').length)
					$('html').addClass('overflow-auto');*/
				app.init('<?=$return['base']?>', data, $('.page').html());
			});
        </script>
        
        <div id="error-view-modal" class="modal fade">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?=__('Error') ?></h4>
              </div>
              <div class="modal-body">
                
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Close')?></button>
              </div>
            </div><!-- /.modal-content -->
          </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
    </body>
</html>