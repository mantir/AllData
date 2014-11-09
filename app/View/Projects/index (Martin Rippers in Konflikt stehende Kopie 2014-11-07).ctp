<div class="row">
	<div class="col-md-12">
    	<h2><?php echo __('Projekte'); ?> <?php echo $this->Html->link(__('+ Neues Projekt erstellen'), array('action' => 'add'), array( 'class' => 'btn btn-primary')); ?></h2>
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
        'format' => __('Seite {:page}/{:pages}')
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