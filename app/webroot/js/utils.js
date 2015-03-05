var utils = {
	getUrlParameters:  function(url){
		var vars = {}, hash;
		var hashes = url.slice(url.indexOf('?') + 1).split('&');
		for(var i = 0; i < hashes.length; i++)
		{
			hash = hashes[i].split('=');
			//vars.push(hash[0]);
			vars[hash[0]] = hash[1];
		}
		return vars;
	},
	parseMethodParams: function(paramString){
		var params = paramString.split(/\n/);
		for(var i in params) {
			var p = params[i].split(':');
			params[i] = {'name': p[0], 'type': p[1], 'description':p[2]};
		}
		return params;
	}
}