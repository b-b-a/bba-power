/*
 * ContentPane.js
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
 * @subpackage ContentPane
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

dojo.extend(
    dijit.layout.ContentPane,
    {
        _load: function(){
            // summary:
            //		Load/reload the href specified in this.href

            // display loading message
            this._setContent(this.onDownloadStart(), true);

            var self = this;
            var getArgs = {
                preventCache: (this.preventCache || this.refreshOnShow),
                url: this.href,
                handleAs: "text"
            };
            if(dojo.isObject(this.ioArgs)){
                dojo.mixin(getArgs, this.ioArgs);
            }

            var hand = (this._xhrDfd = (this.ioMethod || dojo.xhrPost)(getArgs));

            hand.addCallback(function(html){
                try{
                    self._isDownloaded = true;
                    self._setContent(html, false);
                    self.onDownloadEnd();
                }catch(err){
                    self._onError('Content', err); // onContentError
                }
                delete self._xhrDfd;
                return html;
            });

            hand.addErrback(function(err){
                if(!hand.canceled){
                    // show error message in the pane
                    self._onError('Download', err); // onDownloadError
                }
                delete self._xhrDfd;
                return err;
            });

            // Remove flag saying that a load is needed
            delete this._hrefChanged;
        }
    }
);

