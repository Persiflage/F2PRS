<?php

	function multi($data, $options = array()) {
		$i = 0;
		foreach ($data as $value) {
			$tmp_value = str_replace(" ", "_", $value);
			$data[$i] = 'http://services.runescape.com/m=hiscore/index_lite.ws?player='.$tmp_value;
			$i++;
		}

		// array of curl handles
		$curly = array();
		// data to be returned
		$result = array();

		// multi handle
		$mh = curl_multi_init();

		// loop through $data and create curl handles
		// then add them to the multi-handle
		foreach ($data as $id => $d) {

			$curly[$id] = curl_init();

			$url = (is_array($d) && !empty($d['url'])) ? $d['url'] : $d;
			curl_setopt($curly[$id], CURLOPT_URL,            $url);
			curl_setopt($curly[$id], CURLOPT_HEADER,         0);
			curl_setopt($curly[$id], CURLOPT_RETURNTRANSFER, 1);


			// post?
			if (is_array($d)) {
				if (!empty($d['post'])) {
					curl_setopt($curly[$id], CURLOPT_POST,       1);
					curl_setopt($curly[$id], CURLOPT_POSTFIELDS, $d['post']);
				}
			}

			// extra options?
			if (!empty($options)) {
				curl_setopt_array($curly[$id], $options);
			}

			curl_multi_add_handle($mh, $curly[$id]);
		}

		// execute the handles
		$running = null;
		do {
			curl_multi_exec($mh, $running);
		} while($running > 0);

		// get content and remove handles
		foreach($curly as $id => $c) {
			$notFoundCheck = curl_getinfo($c, CURLINFO_HTTP_CODE);
			if($notFoundCheck == 404)
				$result[$id] = 404;
			else
				$result[$id] = curl_multi_getcontent($c);
			curl_multi_remove_handle($mh, $c);
		}

		// all done
		curl_multi_close($mh);

		return $result;
	}

?>
