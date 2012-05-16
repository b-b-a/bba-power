dependencies = {
	action: "clean,release",
	version: "bba-3.0",
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
                "dojo/dom",
                "dojo/ready",
                "dojo/parser",
                "dojo/_base/array",
                "dojo/_base/connect",
                "dojo/_base/declare",
                "dojo/_base/html",
                "dojo/_base/lang",
                "dojo/_base/xhr",
                "dojo/data/ItemFileReadStore",
                "dojo/data/ItemFileWriteStore",

                "dijit/registry",
                "dijit/Dialog",
                "dijit/WidgetSet",
                "dijit/layout/ContentPane",
                "dijit/layout/StackContainer",
                "dijit/layout/BorderContainer",
                "dijit/layout/TabContainer",

                "dijit/form/Form",
                "dijit/form/Button",
                "dijit/form/ValidationTextBox",
                "dijit/form/Button",
                "dijit/form/RadioButton",
                "dijit/form/NumberTextBox",
                "dijit/form/FilteringSelect",
                "dijit/form/SimpleTextarea",

                "dojox/data/QueryReadStore",
                "dojox/grid/DataGrid",
                "dojox/grid/_CheckBoxSelector",
                "dojox/widget/Wizard",
                "dojox/widget/Standby"
			]
        }
    ],
    "prefixes": [
        ["dijit","../dijit"],
        ["dojox","../dojox"]
    ]
};
