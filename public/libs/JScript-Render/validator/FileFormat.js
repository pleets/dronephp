/*
 * JScript Render - FileFormat class
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
if (!JScriptRender.hasOwnProperty('validator'))
   JScriptRender.validator = new Object();

/* FileFormat class */
JScriptRender.validator.FileFormat = function(format)
{
   JScriptRender.validator.FileFormat.prototype.format = format;

   this.messages = {};

   /* Get language */
   var language = JScriptRender.settings.general.language;
   this.languageHelper = JScriptRender.language[language];   
}

JScriptRender.validator.FileFormat.prototype =
{
   isValid: function(file) 
   {
      this.messages = {};

      if (!this.format.test(file.type))
      {
         this.messages.invalidFileFormat = this.languageHelper.invalidFileFormat;
         return false;
      }

      return true;
   },
   getMessages: function()
   {
      return this.messages;
   }
}
