$(function(){
   	
	/* EXPORTAR INFORMES A EXCEL */
	$("body").delegate(".general-export-button","click", function()
	{
        var div = document.createElement("div");

        var tbl = $(this).attr('data-table');
        var baseURL = $(this).attr('data-base-url');

        var _table = $("#" + tbl);

        _table = _table.clone();

        $.each(_table.children("tbody").children("tr").children("td"), function(){
            this.style.border = "solid 1px #06a2ec";
        });
        $.each(_table.children("thead").children("tr").children("th"), function(){
            this.style.border = "solid 1px #463265";
        });

        div.appendChild(_table[0]);
        table = $(div).html();

        if (_table.children("caption").length)
        {
	        var caption = _table.children("caption");
        	
	        var div = document.createElement("div");
	        var _title = caption.clone();
	        div.appendChild(_title[0]);
	        title = $(div).html();

	        var data = title + "<br /><br />" + table;
	        var doc_title = _title.text();
        }

        var data = table;
        var doc_title = "Report";

        $.ajax({
            type: "POST",
            url: baseURL + "/library/Calc-Writer/writer.php",
            data: "data=" + data,
            success: function(datos){
                window.location = baseURL + "/library/Calc-Writer/xls.php?name=" + $("title").text() + "-" + doc_title;
            }
        });		
	});    
});