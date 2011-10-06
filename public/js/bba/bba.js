
dojo.require("dojox.grid.DataGrid");

dojo.extend(dojox.grid.DataGrid, {
    abrev : {
        ad : "address",
        co : "contact"
    },

    _onFetchComplete : function(items, req) {
        if(!this.scroller){return;}
		if(items && items.length > 0){
			//console.log(items);
			dojo.forEach(items, function(item, idx){
				this._addItem(item, req.start+idx, true);
			}, this);
			if(this._autoHeight){
				this._skipRowRenormalize = true;
			}
			this.updateRows(req.start, items.length);
			if(this._autoHeight){
				this._skipRowRenormalize = false;
			}
			if(req.isRender){
				this.setScrollTop(0);
				this.postrender();
			}else if(this._lastScrollTop){
				this.setScrollTop(this._lastScrollTop);
			}
		}
		delete this._lastScrollTop;
		if(!this._isLoaded){
			this._isLoading = false;
			this._isLoaded = true;
		}
		this._pending_requests[req.start] = false;

        dojo.connect(this, "onRowClick", function(val) {
            this.gridRowClick();
        });

        if (this.rowCount == 1) this.gridLineCheck();
    },

    gridLineCheck : function() {
        if(!location.href.match('edit') && !location.href.match('add')) {
            this.gridRowClick(0);
        }
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

    gridRowClick : function(selectedIndex) {
        if (selectedIndex == null) {
            selectedIndex = this.focus.rowIndex;
        }

        selectedItem = this.getItem(selectedIndex);

        ident = this.store._identifier;

        if (!ident) {
            ident = this.store.getFeatures()["dojo.data.api.Identity"];
        }

        i = ident.split("_");

        selectedId = this.store.getValue(selectedItem, ident);

        this.gridLink(selectedId, i[1], "/" + this.hyphenate(i[0]) + "/edit");
    },

    hyphenate: function(str) {
        str = str.replace(/[A-Z]/g, function(match) {
            return ('-{' + match.charAt(0).toLowerCase());
        });

        if (str.match('{')) str = str + '}';

        return dojo.replace(str, this.abrev);
    }
 });

