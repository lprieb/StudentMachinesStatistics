<html>
<body>
<?php 
$netid = $_GET["netid"]; 
$sm = array("student00", "student01", "student02");
// Connecting, selecting database
$link = mysqli_connect('localhost', 'mchen6','1234')
 or die('Could not connect: ' . mysql_error());
 mysqli_select_db($link, 'feb31') or die('Could not select database');
 // Performing SQL query
 $query = "SELECT * FROM user where netid = \"{$netid}\"";
 $result = mysqli_query($link, $query) or die('Query failed: ' . mysql_error());
 // Printing results in HTML
 if ($result->num_rows > 0 ){
 	 echo "<h3>User Information</h3>";
 	 echo "<table class=\"table\">";
	 echo "<tbody>";
	 $row= mysqli_fetch_row($result);
	 echo "<tr><td>First Name</td><td>{$row[1]}</td></tr>";
	 echo "<tr><td>Last Name</td><td>{$row[2]}</td></tr>";
	 echo "<tr><td>Title</td><td>{$row[3]}</td></tr>";
	 echo "<tr><td>College</td><td>{$row[4]}</td></tr>";
	 if ($row[5]){
	   echo "<tr><td>Grade</td><td>{$row[5]}</td></tr>";
	 }
	mysqli_free_result($result);

	// Determine favorite editor
	$editors = array("vim", "gedit", "nano", "emacs");
	$editor = "NA";
	$editor_count = 0;
	for ($j = 0; $j < count($editors); $j++){
		$query = "select count(command) from process2 where netid=\"{$netid}\" and command like \"{$editors[$j]} %\";";
		$result = mysqli_query($link, $query) or die('Query failed: ' . mysql_error());
		$row = mysqli_fetch_row($result);
		if ((int) $row[0] > $editor_count){
			$editor_count = (int) $row[0];
			$editor = $editors[$j];
		}
		mysqli_free_result($result);
	}
	echo "<tr><td>Preferred Editor</td><td>{$editor}</td></tr>";

	// Determine favorite programming language
	$lang = array("py", "cpp", "c", "sh", "html", "md");
	$language = array("Python", "C++", "C", "Shell", "HTML", "Markdown");
	$counts = array();
	for ($k = 0; $k < count($lang); $k++){
		$c = 0;
		for ($j = 0; $j < count($editors); $j++){ 
			$query = "select count(command) from process2 where netid = \"{$netid}\" and command like \"{$editors[$j]} %.{$lang[$k]}\";";
			$result = mysqli_query($link, $query) or die('Query failed: ' . mysql_error());
			$row = mysqli_fetch_row($result);
			$c += (int) $row[0];
			mysqli_free_result($result);
		}
		array_push($counts, $c);
	}
	$high = 0;
	$index = Null;
	for ($k = 0; $k < count($counts); $k++){
		if ($counts[$k] > $high){
			$high = $counts[$k];
			$index = $k;
		}
	}
	if ($high > 0){
		echo "<tr><td>Preferred Programming Language</td><td>{$language[$index]}</td></tr>";
	}
	// Favorite student machine
	$query = "select count(netid), machineid from process2 where netid = \"{$netid}\" group by machineid; ";
	$result = mysqli_query($link, $query) or die('Query failed: ' . mysql_error());
	$sm_top = 0;
	$sm_proc_num = 0;
	for ($j = 0; $j < count($sm); $j++){
		$row = mysqli_fetch_row($result);
		if ((int) $row[0] > $sm_proc_num){
			$sm_top = $row[1];
			$sm_proc_num = (int) $row[0];
		}
	}
	mysqli_free_result($result);

	if ($sm_proc_num > 0){
		echo "<tr><td>Favorite Student Machine</td><td>{$sm_top}</td></tr>";
	}
	echo "</tbody>";
	echo "</table>";
  }
 else { 
     echo "No record of {$netid}'s processes on student machines";
 }
// Closing connection
mysqli_close($link);
?>
</body>
</html>
