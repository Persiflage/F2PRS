<?php

	require_once 'rue.php';

	$r = new \Rue\rs_api();
	$r->set_pug("daim@daim.com", "altsalts", "Daim");

	$result = $r->get_player_skills("Mr Tyr", true);


	print_r($result);




?>
