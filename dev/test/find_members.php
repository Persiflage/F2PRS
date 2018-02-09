<?php

require_once "../../assets/includes/rue/rue.php";
require_once "../../assets/includes/php/db.php";


$link = database_connect("f2prs_test");
$r = new \Rue\rs_api();
$r->set_pug("daim@daim.com", "altsalts", "Daim");
$users = array();

/* Select users from DB */
$data = $link->query("SELECT rsn FROM hs");

/* Add to a big array of users to check */
while($info = $data->fetch_array(MYSQLI_ASSOC)) {
	array_push($users, $info['rsn']);
}

/* Check them (rue does chunking) */
$result = $r->get_multi_details($users, true);

/* Parse results */
$flag = false;
foreach($result as $entry) {
	if($entry->member != "") {
		echo $entry->name."<br/>";
		$flag = true;
	}
}

if(!$flag) {
	echo "No P2P users in database!";
}




?>
