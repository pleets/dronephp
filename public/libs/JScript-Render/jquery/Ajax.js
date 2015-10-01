/*
 * JScript Render - Ajax
 * http://www.pleets.org
 *
 * Copyright 2014, Pleets Apps
 * Free to use under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
 *
 * Depends:
 *  [lib] jQuery
 *  [class] Animation
 */

/* JScriptRender alias */
if (!window.hasOwnProperty('JScriptRender'))
   JScriptRender = {};

/* Namespace */
if (!JScriptRender.hasOwnProperty('jquery'))
   JScriptRender.jquery = new Object();

/* Ajax class */
JScriptRender.jquery.Ajax = function()
{
   // API's
   JScriptRender.jquery.Ajax.UI = new $jS.jquery.UI();
   JScriptRender.jquery.Ajax.Debug = new $jS.jquery.Debug();
   JScriptRender.jquery.Ajax.Animation = new $jS.jquery.Animation();
};

JScriptRender.jquery.Ajax.prototype  = 
{
    search: function(input, view, settings)
    // void search( jQuery_element, jQuery_element [, Object] )
    {
        var HTML = JScriptRender.jquery.Ajax.UI;
        var DEBUG = JScriptRender.jquery.Ajax.Debug;

        // Settings
        var set = settings || {};

        // Callbacks
        set.ajaxCallback = (set.ajaxCallback instanceof Object) ? set.ajaxCallback: {};
        set.ajaxCallback.success = set.ajaxCallback.success || new Function();
        set.ajaxCallback.complete = set.ajaxCallback.complete || new Function();

        var url = input[0].getAttribute("data-url");

        // Runtime parameters (Arrays and Objects)
        var parameters;

        try {
            if (input[0].getAttribute("data-runtime-parameters") != null && input[0].getAttribute("data-runtime-parameters").trim() != "")
            {
                eval("var parameters = " + input[0].getAttribute("data-runtime-parameters"));
                if (!(parameters instanceof Object) && !(parameters instanceof Array))
                    throw DEBUG.exception("Invalid type", "Runtime parameters should be an array or object");
            }
        }
        catch (e)
        {
            if (e.name == "Invalid type")
                DEBUG.error(e);
            else
                DEBUG.error(e, { 
                    width: 400,
                    suggestion: "Bad runtime parameters in <strong>" + input.selector + "</strong> element"
                });
        }

        var loader = new $jS.html.Loader({ context: view });

        $.ajax({
            url: url,
            type: 'POST',
            data: { request: input.val(), params: parameters },
            dataType: 'html',
            async: true,
            error: function(jqXHR, textStatus, error) {
                view.empty().append(error);
                DEBUG.ajaxError(jqXHR, textStatus, error);
            },
            beforeSend: function()
            {
                view.empty();
                loader.show();
            },
            success: function(data)
            {
                loader.hide(function(){
                    view.append(data);
                });
                set.ajaxCallback.success();
            },
            complete: function() {
                setTimeout(function(){
                    if (loader.isActive())
                        loader.hide();                    
                }, 1000);
                set.ajaxCallback.complete() 
            }
        });
    },
    addAction: function(delegateButton, formElementToProcess, settings)
    {
        var HTML = JScriptRender.jquery.Ajax.UI;
        var DEBUG = JScriptRender.jquery.Ajax.Debug;

        var set = settings || {};
        set.id = set.id || "dialog-ui";
        set.title = set.title || "Add Action";
        set.width = set.width || 400;
        set.modal = (set.modal !== undefined) ? set.modal : true;
        set.position = set.position || "center";

        set.searchConfig = (set.searchConfig instanceof Object) ? set.searchConfig: {};

        set.searchConfig.input = set.searchConfig.input || undefined;
        set.searchConfig.button = set.searchConfig.button || undefined;
        set.searchConfig.view = set.searchConfig.view || undefined;

        set.ajaxCallback = (set.ajaxCallback instanceof Object) ? set.ajaxCallback: {};

        // GET Callbacks
        set.ajaxCallback.get = (set.ajaxCallback.get instanceof Object) ? set.ajaxCallback.get: {};
        set.ajaxCallback.get.success = set.ajaxCallback.get.success || new Function();
        set.ajaxCallback.get.complete = set.ajaxCallback.get.complete || new Function();

        // POST Callbacks
        set.ajaxCallback.post = (set.ajaxCallback.post instanceof Object) ? set.ajaxCallback.post: {};
        set.ajaxCallback.post.success = set.ajaxCallback.post.success || new Function();
        set.ajaxCallback.post.complete = set.ajaxCallback.post.complete || new Function();

        var loader = new $jS.html.Loader();

        $("body").delegate(delegateButton, "click", function(event)
        {
            var that = $(this);
            event.preventDefault();

            var getData = function() 
            {
                var form = $(formElementToProcess);

                $.ajax({
                    url: $(delegateButton)[0].getAttribute("data-resource"),
                    type: 'GET',
                    data: { request: 'data' },
                    dataType: 'html',
                    async: true,
                    error: function(jqXHR, textStatus, error) {
                        DEBUG.ajaxError(jqXHR, textStatus, error);
                    },
                    beforeSend: function() {
                        loader.show();
                    },
                    success: function(data) {
                        loader.hide(function(){
                            form.empty();
                            form.append(data);
                        });
                        set.ajaxCallback.get.success();
                    },
                    complete: function() {
                        setTimeout(function(){
                            if (loader.isActive())
                                loader.hide();                    
                        }, 1000);
                        set.ajaxCallback.get.complete();
                    }
                });
            }

            var form_id = formElementToProcess.replace("#",'');

            HTML.dialog({
                id: set.id,
                title: set.title,
                content: $("<div id='" + form_id + "'></div>"),
                width: set.width,
                modal: set.modal,
                position: set.position,
                buttons: {
                    "Registrar": function()
                    {
                        var form = $(formElementToProcess);
                        var formElement = $(form.children("form"));

                        if (formElement.length)
                        {
                            var url = $(delegateButton)[0].getAttribute("data-resource");
                            var that = $(this);

                            var data = new Object();
                            $.each(formElement.serializeArray(), function() {
                                data[this.name] = this.value;
                            });

                            $.ajax({
                                url: url,
                                type: 'POST',
                                data: data,
                                async: true,
                                error: function(jqXHR, textStatus, error) {
                                    DEBUG.ajaxError(jqXHR, textStatus, error);
                                },
                                beforeSend: function() {
                                    loader.show();
                                },
                                success: function(data) 
                                {
                                    form.empty();
                                    form.append(data);

                                    if (!form.children("form").length) 
                                    {
                                        if (set.searchConfig.view !== undefined)
                                           $(set.searchConfig.view).empty();
                                        if (set.searchConfig.input !== undefined)
                                           $(set.searchConfig.input).val('');
                                        if (set.searchConfig.button !== undefined)
                                           $(set.searchConfig.button).trigger("click");
                                    }
                                    set.ajaxCallback.post.success();
                                },
                                complete: function() {
                                    loader.hide();
                                    set.ajaxCallback.post.complete();
                                }
                            });
                        } else {
                            getData();
                        }
                    },
                    "Cancelar": function() {
                        $(this).dialog("close");
                    }
                }
            }, getData);
        });            
    },
    editAction: function(editSelectionButton, editLoadData, inputSelection, settings)
    {
        var HTML = JScriptRender.jquery.Ajax.UI;
        var DEBUG = JScriptRender.jquery.Ajax.Debug;
        var ANIMATION = JScriptRender.jquery.Ajax.Animation;

        var set = settings || {}
        set.id = set.id || "ui-dialog";
        set.title = set.title || "Editar";
        set.width = set.width || 700;
        set.modal = (set.modal !== undefined) ? set.modal : true;
        set.position = set.position || "center";

        set.searchConfig = (set.searchConfig instanceof Object) ? set.searchConfig: {};

        set.searchConfig.input = set.searchConfig.input || undefined;
        set.searchConfig.button = set.searchConfig.button || undefined;
        set.searchConfig.view = set.searchConfig.view || undefined;

        set.ajaxCallback = (set.ajaxCallback instanceof Object) ? set.ajaxCallback: {};

        // GET Callbacks
        set.ajaxCallback.get = (set.ajaxCallback.get instanceof Object) ? set.ajaxCallback.get: {};
        set.ajaxCallback.get.success = set.ajaxCallback.get.success || new Function();

        // POST Callbacks
        set.ajaxCallback.post = (set.ajaxCallback.post instanceof Object) ? set.ajaxCallback.post: {};
        set.ajaxCallback.post.success = set.ajaxCallback.post.success || new Function();

        var loader = new $jS.html.Loader();

        $("body").delegate(editSelectionButton, "click", function(event)
        {
            event.preventDefault();
            var selection = $(inputSelection + ":checked");
            var row = selection.parent().parent();

            if (!selection.length)
                return alert("Debe seleccionar mínimo un elemento!");

            for (var i = selection.length - 1; i >= 0; i--) {
                    $(selection[i]).parent().parent().attr("class", "warning");
            };

            $(editLoadData + " div.item").remove();

            HTML.dialog({
                title: set.title,
                id: set.id,
                content: $('<div class="$ jrender-slide" id="' + editLoadData.replace("#","") + '"> \
                                <div class="toolbar"> \
                                    <div class="button-left"><span class="glyphicon glyphicon-chevron-left"></span></div> \
                                    <div class="button-right"><span class="glyphicon glyphicon-chevron-right"></span></div> \
                                </div> \
                            </div>'),
                width: set.width,
                modal: set.modal,
                position: set.position,
                buttons: {
                    "Guardar": function()
                    {
                        var form = $(editLoadData);
                        var formElements = $(form.children("div").children("form"));

                        var loadData = function (data, callback) {

                            elements = data.length;
                            if (!elements)
                                return callback();

                            var url = data[elements - 1].getAttribute("action");
                            var item = $(data[elements - 1]).parent();
                            var that = $(this);

                            var ajax_data = new Object();
                            $.each($(data[elements - 1]).serializeArray(), function() {
                                ajax_data[this.name] = this.value;
                            });

                            $.ajax({
                                url: url,
                                type: 'POST',
                                data: { request: ajax_data, action: "edit"},
                                async: true,
                                error: function(jqXHR, textStatus, error) {
                                    DEBUG.ajaxError(jqXHR, textStatus, error);
                                },
                                success: function(response) {
                                    item.empty();
                                    item.append(response);
                                    data.splice(data.length - 1 , 1);
                                    loadData(data, callback);
                                }
                            });
                        }

                        loader.show();
                        loadData(formElements, function () { 
                            loader.hide();
                            if (set.searchConfig.view !== undefined)
                               $(set.searchConfig.view).empty();
                            if (set.searchConfig.input !== undefined)
                               $(set.searchConfig.input).val('');
                            if (set.searchConfig.button !== undefined)
                               $(set.searchConfig.button).trigger("click");
                            set.ajaxCallback.post.success();
                        });

                    },
                    "Cancelar": function() {
                        $(this).dialog("close");
                    }
                }
            });

            var selection = $(inputSelection);
            var url = $(editSelectionButton)[0].getAttribute("data-resource");
            var data = new Array();

            $.each(selection,function(key){
                if ($(this).is(":checked"))
                    data.push($(this)[0].getAttribute("data-selection-id"));
            });

            var loadData = function (data, callback) {

                elements = data.length;
                if (!elements)
                    return callback();

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: { request: data[elements - 1], action: "get" },
                    dataType: 'html',
                    async: true,
                    error: function(jqXHR, textStatus, error) {
                        DEBUG.ajaxError(jqXHR, textStatus, error);
                    },
                    success: function(response)
                    {
                        if (!$(editLoadData + " div.item").length)
                            var item = $("<div class='item'></div>");
                        else
                            var item = $("<div class='item' style='display: none'></div>");
                        item.attr("data-id", data[elements - 1]);
                        var content = $(response);
                        var items = content.length;
                        for (var j = 0; j < items; j++) {
                            item.append(content[j]);
                        };
                        $(editLoadData).append(item);
                        data.splice(data.length - 1 , 1);

                        return loadData(data, callback);
                    }
                });
            }

            loader.show();
            loadData(data, function () { 
                loader.hide();
                new ANIMATION.slider($(editLoadData),{effect: "fade", keys: true});
                set.ajaxCallback.get.success();
            });
        });
    },
    postAction: function(delegateButton, inputSelection, settings)
    {
        var HTML = JScriptRender.jquery.Ajax.UI;
        var DEBUG = JScriptRender.jquery.Ajax.Debug;

        var set = settings || {};
        set.title = set.title || "Action";
        set.content = set.content || $("<p>Start action ?<p>");
        set.className = set.className || "warning";

        set.searchConfig = (set.searchConfig instanceof Object) ? set.searchConfig: {};

        set.searchConfig.input = set.searchConfig.input || undefined;
        set.searchConfig.button = set.searchConfig.button || undefined;
        set.searchConfig.view = set.searchConfig.view || undefined;

        // Callbacks
        set.ajaxCallback = (set.ajaxCallback instanceof Object) ? set.ajaxCallback: {};
        set.ajaxCallback.success = set.ajaxCallback.success || new Function();
        set.ajaxCallback.complete = set.ajaxCallback.complete || new Function();

        var loader = new $jS.html.Loader();

        $("body").delegate(delegateButton, "click", function(event)
        {
            event.preventDefault();
            var selection = $(inputSelection + ":checked");
            var row = selection.parent().parent();

            if (!selection.length)
                return alert("Debe seleccionar mínimo un elemento!");

            for (var i = selection.length - 1; i >= 0; i--) {
                    $(selection[i]).parent().parent().attr("class", set.className);
            };

            HTML.dialog({
                title: set.title,
                content: set.content,
                buttons: {
                    "Aceptar": function()
                    {
                        var selection = $(inputSelection);
                        var url = $(delegateButton)[0].getAttribute("data-resource");
                        var data = new Array();
                        var that = $(this);

                        $.each(selection, function(key) {
                            if ($(this).is(":checked"))
                                data.push($(this)[0].getAttribute("data-selection-id")); 
                        });

                        $.ajax({
                            url: url,
                            type: 'POST',
                            data: { request: data },
                            dataType: 'json',
                            async: true,
                            beforeSend: function() {
                                loader.show();
                            },
                            error: function(jqXHR, textStatus, error) {
                                $.ajax({
                                    url: url,
                                    type: 'POST',
                                    data: { request: data },
                                    dataType: 'html',
                                    async: true,
                                    error: function(jqXHR, textStatus, error) {
                                        DEBUG.ajaxError(jqXHR, textStatus, error);
                                    },
                                    success: function(data) {
                                        DEBUG.phpError(data);
                                    }
                                });

                                DEBUG.ajaxError(jqXHR, textStatus, error);
                            },
                            success: function(data)
                            {
                                if (set.searchConfig.view !== undefined)
                                   $(set.searchConfig.view).empty();
                                if (set.searchConfig.input !== undefined)
                                   $(set.searchConfig.input).val('');
                                if (set.searchConfig.button !== undefined)
                                   $(set.searchConfig.button).trigger("click");
                                that.dialog("close");
                                set.ajaxCallback.success();
                            },
                            complete: function()
                            {
                                loader.hide();
                                set.ajaxCallback.complete();
                            }
                        });
                    },
                    "Cancelar": function() {
                        $(this).dialog("close");
                    }
                }
            });
        });
    },
    loadResource: function(delegateButton, context)
    {
        var HTML = JScriptRender.jquery.Ajax.UI;
        var DEBUG = JScriptRender.jquery.Ajax.Debug;

        $("body").delegate(delegateButton, "click", function(event)
        {
            event.preventDefault();

            var url = $(this)[0].getAttribute("data-resource");

            var container = $(context);
            container.empty();

            var loader = new $jS.html.Loader({ context: container });

            $.ajax({
                url: url,
                type: 'POST',
                data: { request: '', simulateXmlHttpRequest: 0 },
                dataType: 'html',
                async: true,
                beforeSend: function() {
                    loader.show();
                },
                error: function(jqXHR, textStatus, error) {
                    container.empty().append(error);
                    DEBUG.ajaxError(jqXHR, textStatus, error);
                },
                success: function(data) {
                    loader.hide(function(){
                        container.empty().append(data);
                    });
                },
                complete: function() {
                    setTimeout(function(){
                        if (loader.isActive())
                            loader.hide();                    
                    }, 1000);
                },
            });
        });
    }
}