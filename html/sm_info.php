<?php 
$sm = array("student00", "student01", "student02");
$title = array("Total Memory", "Number of CPUs", "Total Swap Space");
$field = array("total_memory", "num_cpus", "total_swap");
// Connecting, selecting database
$link = mysqli_connect('localhost', 'mchen6', '1234')
 or die('Could not connect: ' . mysql_error());
 mysqli_select_db($link, 'feb31') or die('Could not select database');


// Number of Tracked Processes
echo "<tr>";
echo "<td class=\"col-md-3\">Tracked Processes</td>";
for ($j = 0; $j < count($sm); $j++) {
	$query = "select (select count(*) from process where machineid = \"{$sm[$j]}\") + (select count(*) from process2 where machineid = \"{$sm[$j]}\") as {$sm[$j]}_count;";
	$result = mysqli_query($link, $query) or die('Query failed: ' . mysql_error());
	$row = mysqli_fetch_row($result);
	echo "<td class=\"col-md-3\">{$row[0]}</td>";
	mysqli_free_result($result);
}
echo "</tr>";

// Total memory
for ($j = 0; $j < count($title); $j++) { 
	echo "<tr>";
	echo "<td class=\"col-md-3\">{$title[$j]}</td>";
	$query = "select {$field[$j]} from machine;";
	$result = mysqli_query($link, $query) or die('Query failed: ' . mysql_error());
	while ($tuple = mysqli_fetch_row($result)){
		echo "<td class=\"col-md-3\">{$tuple[0]}</td>";
	}
	echo "</tr>";
	mysqli_free_result($result);
}
// Number of CPUs

// Total swap

// Closing connection
mysqli_close($link);
?>
