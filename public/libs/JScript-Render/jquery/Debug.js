/*
 * JScript Render - Debug
 * http://www.pleets.org
 *
 * Copyright 2014, Pleets Apps
 * Free to use under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
 *
 * Depends:
 *  [lib] jQuery
 */

/* JScriptRender alias */
if (!window.hasOwnProperty('JScriptRender'))
   JScriptRender = {};

/* Namespace */
if (!JScriptRender.hasOwnProperty('jquery'))
   JScriptRender.jquery = new Object();

/* UI class */
JScriptRender.jquery.Debug = function() 
{
   // API's
   JScriptRender.jquery.Debug.UI = new $jS.jquery.UI();
};

JScriptRender.jquery.Debug.prototype = 
{
    ajaxError: function(jqXHR, textStatus, error) 
    {
        errorContent = $("<div><h3>Error</h3></div>");
        errorContent.append("<p>Type: <strong>" + textStatus +"</strong></p>");
        errorContent.append("status: " + jqXHR.status +"<br />");
        errorContent.append("statusText: " + jqXHR.statusText +"<br />");
        JScriptRender.jquery.Debug.UI.dialog({ title: "An error ocurred!", content: errorContent, width: 300 });
    },
    error: function(e, settings) 
    {
        var set = settings || {};
        set.suggestion = set.suggestion || "";

        // Dialog parameters
        set.width = set.width || 300;

        errorContent = $("<div><h3>Error</h3></div>");
        errorContent.append("<p>Type: <strong>" + e.name +"</strong></p>");
        errorContent.append("Message: <em>" + e.message +"</em><br />");
        if (set.suggestion.length)
            errorContent.append("Suggestion: <em>" + set.suggestion +"</em><br />");
        JScriptRender.jquery.Debug.UI.dialog({ title: "An error ocurred!", content: errorContent, width: set.width });
    },
    phpError: function(phpCode, settings)
    {
        var set = settings || {};
        set.suggestion = set.suggestion || "";

        // Dialog parameters
        set.width = set.width || "auto";

        errorContent = $("<div><h3>Error</h3></div>");
        errorContent.append(phpCode);
        if (set.suggestion.length)
            errorContent.append("Suggestion: <em>" + set.suggestion +"</em><br />");
        JScriptRender.jquery.Debug.UI.dialog({ title: "An error ocurred!", content: errorContent, width: set.width });
    },
    exception: function(name, message) {
        this.name = name;
        this.message = message;
        return this;            
    }
}