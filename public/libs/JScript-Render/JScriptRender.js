/*
 * JScript Render - Javascript renderization tools
 * http://www.pleets.org
 * Copyright 2014, Pleets Apps
 * Free to use under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
 *
 * Date: 2015-01-22
 */

/* JScriptRender alias */
if (!window.hasOwnProperty('JScriptRender'))
    JScriptRender = {};

/* relative path to the element whose script is currently being processed.*/
if (typeof document.currentScript != "undefined" && document.currentScript != null)
{
    var str = document.currentScript.src;
    JScriptRender.PATH = (str.lastIndexOf("/") == -1) ? "." : str.substring(0, str.lastIndexOf("/"));
}
else {
    /* alternative method to get the currentScript (older browsers) */
        // ...
    /* else get the URL path */
    JScriptRender.PATH = 'Framework/public/libs/JScript-Render';
}

JScriptRender.STATE = 'loading';

/* Standard class */
JScriptRender.StdClass = 
{
    include: function(url, ajax, callback) 
    {
        callback = callback || new Function();

        url = JScriptRender.PATH + '/' + url;

        if (typeof ajax == "undefined" || ajax == false)
        {
            var script = document.createElement("script");
            script.src = url;
            script.type = 'text/javascript';

            script.id = 'JScriptRender-module';

            /* IE */
            if (script.readyState)
            {
                script.onreadystatechange = function() 
                {
                    if (this.readyState == 'complete') {
                        var scriptTag = document.querySelector('#' + script.id);
                        scriptTag.parentNode.removeChild(scriptTag);
                        callback();
                    }
                }                
            }
            /* Others */
            else {
                script.onload = function() {
                    var scriptTag = document.querySelector('#' + script.id);
                    scriptTag.parentNode.removeChild(scriptTag);
                    callback();
                }
            }

            var head = document.querySelector('head');
            head.appendChild(script);
        }
        else {
            var xhr = new XMLHttpRequest();
            // To prevent 412 (Precondition Failed) use GET method instead of POST
            // Set async to false to can use xhr.status after xhr.send()
            xhr.open("GET", url, false);

            xhr.onreadystatechange = function()
            {
                if (xhr.readyState == 4 && xhr.status == 200)
                    eval(xhr.responseText);
                if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 404)) {
                    callback();   
                }
            }

            xhr.send();

            if (xhr.status == 404)
                return false;
        }
        return true;
    },
    require: function(url, callback)
    {
        if (!this.include(url, true, callback))
            alert('The resource ' + url + ' probably does not exists');
    },
    array_include: function(urlArray, callback)
    {
        var that = this;
        var resource = urlArray[0];

        callback = callback || new Function();

        if (urlArray.length > 0)
            this.include(resource, false, function(){
                urlArray = urlArray.splice(1, urlArray.length);
                that.array_include(urlArray, callback);
            });
        else
            callback();
    },
    ready: function(handler)
    {
        handler = handler || new Function();

        var libReady = function(handler) 
        {
            setTimeout(function(){
                if (JScriptRender.STATE == "complete")
                    handler();
                else
                    return libReady(handler);
            }, 100);
        }

        if (document.readyState == "complete")
            libReady(handler);
        else {
            document.onreadystatechange = function () {
                if (document.readyState == "complete") {
                    libReady(handler);
                }
            }
        }
    }
}

/* Short alias */
var $jS = JScriptRender;
for (var f in $jS.StdClass) {
    $jS[f] = $jS.StdClass[f];
};

/* Load classes */
try {
    $jS.array_include([

        // Languages
        'language/en_US.js',
        'language/es_ES.js',

        // General settings
        'settings/general.js',

        // Validators
        'validator/MathExpression.js',
        'validator/StringLength.js',
        'validator/Digits.js',
        'validator/Alnum.js',
        'validator/Date.js',
        'validator/FileFormat.js',

        // Filters
        'filter/InputFilter.js',

        // Html
        'html/Overlay.js',
        'html/Loader.js',
        'html/Dialog.js',
        'html/Form.js',
        'html/FormValidator.js',

        // Exceptions
        'exception/Exception.js',

        // Readers
        'readers/File.js',

        // jQuery utils
        'jquery/Ajax.js',
        'jquery/UI.js',
        'jquery/Debug.js',
        'jquery/Animation.js',

        // Utils
        'utils/DateControl.js',

    ], function(){
        JScriptRender.STATE = 'complete';
    });    
}
catch (e) {
    JScriptRender.STATE = 'error';
}