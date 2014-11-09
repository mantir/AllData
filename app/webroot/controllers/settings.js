app.controllers.settings = BackboneMVC.Controller.extend({
    name: 'settings',
	views:[], //These are the views we will use with the controller
	
	default:function(){
		this.index();
	},
	
    index:function(){
		var self = this;
		app.loadPage(this.name, 'index', app.url()).done(function(d){
			if(!d) return;
		});
    },
	
	add:function(){
		var self = this;
		app.loadPage(this.name, 'add', app.url()).done(function(d){
			if(!d) return;
		});
    },
	
	edit:function(){
		var self = this;
		app.loadPage(this.name, 'edit', app.url()).done(function(d){
			if(!d) return;
		});
    }
});