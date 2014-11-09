// serializeJSON //
(function ($) {
	/*for serializing a form*/
	$.fn.serializeJSON = function () {
		var json = {};
		//send also the not checked checkboxes with value 0
		$(':checkbox:not(:checked):not(.noCheckNoSend)', this).each(function(){
			json[$(this).attr('name')] = 0;
		});
		jQuery.map($(this).serializeArray(), function (n, i) {
			if(n['value'] == 'on') n['value'] = 1; //checkbox value transformation to 1 if checked
			if(n['name'].match(/\[\]$/)) { //for names like name[], creates an array name and appends new elements to it
				var na = n['name'].replace(/\[\]$/, '');
				if(json[na])
					json[na].push(n['value']);
				else
					json[na] = [n['value']];
			} else {
				json[n['name']] = n['value'];
			}
		});
		return json;
	};
})(jQuery);

loge = function(d){
	//return;
	if(console) console.log(d);
};

/*return a word from a word list (can't be used in smarty template)*/
function _(w, vars){
	if($.isPlainObject(vars))
		arguments = vars;
	if(arguments.length > 1)
		for(i = 1; i < arguments.length; i++){
			loge(arguments[i]);
			w = w.replace(/\%s/, arguments[i]);
		}
	return w;
}
/*wrapper for "_()" can also be used in a smarty template*/
function __(w){
	return _(w, arguments);
}

/*a smarty template string, the data, the name of the function to be executed, the params for the function*/
function _parse(template, data, func, params){
	if(isset(func)) {
		var funcs = getFunctions(template);
		template = funcs;
		template += '{'+func;
		//set global variables like langExt
		if(!$.isArray(params) && !$.isPlainObject(params)){
			params = {langExt:Page.current.data.vars.langExt}
		} else {
			params['langExt'] = Page.current.data.vars.langExt;
		}
		var quote;
		for(var i in params) {
			quote = $.inArray(params[i][0], ['[','$']) ? '' : '"';
			template += ' '+i+'='+quote+params[i]+quote;
		}
		template += '}';
	}
	var t = new jSmart(document.getElementById('globalFunctions').innerHTML + template);
	return t.fetch(data);
}

function isset (){
	// http://kevin.vanzonneveld.net
	// +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	// +   improved by: FremyCompany
	// +   improved by: Onno Marsman
	// +   improved by: Rafał Kukawski
	// *     example 1: isset( undefined, true);
	// *     returns 1: false
	// *     example 2: isset( 'Kevin van Zonneveld' );
	// *     returns 2: true
	var a = arguments,
	l = a.length,
	i = 0,
	undef;
	
	if (l === 0){
		throw new Error('Empty isset');
	}
	while (i !== l){
		if (a[i] === undef || a[i] === null){
			return false;
		}
	i++;
	}
	return true;
}


