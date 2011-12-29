dependencies = {
	action: "clean,release",
	version: "bba-2.0",
	releaseName: "bba",
	stripConsole: "normal",
	selectorEngine:"acme",
	optimize: "shrinksafe",
	layerOptimize: "shrinksafe",
	copyTests: "false",
    layers:[
        {
            name: "dojo.js",
            dependencies: [
				"dojo.data.ItemFileReadStore",
				"dojo.data.ItemFileWriteStore",
				"dojo.parser",
				"dijit.Dialog",

				"dijit.form.Button",
				"dijit.form.DateTextBox",
				"dijit.form.FilteringSelect",
				"dijit.form.Form",
				"dijit.form.NumberSpinner",
				"dijit.form.RadioButton",
				"dijit.form.SimpleTextarea",
				"dijit.form.TextBox",
				"dijit.form.ValidationTextBox",

				"dijit.layout.BorderContainer",
				"dijit.layout.ContentPane",
				"dijit.layout.LinkPane",
				"dijit.layout.TabContainer",

				"dojox.data.QueryReadStore",
				"dojox.grid.DataGrid",

				"dojox.widget.Wizard",

                "bba.Core",
                "bba.ContentPane",
                "bba.StackContainer",
                "bba.DataGrid",

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
