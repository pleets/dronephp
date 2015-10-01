/*
 * JScript Render - DateControl Class
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
if (!JScriptRender.hasOwnProperty('utils'))
   JScriptRender.utils = new Object();

/* DateControl class */
JScriptRender.utils.DateControl = function(yearSelector, monthSelector, inclusive) {
   
   this.yearSelector = yearSelector;
   this.monthSelector = monthSelector;
   this.inclusive = (inclusive !== undefined) ? inclusive : true;    /* include actual month */
}

JScriptRender.utils.DateControl.prototype = 
{
   setYearSelector: function(yearSelector) 
   {
      this.yearSelector = year;
   },
   setMonthSelector: function(monthSelector)
   {
      this.monthSelector = month;
   },
   render: function()
   {
      var elements = document.querySelector(this.monthSelector);

      for (var i = elements.length - 1; i >= 0; i--) 
      {
         elements.removeChild(elements[i]);
      };

      var yearSelected = document.querySelector(this.yearSelector).value;

      var date = new Date();
      var year = date.getFullYear();

      var num_months = (yearSelected < year) ? 12 : date.getMonth() + 1;

      if (!this.inclusive && yearSelected == year)
         num_months--;

      var language = JScriptRender.settings.general.language;
      var months = JScriptRender.language[language].months;

      for (var i = num_months; i > 0; i--)
      {
         var opt = document.createElement('option');
         opt.value = i;
         var text = document.createTextNode(months[i-1]);
         opt.appendChild(text);
         document.querySelector(this.monthSelector).appendChild(opt);
      }
   }
}
