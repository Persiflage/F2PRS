<!DOCTYPE html>
<html lang='en'>

	<head>
		<?php include_once 'assets/includes/html/head.html'; ?>


		<?php

			require_once 'assets/includes/php/db.php';

			/* Global variables */
			$errors = array();
			$skills_f2p = array("total", "attack", "defence", "strength", "constitution", "ranged", "prayer", "magic", "cooking", "woodcutting", "fletching", "fishing", "firemaking", "crafting", "smithing", "mining", "runecrafting", "dungeoneering", "runescore", "ehp", "sk_ehp", "combined_ehp", "total_xp");

			/* Parse URL/POST/GET variables */
			$args = array(
				'player' => array(
								'filter' => FILTER_SANITIZE_STRING,
								'flags'  => FILTER_FLAG_NO_ENCODE_QUOTES
							),
				'skill'  => array(
								'filter' => FILTER_SANITIZE_STRING,
								'flags'  => FILTER_FLAG_NO_ENCODE_QUOTES
							),
				'page'   => array(
								'filter' => FILTER_SANITIZE_NUMBER_INT
							),
				'mode'	 => array(
								'filter' => FILTER_SANITIZE_NUMBER_INT
							)
			);
			$_GET = filter_input_array(INPUT_GET, $args);

			$skill = parse_skill($_GET['skill']);
			$player = parse_player($_GET['player']);
			$page = parse_page($_GET['page']);
			$mode = parse_mode($_GET['mode']);

			/* flag: true if 2 columns, false if 3 */
			$column_flag = false;

			switch($skill) {
				case "ehp":
					$skill_title = "EHP";
					$unit_title = "EHP";
					$column_flag = true;
					break;
				case "sk_ehp":
					$skill_title = "Skiller EHP";
					$unit_title = "EHP";
					$column_flag = true;
					break;
				case "combined_ehp":
					$skill_title = "Combined EHP";
					$unit_title = "EHP";
					$column_flag = true;
					break;
				case "runescore":
					$skill_title = "Achievements";
					$unit_title = "Score";
					$column_flag = true;
					break;
				case "total_xp":
					$skill_title = "Total XP";
					$unit_title = "XP";
					$column_flag = true;
					break;
				default:
					$skill_title = ucwords($skill);
					$unit_title = "XP";
			}

			echo "<title>$skill_title | $page</title>";

		?>
	</head>

	<body>
		<?php include_once 'assets/includes/html/nav.html'; ?>

		<div>
			<img src="assets/images/icons/<?php echo $skill; ?>.png" style="width: 25px; height: 25px;"/>
			<h1 style="display: inline"> <?php echo $skill_title; ?> </h1>
			<div id='skill_dropdown' class='fa fa-caret-down'></div>
		</div>

		<?php include_once 'assets/includes/html/skillmenu.html'; ?>

		<table class='hiscore_table'>
			<tr>
				<th></th>
				<th style='text-align: left'>Player</th>
				<?php if(!$column_flag) { ?><th style='text-align: right'>Level</th><?php } ?>
				<th style='text-align: right'><?= $unit_title ?></th>
			</tr>


		<?php
			/* BEGIN PAGE GENERATION */

			/* Connect to database */
			$link = database_connect();

			/* Helpful variables for generalizing SQL code */
			$skill_name = $skill;
			if($skill == "total")
				$skill_name = "f2ptotal";
			$skill_xp = $skill."_xp";
			$skill_global = "global_".$skill;

			/* Calculate start player rank from page # */
			$start = ($page - 1) * 25;

			/* Get ranks */
			$ranks = get_skill_ranks($skill, $start, $mode);

			$idx = 1;

			foreach($ranks as $rank_tmp) {

				/* If &player, highlight the row */
				if($player == strtolower($rank_tmp['rsn']))
					echo "<tr class='tr_selected'>";
				else
					echo '<tr>';

				/* Print rank # */
				$lol = (($page-1) * 25) + $idx;
				echo '<td>'.number_format($lol);

				/* Print rsn */
				echo "<td><a href='track.php?player=$rank_tmp[rsn]'>$rank_tmp[rsn]</a></td>";

				/* Print level */
				if(!$column_flag)
					echo "<td>".$rank_tmp['skill']."</td>";

				/* Print XP */
				if($skill == "ehp" || $skill == "sk_ehp")
					echo "<td>".number_format($rank_tmp['xp'], 2)."</td>";
				else
					echo "<td>".number_format($rank_tmp['xp'])."</td>";

				echo "</tr>";

				$idx++;
			}

			echo "</table>";


			/* Calculate total pages for pagination */
			$users = $link->query("SELECT COUNT(*) AS count FROM hs");
			$users = $users->fetch_array(MYSQLI_ASSOC);
			$users = $users['count'];
			$max_page = ceil($users / 25);

			/* PAGINATION ???? */

						if($page < 4) {
							echo "<div class='page_select'>";

							if($page == 1) {
								echo "<div class='disabled'><p><i class='fa fa-arrow-left'></i></p></div>";
								echo "<a href='hiscores.php?skill=$skill&mode=$mode&page=1'><div class='page_active'><p>1</p></div></a>";
							} else {
								echo "<a href='hiscores.php?skill=$skill&mode=$mode&page=".($page - 1)."'><div><p><i class='fa fa-arrow-left'></i></p></div></a>";
								echo "<a href='hiscores.php?skill=$skill&mode=$mode&page=1'><div><p>1</p></div></a>";
							}

							if($page == 2) { echo "<a href='hiscores.php?skill=$skill&mode=$mode&page=2'><div class='page_active'><p>2</p></div></a>"; }
							else { echo "<a href='hiscores.php?skill=$skill&mode=$mode&page=2'><div><p>2</p></div></a>"; }

							if($page == 3) { echo "<a href='hiscores.php?skill=$skill&mode=$mode&page=3'><div class='page_active'><p>3</p></div></a>"; }
							else { echo "<a href='hiscores.php?skill=$skill&mode=$mode&page=3'><div><p>3</p></div></a>"; }

							if($page == 4) { echo "<a href='hiscores.php?skill=$skill&mode=$mode&page=4'><div class='page_active'><p>4</p></div></a>"; }
							else { echo "<a href='hiscores.php?skill=$skill&mode=$mode&page=4'><div><p>4</p></div></a>"; }

							if($page == 5) { echo "<a href='hiscores.php?skill=$skill&mode=$mode&page=5'><div class='page_active'><p>5</p></div></a>"; }
							else { echo "<a href='hiscores.php?skill=$skill&mode=$mode&page=5'><div><p>5</p></div></a>"; }

							if($page == 6) { echo "<a href='hiscores.php?skill=$skill&mode=$mode&page=6'><div class='page_active'><p>6</p></div></a>"; }
							else { echo "<a href='hiscores.php?skill=$skill&mode=$mode&page=6'><div><p>6</p></div></a>"; }

							if($page == 7) { echo "<a href='hiscores.php?skill=$skill&mode=$mode&page=7'><div class='page_active'><p>7</p></div></a>"; }
							else { echo "<a href='hiscores.php?skill=$skill&mode=$mode&page=7'><div><p>7</p></div></a>"; }

							echo "<div><form action='hiscores.php'><input type='hidden' name='skill' value='$skill' /><input id='page_search' type='text' maxlength='2' name='page' placeholder='...' /></form></div>";

							echo "<a href='hiscores.php?skill=$skill&mode=$mode&page=$max_page'><div><p>$max_page</p></div></a>";
							echo "<a href='hiscores.php?skill=$skill&mode=$mode&page=".($page + 1)."'><div><p><i class='fa fa-arrow-right'></i></p></div></a>";
							echo "</div>";
						}

						else if($page > 3 && $page < $max_page - 2) {
							echo "<div class='page_select'>";
							echo "<a href='hiscores.php?skill=$skill&mode=$mode&page=".($page - 1)."'><div><p><i class='fa fa-arrow-left'></i></p></div></a>";
							echo "<a href='hiscores.php?skill=$skill&mode=$mode&page=1'><div><p>1</p></div></a>";
							echo "<div><form action='hiscores.php'><input type='hidden' name='skill' value='$skill' /><input id='page_search' type='text' maxlength='2' name='page' placeholder='...' /></form></div>";
							echo "<a href='hiscores.php?skill=$skill&mode=$mode&page=".($page - 2)."'><div><p>".($page - 2)."</p></div></a>";
							echo "<a href='hiscores.php?skill=$skill&mode=$mode&page=".($page - 1)."'><div><p>".($page - 1)."</p></div></a>";
							echo "<div class='page_active'><p>$page</p></div>";
							echo "<a href='hiscores.php?skill=$skill&mode=$mode&page=".($page + 1)."'><div><p>".($page + 1)."</p></div></a>";
							echo "<a href='hiscores.php?skill=$skill&mode=$mode&page=".($page + 2)."'><div><p>".($page + 2)."</p></div></a>";
							echo "<div><form action='hiscores.php'><input type='hidden' name='skill' value='$skill' /><input id='page_search' type='text' maxlength='2' name='page' placeholder='...' /></form></div>";
							echo "<a href='hiscores.php?skill=$skill&mode=$mode&page=$max_page'><div><p>$max_page</p></div></a>";
							echo "<a href='hiscores.php?skill=$skill&mode=$mode&page=".($page + 1)."'><div><p><i class='fa fa-arrow-right'></i></p></div></a>";
							echo "</div>";
						}

						else if($page > $max_page - 3) {
							echo "<div class='page_select'>";
							echo "<a href='hiscores.php?skill=$skill&mode=$mode&page=".($page - 1)."'><div><p><i class='fa fa-arrow-left'></i></p></div></a>";
							echo "<a href='hiscores.php?skill=$skill&mode=$mode&page=1'><div><p>1</p></div></a>";
							echo "<div><form action='hiscores.php'><input type='hidden' name='skill' value='$skill' /><input id='page_search' type='text' maxlength='2' name='page' placeholder='...' /></form></div>";
							echo "<a href='hiscores.php?skill=$skill&mode=$mode&page=".($max_page - 6)."'><div><p>".($max_page - 6)."</p></div></a>";
							echo "<a href='hiscores.php?skill=$skill&mode=$mode&page=".($max_page - 5)."'><div><p>".($max_page - 5)."</p></div></a>";
							echo "<a href='hiscores.php?skill=$skill&mode=$mode&page=".($max_page - 4)."'><div><p>".($max_page - 4)."</p></div></a>";
							echo "<a href='hiscores.php?skill=$skill&mode=$mode&page=".($max_page - 3)."'><div><p>".($max_page - 3)."</p></div></a>";

							if($page == $max_page - 2) { echo "<a href='hiscores.php?skill=$skill&mode=$mode&page=".($max_page - 2)."'><div class='page_active'><p>".($max_page - 2)."</p></div></a>"; }
							else { echo "<a href='hiscores.php?skill=$skill&mode=$mode&page=".($max_page - 2)."'><div><p>".($max_page - 2)."</p></div></a>"; }

							if($page == $max_page - 1) { echo "<a href='hiscores.php?skill=$skill&mode=$mode&page=".($max_page - 1)."'><div class='page_active'><p>".($max_page - 1)."</p></div></a>"; }
							else { echo "<a href='hiscores.php?skill=$skill&mode=$mode&page=".($max_page - 1)."'><div><p>".($max_page - 1)."</p></div></a>"; }

							if($page == $max_page) {
								echo "<a href='hiscores.php?skill=$skill&mode=$mode&page=$max_page'><div class='page_active'><p>$max_page</p></div></a>";
								echo "<div class='disabled'><p><i class='fa fa-arrow-right'></i></p></div>";
							} else {
								echo "<a href='hiscores.php?skill=$skill&mode=$mode&page=$max_page'><div><p>$max_page</p></div></a>";
								echo "<a href='hiscores.php?skill=$skill&mode=$mode&page=".($page + 1)."'><div><p><i class='fa fa-arrow-right'></i></p></div></a>";
							}
							echo "</div>";

						}




			?>

		</body>
</html>







<?php



	/* ___FUNCTIONS___ */

	/* ___PARSE URL___ */

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

	/* ___EXIT___ */

	function graceful_exit() {
		foreach($GLOBALS['errors'] as $s) {
			echo $s."\n";
		}

		exit();
	}


?>