var Inflector = new function() { 
	this.pluralRules = {
		'(s)tatus$' : '$1$2tatuses',
		'(quiz)$' : '$1zes',
		'^(ox)$' : '$1$2en',
		'([m|l])ouse$' : '$1ice',
		'(matr|vert|ind)(ix|ex)$' : '$1ices',
		'(x|ch|ss|sh)$' : '$1es',
		'([^aeiouy]|qu)y$' : '$1ies',
		'(hive)$' : '$1s',
		'(?:([^f])fe|([lr])f)$' : '$1$2ves',
		'sis$i' : 'ses',
		'([ti])um$' : '$1a',
		'(p)erson$' : '$1eople',
		'(m)an$' : '$1en',
		'(c)hild$' : '$1hildren',
		'(buffal|tomat)o$' : '$1$2oes',
		'(alumn|bacill|cact|foc|fung|nucle|radi|stimul|syllab|termin|vir)us$' : '$1i',
		'us$' : 'uses',
		'(alias)$' : '$1es',
		'(ax|cris|test)is$' : '$1es',
		's$' : 's',
		'^$' : '',
		'$' : 's'
	};
	this.singularRules = {
		'(s)tatuses$' : '$1$2tatus',
		'^(.*)(menu)s$' : '$1$2',
		'(quiz)zes$' : '$$1',
		'(matr)ices$' : '$1ix',
		'(vert|ind)ices$' : '$1ex',
		'^(ox)en' : '$1',
		'(alias)(es)*$' : '$1',
		'(alumn|bacill|cact|foc|fung|nucle|radi|stimul|syllab|termin|viri?)i$' : '$1us',
		'([ftw]ax)es' : '$1',
		'(cris|ax|test)es$' : '$1is',
		'(shoe|slave)s$' : '$1',
		'(o)es$' : '$1',
		'ouses$/' : 'ouse',
		'([^a])uses$/' : '$1us',
		'([m|l])ice$' : '$1ouse',
		'(x|ch|ss|sh)es$' : '$1',
		'(m)ovies$' : '$1$2ovie',
		'(s)eries$' : '$1$2eries',
		'([^aeiouy]|qu)ies$' : '$1y',
		'([lr])ves$' : '$1f',
		'(tive)s$' : '$1',
		'(hive)s$' : '$1',
		'(drive)s$' : '$1',
		'([^fo])ves$' : '$1fe',
		'(^analy)ses$' : '$1sis',
		'(analy|diagno|^ba|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$' : '$1$2sis',
		'([ti])a$' : '$1um',
		'(p)eople$' : '$1$2erson',
		'(m)en$' : '$1an',
		'(c)hildren$' : '$1$2hild',
		'(n)ews$' : '$1$2ews',
		'eaus$/' : 'eau',
		'^(.*us)$/' : '$1',
		's$' : ''
	}
	/*return the plural of an english word*/
	this.pluralize = function(word){
		var rules = this.pluralRules;
		for(var i in rules) {
			var r = new RegExp(i, 'i');
			if(r.exec(word)) {
				return word.replace(r, rules[i]);
			}
		}
		return word;
	}
	
	/*return the singular of an english word*/
	this.singularize = function(word){
		var rules = this.singularRules;
		for(var i in rules) {
			var r = new RegExp(i, 'i');
			if(r.exec(word)) {
				return word.replace(r, rules[i]);
			}
		}
		return word;
	}
	
	/*Make camel case from string removing all non letter characters*/
	this.camelize = function(w){
		return ucwords((w+"").replace(/([^a-zA-Z\u00E0-\u00FC])/, ' ')).replace(/ /, '');
	}
	
	this.urlize = function(w){
		return this.pluralize(w).toLowerCase();
	}
}

var Routes = new function(){
	/*get the part of the url with the action name*/
	this.urlAction = function(url){
		url = url.replace(Page.webroot, '');
		var e = url.split('/');
		//loge(e[0]);
		if(e.length == 1) return e[0];
		return ucwords(Inflector.singularize(e[0])) + '.' + e[1];
	}
	
	/*remove .. and . from path*/
	this.parsePath = function(path){
		var p = (""+path).split('/');
		var s1 = path[0], s2 = path[path.length - 1];
		s1 = s1 == '/' ? '/' : ''; 
		s2 = s2 == '/' ? '/' : '';
		//alert(p.length);
		var newP = [];
		for(var i in p)
			if(!(p[i + 1] && p[i + 1] == '..' || p[i] == '..' || p[i] == '' || p[i] == '.')) {
				newP.push(p[i]);
			} else {
				//alert(p[i]);
			}
		//alert( newP.join('/'));
		if(newP.length == 0) return s1;
		return s1+newP.join('/')+s2;
	}
}

var Set = new function(){
	/*Usage: if arr is [0 : {name:'Peter', surname:'Mustermann'}] with path = 'name' will return [0 : 'Peter']*/
	this.extract = function(arr, path){
		return $.map(arr, function(el, i){
			return eval('el.'+path);
		});
	}
	
	var self = this;
	/*encode an object as a string for comparison. TODO: sort order*/
	this.commaList = function(arr){
		var s = $.map(arr, function(el, i){
			if(typeof(el) == 'object') return i + '=(' +self.commaList(el)+')';
			return i+'='+el;
		});
		return s.join(',');
	}
}



