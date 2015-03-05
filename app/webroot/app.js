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
	home:function(){
		app.route('projects/index');
	},
	admin:function(){
		alert('THX');
	},
});
app = {
	c: {}, //Controller array
	controllers: {}, //Controllerklassen
	controllerList: ["c/projects", "c/pages", "c/documentations", "c/logs", 'c/settings', 'c/values', 'c/inputs', 'c/units', 'c/methods', 'c/exports', 'c/users', 'c/admin'],
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
	url:function(query){
		var s = query ? query : Backbone.history.location.search;
		if(Backbone.history.fragment.indexOf(s) > -1 || Backbone.history.fragment.indexOf(decodeURI(s)) > -1)
			s = '';
		return Backbone.history.fragment + s;
	},
	unbaseUrl :function(url) {
		if(url.indexOf(this.baseUrl) > -1)
			url = url.replace(new RegExp(this.baseUrl), '');
		return url;
	},
	route:function(url, notrigger, replace){
		url = app.unbaseUrl(url);
		/*var fr = url.indexOf('?'); 
		if(fr > -1)
			url = url.substr(0, fr) + '~'+url.substr(fr + 1);*/
		if(!notrigger)
			app.lastURL = Backbone.history.fragment;
		this.router.navigate(url, {trigger: !notrigger, replace: replace});
	},
	updateUrl: function(url){
		this.route(url, true, true);
	},
	call_url: function(url){
		var current = Backbone.history.fragment;
		this.route(url, false, true);
		this.route(current, true, true);
	},
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
	loadCss:function(url) {
		var link = document.createElement("link");
		link.type = "text/css";
		link.rel = "stylesheet";
		link.href = url;
		document.getElementsByTagName("head")[0].appendChild(link);
	},
	refresh:function(callback){
		var url = Backbone.history.fragment;
		this.refreshing = true;
		this.setCallback(callback);
		this.route(url);
	},
	setRefresh:function(url){
		if(url) {
			delete app.requests[url];
		} else
		this.refreshNext = true;
	},
	callback : function(){},
	setCallback:function(callback){
		var self = this;
		this.callback = function(){
			callback(arguments);
			self.callback = function(){};
		}
	},
	render: function(view, d, con){
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
	changePage:function(){
		
	},
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
	post: function(url){
		var q = Q.defer();
        $.post(this.baseUrl+this.jsonUrl + url).done(function(d){ app.requests[url] = d; q.resolve(d);}).fail(function(){ alert('Es ist ein Fehler aufgetreten. Bitte Seite neuladen.'); q.resolve({}); });
		return q.promise;
	},
	checkForMessageToShow: function(d){
		if(d.message) {
			$('#error-view-modal').find('.modal-body').html(d.message);
			$('#error-view-modal').modal();
		}
		d.message = false;
	},
	submit: function(e){
		var p = $(e.target).serializeArray();
		
		return false;
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
		return '<img class="loader" src="'+this.loader_gif+'" alt="Loading..." />';
	},
	loadIndicator2:function(){
		return '<img src="'+this.loader_gif_2+'" alt="Loading..." />';
	}, 
	clearAlerts: function(){
		if($('.alert').length)
			window.setTimeout(function(){
				$('.alert').fadeOut(200);
			}, 4000);
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

	