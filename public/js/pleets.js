$(function(){

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
