app.controllers.logs = BackboneMVC.Controller.extend({
    name: 'logs',
	views:[], //These are the views we will use with the controller
	
    view:function(id){
		var self = this;
		app.loadPage(this.name, 'view', app.url()).done(function(d){

		});
    },
	
    init:function(){
     	
    },
	default:function(filter){this.index(filter)},
    index:function(filter){
		var self = this;
		app.loadPage(this.name, 'index', app.url()).done(function(d){
			console.log(d);
			app.require(['charts', 'calendar'], function(){
				if(d.vars.chartData.length > 0) {
					new Morris.Area({
						element:'log-chart',
						data: d.vars.chartData,
						ykeys: d.vars.ykeys,
						labels: d.vars.ykeys,
						xkey:'x',
					});
				}
				$('#LogImportDate').datepicker(app.config['calendar']);
				$('#LogEventsDate').datepicker(app.config['calendar']);
				$('#LogImportDate, #LogEventsDate').dblclick(function(){
					$(this).val('');
				});
			});
		});
    }
});