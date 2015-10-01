/*
 * JScript Render - Overlay class
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

/* Overlay class */
JScriptRender.html.Overlay = function(settings, callback)
{
   this.selector = "JScriptRender-overlay-"+Math.random().toFixed(9).substring(2);
   this.state = false;
}

JScriptRender.html.Overlay.prototype =
{
   show: function()
   {
      var that = this;        // class reference

      // Frequency of requests
      /*  When an overlay is actived the propertie state is passed to true. Any request can be processed
         until that state changes to false. So, if overlay is executed when other instance is in process
         an anonymus function with show() function is executed again in the specified time (frecuency).
      */
      if (this.state)
         return setTimeout(function(){ that.show(); }, 50);     // frecuency

      // Overlay duration
      /*  When an overlay function is executed this lasts for less the time especified. */ 
      setTimeout(function() { that.state = true; }, 300);     // duration

      var overlay = document.querySelector("#" + this.selector);

      if (overlay == null)     // if overlay not exists
      {
         /* Secure style for overlay on 100% */
         if (document.querySelector("html").style.height != "100%")
            document.querySelector("html").style.height = "100%";

         // Creates Overlay
         /*  Creates the overlay element and appends it to the body
             << $ fade-in >> class is a part of CSS Render core. It creates a fade effect.
         */
         var overlay = document.createElement('div');
         overlay.setAttribute('id', this.selector);

         /* << $ fade-in >> class is a part of CSS Render. It creates a fade effect. */         
         overlay.setAttribute('class', '$ fade-in fast overlay');

         document.body.appendChild(overlay);

         return true;
      }
      return false;
   },
   hide: function()
   {
      var that = this;

      var overlay = document.querySelector("#" + this.selector);

      if (overlay !== null)     // if overlay exists it's removed
      {
         if (!(this.state))
            return setTimeout(function(){ that.hide(); }, 50);     // frecuency

         /* << $ fade-out >> class is a part of CSS Render. It creates a fade effect. */
         overlay.setAttribute('class', '$ fade-out fast overlay');

         setTimeout(function(){
            overlay.parentNode.removeChild(overlay);
         }, 299);      // This time is equals to overlay duration or slightly lower
           
         setTimeout(function() { that.state = false; }, 300);     // duration

         return true;
      }
      return false;
   },
}
