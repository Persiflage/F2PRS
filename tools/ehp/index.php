<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>F2PRS / Tools / EHP</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.5.3/css/bulma.min.css">

    <link rel="stylesheet" type="text/css" href="../css/tools.css">
    <link rel="shortcut icon" href="../../assets/images/favicon.png">
  </head>

  <body>

    <section class="hero">

      <nav class="navbar" role="navigation" aria-label="main navigation">
        <div class="navbar-brand">
          <div class="navbar-item">
            <a href="../" class="is-danger"><i class="fa fa-chevron-left" style="color: #000;"></i></a>
          </div>
        </div>
      </nav>

      <div class="hero-body">
        <div class="container">
          <div class="columns">
            <div class="column is-6 is-offset-3">

              <h1 class="title">EHP</h1>
              <hr />

              <?php if(!isset($_GET["player"])) { ?>

              <br />
              <form action="." method="get">
                <div class="field has-addons">
                  <div class="control is-expanded">
                    <input class="input is-dark" type="text" name="player" placeholder="Enter a username...">
                  </div>
                  <div class="control">
                    <input class="button is-dark" type="submit">
                  </div>
                </div>
              </form>
              <br />
              <hr />

              <?php

              } else {

                require_once "../../assets/includes/php/functions.php";

                $player = parse_player($_GET["player"]);

                require_once "../../assets/includes/php/db.php";

                $link = database_connect();
                $count = $link->query("SElECT rsn FROM hs WHERE rsn='$player'");

              ?>

              <br />

              <form action="." method="get">
                <div class="field has-addons">
                  <div class="control is-expanded">
                    <input class="input is-dark" type="text" name="player" placeholder="Enter a username...">
                  </div>
                  <div class="control">
                    <input class="button is-dark" type="submit">
                  </div>
                </div>
              </form>

              <br />
              <hr />

              <?php

                if($count->num_rows == 0) {
                  exit("This player is not being tracked by F2PRS.");
                } else {

                  echo '<h1 class="title">'.ucwords($player).'</h1>';

                  require_once '../../assets/includes/php/get_stats.php';
                  require_once '../../assets/includes/php/rates.php';
                  require_once '../../assets/includes/php/ehp.php';

                  $f2p_skills = array("total", "attack", "defence", "strength", "constitution", "ranged", "prayer", "magic", "cooking", "woodcutting", "fletching", "fishing", "firemaking", "crafting", "smithing", "mining", "runecrafting", "dungeoneering");

                  $p2p_skills = array("herblore", "agility", "thieving", "slayer", "farming", "hunter", "construction", "summoning", "divination", "invention");

                  $stats = get_stats($player);

                  $stats = ehp($stats, $rates, 1);

                  /* Remove P2P skils */
                  foreach($p2p_skills as $skill) {
                      unset($stats[$skill]);
                  }

                  $ehps = array();
                  foreach($stats as $skill => $row) {
                      if(!array_key_exists("ehp", $row))
                          $ehps[$skill] = 0;
                      else
                          $ehps[$skill] = $row["ehp"];
                  }

                  array_multisort($ehps, SORT_DESC);

                  foreach($ehps as $skill => $value) {
                    $skill_percent = ($value / $ehps["total"]) * 100;
                    $skill_percent = round($skill_percent, 2);
                    $skill_ehp = $value;
                    $skill_ehp = round($skill_ehp, 2);

                    print_skill($skill, $skill_percent, $skill_ehp);
                  }
                }
              }

              function print_skill($skill, $percent, $ehp) {
                echo '<nav class="level">';
                echo '<div class="level-left">';
                echo '<p class="level-item">'.ucwords($skill).'</p>';
                echo '</div>';
                echo '<div class="level-right">';
                echo '<p class="level-item">'.$ehp.' EHP</p>';
                echo '<p class="level-item">('.$percent.'%)</p>';
                echo '</div></nav>';

                echo '<progress class="progress"  value="'.$percent.'" max="100" id="'.$skill.'">'.$percent.'</progress>';
              }

              ?>

            </div>
          </div>
        </div>
      </div>

    </section>

  </body>
</html>
