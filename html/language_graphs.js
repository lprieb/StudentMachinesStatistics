// ----------------------- EDITOR STACKED BAR GRAPH -------------------------------
//svg.selectAll("*").remove();
var svg = d3.select("#language_graph"),
    margin = {top: 20, right: 20, bottom: 30, left: 40},
    width = +svg.attr("width") - margin.left - margin.right,
    height = +svg.attr("height") - margin.top - margin.bottom,
    g = svg.append("g").attr("transform", "translate(" + margin.left + "," + margin.top + ")");

// helps you create bars
var x = d3.scaleBand()
    .rangeRound([0, width]) 
    .paddingInner(0.05) 			// padding between bars
    .align(0.1);

var y = d3.scaleLinear()
    .rangeRound([height, 0]);

// Colors
var z = d3.scaleOrdinal()
    .range(["#98abc5", "#8a89a6", "#7b6888", "#6b486b", "#a05d56", "#d0743c", "#ff8c00"]);

// function 1: for every row in the function 
// (row of data, row index, array containing the names of the columns
d3.csv("./csv/languages.csv", function(d, i, columns) {
  for (i = 1, t = 0; i < columns.length; ++i) t += d[columns[i]] = +d[columns[i]];
  d.total = t;
  return d;
},function(error, data) {
  if (error) throw error;

  var keys = data.columns.slice(1);

  data.sort(function(a, b) { return b.total - a.total; });
  x.domain(data.map(function(d) { if(d.Language != "html" && d.Language != "md" && d.Language != "go" && d.Language != "js" && d.Language != "java") return d.Language; }));
  y.domain([0, d3.max(data, function(d) { return d.total; })]).nice();
  z.domain(keys);
  g.append("g")
    .selectAll("g")
    .data(d3.stack().keys(keys)(data))
    .enter().append("g")
      .attr("fill", function(d) { return z(d.key); })
    .selectAll("rect")
    .data(function(d) { return d; })
    .enter().append("rect")
      .attr("x", function(d) { return x(d.data.Language); })
      .attr("y", function(d) { return y(d[1]); })
      .attr("height", function(d) { return y(d[0]) - y(d[1]); })
      .attr("width", x.bandwidth());
	/*
	.append("text")
	  .attr("x", function(d) { return x(d.data.Language); })
	  .attr("y", function(d) { return y(d[1]); } )
	  .text(function(d) { return (d.total); });
	 */

  g.append("g")
      .attr("class", "axis")
      .attr("transform", "translate(0," + height + ")")
      .call(d3.axisBottom(x));

  g.append("g")
      .attr("class", "axis")
      .call(d3.axisLeft(y).ticks(null, "s"))
    .append("text")
      .attr("x", 2)
      .attr("y", y(y.ticks().pop()) + 0.5)
      .attr("dy", "0.32em")
      .attr("fill", "#000")
      .attr("font-weight", "bold")
      .attr("text-anchor", "start")
      .text("# of Tracked Processes");

  var legend = g.append("g")
      .attr("font-family", "sans-serif")
      .attr("font-size", 10)
      .attr("text-anchor", "end")
    .selectAll("g")
    .data(keys.slice().reverse())
    .enter().append("g")
      .attr("transform", function(d, i) { return "translate(0," + i * 30 + ")"; });

  legend.append("rect")
      .attr("x", width - 19)
      .attr("width", 19)
      .attr("height", 19)
      .attr("fill", z);

  legend.append("text")
      .attr("x", width - 24)
      .attr("y", 9.5)
      .attr("dy", "0.32em")
      .text(function(d) { return d; });
});

// ----------------------- SENIOR EDITOR PIE CHART -------------------------------
/*
var width = 500; 
var height = 390;
var radius = Math.min(width, height) / 2;
var svg2 = d3.select("#senior_editor_graph").append("svg")
	.attr("width", width)
	.attr("height", height);
var g = svg2.append("g").attr("transform", "translate(" + width / 2 + "," + height / 2 + ")");
*/
function grade_level_languages(){
	// Process radio button inputs
	var grade = $('input[name=grade]:checked').val();
	if (grade == 4){ 
		var csv_path = "./csv/senior_languages.csv";
		var title = "Preferred Language Among Seniors";
	}
	else if (grade == 3) { 
		var csv_path = "./csv/junior_languages.csv";
		var title = "Preferred Language Among Juniors";
	}
	else if (grade == 2) { 
		var csv_path = "./csv/sophomore_languages.csv";
		var title = "Preferred Language Among Sophomores";
	}

	// Create pie graph
	var svg2 = d3.select("#grade_language_graph"),
		w = +svg2.attr("width"),
		h = +svg2.attr("height"),
		radius = Math.min(w, h) / 2,
		g2 = svg2.append("g").attr("transform", "translate(" + w / 2 + "," + h / 2 + ")");
	/*
	svg2.append("text")
		.attr("x", (w/2))
		.attr("y", 20)
		.attr("fill", "black")
		.attr("text-anchor", "middle")
		.style("font-size", "16px")
		.text(title);
	*/
	var color = d3.scaleOrdinal(["#98abc5", "#8a89a6", "#7b6888", "#6b486b", "#a05d56", "#d0743c", "#ff8c00"]);

	var pie = d3.pie()
		.sort(null)
		.value(function(d) { return d.Count; });

	var path = d3.arc()
		.outerRadius(radius - 10)
		.innerRadius(0);

	var label = d3.arc()
		.outerRadius(radius - 40)
		.innerRadius(radius - 40);

	d3.csv(csv_path, function(d) {
	  d.Count = +d.Count;
	  return d;
	}, function(error, data) {
	  if (error) throw error;

	  var arc = g2.selectAll(".arc")
		.data(pie(data))
		.enter().append("g")
		  .attr("class", "arc");

	  arc.append("path")
		  .attr("d", path)
		  .attr("fill", function(d) { return color(d.data.Language); });

	  arc.append("text")
		  .attr("transform", function(d) { if (d.Count != 0) return "translate(" + label.centroid(d) + ")"; })
		  .attr("dy", "0.35em")
		  .text(function(d) { return d.data.Language; });
	});
}
