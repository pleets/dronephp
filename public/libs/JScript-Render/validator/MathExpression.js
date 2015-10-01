/*
 * JScript Render - MathExpression class
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

/* MathExpression class */
JScriptRender.validator.MathExpression = function(mode) 
{
   this.messages = {};

   if (['unaryRegEx','multipleRegEx', 'simpleAssociativeRegEx', 'associativeRegEx'].indexOf(mode) == -1 && typeof mode !== "undefined")
      throw "Invalid math expression mode";
   else if (typeof mode == "undefined")
      mode = "unaryRegEx";

   /*
    * Unary expression
   */

   // Examples: 5, 1.25, -2.28, √2, -√2, -√5.24, -5√2
   var unaryRegEx = /^[\+\-]?([0-9]+([.][0-9]+)?)?[√]?[0-9]+([.][0-9]+)?$/;

   /*
    * Multiple expression
   */

   // Examples: 5 + 8, 1.25 - 2.28, -√16*9, √2.64 + 8*9 + 10
   var multipleRegEx = /^[\+\-]?([0-9]+([.][0-9]+)?)?[√]?[0-9]+([.][0-9]+)?([\s]*[\+\-/*%^][\s]*[√]?[0-9]+([.][0-9]+)?)*$/;

   var multiple = "" + multipleRegEx + "";      // parsing to string
   var inMultipleRegEx = multiple.substring(2, multiple.length - 2);    // get expression without start and end caracters

   /*
    * Base associative expression
   */

   // Examples: (16+2), (√16+2), (√16+2+8*9)
   var multipleWithBrackets = "[\(][\\s]*" + inMultipleRegEx + "[\\s]*[\)]";

   /* multiple with or without brackets */
   // Examples: (√16+2), √16+2
   var baseAssociative = "(" + multipleWithBrackets + "|" + inMultipleRegEx + ")";

   /*
    * Simple associative expression
   */

   /* Example: 2^(√16+2)+8+(5+4) */
   simpleAssociativeRegEx = "^[√]?" + baseAssociative + "([\\s]*[\+\-/*%^][\\s]*[√]?" + baseAssociative + "[\\s]*)*$";

   /*
    * Associative expression
   */

   // Not supported
   associativeRegEx = multipleRegEx;

   JScriptRender.validator.MathExpression.prototype.unaryRegEx = unaryRegEx;
   JScriptRender.validator.MathExpression.prototype.multipleRegEx = multipleRegEx;
   JScriptRender.validator.MathExpression.prototype.simpleAssociativeRegEx = simpleAssociativeRegEx;
   JScriptRender.validator.MathExpression.prototype.associativeRegEx = associativeRegEx;

   JScriptRender.validator.MathExpression.prototype.mode = mode;

   /* Get language */
   var language = JScriptRender.settings.general.language;
   this.languageHelper = JScriptRender.language[language];   
}

JScriptRender.validator.MathExpression.prototype =
{    
   isValid: function(string)
   {
      // Remove whitespaces
      string = string.trim();

      if (string.length && !(string.match(this[this.mode])))
      {
         this.messages.malformedMathExpression = this.languageHelper.malformedMathExpression;
         return false;
      }

      return true;
   },
   getMessages: function()
   {
      return this.messages;
   },
   getMode: function()
   {
      return this.mode;
   },
   setMode: function(mode)
   {
      if (['unaryRegEx','multipleRegEx', 'simpleAssociativeRegEx', 'associativeRegEx'].indexOf(mode) == -1 && typeof mode !== "undefined")
         throw "Invalid math expression mode";
      else if (typeof mode == "undefined")
         this.mode = "multipleRegEx";
      else
         this.mode = mode;
   }
}
