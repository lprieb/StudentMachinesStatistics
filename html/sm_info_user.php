<?php 
$sm = array("student00", "student01", "student02");
$grade = array("Senior", "Junior", "Sophomore");
$affil = array("Staff", "Faculty");
// Connecting, selecting database
$link = mysqli_connect('localhost', 'mchen6', '1234')
 or die('Could not connect: ' . mysql_error());
 mysqli_select_db($link, 'feb31') or die('Could not select database');

// Number of Unique Users
echo "<tr>";
echo "<td class\"col-md-3\">Unique Users</td>";
$query = "select count(distinct netid), machineid from process2 group by machineid;";
$result = mysqli_query($link, $query) or die('Query failed: ' . mysql_error());
for ($j = 0; $j < count($sm); $j++){
	$row = mysqli_fetch_row($result);
	echo "<td class=\"col-md-3\">{$row[0]}</td>";
}
echo "</tr>";
mysqli_free_result($result);

// Number of class (senior, junior, sophomore) on machine
for ($i = 0; $i < count($grade); $i++){
	echo "<tr>";
	echo "<td class\"col-md-3\">{$grade[$i]}s</td>";
	$query = "select count( distinct P.netid), P.machineid from process2 P, user U  where U.netid=P.netid and U.classLevel=\"{$grade[$i]}\" group by machineid;";
	$result = mysqli_query($link, $query) or die('Query failed: ' . mysql_error());
	for ($j = 0; $j < count($sm); $j++){
		$row = mysqli_fetch_row($result);
		echo "<td class=\"col-md-3\">{$row[0]}</td>";
	}
	echo "</tr>";
	mysqli_free_result($result);
}
// Number of faculty and staff
for ($i = 0; $i < count($affil); $i++){
	echo "<tr>";
	echo "<td class\"col-md-3\">{$affil[$i]}</td>";
	$query = "select count( distinct P.netid), P.machineid from process2 P, user U  where U.netid=P.netid and U.affiliation=\"{$affil[$i]}\" group by machineid;";
	$result = mysqli_query($link, $query) or die('Query failed: ' . mysql_error());
	for ($j = 0; $j < count($sm); $j++){
		$row = mysqli_fetch_row($result);
		echo "<td class=\"col-md-3\">{$row[0]}</td>";
	}
	echo "</tr>";
	mysqli_free_result($result);
}

// Closing connection
mysqli_close($link);
?>
