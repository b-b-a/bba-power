
require(
    ["dojo/dom", "dojo/ready", "bba/Core", "dijit/layout/BorderContainer", "dijit/layout/ContentPane",
    "dijit/form/Form", "dijit/form/ValidationTextBox", "dijit/form/Button"],
    function(dom,ready){
        ready(function(){
            loader = dom.byId("loader");
            loader.style.display = "none";

            login._onKey = function(){};
            login.show();

            dom.byId('user_name').focus();
        });
    }
);
