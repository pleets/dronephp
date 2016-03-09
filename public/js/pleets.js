$(function(){

    $("[data-role='file-actions']").click(function(event){
        event.preventDefault();

        var url = $(this).attr('href');

        $.ajax({
            url: url,
            data: {},
            beforeSend: function() {
                $("#file-functions").openModal();
            },
            error: function()
            {
                $("#file-functions .modal-content").html('Error!');
            },
            success: function(data)
            {
                $("#file-functions .modal-content").html(data);
            }
        });
    });

    $("#get-views-from-controller").click(function(event) {
        event.preventDefault();

        var url = $(this).attr('href');
        var module = $(this).attr('data-module');
        var controller = $(this).attr('data-controller');

        $.ajax({
            url: url,
            method: 'post',
            data: { module: module, controller: controller },
            beforeSend: function()
            {
                $("#show-views-from-controller").openModal();
            },
            error: function()
            {
                $("#show-views-from-controller .modal-content").html('Error!');
            },
            success: function(data)
            {
                $("#show-views-from-controller .modal-content").html(data);
            }
        });
    });

    $("#save-file-action").submit(function(event)
    {
        event.preventDefault();

        var url = $(this).attr('action');
        var type = $(this).attr('method');
        var data = $(this).serializeArray();

        $.ajax({
            url: url,
            type: type,
            data: data,
            dataType: 'json',
            beforeSend: function()
            {
                $("#save-file-action-loader").show(500);
                $("#save-file-action-response").hide(500)
                $("#save-file-action-response").empty();
            },
            error: function()
            {
                $("#save-file-action-response").html("<div class='card-panel red'>Error al guardar los cambios!</div>");
            },
            success: function(data)
            {
                if (!data.success)
                    $("#save-file-action-response").html("<div class='card-panel red'>Error al guardar los cambios!</div>");
            },
            complete: function()
            {
                setTimeout(function(){
                    $("#save-file-action-response").show();
                }, 1000)

                $("#save-file-action-loader").hide(500);
            }
        });
    });

    $("body").delegate("[data-action='ajax-request']", "click", function(event)
    {
        event.preventDefault();

        var url = $(this).attr('href');
        var type = $(this).attr('data-type');
        var box = $(this).attr('data-response');
        var data = $(this).attr('data-object');

        var call = eval($(this).attr('data-callback')) || {};
        call.success = call.success || new Function();

        $.ajax({
            url: url,
            type: type,
            data: eval(data),
            beforeSend: function() {
                $(box).html("<img class='responsive-image-320' src='img/preloader.gif' width='50' />");
            },
            error: function(jqXHR, textStatus, errorThrown)
            {
                $(box).html("<div class='alert alert-danger'>Ha ocurrido un error inesperado!</div>");
            },
            success: function(data)
            {
                $(box).html(data);
                call.success();
            }
        });
    });

});
