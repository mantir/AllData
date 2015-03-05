var Layout = new function(){
	this.pageWindowResize = false;

	this.windowResize = function() {
		// IE trick prevent freezes (r) start//
		window.blur();
		//end of trick //
		// define container //
		var container = $('#container')[0];
		var page = $("html")[0];
	
		var pageHeight = $(window).innerHeight();
		
		/*if (pageHeight < 600) {
			pageHeight = 600;
			container.style.height = "600px";
			page.style.overflow = "auto";
		} else*/ {
			page.style.overflow = "hidden";
			container.style.height = pageHeight + "px";
		}
	
		$("#content")[0].style.height = (pageHeight - 120) + "px";
		try{
			var scrollbar = $('.mainScrollbarN').eq(0);
			window.setTimeout(function(){
				scrollbar.children().each(function(){
					if($(this).width() >= scrollbar.width()) {
						scrollbar.width(scrollbar.width() + 15);
						return false;
					}
				})
			}, 1);
			//$(".mainScrollbar")[0].style.height = (pageHeight - 106) + "px";
			//$(".mainScrollbarN")[0].style.height = (pageHeight - 135) + "px";
			//$(".overlayContent")[0].style.height = (pageHeight - 100) + "px";
			// tinyscrollbar (tinyscrollbar_update)//
			//$($('.mainScrollbar')[0]).tinyscrollbar_update('relative');
			$($('.mainScrollbarN')[0]).tinyscrollbar_update('relative');
			//$($('.mainScrollbarF')[0]).tinyscrollbar_update('relative');
		} catch(ex) {
			
		}
		
		$('#content').find('.view').each(function(){
			loge($(this).offset().top);
			$(this)[0].style.height = (pageHeight - $(this).offset().top - 30) + 'px';
		});
		
		//loge('resizing');
		//loge(Layout.pageWindowResize);
		if(typeof(Layout.pageWindowResize) == 'function'){
			loge('pageWindowResize');
			Layout.pageWindowResize();
		} 

		// IE trick prevent freezes (r) start//
		window.focus();
		//end of trick //
	}
	
	//convinience wrapper for windowResize
	this.resize = this.windowResize;
	
	this.beforeBuild = function(data, context){
		
	}
	
	this.afterBuild = function(data, context){
		//alert(1);
		$(window).resize(); //to fix sizes e.g. in dialog because of inserted content
	}
	
	this.setWindowResize = function(func){
		if(func && typeof(func) == 'function') {
			//loge('changeResizeFunc');
			//loge(func);
			Layout.pageWindowResize = func;
			Layout.windowResize();
		} else {
			loge('noResizeFunc');
			Layout.pageWindowResize = false;
			Layout.windowResize();
		}
			
	}

	this.basicStructure = function(){
		//layout//
		$(window).resize(Layout.windowResize);
		Layout.windowResize();
	}
}