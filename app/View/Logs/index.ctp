<? 
	echo $this->Html->Css('datepicker3');
	echo $this->Html->Css('morris');
?>
<div class="row">
	<div class="col-md-12"><h1 class="page-header">Logs</h1></div>
</div>
<div class="row">
	<div class="col-md-12"><div id="log-chart" class="chart"></div></div>
</div>
<div class="row">
    <div id="logs" class="col-md-6">
        <ul class="list">
        <?php
        foreach ($logs as $log): ?>
        <li>
            <h4><?php echo $this->Html->link($log['Log']['title'], array('action' => 'view', $log['Log']['id']), array('target' => '_dialog')); ?>&nbsp;<?php echo date('d.m.Y, H:i:s', $log['Log']['time']); ?></h3>
            <div><?php echo nl2br($log['Log']['info']); ?>&nbsp;</div>
            <div><?php echo $this->Html->link('Link', '../'.$log['Log']['link'].'/1/1', array('target' => '_blank')); ?>&nbsp;</div>
            <div><?php echo h($log['Log']['error']) ? 'Fehler' : ''; ?>&nbsp;</div>
            <div>
                <?php echo $this->Form->postLink(__('Löschen'), array('action' => 'delete', $log['Log']['id']), null, __('Are you sure you want to delete # %s?', $log['Log']['id'])); ?>
            </div>
        </li>
    <?php endforeach; ?>
    </div>
    <div class="col-md-3">
        <div class="panel panel-default">
			<?=$this->Form->create(null, array('url' => '/logs/index', 'type' => 'get', 'class' => 'panel-body'));?>
                 <div class="row form-group">
                        <div class="col-md-12"><?=$this->Form->input('station_id', array('type' => "select", 'value' => $request['station_id'], 'class' => 'form-control input-lg', 'label'=>"Sender <br />",'options'=>$stations))?></div>
                 </div>
                 <div class="row form-group">
                        <div class="col-md-12"><?=$this->Form->input('type', array('type' => "select", 'value' => $request['type'], 'class' => 'form-control input-lg', 'label'=>"Typ <br />",'options'=>$logTypes))?></div>
                </div>
                 <div class="row form-group">
                    <div class="col-md-6"><?=$this->Form->input("import_date", array('value' => $request['import_date'], 'label' => 'Import-Datum', 'autocomplete' => 'off', 'class' => 'form-control input-lg calendar-input'))?></div>
                    <div class="col-md-6"><?=$this->Form->input("events_date", array('value' => $request['events_date'], 'label' => 'Sende-Datum', 'autocomplete' => 'off', 'class' => 'form-control input-lg calendar-input'))?></div>
                </div>
            <?= $this->Form->end(array('class' => 'btn btn-primary btn-md', 'label' => "Anzeigen")); ?>
        </div>
    </div>
</h4>
<script type="text/javascript">
	<?php /*?>var chartData = <?=json_encode($chartData)?>;
	console.log(chartData);
	var ykeys = <?=json_encode($ykeys)?>;<?php */?>
	//$('#log-chart').html(app.loadIndicator());
</script>