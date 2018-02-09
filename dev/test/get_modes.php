<?php

require_once "../../assets/includes/php/db.php";


$link = database_connect("f2prs_test");

$users = array();
$users2 = array();

/* Setup local SQL variable and code */
// $data = $link->query("SET @rank:=0");

$sql = "SELECT ehp AS skill, rsn
FROM hs WHERE mode = 1 OR mode = 2 OR mode = 3 OR mode = 4 ORDER BY ehp DESC
LIMIT 6, 5";

$sql2 = "SELECT sk_ehp AS skill, rsn
FROM hs WHERE mode = 2 ORDER BY sk_ehp DESC
LIMIT 0, 4";

$data = $link->query($sql);


while($info = $data->fetch_array(MYSQLI_ASSOC)) {
	array_push($users, $info);
}

$data = $link->query($sql2);

while($info = $data->fetch_array(MYSQLI_ASSOC)) {
	array_push($users2, $info);
}

/* Combine them in one array */
foreach($users as $tmp) {
	$combined[$tmp[rsn]] = $tmp[skill];
}

$result = array_merge($users, $users2);
ksort($result);

print_r($result);


?>
