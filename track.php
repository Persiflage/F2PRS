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

			echo "<title>$player | Track</title>";

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

			if($data->num_rows != 1)
				graceful_exit("This user was not found in the database. <a href='add.php?player=$player'>Add them?</a>");

			/* Player checks out -- query stats */
			require_once 'assets/includes/php/get_stats.php';
			require_once 'assets/includes/php/rates.php';
			require_once 'assets/includes/php/ehp.php';
			require_once 'assets/includes/php/db.php';

			$f2p_skills = array("total", "attack", "defence", "strength", "constitution", "ranged", "prayer", "magic", "cooking", "woodcutting", "fletching", "fishing", "firemaking", "crafting", "smithing", "mining", "runecrafting", "dungeoneering");

			$f2p_skills_with_ehp = array("total", "attack", "defence", "strength", "constitution", "ranged", "prayer", "magic", "cooking", "woodcutting", "fletching", "fishing", "firemaking", "crafting", "smithing", "mining", "runecrafting", "dungeoneering", "ehp", "sk_ehp");

			/* Gets stats and virtual levels */
			$stats = get_stats($player);

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

			/* Calculate weekly and monthly gain */
			$stats = calc_gains($player, $stats, $link, $f2p_skills_with_ehp);

			/* Store stats into database */
			update_hs($stats, $link, $player, $f2p_skills);

			/* Calculate skill ranks for player and add to skills[] */
			$stats = get_player_ranks($player, $stats, $link, $f2p_skills);

			/* Display the table */
			print_table($stats, $player, $f2p_skills_with_ehp);

		?>

	</body>
</html>

