app.controllers.events = BackboneMVC.Controller.extend({
    name: 'values',
	views:[], //These are the views we will use with the controller
	
	method_change: function(){
		var method_id = $('#ValueMethodId').val();
		if(!method_id) {
			$('#value-method').hide();
			$('#method-params').html('');
		} else {
			$('#value-method').hide();
			$('#value-method').show();
			$('#method-params').html(app.loadIndicator());
			app.get('methods/get_params/'+method_id).done(function(d){
				$('#method-params').html(d.content);
			});
		}
		
	},
	
	add:function(id){
		var self = this;
		var dialog = $('#value-view-modal');
		app.loadDialog(this.name, 'add', app.url(), dialog).done(function(d){
			$('#ValueMethodId').change(self.method_change);
		});
    },
	
	edit:function(id){
		var self = this;
		app.setRefresh();
		var dialog = $('#value-view-modal');
		app.loadDialog(this.name, 'edit', app.url(), dialog).done(function(d){
			$('#ValueMethodId').change(self.method_change);
			/*if(dialog.length) {
				dialog.find('form').submit(function(e){
					app.submit(e, function(){
						dialog.modal('hide');
					});
				});
			}*/
		});
    },
	
	delete:function(id){
		var self = this;
		app.post(this.name+'/delete/' + app.url()).done(function(d){
			alert(d);
			alert(d.vars.message);
		});
    },
	
    init:function(){
     	
    },
	default:function(){this.index()},
    index:function(project_id){
		var self = this;
		app.loadPage(this.name, 'index', app.url()).done(function(d){

		});
    }
});