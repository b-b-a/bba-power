var profile = (function(){

    copyOnly = function(filename, mid){
        var list = {
            "bba/bba.profile":1,
            "bba/package.json":1
        };
        return (mid in list) || /\.js$/.test(filename);
    };

	return {
		resourceTags:{

			copyOnly: function(filename, mid){
				return copyOnly(filename, mid);
			},

			amd: function(filename, mid){
				return !copyOnly(filename, mid) && /\.js$/.test(filename);
			}
		},

		trees:[
			[".", ".", /(\/\.)|(~$)/]
		]
	};
})();