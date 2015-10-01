/*
 * JScript Render - Animation
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

/* Animation class */
JScriptRender.jquery.Animation = new Function();

JScriptRender.jquery.Animation.prototype =
{
    slider: function(slide, settings) 
        {
        var set = settings || {};

        set.effect = set.effect || "fade";
        set.keys = set.keys || false;

        // Right button functionality
        slide.children(".toolbar").children(".button-right").click(function()
        {
            var cssHeight = slide.css("height");
            slide.css("height", cssHeight);
            setTimeout(function(){slide.css("height", "")},1000);

            var items = [];
            $.each(slide.children(".item"),function(){
               items.push($(this));
            });

            iters = items.length;
            for (var i = 0; i < iters; i++) {
                /* Detects if the current element is visible and if exists an next element. If there is,
                 * the visible element is hidden and the next element is displayed. */
                if (items[i].css("display") != "none" && (i+1) in items) {
                    items[i].hide(set.effect,{direction: "left"},400);
                    var nextElement = items[i+1];
                        nextElement.delay(400).show(set.effect,{direction: "right"},400);
                    break;
                }
                /* If the visible element is the last element, the first element is displayed */
                else if (items[i].css("display") != "none") {
                    items[i].hide(set.effect,{direction: "right"},400);
                        items[0].delay(400).show(set.effect,{direction: "left"},400);
                }
            }
        });

        // Left button functionality
        slide.children(".toolbar").children(".button-left").click(function()
        {
            var cssHeight = slide.css("height");
            slide.css("height", cssHeight);
            setTimeout(function(){slide.css("height", "")},1000);

            var items = [];
            $.each(slide.children(".item"),function(){
               items.push($(this));
            });

            iters = items.length;
            for (var i=0;i<iters;i++) {
                /* Detects if the current element is visible and if exists an before element. If there is,
                 * the visible element is hidden and the before element is displayed. */
                if (items[i].css("display") != "none" && (i-1) in items) {
                    items[i].hide(set.effect,{direction: "right"},400);
                    var nextElement = items[i-1];
                    nextElement.delay(400).show(set.effect,{direction: "left"},400);
                    break;
                }
                /* If the visible element is the first element, the last element is displayed */
                else if (items[i].css("display") != "none") {
                    items[i].hide(set.effect,{direction: "left"},400);
                    items[items.length-1].delay(400).show(set.effect,{direction: "right"},400);
                    break;      // Es necesario, puesto que se detecta el primer elemento y no el último
                }
            }
        });

        // Down button functionality
        slide.children(".button-down").click(function(){
            
            var slide = $(".slide");

            if (slide[0].style.height != "auto") {
                slide.css({"height": "auto"});
                $(this).children("span").removeClass("glyphicon-chevron-down");
                $(this).children("span").addClass("glyphicon-chevron-up");
            }
            else {
                slide.animate({height: "400px"},1300);
                $(this).children("span").removeClass("glyphicon-chevron-up");
                $(this).children("span").addClass("glyphicon-chevron-down");
            }
        });

        // Keys functionality
        if (set.keys) {
            $("body").keydown(function(event){
                var keycode = event.which;
                if (keycode == 37)
                    slide.children(".toolbar").children(".button-left").trigger("click");
                if (keycode == 39)
                    slide.children(".toolbar").children(".button-right").trigger("click");
            });
        }
    }
}