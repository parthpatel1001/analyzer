var Graph = function(d3){
    /**
     * data should be in the format:
     * {
     *  "seriesName" : {
     *      "date" : value
     *  }
     * }
     * @param data
     */
    this.render = function(data){
        console.log('rendering',data);


        var margin = {top: 0, right: 0, bottom: 5, left: 50},
            width = 1000 - margin.left - margin.right,
            height = 650 - margin.top - margin.bottom;

        var parseDate = d3.time.format("%Y-%m-%d").parse;
        var convertDate = function(d) {
            return d.getFullYear() + '-' + (d.getMonth()+1) + '-' + d.getDay();
        };

        var x = d3.time.scale()
            .range([0, width]);

        var y = d3.scale.linear()
            .range([height, 0]);

        var color = d3.scale.ordinal()
            .range(["#ac965c", "#ffffff" , "#79f79c"]);

        var xAxis = d3.svg.axis()
            .scale(x)
            .tickFormat(function(d){ return convertDate(d); })
            .orient("bottom");

        var yAxis = d3.svg.axis()
            .scale(y)
            .orient("left");

        var area = d3.svg.area()
            .interpolate("basis")
            .x(function (d) {
                return x(parseDate(d.key));
            })
            .y0(height)
            .y1(function (d) {
                return y(d.value);
            });

        var svg = d3.select("#graph-holder").append("svg")
            /*
             .attr("width", width + margin.left + margin.right)

             */
            .attr("width", "100%")
            //.attr("height", "100%")
            .attr("height", height + margin.bottom)
            .attr("viewBox", "0 0 "
                + ((width + margin.left + margin.right)*1.05)
                + " "
                + ((height + margin.top + margin.bottom)*1.08))
            .append("g")
            .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

        var entries = d3.entries(data);

        color.domain(entries.map(function (d) {
            return d;
        }));

        var minX = d3.min(entries, function (kv) {
            var entry = d3.entries(kv.value);
            return d3.min(entry, function (d) {
                return parseDate(d.key);
            })
        });
        var maxX = d3.max(entries, function (kv) {
            var entry = d3.entries(kv.value);
            return d3.max(entry, function (d) {
                return parseDate(d.key);
            })
        });
        var minY = d3.min(entries, function (kv) {
            var entry = d3.entries(kv.value);
            return d3.min(entry, function (d) {
                return d.value;
            })
        });
        var maxY = d3.max(entries, function (kv) {
            var entry = d3.entries(kv.value);
            return d3.max(entry, function (d) {
                return d.value;
            })
        });

        x.domain([minX, maxX]);
        y.domain([minY, maxY]);

        var element = svg.selectAll(".element")
            .data(entries)
            .enter().append("g")
            .attr("class", "element");

        element.append("path")
            .attr("class", "area")
            .attr("d", function (d) {
                var entry = d3.entries(d.value);
                return area(entry);
            })
            .style("stroke", function (d) {
                return color(d.key);
            })
            .style("fill", "none");

        element.append("text")
            .datum(function (d) {
                var entry = d3.entries(d.value);
                return {
                    name: d.key,
                    date: parseDate(entry[entry.length - 1].key),
                    value: entry[entry.length - 1].value
                };
            })
            .attr("transform", function (d) {
                return "translate(" + x(d.date) + "," + y(d.value) + ")";
            })
            .attr("x", -6)
            .attr("dy", ".35em")
            .text(function (d) {
                return d.name;
            });

        svg.append("g")
            .attr("class", "x axis")
            .attr("transform", "translate(0," + height + ")")
            .call(xAxis)
            .selectAll("text")
            .style("text-anchor", "end")
            .attr("dx", "-.8em")
            .attr("dy", ".15em")
            .attr("transform", "rotate(-65)" );

        svg.append("g")
            .attr("class", "y axis")
            .call(yAxis);

    };
};
