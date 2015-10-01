/*
 * JScript Render - StringLength class
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

/* StringLength class */
JScriptRender.validator.StringLength = function(settings)
{
   this.messages = {};

   var set = settings || {};


   if (typeof set.min !== "number")
      throw ("The minimun value must be number type");
   if (typeof set.max !== "number" && typeof set.max !== "undefined")
      throw ("The maximum value must be number type");

   // Get a natural number as parameter or set the default parameters to zero 
   set.min = (set.min >= 0) ? set.min : 0;

   // Get a natural number as parameter grater tha the minumun value or set the maximum value to undefined
   set.max = (set.max > set.min) ? set.max : undefined;

   this.min = set.min;
   this.max = set.max;

   /* Get language */
   var language = JScriptRender.settings.general.language;
   this.languageHelper = JScriptRender.language[language];
}

JScriptRender.validator.StringLength.prototype =
{
   isValid: function(string, whitespaces) 
   {
      // Allow whitespaces
      if (typeof whitespaces !== "boolean" && typeof whitespaces !== "undefined")
         throw "whitespaces must be boolean type!";
      whitespaces = (typeof whitespaces === "undefined") ? false: whitespaces;

      if (typeof string !== "string")
         throw "The first argument must be string type!";
      if (typeof whitespaces !== "boolean")
         throw "The second argument must be boolean type!";

      this.messages = {};

      if (whitespaces)
      {
         if (typeof this.max !== "undefined")
         {
            if (string.length < this.min)
            {
               this.messages.stringLengthTooShort = this.languageHelper.stringLengthTooShort(this.min);
               return false;
            }
            else if (string.length > this.max)
            {
               this.messages.stringLengthTooLong = this.languageHelper.stringLengthTooLong(this.max);
               return false;
            }
         }
         else {
            if (string.length < this.min)
            {
               this.messages.stringLengthTooShort = this.languageHelper.stringLengthTooShort(this.min);
               return false;   
            }
         }
      }
      else {
         if (typeof this.max !== "undefined")
         {
            if (string.trim().length < this.min)
            {
              this.messages.stringLengthTooShort = this.languageHelper.stringLengthTooShort(this.min);
              return false;
            }
            else if (string.trim().length > this.max)
            {
              this.messages.stringLengthTooLong = this.languageHelper.stringLengthTooLong(this.max);
              return false;
            }
         }
         else {
            if (string.trim().length < this.min)
            {
              this.messages.stringLengthTooShort = this.languageHelper.stringLengthTooShort(this.min);
              return false;
            }
         }
      }
      return true;
   },
   getMessages: function()
   {
      return this.messages;
   }
}
