<?php


	$f2p_skills = array("total", "attack", "defence", "strength", "constitution", "ranged", "prayer", "magic", "cooking", "woodcutting", "fletching", "fishing", "firemaking", "crafting", "smithing", "mining", "runecrafting", "dungeoneering");

	$f2p_skills_with_ehp = array("total", "attack", "defence", "strength", "constitution", "ranged", "prayer", "magic", "cooking", "woodcutting", "fletching", "fishing", "firemaking", "crafting", "smithing", "mining", "runecrafting", "dungeoneering", "ehp", "sk_ehp");

	function calc_page($rank) {
		return ceil($rank/25);
	}

	function is_p2p($stats) {
		$MAX_F2P_TOTAL = 1750;

		if(
		$stats['herblore']['level'] != 1 ||
		$stats['agility']['level'] != 1 ||
		$stats['thieving']['level'] != 1 ||
		$stats['slayer']['level'] != 1 ||
		$stats['farming']['level'] != 1 ||
		$stats['hunter']['level'] != 1 ||
		$stats['construction']['level'] != 1 ||
		$stats['summoning']['level'] != 1 ||
		$stats['divination']['level'] != 1 ||
		$stats['invention']['level'] != 0 ||
		$stats['total']['level'] > $MAX_F2P_TOTAL) {
			return true;
		} else
			return false;
	}

	function get_mode($stats) {
		if($stats["attack"]["xp"] == 0 && $stats["defence"]["xp"] == 0 && $stats["strength"]["xp"] == 0 && $stats["constitution"]["xp"] == 0 && $stats["ranged"]["xp"] == 0 && $stats["prayer"]["xp"] == 0 && $stats["magic"]["xp"] == 0)
			return 2;
		else
			return 1;
	}

	function calc_f2p_total($stats) {
		$NUM_P2P_SKILLS = 10;
		return $stats["attack"]["level"] + $stats["defence"]["level"] + $stats["strength"]["level"] + $stats["constitution"]["level"] + $stats["ranged"]["level"] + $stats["prayer"]["level"] + $stats["magic"]["level"] + $stats["cooking"]["level"] + $stats["woodcutting"]["level"] + $stats["fletching"]["level"] + $stats["fishing"]["level"] + $stats["firemaking"]["level"] + $stats["crafting"]["level"] + $stats["smithing"]["level"] + $stats["mining"]["level"] + $stats["runecrafting"]["level"] + $stats["dungeoneering"]["level"] + $NUM_P2P_SKILLS;
	}

	function calc_sk_total($stats) {
		$NUM_P2P_SKILLS = 10;
		return $stats["cooking"]["level"] + $stats["woodcutting"]["level"] + $stats["fletching"]["level"] + $stats["fishing"]["level"] + $stats["firemaking"]["level"] + $stats["crafting"]["level"] + $stats["smithing"]["level"] + $stats["mining"]["level"] + $stats["runecrafting"]["level"] + $stats["dungeoneering"]["level"] + $NUM_P2P_SKILLS + 16;
	}


	/* ___PARSE URL___ */

	function parse_player($player) {

		if(!$player || $player == "") {
			array_push($GLOBALS['errors'], "parse_player: No player specified, no highlighting");
		}

		$skill_delimeter = array("'", "\"");
		$player = str_replace($skill_delimeter, "", $player);

		$player_delimiter = array("-", "_", " ");
		$player = str_replace($player_delimiter, " ", $player);

		$player = trim($player);
		$player = preg_replace('/[^\w -]+/', '', $player);

		return strtolower($player);
	}

	/* ___EXIT___ */

	function graceful_exit($message) {
		echo $message;
		echo "</body></html>";
		exit();
	}



?>
