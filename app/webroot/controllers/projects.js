app.controllers.projects = BackboneMVC.Controller.extend({
    name: 'projects',
	current: 0, //current project
	measured_data: {},
	loaded_value:{},
	show_flagged_values:{},
	show_original_values:{},
	value_states: {},
	initialized: false,
	start:0,
	end:0,
	maxZoom:0,
	minZoom:0,
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
			self.loadLogs('import');
			$('#log-selector').find('a').click(function(){
				var type = $(this).data('log');
				self.loadLogs(type, 0);
			});
		});
    },
	
	loadLogs: function(type, page){
		if(!page)
			$('#logs').html('');
		$.get(app.baseUrl + 'logs/view/'+this.current.id+'/'+type+'', function(d){
			$('#logs').append(d);
		})
	},
	
	add:function(id){
		var self = this;
		var dialog = $('#project-view-modal');
		app.loadDialog(this.name, 'add', app.url(), dialog).done(function(d){
			
		});
    },
	
	/**
	* Upload a file or import a list of links from an input source
	* @param id: project id
	*/
	upload_import:function(id){
		var self = this;
		var dialog = $('#project-view-modal');
		app.loadDialog(this.name, 'import', app.url(), dialog).done(function(d){
			if(d.vars.links && d.vars.links.length) {
				app.setRefresh(app.url());
			}
			/*$('#Input').change(function(){
				var vid = $('#Input').val();
				app.get('inputs/view/' + vid).done(function(d){
					
				});
			});*/
			$('#import_links').submit(function(){
				self.fetch_links(d.vars);
				return false;
			});
		});
    },
	
	/**
	* Import an uploaded file 
	* @param id: input id
	* @param $_GET[filename]: the filename of the file to import must be provided in the url
	*/
	import:function(id){
		/*TODO
		Create the logic to import a file that has already been uploaded. E.g. from the logs to import again if there was an error
		*/
    },
	
	/**
	* Fetch all selected file links in the upload_import view
	* @param d: Object from server with available links
	*/
	fetch_links: function(d) {
		var self = this;
		if(self.importing) return;
		//console.log(d);
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
		$.get(app.baseUrl+app.jsonUrl+'projects/update_imports/' + d.input.Input.id, { "link": link }, function(d){
			d = eval('('+d+')');
			//console.log(d);
			self.importing = false;
			$ch.first().siblings('label').append(' Imported');
			$ch.first().siblings('label').find('.loader').remove();
			$ch.first().parents('tr').find('td:last').html(date('d.m.Y, H:i'));
			$ch.first().parents('tr').find('td:eq(1)').html(date('d.m.Y, H:i', d.vars.imported.earliest)+' - '+date('d.m.Y, H:i', d.vars.imported.latest));
			$ch.first().prop('checked', false);
			self.fetch_links(d);
		});
	},
	
	/**
	* Before submitting data the selected values should be added to the form so they are passed over the url so they can be checked in the new view again (E.g. when changing time range)
	* @return true
	*/
	addSelectedValuesToForm: function(){
		var values = $('.value-checkbox:checked').map(function(d,i){ return $(this).data('value_id') } ).get();
		$('#form-value-ids').html($.map(values, function(d){
			return '<input type="hidden" name="values[]" value="'+d+'" />';
		}));
		return true;
	},
	
	/**
	* Update the Url if other values are selected to keep track of the changes in history
	*/
	updateDataUrl:function(){
		this.addSelectedValuesToForm();
		var $project_form = $('#ProjectDataForm');
		var query = $project_form.serialize();
		app.route('/projects/data/'+this.current.id+'?'+query, true, true);
	},
	
	/**
	*
	*/
	data:function(params){
		var self = this;
		this.loaded_value = {};
		app.loadPage(this.name, 'data', app.url()).done(function(d){
			self.setCurrent(d.vars.project.Project);
			app.require(['calendar'], function() {
				var p = d = d.vars;
				self.measured_data = d;
				self.start = d.start;
				self.end = d.end;
				//console.log(d);
				$('.value-link').click(function(){
					var id = $(this).data('value_id');
					self.selectValue(id);
					self.drawGraphs();
				});
				$('.value-method-link').click(function(e) {
                    var id = $(this).data('value_id');
					self.calculateValue(id, $('[name=start]').val(), $('[name=end]').val(), $('[name=start_hour]').val(), $('[name=start_minute]').val(), $('[name=end_hour]').val(), $('[name=end_minute]').val());
                });
				$('.value-edit-link').click(function(e) {
					e.preventDefault();
					e.stopImmediatePropagation();
                    var href = $(this).attr('href');
					//href = href.substr(0, href.indexOf('?') != -1 ? href.indexOf('?') + 1 : href.length);
					href = href+'?redirect='+encodeURIComponent(window.location.href);
					app.route(href);
                });
				$('.value-check-data-link').click(function(e) {
                    var id = $(this).data('value_id');
					self.checkValueData(id, self.start, self.end);
                });
				//click on a value
				$('.value-checkbox,#diagram_type').change(function(){
					self.updateDataUrl();
					self.drawGraphs();
				});
				$('.value-toggle-flag-link').click(function(e) {
					var id = $(this).data('value_id');
                    self.toggleFlags(id);
                });
				$('.value-original-link').click(function(e) {
					var id = $(this).data('value_id');
                    self.showOriginal(id);
                });
				
				$('#manipulate-btn').click(function(){
					self.manipulateFlags($('#point_action').val(), $('#point_class').val());
				});
				
				$('#flag-btn').click(function(){
					self.flag_data($('#flag_class').val());
				});
				
				$('#reset-btn').click(function(){
					self.reset_data($('#reset_class').val());
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
						$('.methods-view').find('.calendar-input').datepicker($.extend(app.config['calendar'], {
							startDate: '1.1.2014',
							endDate: new Date()
						}));
						
						$('#calculate-form').submit(function(){
							var params = $(this).serialize();
							app.setRefresh();
							$(this).find('input').attr('disabled', 'disabled'); 
							$(this).append(app.loadIndicator());
							app.get('methods/execute/'+method_id+'/?'+params).done(function(d){
								//console.log(d);
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
						$('#chart-controls').height(100);
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
	
	/**
	*
	*/
	selectValue:function(id){
		$('.value-checkbox').prop('checked', false);
		$('#value_'+id).prop('checked', true);
		this.updateDataUrl();
	},
	
	/**
	*
	*/
	toggleFlags: function(value_id){
		this.show_flagged_values[value_id] = !this.show_flagged_values[value_id];
		$btn = $('.value-toggle-flag-link[data-value_id='+value_id+']');
		$btn.find('.fa').toggleClass('fa-flag-o', !this.show_flagged_values[value_id]);
		$btn.find('.fa').toggleClass('fa-flag', this.show_flagged_values[value_id]);
		this.selectValue(value_id);
		if( this.show_original_values[value_id] && this.show_flagged_values[value_id] ) {
			this.showOriginal(value_id);
		} else
			this.drawGraphs();
	},
	
	/**
	*
	*/
	showOriginal: function(value_id){
		this.show_original_values[value_id] = !this.show_original_values[value_id];
		$btn = $('.value-original-link[data-value_id='+value_id+']');
		$btn.find('.fa').toggleClass('fa-history', !this.show_original_values[value_id]);
		$btn.find('.fa').toggleClass('fa-bolt', this.show_original_values[value_id]);
		this.selectValue(value_id);
		if( this.show_original_values[value_id] && this.show_flagged_values[value_id] ) {
			this.toggleFlags(value_id);
		} else 
			this.drawGraphs();
	},
	
	/**
	*
	*/
	flag_data: function(flag){
		var start = Math.floor(this.minZoom / 1000); 
		var end = Math.ceil(this.maxZoom / 1000);
		var value_id = this.get_selected_value_ids()[0];
		//console.log(value_id);
		var self = this;
		$('#flag-btn').hide();
		$loader = $(app.loadIndicator2());
		$('#flag-btn').after($loader);
		app.get('values/flag_data/'+value_id+'/'+flag+'/'+start+'/'+end+'/').done(function(d){
			delete self.loaded_value[value_id];
			$loader.remove();
			self.toggleFlags(value_id);
			$('#flag-btn').show();
		});
	},
	
	/**
	*
	*/
	reset_data: function(flag){
		var start = Math.floor(this.minZoom / 1000); 
		var end = Math.ceil(this.maxZoom / 1000);
		var value_id = this.get_selected_value_ids()[0];
		//console.log(value_id);
		var self = this;
		$('#reset-btn').hide();
		$loader = $(app.loadIndicator2());
		$('#reset-btn').after($loader);
		app.get('values/reset_data/'+value_id+'/'+flag+'/'+start+'/'+end+'/').done(function(d){
			delete self.loaded_value[value_id];
			$loader.remove();
			$('#reset-btn').show();
			if( self.show_original_values[value_id] ) {
				self.showOriginal(value_id);
			} else 
				self.drawGraphs();
		});
	},
	
	/**
	*
	*/
	checkValueData: function(value_id, start, end){
		var self = this;
		app.setRefresh();
		$btn = $('.value-check-data-link[data-value_id='+value_id+']');
		var html = $btn.html();
		$btn.html(app.loadIndicator2());
		app.get('values/check_data/'+value_id+'/'+start+'/'+end).done(function(d){
			delete self.loaded_value[value_id];
			$btn.html(html);
			self.selectValue(value_id);
			if(d.vars.flagged > 0) {
				self.toggleFlags(value_id);
			} else
				self.drawGraphs();
		});
	},
	
	manipulateFlags: function(method, flagType){
		var start = Math.floor(this.minZoom / 1000); 
		var end = Math.ceil(this.maxZoom / 1000);
		var value_id = this.get_selected_value_ids()[0];
		var self = this;
		$('#manipulate-btn').hide();
		$loader = $(app.loadIndicator2());
		$('#manipulate-btn').after($loader);
		app.get('values/manipulate_data/'+value_id+'/'+start+'/'+end+'/'+method+'/'+flagType).done(function(d){
			delete self.loaded_value[value_id];
			$loader.remove();
			$('#manipulate-btn').show();
			self.drawGraphs();
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
	
	/**
	*
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
	
	value_state_btns: function(value_id){
		//console.log(this.value_states[value_id]);
		$('.value-check-data-link[data-value_id='+value_id+']').toggle(this.value_states[value_id][0] != undefined);
		$('.value-toggle-flag-link[data-value_id='+value_id+']').toggle(this.value_states[value_id][-1] != undefined);
		$('.value-original-link[data-value_id='+value_id+']').toggle(this.value_states[value_id][2] != undefined || this.value_states[value_id][-2] != undefined);
	},
	
	/**
	* converts an array of values with measures from the database into the chart format
	* @param values: object from the server, with values and its measures
	*/
	data_to_chart_format: function(values){
		var data = {};
		var timestamps = {};
		var t, d;
		for(var i in values) { //convert data structure for diagram
			var v = values[i];
			data[i] = [];
			this.value_states[i] = {};
			if(v.Measure.length > 0) {
				for(var j in v.Measure) {
					var m = v.Measure[j];
					if($.isNumeric(m.d)) {
						this.value_states[i][m.s] = true;
						/*if(timestamps[m.t])
							m.t = parseInt(m.t) + timestamps[m.t];
						else
							timestamps[parseInt(m.t)] = 0;*/
						if(m.s < 0) {
							if(m.s == -1 && this.show_flagged_values[v.Value.id])
								data[i].push([m.t * 1000, parseFloat(m.d)]);
							if(m.s == -2 && this.show_original_values[v.Value.id])
								data[i].push([m.t * 1000, parseFloat(m.d)]);
						} else {
							//console.log(m);
							data[i].push([m.t * 1000, parseFloat(m.d)]);
						}
						//timestamps[m.t]++;
					}
				}
			}
		}
		return data;
	},
	
	/**
	* converts an array of values with measures from the database into the series of flags to be drawn on the data
	* @param values: object from the server, with values and its measures
	*/
	data_to_flags: function(values, original){
		var data = {};
		for(var i in values) { //convert data structure for diagram
			var v = values[i];
			data[i] = [];
			if(!this.show_flagged_values[v.Value.id] && !original)
				continue;
			if(v.Measure.length > 0) {
				for(var j in v.Measure) {
					var m = v.Measure[j];
					if($.isNumeric(m.d)) {
						m.d = parseFloat(m.d);
						//console.log(m.s);
						if(original && ( m.s == -2 || m.s == 2)) {
							data[i].push({
								title:m.s == 2 ? 'c' : 'd',
								x:m.t*1000,
								y:parseFloat(m.d)
							});
						} else
						if(m.s == -1 && !original) {
							var title = [];
							if(m.d < parseFloat(v.Value.minimum)) title.push('Min')
							else if(m.d > parseFloat(v.Value.maximum)) title.push('Max');
							else if(v.Value.error_codes.indexOf(m.d+':') > -1) {
								title.push('Error');
							}
							else title.push('!'); 
							data[i].push({
								title:title.join(', '),
								x:m.t*1000,
								y:parseFloat(m.d)
							});
						}
					} else {
						data[i].push({
							title:m.d,
							x:m.t*1000
						});
					}
				}
			}
		}
		return data;
	},
	
	get_selected_value_ids: function(){
		return $('.value-checkbox:checked').map(function(){ return $(this).data('value_id'); }).get();
	},
	
	/**
	* draws the data-charts for the selected values
	*/
	drawGraphs:function(){
		var p = this.measured_data;
		var diagram_type = $('#diagram_type').val();
		//console.log(diagram_type);
		this.charts = [];
		var start = false;
		var value_ids = this.get_selected_value_ids();
		var showMarker = false;
		var showOriginal = false;
		for(var i in value_ids) {
			//if measured data is already loaded for the value add it to the data array
			showMarker = this.show_flagged_values[value_ids[i]] || showMarker;
			showOriginal = this.show_original_values[value_ids[i]] || showOriginal;
		}
		
		var data = this.data_to_chart_format(p.values);
		if(showOriginal)
			var flags = this.data_to_flags(p.values, true); //Get data series to put flags on the graphs
		else if(showMarker) {
			var flags = this.data_to_flags(p.values);
		}
		var showFlags = showMarker || showOriginal;
		
		var the_data = {}, the_flags = {};
		var self = this;
		$('#charts').html(app.loadIndicator()); //clear the chart container
		$('#charts').html('<div id="chart" class="chart-container">'+app.loadIndicator()+'</div>');
		
		var count = 0;
		for(var i in value_ids) {
			//if measured data is already loaded for the value add it to the data array
			if(this.loaded_value[value_ids[i]]) {
				the_data[value_ids[i]] = data[value_ids[i]];
				if(showFlags)
					the_flags[value_ids[i]] = flags[value_ids[i]];
			} else { //otherwise load the data for the value from the server
				var params = utils.getUrlParameters(window.location.href);
				params.value_id = value_ids[i];
				app.setRefresh();
				app.get('projects/data/'+this.current.id, params).done(function(d){
					self.loaded_value[params.value_id] = true;
					self.measured_data.values[params.value_id] = d.vars.values[params.value_id];
					self.drawGraphs();
				});
				return;
			}
			this.value_state_btns(value_ids[i]);
			count++;
		}
		data = the_data;
		if(showFlags)
			flags = the_flags;
		
		$('#manipulate-points').toggle(showMarker && count == 1);
		$('#reset-points').toggle(showOriginal && count == 1);
		$('#flag-points').toggle(!showOriginal && !showMarker && count == 1);
		
		
		$charts = $('.chart-container');
		
		//console.log(data);
		app.require(['charts'], function(Chart) {
			Highcharts.setOptions({ //Global Highcharts options
				global: {
					//timezoneOffset: 0,
					useUTC: false //Show time in browser timezone settings
				}
			});
			var opts = {
				chart: {
					zoomType: 'x'
				},
				xAxis: { //
					type: 'datetime',
					events: {
						setExtremes: function (e) { //Store zoom level to reset it on redrawing
							self.maxZoom = e.max;
							self.minZoom = e.min;
						}
					}
					//minRange: 14 * 24 * 3600000 // fourteen days
				},
				legend: {
					enabled: false
				},
				rangeSelector : {
					selected:5,
					buttons: [{ //Zoom Buttons top left corner
						type: 'hour', count: 1, text: '1h'
					}, {type: 'day', count: 1, text: '1d'
					}, {type: 'week', count: 1, text: '1w'
					}, {type: 'month', count: 1, text: '1m'
					}, {type: 'year', count: 1, text: '1y'
					}, {type: 'all', text: 'All'
					}]
				},
				credits: {
					text: '',
				},
				plotOptions: {
					series: {
						animation:false, //no animation
						turboThreshold: showOriginal || value_ids.length > 1 ? 0 : undefined ,
						dataGrouping: {
							enabled: false
						}
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
			var tooltip_point_format = '<span style="color:{series.color}">\u25CF</span> {series.name}: <b>{point.y}</b><br/>'; //Tooltip format
			var aSeries = {
					type: diagram_type,
					pointInterval: 24 * 3600 * 1000, //One day
					marker: {
						enabled: false//showMarker
					}
					//pointStart: start,
			};
			var get_y_text = function(i){ return (p.values[i].Value.prefix ? p.unit_prefixes[p.values[i].Value.prefix] : '') + p.values[i].Unit.name + ' (' + p.values[i].Value.prefix + p.values[i].Unit.symbol + ')'; }; //Text for y Axis: unit name
			
			
			var $chart = $('#chart');
			var linkUnits = {};
			
			for(var i in value_ids) {
				i = parseInt(i);
				if(!p.values[value_ids[i]].Unit.id && p.values[value_ids[i + 1]]) {
					linkUnits[i + 1] = i;
				}
			}
			
			//Build the yAxis for all data graphs to be drawn
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
				return yAx;
			})
			
			//Build the series for all graphs
			var counter = -1;
			var series = $.map(data, function(d,i) { 
				counter++;
				var yAx = linkUnits[counter] != undefined ? linkUnits[counter] : counter;
				return $.extend(true, {},  $.extend(aSeries, {
					yAxis:yAx, 
					data:d, 
					id:'series'+counter,
					//linkedTo: yAx != counter ? 'series'+yAx : null,
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
			
			//Build the flags for all graphs if they should be shown
			if(showFlags) {
				var counter = -1;
				var flagseries = $.map(flags, function(f,i) { 
					counter++;
					return {
						type : 'flags',
						data : f,
						onSeries : 'series'+counter,  // Id of which series it should be placed on.
						shape : 'flag'  // Defines the shape of the flags.
					}
				});
				series = series.concat(flagseries);
			}
			
			//Assemble the chart options
			var chartOpts = $.extend(opts, {
				chart: $.extend(opts.chart, {
					renderTo: "charts" //Chart container id
				}),
				title: {
					text: $.map(data, function(d, i) { return $.trim(p.values[i].Value.name) }).join(', ') //All Value names as diagram title
				},
				subtitle: {
					text: ''//No subtitle
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
		 	//console.log(JSON.stringify(series));
			var chart = new Highcharts.StockChart(chartOpts);
			if(self.minZoom && self.maxZoom) {
				if(chart.xAxis[0].getExtremes().max < self.minZoom || chart.xAxis[0].getExtremes().min > self.maxZoom) {
					self.minZoom = chart.xAxis[0].getExtremes().min;
					self.maxZoom = chart.xAxis[0].getExtremes().max;
				}
				chart.xAxis[0].setExtremes(self.minZoom, self.maxZoom);
			} else {
				self.maxZoom = chart.xAxis[0].getExtremes().max
				self.minZoom = chart.xAxis[0].getExtremes().min;
			}
			
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
	
	invite:function(id){
		var self = this;
		var dialog = $('#user-view-modal');
		app.loadDialog(this.name, 'invite', app.url(), dialog).done(function(d){
			self.setCurrent(d.vars.project.Project);
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