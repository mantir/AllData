<div class="row">
	<div class="col-md-12">
        <h2><?php echo __('Unit'); ?> <?php echo $this->Html->link(__('+ new Unit'), array('action' => 'add', 'admin' => false), array('class' => 'btn btn-primary btn-sm')); ?></h2>
        <table class="table table-condensed table-bordered table-hover">
        <tr>
                <th><?php echo $this->Paginator->sort('id'); ?></th>
                <th><?php echo $this->Paginator->sort('name'); ?></th>
                <th><?php echo $this->Paginator->sort('symbol'); ?></th>
                <th><?php echo $this->Paginator->sort('Project'); ?></th>
                <th class="actions"><?php echo __('Actions'); ?></th>
        </tr>
        <?php
        foreach ($units as $unit): ?>
        <tr>
            <td><?php echo h($unit['Unit']['id']); ?>&nbsp;</td>
            <td><?php echo $this->Html->link($unit['Unit']['name'], array('action' => 'edit', 'admin' => false, $unit['Unit']['id']), array('escape' => false)); ?>&nbsp;</td>
            <td><?php echo h($unit['Unit']['symbol']); ?>&nbsp;</td>
            <td><?php echo h($unit['Project']['name']); ?>&nbsp;</td>
            <td class="actions">
                <?php echo $this->Html->link(console::$icons['edit'], array('action' => 'edit', 'admin' => false, $unit['Unit']['id']), array('escape' => false)); ?>
                <?php echo $this->Form->postLink(console::$icons['delete'], array('action' => 'delete', 'admin' => false, $unit['Unit']['id']), array('escape' => false), __('Are you sure you want to delete %s?', $unit['Unit']['name'])); ?>
            </td>
        </tr>
    <?php endforeach; ?>
        </table>
    
        <div class="paging">
        <?php
            echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev hidden'));
            echo $this->Paginator->numbers(array('separator' => ''));
            echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next hidden'));
        ?>
        </div>
    </div>
</div>

<div id="unit-view-modal" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Unit</h4>
      </div>
      <div class="modal-body">
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Close')?></button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
