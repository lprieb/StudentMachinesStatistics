<?php
class TableRows extends RecursiveIteratorIterator { 
    function __construct($it) { 
        parent::__construct($it, self::LEAVES_ONLY); 
    }

    function current() {
        return "<td>" . parent::current(). "</td>";
    }

    function beginChildren() { 
        echo "<tr>"; 
    } 

    function endChildren() { 
        echo "</tr>" . "\n";
    } 
} 
$servername = "localhost";
$username = "lprieb";
$password = "open1234";
$key = $_GET['t'];
try {
    $conn = new PDO("mysql:host=$servername;dbname=feb31", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e)
{
	die("Connection to database failed: " . $e->getMessage());
}

$sql = Array();
$sql["mProcesses"] = "select netid, count(*) c from process2 GROUP BY netid ORDER BY c DESC LIMIT 10;";
$sql["avgCpu"] = "select netid, average_rss rss, average_vsz vsz,average_cpu_usage cpu, average_mem_usage mem,command from process2 ORDER BY cpu DESC LIMIT 10;";
$sql["avgMem"] = "select netid, average_rss rss, average_vsz vsz,average_cpu_usage cpu, average_mem_usage mem,command from process2 ORDER BY mem DESC LIMIT 10;";
$sql["avgVsz"] = "select netid, average_rss rss, average_vsz vsz,average_cpu_usage cpu, average_mem_usage mem,command from process2 ORDER BY vsz DESC LIMIT 10;";
$sql["avgRss"] = "select netid, average_rss rss, average_vsz vsz,average_cpu_usage cpu, average_mem_usage mem,command from process2 ORDER BY rss DESC LIMIT 10;";
$sql["maxCpu"] = "select netid, max_rss rss, max_vsz vsz,max_cpu_usage cpu, max_mem_usage mem,command from process2 ORDER BY cpu DESC LIMIT 10;";
$sql["maxMem"] = "select netid, max_rss rss, max_vsz vsz,max_cpu_usage cpu, max_mem_usage mem,command from process2 ORDER BY mem DESC LIMIT 10;";
$sql["maxVsz"] = "select netid, max_rss rss, max_vsz vsz,max_cpu_usage cpu, max_mem_usage mem,command from process2 ORDER BY vsz DESC LIMIT 10;";
$sql["maxRss"] = "select netid, max_rss rss, max_vsz vsz,max_cpu_usage cpu, max_mem_usage mem,command from process2 ORDER BY rss DESC LIMIT 10;";

try {
    $stmt = $conn->prepare($sql[$key]); 
    $stmt->execute();

    // set the resulting array to associative
    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC); 
	echo '<div class="table-responsive">';
	echo '<table class="table table-striped">';
	echo '<thead>';
	echo '<tr>';
	
	if($key === "mProcesses"){
		echo '<th>Netid</th>';
		echo '<th>Number of Processes</th>';
	}
	else if(substr($key,0,3) ==="avg"){
		echo '<th>Netid</th>';
		echo '<th>Average RSS</th>';
		echo '<th>Average VSZ</th>';
		echo '<th>Average CPU Usage</th>';
		echo '<th>Average Mem Usage</th>';
		echo '<th>Command</th>';
	}
	else{
		echo '<th>Netid</th>';
		echo '<th>Max RSS</th>';
		echo '<th>Max VSZ</th>';
		echo '<th>Max CPU Usage</th>';
		echo '<th>Max Mem Usage</th>';
		echo '<th>Command</th>';
	}
	echo '</tr>';
	echo '</thead>';
	echo '<tbody>';
    foreach(new TableRows(new RecursiveArrayIterator($stmt->fetchAll())) as $k=>$v) { 
        echo $v;
    }
	echo '</tbody>';
	echo '</table>';
	echo '</div>';
}
catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}

?>


