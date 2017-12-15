<!DOCTYPE html>
<html lang="en">
	<head>
		<?php include('head.php'); ?>
		<title>ND Student Machines Tracker</title>
		<script src="leaderboard.js"></script>
	</head>
	<body>
		<?php include('banner.php'); ?>
		<div class="container">
			<div class="row">
				<div class="col-xs-6">
					<h2 class="sub-header"><i class="fa fa-laptop" aria-hidden="true"></i>&nbsp; <a href="student_machines.php">Student Machines</a></h2>
					<div class="table-responsive">
						<table class="table table-striped">
							<thead>
								<tr>
									<th class="col-md-2">Student Machine</th>
									<th class="col-md-4"># of Tracked Processes</th>
								</tr>
							</thead>
							<tbody>
								<?php include('sm_home.php'); ?>
							</tbody>
						</table>
					</div>
					<div class="table-responsive">
						<table class="table table-striped">
							<thead>
								<tr>
									<th class="col-md-2">Student Machine</th>
									<th class="col-md-4"># of Unique Tracked Users</th>
								</tr>
							</thead>
							<tbody>
								<?php include('sm_home2.php'); ?>
							</tbody>
						</table>
					</div>
				</div>
				<div class="col-xs-6">
					<h2 class="sub-header"><i class="fa fa-list-ul" aria-hidden="true"></i>&nbsp; <a href="t_leaderboard.php">Leaderboard</a></h2>
					<div id="leaderboardDiv">
					</div>
					<script>changeLeaderboard("mProcesses");</script>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-6">
				<h2 class="sub-header"><i class="fa fa-bar-chart" aria-hidden="true"></i>&nbsp; Analytics</h2>
				</div>
			</div>
			<div class="row">
				<div class="col-md-4">
					<h4><i class="fa fa-code" aria-hidden="true"></i>&nbsp; Programming Languages</h4>
					<p>See the top programming languages among Notre Dame staff, faculty, and students.</p>
					<p>
						<a class="btn btn-default" href="languages.php" role="button">View analytics &raquo;</a>
					</p>
				</div>
				<div class="col-md-4">
					<h4><i class="fa fa-pencil-square-o" aria-hidden="true"></i>&nbsp; Editors</h4>
					<p>See the preferred editor among Notre Dame staff, faculty, and students.</p>
					<p>
						<a class="btn btn-default" href="editors.php" role="button">View analytics &raquo;</a>
					</p>
				</div>
				<div class="col-md-4">	
					<h4><i class="fa fa-user" aria-hidden="true"></i>&nbsp; Single User</h4>
					<p>See individual user's processes and statistics</p>
					<p>
						<a class="btn btn-default" href="single_user.php" role="button">View analytics &raquo;</a>
					</p>
				</div>
			</div>
		</div>
		<br>
		<?php include('footer.php'); ?>
	</body>
</html>
