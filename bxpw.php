<!DOCTYPE html>
<html lang='en'>

	<head>
		<?php include_once 'assets/includes/html/head.html'; ?>


		<?php

			/* Globals */
			$errors = array();

			require_once 'assets/includes/php/db.php';

			echo "<title>BXPW | F2PRS</title>";

		?>
	</head>

	<body>
		<?php include_once 'assets/includes/html/nav.html'; ?>


	<h1>BXPW</h1>

	<div class="content">
	<div class="tabs">
	    <ul class="tab-links">
	        <li class="active"><a href="#tab1"><img src="assets/images/icons/total.png" class="tab_icon" /></a></li>
	        <li><a href="#tab2"><img src="assets/images/icons/attack.png" class="tab_icon" /></a></li>
	        <li><a href="#tab3"><img src="assets/images/icons/defence.png" class="tab_icon" /></a></li>
	        <li><a href="#tab4"><img src="assets/images/icons/strength.png" class="tab_icon" /></a></li>
   	     	<li><a href="#tab5"><img src="assets/images/icons/constitution.png" class="tab_icon" /></a></li>
   	     	<li><a href="#tab6"><img src="assets/images/icons/ranged.png" class="tab_icon" /></a></li>
   	     	<li><a href="#tab7"><img src="assets/images/icons/prayer.png" class="tab_icon" /></a></li>
   	     	<li><a href="#tab8"><img src="assets/images/icons/magic.png" class="tab_icon" /></a></li>
   	     	<li><a href="#tab9"><img src="assets/images/icons/cooking.png" class="tab_icon" /></a></li>
   	     	<li><a href="#tab10"><img src="assets/images/icons/woodcutting.png" class="tab_icon" /></a></li>
   	     	<li><a href="#tab11"><img src="assets/images/icons/fishing.png" class="tab_icon" /></a></li>
   	     	<li><a href="#tab12"><img src="assets/images/icons/firemaking.png" class="tab_icon" /></a></li>
   	     	<li><a href="#tab13"><img src="assets/images/icons/crafting.png" class="tab_icon" /></a></li>
   	     	<li><a href="#tab14"><img src="assets/images/icons/smithing.png" class="tab_icon" /></a></li>
   	     	<li><a href="#tab15"><img src="assets/images/icons/mining.png" class="tab_icon" /></a></li>
   	     	<li><a href="#tab16"><img src="assets/images/icons/runecrafting.png" class="tab_icon" /></a></li>
   	     	<li><a href="#tab17"><img src="assets/images/icons/dungeoneering.png" class="tab_icon" /></a></li>
   	     	<li><a href="#tab18"><img src="assets/images/icons/ehp.png" class="tab_icon" /></a></li>
   	 	</ul>

 	   <div class="tab-content">


		<?php


			/* BEGIN PAGE GENERATION */

			$link = database_connect();

			$order = array("total_xp", "attack_xp", "defence_xp", "strength_xp", "constitution_xp", "ranged_xp", "prayer_xp", "magic_xp", "cooking_xp",
			"woodcutting_xp", "fletching_xp", "fishing_xp", "firemaking_xp", "crafting_xp", "smithing_xp", "mining_xp", "runecrafting_xp", "dungeoneering_xp",
			"ehp");

			$tab_num = 1;

			foreach($order as $key => $value) { // LOOP THROUGH SKILLS
				if($tab_num == 1)
					echo "<div id='tab$tab_num' class='tab active'>\n";
				else
					echo "<div id='tab$tab_num' class='tab'>\n";

				$i = 1;

				$data = $link->query("SELECT rsn, $value FROM bxpw ORDER BY $value desc LIMIT 10");
				echo "<div class='record_column' id='bxpw_table'>\n";
				echo "<h1>September 2017</h1>\n";
				echo "<table class='record_table'>\n";
				while($gains = $data->fetch_array(MYSQLI_ASSOC)) {
					if($gains[$value] != 0) {
						if($value != "ehp")
							echo "<tr><td>$i</td><td><a href='track.php?player=$gains[rsn]'>$gains[rsn]</a></td><td>+".number_format($gains[$value])."</td></tr>\n";
						else
							echo "<tr><td>$i</td><td><a href='track.php?player=$gains[rsn]'>$gains[rsn]</a></td><td>+".number_format($gains[$value], 1)."</td></tr>\n";
						}
					$i++;
				}
				echo "</table>";
				echo "</div>";

				echo "</div>\n";

				$tab_num++;
			}


		?>

	</body>
</html>
