<?php 

$editors = array("vim", "vi", "nano", "emacs", "gedit");
$affil = array("Staff", "Faculty");
$class = array("Senior", "Junior", "Sophomore", "Freshman");
$data = array();

// Connecting, selecting database
$link = mysqli_connect('localhost', 'mchen6', '1234')
 or die('Could not connect: ' . mysql_error());
 //echo 'Connected successfully';
 mysqli_select_db($link, 'feb31') or die('Could not select database');


for ($j = 0; $j < count($editors); $j++) { 
	$editor = $editors[$j];
	$editor_data = array();
	for ($i = 0; $i < count($affil); $i++){
	  $query = "select count(*) from ( select netid, command from process where command like \"{$editor} %\" union all select netid, command from process2 where command like \"{$editor} %\") P, user U where P.netid = U.netid and U.affiliation = \"{$affil[$i]}\";";
	  $result = mysqli_query($link, $query) or die('Query failed: ' . mysql_error());
	  // Printing results in HTML
	  if ($result->num_rows > 0 ){
		$row = mysqli_fetch_row($result);
		$editor_data[$affil[$i]] = $row[0]; 
	  }
	  mysqli_free_result($result);
	}
	for ($i = 0; $i < count($class); $i++){ 
	  $query = "select count(*) from ( select netid, command from process where command like \"{$editor} %\" union all select netid, command from process2 where command like \"{$editor} %\") P, user U where P.netid = U.netid and U.classLevel = \"{$class[$i]}\";";
	  $result = mysqli_query($link, $query) or die('Query failed: ' . mysql_error());
	  // Printing results in HTML
	  if ($result->num_rows > 0 ){
		$row = mysqli_fetch_row($result);
		$editor_data[$class[$i]] = $row[0]; 
	  }
	  mysqli_free_result($result);
	}
	if($editor == "vi" || $editor == "vim"){
		if(!array_key_exists("vim",$data)){
			$data["vim"] = $editor_data;
		}
		else{
			foreach($editor_data as $key => $value){
				$data["vim"][$key] += $value;
			}
		}
	}
	else{
		$data[$editor] = $editor_data;
	}
}
print_r(json_encode($data));
// Closing connection
mysqli_close($link);
?>
