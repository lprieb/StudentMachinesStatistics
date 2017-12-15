<?php 

$languages = array("c", "cpp", "sh", "py", "html", "md", "go", "js", "java");
// "f03", "*f93", "f90", "ss", "perl", "tex", "scm", "mips", "arm", "php", "bash", "csh" are not in this 
$groups = array("Staff", "Faculty", "Senior", "Junior", "Sophomore", "Freshman");

// Create link
$file = fopen("./csv/languages2.csv", "r");

for ($i = 0; $i < count($languages); $i++){

    echo "<tr>";
    echo "<td class\"col-md-3\">{$languages[$i]}</td>";
    $line = fgets($file);
    $split = explode(",", $line);
    if($i == 0){
        $line = fgets($file);
        $split = explode(",", $line);
    }
    for ($j = 1; $j < count($split); $j++){
        echo "<td class=\"col-md-3\">{$split[$j]}</td>";
    }
    echo "</tr>";
}


echo "</tr>";
fclose($file);
// Closing connection
?>