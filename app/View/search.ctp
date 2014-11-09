<?= $this->Html->Script('jquery.autocomplete'); ?>
<?= $this->Html->Script('muzup.autocomplete'); ?>
<? echo $this->Html->Css('autocomplete');
//echo $this->Html->Css('token.input/token-input-mac'); ?>
<script type="text/javascript">
	$(document).ready(function(e) {
       var ac = $('#tag_search').muAutocomplete({
	   		url: "<?=$this->Html->url(array('controller' => 'tags', 'action' => 'autocomplete'))?>/", 
		   	property : 'name',
			<? if($return['controller'] == 'genres'): ?>
			/*params:{'conditions':{ //For topgenres
				'count >=' : 1000,
				'topgenre_id' : 0
			}},*/
			params:{'conditions':{ //For subgenres of id=12
				'topgenre_id' : 12
			}},
			<? endif; ?>
			delimiter: ',',
			<? if($return['controller'] == 'brands'): ?>
			params:{ 
				distance : 0
			},
			<? endif; ?>
			onSelect : function(value, data){
				console.log(data);
				/*
					data.name, data.id you can use here.
				*/
			},
			
		});
		 var zip = $('#plz').muAutocomplete({
	   		url: "<?=$this->Html->url(array('controller' => 'zipcodes', 'action' => 'autocomplete'))?>/", 
		   	property : 'zip',
			params:{ 
				distance : 0
			},
			onSelect : function(value, data){
				console.log(data);
				/*
					data.name, data.id you can use here.
				*/
			},
			
		});
    });
</script>
<?= $this->Form->create(false) ?>
<?= $this->Form->input('tag_search'); ?>
<?= $this->Form->select('category', $categories, array('label' => 'Category', 'empty' => __('all'))); ?>
<?= $this->Form->input('plz'); ?>
<?= $this->Form->end(__('Search')); ?>