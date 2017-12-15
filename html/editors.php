<html>
	<head>
		<?php include('head.php'); ?>
		<script src="https://d3js.org/d3.v4.min.js"></script>
		<title>ND Student Machines Tracker - Editors</title>
	</head>
	<body>
		<?php include('banner.php'); ?>
		<?php include('editor_csv.php'); ?>
		<div class="container">
			<h1>Editors</h1>
			<div class="row">
				<h3>&nbsp;&nbsp;Popularity of Editors by Affiliation</h3>
				<p>&nbsp;&nbsp;&nbsp;Tracked processes of file edits by ND staff, faculty, and students.</p>
				<svg id="editor_graph" width="750" height="450"></svg>
			</div>
			<div class="row">
				<div class="col-md-5">
					<h3>Preferred Editors Among...</h3>
					<form id="class_selection" onclick="grade_level_editor()" >
						<input type="radio" name="grade" value=4 checked="checked">&nbsp;Seniors<br>
						<input type="radio" name="grade" value=3>&nbsp;Juniors<br>
						<input type="radio" name="grade" value=2>&nbsp;Sophomores<br>
					</form>
					<table class="table table-striped" id="grade_editor_table"></table>
				</div>

				<div class="col-md-7">
					<svg id="grade_editor_graph" width="350" height="450"></svg>
				</div>
			</div>
			<script type="text/javascript">
				$(document).ready(function(){
					var radioValue = $("input[name='gender']:checked").val();
					grade_level_editor();
				});
			</script>
		</div>
		<script type="text/javascript" src="graphs.js"></script>
		<?php include('footer.php'); ?>
	</body>
</html>
