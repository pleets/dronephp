/*
 * JScript Render - Form class
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

/* Form class */
JScriptRender.html.Form = function(formObject)
{
    if (typeof formObject != "undefined")
    {
        if (typeof formObject == "object" && formObject.nodeName == "FORM")
        {
            JScriptRender.html.Form.prototype._form = formObject;
            JScriptRender.html.Form.prototype._elements = document.querySelectorAll('#'+ this._form.id +' input[name]');      
        }
    else
        throw "The element must be a FORM object";
    }

   // Array of all elements
   JScriptRender.html.Form.prototype.elements = [];

   // The current element to appy changes
   JScriptRender.html.Form.prototype.element = null;
}

JScriptRender.html.Form.prototype = 
{
    add: function(object)
    {
        if (!object.hasOwnProperty('type'))
            throw "The element must be typed";
        if (!object.hasOwnProperty('name') && object.type != "submit")
            throw "The element must be named";
        for (var i = this.elements.length - 1; i >= 0; i--) 
        {
            if (this.elements[i].name == object.name)
                throw "Form.add: The element " + object.name + " already exists in the form";
        }
        this.elements.push(object);
        return this;
    },
    setData: function(data)
    {
        for (var i = this.count() - 1; i >= 0; i--) {
            for (var element in data)
            {
                if (element == this.elements[i].name)
                    this.get(this.elements[i].name).setAttribute('value', data[element]);
            }
        }
    },
    count: function()
    {
        return this.elements.length;
    },
    get: function(element)
    {
        if (typeof element !== "string")
            throw "The argument must be an string";

        var match = false;

        for (var i = this.elements.length - 1; i >= 0; i--) 
        {
            if (this.elements[i].name == element)
            {
                this.element = this.elements[i];
                match = true;
            }
        }
        if (!match)
            throw "Element "+ element +" not found in form";
        return this;
    },
   getAttribute: function(attribute)
   {
        if (typeof element !== "string")
            throw "The argument must be an string";
        if (this.element === null)
            throw "There is not any matched element";

        if (this.element.attributes !== "undefined")
            return this.element.attributes[attribute];
        return null;
   },
    getAttributes: function()
    {
        if (this.element === null)
            throw "There is not any matched element";

        return this.element.attributes;
    },
    getData: function()
    {
        var data = new Object();

        for (var i = this.count() - 1; i >= 0; i--) 
        {
            var value = (typeof this.elements[i].attributes !== "undefined" && typeof this.elements[i].attributes.value !== "undefined") ? this.elements[i].attributes.value : null;
            data[this.elements[i].name] = value;
        }
        return data;
    },
    getElements: function()
    {
        return this.elements;
    },
    getInputFilter: function()
    {
        throw "Not supported";
    },
    getLabel: function()
    {
        if (this.element === null)
            throw "There is not any matched element";

        if (this.element.hasOwnProperty('options'))
        {
            if (this.element.options.hasOwnProperty('label'))
            return this.element.options.label;
        }
        return null;
    },
    getName: function()
    {
        if (this.element === null)
            throw "There is not any matched element";

        return this.element.name;
    },
    getValue: function()
    {
        if (this.element === null)
            throw "There is not any matched element";

        if (this.element.hasOwnProperty('attributes'))
        {
            if (this.element.attributes.hasOwnProperty('value'))
            return this.element.attributes.value;
        }
        return null;
    },
    has: function(element)
    {
        if (typeof element !== "string")
            throw "The argument must be an string";

        for (var i = this.count() - 1; i >= 0; i--) {
            if (this.elements[i].name == element)
            return true;
        }
        return false;
    },
    hasAttribute: function(attribute)
    {
        if (typeof element !== "string")
            throw "The argument must be an string";
        if (this.element === null)
            throw "There is not any matched element";

        if (this.element.hasOwnProperty('attributes'))
        {
            if (this.element.attributes.hasOwnProperty(attribute))
            return true;
        }
        return false;
    },
    isValid: function()
    {
        throw "Not supported";
    },
    submit: function()
    {
        var HTML = new JScriptRender.html.Html();
        var DEBUG = new JScriptRender.Debug();

        var set = settings || {};

        // Highlight inputs on error
        set.highlight = (set.highlight !== undefined) ? set.highlight : true; 

        set.id = set.id || "dialog-ui";

        set.debug = (set.debug !== undefined) ? set.debug : false;

        set.callback = (set.callback instanceof Object) ? set.callback: {};

        // Error and success callback
        set.callback.error = set.callback.error || new Function();
        set.callback.success = set.callback.success || new Function();

        // Debug Callbacks
        set.callback.debug = (set.callback.debug instanceof Object) ? set.callback.debug: {};
        set.callback.debug.success = set.callback.debug.success || new Function();
        set.callback.debug.error = set.callback.debug.error || new Function();

        $("body").delegate(formElementToProcess, "submit", function(event)
        {
            var that = $(this);
            event.preventDefault();

            var form_id = formElementToProcess.replace("#",'');

            var url = $(this)[0].getAttribute("action");
            var _url = (url == null || url.trim() == "") ? document.URL : url;

            var data = new Object();
            $.each($(formElementToProcess).serializeArray(), function() {
                data[this.name] = this.value;
            });

            var _data = JSON.stringify(data);

            set.buttons = set.buttons || {
                "Accept": function() {
                    $(this).dialog("close");
                }
            };

            _validators = (set.validators !== "undefined" && set.validators) ? set.validators : false;

            if (_validators)
            {
                var InputFilter = new JScriptRender.filter.InputFilter(formElementToProcess);
                
                for (var input in set.validators)
                {
                    InputFilter.add({ name: input, validators: set.validators[input]});
                }

                var invalid = (InputFilter.getInvalidInput().length);

                // Refresh
                if (set.highlight)
                {
                    var inputs = InputFilter.getValidInput();
                    for (var i = inputs.length - 1; i >= 0; i--) {
                        var classes = inputs[i].className.split(" ");
                        var classString = "";
                        
                        for (var j = classes.length - 1; j >= 0; j--) {
                            if (classes[j] != "input-error")
                                classString += " " +classes[j];
                        };
                        inputs[i].className = classString;
                    };
                }

                if (invalid && set.debug)
                {
                    return HTML.dialog({
                        id: set.id,
                        title: set.title,
                        content: $("<div id='" + form_id + "'> \
                                    <div><h3>Warning!</h3></div> \
                                    <p>Message: <strong>Missing parameters!</strong></p> \
                                    Type: " + "validator" + "<br /> \
                                    Response: " + JSON.stringify(invalid) + "<br /> \
                                    </div>"),
                        width: set.width,
                        modal: set.modal,
                        position: set.position,
                        persistence: false,
                        buttons: set.buttons,
                    }, set.callback.debug.error());             // Debug error callback
                }
                else if (invalid)
                {
                    if (set.highlight)
                    {
                        var inputs = InputFilter.getInvalidInput();

                        for (var i = inputs.length - 1; i >= 0; i--) {
                            inputs[i].className = inputs[i].className + " input-error";
                            if (i == 0)
                                inputs[i].focus();
                        };
                    }
                    return set.callback.error();                // Error callback
                }
                else {
                    set.callback.debug.success();
                }
            }

            if (set.debug)
            {
                HTML.dialog({
                id: set.id,
                title: set.title,
                content: $("<div id='" + form_id + "'> \
                            <div><h3>Request</h3></div> \
                            Data: " + _data + "<br /> \
                            Url: " + _url + "<br /> \
                            </div>"),
                width: set.width,
                modal: set.modal,
                position: set.position,
                persistence: false,
                buttons: set.buttons,
                }, set.callback.debug.success());               // Debug success callback
            }
            else {
                set.callback.success();
            }
        });
    },
    prepare: function()
    {
        throw "Not supported";
    },
    setAttribute: function(attribute, value)
    {
        if (typeof attribute !== "string")
            throw "The argument must be an string";
        if (this.element === null)
            throw "There is not any matched element";

        if (this.element.hasOwnProperty('attributes'))
            return this.element.attributes[attribute] = value;
        return this;
    },
    remove: function(element)
    {
        if (typeof element !== "string")
            throw "The argument must be an string";

        for (var i = this.count() - 1; i >= 0; i--) {
            if (this.elements[i].name == element)
            {
                var idx = this.elements.indexOf(element);
                this.elements.splice(idx, 1);
            }
        }
    },
    setLabel: function(label)
    {
        if (typeof label !== "string")
            throw "The argument must be an string";
        if (this.element === null)
            throw "There is not any matched element";

        if (this.element.hasOwnProperty('options'))
            return this.element.options.label = label;
        return this;
    },
    setInputFilter: function()
    {
        throw "Not supported";
    },
    setValidationGroup: function()
    {
        throw "Not supported";
    },
    getForm: function()
    {
        var form = document.createElement('form');

        for (var i = 0, length = this.count() - 1; i <= length; i++) 
        {
            elObj = this.elements[i];
            var element = document.createElement('input');

            /* principal attributes */
            element.type = elObj.type;
            element.name = elObj.name;

            var attributes = elObj.attributes;
            var options = elObj.options;

            /* others attributes */
            for (attr in attributes)
            {
                element.setAttribute(attr, attributes[attr]);
            }

            /* options */
            for (opt in options)
            {
                if (opt == 'label')
                {
                    var label = document.createElement('label');
                    var text = document.createElement('span');
                    text.appendChild(document.createTextNode(options[opt]));
                    label.appendChild(text);
                    label.appendChild(element);
                    element = label;
                }
            }

            form.appendChild(element);
        };
        return form;
    }
}
