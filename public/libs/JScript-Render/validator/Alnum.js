/*
 * JScript Render - Alnum Class
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

/* Alnum class */
JScriptRender.validator.Alnum = function() {

   /* Get language */
   var language = JScriptRender.settings.general.language;
   this.languageHelper = JScriptRender.language[language];
};

JScriptRender.validator.Alnum.prototype = 
{
   Messages: {},

   isValid: function(string)
   {
      this.Messages = {};

      // Remove whitespaces
      string = string.trim();

      // EN, ES
      var RegExpr = /^(\d|[a-zA-Z]|\s|[áÁéÉíÍóÓúÚäÄëËïÏöÖüÜñÑ])*$/;

      if (!(string.match(RegExpr)))
      {
         this.Messages.notAlnum = this.languageHelper.notAlnum;
         return false;
      }

      return true;
   },
   getMessages: function()
   {
      return this.Messages;
   }
}
