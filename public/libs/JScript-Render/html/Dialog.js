/*
 * JScript Render - Dialog class
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

/* Form class */
JScriptRender.html.Dialog = function(settings, callback)
{
   // Settings object
   var set = settings || {};

   // Window attributes
   set.id = set.id || "";
   set.class = set.class || "";
   set.title = set.title || "";

   // Window styles
   set.style = (set.style instanceof Object) ? set.style : {};

   // Positioning
   set.width = set.width || "auto";
   set.height = set.height || "auto";

   set.content = set.content || "";

   // Callback
   callback = callback || new Function();

   // Don't create more popups therewith identifiers
   if (set.id !== "")
   {
      var popup = document.querySelector('#' + set.id);
      if (popup !== null)
      {
         if (popup)
            return false;
      }
   }

   // container
   var container = document.createElement('div');
   if (set.id !== "")
      container.setAttribute('id', set.id);
   container.setAttribute('class', '$ wrapper base-fix-center ' + set.class);      // Fixed position

   container.style.zIndex = 100;
   container.style.width = set.width;
   container.style.height = set.height;

   if (set.style.top) container.style.top = set.style.top + 'px';
   if (set.style.left) container.style.left = set.style.left + 'px';

   // contents
   var contents = document.createElement('div');
   contents.setAttribute('class', 'contents');

   // header
   var header = document.createElement('div');
   header.setAttribute('class', 'header');

   // main
   var main = document.createElement('div');
   main.setAttribute('class', 'main');

   // Text nodes
   var text = document.createTextNode(set.title);
   header.appendChild(text);

   main.innerHTML = set.content;

   // close button
   var closeButton = document.createElement('button');
   var closeText = document.createTextNode('×');
   closeButton.appendChild(closeText);

   // close listeners
   closeButton.addEventListener('click', function(event){
      container.parentNode.removeChild(container);
      event.stopPropagation();
   });

   closeButton.addEventListener('mousedown', function(event){
      event.stopPropagation();
   });

   // draggable
   var x, y;

   function move(event) {
   // For absotulte position
   //container.style.top = event.pageY - y + 'px';
   //container.style.left = event.pageX - x + 'px';
   container.style.top = event.clientY - y + 'px';
   container.style.left = event.clientX - x + 'px';
   }

   header.addEventListener('mousedown', function(event){
      // For absotulte position
      //y = event.pageY - container.offsetTop;
      //x = event.pageX - container.offsetLeft;
      y = event.clientY - container.offsetTop;
      x = event.clientX - container.offsetLeft;
      document.body.addEventListener('mousemove', move);
      container.style.opacity = 0.7;
   });

   header.addEventListener('mouseup', function(event){
      document.body.removeEventListener('mousemove', move);
      container.style.opacity = 1;
   });

   header.appendChild(closeButton);

   contents.appendChild(header);
   contents.appendChild(main);
   container.appendChild(contents);

   callback();

   // Dialog Node
   JScriptRender.html.Dialog.prototype.dialog = container;
}

JScriptRender.html.Dialog.prototype = 
{
   show: function()
   {
       document.body.appendChild(this.dialog);
   }
}
