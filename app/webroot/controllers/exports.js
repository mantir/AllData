app.controllers.exports = BackboneMVC.Controller.extend({
    name: 'exports',
	
	/**
	*
	*/
	add:function(id){
		var self = this;
		var dialog = $('#export-view-modal');
		app.loadDialog(this.name, 'add', app.url(), dialog).done(function(d){
			
		});
    },
	
	/**
	*
	*/
	view:function(id){
		var self = this;
		var dialog = $('#export-view-modal');
		app.setRefresh();
		app.loadDialog(this.name, 'view', app.url(), dialog).done(function(d){
			app.require(['calendar'], function() {
				$('.calendar-input').datepicker($.extend(app.config['calendar'], {
					startDate: '1.1.2014',
					endDate: new Date()
				}));
				
				var change_fun = function(e) {
                    self.setUrlDate(id, $('#ExportStart').val(), $('#ExportEnd').val(), $('#ExportStartHour').val(), $('#ExportStartMinute').val(), $('#ExportEndHour').val(), $('#ExportEndMinute').val(), $('#ExportDeleted').prop('checked'), $('#ExportStates').prop('checked'));
                };
				
				$('input[type=checkbox],.calendar-input,select').change(change_fun);
				
			});
		});
    },
	
	/**
	*
	*/
	setUrlDate: function(id, startDate, endDate, startHour, startMinute, endHour, endMinute, deleted, states){
		var start = startDate+','+startHour+':'+startMinute;
		var end = endDate+','+endHour+':'+endMinute;
		$('.value-method-link[data-value_id='+id+']').html(app.loadIndicator2());
		var url = $('#export-url').attr('href');
		url = url.replace(RegExp('start=([^\&])*&end=([^\&]*)'), 'start='+start+'&end='+end);
		url = url.replace(RegExp('states=([^\&])*&deleted=([^\&]*)'), 'states='+(states ? 1 : 0)+'&deleted='+(deleted ? 1 : 0));
		$('#export-url').attr('href', url);
		$('#download-url').attr('href', url+'&download=1');
	},
	
	
	edit:function(id){
		var self = this;
		var dialog = $('#export-view-modal');
		app.setRefresh();
		app.loadDialog(this.name, 'edit', app.url(), dialog).done(function(d){

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

});