$(document).ready(function(){
    var $tickerHolder = $("#tickerColumns");
    var $getData = $("#getData");

    var create_ticker_data_column = function($tickerHolder,data){
        $tickerHolder.empty();
        var i = 1;

        for (var ticker in data)
        {
            var dataHtml = '<div class="col-sm-4 shade ticker-'+i+'">';
            dataHtml += '<h4>_TICKER_</h4>';
            dataHtml += '<ul>_DATA_</ul>';
            dataHtml += '</div>';

            dataHtml = dataHtml.replace('_TICKER_',ticker);
            var dataUl = '';
            for (var j in data[ticker])
            {
                var r = parseFloat(data[ticker][j]) * 100;
                dataUl += '<li>'+j+'</li>' +'<li>'+ r.toFixed(4)+'%</li>';
            }
            dataHtml = dataHtml.replace('_DATA_',dataUl);

            $tickerHolder.append(dataHtml);
            i++;

        }
    };

    
    $(document).keypress(function(e) {
	    if(e.which == 13) {
	        e.preventDefault();

	        var tickers = [$("#ticker1").val(),$("#ticker2").val(),$("#ticker3").val()];
	        var i = $("#returnsDayStart");
	        var m = $("#returnsDayEnd");

	        var data = {
	            tickers: tickers,
	            i: i.val(),
	            m: m.val()
	        };

	        console.log(data);

	        $.ajax({
	            dataType: "json",
	            url: '/',
	            data: data,
	            success: function(results) {
	                console.log('results',results);
	                create_ticker_data_column($tickerHolder,results);
	            }
	        });
	    }
    });
});
