<html>
<body>
<?php 
$netid = $_GET["netid"]; 
// Connecting, selecting database
$link = mysqli_connect('localhost', 'mchen6', '1234')
 or die('Could not connect: ' . mysql_error());
 //echo 'Connected successfully';
 mysqli_select_db($link, 'feb31') or die('Could not select database');
 // Performing SQL query
 $query = "SELECT command, average_cpu_usage, average_mem_usage, average_rss, average_vsz  FROM process2 where netid = \"{$netid}\"";
 $result = mysqli_query($link, $query) or die('Query failed: ' . mysql_error());
 // Printing results in HTML
 if ($result->num_rows > 0 ){
  echo "<h3>Number of tracked processes: {$result->num_rows}</h3>";
  echo "<table class=\"table table-striped\">\n";
  echo "<thead>";
  echo "<th>Command</th><th>Avg CPU Usage</th><th>Avg Mem Usage</th><th>Avg RSS</th><th>Avg VSZ</th>";
  echo "</thead>";
  while ($tuple = mysqli_fetch_array($result, MYSQL_ASSOC)) {
   echo "\t<tr>\n";
   foreach ($tuple as $col_value) {
    echo "\t\t<td>$col_value</td>\n";
   }
   echo "\t</tr>\n";
  }
  echo "</table>\n";
 }
// Free resultset
mysqli_free_result($result);
// Closing connection
mysqli_close($link);
?>
</body>
</html>
