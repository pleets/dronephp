/*
 * JScript Render - FormValidator Class
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
if (!JScriptRender.hasOwnProperty('html'))
   JScriptRender.html = new Object();

/* FormValidator class */
JScriptRender.html.FormValidator = function(formElementToProcess, settings) {

    this.formElementToProcess = formElementToProcess;
    this.settings = settings;

};

JScriptRender.html.FormValidator.prototype = 
{
    render: function()
    {
        var formElementToProcess = this.formElementToProcess;
        var settings = this.settings;

        var HTML = JScriptRender.html;

        var set = settings || {};

        // Highlight classes on error or success
        set.highlight = (set.highlight instanceof Object) ? set.highlight: {};
        set.highlight.onValid = set.highlight.onValid || "";
        set.highlight.onInvalid = set.highlight.onInvalid || "";

        set.showMessages = (set.showMessages !== undefined) ? set.showMessages : true;

        set.id = set.id || "dialog-ui";

        // Callbacks
        set.onInvalid = set.onInvalid || new Function();
        set.onValid = set.onValid || new Function();


        var form_id = formElementToProcess.replace("#",'');
console.info(formElementToProcess);
        var url = document.querySelector(formElementToProcess).getAttribute("action");
        var _url = (url == null || url.trim() == "") ? document.URL : url;
        

        _validators = (set.validators !== "undefined" && set.validators) ? set.validators : false;

        if (_validators)
        {
            var InputFilter = new JScriptRender.filter.InputFilter(formElementToProcess);
            for (var input in set.validators)
            {
                InputFilter.add({ name: input, validators: set.validators[input]});
            }


            // [BUG] - Bad return value for InputFilter.getValidInput()

            var validInputs = InputFilter.getInputs();
            var invalidInputs = InputFilter.getInvalidInput();             

            // Refresh
            for (var i = validInputs.length - 1; i >= 0; i--) 
            {
                var classes = validInputs[i].className.split(" ");
                var classString = "";
                for (var j = classes.length - 1; j >= 0; j--) {
                    if (classes[j] != set.highlight.onInvalid)
                        classString += " " +classes[j];
                };
                validInputs[i].className = classString;

                var logMessagesBox = document.querySelector(InputFilter.scope + ' [data-log=\'' + validInputs[i].name + '\']');

                if (logMessagesBox != null)
                {
                    while (logMessagesBox.firstChild) {
                        logMessagesBox.removeChild(logMessagesBox.firstChild);
                    }              
                }                    
            };

            // [BUG] - Missing support for onValid class

            if (invalidInputs.length)
            {
                for (var i = invalidInputs.length - 1; i >= 0; i--) 
                {
                    invalidInputs[i].className = invalidInputs[i].className + " " + set.highlight.onInvalid;
                    if (i == 0)
                        invalidInputs[i].focus();
                };

                if (set.showMessages)
                {
                    var messages = InputFilter.getMessages();

                    for (var item in messages)
                    {
                        var element = document.querySelector(InputFilter.scope + ' [name=\'' + item + '\']');
                        var logMessagesBox = document.querySelector(InputFilter.scope + ' [data-log=\'' + item + '\']');
                        var elementMessages = messages[item];

                        for (var msg in elementMessages)
                        {
                            var span = document.createElement('span');
                            var text = elementMessages[msg];
                            var textNode = document.createTextNode(text);
                            span.appendChild(textNode);

                            if (logMessagesBox != null)
                                logMessagesBox.appendChild(span);
                        }
                    }
                }

                return set.onInvalid(InputFilter.getMessages());
            }
            else
                set.onValid(InputFilter.getInputs());
        }

        return false;
    },
}
