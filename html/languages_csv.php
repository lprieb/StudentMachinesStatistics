<?php 

$languages = array("c", "cpp", "sh", "py", "html", "md", "go", "js", "java", "other");
$languages2 = array("c", "cpp", "sh", "py", "html", "md", "go", "js", "java");

// "f03", "*f93", "f90", "ss", "perl", "tex", "scm", "mips", "arm", "php", "bash", "csh" are not in this 
$class = array("Senior", "Junior", "Sophomore", "Freshman");
$affil = array("Staff", "Faculty");

$data = array("Language,Staff,Faculty,Senior,Junior,Sophomore,Freshman");
$senior_data = array("Language,Count");
$junior_data = array("Language,Count");
$sophomore_data = array("Language,Count");

$data2 = array("Language,Staff,Faculty,Senior,Junior,Sophomore,Freshman");
$senior_data2 = array("Language,Count");
$junior_data2 = array("Language,Count");
$sophomore_data2 = array("Language,Count");

// Opens csv files so you can write to them
$file = fopen("./csv/languages.csv", "w");
$senior_file = fopen("./csv/senior_languages.csv", "w");
$junior_file = fopen("./csv/junior_languages.csv", "w");
$sophomore_file = fopen("./csv/sophomore_languages.csv", "w");

$file2 = fopen("./csv/languages2.csv", "w");
$senior_file2 = fopen("./csv/senior_languages2.csv", "w");
$junior_file2 = fopen("./csv/junior_languages2.csv", "w");
$sophomore_file2 = fopen("./csv/sophomore_languages2.csv", "w");
// Connecting, selecting database
$link = mysqli_connect('localhost', 'mchen6', '1234')
 or die('Could not connect: ' . mysql_error());
 //echo 'Connected successfully';
 mysqli_select_db($link, 'feb31') or die('Could not select database');

$html_count = array(); 
$md_count = array();
$go_count = array();
$js_count = array();
$java_count = array();

for ($j = 0; $j < count($languages); $j++) { 
	$language = $languages[$j];
	$row_data = "{$language}"; 
	for ($i = 0; $i < count($affil); $i++){
	  $query = "select count(*) from ( select netid, command from process where command like \"%.{$language}\" union all select netid, command from process2 where command like \"%.{$language}\") P, user U where P.netid = U.netid and U.affiliation = \"{$affil[$i]}\";";
	  $result = mysqli_query($link, $query) or die('Query failed: ' . mysql_error());
	  // Printing results in HTML
	  if ($result->num_rows > 0 ){
		$row = mysqli_fetch_row($result);
        if ($j == 4){ 
	      array_push($html_count, $row[0]); 
	    }
        elseif ($j == 5){
          array_push($md_count, $row[0]);
        }
        elseif ($j == 6){
          array_push($go_count, $row[0]);
        }
        elseif ($j == 7){
          array_push($js_count, $row[0]);
        }
        elseif ($j == 8){
          array_push($java_count, $row[0]);
        }
        elseif ($j == 9){
          $count = (int)$row[0] + (int)$html_count[$i] + (int)$md_count[$i] + (int)$go_count[$i] + (int)$js_count[$i] + (int)$java_count[$i];
		  $row_data .= ",";
		  $row_data .= (string)$count;
        }
	  // other languages
        else{
          $row_data .= ",{$row[0]}";
        }
	  //}
	  }else{
	  $row_data .=",0";
	  }
	  mysqli_free_result($result);
	}
	for ($i = 0; $i < count($class); $i++){ 
	  $query = "select count(*) from ( select netid, command from process where command like \"%.{$language}\" union all select netid, command from process2 where command like \"%.{$language}\") P, user U where P.netid = U.netid and U.classLevel = \"{$class[$i]}\";";
	  $result = mysqli_query($link, $query) or die('Query failed: ' . mysql_error());
	  // Printing results in HTML
	  if ($result->num_rows > 0 ){
		$row = mysqli_fetch_row($result);
        if ($j == 4){ 
	      array_push($html_count, $row[0]); 
	    }
        elseif ($j == 5){
          array_push($md_count, $row[0]);
        }
        elseif ($j == 6){
          array_push($go_count, $row[0]);
        }
        elseif ($j == 7){
          array_push($js_count, $row[0]);
        }
        elseif ($j == 8){
          array_push($java_count, $row[0]);
        }
        elseif($j == 9){
          $count = (int)$row[0] + (int)$html_count[$i + count($affil)] + (int)$md_count[$i + count($affil)] + (int)$go_count[$i + count($affil)] + (int)$js_count[$i + count($affil)] + (int)$java_count[$i + count($affil)];
		  $str_count = (string)$count;
		  $row_data .= ",{$str_count}";
		  // Don't need to write to file if count is 0
		  if ($count != 0){
		    // Senior
		    if ($i == 0){ 
		      array_push($senior_data, "{$language},{$str_count}");
  		    } 
		    // Junior
		    elseif ($i == 1) {
		      array_push($junior_data, "{$language},{$str_count}");
		    }
		    elseif ($i == 2) { 
		      array_push($sophomore_data, "{$language},{$str_count}");
		    }
		  }
        }
        else{
		  $row_data .= ",{$row[0]}";
		  if ((int)$row[0] != 0){
		    // Senior
		    if ($i == 0){ 
		      array_push($senior_data, "{$language},{$row[0]}");
  		    } 
		    // Junior
		    elseif ($i == 1) {
		      array_push($junior_data, "{$language},{$row[0]}");
		    }
		    elseif ($i == 2) { 
		      array_push($sophomore_data, "{$language},{$row[0]}");
		    }
		  }
	    }

	  }
      else{
	  	$row_data .= ",0";
        //$row_data .= ",{$row[0]}";
		  if ((int)$row[0] != 0){
		    // Senior
		    if ($i == 0){ 
		      array_push($senior_data, "{$language},{$row[0]}");
  		    } 
		    // Junior
		    elseif ($i == 1) {
		      array_push($junior_data, "{$language},{$row[0]}");
		    }
		    elseif ($i == 2) { 
		      array_push($sophomore_data, "{$language},{$row[0]}");
		    }
          }
        
      }
	  mysqli_free_result($result);
	}
	array_push($data, $row_data);
}
foreach ($data as $line){ fputcsv($file, explode(',',$line));}
foreach ($senior_data as $line){ fputcsv($senior_file, explode(',',$line));}
foreach ($junior_data as $line){ fputcsv($junior_file, explode(',',$line));}
foreach ($sophomore_data as $line){ fputcsv($sophomore_file, explode(',',$line));}

