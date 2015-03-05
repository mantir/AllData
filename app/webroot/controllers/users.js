app.controllers.users = BackboneMVC.Controller.extend({
    name: 'users',
	views:[], //These are the views we will use with the controller
	
	login:function(){
		var self = this;
		app.loadPage(this.name, 'login', app.url()).done(function(d){
			
		});
    },
	
	logout:function(){
		window.location.href = window.location.href;
	},
	
	settings:function(){
		var self = this;
		app.loadPage(this.name, 'settings', app.url()).done(function(d){
			
		});
    },
	
	view:function(id){
		var self = this;
		var dialog = $('#user-view-modal');
		app.loadDialog(this.name, 'profile', app.url(), dialog).done(function(d){
			
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
	
	resetPassword:function(){
		var self = this;
		app.loadPage(this.name, 'resetPassword', app.url()).done(function(d){

		});
    },
	
	changePassword:function(){
		var self = this;
		app.loadPage(this.name, 'resetPassword', app.url()).done(function(d){

		});
    },
	
    register:function(){
		var self = this;
		app.loadPage(this.name, 'register', app.url()).done(function(d){

		});
    }
});