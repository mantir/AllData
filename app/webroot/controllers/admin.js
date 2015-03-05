app.controllers.admin = BackboneMVC.Controller.extend({
    name: 'admin',
	default:function(c, a){
		var url = Backbone.history.fragment;
		var newUrl = url.replace(RegExp('admin/'), '');
		//alert(newUrl);
		newUrl = newUrl.replace(RegExp(c+'/'+a), c+'/admin_'+a);
		//alert(newUrl);
		app.route(newUrl, false, true);
		app.updateUrl(url);
	},
	methods:function(action){
		this.default('methods', action);
	},
	units:function(action){
		this.default('units', action);
	}
});