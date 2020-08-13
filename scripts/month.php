<?php

	/* Ensure script can finish */
	set_time_limit(0);

	/* Globals */
	$errors = array();

	/* Dependencies */
	require_once '../assets/includes/php/db.php';
	require_once '../assets/includes/php/multi.php';
	require_once '../assets/includes/php/get_stats.php';
	require_once '../assets/includes/rue/rue.php';
	require_once '../assets/includes/php/rates.php';
	require_once '../assets/includes/php/ehp.php';
	require_once '../assets/includes/php/functions.php';


	/* Connect to database */
	$link = database_connect();

	/* Start parsing players */

	$i = 0;

	/* Select all users */
	$users = $link->query("SELECT rsn FROM hs");

	/* Add them to a names array */
	while($data = $users->fetch_array(MYSQLI_ASSOC)) {
	   $names[$i] = $data['rsn'];
	   $i++;
	}

	/* Loop through 10 users at a time */
	for($i = 0; $i < count($names); $i = $i + 10) {

		/* Store each user in multiArray to pass to multi() */
	   for($j = 0; $j < 10; $j++) {
			   $multiArray[$j] = $names[$i + $j];
	   }

	   /* Get multi stats */
	   $curlReturn = multi($multiArray);

	   /* Loop through each of 10 current users to parse */
	   for($j = 0; $j < 10; $j++) {

		   /* User not found on hiscores, delete */
		   if($curlReturn[$j] == "404") {
			   delete_user($multiArray[$j], $link);
		   } else {
			   if($curlReturn[$j] != "") {
				   echo "User: ".$multiArray[$j]."<br/>";
				   parse_function($multiArray[$j], $curlReturn[$j]);
			   }
		   }
	   }
	}

	echo "Finished parsing users, so that's cool!";





?>






<?php

	/* ___FUNCTIONS___ */

	/* ___PARSE___ */


	function parse_function($player, $raw) {

		global $link, $rates, $sk_rates;

		$f2p_skills = array("total", "attack", "defence", "strength", "constitution", "ranged", "prayer", "magic", "cooking", "woodcutting", "fletching", "fishing", "firemaking", "crafting", "smithing", "mining", "runecrafting", "dungeoneering", "runescore");

		$f2p_skills_with_ehp = array("total", "attack", "defence", "strength", "constitution", "ranged", "prayer", "magic", "cooking", "woodcutting", "fletching", "fishing", "firemaking", "crafting", "smithing", "mining", "runecrafting", "dungeoneering", "runescore", "ehp", "sk_ehp");

		/* Gets stats and virtual levels */
		$stats = parse_raw_stats($player, $raw);

		// if($stats == null)
			// curl returned null -- player doesn't exist on RSHS
		// if(is_p2p($stats))
			// stats are of a P2P player -- remove from database

		$stats["ehp"]["xp"] = ehp($stats, $rates, 0);
		$stats["sk_ehp"]["xp"] = ehp($stats, $sk_rates, 0);

		$stats["f2p_total"] = calc_f2p_total($stats);
		$stats["sk_total"] = calc_sk_total($stats);

		/* Want to check mode everytime in case skiller */
		/* changes to main or ironman de-irons etc....  */
		$stats["mode"] = get_mode($stats);

		/* Store stats into database */
		update_hs($stats, $link, $player, $f2p_skills);

		/* Reset week */
		reset_month($player, $stats, $link, $f2p_skills_with_ehp);

	}

	function delete_user($player) {
		global $link;
		echo "Deleting $player.<br/>";
		$link->query("DELETE FROM hs WHERE rsn='$player'");
		$link->query("DELETE FROM week WHERE rsn='$player'");
		$link->query("DELETE FROM month WHERE rsn='$player'");
		$link->query("DELETE FROM week_stats WHERE rsn='$player'");
		$link->query("DELETE FROM month_stats WHERE rsn='$player'");
	}

	function reset_month($player, $stats, $link, $f2p_skills_with_ehp) {
		/* update month */
		foreach($f2p_skills_with_ehp as $skill) {
			if($skill == "ehp" || $skill == "sk_ehp" || $skill == "runescore")
				$skill_xp = $skill;
			else
				$skill_xp = $skill . "_xp";

			$xp = $stats[$skill]["xp"];

			$link->query("UPDATE month_stats SET $skill_xp = $xp WHERE rsn='$player'");
		}

		/* reset gains */
		$link->query("UPDATE month SET
			total_xp=0,
			attack_xp=0,
			defence_xp=0,
			strength_xp=0,
			constitution_xp=0,
			ranged_xp=0,
			prayer_xp=0,
			magic_xp=0,
			cooking_xp=0,
			woodcutting_xp=0,
			fletching_xp=0,
			fishing_xp=0,
			firemaking_xp=0,
			crafting_xp=0,
			smithing_xp=0,
			mining_xp=0,
			runecrafting_xp=0,
			dungeoneering_xp=0,
			runescore=0,
			ehp=0, sk_ehp=0 WHERE rsn='$player'"
		);
	}

	function calc_gains($player, $stats, $link, $f2p_skills) {

		/* Weekly */
		$week = $link->query("SELECT * FROM week_stats WHERE rsn='$player'");
		$data = $week->fetch_array(MYSQLI_ASSOC);

		foreach($f2p_skills as $skill) {
			if($skill == "ehp" || $skill == "sk_ehp" || $skill == "runescore")
				$skill_xp = $skill;
			else
				$skill_xp = $skill . "_xp";

			$gain = $stats[$skill]["xp"] - $data[$skill_xp];
			$stats[$skill]["week"] = $gain;

			/* Update weekly gain */
			//$link->query("UPDATE week SET $skill_xp = $gain WHERE rsn='$player'");
		}

		/* Monthly */
		$month = $link->query("SELECT * FROM month_stats WHERE rsn='$player'");
		$data = $month->fetch_array(MYSQLI_ASSOC);

		foreach($f2p_skills as $skill) {
			if($skill == "ehp" || $skill == "sk_ehp" || $skill == "runescore")
				$skill_xp = $skill;
			else
				$skill_xp = $skill . "_xp";

			$gain = $stats[$skill]["xp"] - $data[$skill_xp];
			$stats[$skill]["month"] = $gain;

			/* Update monthly gain */
			//$link->query("UPDATE month SET $key = $gain WHERE rsn='$player'");
		}

		return $stats;
	}

	function update_hs($stats, $link, $player, $f2p_skills) {

		/* Update all the skill levels, xp, and global ranks */
		foreach($f2p_skills as $skill) {
			$skill_xp = $skill."_xp";
			$skill_global = "global_".$skill;

			$level = $stats[$skill]['level'];
			$xp = $stats[$skill]['xp'];
			$global = $stats[$skill]['global'];

			$sql = "UPDATE hs SET $skill = $level,
			$skill_xp = $xp,
			$skill_global = $global
			WHERE rsn='$player'";
			$link->query($sql);
		}

		/* Now do special cases */
		$ehp = $stats['ehp']['xp'];
		$skehp = $stats['sk_ehp']['xp'];
		$link->query("UPDATE hs SET
		ehp = $ehp,
		sk_ehp = $skehp,
		f2p_total = $stats[f2p_total],
		sk_total = $stats[sk_total],
		mode = $stats[mode] WHERE rsn = '$player'");
	}

?>
