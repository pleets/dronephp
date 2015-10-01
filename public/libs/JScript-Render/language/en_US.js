/*
 * JScript Render - en_US language
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
if (!JScriptRender.hasOwnProperty('language'))
   JScriptRender.language = new Object();

JScriptRender.language.en_US = 
{
   months: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],

   /* Standard validation classes */

   // Date
   dateInvalidDate: "The input does not appear to be a valid date",
   dateFalseFormat: "The input does not fit the date format 'Y-m-d'",

   // StringLength
   stringLengthTooShort: function(min){ return "The input is less than " + min + " characters long" },
   stringLengthTooLong: function(max){ return "The input is more than " + max + " characters long" },

   // Digits
   notDigits: "The input must contain only digits",

   // Alnum
   notAlnum: "The input contains characters which are non alphabetic and no digits",

   // FileFormat
   invalidFileFormat: function(format) { return "The file format is invalid!, the current file format is " + format },

   // MathExpression
   malformedMathExpression: "Malformed expression"
}
