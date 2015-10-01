/*
 * JScript Render - Loader class
 * http://www.pleets.org
 *
 * Copyright 2014, Pleets Apps
 * Free to use under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
 */

/* JScriptRender alias */
if (!window.hasOwnProperty('JScriptRender'))
  JScriptRender = {};

/* Namespace */
if (!JScriptRender.hasOwnProperty('html'))
  JScriptRender.html = new Object();

/* Loader class */
JScriptRender.html.Loader = function(settings)
{
   var set = settings || {};

   // Binds an HTMLObject or jQuery selector
   set.context = (typeof set.context !== "undefined" && set.context !== null) ? set.context : null;
   set.height = set.height || 50;
   set.width = set.width || 100;

   // Settings
   this.context = set.context;
   this.height = set.height;
   this.width = set.width;

   this.selector = "JScriptRender-loader-" + Math.random().toFixed(9).substring(2);
   this.overlay = new JScriptRender.html.Overlay();
   this.interval = null;
   this.state = false;
}

JScriptRender.html.Loader.prototype = 
{
   isActive: function()
   {
     /* for some reason the object does not exist */
      if (document.querySelector("#" + this.selector) == null)
        return false;     
      return this.state;
   },
   show: function(callback)
   {
      var that = this;            // class reference
      var loader = document.querySelector("#" + this.selector);

      function renderLoader(that, context)
      {
         var isset_context = (context !== null && typeof context !== "undefined");

         var context = context;

         if (isset_context)
         {
            if (context.jquery)         // if the object was catched with jquery get the HTMLObject from the array
               context = context[0];
         }
         else
            context = document.querySelector('body');

         // callback
         callback = callback || new Function();

         // Create Canvas element
         /* Creates the canvas element and set the basic classes for it */  
         var newCanvas = document.createElement('canvas');
         newCanvas.setAttribute('id', that.selector);
         newCanvas.setAttribute('height', that.height);
         newCanvas.setAttribute('width', that.width);
         newCanvas.setAttribute('style', 'background: white; box-shadow: 0 0 2px black');

         if (!isset_context)
            newCanvas.setAttribute('class', 'base-loader base-loader-style');

         /* Appens canvas element to context */
         context.appendChild(newCanvas);

         var canvas = document.querySelector("#" + that.selector);

         if (!isset_context)
         {
            that.overlay.show();
            canvas.style.marginLeft = "-" + (newCanvas.width / 2) + "px";
            canvas.style.marginTop = "-" + (newCanvas.height / 2) + "px";
         }

         var x = 0, y = 2, speed = 5, direction = speed;
         var height = newCanvas.height - 4;

         var ctx = canvas.getContext("2d");
         ctx.fillStyle = 'rgb(0,150,200)';
         ctx.fillRect(x,y,10,height);
         ctx.fill();

         var interval = setInterval(function(){
             if (x > canvas.width)
             {
                 direction = -speed;
                 x = 2; y = 2;
                 canvas.width = canvas.width;
             }
             if (x < 0) direction = speed;
             x += direction;
             ctx.fillStyle = 'rgb(0,150,200)';
             ctx.fillRect(x,y,10,height);
             ctx.fill();
         }, 20);

         that.interval = interval;
      }

      // Frequency of requests
      if (this.isActive())
         return setTimeout(function(){ that.show(callback); }, 50);    // frecuency

      // Update state
      setTimeout(function() { that.state = true; }, 0);    // duration

      // Create loader
      if (loader === null)
      {
         if (this.context === null)
            return renderLoader(this);
         else
            return renderLoader(this, this.context);
      }
   },
   hide: function(callback)
   {
      var that = this;           // class reference
      var loader = document.querySelector("#" + this.selector);

      // callback
      callback = callback || new Function();

      // Frequency of requests
      if (!(this.isActive()))
         return setTimeout(function(){ that.hide(callback); }, 50);    // frecuency

      // Update state
      setTimeout(function() { that.state = false; }, 0);    // duration

      // Remove loader
      this.overlay.hide();
      loader.setAttribute('class', '$ ' + loader.getAttribute("class") + ' fade-out fast');

      setTimeout(function(){
         loader.parentNode.removeChild(loader); window.clearInterval(that.interval);
         that.interval = null;
         callback();
      }, 299);        // This time is equals to loader duration or slightly lower
   }
}
