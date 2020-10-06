<?php

/* A collection of functions relating to the hiscores database */

/**
 * Connect to the mySQL database and return a connection link.
 */

function database_connect() {
	/* Connect to database */
	if (!isset($_SERVER['HTTP_HOST'])) {
		// REPLACEME
		$link = mysqli_connect('127.0.0.1', 'username', 'password', 'f2prs');
	} else {
		if (startsWith($_SERVER['HTTP_HOST'], "localhost")) {
			// REPLACEME
			$link = mysqli_connect('127.0.0.1', 'username', 'password', "f2prs");
		} else {
			// REPLACEME
			$link = mysqli_connect('127.0.0.1', 'username', 'password', 'f2prs');
		}
	}

	/* Error catch */
	if(mysqli_connect_errno()) {
		echo "Database Connection Failed: ".mysqli_connect_errno();
		return null;
	} else {
		return $link;
	}
}

/**
 * Calculate skill ranks for a specific player.
 * @param player	The player being calculated
 * @param skills	A list of F2P skills
 * @param link		Link to the database connection
 */

function get_player_ranks($player, $stats, $link, $f2p_skills) {
	$ranks = array();

	foreach($f2p_skills as $skill) {
        $skill_xp = $skill . "_xp";
				$skill_global = "global_" . $skill;

				$sql = "WITH tmp AS (SELECT *, RANK() OVER (ORDER BY $skill_xp DESC, $skill_global ASC) ranking FROM hs) SELECT * FROM tmp WHERE rsn = '$player'";
        $data = $link->query($sql);
        $info = $data->fetch_array(MYSQLI_ASSOC);

        $stats[$skill]["rank"] = $info["ranking"];
    }

	/* Total Level Rank */
	$sql = "WITH tmp AS (SELECT *, RANK() OVER (ORDER BY f2p_total DESC, total_xp DESC) ranking FROM hs) SELECT * FROM tmp WHERE rsn = '$player'";
	$data = $link->query($sql);
	$info = $data->fetch_array(MYSQLI_ASSOC);
	$stats["total"]["level_rank"] = $info["ranking"];

	/* EHP and SK_EHP ranks */
	$sql = "WITH tmp AS (SELECT *, RANK() OVER (ORDER BY ehp DESC) ranking FROM hs) SELECT * FROM tmp WHERE rsn = '$player'";
	$data = $link->query($sql);
	$info = $data->fetch_array(MYSQLI_ASSOC);
	$stats["ehp"]["rank"] = $info["ranking"];

	$sql = "WITH tmp AS (SELECT *, RANK() OVER (ORDER BY sk_ehp DESC) ranking FROM hs WHERE mode = 2) SELECT * FROM tmp WHERE rsn = '$player'";
	$data = $link->query($sql);
	$info = $data->fetch_array(MYSQLI_ASSOC);
	$stats["sk_ehp"]["rank"] = $info["ranking"];

	return $stats;
}

/**
 * Calculate ranks for specific skill:
 * ranks $start through $start+25
 */

function get_skill_ranks($skill, $start, $mode) {

	/* ranks[25] array for relevant skill */
	$ranks = array();

	/* TODO Pass these as arguments? */
	/* Global database connection */
	$link = $GLOBALS['link'];

	/* Helpful naming conventions */
	$skill_xp = $skill."_xp";
	$skill_global = "global_".$skill;

	/* Regular Mode should not exclude Skillers or Ironmen */
	if($mode == 1)
		$mode = "1 OR mode = 2 OR mode = 3 OR mode = 4";

	switch($skill) {
		case "ehp":
		case "sk_ehp":
			$sql = "SELECT rsn, $skill AS xp
			FROM hs WHERE mode = $mode ORDER BY $skill DESC
			LIMIT $start, 25";
			break;
		case "combined_ehp":
			//$SQL
			break;
		case "total_xp":
			$sql = "SELECT rsn, $skill AS xp
			FROM hs WHERE mode = $mode ORDER BY $skill DESC
			LIMIT $start, 25";
			break;
		case "total":
			$sql = "SELECT rsn, f2p_total AS skill, $skill_xp AS xp
			FROM hs WHERE mode = $mode ORDER BY f2p_total DESC, $skill_xp DESC
			LIMIT $start, 25";
			break;
		default:
			$sql = "SELECT rsn, $skill_xp AS xp, $skill AS skill
			FROM hs WHERE mode = $mode ORDER BY $skill_xp DESC, $skill_global ASC
			LIMIT $start, 25";
			break;
	}

	/* Execute SQL */
	$data = $link->query($sql);

	/* Error check */
	if($data != TRUE)
		echo "Database Error (get_skill_ranks): ".$link->error;

	while($info = $data->fetch_array(MYSQLI_ASSOC)) {
		array_push($ranks, $info);
	}

	return $ranks;
}

/**
 * Return an array of all users in the database.
 */

function get_all_users($link) {
	$data = $link->query("SELECT rsn FROM hs");
	$users = array();
	while($tmp = $data->fetch_array(MYSQLI_ASSOC)) {
		array_push($users, $tmp['rsn']);
	}

	return $users;
}

/**
 * Return a count of all users in the database.
 */

function get_total_users($link) {
	$data = $link->query("SELECT COUNT(*) as count FROM hs");
	$tmp = $data->fetch_array(MYSQLI_ASSOC);

	return $tmp['count'];
}

function startsWith( $haystack, $needle ) {
	$length = strlen( $needle );
	return substr( $haystack, 0, $length ) === $needle;
}

?>