for ($j = 0; $j < count($languages2); $j++) { 
	$language = $languages2[$j];
	$row_data = "{$language}"; 
	for ($i = 0; $i < count($affil); $i++){
	  $query = "select count(*) from ( select netid, command from process where command like \"%.{$language}\" union all select netid, command from process2 where command like \"%.{$language}\") P, user U where P.netid = U.netid and U.affiliation = \"{$affil[$i]}\";";
	  $result = mysqli_query($link, $query) or die('Query failed: ' . mysql_error());
	  // Printing results in HTML
	  if ($result->num_rows > 0 ){
		$row = mysqli_fetch_row($result);
	  // other languages
       //else{
          $row_data .= ",{$row[0]}";
        //}
	  //}
	  }else{
	  $row_data .=",0";
	  }
	  mysqli_free_result($result);
	}
	for ($i = 0; $i < count($class); $i++){ 
	  $query = "select count(*) from ( select netid, command from process where command like \"%.{$language}\" union all select netid, command from process2 where command like \"%.{$language}\") P, user U where P.netid = U.netid and U.classLevel = \"{$class[$i]}\";";
	  $result = mysqli_query($link, $query) or die('Query failed: ' . mysql_error());
	  // Printing results in HTML
	  if ($result->num_rows > 0 ){
		$row = mysqli_fetch_row($result);
        //else{
		  $row_data .= ",{$row[0]}";
		  if ((int)$row[0] != 0){
		    // Senior
		    if ($i == 0){ 
		      array_push($senior_data2, "{$language},{$row[0]}");
  		    } 
		    // Junior
		    elseif ($i == 1) {
		      array_push($junior_data2, "{$language},{$row[0]}");
		    }
		    elseif ($i == 2) { 
		      array_push($sophomore_data2, "{$language},{$row[0]}");
		    }
		  }
	    //}

	  }
      else{
	  	$row_data .= ",0";
        //$row_data .= ",{$row[0]}";
		  if ((int)$row[0] != 0){
		    // Senior
		    if ($i == 0){ 
		      array_push($senior_data2, "{$language},{$row[0]}");
  		    } 
		    // Junior
		    elseif ($i == 1) {
		      array_push($junior_data2, "{$language},{$row[0]}");
		    }
		    elseif ($i == 2) { 
		      array_push($sophomore_data2, "{$language},{$row[0]}");
		    }
          }
        
      }
	  mysqli_free_result($result);
	}
	array_push($data2, $row_data);
}
foreach ($data2 as $line){ fputcsv($file2, explode(',',$line));}
foreach ($senior_data2 as $line){ fputcsv($senior_file2, explode(',',$line));}
foreach ($junior_data2 as $line){ fputcsv($junior_file2, explode(',',$line));}
foreach ($sophomore_data2 as $line){ fputcsv($sophomore_file2, explode(',',$line));}

// Closing connection
mysqli_close($link);
fclose($file);
fclose($senior_file);
fclose($junior_file);
fclose($sophomore_file);
fclose($file2);
fclose($senior_file2);
fclose($junior_file2);
fclose($sophomore_file2);
?>
