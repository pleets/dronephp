/*
 * JScript Render - Digits class
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

/* Digits class */
JScriptRender.validator.Digits = function() {

   this.messages = {};

   /* Get language */
   var language = JScriptRender.settings.general.language;
   this.languageHelper = JScriptRender.language[language];
};

JScriptRender.validator.Digits.prototype =
{
   isValid: function(string)
   {
      // Remove whitespaces
      string = string.trim();

      var RegExpr = /^\d*$/;

      if (!(string.match(RegExpr)))
      {
         this.messages.notDigits = this.languageHelper.notDigits;
         return false;
      }

      return true;
   },
   getMessages: function()
   {
      return this.messages;
   }     
}
