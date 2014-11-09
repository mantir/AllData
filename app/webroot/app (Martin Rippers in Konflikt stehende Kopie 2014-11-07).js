requirejs.config({
	baseUrl : baseUrl+'/',
	paths: {
		js:'js',
		views: 'views',
		controllers: 'controllers'
	}
});
var routes = {
	'/':'home',
	'':'home'
};
//routes[baseUrl+'/*'] = 'relative';
var AppRouter = BackboneMVC.Router.extend({
	name: "appRouter",
	routes:routes,
	defaultRoute: function(path) {
		alert(Backbone.history.fragment);
    },
	home:function(){
		app.route('projects/index');
	}
});
app = {
	c: {}, //Controller array
	controllers: {}, //Controllerklassen
	controllerList: ["controllers/projects", "controllers/pages", "controllers/documentations", "controllers/logs", 'controllers/settings', 'controllers/values', 'controllers/inputs', 'controllers/units'],
	viewType:"text/x-underscore-template",
	loader_gif:baseUrl+"/img/gloader.gif",
	requests : [],
	jsonUrl: 'json/',
	going : {},
	config : [],
	viewFileExt: 'js',
	router : new AppRouter(),
	init: function(baseUrl, d, content){ //Base Url and Data
		if(baseUrl.substr(-1, 1) != '/') baseUrl += '/';
		d.content = content;
		if(baseUrl) {
			this.baseUrl = baseUrl;
		} else {
			this.baseUrl = document.location.pathname.replace(/\/index\.html/, '');
		}
		var that = this;
		$(document).one('app:controllersLoaded', function(){
			that.updateLayout();
			that.bindEvents();
			Backbone.history.start({pushState: true, root: that.baseUrl, silent: true});
			var curUrl = that.url();
			that.requests[curUrl] = d; //Start Url schon geladen
			that.route(curUrl);
		});
		this.loadControllers(app.controllerList);
	},
	url:function(query){
		var s = query ? query : Backbone.history.location.search;
		if(Backbone.history.fragment.indexOf(s) > -1)
			s = '';
		return Backbone.history.fragment + s;
	},
	unbaseUrl :function(url) {
		if(url.indexOf(this.baseUrl) > -1)
			url = url.replace(new RegExp(this.baseUrl), '');
		return url;
	},
	route:function(url, notrigger){
		url = app.unbaseUrl(url);
		/*var fr = url.indexOf('?'); 
		if(fr > -1)
			url = url.substr(0, fr) + '~'+url.substr(fr + 1);*/
		if(!notrigger)
			app.lastURL = Backbone.history.fragment;
		this.router.navigate(url, {trigger: !notrigger, replace: false});
	},
	require:function(what, callback){
		for(var i in what) {
			if(what[i] == 'calendar'){ what[i] = this.baseUrl+'js/bootstrap-datepicker.js'; this.config['calendar'] = {language: "de", autoclose: true, todayHighlight: true}; } else
			if(what[i] == 'editor') { what[i] = this.baseUrl+'js/ckeditor/ckeditor.js'; }
			if(what[i] == 'charts') { what[i] = this.baseUrl+'js/morris.min.js'; what.push(this.baseUrl+'js/raphael-min.js'); }
			//if(what[i] == 'tooltip') { what[i] = this.baseUrl+'js/morris.min.js'; what.push(this.baseUrl+'js/raphael-min.js'); }
		}
		require(what, callback);
	},
	refresh:function(callback){
		var url = Backbone.history.fragment;
		this.refreshing = true;
		this.setCallback(callback);
		this.route(url);
	},
	callback : function(){},
	setCallback:function(callback){
		var self = this;
		this.callback = function(){
			callback(arguments);
			self.callback = function(){};
		}
	},
	render : function(view, d, con){
		var t = this.template('views/'+view);
		if(!t) return d.content;
		if(d && d.vars)
			d = d.vars;
		var temp = _.template(t);
		return temp(d);
	},
	loadDialog: function(c, a, url, target) {
		var q = Q.defer();
		if(this.locked) {
			q.resolve();
			return q.promise;
		}
		target.modal();
		target.on('hidden.bs.modal', function() {
			app.route(app.lastURL, true);
		})
		this.loadPage(c, a, url, target.find('.modal-body')).done(function(d){
			q.resolve(d);
		});
		return q.promise;
	},
	loadPage: function(c, a, url, target){
		var q = Q.defer();
		if(this.locked) {
			q.resolve();
			return q.promise;
		}
		this.locked = true;

		if(window.backDetected) {
			back = true;
			window.backDetected = false;
		}
		//window.reverseTransition = back;
		var self = this; var samePage = false;
		var $con = this.container.find('.page');
		if(target && target.length > 0)
			$con = target;
		$con.html(this.loadIndicator());
		var callback = function(d){
			//console.log($.mobile.activePage.data('appfunction')+' '+c+'.'+a);
			if(self.activePage().data('appurl') == url) {
				app.locked = false;
				q.resolve(d);
				samePage = true;
				//if(!self.refreshing) return;
			}
			var html = app.render(c+'.'+a, d); 
			//var footer = $('.footer', '#'+c+'-'+a);
			if(self.refreshing) {
				$con.append(html);
				self.callback();
			} else {
				$con[0].innerHTML = html;
			}
			self.activePage().data('appurl', url);
			self.refreshing = false;
			app.locked = false;
			q.resolve(d);
			if(samePage || true) return;
			//$.mobile.pageContainer.change will be needed in future versions
			Q(app.changePage('#'+c+'-'+a,{allowSamePageTransition:true,reloadPage:false,changeHash:true,transition:transition, reverse:back}))
			.then(function(){
				$.mobile.activePage.data('appurl', url);
				$.mobile.activePage.data('appfunction', c+'.'+a);
				app.locked = false;
				q.resolve(d);
			});
		}
	

		if(url && url != null && typeof(url) == 'string')
			app.get(url).done(callback);
		else
			callback(url);
		return q.promise;
	},
	changePage:function(){
		
	},
	get: function(url){
		var q = Q.defer();
		if(app.requests[url] && !this.refreshing) {
			q.resolve(app.requests[url]);
		} else {
        	$.getJSON(this.baseUrl+this.jsonUrl + url).done(function(d){ app.requests[url] = d; q.resolve(d);}).fail(function(){ alert('Es ist ein Fehler aufgetreten. Bitte Seite neuladen.'); q.resolve({}); });
		}
		return q.promise;
	},
	post: function(url){
		var q = Q.defer();
        $.post(this.baseUrl+this.jsonUrl + url).done(function(d){ app.requests[url] = d; q.resolve(d);}).fail(function(){ alert('Es ist ein Fehler aufgetreten. Bitte Seite neuladen.'); q.resolve({}); });
		return q.promise;
	},
	uncache: function(url){
		url = app.unbaseUrl(url);
		delete app.requests[url];
	},
	updateLayout:function(){
		if(!this.container) {
			this.container = $('#page-wrapper');
			console.log(this.container);
			this.header = $('#header');
			var self = this;
			$(window).resize(function(){
				self.updateLayout();
			});
			$(window).resize();
			return;
		}
		var hheight = this.container.offset().top;
		var fheight = 0//(this.footer.is(':visible')) * this.footer.outerHeight(true);
		var height = $(window).height() - hheight - fheight;
		this.container.css('min-height', height+'px');
	},
	bindEvents:function(){
		var self = this;
		$(document).on('pagebeforechange', function(e, a){
			var toPage = a.toPage;
			if(typeof(a.toPage) == 'string') return;
			var header = $('.header', toPage);
			var footer = $('.footer', toPage);
			self.headerContent[0].innerHTML = header[0].innerHTML;
			var duration = 350, animating = 'footer';
			window.footerAnimating = true;
			var dir = window.reverseTransition ? 1 : -1;
			if(footer.length > 0) {
				self.footerContent[0].innerHTML = footer[0].innerHTML;
				self.footer[0].style.display = 'block';
				self.footer.animate({'left':0}, duration, function(){
					window.footerAnimating = false;
					self.updateLayout();
				});
			} else { 
				self.footer.animate({'left':(self.footer.width() * dir)+'px'}, duration, function(){ 
					self.footer[0].style.display = 'none';
					self.updateLayout();
					window.footerAnimating = false;
				});
			}
			self.updateLayout(animating);
		});
		$(document).on('click', 'a[data-rel="back"]', function(){
			window.history.back();
		});
		//Klick Handler
		$(document).on('click', 'a', function(e){
			var href = $(this).attr('href');
			if(href && href.indexOf('javascript:') == -1 && href.indexOf('http://') == -1) {
				$(this).addClass('ui-btn-active');
				self.route(href);
				e.preventDefault();
			}
		})
		
		$(document).on('submit', 'form', function(e){
			var m = $(this).attr('method');
			var href = $(this).attr('action');
			if(m == 'post'){
				//app.post();
				return;
			} else {
				var query = $(this).serialize();
				app.route(href+'/?'+query);
			}
			e.preventDefault();
		});
	},
	activePage: function(){
		return this.container;
	},
	loadControllers: function(urls) {
		var that = this;
		require(urls, function(){
			var views = [], viewNames = [], appc = [];
			var c = 0;
			for(var i in app.controllers) {
				that.controllersLoaded = true;
				app.c[i] = appc[i] = (new app.controllers[i]);
				if(app.c[i].init)
					app.c[i].init();
				if(appc[i].views)
				for(var j in appc[i].views) {
					views[c] = 'text!'+appc[i].views[j]+'.' + that.viewFileExt;
					viewNames[c] = appc[i].views[j];
					c++;
				}
			}
			require(views, function(data){
				for(var c in views) {
					var id = that.getTemplateID(viewNames[c]);
					$('body').append($("<script type='" + that.viewType + "' id='" + id + "'>" + arguments[c] + "</script>"));
				}
				$(document).trigger('app:controllersLoaded');
			});
		});
	},
	getTemplateID: function(url){
		return url.replace(/\//g, '-').replace(/\./g, '__');
	},
	template: function(url){
		var id = this.getTemplateID(url);
		var t = document.getElementById(id);
		if(t)
			return t.innerHTML;
		else
			return false;
	},
	loadIndicator:function(){
		return '<img class="loader" src="'+this.loader_gif+'" alt="Laden..." />';
	}
}

var detectUA = function($, userAgent) {
	$.os = {};
	$.os.webkit = userAgent.match(/WebKit\/([\d.]+)/) ? true : false;
	$.os.android = userAgent.match(/(Android)\s+([\d.]+)/) || userAgent.match(/Silk-Accelerated/) ? true : false;
	$.os.androidICS = $.os.android && userAgent.match(/(Android)\s4/) ? true : false;
	$.os.ipad = userAgent.match(/(iPad).*OS\s([\d_]+)/) ? true : false;
	$.os.iphone = !$.os.ipad && userAgent.match(/(iPhone\sOS)\s([\d_]+)/) ? true : false;
	$.os.ios7 = userAgent.match(/(iPhone\sOS)\s([7_]+)/) ? true : false;
	$.os.webos = userAgent.match(/(webOS|hpwOS)[\s\/]([\d.]+)/) ? true : false;
	$.os.touchpad = $.os.webos && userAgent.match(/TouchPad/) ? true : false;
	$.os.ios = $.os.ipad || $.os.iphone;
	$.os.playbook = userAgent.match(/PlayBook/) ? true : false;            
	$.os.blackberry10 = userAgent.match(/BB10/) ? true : false;
	$.os.blackberry = $.os.playbook || $.os.blackberry10|| userAgent.match(/BlackBerry/) ? true : false;
	$.os.chrome = userAgent.match(/Chrome/) ? true : false;
	$.os.opera = userAgent.match(/Opera/) ? true : false;
	$.os.fennec = userAgent.match(/fennec/i) ? true : userAgent.match(/Firefox/) ? true : false;
	$.os.ie = userAgent.match(/MSIE 10.0/i) ? true : false;
	$.os.ieTouch = $.os.ie && userAgent.toLowerCase().match(/touch/i) ? true : false;
	$.os.supportsTouch = ((window.DocumentTouch && document instanceof window.DocumentTouch) || 'ontouchstart' in window);
	//features
	$.feat = {};
	var head = document.documentElement.getElementsByTagName("head")[0];
	$.feat.nativeTouchScroll = typeof(head.style["-webkit-overflow-scrolling"]) !== "undefined" && ($.os.ios||$.os.blackberry10);
	$.feat.cssPrefix = $.os.webkit ? "Webkit" : $.os.fennec ? "Moz" : $.os.ie ? "ms" : $.os.opera ? "O" : "";
	$.feat.cssTransformStart = !$.os.opera ? "3d(" : "(";
	$.feat.cssTransformEnd = !$.os.opera ? ",0)" : ")";
	if ($.os.android && !$.os.webkit)
		$.os.android = false;
}

	