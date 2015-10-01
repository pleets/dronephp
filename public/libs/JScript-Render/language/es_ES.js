/*
 * JScript Render - es_ES language
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

JScriptRender.language.es_ES = 
{
   months: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],

   /* Standard validation classes */

   // Date
   dateInvalidDate: "La entrada no parece ser una fecha válidaa",
   dateFalseFormat: "La entrada no se ajusta al formato de fecha 'Y-m-d'",

   // StringLength
   stringLengthTooShort: function(min){ return "La entrada contiene menos de " + min + " caracteres" },
   stringLengthTooLong: function(max){ return "La entrada contiene más de " + max + " caracteres" },

   // Digits
   notDigits: "La entrada debe contener sólo dígitos",

   // Alnum
   notAlnum: "La entrada contiene caracteres que no son alfabéticos o dígitos",

   // FileFormat
   invalidFileFormat: function(format) { return "El formato del archivo es inválido!, el formato actual es " + format },

   // MathExpression
   malformedMathExpression: "Expresión mal formada"   
}