<?php

	/* ___FUNCTIONS___ */

	/* ___PRINT___ */

	function print_table($stats, $player, $f2p_skills) {

		/* Setup name box */
		echo "<div class='top_box'><h1 class='username'>$player</h1></div><br/><br/><br/>";

		/* Setup table tags */
		echo "<table class='track_table'><tr><th></th><th>Level</th><th>XP</th><th>Rank</th><th>Week</th><th>Month</th></tr>";

		foreach($f2p_skills as $skill) {
			/* Begin row */
			echo "<tr>";

			/* Skill image */
			echo "<td style=\"background-image: url('assets/images/icons/$skill.png')\"></td>";

			/* Skill level */
			if(array_key_exists("virtual", $stats))
				echo "<td class='virt'>".$stats[$skill]['virtual']."</td>";
			else if($skill == "total")
				echo "<td><div class='tooltip'>".$stats['f2p_total']."<span class='tooltiptext'>".$stats['total']['level']."</span></div></td>";
			else if($skill == "ehp" || $skill == "sk_ehp")
				echo "<td></td>";
			else
				echo "<td>".$stats[$skill]["level"]."</td>";

			/* Skill xp */
			if($skill == "ehp" || $skill == "sk_ehp")
				echo "<td>".number_format($stats[$skill]['xp'], 2)."</td>";
			else
				echo "<td>".number_format($stats[$skill]['xp'])."</td>";

			/* Skill rank */
			if($skill == "total")
			echo "<td><a href='hiscores.php?skill=$skill&player=$player&page=".calc_page($stats[$skill]['level_rank'])."'>".number_format($stats[$skill]['level_rank'])."</a> (<a href='hiscores.php?skill=total_xp&player=$player&page=".calc_page($stats[$skill]['rank'])."'>".$stats[$skill]['rank']."</a>)</td>";
			else if($skill == "sk_ehp")
				echo "<td><a href='hiscores.php?skill=$skill&mode=2&player=$player&page=".calc_page($stats[$skill]['rank'])."'>".number_format($stats[$skill]['rank'])."</a></td>";
			else
				echo "<td><a href='hiscores.php?skill=$skill&player=$player&page=".calc_page($stats[$skill]['rank'])."'>".number_format($stats[$skill]['rank'])."</a></td>";

			/* Week gain */
			if($stats[$skill]['week'] > 0) {
				if($skill == "ehp" || $skill == "sk_ehp")
					echo "<td class='gain'>".number_format($stats[$skill]['week'], 2)."</td>";
				else
					echo "<td class='gain'>".number_format($stats[$skill]['week'])."</td>";
			}else {
				if($skill == "ehp" || $skill == "sk_ehp")
					echo "<td>".number_format($stats[$skill]['week'], 2)."</td>";
				else
					echo "<td>".number_format($stats[$skill]['week'])."</td>";
			}

			/* Month gain */
			if($stats[$skill]['month'] > 0) {
				if($skill == "ehp" || $skill == "sk_ehp")
					echo "<td class='gain'>".number_format($stats[$skill]['month'], 2)."</td>";
				else
					echo "<td class='gain'>".number_format($stats[$skill]['month'])."</td>";
			}else {
				if($skill == "ehp" || $skill == "sk_ehp")
					echo "<td>".number_format($stats[$skill]['month'], 2)."</td>";
				else
					echo "<td>".number_format($stats[$skill]['month'])."</td>";
			}

			/* BXPW gain */
			// if($stats[$skill]['bxpw'] > 0) {
			// 	if($skill == "ehp" || $skill == "sk_ehp")
			// 		echo "<td class='gain'>".number_format($stats[$skill]['bxpw'], 2)."</td>";
			// 	else
			// 		echo "<td class='gain'>".number_format($stats[$skill]['bxpw'])."</td>";
			// }else {
			// 	if($skill == "ehp" || $skill == "sk_ehp")
			// 		echo "<td>".number_format($stats[$skill]['bxpw'], 2)."</td>";
			// 	else
			// 		echo "<td>".number_format($stats[$skill]['bxpw'])."</td>";
			// }

		}
	}

	/* ___DATABASE___ */

	function calc_gains($player, $stats, $link, $f2p_skills) {

		/* Weekly */
		$week = $link->query("SELECT * FROM week_stats WHERE rsn='$player'");
		$data = $week->fetch_array(MYSQLI_ASSOC);

		foreach($f2p_skills as $skill) {
			if($skill == "ehp" || $skill == "sk_ehp")
				$skill_xp = $skill;
			else
				$skill_xp = $skill . "_xp";

			$gain = $stats[$skill]["xp"] - $data[$skill_xp];
			$stats[$skill]["week"] = $gain;

			/* Update weekly gain */
			$link->query("UPDATE week SET $skill_xp = $gain WHERE rsn='$player'");
		}

		/* Monthly */
		$month = $link->query("SELECT * FROM month_stats WHERE rsn='$player'");
		$data = $month->fetch_array(MYSQLI_ASSOC);

		foreach($f2p_skills as $skill) {
			if($skill == "ehp" || $skill == "sk_ehp")
				$skill_xp = $skill;
			else
				$skill_xp = $skill . "_xp";

			$gain = $stats[$skill]["xp"] - $data[$skill_xp];
			$stats[$skill]["month"] = $gain;

			/* Update monthly gain */
			$link->query("UPDATE month SET $skill_xp = $gain WHERE rsn='$player'");
		}

		// /* BXPW */
		// $bxpw = $link->query("SELECT * FROM bxpw_stats WHERE rsn='$player'");
		// $data = $bxpw->fetch_array(MYSQLI_ASSOC);
		//
		// foreach($f2p_skills as $skill) {
		// 	if($skill == "ehp" || $skill == "sk_ehp")
		// 		$skill_xp = $skill;
		// 	else
		// 		$skill_xp = $skill . "_xp";
		//
		// 	$gain = $stats[$skill]["xp"] - $data[$skill_xp];
		// 	$stats[$skill]["bxpw"] = $gain;
		//
		// 	/* Update BXPW gain */
		// 	//$link->query("UPDATE bxpw SET $skill_xp = $gain WHERE rsn='$player'");
		// }


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
