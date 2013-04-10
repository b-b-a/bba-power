/*
 * Client.js
 *
 * Copyright (c) 2011 Shaun Freeman <shaun@shaunfreeman.co.uk>.
 *
 * This file is part of BBA.
 *
 * BBA is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * BBA is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with BBA.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @category   BBA
 * @package
 * @subpackage
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
define("bba/Client",
[
	"dojo/json",
    "dojo/dom",
    "dojo/query",
    "dojo/parser",
    "dojo/_base/connect",
    "dojo/_base/xhr",
    "dijit/registry",
    "dojo/date",
    "dijit/Dialog",
    "dojo/text!./html/LoaEmptyMessage.html",
    "dojo/text!./html/LoaDateMessage.html",
    "bba/Core",
    "bba/Site",
    "bba/Contract",
    "dojox/widget/Wizard",
    "dijit/form/ValidationTextBox",
    "dijit/form/FilteringSelect",
    "dijit/form/CheckBox",
    "dijit/form/SimpleTextarea",
    "dojox/form/Uploader",
    "dojox/form/uploader/plugins/IFrame"
],
    function(json, dom, query, parser, connect, xhr, registry, date, Dialog, LoaEmpty, LoaDate, core) {

    bba.Client = {
        dateExpiryLoa : null,

        gridLayouts : {
            client : [
                {field: 'client_idClient', width: '50px', name: 'Id'},
                {field: 'client_name', width: '200px', name: 'Client'},
                {field: 'clientAd_address1', width: '200px', name: 'Address 1'},
                {field: 'clientAd_address2', width: '200px', name: 'Address 2'},
                {field: 'clientAd_address3', width: '200px', name: 'Town/City'},
                {field: 'clientAd_postcode', width: '100px', name: 'Postcode'},
                {field: '', width: 'auto', name: ''}
            ],
            clientAddress : [
                {field: 'clientAd_idAddress', width: '50px', name: 'Id'},
                {field: 'clientAd_addressName', width: '250px', name: 'Address Name'},
                {field: 'clientAd_address1', width: '300px', name: 'Address 1'},
                {field: 'clientAd_address2', width: '200px', name: 'Address 2'},
                {field: 'clientAd_address3', width: '100px', name: 'Town/City'},
                {field: 'clientAd_postcode', width: '100px', name: 'Postcode'},
                {field: '', width: 'auto', name: ''}
            ],
            clientPersonnel : [
                {field: 'clientPers_idClientPersonnel', width: '50px', name: 'Id'},
                {field: 'tables_value', width: '100px', name: 'Type'},
                {field: 'clientPers_name', width: '200px', name: 'Name'},
                {field: 'clientPers_phone', width: '300px', name: 'Phone'},
                {field: 'clientPers_email', width: '200px', name: 'Email'},
                {field: 'clientAd_postcode', width: '100px', name: 'Postcode'},
                {field: '', width: 'auto', name: ''}
            ],
            meter : [
                 {field: 'meter_idMeter', width: '50px', name: 'Id'},
                 {field: 'clientAd_addressName', width: '200px', name: 'Address Name'},
                 {field: 'clientAd_postcode', width: '100px', name: 'Postcode'},
                 {field: 'meter_type', width: '95px', name: 'Meter Type'},
                 {field: 'meter_status', width: '115px', name: 'Meter Status'},
                 {field: 'meter_numberTop', width: '100px', name: 'Number Top'},
                 {field: 'meter_numberMain', width: '120px', name: 'Number Main'},
                 {field: 'contract_type', width: '100px', name: 'Contract Type'},
                 {field: 'contract_status', width: '110px', name: 'Contract Status'},
                 {field: 'contract_dateEnd', width: '100px', name: 'End Date'},
                 {field: '', width: 'auto', name: ''}
             ]
        },
         
        init : function()
        {
            core.addDataStore('clientStore', core.storeUrls.client);

            core.addGrid({
                id : 'clientGrid',
                store : core.dataStores.clientStore,
                structure : bba.Client.gridLayouts.client,
                sortInfo : '2',
                onRowClick : function() {
                     bba.Client.clientGridRowClick();
                }
            });
        },

        clientGridRowClick : function(grid)
        {
            grid = (grid) ? grid : core.grids.clientGrid;
            selectedIndex = grid.focus.rowIndex;
            selectedItem = grid.getItem(selectedIndex);
            id = grid.store.getValue(selectedItem, 'client_idClient');
            tabTitle = grid.store.getValue(selectedItem, 'client_name');

             bba.openTab({
                tabId : 'client' + id,
                title : (tabTitle) ? tabTitle : 'Client',
                url : './client/edit-client',

                content : {
                    type : 'details',
                    client_idClient : id
                }
            });
        },

        clientAdGridRowClick : function(grid, contentVars)
        {
            selectedIndex = grid.focus.rowIndex;
            selectedItem = grid.getItem(selectedIndex);
            id = grid.store.getValue(selectedItem, 'clientAd_idAddress');
            tabTitle = grid.store.getValue(selectedItem, 'clientAd_addressName');

            bba.openTab({
                tabId : 'clientAd' + id,
                title : (tabTitle) ? tabTitle : 'Client Address',
                url : './client/edit-client-address',
                content : dojo.mixin({
                        type : 'details',
                        clientAd_idAddress : id
                    }, contentVars)
            });
        },

        clientPersGridRowClick : function(grid, contentVars)
        {
            selectedIndex = grid.focus.rowIndex;
            selectedItem = grid.getItem(selectedIndex);
            id = grid.store.getValue(selectedItem, 'clientPers_idClientPersonnel');

             if (!dom.byId('clientPersForm')) {
                bba.openFormDialog({
                    url: './client/edit-client-personnel',
                    content: dojo.mixin({
                        type : 'edit',
                        clientPers_idClientPersonnel : id
                    }, contentVars),
                    dialog: 'clientPersForm'
                });
            } else {
                clientPersForm.show();
            }
        },

        newClientButtonClick : function()
        {
            if (!dom.byId('clientForm')) {
                bba.openFormDialog({
                    url: './client/add-client',
                    content: {type : 'add'},
                    dialog: 'clientForm'
                });
            } else {
                clientForm.show();
            }
        },

        newClientAdButtonClick : function(contentVars)
        {
            if (!dom.byId('clientAdForm')) {
                bba.openFormDialog({
                    url: './client/add-client-address',
                    content: dojo.mixin({type : 'add'}, contentVars),
                    dialog: 'clientAdForm'
                });
            } else {
                clientAdForm.show();
            }
        },

        newClientPersButtonClick : function(contentVars)
        {
            if (!dom.byId('clientPersForm')) {
                bba.openFormDialog({
                    url: './client/add-client-personnel',
                    content: dojo.mixin({type : 'add'}, contentVars),
                    dialog: 'clientPersForm'
                });
            } else {
                clientPersForm.show();
            }
        },

        editClientButtonClick : function(contentVars)
        {
            if (!dom.byId('clientForm')) {
                bba.openFormDialog({
                    url: './client/edit-client',
                    content: dojo.mixin({type : 'edit'}, contentVars),
                    dialog: 'clientForm',
                    deferredFunction: function() {
                    	bba.Client.setupDocEvents();
                    	bba.Client.dateExpiryLoa = clientForm.getValues().client_dateExpiryLoa;
                    }
                });
            } else {
                clientForm.show();
            }
        },

        editClientAdButtonClick : function(contentVars)
        {
            if (!dom.byId('clientAdForm')) {
                bba.openFormDialog({
                    url: './client/edit-client-address',
                    content: dojo.mixin({type : 'edit'}, contentVars),
                    dialog: 'clientAdForm',
                    deferredFunction: function() {
                        this.clientAdPostcode = clientAdForm.getValues().clientAd_postcode;
                    }.bind(this)
                });
            } else {
                clientAdForm.show();
            }
        },

        clientLoaEmptyDialog : function()
        {
            clientFormLoaEmpty = new Dialog({
                title: "Client Form Warning",
                content: LoaEmpty,
                style: "width: 300px",
                onShow : function(){
                    connect.connect(clientOKButton, 'onClick', function(){
                        clientFormLoaEmpty.hide();
                    });
                },
                onHide : function() {
                    bba.closeDialog(clientFormLoaEmpty);
                }
            });
            clientFormLoaEmpty.show();
        },
        
        clientLoaDateDialog : function()
        {
        	clientFormLoaDate = new Dialog({
                title: "Client Form Warning",
                content: LoaDate,
                style: "width: 300px",
                onShow : function(){
                    connect.connect(clientYesButton, 'onClick', function(){
                        if (clientForm.validate()) {
                            client_docLoa.submit();
                        }
                        clientFormLoaDate.hide();
                    });
                    connect.connect(clientNoButton, 'onClick', function(){
                    	clientFormStandby.hide();
                        clientFormLoaDate.hide();
                        bba.pageStandby.hide();
                    });
                },
                onHide : function() {
                    bba.closeDialog(clientFormLoaDate);
                }
            });
            clientFormLoaDate.show();
        },

        clientFormValidate : function()
        {
        	clientFormStandby.show();
            formValues = clientForm.getValues();
            
            if (!formValues.client_docLoa[0]) {
            	//clientFormStandby.hide();
                return clientForm.validate();
            }

            if (formValues.client_dateExpiryLoa === '') {
                bba.Client.clientLoaEmptyDialog();
                clientFormStandby.hide();
                return false;
            }
            
            xhr.post({
                url: './client/check-loa-date',
                content: {
                	oldDate : bba.Client.dateExpiryLoa,
                	newDate : formValues.client_dateExpiryLoa
                },
                handleAs: 'json',
                preventCache: true,
                load: function(data) {
                    if (data.error) {
                    	dom.byId('dialog').innerHTML = data.html;
                        parser.parse('dialog');
                        error.show();
                    } else {
                    	if (data.test == 'pass') {
                    		if (clientForm.validate()) {
                    			//bba.pageStandby.show();
                                client_docLoa.submit();
                            }
                    	} else {
                    		bba.Client.clientLoaDateDialog();
                    	}
                    }
                },
                error: function(data) {
                	bba.showXhrError(data.xhr.responseText);
                }
            });

            return false;
        },

        processClientForm : function()
        {
        	clientFormStandby.hide();
        	bba.closeDialog(clientForm);
            //bba.pageStandby.hide();
            data = arguments[0];
            console.log(data);
            
            try {
	            dom.byId('dialog').innerHTML = data.html;
	            parser.parse('dialog');
	
	            if (data.error) {
	                error.show();
	            } else if (data.saved > 0) {
	                if (data.client_idClient) {
	                    registry.byId('client' + data.client_idClient).refresh();
	                } else {
	                    registry.byId('clientGrid')._refresh();
	                }
	
	                if (bba.config.confirmBox) {
	                    confirm.show();
	                }
	            } else {
	                bba.setupDialog(clientForm);
	                bba.Client.setupDocEvents();
	                clientForm.show();
	            }
            } catch (e){
            	console.log(data)
            }
        },
        
        validateClientAdForm : function()
        {
        	clientAdFormStandby.show();
        	
        	// first check form for errors.
        	if (!clientAdForm.validate()) {
        		clientAdFormStandby.hide();
        		return false;
        	}
        	
        	formValues = clientAdForm.getValues();
        	
        	if (this.clientAdPostcode == formValues.clientAd_postcode) {
        		return true;
        	}
        	
        	// check for duplicate contract
        	xhr.post({
                url: './client/check-address-duplicates',
                content: formValues,
                handleAs: 'json',
                preventCache: true,
                load: function(data) {
                	if (data.dups) {
                		
                		console.log(data);
                		
                		dom.byId('dialog').innerHTML = data.html;
                        parser.parse('dialog');
                        
                        bba.setupDialog(addressDuplicates);
                        
                        connect.connect(dupsCloseButton, 'onClick', function(){
                        	clientAdFormStandby.hide();
                        	addressDuplicates.hide();
                        	
                        });
                        
                        connect.connect(dupsContinueButton, 'onClick', function(){
                        	bba.pageStandby.show();
                        	addressDuplicates.hide();
                        	bba.Client.processClientAdForm(formValues);
                        });
                		
                		addressDuplicates.show();
                	} else {
                		bba.pageStandby.show();
                		bba.Client.processClientAdForm(formValues);
                	}
                },
                error: function(data) {
                	bba.showXhrError(data.xhr.responseText);
                }
        	});
        	
        	return false;
        },

        processClientAdForm : function()
        {
        	clientAdFormStandby.hide();
            bba.closeDialog(clientAdForm);
        	bba.pageStandby.show();
            values = arguments[0];
            values.type = (values.clientAd_idAddress) ? 'edit' : 'add';
            
            console.log(values);

            xhr.post({
                url: './client/save-client-address',
                content: values,
                handleAs: 'json',
                preventCache: true,
                load: function(data) {
                    dom.byId('dialog').innerHTML = data.html;
                    parser.parse('dialog');
                    bba.pageStandby.hide();

                    if (data.error) {
                        error.show();
                    } else if (data.saved > 0) {
                        if (values.clientAd_idAddress) {
                        	if (values.site_idSite) {
                        		registry.byId('site' + values.site_idSite).refresh();
                        	} else {
                        		registry.byId('clientAd' + values.clientAd_idAddress).refresh();
                        	}
                        } else if (registry.byId('clientAdGrid' + values.clientAd_idClient)) {
                            registry.byId('clientAdGrid' + values.clientAd_idClient)._refresh();
                        }

                        if (bba.config.confirmBox) {
                            confirm.show();
                        }

                        bba.deferredFunction(data.saved);
                    } else {
                        bba.setupDialog(clientAdForm);
                        clientAdForm.show();
                    }
                },
                error: function(data) {
                	bba.showXhrError(data.xhr.responseText);
                }
            });
        },
        
        validateClientPersForm : function()
        {
        	clientPersFormStandby.show();
            formValues = clientPersForm.getValues();
            
            if (!clientPersForm.validate()) {
            	clientPersFormStandby.hide();
                return false;
            }
            
            xhr.post({
                url: './client/check-email-duplicates',
                content: formValues,
                handleAs: 'json',
                preventCache: true,
                load: function(data) {
                	if (data.dups) {
                		
                		console.log(data);
                		
                		dom.byId('dialog').innerHTML = data.html;
                        parser.parse('dialog');
                        
                        bba.setupDialog(emailDuplicates);
                        
                        connect.connect(dupsCloseButton, 'onClick', function(){
                        	clientPersFormStandby.hide();
                        	emailDuplicates.hide();
                        	
                        });
                        
                        connect.connect(dupsContinueButton, 'onClick', function(){
                        	bba.pageStandby.show();
                        	emailDuplicates.hide();
                        	bba.Client.processClientPersForm(formValues);
                        });
                		
                		emailDuplicates.show();
                	} else {
                		bba.pageStandby.show();
                		bba.Client.processClientPersForm(formValues);
                	}
                },
                error: function(data) {
                	bba.showXhrError(data.xhr.responseText);
                }
            });

            return false;
        },

        processClientPersForm : function()
        {
        	clientPersFormStandby.hide();
            bba.closeDialog(clientPersForm);
        	bba.pageStandby.show();
            values = arguments[0];
            values.type = (values.clientPers_idClientPersonnel) ? 'edit' : 'add';

            xhr.post({
                url: './client/save-client-personnel',
                content: values,
                handleAs: 'json',
                preventCache: true,
                load: function(data) {
                    dom.byId('dialog').innerHTML = data.html;
                    parser.parse('dialog');
                    bba.pageStandby.hide();
                    if (data.error) {
                        error.show();
                    } else if (data.saved > 0) {
                        if (registry.byId('clientPersGrid' + values.clientPers_idClient)) {
                            registry.byId('clientPersGrid' + values.clientPers_idClient)._refresh();
                        }

                        if (registry.byId('clientAdPersGrid' + values.clientPers_idAddress)) {
                            registry.byId('clientAdPersGrid' + values.clientPers_idAddress)._refresh();
                        }

                        if (bba.config.confirmBox) {
                            confirm.show();
                        }
                        bba.deferredFunction(data.saved);
                    } else {
                        bba.setupDialog(clientPersForm);
                        clientPersForm.show();
                    }
                },
                error: function(data) {
                	bba.showXhrError(data.xhr.responseText);
                }
            });
        },

        setupDocEvents : function()
        {
            /*docClick = connect.connect(dom.byId('client_docLoa_file'), "onclick", function(){
                query('input[name=client_docLoa]')[0].click();
            });*/

            docChange = connect.connect(client_docLoa, "onChange", function(fileArray){
                bba.docFileList(fileArray, 'client_docLoa_file');
            });
            
            docComplete = connect.connect(client_docLoa, "onComplete", bba.Client.processClientForm);
            docError = connect.connect(client_docLoa, "onError", bba.Client.processClientForm);
            
            connect.connect(registry.byId('client_registeredCompany'), "onClick", function(){
            	if (this.get('value')) {
            		registry.byId('client_numberCompany').set('value', 'Not a Registered Company');
            	} else {
            		registry.byId('client_numberCompany').set('value', '');
            	}
            });
            
            connect.connect(registry.byId('client_registeredVAT'), "onClick", function(){
            	if (this.get('value')) {
            		registry.byId('client_numberVAT').set('value', 'Not VAT Registered');
            	} else {
            		registry.byId('client_numberVAT').set('value', '');
            	}
            });
        },
        
        wizardClientPane : function()
        {
        	clientForm.attr('title', 'Client Information');
        	bba.Client.setupDocEvents();
        },
        
        wizardClientAdPane : function()
        {
        	if (typeof docClick != 'undefined'){
	        	//connect.disconnect(docClick);
	        	//connect.disconnect(docChange);
	        	//connect.disconnect(docComplete);
	        	//connect.disconnect(docError);
        	}
        	
        	clientForm.attr('title', 'Main (HQ) Address');
            dijit.byId('clientAd_addressName').attr('value', dijit.byId('client_name').attr('value'));
        },
        
        wizardClientPersPane : function()
        {
        	clientForm.attr('title', 'Main Liaison for BBA');
            dijit.byId('clientAd_addressName').set('value', dijit.byId('client_name').get('value'));
        },
        
        wizardDoneFunction : function()
        {
        	clientFormStandby.show();
        	
        	if (!clientForm.validate()) {
                alert('Please recheck all form entries for mistakes.');
                clientFormStandby.hide();
                return false;
            }
            
            if (clientForm.getValues().client_docLoa[0] && clientForm.getValues().client_dateExpiryLoa === '') {
                bba.Client.clientLoaEmptyDialog();
                clientFormStandby.hide();
                return false;
            }
        	
        	// check client address here.
            var vals = clientForm.get('value');
            
            xhr.post({
                url: './client/check-address-duplicates',
                content: vals,
                handleAs: 'json',
                preventCache: true,
                load: function(data) {
                	if (data.dups) {
                		
                		console.log(data);
                		
                		dom.byId('dialog').innerHTML = data.html;
                        parser.parse('dialog');
                        
                        bba.setupDialog(addressDuplicates);
                        
                        connect.connect(dupsCloseButton, 'onClick', function(){
                        	addressDuplicates.hide();
                        	clientFormStandby.hide();
                        });
                        
                        connect.connect(dupsContinueButton, 'onClick', function(){
                        	//bba.pageStandby.show();
                        	addressDuplicates.hide();
                        	bba.Client.checkEmail(vals);
                        });
                		
                		addressDuplicates.show();
                	} else {
                		bba.Client.checkEmail(vals);
                		//client_docLoa.submit(vals);
                	}
                },
                error: function(data) {
                	bba.showXhrError(data.xhr.responseText);
                }
        	});
        },
        
        checkEmail : function(vals)
        {
        	xhr.post({
                url: './client/check-email-duplicates',
                content: vals,
                handleAs: 'json',
                preventCache: true,
                load: function(data) {
                	if (data.dups) {
                		
                		console.log(data);
                		
                		dom.byId('dialog').innerHTML = data.html;
                        parser.parse('dialog');
                        
                        bba.setupDialog(emailDuplicates);
                        
                        connect.connect(dupsCloseButton, 'onClick', function(){
                        	clientPersFormStandby.hide();
                        	emailDuplicates.hide();
                        	
                        });
                        
                        connect.connect(dupsContinueButton, 'onClick', function(){
                        	//bba.pageStandby.show();
                        	emailDuplicates.hide();
                        	client_docLoa.submit(vals);
                        });
                		
                		emailDuplicates.show();
                	} else {
                		//bba.pageStandby.show();
                		client_docLoa.submit(vals);
                	}
                },
                error: function(data) {
                	bba.showXhrError(data.xhr.responseText);
                }
            });
        }
    };

    return bba.Client;

});
