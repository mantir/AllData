<div class="row">
	<div class="col-md-12">
    	<h2><?php echo __('Projects'); ?> <?php echo $this->Html->link(__('+ Create new Project'), array('action' => 'add'), array( 'class' => 'btn btn-primary')); ?></h2>
    </div>
</div>
<div class="row">
	<div class="col-md-12">
        
        <table class="table">
        <tr>
                <th><?php echo $this->Paginator->sort('name'); ?></th>
        </tr>
        <?php
        foreach ($projects as $project): ?>
        <tr>
            <td><h2><?= $this->Html->link(h($project['Project']['name']), array('action' => 'view', $project['Project']['id'])); ?></h2></td>
        </tr>
    <?php endforeach; ?>
        </table>
        <p>
        <?php
        echo $this->Paginator->counter(array(
        'format' => __('page {:page}/{:pages}')
        ));
        ?>	</p>
    
        <div class="paging">
        <?php
            echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev hidden'));
            echo $this->Paginator->numbers(array('separator' => ''));
            echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next hidden'));
        ?>
        </div>
    </div>
</div>

<div id="project-view-modal" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Project</h4>
      </div>
      <div class="modal-body">
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?=__('Close');?></button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->