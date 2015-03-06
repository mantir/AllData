/**
 * This is the methods controller
 *
 * @copyright     Martin Kapp 2014-15
 * @link          http://headkino.de
 * @package       app.Lib
 * @since         v 0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * 
 * @class app.controllers.methods
 */
app.controllers.methods = BackboneMVC.Controller.extend({
    name: 'methods',
	views:[], //These are the views we will use with the controller
	editor: {
		lineNumbers: true,
		mode: "php"
	},
	
	/**
	 * Description
	 * @method add
	 * @param {} id
	 * @return 
	 */
	add:function(id){
		var self = this;
		app.loadPage(this.name, 'add', app.url()).done(function(d){
			self.coding();
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
		app.loadPage(this.name, 'edit', app.url()).done(function(d){
			self.coding(id);
		});
    },
	
	/**
	 * Description
	 * @method view
	 * @return 
	 */
	view: function(){
		$('.make-null').click(function(){
			$(this).prevAll('select').each(function(index, element) {
				$(this).val(0);
			});
		});
		app.require(['calendar'], function() {
			$('.methods-view').find('.calendar-input').datepicker($.extend(app.config['calendar'], {
				startDate: '1.1.2014',
				endDate: new Date()
			}));
		});
	},
	
	/**
	 * Description
	 * @method execute
	 * @return 
	 */
	execute: function(){
		
	},
	
	structs: {
		'values': "foreach($%s['data'] as $i => $m) {\n\t $m['data']\n}\n",
		'end_results': "end($results)"
	},
	
	$doc: false,
	$params: false,
	/**
	 * Description
	 * @method coding
	 * @param {} id
	 * @return 
	 */
	coding:function(id){
		var self = this;
		if($('#MethodCode').length == 0)
			return;
		app.require('codeeditor', function(CodeMirror){
			self.$doc = CodeMirror.fromTextArea($('#MethodCode')[0], self.editor);
			self.$params = CodeMirror.fromTextArea($('#MethodParams')[0], self.editor);
			self.$params.on('change', function(){
				self.parseParams();
			});
			self.parseParams();
		});
	},
	
	/**
	 * Description
	 * @method parseParams
	 * @return 
	 */
	parseParams: function(){
		var self = this;
		var params = [
			'start:num:Start timestamp of current calculation',
			'end:num:End Timestamp of current calculation',
			'results:Array:Is a list of previous calculated results. An element in the array has the form Array([data] => ..., [timestamp] => ...)'
		];
		params = utils.parseMethodParams(self.$params.getValue()+"\n"+params.join("\n"));
		console.log(params);
		var outV = '';
		var outL = '';
		for(var i in params) {
			var name = params[i].name;
			outV += '<li class="list-group-item"><a data-toggle="tooltip" class="add-variable" title="'+params[i].description+'" href="javascript:;">$'+name+'</a></li>';
			if(params[i].type == 'val')
				outL += '<li class="list-group-item"><a data-toggle="tooltip" data-struct="values" data-var="'+name+'" class="add-struct" title="Loop through the measured data of $'+name+': '+params[i].description+'" href="javascript:;">Loop $'+name+'</a></li>';
		}
		outL += '<li class="list-group-item"><a data-toggle="tooltip" data-struct="end_results" data-var="'+name+'" class="add-struct" title="Last result" href="javascript:;">Last result</a></li>';
		$('#vars-list').html(outV);
		$('#loop-list').html(outL);
		$('#vars-list a, #loop-list a').tooltip();
		$('.add-variable,.add-struct').click(function(){
			var doc = self.$doc;
			var t = $(this).text();
			var s = $(this).data('struct');
			var variable = $(this).data('var');
			var v = s ? self.structs[s].replace(/%s/, variable) : t;
			var c = doc.getCursor();
			if(c.ch > 0) v = "\n"+v;
			doc.replaceRange(v, c);
			doc.focus();
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
     * @method admin_index
     * @return 
     */
    admin_index:function(){
		var self = this;
		app.loadPage(this.name, 'index', app.url()).done(function(d){
			
		});
    }
});