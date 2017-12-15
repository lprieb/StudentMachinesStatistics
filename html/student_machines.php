<html>
	<head>
		<?php include('head.php'); ?>
		<script src="https://d3js.org/d3.v4.min.js"></script>
		<title>ND Student Machines Tracker</title>
	</head>
	<body>
		<?php include('banner.php'); ?>
		<div class="container">
			<h2>Student Machines Info</h2>
			<div class="table-responsive">
				<table class="table table-striped">
					<thead>
						<tr>
							<th class="col-md-3"></th>
							<th class="col-md-3">student00</th>
							<th class="col-md-3">student01</th>
							<th class="col-md-3">student02</th>
						</tr>
					</thead>
					<tbody>
						<?php include('sm_info.php'); ?>
					</tbody>
				</table>
			</div>
			<h2>Tracked Users on Student Machines</h2>
			<div class="table-responsive">
				<table class="table table-striped">
					<thead>
						<tr>
							<th class="col-md-3"></th>
							<th class="col-md-3">student00</th>
							<th class="col-md-3">student01</th>
							<th class="col-md-3">student02</th>
						</tr>
					</thead>
					<tbody>
						<?php include('sm_info_user.php'); ?>
					</tbody>
				</table>
			</div>
		</div>
		<?php include('footer.php'); ?>
	</body>
</html>
