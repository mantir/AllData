/**
 * This is the exports controller
 *
 * @copyright     Martin Kapp 2014-15
 * @link          http://headkino.de
 * @package       app.Lib
 * @since         v 0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * 
 * @class app.controllers.exports
 */
app.controllers.exports = BackboneMVC.Controller.extend({
    name: 'exports',
	
	/**
	 * Description
	 * @method add
	 * @param {int} id
	 * @return 
	 */
	add:function(id){
		var self = this;
		var dialog = $('#export-view-modal');
		app.loadDialog(this.name, 'add', app.url(), dialog).done(function(d){
			
		});
    },
	
	/**
	 * Description
	 * @method view
	 * @param {int} id
	 * @return 
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
				
				/**
				 * Mapper for exports.setUrlDate function
				 * @method change_fun
				 * @param {Object} e Event
				 * @return 
				 */
				var change_fun = function(e) {
                    self.setUrlDate(id, $('#ExportStart').val(), $('#ExportEnd').val(), $('#ExportStartHour').val(), $('#ExportStartMinute').val(), $('#ExportEndHour').val(), $('#ExportEndMinute').val(), $('#ExportDeleted').prop('checked'), $('#ExportStates').prop('checked'));
                };
				
				$('input[type=checkbox],.calendar-input,select').change(change_fun);
				
			});
		});
    },
	
	/**
	 * Changes the links in the download and export buttons in the Export.view view, when the date or states to export are changed by the user.
	 * @method setUrlDate
	 * @param {int} id Export ID
	 * @param {string} startDate date string parsebale with PHP strtotime function 
	 * @param {string} endDate date string parsebale with PHP strtotime function 
	 * @param {} startHour
	 * @param {} startMinute
	 * @param {} endHour
	 * @param {} endMinute
	 * @param {bool} deleted If data with deleted states should be exported
	 * @param {bool} states If the states should be exported
	 * @return 
	 */
	setUrlDate: function(id, startDate, endDate, startHour, startMinute, endHour, endMinute, deleted, states){
		var start = startDate+','+startHour+':'+startMinute;
		var end = endDate+','+endHour+':'+endMinute;
		var url = $('#export-url').attr('href');
		url = url.replace(RegExp('start=([^\&])*&end=([^\&]*)'), 'start='+start+'&end='+end);
		url = url.replace(RegExp('states=([^\&])*&deleted=([^\&]*)'), 'states='+(states ? 1 : 0)+'&deleted='+(deleted ? 1 : 0));
		$('#export-url').attr('href', url);
		$('#download-url').attr('href', url+'&download=1');
	},
	
	
	/**
	 * Description
	 * @method edit
	 * @param {int} id
	 * @return 
	 */
	edit:function(id){
		var self = this;
		var dialog = $('#export-view-modal');
		app.setRefresh();
		app.loadDialog(this.name, 'edit', app.url(), dialog).done(function(d){

		});
    },
		
	/**
	 * Description
	 * @method delete
	 * @param {int} id
	 * @return 
	 */
	delete:function(id){
		var self = this;
		app.post(this.name+'/delete/' + app.url()).done(function(d){
			alert(d);
			alert(d.vars.message);
		});
    }
});