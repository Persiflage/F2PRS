<!DOCTYPE html>
<html lang='en'>

	<head>
		<?php include_once 'assets/includes/html/head.html'; ?>
		<title>Gains | F2PRS</title>
	</head>

	<body>
		<?php
			/* Globals */
			$errors = array();
			require_once 'assets/includes/php/db.php';

			include_once 'assets/includes/html/nav.html';

			include_once 'assets/constants/gainsSkills.php';
		?>

		<h1>Top Gains</h1>

		<div class="content">
			<div class="tabs">
				<ul class="tab-links">
					<?php
						foreach ($gainsSkills as $key => $value): ?>
							<li id="tab-selector-<?= $value["name"] ?>">
								<a href="#<?= $value["name"] ?>">
									<img src="assets/images/icons/<?= $value["name"] ?>.png" class="tab_icon" />
								</a>
							</li>
					<?php endforeach; ?>
				</ul>
				<div class="tab-content">
					<?php
					   $link = database_connect();
					   foreach ($gainsSkills as $key => $value): ?>
							<div id='tab-<?= $value["name"] ?>' class='tab'>
								<?php
									$rankings = array("week", "month");
									foreach ($rankings as $rankingIndex => $rankingPeriod): ?>
										<div class='record_column'>
											<h1><?= ucfirst($rankingPeriod) ?></h1>
											<table class='record_table'>
												<?php
													$skillKey = $value['sql_key'];
													$data = $link->query("SELECT rsn, $skillKey FROM $rankingPeriod ORDER BY $skillKey desc LIMIT 10");
													$i = 1;
													while ($gains = $data->fetch_array(MYSQLI_BOTH)) {
														if ($gains[$skillKey] != 0) { ?>
															<tr>
																<td>
																	<?= $i ?>
																</td>
																<td>
																	<a href='track.php?player=<?= $gains[0] ?>'>
																		<?= $gains[0] ?>
																	</a>
																</td>
																<td>
																	<?= number_format($gains[1], $skillKey == "ehp" ? 1 : 0) ?>
																</td>
															</tr>
													<?php
														}
														$i++;
													}
												?>
											</table>
										</div>
								<?php
									endforeach;
								?>
							</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</body>
</html>
