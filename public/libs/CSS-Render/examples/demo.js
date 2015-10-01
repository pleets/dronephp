$(function(){
   $("#run-effect").click(function(){
      var element = $("#animated-element");
      
      var effect = $("#effect").val();
      var timming = $("#timming").val();
      var iterations = $("#iterations").val();

      element.attr("class", "ui-title");

      setTimeout(function(){
         element.attr("class", "$ ui-title " + effect + " " + iterations + " " + timming);   
      }, 100);
   });

   $("body").delegate(".list-group > a", "click", function(){
      if (typeof $(this).next()[0] != "undefined")
      {
         if ($(this).next()[0].nodeName == "UL")
            $(this).next().fadeToggle();
      }
   });
});