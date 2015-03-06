/**
 * This is the pages controller
 *
 * @copyright     Martin Kapp 2014-15
 * @link          http://headkino.de
 * @package       app.Lib
 * @since         v 0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * 
 * @class app.controllers.pages
 */
app.controllers.events = BackboneMVC.Controller.extend({
    name: 'pages',
	views:[], //These are the views we will use with the controller

	
	/**
	 * Description
	 * @method documentation
	 * @return 
	 */
	documentation:function(){
		var self = this;
		var dialog = $('#help-modal');
		app.loadDialog(this.name, 'documentation', app.url(),dialog).done(function(d){
			if(!d) return;
		});
    }
});