dependencies = {
	action: "clean,release",
	version: "bba-2.0",
	releaseName: "bba-temp",
	stripConsole: "normal",
	selectorEngine:"acme",
	optimize: "shrinksafe",
	layerOptimize: "shrinksafe",
	copyTests: "false",
    layers:[
        {
            name: "dojo.js",
            dependencies: [
                "bba.Auth",
                "bba.Client",
                "bba.Contract",
                "bba.Meter",
                "bba.Site",
                "bba.Supplier",
                "bba.User"
			]
        }
    ],
    "prefixes": [
        ["dijit","../dijit"],
        ["dojox","../dojox"],
        ["bba","../bba"]
    ]
};
