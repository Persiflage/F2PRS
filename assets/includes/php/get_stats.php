<?php

	function curl($player) {
		$player = str_replace(" ", "_", $player);
		$url = "https://services.runescape.com/m=hiscore/index_lite.ws?player=$player";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, false);
		$cURL = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if ($httpCode == 404) {
				return null;
		} else {
			return $cURL;
		}
	}

	function parse_raw_stats($player, $raw) {

		$order = array("total", "attack", "defence", "strength", "constitution", "ranged", "prayer", "magic", "cooking", "woodcutting", "fletching", "fishing", "firemaking", "crafting", "smithing", "mining", "herblore", "agility", "thieving", "slayer", "farming", "runecrafting", "hunter", "construction", "summoning", "dungeoneering", "divination", "invention");
		$stats = array();

		/* Player wasn't found, return null */
		if($raw == null)
			return null;

		$raw = explode("\n", str_replace("-1", "0", $raw));

		$i = 0;
		foreach($order as $key) {
			$split = explode(",", $raw[$i]);

			$stats[$key]["global"] = $split[0];
			$stats[$key]["level"] = $split[1];
			$stats[$key]["xp"] = $split[2];

			/* Setup a virtual field IF xp > level 100 */
			if($split[2] > 14391160 && $key != "total")
				$stats[$key]["virtual"] = calc_virtual($split[2]);

			$i++;
		}

		if($stats["constitution"]["level"] == 1)
			$stats["constitution"]["level"] = 10;

		return $stats;
	}

	function get_stats($player) {

		$order = array("total", "attack", "defence", "strength", "constitution", "ranged", "prayer", "magic", "cooking", "woodcutting", "fletching", "fishing", "firemaking", "crafting", "smithing", "mining", "herblore", "agility", "thieving", "slayer", "farming", "runecrafting", "hunter", "construction", "summoning", "dungeoneering", "divination", "invention");
		$stats = array();

		$raw = curl($player);

		/* Player wasn't found, return null */
		if($raw == null)
			return null;

		$raw = explode("\n", str_replace("-1", "0", $raw));

		$i = 0;
		foreach($order as $key) {
			$split = explode(",", $raw[$i]);

			$stats[$key]["global"] = $split[0];
			$stats[$key]["level"] = $split[1];
			$stats[$key]["xp"] = $split[2];

			/* Setup a virtual field IF xp > level 100 */
			if($split[2] > 14391160 && $key != "total")
				$stats[$key]["virtual"] = calc_virtual($split[2]);

			$i++;
		}

		if($stats["constitution"]["level"] == 1)
			$stats["constitution"]["level"] = 10;

		return $stats;
	}

	function calc_virtual($skillXP) {

		$levelArray = array(0, 0, 83, 174, 276, 388, 512, 650, 801, 969, 1154, 1358, 1584, 1833, 2107, 2411, 2746, 3115, 3523, 3973, 4470, 5018, 5624, 6291, 7028, 7842, 8740, 9730, 10824, 12031, 13363, 14833, 16456, 18247, 20224, 22406, 24815, 27473, 30408, 33648, 37224, 41171, 45529, 50339, 55649, 61512, 67983, 75127, 83014, 91721, 101333, 111945, 123660, 136594, 150872, 166636, 184040, 203254, 224466, 247886, 273742, 302288, 333804, 368599, 407015, 449428, 496254, 547953, 605032, 668051, 737637, 814445, 899257, 992895, 1096278, 1210421, 1336443, 1475581, 1629200, 1798808, 1986068, 2192818, 2421087, 2673114, 2951373, 3258594, 3597792, 3972294, 4385776, 4842295, 5346332, 5902831, 6517253, 7195629, 7944614, 8771558, 9684577, 10692629, 11805606, 13034431, 14391160, 15889109, 17542976, 19368992, 21385073, 23611006, 26068632, 28782069, 31777943, 35085654, 38737661, 42769801, 47221641, 52563718, 57563718, 63555443, 70170840, 77474828, 85539082, 94442737, 104273167, 115126838, 127110260, 140341028, 154948977, 171077457, 188884740, 200000000);
		$i = 99;
		$skillLev = 99;

		if ($skillXP == 200000000) {
			return 126;
		}

		for ($i = 99; $i <= 127; $i++) {
			if ($skillXP < $levelArray[$i]) {
				$skillLev = $i - 1;
				break;
			}
		}

		return $skillLev;
	}








?>
