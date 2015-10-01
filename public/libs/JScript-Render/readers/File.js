/*
 * JScript Render - File class
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
if (!JScriptRender.hasOwnProperty('reader'))
	JScriptRender.reader = new Object();

/* File class */
JScriptRender.reader.File = function(settings)
{
	var set = settings || {};
   set.size = set.size || 1048576;        // 1Mb

   /* Values: image, pdf, excel, word, javascript */
   set.fileBox = set.fileBox || "";
   set.dropBox = set.dropBox || "";
   set.preview = set.preview || "";
   set.url = set.url || "";

   set.fileFormat = set.fileFormat || "any";   		// set file format

   this.fileBox = set.fileBox;
   this.dropBox = set.dropBox;
   this.previewBox = set.preview;
   this.url = set.url;

   switch (set.fileFormat.toLowerCase()) {
      case "excel":
         var fileFormat = new JScriptRender.validator.FileFormat(/^(application\/vnd.openxmlformats-officedocument.spreadsheetml.sheet)$/i);
         break;
      case "javascript":
         var fileFormat = new JScriptRender.validator.FileFormat(/^(application\/javascript)$/i);
         break;
      case "any":
         var fileFormat = null;     // any format
         break;
      default:
         var fileFormat = false;    // unknown format
   }

   this.filter = fileFormat;
   this.size = set.size;
}

JScriptRender.reader.File.prototype =
{
   getFormat: function() {
      return this.filter;
   },
   getData: function(files)
   {
      var formData = new FormData();

      for (var i = files.length - 1; i >= 0; i--)
      {
      	// Test file format

         if (this.filter === false)
            throw ("Unknown format");

         if (this.filter != null) {
           if (!this.filter.isValid(files[i]))
               throw ('Invalid file format ' + files[i].type + ' of ' + files[i].name);      
         }

         // Test file size
         if (files[i].size > this.size)
            throw ("The file size of " + files[i].name + " is greater than " + (this.size/1024).toFixed(2) + "KiB");

         formData.append(i, files[i]);
         this.preview(files[i]);
      }

      return formData;
   },
   preview: function(file)
   {
   	var that = this;

      var fReader = new FileReader();

      fReader.onload = function(event)
      {
      	var item = document.createElement("div");
      	item.setAttribute("class", "item");

         var name = document.createElement('span');
         name.setAttribute('class', 'name');
         var nameText = "<span>Name: </span>" + file.name;
         name.innerHTML = nameText;

         var type = document.createElement('span');
         type.setAttribute('class', 'type');
         var typeText = "<span>Type: </span>" + file.type;
         type.innerHTML = typeText;

         var size = document.createElement('span');
         size.setAttribute('class', 'size');
         var sizeText = "<span>Size: </span>" + (file.size/1024).toFixed(2) + "KiB";
         size.innerHTML = sizeText;

         var view = document.createElement('div');
         view.setAttribute('class', 'file-item');
         view.appendChild(name);
         view.appendChild(document.createElement("br"));
         view.appendChild(type);
         view.appendChild(document.createElement("br"));
         view.appendChild(size);
         view.appendChild(document.createElement("br"));

         item.appendChild(view);
         if (that.previewBox.hasChildNodes())
         	that.previewBox.insertBefore(item, that.previewBox.firstChild);
         else
         	that.previewBox.appendChild(item);
      }

      fReader.readAsDataURL(file);
   },
   putFiles: function(formData) {
      for (var i = formData.length - 1; i >= 0; i--) {
         preview(formData[i]);
      };
   },
   upload: function(files, callback) 
   {
      var xhr = new XMLHttpRequest();
      xhr.open('POST', this.url);

      var that = this;

      var progress = document.createElement("progress");
      progress.setAttribute("id", "JScriptRender-file-progress");
      var progressBar = document.createElement("div");
      progressBar.setAttribute("class", "progress-bar");
      var span = document.createElement("span");
      progressBar.appendChild(span);
      progress.appendChild(progressBar);

      this.previewBox.appendChild(progress);

      callback = callback || new Function();

      xhr.onreadystatechange = function()
      {
         if (xhr.readyState == 4 && xhr.status == 200) 
         {
            //that.previewBox.innerHTML += xhr.responseText;
            document.querySelector("#JScriptRender-file-progress").parentNode.removeChild(document.querySelector("#JScriptRender-file-progress"));
            callback(xhr.responseText);
         }
         console.info(xhr.readyState + " - " + xhr.status);
      }

      progress.value = 0;

      xhr.upload.onprogress = function(event)
      {
         progress.value = (event.loaded / event.total * 100);
      };

      xhr.send(files);
   },
   addDropEvent: function(dropfunction)
   {
      var that = this;

      this.dropBox.addEventListener("drop", function(event)
      {
         event.preventDefault();
         if (typeof dropfunction != "undefined" && typeof dropfunction == "function")
            dropfunction(that.getData(event.dataTransfer.files));
         else
            that.putFiles(that.getData(event.dataTransfer.files));
         return false;
      }, false);

      this.dropBox.addEventListener("dragover", function(event)
      {
         event.preventDefault();
         return false;
      }, false);

      this.dropBox.addEventListener("dragleave", function(event)
      {
         return false;
      }, false);
   },
   addChangeEvent: function(dropfunction)
   {
      var that = this;

      this.fileBox.addEventListener("change", function(event)
      {
         event.preventDefault();

         try {
            var files = that.getData(this.files);

            if (typeof dropfunction != "undefined" && typeof dropfunction == "function")
               dropfunction(files);
            else
               that.putFiles(files);
         }
         catch(e)
         {
            var exception = new $j.exception.Exception(e);
            exception.print();
         }

         return false;
      }, false);

   }
}
