<?php 
$sm = array("student00", "student01", "student02");
// Connecting, selecting database
$link = mysqli_connect('localhost', 'mchen6', '1234')
 or die('Could not connect: ' . mysql_error());
 mysqli_select_db($link, 'feb31') or die('Could not select database');

// Number of Unique Users
for ($j = 0; $j < count($sm); $j++){
	$query = "select count(*) from (select distinct netid from (select netid from process where machineid = \"{$sm[$j]}\" union all select netid from process2 where machineid = \"{$sm[$j]}\") A) B";
	$result = mysqli_query($link, $query) or die('Query failed: ' . mysql_error());
	$row = mysqli_fetch_row($result);
	echo "<tr>";
	echo "<td class=\"col-md-2\">{$sm[$j]}</td>";
	echo "<td class=\"col-md-4\">{$row[0]}</td>";
	echo "</tr>";
	mysqli_free_result($result);
}

// Closing connection
mysqli_close($link);
?>
