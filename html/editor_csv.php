<?php 

$editors = array("vi", "vim", "nano", "emacs", "gedit");
$class = array("Senior", "Junior", "Sophomore", "Freshman");
$affil = array("Staff", "Faculty");

$data = array("Editor,Staff,Faculty,Senior,Junior,Sophomore,Freshman");
$senior_data = array("Editor,Count");
$junior_data = array("Editor,Count");
$sophomore_data = array("Editor,Count");

$file = fopen("./csv/editors.csv", "w");
$senior_file = fopen("./csv/senior_editors.csv", "w");
$junior_file = fopen("./csv/junior_editors.csv", "w");
$sophomore_file = fopen("./csv/sophomore_editors.csv", "w");

// Connecting, selecting database
$link = mysqli_connect('localhost', 'mchen6', '1234')
 or die('Could not connect: ' . mysql_error());
 //echo 'Connected successfully';
 mysqli_select_db($link, 'feb31') or die('Could not select database');

$vi_count = array();

for ($j = 0; $j < count($editors); $j++) { 
	
	$editor = $editors[$j];
	$row_data = "{$editor}"; 
	for ($i = 0; $i < count($affil); $i++){
	  $query = "select count(*) from ( select netid, command from process where command like \"{$editor} %\" union all select netid, command from process2 where command like \"{$editor} %\") P, user U where P.netid = U.netid and U.affiliation = \"{$affil[$i]}\";";
	  $result = mysqli_query($link, $query) or die('Query failed: ' . mysql_error());
	  // Printing results in HTML
	  if ($result->num_rows > 0 ){
		$row = mysqli_fetch_row($result);

	  // vi count
	  if ($j == 0){ 
	    array_push($vi_count, $row[0]); 
	  } 
	  // vim count
	  elseif ($j == 1) { 
	  	$count = (int)$row[0] + (int)$vi_count[$i];
		$row_data .= ",";
		$row_data .= (string)$count;
	  }
	  // emacs, gedit
	  else {
		$row_data .= ",{$row[0]}";
	  }
	  }
	  mysqli_free_result($result);
	}
	for ($i = 0; $i < count($class); $i++){ 
	  $query = "select count(*) from ( select netid, command from process where command like \"{$editor} %\" union all select netid, command from process2 where command like \"{$editor} %\") P, user U where P.netid = U.netid and U.classLevel = \"{$class[$i]}\";";
	  $result = mysqli_query($link, $query) or die('Query failed: ' . mysql_error());
	  // Printing results in HTML
	  if ($result->num_rows > 0 ){
		$row = mysqli_fetch_row($result);
 	    if ($j == 0){ 
	      array_push($vi_count, $row[0]); 
	    } elseif ($j == 1) { 
	  	  $count = (int)$row[0] + (int)$vi_count[$i + count($affil)];
		  $str_count = (string)$count;
		  $row_data .= ",{$str_count}";
		  // Don't need to write to file if count is 0
		  if ($count != 0){
		    // Senior
		    if ($i == 0){ 
		      array_push($senior_data, "{$editor},{$str_count}");
  		    } 
		    // Junior
		    elseif ($i == 1) {
		      array_push($junior_data, "{$editor},{$str_count}");
		    }
		    elseif ($i == 2) { 
		      array_push($sophomore_data, "{$editor},{$str_count}");
		    }
		  }
	    } else {
		  $row_data .= ",{$row[0]}";
		  if ((int)$row[0] != 0){
		    // Senior
		    if ($i == 0){ 
		      array_push($senior_data, "{$editor},{$row[0]}");
  		    } 
		    // Junior
		    elseif ($i == 1) {
		      array_push($junior_data, "{$editor},{$row[0]}");
		    }
		    elseif ($i == 2) { 
		      array_push($sophomore_data, "{$editor},{$row[0]}");
		    }
		  }
	    }

	  }
	  mysqli_free_result($result);
	}
	if ($j != 0){
	  array_push($data, $row_data);
	}
}
foreach ($data as $line){ fputcsv($file, explode(',',$line));}
foreach ($senior_data as $line){ fputcsv($senior_file, explode(',',$line));}
foreach ($junior_data as $line){ fputcsv($junior_file, explode(',',$line));}
foreach ($sophomore_data as $line){ fputcsv($sophomore_file, explode(',',$line));}

// Closing connection
mysqli_close($link);
fclose($file);
fclose($senior_file);
fclose($junior_file);
fclose($sophomore_file);
?>
