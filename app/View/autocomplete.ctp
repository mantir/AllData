<?= $this->Html->Script('jquery.autocomplete'); ?>
<?= $this->Html->Script('muzup.autocomplete'); ?>
<? echo $this->Html->Css('autocomplete');
//echo $this->Html->Css('token.input/token-input-mac'); ?>
<script type="text/javascript">
	$(document).ready(function(e) {
       var ac = $('#query').muAutocomplete({
	   		url: "<?=$this->request->here?>/", 
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
			<? if($return['controller'] == 'tags'): ?>
			delimiter: ',',
			<? endif; ?>
			<? if($return['controller'] == 'zipcodes'): ?>
			distance: 0,
			property: 'zip',
			<? endif; ?>
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
    });
</script>
<?= $this->Form->create(false) ?>
<?= $this->Form->input('query'); ?>
<?= $this->Form->end(); ?>