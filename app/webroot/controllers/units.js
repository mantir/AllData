/**
 * This is the units controller
 *
 * @copyright     Martin Kapp 2014-15
 * @link          http://headkino.de
 * @package       app.Lib
 * @since         v 0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * 
 * @class app.controllers.units
 */
app.controllers.events = BackboneMVC.Controller.extend({
    name: 'units',
	views:[], //These are the views we will use with the controller
	
	/**
	 * Create a unit in a project
	 * @method add
	 * @param {int} id Project ID
	 * @return 
	 */
	add:function(id){
		var self = this;
		var dialog = $('#unit-view-modal');
		app.loadDialog(this.name, 'add', app.url(), dialog).done(function(d){
			
		});
    },
	
	/**
	 * Edit unit
	 * @method edit
	 * @param {int} id Unit ID
	 * @return 
	 */
	edit:function(id){
		var self = this;
		var dialog = $('#unit-view-modal');
		app.loadDialog(this.name, 'edit', app.url(), dialog).done(function(d){
			
		});
    },
	
	/**
	 * Delted a unit
	 * @method delete
	 * @param {int} id Unit ID
	 * @return 
	 */
	delete:function(id){
		var self = this;
		app.post(this.name+'/delete/' + app.url()).done(function(d){
			alert(d);
			alert(d.vars.message);
		});
    },
	
	/**
	 * The URL units/ is mapped onto the index function
	 * @method default
	 * @return 
	 */
	default:function(){this.index()},
	
    /**
     * Shows all created units for the admin
     * @method admin_index
     * @return 
     */
    admin_index:function(){
		var self = this;
		app.loadPage(this.name, 'index', app.url()).done(function(d){

		});
    }
});