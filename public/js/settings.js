/* Namespace app */
if (!(Srvdata instanceof Object))
    var Srvdata = {};

$(function () {
    
    // jQuery UI and Bootstrap functionality
    if (!Srvdata.noConflict) {
        var btn = $.fn.button.noConflict() // reverts $.fn.button to jqueryui btn
        $.fn.btn = btn // assigns bootstrap button functionality to $.fn.btn
        Srvdata.noConflict = true;
    }


    /*
     *  ALTERNATE TABLE
     *  Show and hide columns in tables
     */

    Srvdata.alterTable = function (table, button)
    {
        $("body").delegate(button, "click", function(event) {
            event.preventDefault();

            var exception = $(this)[0].getAttribute('data-exception');

            if (!eval(exception) instanceof Array)
                throw "Expression is not an array";

            $(table + " tr > *").fadeToggle();
            for (var i = eval(exception).length - 1; i >= 0; i--) {
                $(table + " tr > *:nth-child(" + eval(exception)[i] + ")").fadeToggle();
            };
        });
    }

});