app.controllers.documentations = BackboneMVC.Controller.extend({
    name: 'documentations',
	views:[], //These are the views we will use with the controller
	
    view:function(id){
		var self = this;
		app.loadPage(this.name, 'view', app.url()).done(function(d){

		});
    },
	edit:function(id){
		var self = this;
		app.loadPage(this.name, 'edit', app.url()).done(function(d){
			app.require(['editor'], function(){
				CKEDITOR.replace('DocumentationText');
				//nicEditors.allTextAreas(app.config['editor']);//$('#DocumentationText').htmlarea();
			});
		});
    },
	add:function(){
		var self = this;
		app.loadPage(this.name, 'add', app.url()).done(function(d){
			app.require(['editor'], function(){
				CKEDITOR.replace('DocumentationText');
				//nicEditors.allTextAreas();//$('#DocumentationText').htmlarea();
			});
		});
    },
	
    init:function(){
     	
    },
	
	default:function(){this.index()},
    index:function(filter){
		var self = this;
		app.loadPage(this.name, 'index', app.url()).done(function(d){
			if(!d) return;
		});
    }
});