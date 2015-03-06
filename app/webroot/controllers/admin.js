/**
 * This is the admin controller. It provides methods for admin routing like in CakePHP. Urls containing /admin/ are directed here an then matched on their real target controllers.
 *
 * @example /admin/methods/add is mapped onto /methods/admin_add on the server. To enabled admin routing
 *
 * @copyright     Martin Kapp 2014-15
 * @link          http://headkino.de
 * @package       app.Lib
 * @since         v 0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * 
 * @class app.controllers.admin
 */
app.controllers.admin = BackboneMVC.Controller.extend({
    name: 'admin',
	/**
	 * Mapping method
	 * @method default
	 * @param {string} c Controller name to map action
	 * @param {string} a Action to execute in the controller
	 * @return 
	 */
	default:function(c, a){
		var url = Backbone.history.fragment;
		var newUrl = url.replace(RegExp('admin/'), '');
		//alert(newUrl);
		newUrl = newUrl.replace(RegExp(c+'/'+a), c+'/admin_'+a);
		//alert(newUrl);
		app.route(newUrl, false, true);
		app.updateUrl(url);
	},
	
	/**
	 * Maps onto the methods controller
	 * @method methods
	 * @param {string} action
	 * @return 
	 */
	methods:function(action){
		this.default('methods', action);
	},
	
	/**
	 * Maps onto the units controller
	 * @method units
	 * @param {string} action
	 * @return 
	 */
	units:function(action){
		this.default('units', action);
	}
});