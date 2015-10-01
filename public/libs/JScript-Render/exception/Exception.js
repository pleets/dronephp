/*
 * JScript Render - Exception
 * http://www.pleets.org
 *
 * Copyright 2014, Pleets Apps
 * Free to use under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
 *
 * Depends:
 *  [class] JScriptRender.html.Html
 */

/* JScriptRender alias */
if (!window.hasOwnProperty('JScriptRender'))
   JScriptRender = {};

/* Namespace */
if (!JScriptRender.hasOwnProperty('exception'))
   JScriptRender.exception = new Object();

/* Exception class */
JScriptRender.exception.Exception = function(name, message) 
{
    JScriptRender.exception.Exception.prototype.name = name;
    JScriptRender.exception.Exception.prototype.message = message;
};

JScriptRender.exception.Exception.prototype = 
{
    print: function(e, settings) 
    {
        var set = settings || {};

        if (typeof e == "undefined") 
        {
            if (typeof this.message == "undefined")
                e = {
                    name: "Exception",
                    message: this.name,
                }
            else
                e = {
                    name: this.name,
                    message: this.message,
                }
        }

        // Dialog parameters
        set.width = set.width || 300;

        var d = new $j.html.Dialog({ title: e.name, content: e.message, width: set.width });
        d.show();
    },
    parse: function(name, message)
    {
        this.name = name;
        this.message = message;
        return this;            
    }
}
