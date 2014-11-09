app.controllers.events = BackboneMVC.Controller.extend({
    name: 'pages',
	views:[], //These are the views we will use with the controller
	
    home:function(){
		var self = this;
		app.loadPage(this.name, '/', app.url()).done(function(d){
			if(!d) return;
		});
    }
});