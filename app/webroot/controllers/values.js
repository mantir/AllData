/**
 * This is the values controller
 *
 * @copyright     Martin Kapp 2014-15
 * @link          http://headkino.de
 * @package       app.Lib
 * @since         v 0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * 
 * @class app.controllers.values
 */
app.controllers.events = BackboneMVC.Controller.extend({
    name: 'values',
	views:[], //These are the views we will use with the controller
	
	/**
	 * Executed in the Values.add or Values.edit action if the user changes the method for the value
	 * @method method_change
	 * @return 
	 */
	method_change: function(){
		var method_id = $('#ValueMethodId').val();
		if(!method_id) {
			$('#value-method').hide();
			$('#method-params').html('');
		} else {
			$('#value-method').hide();
			$('#value-method').show();
			$('#method-params').html(app.loadIndicator());
			app.get('methods/get_params/'+method_id).done(function(d){
				$('#method-params').html(d.content);
			});
		}
		
	},
	
	/**
	 * Create a value in a project
	 * @method add
	 * @param {int} id Project ID
	 * @return 
	 */
	add:function(id){
		var self = this;
		var dialog = $('#value-view-modal');
		app.loadDialog(this.name, 'add', app.url(), dialog).done(function(d){
			$('#ValueMethodId').change(self.method_change);
		});
    },
	
	/**
	 * Edit a value
	 * @method edit
	 * @param {int} id Value ID
	 * @return 
	 */
	edit:function(id){
		var self = this;
		app.setRefresh();
		var dialog = $('#value-view-modal');
		app.loadDialog(this.name, 'edit', app.url(), dialog).done(function(d){
			$('#ValueMethodId').change(self.method_change);
		});
    },
	
	/**
	 * Delete a Value
	 * @method delete
	 * @param {int} id Value ID
	 * @return 
	 */
	delete:function(id){
		var self = this;
		app.post(this.name+'/delete/' + app.url()).done(function(d){
			alert(d);
			alert(d.vars.message);
		});
    },
	
});