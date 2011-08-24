dependencies = {
	action: "clean,release",
	version: "1.0-bba",
	releaseName: "bba",
	loader: "default",
	optimize: "shrinksafe",
	layerOptimize: "shrinksafe",
	copyTests: "false",
    layers:[
        {
            name: "bba.js",
            dependencies: [
                "dijit.layout.LayoutContainer",
				"dijit.layout.BorderContainer",
				"dijit.layout.TabContainer",
				"dijit.layout.ContentPane",
				"dijit.layout.LinkPane",
				"dojox.grid.DataGrid",
				"dojo.data.ItemFileReadStore",
				"dijit.TitlePane",
				"dijit.form.FilteringSelect",
				"dijit.form.TextBox",
				"dijit.form.DateTextBox",
				"dijit.form.NumberSpinner",
				"dijit.form.SimpleTextarea",
				"dijit.form.Button",
				"dijit.form.Form",
				"dojo.parser"
			]
        }
    ],
    "prefixes": [
        ["dijit","../dijit"],
        ["dojox","../dojox"]
    ]
};
