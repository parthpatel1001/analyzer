var Analyzer = function(document,$,d3,Graph){
    var self = this;

    this.init = function() {
        self.$tickerHolder = $("#tickerColumns");
        self.graph = new Graph(d3);
        $(document).keypress(self.onEnterKeyPress);

        return this;
    };

    this.createTickerDataColumn = function(data){
        //self.$tickerHolder.empty();
        //var i = 1;
        //
        //for (var ticker in data)
        //{
        //    var dataHtml = '<div class="col-sm-4 ticker-'+i+'">';
        //    //dataHtml += '<h4>_TICKER_</h4>';
        //    dataHtml += '<ul>_DATA_</ul>';
        //    dataHtml += '</div>';
        //
        //    dataHtml = dataHtml.replace('_TICKER_',ticker);
        //    var dataUl = '';
        //    for (var j in data[ticker])
        //    {
        //        var r = parseFloat(data[ticker][j]) * 100;
        //        dataUl += '<li>'+j+'</li>' +'<li>'+ r.toFixed(4)+'%</li>';
        //    }
        //    dataHtml = dataHtml.replace('_DATA_',dataUl);
        //
        //    self.$tickerHolder.append(dataHtml);
        //    i++;
        //
        //}
    };

    this.finished = function(results){
        console.log('results',results);
        self.createTickerDataColumn(results);
        $(".ticker-1, .ticker-3").addClass("shade");
        self.graph.render(results);
    };

    this.get_data = function(finished){
        var tickers = [$("#ticker1").val(),$("#ticker2").val(),$("#ticker3").val()];
        var i = $("#returnsDayStart");
        //var m = $("#returnsDayEnd");

        var params = {
            tickers: tickers,
            i: i.val(),
            //m: m.val()
        };

        console.log('params',params);

        $.ajax({
            dataType: "json",
            url: '/',
            data: params,
            success: finished
        });
    };

    this.onEnterKeyPress = function(e){
        if(e.which == 13) {
            e.preventDefault();
            self.get_data(self.finished);
        }
    };
};