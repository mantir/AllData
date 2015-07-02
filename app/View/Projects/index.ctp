<div class="row">
	<div class="col-md-12">
    	<h2><?php echo __('Projects'); ?> <?php echo $this->Html->link(__('+ Create new Project'), array('action' => 'add'), array( 'class' => 'btn btn-primary')); ?></h2>
    </div>
</div>
<div class="row">
	<div class="col-md-12">
        
        <table class="table table-hover">
        <?php if(is_array($project)) {
        foreach ($projects as $project): ?>
        <tr>
            <td><h2><?= $this->Html->link(h($project['name']), array('action' => 'view', $project['id'])); ?></h2></td>
        </tr>
    <?php endforeach; } else { 
		?><td><?=__('Here will be shown your projects, when you created them.')?></td><?
	} ?>
        </table>
    </div>
</div>