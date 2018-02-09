<!DOCTYPE html>
<html lang='en'>

	<head>
		<?php include_once 'assets/includes/html/head.html'; ?>


		<?php

			/* Globals */
			$errors = array();

			require_once 'assets/includes/php/db.php';
			require_once 'assets/includes/php/functions.php';

			/* Parse URL/POST/GET variables */
			$args = array(
				'player' => array(
								'filter' => FILTER_SANITIZE_STRING,
								'flags'  => FILTER_FLAG_NO_ENCODE_QUOTES
							)
			);
			$_GET = filter_input_array(INPUT_GET, $args);

			$player = parse_player($_GET['player']);
			$player = ucwords($player);

			echo "<title>$player | Add</title>";

		?>
	</head>

	<body>
		<?php include_once 'assets/includes/html/nav.html'; ?>


		<?php


			/* BEGIN PAGE GENERATION */

			/* Connect to database */
			$link = database_connect();

			/* Check if user is banned */
			$data = $link->query("SELECT rsn FROM banlist WHERE rsn='$player'");

			if($data->num_rows != 0)
				graceful_exit("This user is banned from F2PRS.");

			/* Check if user exists in database */
			$data = $link->query("SELECT rsn FROM hs WHERE rsn='$player'");

			if($data->num_rows == 1)
				graceful_exit("This user already exists in our database.");

			/* RuneMetrics checks featuring rue : https://github.com/dreadnip/rue */
			require_once 'assets/includes/rue/rue.php';

			$r = new \Rue\rs_api();
			$r->set_pug("daim@daim.com", "altsalts", "Daim");

			/* Check if player has RuneMetrics activity */
			$result = $r->get_player_activity($player);
			if($result == "PROFILE_PRIVATE")
				graceful_exit("This user has a private RuneMetrics profile.");
			else if($result == "NO_PROFILE")
				graceful_exit("This user does not exist on the hiscores.");
			else if($result == "Unable to fetch profile")
			    graceful_exit("This user's RuneMetrics profile could not be retrieved. Try again later.");
			else if(!empty($result))
			    graceful_exit("This user has RuneMetrics activity.");


			/* Player checks out -- query stats */
			require_once 'assets/includes/php/get_stats.php';
			require_once 'assets/includes/php/ehp.php';
			require_once 'assets/includes/php/rates.php';
			require_once 'assets/includes/php/db.php';
			require_once 'assets/includes/php/functions.php';


			/* Gets stats and virtual levels */
			$stats = get_stats($player);


			/* curl returned null, player doesn't exist on RSHS */
			if($stats == null)
				graceful_exit("This player doesn't exist on the hiscores.");
			if(is_p2p($stats))
				graceful_exit("This player has P2P stats.");


			$stats["ehp"]["xp"] = ehp($stats, $rates, 0);
			$stats["sk_ehp"]["xp"] = ehp($stats, $sk_rates, 0);

			$stats["f2p_total"] = calc_f2p_total($stats);
			$stats["sk_total"] = calc_sk_total($stats);



			/* Want to check mode everytime in case skiller */
			/* changes to main or ironman de-irons etc....  */
			$stats["mode"] = get_mode($stats);

			/* Insert into tables */
			insert_database($player, $stats, $link, $f2p_skills_with_ehp);
			update_hs($stats, $link, $player, $f2p_skills);

			echo "<a href='track.php?player=$player'>$player</a> has been added.";

		?>

	</body>
</html>




<?php

	/* ___FUNCTIONS___ */


	/* ___DATABASE___ */

	function insert_database($player, $stats, $link, $f2p_skills_with_ehp) {

		/* Defaults will handle values */
		$link->query("INSERT INTO hs (rsn) VALUES('$player')");
		$link->query("INSERT INTO week (rsn) VALUES('$player')");
		$link->query("INSERT INTO month (rsn) VALUES('$player')");

		/* Insert stats into week_stats */
		$link->query("INSERT INTO week_stats (rsn) VALUES('$player')");

		foreach($f2p_skills_with_ehp as $skill) {

			if($skill == "ehp" || $skill == "sk_ehp")
				$skill_xp = $skill;
			else
				$skill_xp = $skill . "_xp";

			$tmp = $stats[$skill]['xp'];
			$sql = "UPDATE week_stats SET $skill_xp = $tmp WHERE rsn='$player'";
			$link->query($sql);
		}

		/* Insert stats into month_stats */
		$link->query("INSERT INTO month_stats (rsn) VALUES('$player')");
		foreach($f2p_skills_with_ehp as $skill) {

			if($skill == "ehp" || $skill == "sk_ehp")
				$skill_xp = $skill;
			else
				$skill_xp = $skill . "_xp";

			$tmp = $stats[$skill]['xp'];
			$sql = "UPDATE month_stats SET $skill_xp = $tmp WHERE rsn='$player'";
			$link->query($sql);
		}

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
