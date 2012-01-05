/*
 * StackContainer.js.js
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
 * @package    JavaScript
 * @subpackage StackContainer
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

define("bba/StackContainer", ["dojo", "dijit"], function(dojo, dijit){

dojo.extend(
    dijit.layout.StackContainer,
    {
        prevTab : null,

        removeChild: function(/*dijit._Widget*/ page){
            // Overrides _Container.removeChild() to do layout and publish events

            //this.inherited(arguments);
            dijit.layout._LayoutWidget.prototype.removeChild.apply(this, arguments);

            if(this._started){
                // this will notify any tablists to remove a button; do this first because it may affect sizing
                dojo.publish(this.id + "-removeChild", [page]);
            }

            // If we are being destroyed than don't run the code below (to select another page), because we are deleting
            // every page one by one
            if(this._beingDestroyed){ return; }

            // Select new page to display, also updating TabController to show the respective tab.
            // Do this before layout call because it can affect the height of the TabController.
            if(this.selectedChildWidget === page){
                this.selectedChildWidget = undefined;
                if(this._started){
                    var children = this.getChildren();
                    if(children.length){
                        var i = this.getIndexOfChild(this.prevTab);
                        if (i > 0) {
                            this.selectChild(children[i]);
                        } else {
                            this.selectChild(children[0]);
                        }
                    }
                }
            }

            if(this._started){
                // In case the tab titles now take up one line instead of two lines
                // (note though that ScrollingTabController never overflows to multiple lines),
                // or the height has changed slightly because of addition/removal of tab which close icon
                this.layout();
            }
        }
    }
);

});

