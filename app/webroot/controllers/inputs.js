/**
 * This is the inputs controller
 *
 * @copyright     Martin Kapp 2014-15
 * @link          http://headkino.de
 * @package       app.Lib
 * @since         v 0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * 
 * @class app.controllers.inputs
 */
app.controllers.inputs = BackboneMVC.Controller.extend({
    name: 'inputs',
	views:[], //These are the views we will use with the controller
	
	/**
	 * Description
	 * @method add
	 * @param {} id
	 * @return 
	 */
	add:function(id){
		var self = this;
		var dialog = $('#input-view-modal');
		app.loadDialog(this.name, 'add', app.url(), dialog).done(function(d){
			
		});
    },
	
	/**
	 * Description
	 * @method edit
	 * @param {} id
	 * @return 
	 */
	edit:function(id){
		var self = this;
		app.setRefresh();
		app.loadPage(this.name, 'edit', app.url()).done(function(d){
			$('pre > select').tooltip({placement:'bottom'});
		});
    },
		
	/**
	 * Description
	 * @method delete
	 * @param {} id
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
     * Description
     * @method init
     * @return 
     */
    init:function(){
     	
    },
	/**
	 * Description
	 * @method default
	 * @return 
	 */
	default:function(){this.index()},
    /**
     * Description
     * @method index
     * @param {} project_id
     * @return 
     */
    index:function(project_id){
		var self = this;
		app.loadPage(this.name, 'index', app.url()).done(function(d){

		});
    }
});