//return only the functions from a smarty template string
function getFunctions(str){
	var matches = str.replace(/\n|\r/g, '').match(/{function.*?{\/function}/g);
	return matches.join(' ');
}
//return only the function names from a smarty template string
function getFunctionNames(str){
	var matches = str.replace(/\n|\r/g, '').match(/{function.+?name=['"](.*?)['"]/g).join('|').replace(/{function.+?name=['"](.*?)['"]/g, "$1").split('|');
	return matches;
}

//return the meta data from a template like <title>A title</title> or <icon>An icon</icon>
function getMetaData(str){
	var metas = ['title', 'icon'];
	var data = {};
	var r, d;
	//loge(str);
	for(var i in metas) {
		r = new RegExp('<'+metas[i]+'>(.*?)</'+metas[i]+'>', 'g');
		d = (str+'').replace(/\n|\r/g, '').match(r);
		//loge(d);
		if(d && d.length > 0)
			d = d[0].replace(r, '$1');
		data[metas[i]] = d;
	}
	return data;
}
		

//see php ucwords: make first letter of a word uppercase
function ucwords(str){
  // +   original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
  // +   improved by: Waldo Malqui Silva
  // +   bugfixed by: Onno Marsman
  // +   improved by: Robin
  // +      input by: James (http://www.james-bell.co.uk/)
  // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	return (str + '').replace(/^([a-z\u00E0-\u00FC])|\s+([a-z\u00E0-\u00FC])/g, function ($1) {
    	return $1.toUpperCase();
  	});
}

var Time = new function(){
	this.date = function(format, timestamp) {
	//for format letters: http://php.net/manual/de/function.date.php
		
	  // http://kevin.vanzonneveld.net
	  // +   original by: Carlos R. L. Rodrigues (http://www.jsfromhell.com)
	  // +      parts by: Peter-Paul Koch (http://www.quirksmode.org/js/beat.html)
	  // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	  // +   improved by: MeEtc (http://yass.meetcweb.com)
	  // +   improved by: Brad Touesnard
	  // +   improved by: Tim Wiel
	  // +   improved by: Bryan Elliott
	  //
	  // +   improved by: Brett Zamir (http://brett-zamir.me)
	  // +   improved by: David Randall
	  // +      input by: Brett Zamir (http://brett-zamir.me)
	  // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	  // +   improved by: Brett Zamir (http://brett-zamir.me)
	  // +   improved by: Brett Zamir (http://brett-zamir.me)
	  // +   improved by: Theriault
	  // +  derived from: gettimeofday
	  // +      input by: majak
	  // +   bugfixed by: majak
	  // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	  // +      input by: Alex
	  // +   bugfixed by: Brett Zamir (http://brett-zamir.me)
	  // +   improved by: Theriault
	  // +   improved by: Brett Zamir (http://brett-zamir.me)
	  // +   improved by: Theriault
	  // +   improved by: Thomas Beaucourt (http://www.webapp.fr)
	  // +   improved by: JT
	  // +   improved by: Theriault
	  // +   improved by: Rafał Kukawski (http://blog.kukawski.pl)
	  // +   bugfixed by: omid (http://phpjs.org/functions/380:380#comment_137122)
	  // +      input by: Martin
	  // +      input by: Alex Wilson
	  // +   bugfixed by: Chris (http://www.devotis.nl/)
	  // %        note 1: Uses global: php_js to store the default timezone
	  // %        note 2: Although the function potentially allows timezone info (see notes), it currently does not set
	  // %        note 2: per a timezone specified by date_default_timezone_set(). Implementers might use
	  // %        note 2: this.php_js.currentTimezoneOffset and this.php_js.currentTimezoneDST set by that function
	  // %        note 2: in order to adjust the dates in this function (or our other date functions!) accordingly
	  // *     example 1: date('H:m:s \\m \\i\\s \\m\\o\\n\\t\\h', 1062402400);
	  // *     returns 1: '09:09:40 m is month'
	  // *     example 2: date('F j, Y, g:i a', 1062462400);
	  // *     returns 2: 'September 2, 2003, 2:26 am'
	  // *     example 3: date('Y W o', 1062462400);
	  // *     returns 3: '2003 36 2003'
	  // *     example 4: x = date('Y m d', (new Date()).getTime()/1000);
	  // *     example 4: (x+'').length == 10 // 2009 01 09
	  // *     returns 4: true
	  // *     example 5: date('W', 1104534000);
	  // *     returns 5: '53'
	  // *     example 6: date('B t', 1104534000);
	  // *     returns 6: '999 31'
	  // *     example 7: date('W U', 1293750000.82); // 2010-12-31
	  // *     returns 7: '52 1293750000'
	  // *     example 8: date('W', 1293836400); // 2011-01-01
	  // *     returns 8: '52'
	  // *     example 9: date('W Y-m-d', 1293974054); // 2011-01-02
	  // *     returns 9: '52 2011-01-02'
		var that = this,
		  jsdate,
		  f,
		  formatChr = /\\?([a-z])/gi,
		  formatChrCb,
		  // Keep this here (works, but for code commented-out
		  // below for file size reasons)
		  //, tal= [],
		  _pad = function (n, c) {
			n = n.toString();
			return n.length < c ? _pad('0' + n, c, '0') : n;
		  },
		  txt_words = ["Sun", "Mon", "Tues", "Wednes", "Thurs", "Fri", "Satur", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
	  formatChrCb = function (t, s) {
		return f[t] ? f[t]() : s;
	  };
	  f = {
		// Day
		d: function () { // Day of month w/leading 0; 01..31
		  return _pad(f.j(), 2);
		},
		D: function () { // Shorthand day name; Mon...Sun
		  return f.l().slice(0, 3);
		},
		j: function () { // Day of month; 1..31
		  return jsdate.getDate();
		},
		l: function () { // Full day name; Monday...Sunday
		  return txt_words[f.w()] + 'day';
		},
		N: function () { // ISO-8601 day of week; 1[Mon]..7[Sun]
		  return f.w() || 7;
		},
		S: function () { // Ordinal suffix for day of month; st, nd, rd, th
		  var j = f.j();
		  return j < 4 | j > 20 && (['st', 'nd', 'rd'][j % 10 - 1] || 'th');
		},
		w: function () { // Day of week; 0[Sun]..6[Sat]
		  return jsdate.getDay();
		},
		z: function () { // Day of year; 0..365
		  var a = new Date(f.Y(), f.n() - 1, f.j()),
			b = new Date(f.Y(), 0, 1);
		  return Math.round((a - b) / 864e5);
		},
	
		// Week
		W: function () { // ISO-8601 week number
		  var a = new Date(f.Y(), f.n() - 1, f.j() - f.N() + 3),
			b = new Date(a.getFullYear(), 0, 4);
		  return _pad(1 + Math.round((a - b) / 864e5 / 7), 2);
		},
	
		// Month
		F: function () { // Full month name; January...December
		  return txt_words[6 + f.n()];
		},
		m: function () { // Month w/leading 0; 01...12
		  return _pad(f.n(), 2);
		},
		M: function () { // Shorthand month name; Jan...Dec
		  return f.F().slice(0, 3);
		},
		n: function () { // Month; 1...12
		  return jsdate.getMonth() + 1;
		},
		t: function () { // Days in month; 28...31
		  return (new Date(f.Y(), f.n(), 0)).getDate();
		},
	
		// Year
		L: function () { // Is leap year?; 0 or 1
		  var j = f.Y();
		  return j % 4 === 0 & j % 100 !== 0 | j % 400 === 0;
		},
		o: function () { // ISO-8601 year
		  var n = f.n(),
			W = f.W(),
			Y = f.Y();
		  return Y + (n === 12 && W < 9 ? 1 : n === 1 && W > 9 ? -1 : 0);
		},
		Y: function () { // Full year; e.g. 1980...2010
		  return jsdate.getFullYear();
		},
		y: function () { // Last two digits of year; 00...99
		  return f.Y().toString().slice(-2);
		},
	
		// Time
		a: function () { // am or pm
		  return jsdate.getHours() > 11 ? "pm" : "am";
		},
		A: function () { // AM or PM
		  return f.a().toUpperCase();
		},
		B: function () { // Swatch Internet time; 000..999
		  var H = jsdate.getUTCHours() * 36e2,
			// Hours
			i = jsdate.getUTCMinutes() * 60,
			// Minutes
			s = jsdate.getUTCSeconds(); // Seconds
		  return _pad(Math.floor((H + i + s + 36e2) / 86.4) % 1e3, 3);
		},
		g: function () { // 12-Hours; 1..12
		  return f.G() % 12 || 12;
		},
		G: function () { // 24-Hours; 0..23
		  return jsdate.getHours();
		},
		h: function () { // 12-Hours w/leading 0; 01..12
		  return _pad(f.g(), 2);
		},
		H: function () { // 24-Hours w/leading 0; 00..23
		  return _pad(f.G(), 2);
		},
		i: function () { // Minutes w/leading 0; 00..59
		  return _pad(jsdate.getMinutes(), 2);
		},
		s: function () { // Seconds w/leading 0; 00..59
		  return _pad(jsdate.getSeconds(), 2);
		},
		u: function () { // Microseconds; 000000-999000
		  return _pad(jsdate.getMilliseconds() * 1000, 6);
		},
	
		// Timezone
		e: function () { // Timezone identifier; e.g. Atlantic/Azores, ...
		  // The following works, but requires inclusion of the very large
		  // timezone_abbreviations_list() function.
	/*              return that.date_default_timezone_get();
	*/
		  throw 'Not supported (see source code of date() for timezone on how to add support)';
		},
		I: function () { // DST observed?; 0 or 1
		  // Compares Jan 1 minus Jan 1 UTC to Jul 1 minus Jul 1 UTC.
		  // If they are not equal, then DST is observed.
		  var a = new Date(f.Y(), 0),
			// Jan 1
			c = Date.UTC(f.Y(), 0),
			// Jan 1 UTC
			b = new Date(f.Y(), 6),
			// Jul 1
			d = Date.UTC(f.Y(), 6); // Jul 1 UTC
		  return ((a - c) !== (b - d)) ? 1 : 0;
		},
		O: function () { // Difference to GMT in hour format; e.g. +0200
		  var tzo = jsdate.getTimezoneOffset(),
			a = Math.abs(tzo);
		  return (tzo > 0 ? "-" : "+") + _pad(Math.floor(a / 60) * 100 + a % 60, 4);
		},
		P: function () { // Difference to GMT w/colon; e.g. +02:00
		  var O = f.O();
	
		  return (O.substr(0, 3) + ":" + O.substr(3, 2));
		},
		T: function () { // Timezone abbreviation; e.g. EST, MDT, ...
		  return 'UTC';
		},
		Z: function () { // Timezone offset in seconds (-43200...50400)
		  return -jsdate.getTimezoneOffset() * 60;
		},
	
		// Full Date/Time
		c: function () { // ISO-8601 date.
		  return 'Y-m-d\\TH:i:sP'.replace(formatChr, formatChrCb);
		},
		r: function () { // RFC 2822
		  return 'D, d M Y H:i:s O'.replace(formatChr, formatChrCb);
		},
		U: function () { // Seconds since UNIX epoch
		  return jsdate / 1000 | 0;
		}
	  };
	  this.date = function (format, timestamp) {
		that = this;
		jsdate = (timestamp === undefined ? new Date() : // Not provided
		  (timestamp instanceof Date) ? new Date(timestamp) : // JS Date()
		  new Date(timestamp * 1000) // UNIX timestamp (auto-convert to int)
		);
		return format.replace(formatChr, formatChrCb);
	  };
	  return this.date(format, timestamp);
	}
	this.ago = function(format, timestamp){
		return this.date(format, timestamp);
	}
	this.secToDuration = function(s){
		return this.date('i:s', s) + ' min';
	}
}


/*wrapper for use in jSmart*/
function urlize(w) {
	return Inflector.urlize(w);
}
function timeAgo(format, timestamp){
	return Time.ago(format, timestamp);
}
function secToDuration(s){
	return Time.secToDuration(s);
}
/* end wrapper for jSmart*/
function random(){return Math.random();}

(function(a,b,c){"use strict",a.fn.selectText=function(){var a=this[0],d=b.body.createTextRange,e=c.getSelection(),f=b.createRange(),g;b.body.createTextRange?(d.moveToElementText(a),d.select()):c.getSelection&&(e.setBaseAndExtent?e.setBaseAndExtent(g,0,g,1):(f.selectNodeContents(a),e.removeAllRanges(),e.addRange(f)))}})(jQuery,document,window)



//var test = [{User : {name:'Hans',id:1}}, {User : {name:'Klaus'}}, {User : {name : 'Dieter'}}];
//var test2 = '{function name="test"} jjhjkdsafdf {} aller  möglicher scheiß! \n {/function} {function name="Hallo"} jjhjkdsafdf {} aller möglicher  scheiß! {/function}  {function name="sadsa"} ttt {} aller möglicher scheiß! {/function} Das hier nicht mehr! dsfdsfdsffagfagdgfda';
//var test3 = $('<form><input name="test[]" value="3" /><input name="test[]" value="4" /></form>');
/*var test4 = "HH{random()}";
$(document).ready(function(){
	var t = new jSmart(test4);
	alert(t.fetch());
});*/

//console.log(test3.serializeJSON());
//console.log(Set.commaList(test));
//console.log(getFunctions(test2));
//console.log(getFunctionNames(test2));