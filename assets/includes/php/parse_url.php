<?php

	function parse_player($player) {

		if(!$player || $player == "") {
			array_push($GLOBALS['errors'], "parse_player: No player specified, no highlighting");
		}

		$skill_delimeter = array("'", "\"");
		$player = str_replace($skill_delimeter, "", $player);

		$player_delimiter = array("-", "_", " ");
		$player = str_replace($player_delimiter, " ", $player);

		return strtolower($player);
	}

	function parse_skill($skill) {

		/* Empty or invalid skill */
		if(!$skill || $skill == "") {
			array_push($GLOBALS['errors'], "parse_skill: No skill specified, default total");
			$skill = "total";
		}

		/* Ensure no HTML exploits */
		$skill_delimeter = array("'", "\"");
		$skill = str_replace($skill_delimeter, "", $skill);

		/* Lowercase skill before in_array check */
		$skill = strtolower($skill);

		/* Check if skill is actually an F2P skill */
		if(!in_array($skill, $GLOBALS['skills_f2p'])) {
			array_push($GLOBALS['errors'], "parse_skill: Invalid skill");
			$skill = "total";
		}

		return $skill;
	}

	function parse_page($page) {

		if(!$page || $page == "") {
			array_push($GLOBALS['errors'], "parse_page: No page specified, default 1");
			$page = 1;
		}

		if($page < 1)
			$page = 1;

		return $page;
	}

	function parse_mode($mode) {

		if(!$mode || $mode == "") {
			array_push($GLOBALS['errors'], "parse_mode: No mode specified, default 1");
			$mode = 1;
		}

		if($mode < 1 || $mode > 4)
			$mode = 1;

		return $mode;
	}








?>
