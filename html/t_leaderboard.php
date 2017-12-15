<html>
<head>
	<script src="leaderboard.js"></script>
	<?php include('head.php'); ?>
	<?php include('banner.php'); ?>
</head>
<body>
	<div class="container">
		<h2>Leaderboard</h2>
		<div class="row">
			<div class="col-md-6">
			<form>
			<select id="leaderboard" class="form-control" onchange="changeLeaderboard(this.value)">
				<option value="mProcesses" selected>Most Processes</option>
				<option value="avgCpu">Top Average CPU Usage (CPUTime/Realtime)</option>
				<option value="avgMem">Top Average Memory Usage (% of Total)</option>
				<option value="avgVsz">Top Average Virtual Memory Usage (KiB)</option>
				<option value="avgRss">Top Average Resident Set Size (KiB)</option>
				<option value="maxCpu">Top Maximum CPU Usage (CPUTime/RealTime)</option>
				<option value="maxMem">Top Maximum Memory Usage (% of Total)</option>
				<option value="maxVsz">Top Maximum Virtual Memory Usage (KiB)</option>
				<option value="maxRss">Top Maximum Resident Set Size (KiB)</option>
			</select>
			</form>
			</div>
		</div>
		<script>changeLeaderboard("mProcesses");</script>
		<div id="leaderboardDiv"></div>
	</div>
	<?php include('footer.php');?>
</body>
</html>
