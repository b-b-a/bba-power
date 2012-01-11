
require(
    ["dojo/dom", "dojo/ready", "dijit/layout/BorderContainer", "dijit/layout/ContentPane",
    "dijit/form/Form", "dijit/form/ValidationTextBox", "dijit/form/Button"],
    function(dom,ready){
        ready(function(){
            loader = dom.byId("loader");
            loader.style.display = "none";
            dom.byId('user_name').focus();
        });
    }
);
