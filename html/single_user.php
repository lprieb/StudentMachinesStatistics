<html>
	<head>
		<?php include('head.php'); ?>
		<script type="text/javascript" src="functions.js"></script>
		<title>ND Student Machine Tracker - Single User</title>
	</head>
	<body>
		<?php include('banner.php'); ?>
		<div class="container">
			<h1>Single User Tracking</h1>
			<form id="uForm">
				Enter a NetID: &nbsp;<input type="text" id="netid">
				<input type="submit"  value="Submit">
			</form>
			<div id="userInfoTable"></div>
			<div id="userProcTable"></div>
		</div>
		<?php include('footer.php'); ?>
	</body>
</html>
