/**
 * This is the users controller
 *
 * @copyright     Martin Kapp 2014-15
 * @link          http://headkino.de
 * @package       app.Lib
 * @since         v 0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * 
 * @class app.controllers.users
 */
app.controllers.users = BackboneMVC.Controller.extend({
    name: 'users',
	views:[], //These are the views we will use with the controller
	
	/**
	 * Login for the user
	 * @method login
	 * @return 
	 */
	login:function(){
		var self = this;
		app.loadPage(this.name, 'login', app.url()).done(function(d){
			
		});
    },
	
	/**
	 * Logout for the user
	 * @method logout
	 * @return 
	 */
	logout:function(){
		window.location.href = window.location.href;
	},
	
	/**
	 * Settings page
	 * @method settings
	 * @return 
	 */
	settings:function(){
		var self = this;
		app.loadPage(this.name, 'settings', app.url()).done(function(d){
			
		});
    },
	
	/**
	 * Show user information
	 * @method view
	 * @param {int} id User ID
	 * @return 
	 */
	view:function(id){
		var self = this;
		var dialog = $('#user-view-modal');
		app.loadDialog(this.name, 'profile', app.url(), dialog).done(function(d){
			
		});
    },

	/**
	 * The URL users/ maps onto the Users.index function
	 * @method default
	 * @return 
	 */
	default:function(){this.index()},
	
	/**
	 * Reset the password for a user
	 * @method resetPassword
	 * @return 
	 */
	resetPassword:function(){
		var self = this;
		app.loadPage(this.name, 'resetPassword', app.url()).done(function(d){

		});
    },
	
	/**
	 * Change the password for a user
	 * @method changePassword
	 * @return 
	 */
	changePassword:function(){
		var self = this;
		app.loadPage(this.name, 'resetPassword', app.url()).done(function(d){

		});
    },
	
    /**
     * Show the register form
     * @method register
     * @return 
     */
    register:function(){
		var self = this;
		app.loadPage(this.name, 'register', app.url()).done(function(d){

		});
    }
});