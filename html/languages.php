<html>
	<head>
		<?php include('head.php'); ?>
		<script src="https://d3js.org/d3.v4.min.js"></script>
		<title>ND Student Machines Tracker - Languages</title>
	</head>
	<body>
		<?php include('banner.php'); ?>
		<?php include('languages_csv.php'); ?>
		<div class="container">
			<h1>Languages</h1>
			<div class="row">
				<div class="col-md-6">
					<h3>Preferred Languages Among...</h3>
					<form id="class_selection" onclick="grade_level_languages()" >
						<input type="radio" name="grade" value=4 checked="checked">&nbsp; Seniors<br>
						<input type="radio" name="grade" value=3>&nbsp; Juniors<br>
						<input type="radio" name="grade" value=2>&nbsp; Sophomores<br>
					</form>
					<svg id="grade_language_graph" width="350" height="450"></svg>
				</div>
				<div class="col-md-6">
					<h3>Popularity of Programming Languages by Affiliation</h3>
					<svg id="language_graph" width="600" height="475"></svg>
				</div>
			</div>
			<script type="text/javascript">
				$(document).ready(function(){
					var radioValue = $("input[name='gender']:checked").val();
					grade_level_languages();
				});
			</script>
			<h3>Processes Written in Different Languages by Affiliation</h3>
            <div class="table-responsive">
				<table class="table table-striped">
					<thead>
						<tr>
							<th>Language</th>
							<th>Staff</th>
							<th>Faculty</th>
							<th>Senior</th>
                            <th>Junior</th>
							<th>Sophomore</th>
							<th>Freshman</th>
						</tr>
					</thead>
					<tbody>
						<?php include('language_info_full.php'); ?>
					</tbody>
				</table>
			</div>
		</div>
		<script type="text/javascript" src="language_graphs.js"></script>
	</body>
</html>
