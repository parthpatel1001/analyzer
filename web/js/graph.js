var Graph = function(d3){
    this.render = function(data){
        console.log('rendering',data);

        var margin = {top: 20, right: 20, bottom: 30, left: 50},
            width = 800 - margin.left - margin.right,
            height = 800 - margin.top - margin.bottom;

        var parseDate = d3.time.format("%Y-%m-%d").parse;

        var x = d3.time.scale()
            .range([0, width]);

        var y = d3.scale.linear()
            .range([height, 0]);

        var color = d3.scale.category20c();

        var xAxis = d3.svg.axis()
            .scale(x)
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
            .attr("width", width + margin.left + margin.right)
            .attr("height", height + margin.top + margin.bottom)
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
            .style("fill", function (d) {
                return color(d.key);
            });

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
            .call(xAxis);

        svg.append("g")
            .attr("class", "y axis")
            .call(yAxis);

    };
};