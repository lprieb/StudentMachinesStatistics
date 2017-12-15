// ----------------------- EDITOR STACKED BAR GRAPH -------------------------------
/*
var  margin = {top: 20, right: 20, bottom: 30, left: 40};
var width = 300;
var height = 450;
var svg = d3.select("#editor_graph").append("svg:svg")
	.attr("width", width - margin.left - margin.right)
	.attr("height", height - margin.top - margin.bottom)
var g = svg.append("g").attr("transform", "translate(" + margin.left + "," + margin.top + ")"); // translates by shifting graph over
*/
var svg = d3.select("#editor_graph"),
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
d3.csv("./csv/editors.csv", function(d, i, columns) {
  for (i = 1, t = 0; i < columns.length; ++i) t += d[columns[i]] = +d[columns[i]];
  d.total = t;
  return d;
},function(error, data) {
  if (error) throw error;

  var keys = data.columns.slice(1);

  data.sort(function(a, b) { return b.total - a.total; });
  x.domain(data.map(function(d) { return d.Editor; }));
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
      .attr("x", function(d) { return x(d.data.Editor); })
      .attr("y", function(d) { return y(d[1]); })
      .attr("height", function(d) { return y(d[0]) - y(d[1]); })
      .attr("width", x.bandwidth());

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

// ----------------------- GRADE LEVEL EDITOR PIE CHART -------------------------------
/*
var width = 500; 
var height = 390;
var radius = Math.min(width, height) / 2;
var svg2 = d3.select("#senior_editor_graph").append("svg")
	.attr("width", width)
	.attr("height", height);
var g = svg2.append("g").attr("transform", "translate(" + width / 2 + "," + height / 2 + ")");
*/
function readTextFile(file)
{
    var rawFile = new XMLHttpRequest();
    rawFile.open("GET", file, true);
    rawFile.onreadystatechange = function ()
    {
        if(rawFile.readyState === 4)
        {
            if(rawFile.status === 200 || rawFile.status == 0)
            {
                var allText = rawFile.responseText;
				var textarry = allText.split("\n");

				var table = document.getElementById("grade_editor_table");
				table.innerHTML = "";
				// Create table header
				var theader = table.appendChild(document.createElement('thead'));
				theader.setAttribute("id", "tableHeader");

				// Create table body
				var tbody = table.appendChild(document.createElement('tbody'));
				tbody.setAttribute("id", "tableBody");

				// Create table header row
				var trow = theader.appendChild(document.createElement('tr'));

				// Create table header columns
				var th_0 = trow.appendChild(document.createElement('th'));
				var th_1 = trow.appendChild(document.createElement('th'));

				th_0.innerHTML = "Editor";
				th_1.innerHTML = "# of Tracked Processes";

				for (var i = 1; i < textarry.length; ++i){
					if (textarry[i] != ""){
						var row = tbody.insertRow(i-1);
						var cell0 = row.insertCell(0);
						var cell1 = row.insertCell(1);
						var data = textarry[i].split(",");
						cell0.innerHTML = data[0]
						cell1.innerHTML = data[1];
					}
				}
            }
        }
    }
    rawFile.send(null);
}

function grade_level_editor(){
	// Process radio button inputs
	var grade = $('input[name=grade]:checked').val();
	if (grade == 4){ 
		var csv_path = "./csv/senior_editors.csv";
		var title = "Preferred Editor Among Seniors";
	}
	else if (grade == 3) { 
		var csv_path = "./csv/junior_editors.csv";
		var title = "Preferred Editor Among Juniors";
	}
	else if (grade == 2) { 
		var csv_path = "./csv/sophomore_editors.csv";
		var title = "Preferred Editor Among Sophomores";
	}

	readTextFile(csv_path);

	// Create pie graph
	var svg2 = d3.select("#grade_editor_graph");

	var svg2 = d3.select("#grade_editor_graph"),
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
		  .attr("fill", function(d) { return color(d.data.Editor); });

	  arc.append("text")
		  .attr("transform", function(d) { if (d.Count != 0) return "translate(" + label.centroid(d) + ")"; })
		  .attr("dy", "0.35em")
		  .text(function(d) { return d.data.Editor; });
	});
}
