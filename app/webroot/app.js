/**
 * This is the app class. It manages the whole application.
 *
 * @copyright     Martin Kapp 2014-15
 * @link          http://headkino.de
 * @package       app.Lib
 * @since         v 0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * 
 * @class app
 */
requirejs.config({
	baseUrl : baseUrl+'/',
	paths: {
		c: 'controllers', //for shorter paths in app.controllerList

		calendar: 'js/bootstrap-datepicker',
		editor: 'js/ckeditor/ckeditor',
		codeeditor: 'js/codemirror-4.8/lib/codemirror',
		phpjs: 'js/phpjs',
		charts: 'js/highcharts/highstock',
		charts_exporting: 'js/highcharts/modules/exporting',
		charts_theme: 'js/highcharts/themes/dark-unica'
	},
	shim: {
		charts_exporting: ['charts'],
		charts_theme: ['charts']
	}
	
});
var routes = {
	'/':'home',
	'':'home',
};
//routes[baseUrl+'/*'] = 'relative';
var AppRouter = BackboneMVC.Router.extend({
	name: "appRouter",
	routes:routes,
	/**
	 * Route the home url to the Projects.index page
	 * @method home
	 * @return 
	 */
	home:function(){
		app.route('projects/index');
	},
});
app = {
	c: {}, //Controller array
	controllers: {}, //Controllerklassen
	controllerList: ["c/projects", "c/pages", "c/logs", 'c/values', 'c/inputs', 'c/units', 'c/methods', 'c/exports', 'c/users', 'c/admin'],
	viewType:"text/x-underscore-template",
	loader_gif:baseUrl+"/img/gloader.gif",
	loader_gif_2:baseUrl+"/img/fbloader.gif",
	requests : [],
	refreshNext: false, //Whether to not fetch data from cache instead of loading it from the server in the next request
	jsonUrl: 'json/',
	going : {},
	config : [],
	viewFileExt: 'js',
	router : new AppRouter(),
	/**
	 * Initializes the app on page loading
	 * @method init
	 * @param {string} baseUrl The base url of alldata, it can be passed or is calculated from the current URL: e.g. http://headkino.de/alldata/
	 * @param {Object} d Data array from the server. The same object would have been returned from an AJAX request to the page. The data is cached in the app.requests Object
	 * @param {string} content Rendered view HTML
	 * @return 
	 */
	init: function(baseUrl, d, content){ //Base Url and Data
		if(baseUrl.substr(-1, 1) != '/') baseUrl += '/';
		if(baseUrl) {
			this.baseUrl = baseUrl;
		} else {
			this.baseUrl = document.location.pathname.replace(/\/index\.html/, '');
		}
		var that = this;
		//app.setUpRequire();
		app.require(['phpjs'], function() {
			d.content = content;
			$(document).one('app:controllersLoaded', function(){
				that.updateLayout();
				that.bindEvents();
				Backbone.history.start({pushState: true, root: that.baseUrl, silent: true});
				var curUrl = that.url();
				that.requests[curUrl] = d; //Start Url schon geladen
				that.route(curUrl);
			});
			that.loadControllers(app.controllerList);
		});
	},
	/**
	 * Returns the current relative url. Relative to the app.baseUrl
	 * @method url
	 * @param {string} query string. If the string isn't already at the end of the current URL, it is added and returned
	 * @return BinaryExpression
	 */
	url:function(query){
		var s = query ? query : Backbone.history.location.search;
		if(Backbone.history.fragment.indexOf(s) > -1 || Backbone.history.fragment.indexOf(decodeURI(s)) > -1)
			s = '';
		return Backbone.history.fragment + s;
	},
	/**
	 * Removes the app.baseUrl from an url
	 * @method unbaseUrl
	 * @param {string} url
	 * @return url
	 */
	unbaseUrl :function(url) {
		if(url.indexOf(this.baseUrl) > -1)
			url = url.replace(new RegExp(this.baseUrl), '');
		return url;
	},
	/**
	 * Routes a given URL to the corresponding controller and method and the URL in the browser can be changed. e.g. /projects/view/2 => Projects-controller executing view-function passing parameter 2.
	 * 
	 * @method route
	 * @param {string} url The route url
	 * @param {bool|undefined} notrigger Just change the Browser URL and don't trigger the action in the controller
	 * @param {bool|undefined} replace Replaces the current URL in the browser with with the url parameter and doesn't add it to the Backbone history. (like nothing happened) 
	 * @return 
	 */
	route:function(url, notrigger, replace){
		url = app.unbaseUrl(url);
		/*var fr = url.indexOf('?'); 
		if(fr > -1)
			url = url.substr(0, fr) + '~'+url.substr(fr + 1);*/
		if(!notrigger)
			app.lastURL = Backbone.history.fragment;
		this.router.navigate(url, {trigger: !notrigger, replace: replace});
	},
	/**
	 * Just change the browser URL without triggering the corresponding controller action.
	 * @method updateUrl
	 * @param {string} url
	 * @return 
	 */
	updateUrl: function(url){
		this.route(url, true, true);
	},
	/**
	 * Executes a controller action and changes the URL, then it replaces the changed URL with the previous URL again (like nothing happened)
	 * @method call_url
	 * @param {} url
	 * @return 
	 */
	call_url: function(url){
		var current = Backbone.history.fragment;
		this.route(url, false, true);
		this.route(current, true, true);
	},
	/**
	 * Wrapper for require.js
	 * @method require
	 * @param {string|array} what What should be required
	 * @param {function} callback Executed after required files are loaded
	 * @return 
	 */
	require:function(what, callback){
		if(typeof(what) == 'string')
			what = [what];
		var which = what.slice(0); //clone array, must be array
		for(var i in what) {
			if(what[i] == 'calendar'){ this.config['calendar'] = {language: "de", autoclose: true, todayHighlight: true}; app.loadCss(this.baseUrl+'css/datepicker3.css'); } else
			if(what[i] == 'phpjs') { which[i] = 'phpjs/date'; which.push('phpjs/strtotime'); } else
			if(what[i] == 'charts') {  which.push('charts_exporting'); /*which.push('charts_theme');*/ } else
			if(what[i] == 'codeeditor') { app.loadCss(this.baseUrl+'js/codemirror-4.8/lib/codemirror.css'); } 
		}
		//console.log(which);
		require(which, callback);
	},
	/**
	 * Loads a stylesheet dynamically and adds it to the header
	 * @method loadCss
	 * @param {string} url
	 * @return 
	 */
	loadCss:function(url) {
		var link = document.createElement("link");
		link.type = "text/css";
		link.rel = "stylesheet";
		link.href = url;
		document.getElementsByTagName("head")[0].appendChild(link);
	},
	/**
	 * Refresh the current view
	 * @method refresh
	 * @param {function} callback
	 * @return 
	 */
	refresh:function(callback){
		var url = Backbone.history.fragment;
		this.refreshing = true;
		this.setCallback(callback);
		this.route(url);
	},
	/**
	 * Uncache the data for an already loaded URL so it is loaded again.
	 * @method setRefresh
	 * @param {string} url URL to be refreshed. If empty the next request is refreshed.
	 * @return 
	 */
	setRefresh:function(url){
		if(url) {
			delete app.requests[url];
		} else
		this.refreshNext = true;
	},
	/**
	 * app.callback a global callback function which can be set with setCallback. Used for example in the app.loadPage function
	 * @method callback
	 * @return 
	 */
	callback : function(){},
	/**
	 * Set the app.callback function.
	 * @method setCallback
	 * @param {} callback
	 * @return 
	 */
	setCallback:function(callback){
		var self = this;
		this.callback = function(){
			callback(arguments);
			self.callback = function(){};
		}
	},

	/**
	 * Like loadPage only laoding the page contents into a given dialog
	 * @method loadDialog
	 * @param {string} c Controller name
	 * @param {string} a Action name
	 * @param {string} url URL to fetch data from the server
	 * @param {jQuery Object} target Backbone modal Dialog container
	 * @return Q.promise from Q.js for callbacks
	 */
	loadDialog: function(c, a, url, target) {
		var q = Q.defer();
		if(this.locked) {
			q.resolve();
			return q.promise;
		}
		if($('.modal-backdrop:visible').length)
			$('.modal-backdrop:visible').remove();
		target.modal();
		target.on('hidden.bs.modal', function() {
			app.route(app.lastURL, true);
		})
		this.loadPage(c, a, url, target.find('.modal-body')).done(function(d){
			q.resolve(d);
		});
		return q.promise;
	},
	/**
	 * Load the page contents rom the server
	 * @method loadPage
	 * @param {} c Controller name
	 * @param {} a Action name
	 * @param {} url URL to fetch data from the server
	 * @param {} target The target container for the loaded page content (Default this.container.find('.page'))
	 * @return Q.promise from Q.js for callbacks
	 */
	loadPage: function(c, a, url, target){
		var q = Q.defer();
		if(this.locked) {
			q.resolve();
			return q.promise;
		}
		this.locked = true;
		if(url.indexOf('/admin_') > -1) //Admin routing
			url = 'admin/'+url.replace(RegExp('/admin_'), '/');
		if(window.backDetected) {
			back = true;
			window.backDetected = false;
		}
		var self = this; var samePage = false;
		var $con = this.container.find('.page');
		if(target && target.length > 0)
			$con = target;
		$con.html(this.loadIndicator());
		/**
		 * Executed after new page was loaded
		 * @method callback
		 * @param {} d
		 * @return 
		 */
		var callback = function(d){
			if(self.activePage().data('appurl') == url) {
				app.locked = false;
				q.resolve(d);
				samePage = true;
			}
			var html = d.content;

			if(self.refreshing) {
				$con.append(html);
				self.callback();
			} else {
				$con[0].innerHTML = html;
			}
			self.activePage().data('appurl', url);
			self.refreshing = false;
			app.locked = false;
			app.clearAlerts();
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
	/**
	 * Implement if page transitions a wanted
	 * @todo
	 * @method changePage
	 * @return 
	 */
	changePage:function(){
		
	},
	/**
	 * Fetches json data from the server
	 * @method get
	 * @param {string} url
	 * @param {object} params URL parameters
	 * @return a Q.promise from Q.js for callback
	 */
	get: function(url, params){
		var q = Q.defer();
		if(!params)
			var queryString = ''; 
		else
			var queryString = $.param(params);
		if(app.requests[url+queryString] && !this.refreshing && !this.refreshNext) {
			q.resolve(app.requests[url]);
		} else {
			this.refreshNext = false;
        	$.getJSON(this.baseUrl+this.jsonUrl + url, params)
				.done(function(d){ 
					app.requests[url+queryString] = d; 
					app.checkForMessageToShow(d);
					q.resolve(d);
				})
				.fail(function(d){
					var text = d.responseText;
					console.log(text);
					var m = text.match(/{.*}/mg);
					console.log(m);
					if(m[0]) {
						for(var i in m) {
							if($.isNumeric(i)) {
								try {
									d = eval('('+m[i]+')');
								} catch(e) {
									d = {};
								}
								if(d.vars) {
									var index = text.search(RegExp(m[i].replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&'), 'mg'));
									text = text.substr(0, index) + text.substring(index + m[i].length);
									break;
								}
							} 
						}
					} else
						d = {};
					if(d.message)
						text += d.message+' ';
					text += 'An error occured, please reaload the page.';
					$('#error-view-modal').find('.modal-body').html(text);
					$('#error-view-modal').modal();
					//app.checkForMessageToShow(d);
					q.resolve(d); 
				});
		}
		return q.promise;
	},
	/**
	 * post data to a server url
	 * @method post
	 * @param {string} url
	 * @return MemberExpression
	 */
	post: function(url){
		var q = Q.defer();
        $.post(this.baseUrl+this.jsonUrl + url).done(function(d){ app.requests[url] = d; q.resolve(d);}).fail(function(){ alert('Es ist ein Fehler aufgetreten. Bitte Seite neuladen.'); q.resolve({}); });
		return q.promise;
	},
	/**
	 * Checks if the returned data object contains a message which should be shown in a dialog
	 * @method checkForMessageToShow
	 * @param {} d
	 * @return 
	 */
	checkForMessageToShow: function(d){
		if(d.message) {
			$('#error-view-modal').find('.modal-body').html(d.message);
			$('#error-view-modal').modal();
		}
		d.message = false;
	},


	/**
	 * Updates the layout on page load or on windows resize
	 * @method updateLayout
	 * @return 
	 */
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
	/**
	 * Binds all global events to their jQuery DOM objects
	 * @method bindEvents
	 * @return 
	 */
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
		
		$('.modal').on('hidden.bs.modal', function( event ) {
        	$(this).removeClass( 'fv-modal-stack' );
        	$('body').data( 'fv_open_modals', $('body').data( 'fv_open_modals' ) - 1 );
        });
		
		$( '.modal' ).on( 'shown.bs.modal', function ( event ) {
		   // keep track of the number of open modals
		   if ( typeof( $('body').data( 'fv_open_modals' ) ) == 'undefined' ) {
			 $('body').data( 'fv_open_modals', 0 );
		   }
			// if the z-index of this modal has been set, ignore.
			if ( $(this).hasClass( 'fv-modal-stack' ) ) {
				return;
			}
			$(this).addClass( 'fv-modal-stack' );
			$('body').data( 'fv_open_modals', $('body').data( 'fv_open_modals' ) + 1 );
			$(this).css('z-index', 1040 + (10 * $('body').data( 'fv_open_modals' )));
			$( '.modal-backdrop' ).not( '.fv-modal-stack' ).css( 'z-index', 1039 + (10 * $('body').data( 'fv_open_modals' )));
			$( '.modal-backdrop' ).not( 'fv-modal-stack' ).addClass( 'fv-modal-stack' ); 
		});

		$(document).on('click', 'a[data-rel="back"]', function(){
			window.history.back();
		});
		//Klick Handler
		$(document).on('click', 'a', function(e){
			var href = $(this).attr('href');
			var target = $(this).attr('target');
			var rel = $(this).attr('rel');
			var role = $(this).attr('role');
			if(href && href.indexOf('javascript:') == -1 && href.indexOf('https://') == -1 && href.indexOf('http://') == -1 && !target && role != 'tab') {
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
	/**
	 * Returns the current page Container as jQuery Object
	 * @method activePage
	 * @return MemberExpression
	 */
	activePage: function(){
		return this.container;
	},
	/**
	 * Loads all controller files
	 * @method loadControllers
	 * @param {array} urls URLs to the controller files. Defined in app.controllerList
	 * @trigger app:controllersLoaded
	 * @return 
	 */
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
			$(document).trigger('app:controllersLoaded');
		});
	},

	/**
	 * Returns HTML for a global loading animation
	 * @method loadIndicator
	 * @return string HTML
	 */
	loadIndicator:function(){
		return '<img class="loader" src="'+this.loader_gif+'" alt="Loading..." />';
	},
	/**
	 * Returns HTML for a non global loading animation
	 * @method loadIndicator2
	 * @return string HTML
	 */
	loadIndicator2:function(){
		return '<img src="'+this.loader_gif_2+'" alt="Loading..." />';
	}, 
	/**
	 * Fades out any alert messages which have been shown to the user after 4 seconds.
	 * @method clearAlerts
	 * @return 
	 */
	clearAlerts: function(){
		if($('.alert').length)
			window.setTimeout(function(){
				$('.alert').fadeOut(200);
			}, 4000);
	}
}

/**
 * Detects the browser the user is using
 * @method detectUA
 * @param {jQuery} $ jQuery
 * @param {string} userAgent UserAgent string
 * @return 
 */
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

	