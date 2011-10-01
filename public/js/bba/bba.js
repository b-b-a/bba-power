dojo.provide("bba");

dojo.declare("bba", null, {
    grids : null,

    abrev : {
        ad : "address",
        co : "contact"
    },

    constructor : function() {
        this.grids = dijit.registry.byClass("dojox.grid.DataGrid");

        this.grids.forEach(function(grid) {
            this.gridRowClick(grid);
        }, this);
    },

    gridLink : function(selectedId, inputName, url) {
        var formNode = dojo.create("form", {
            action:url,
            method:"post"
        }, dojo.body());

        dojo.create("input", {
            type:"hidden",
            name:inputName,
            value:selectedId
        },formNode);

        formNode.submit();
    },

    gridRowClick : function(grid) {
        dojo.connect(grid, "onRowClick", this, function(val) {
            selectedIndex = grid.focus.rowIndex;
            selectedItem = grid.getItem(selectedIndex);

            ident = grid.store._identifier;

            if (!ident) {
                ident = grid.store.getFeatures()["dojo.data.api.Identity"];
            }

            i = ident.split("_");

            selectedId = grid.store.getValue(selectedItem, ident);

            this.gridLink(selectedId, i[1], "/" + this.hyphenate(i[0]) + "/edit");
        });
    },

    hyphenate: function(str) {
        str = str.replace(/[A-Z]/g, function(match) {
            return ('-{' + match.charAt(0).toLowerCase());
        });

        if (str.match('{')) str = str + '}';

        return dojo.replace(str, this.abrev);
    }
});

dojo.require("bba");

dojo.addOnLoad(function(){
    var bbaObject = new bba();
});
