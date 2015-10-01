/*
 * JScript Render - UI
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
JScriptRender.jquery.UI = new Function();

JScriptRender.jquery.UI.prototype = 
{
    overlayState: false,
    loaderState: false,
    intervals: {},
    overlay: function()
    {
        var that = this;

        if (this.overlayState)
            return setTimeout(function(){ that.overlay(); }, 500);

        this.overlayState = true;

        var overlaySelector = "jrender-overlay";
        var overlay = $("#" + overlaySelector);

        if (overlay.length)
        {
            overlay.effect("fade", 500);
            setTimeout(function(){
                if (overlay.length)
                    overlay.remove();
            }, 500);
        }
        else
        {
            if (document.querySelector("html").style.height != "100%")
                $("html").css("height", "100%");

            $("body").append("<div id='" + overlaySelector + "' class='overlay'></div>");
            $("#" + overlaySelector).effect("fade", 500);
        }
        setTimeout(function() { that.overlayState = false; }, 500);
    },    
    loader: function(settings)
    {
        var that = this;            // save a class reference

        var set = settings || {};
        set.context = (set.context !== undefined && set.context.length) ? set.context : false;      // jQuery selector
        set.height = set.height || 50;

        var loaderSelector = "jrenderLoader";
        var loader = $("#" + loaderSelector);

        function renderLoader(that, context)
        {
            loader_ctx = (context !== undefined && context.length);

            if (loader_ctx)
            {
                context = set.context;
                context.addClass("context");
            }
            else
                context = $("body");

            var newCanvas = $("<canvas id='" + loaderSelector + "' height='" + set.height + "' style='display: none; background: white' class='base-loader base-loader-style'>Loading...</canvas>");
            context.append(newCanvas);

            canvas = $("#" + loaderSelector);

            if (!loader_ctx)
            {
                that.overlay();
                canvas.css("margin-left", "-" + (newCanvas[0].width / 2) + "px");
                canvas.css("margin-top", "-" + (newCanvas[0].height / 2) + "px");                    
            }

            $(canvas).effect("fade", 500);

            var x = 0, y = 2, speed = 5, direction = speed;
            var height = newCanvas[0].height - 4;

            ctx = canvas[0].getContext("2d");
            ctx.fillStyle = 'rgb(0,150,200)';
            ctx.fillRect(x,y,10,height);
            ctx.fill();

            interval = setInterval(function(){
                if (x > canvas[0].width) 
                {
                    direction = -speed;
                    x = 2; y = 2;
                    canvas[0].width = canvas[0].width;
                }
                if (x < 0) direction = speed;
                x += direction;
                ctx.fillStyle = 'rgb(0,150,200)';
                ctx.fillRect(x,y,10,height);
                ctx.fill();
            },20);

            that.intervals.loader = interval;
        }

        if (!set.context)
        {
            // If loader is locked return again the function after 0.5 seconds
            if (this.loaderState)
                return setTimeout(function(){ that.loader(); }, 500);

            // Block loader for 0.5 seconds
            this.loaderState = true;
            setTimeout(function() { that.loaderState = false; }, 500);

            if (!loader.length)
                return renderLoader(this);

            // Close loader
            loader.effect("fade", 500); this.overlay();
            setTimeout(function(){ loader.remove(); window.clearInterval(that.intervals.loader); }, 500);
        }
        else
        {
            if (!set.context.children("canvas").length)
                return renderLoader(this, set.context);

            var loader = set.context.children("canvas");
            loader.effect("fade", 500);
            setTimeout(function(){loader.remove(); }, 1000);
        }
    },    
    dialog: function(settings, callback)
    {
        var set = settings || {};
        set.id = set.id || "";                  // String type           
        set.title = set.title || "";            // String type
        set.content = set.content || "";        // jQuery element

        set.persistence = (set.persistence !== undefined) ? set.persistence : true;

        // Callbacks
        callback = callback || function(){};                        // General callback

        // jQuery dialog settings
        set.overlay = (set.overlay !== undefined) ? set.overlay : true;
        set.width = set.width || "auto";
        set.height = set.height || "auto";
        set.autoOpen = (set.autoOpen !== undefined) ? set.autoOpen : true;
        set.position = set.position || "center";
        set.draggable = (set.draggable !== undefined) ? set.draggable : true;
        set.resizable = (set.resizable !== undefined) ? set.resizable : true;

        set.buttons = set.buttons || true;

        if ($("#"+set.id).length && set.persistence)
            $("#"+set.id).dialog("open");
        else
        {
            if ($("#"+set.id).length)
                $("#"+set.id).dialog("destroy");

            var dialog = $("<div></div>");
            dialog.attr("id", set.id);
            dialog.attr("title", set.title);
            dialog.attr("class", "$ fade-in");
            if (set.content.length)
                dialog.append(set.content);

            dialog.dialog({
                overlay: set.overlay,
                width: set.width,
                height: set.height,
                autoOpen: set.autoOpen,
                position: set.position,
                draggable: set.draggable,
                resizable: set.resizable,
                dialogClass: "css-render-nm black-shadow infinite",
                show: {
                    effect: "fade",
                    duration: 500,
                },
                hide: {
                    effect: "fade",
                    duration: 500,
                },
                buttons: set.buttons
            });
        }
        callback();
    },
    checked: function(inputSelection, className)
    {
        $("body").delegate(inputSelection, "change", function()
        {
                var row = $(this).parent().parent();
                if ($(this).is(":checked")) {
                    if (!row.hasClass(className))
                        row.attr("class", className);
                }
                else
                    row.removeAttr("class");
        });
    },
    checkedTrigger: function(inputTrigger, inputSelection, className)
    {
        $("body").delegate(inputTrigger, "change", function(event)
        {
            var selection = $(inputSelection);

            if ($(inputTrigger).is(':checked')) {
                for (var i = selection.length - 1; i >= 0; i--) {
                    if (!$(selection[i]).is(":checked"))
                        $(selection[i]).trigger("click");
                };
            }
            else {
                for (var i = selection.length - 1; i >= 0; i--) {
                    if ($(selection[i]).is(":checked"))
                        $(selection[i]).trigger("click");
                };
            }
        });            
    }
}