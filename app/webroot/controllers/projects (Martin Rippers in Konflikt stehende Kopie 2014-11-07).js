app.controllers.projects = BackboneMVC.Controller.extend({
    name: 'projects',
	views:[], //These are the views we will use with the controller
	current: 0,
	setCurrent: function(c){
		this.current = c;
		if(c)
			$('#project-link').html(c.name).attr('href', app.baseUrl+'projects/view/'+c.id);
		else
			$('#project-link').html('Projekte').attr('href', app.baseUrl+'projects/index/');
	},
	
    view:function(id){
		var self = this;
		app.loadPage(this.name, 'view', app.url()).done(function(d){
			self.setCurrent(d.vars.project.Project);
		});
    },
	
	add:function(id){
		var self = this;
		app.loadPage(this.name, 'add', app.url()).done(function(d){
			
		});
    },
	
	import:function(id){
		var self = this;
		var dialog = $('#project-view-modal');
		app.loadDialog(this.name, 'import', app.url(), dialog).done(function(d){
			
		});
    },
	
	delete:function(id){
		var self = this;
		app.post(this.name+'/delete/' + app.url()).done(function(d){
			alert(d);
			alert(d.vars.message);
		});
    },
	
	edit:function(id){
		var self = this;
		app.loadPage(this.name, 'edit', app.url()).done(function(d){
			
		});
    },
	
    init:function(){
     	
    },
	default:function(filter){this.index(filter)},
    index:function(filter){
		this.setCurrent(0);
		var self = this;
		app.loadPage(this.name, 'index', app.url(filter)).done(function(d){
			app.require(['calendar'], function() {
				$('.calendar-input').datepicker({
					language: "de",
					autoclose: true,
					todayHighlight: true
				});
			});
			$('#EventIndexForm').submit(function(){
				var href = $(this).attr('action');
				var query = $(this).serialize();
				if(query.indexOf('fetch=1') > -1)
					app.uncache(href+'/?'+query.replace(/\&fetch\=1/, ''));
			});
		});
    }
});