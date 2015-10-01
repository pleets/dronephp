/*
 * JScript Render - Input filter class
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
if (!JScriptRender.hasOwnProperty('filter'))
  JScriptRender.filter = new Object();

/* InputFilter class */
JScriptRender.filter.InputFilter = function (form)
{
   this.scope = (typeof form !== "undefined") ? form : "";
   this.filters = {};

   this.messages = {};

   // API's
   this.validator = JScriptRender.validator;
}

JScriptRender.filter.InputFilter.prototype = 
{
   getMessages: function()
   {
      return this.messages;
   },
   add: function(inputFilter)
   {
      if (!inputFilter.hasOwnProperty('name'))
         throw "The element must be named";

      if (typeof inputFilter !== "object")
         throw "The argument must be object type";
      this.filters[inputFilter.name] = inputFilter;
   },
   count: function()
   {
      return this.inputFilter.length;
   },
   get: function(name)
   {
      if (typeof name !== "string")
         throw "The argument must be string type";

      for (var input in this.filters)
      {
         if (name == this.filters[input].name)
            return document.querySelector(this.scope + ' [name=\'' + this.filters[input].name + '\']');
      }
      throw "<input name='" + name + "' /> not found!";
   },
   getInputs: function()
   {
      var inputs = [];
      for (var input in this.filters)
      {
         inputs.push(document.querySelector(this.scope + ' [name=\'' + this.filters[input].name + '\']'));
      }
      return inputs;
   },
   getInvalidInput: function()
   {
      var inputs = [];
      for (var input in this.filters)
      {
         inputElement = document.querySelector(this.scope + ' [name=\'' + this.filters[input].name + '\']');
         for (var validator in this.filters[input].validators)
         {
            if (typeof this.validator[validator] === "undefined")
               throw "InputFilter.getInvalidInput: Undefined validator " + validator;
            var validatorInstance = new this.validator[validator](this.filters[input].validators[validator]);
            if (inputElement == null)
               throw 'Input not Found: ' + this.scope + ' [name=\'' + this.filters[input].name + '\']';
            else if (!validatorInstance.isValid(inputElement.value)) 
            {
               inputs.push(inputElement);

               /* No Overwrite messages */
               if (!this.messages.hasOwnProperty(this.filters[input].name))
                  this.messages[this.filters[input].name] = {};

               var messages = validatorInstance.getMessages();

               for (var item in messages) {
                  this.messages[this.filters[input].name][item] = messages[item];
               };
            }
         }
      }
      return inputs;
   },
   getValidInput: function()
   {
      var invalid_inputs = this.getInvalidInput();

      var inputs = [];
      for (var input in this.filters)
      {
         for (var i = invalid_inputs.length - 1; i >= 0; i--)
         {
            if (this.filters[input].name != invalid_inputs[i].value)
            {
               var element = document.querySelector(this.scope + ' [name=\'' + this.filters[input].name + '\']');
               inputs.push(element);
            }
         }
      }
      return inputs;
   },
   isValid: function()
   {
      if (this.getInvalidInput().length)
         return false;
      return true;
   }
}
