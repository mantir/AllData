app.controllers.projects = BackboneMVC.Controller.extend({
    name: 'projects',
	current: 0, //current project
	measured_data: {},
	loaded_value:{},
	initialized: false,
	w_resize:function(){}, //window resize function
	charts: [],
	setCurrent: function(c){ //Sets the current project {id:"", name:""}
		this.current = c;
		if(c && c.id) {
			$('#project-name-link').html(c.name);
			$('#project-link').attr('href', app.baseUrl+'projects/view/'+c.id);
			$('#projects-link').hide();
			$('#data-link').attr('href', app.baseUrl+'projects/data/'+c.id);
			$('#data-link,#import-link,#project-link').show();
		}
		else {
			$('#project-link,#data-link,#import-link').hide();
			$('#projects-link').show();
		}
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
	
	upload_import:function(id){
		var self = this;
		var dialog = $('#project-view-modal');
		app.loadDialog(this.name, 'import', app.url(), dialog).done(function(d){
			$('#Input').change(function(){
				var vid = $('#Input').val();
				app.get('inputs/view/' + vid).done(function(d){
					alert(1);
				});
			});
			$('#import_links').submit(function(){
				self.fetch_links(d.vars);
				return false;
			});
		});
    },
	
	fetch_links: function(d) {
		var self = this;
		if(self.importing) return;
		console.log(d);
		var $ach = $('input[type=checkbox]');
		$ach.attr('disabled', true);
		var $ch = $('input[type=checkbox]:checked');
		$('#show-data-btn').show();
		if(!$ch.length)
			return $ach.removeAttr('disabled');
		self.importing = true;
		var index = $ach.index($ch.first());
		var link = d.links[index];
		$ch.first().siblings('label').append(app.loadIndicator());
		$('#source-link-container').scrollTop($('#source-link-container').scrollTop() + $ch.first().offset().top - 300);
		$.get(app.baseUrl+'json/projects/update_imports/' + d.input.Input.id, { "link": link }, function(g){
			console.log(g);
			self.importing = false;
			$ch.first().siblings('label').append(' Imported');
			$ch.first().siblings('label').find('.loader').remove();
			$ch.first().parents('tr').find('td:last').html(date('d.m.Y, H:i'));
			$ch.first().attr('checked', false);
			self.fetch_links(d);
		});
	},
	
	addSelectedValuesToForm: function(){
		var values = $('.value-checkbox:checked').map(function(d,i){ return $(this).data('value_id') } ).get();
		$('#form-value-ids').html($.map(values, function(d){
			return '<input type="hidden" name="values[]" value="'+d+'" />';
		}));
	},
	
	updateDataUrl:function(){
		this.addSelectedValuesToForm();
		var $project_form = $('#ProjectDataForm');
		var query = $project_form.serialize();
		app.route('/projects/data/'+this.current.id+'?'+query, true, true);
	},
	
	data:function(params){
		var self = this;
		this.loaded_value = {};
		app.loadPage(this.name, 'data', app.url()).done(function(d){
			self.setCurrent(d.vars.project.Project);
			app.require(['calendar'], function(Chart) {
				var p = d = d.vars;
				self.measured_data = d;
				console.log(d);
				$('.value-link').click(function(){
					var id = $(this).data('value_id');
					$('.value-checkbox').prop('checked', false);
					$('#value_'+id).prop('checked', true);
					self.updateDataUrl();
					self.drawGraphs();
				});
				$('.value-method-link').click(function(e) {
                    var id = $(this).data('value_id');
					self.calculateValue(id, $('[name=start]').val(), $('[name=end]').val(), $('[name=start_hour]').val(), $('[name=start_minute]').val(), $('[name=end_hour]').val(), $('[name=end_minute]').val());
                });
				//click on a value
				$('.value-checkbox,#diagram_type').change(function(){
					self.updateDataUrl();
					self.drawGraphs();
				});
				
				$('.make-null').click(function(){
					$(this).prevAll('select').each(function(index, element) {
                        $(this).val(0);
                    });
				});
				var $project_form = $('#ProjectDataForm');
				//when a method is executed
				var $method_select = $('#method_id');
				var currentMethod = 0;
				var method_function = function(){
					var method_id = $('#method_id').val();
					if(!method_id) return;
					currentMethod = method_id;
					var dialog = $('#method-view-modal');
					app.setRefresh();
					self.addSelectedValuesToForm();
					var p = $project_form.serialize();
					app.loadDialog('method', 'view', 'methods/view/'+method_id+'/'+self.current.id+'?'+p, dialog).done(function(d){
						$('.make-null').click(function(){
							$(this).prevAll('select').each(function(index, element) {
								$(this).val(0);
							});
						});
						$('#calculate-form').submit(function(){
							var params = $(this).serialize();
							app.setRefresh();
							$(this).find('input').attr('disabled', 'disabled'); 
							$(this).append(app.loadIndicator());
							app.get('methods/execute/'+method_id+'/?'+params).done(function(d){
								console.log(d);
								dialog.modal('hide');
								self.calculatedMethod(d);
							});
							return false;
						});
					});
					return false;
				}
				$method_select.change(method_function);
				$('#run-method-btn').click(method_function);
				//when the filter form is submitted
				$project_form.submit(self.addSelectedValuesToForm);
				$('.calendar-input').datepicker($.extend(app.config['calendar'], {
					startDate: '1.1.2014',
					endDate: new Date()
				}));
				//$('#project-data').css('overflow', 'hidden !important');
				
				if(!self.initialized) {
					self.initialized = true;
					self.w_resize = function(){
						$('body').scrollTop();
						if($('#value-list').length)
							$('#value-list').height($(window).height() - $('#value-list').offset().top - 9);
						$('.chart-container').height($(window).height() - 160);
						$('#charts').height($(window).height() - 160);
						$('#project-data').height($(window).height() - $('#project-data').offset().top - 9);
						window.setTimeout(function(){
							for(var i in self.charts) {
								self.charts[i].redraw();
							}
						}, 10);
						
					};
					$(window).resize(self.w_resize);
				}
				$('.nav-tabs a').click(function(){ window.setTimeout(self.w_resize, 10)});
				self.w_resize();
				self.drawGraphs();
				self.w_resize();
			});
		});
    },
	
	calculatedMethod: function(d, method_id){
		var res = d.vars.result;
		if(typeof(data) == 'string') {
			alert(data);
			return;
		}
		var caption = d.vars.execName;
		var data = {'Value':{'prefix': '', 'name':caption},'Measure':res, 'Unit':{ 'symbol': '', 'name' : ''}};
		this.measured_data.values[-1] = data;
		this.loaded_value[-1] = true;
		$('#method-value').find('input')[0].checked = true;
		$('#method-value').find('a').html(caption);
		$('#method-value').show();
		this.drawGraphs();
	},
	
	/*
	
	*/
	calculateValue: function(id, startDate, endDate, startHour, startMinute, endHour, endMinute){
		if(this.calculating_value) {
			alert('It can only one value be calculated at once.');
			return;
		}
		this.calculating_value = true;
		var start = startDate+', '+startHour+':'+startMinute;
		var end = endDate+', '+endHour+':'+endMinute;
		$('.value-method-link[data-value_id='+id+']').html(app.loadIndicator2());
		var self = this;
		app.get('projects/calculate_value/'+id+'/?'+$.param({start:start, end:end})).done(function(d){
			self.calculating_value = false;
			$('.value-method-link[data-value_id='+id+']').remove();
			delete self.loaded_value[id];
			$('.value-link[data-value_id='+id+']').click();
		});
	},
	
	/*converts an array of values with measures from the database into the chart format
	@p: object from the server, with values and it's measures
	*/
	data_to_chart_format: function(values){
		var data = {};
		for(var i in values) { //convert data structure for diagram
			var v = values[i];
			data[i] = [];
			if(v.Measure.length > 0) {
				for(var j in v.Measure) {
					var m = v.Measure[j];
					if($.isNumeric(m.d))
						data[i].push([m.t * 1000, parseFloat(m.d)]); //m.t is the timestamp and m.d is the data
				}
			}
		}
		return data;
	},
	
	/*draws the data-charts for the selected values
	*/
	drawGraphs:function(){
		var p = this.measured_data;
		var data = this.data_to_chart_format(p.values);
		var diagram_type = $('#diagram_type').val();
		console.log(diagram_type);
		this.charts = [];
		var start = false;
		var value_ids = $('.value-checkbox:checked').map(function(){ return $(this).data('value_id'); }).get();
		var the_data = {};
		var self = this;
		$('#charts').html(app.loadIndicator()); //clear the chart container
		$('#charts').html('<div id="chart" class="chart-container">'+app.loadIndicator()+'</div>');

		for(var i in value_ids) {
			//if measured data is already loaded for the value add it to the data array
			if(this.loaded_value[value_ids[i]]) {
				the_data[value_ids[i]] = data[value_ids[i]];
			} else { //otherwise load the data for the value from the server
				var params = utils.getUrlParameters(window.location.href);
				params.value_id = value_ids[i];
				$.get(app.baseUrl+app.jsonUrl+'projects/data/'+this.current.id, params, function(d){
					d = eval('('+d+')');
					self.loaded_value[params.value_id] = true;
					self.measured_data.values[params.value_id] = d.vars.values[params.value_id];
					self.drawGraphs();
				});
				return;
			}
		}
		data = the_data;
		
		$charts = $('.chart-container');
		
		console.log(data);
		app.require(['charts'], function(Chart) {
			Highcharts.setOptions({
				global: {
					//timezoneOffset: 0,
					useUTC: false //Show time in browser timezone settings
				}
			});
			var opts = {
				chart: {
					zoomType: 'x'
				},
				xAxis: {
					type: 'datetime',
					//minRange: 14 * 24 * 3600000 // fourteen days
				},
				legend: {
					enabled: false
				},
				rangeSelector : {
					selected:5,
					buttons: [{ //Zoom Buttons top left corner
						type: 'hour',
						count: 1,
						text: '1h'
					}, {
						type: 'day',
						count: 1,
						text: '1d'
					}, {
						type: 'week',
						count: 1,
						text: '1w'
					}, {
						type: 'month',
						count: 1,
						text: '1m'
					}, {
						type: 'year',
						count: 1,
						text: '1y'
					}, {
						type: 'all',
						text: 'All'
					}]
				},
				credits: {
					text: '',
					href: '',
					position: {
						x: -40
					}
				},
				plotOptions: {
					series: {
						animation:false //no animation
					},
					area: { //Area filling
						stacking: 'normal',
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
				useUTC: false,
				exporting: { //Export parameters
					sourceWidth: 1000,
					sourceHeight: 750,
					buttons: {
						contextButton: {
							text: 'Export'
						}
					}
				}
			};
			var tooltip_point_format = '<span style="color:{series.color}">\u25CF</span> {series.name}: <b>{point.y}</b><br/>';
			var aSeries = {
					type: diagram_type,
					pointInterval: 24 * 3600 * 1000,
					//pointStart: start,
			};
			var get_y_text = function(i){ return (p.values[i].Value.prefix ? p.unit_prefixes[p.values[i].Value.prefix] : '') + p.values[i].Unit.name + ' (' + p.values[i].Value.prefix + p.values[i].Unit.symbol + ')'; };
			
			var $chart = $('#chart');
			var counter = -1;
			var yAxis = $.map(data, function(d,i){
				counter++;
				var yAx = {
					title: {
						text: p.values[i].Value.name
					},
					opposite:  counter > 0,
					labels: {
						format: '{value} ' + p.values[i].Value.prefix + p.values[i].Unit.symbol,
						style: {
							color: Highcharts.getOptions().colors[counter]
						}
					},
				}
				if(p.values[i].Value.id == undefined && data.length > 1) {
					yAx.linkedTo = 0;
				}
				return yAx;
			})
			var counter = -1;
			var series = $.map(data, function(d,i) { 
				counter++;
				return $.extend(true, {},  $.extend(aSeries, {
					yAxis:counter, 
					data:d, 
					name: p.values[i].Value.name,
					fillColor : {
						linearGradient : {
							x1: counter,
							y1: counter,
							x2: counter,
							y2: counter + 1
						},
						stops : [
							[0, Highcharts.Color(Highcharts.getOptions().colors[counter]).setOpacity(0.7).get('rgba')],
							[1, Highcharts.Color(Highcharts.getOptions().colors[counter]).setOpacity(0).get('rgba')]
						]
					},
				})); 
			});

			var chartOpts = $.extend(opts, {
				chart: $.extend(opts.chart, {
					renderTo: "charts"
				}),
				title: {
					text: $.map(data, function(d, i) { return $.trim(p.values[i].Value.name) }).join(', ')
				},
				subtitle: {
					text: ''//p.project.Project.name
				},
				tooltip: {
					shared: true,
					pointFormat: tooltip_point_format,
					valueDecimals : 2
				},
				legend: {
					layout: 'vertical',
					align: 'left',
					x: 120,
					verticalAlign: 'top',
					y: 10,
					floating: true,
					backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
				},
				yAxis: yAxis,
				series: series
			});
			var chart = new Highcharts.StockChart(chartOpts);
			
			self.charts.push(chart);

			$(window).trigger('resize');
			
			$('.chart-container').height($(window).height() - 160);
			
		});
	},
	
	delete:function(id){
		var self = this;
		app.post(this.name+'/delete/' + app.url()).done(function(d){
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