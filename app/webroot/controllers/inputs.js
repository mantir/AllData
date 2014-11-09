app.controllers.events = BackboneMVC.Controller.extend({
    name: 'inputs',
	views:[], //These are the views we will use with the controller
	
	add:function(id){
		var self = this;
		var dialog = $('#input-view-modal');
		app.loadDialog(this.name, 'add', app.url(), dialog).done(function(d){
			
		});
    },
	
	edit:function(id){
		var self = this;
		app.loadPage(this.name, 'edit', app.url()).done(function(d){
			$('pre > select').tooltip();
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