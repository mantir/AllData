app.controllers.projects = BackboneMVC.Controller.extend({
    name: 'projects',
	views:[], //These are the views we will use with the controller
	current: 0,
	setCurrent: function(c){
		this.current = c;
		if(c && c.id)
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
		var dialog = $('#project-view-modal');
		app.loadDialog(this.name, 'add', app.url(), dialog).done(function(d){
			
		});
    },
	
	import:function(id){
		var self = this;
		var dialog = $('#project-view-modal');
		app.loadDialog(this.name, 'import', app.url(), dialog).done(function(d){
			
		});
    },
	
	data:function(params){
		var self = this;
		app.loadPage(this.name, 'data', app.url()).done(function(d){
			var p = d = d.vars;
			console.log(d);
			var data = {};
			var start = d.values[1].Measure[0].timestamp * 1000;
			console.log(start);
			for(var i in d.values) {
				var v = d.values[i];
				if(v.Measure.length > 0) {
					data[i] = [];
					for(var j in v.Measure) {
						var m = v.Measure[j];
						data[i].push([m.timestamp * 1000, parseFloat(m.data)]);
					}
				}
			}
			console.log(data);
			app.require(['charts'], function(Chart) {
				for(var i in data) {
					d = data[i];
					$('#chart'+i).highcharts({
						chart: {
							zoomType: 'x'
						},
						title: {
							text: 'Messdaten ' + p.project.Project.name
						},
						subtitle: {
							text: p.values[i].Value.name
						},
						xAxis: {
							type: 'datetime',
							//minRange: 14 * 24 * 3600000 // fourteen days
						},
						yAxis: {
							title: {
								text: p.values[i].Unit.name + ' (' + p.values[i].Unit.symbol + ')'
							}
						},
						legend: {
							enabled: false
						},
						plotOptions: {
							area: {
								fillColor: {
									linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1},
									stops: [
										[0, Highcharts.getOptions().colors[0]],
										[1, Highcharts.Color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
									]
								},
								marker: {
									radius: 2
								},
								lineWidth: 1,
								states: {
									hover: {
										lineWidth: 1
									}
								},
								threshold: null
							}
						},
				
						series: [{
							type: 'area',
							name: p.values[i].Value.name,
							pointInterval: 24 * 3600 * 1000,
							pointStart: start,
							data: d
						}]
					});
				} //End for
			});
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
		var dialog = $('#project-view-modal');
		app.loadDialog(this.name, 'edit', app.url(), dialog).done(function(d){
			
		});
    },
	
    init:function(){
     	
    },
	default:function(filter){this.index(filter)},
    index:function(filter){
		this.setCurrent(0);
		var self = this;
		app.loadPage(this.name, 'index', app.url(filter)).done(function(d){

		});
    }
});