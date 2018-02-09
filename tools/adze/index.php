<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>F2PRS / Tools / Adze</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.5.3/css/bulma.min.css">

    <link rel="stylesheet" type="text/css" href="../css/tools.css">
    <link rel="shortcut icon" href="images/favicon.ico">
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

              <div class="content">

                <div class="has-text-centered">
                  <a href="."><img src="images/adze.png"></a>
                </div>
                <br />

                <form action="." method="post">
                  <div class="field has-addons">
                    <div class="control is-expanded">
                      <input class="input is-danger" style="border-color: #e8590c" type="text" placeholder="Search for a player..." name="player">
                    </div>
                    <div class="control">
                      <input class="button is-danger" type="submit" style="background-color: #e8590c; color: #fff">
                    </div>
                  </div>
                </form>

                <br />
                <hr />
                <br />

                <?php if(!isset($_POST["player"])) { ?>

                <p>Adze is a simple tool used to gather as much information as possible about a RuneScape account. When a player is searched, we gather the following information:</p>

                <h2 class="subtitle">RuneScape</h2>

                <ul>
                  <li>Account Type (Normal, Ironman, Hardcore Ironman)</li>
                  <li>RuneMetrics Profile Status (Public or Hidden)</li>
                  <li>All RuneMetrics information, including Quests and Activity</li>
                  <li>Player Avatar</li>
                  <li>Stats</li>
                  <li>Clan</li>
                </ul>


                <h2 class="subtitle">Old School RuneScape</h2>

                <ul>
                  <li>Account Type (Normal, Ironman, Ultimate Ironman)</li>
                  <li>Main Game, Deadman Mode, and Seasonal Deadman Mode Stats</li>
                  <li>Previous Name (CML)</li>
                </ul>

                <?php

                  } else {

                    function print_activity($activity) {
                      if(empty($activity)) {
                        echo('<div class="card"><div class="card-content has-text-centered"><div>No activity.</div></div></div>');
                        return;
                      }

                      foreach(array_slice($activity, 0, 5) as $value) {
                        echo ('<div class="card"><div class="card-content"><div class="content">');
                        echo $value->details;

                        echo('<div>'.$value->date.'</div>');

                        echo('</div></div></div>');
                      }


                    }

                    function print_stats($stats_rue) {
                      $order = array("Attack", "Constitution", "Mining", "Strength", "Agility", "Smithing", "Defence", "Herblore", "Fishing", "Ranged", "Thieving", "Cooking", "Prayer", "Crafting", "Firemaking", "Magic", "Fletching", "Woodcutting", "Runecrafting", "Slayer", "Farming", "Construction", "Hunter", "Summoning", "Dungeoneering", "Divination", "Invention");
                      $stats = array();

                      foreach($stats_rue as $key => $value) {
                        if(isset($value->level))
                          $stats[$value->name] = $value->level;
                      }

                      echo('<div class="card">
                        <div class="card-content">');

                      $i = 1;
                      foreach($order as $value) {
                        if($i % 3 == 1)
                          echo('<div class="columns has-text-centered">');

                        echo('<div class="column"><img src="../../assets/images/icons/'.strtolower($value).'.png"> '.$stats[$value].'</div>');

                        if($i % 3 == 0)
                          echo('</div>');

                        $i++;
                      }

                      echo('</div></div>');
                    }

                    require_once "../../assets/includes/php/functions.php";
                    require_once 'rue.php';
                    $r = new \Rue\rs_api();
                    $r->set_pug("daim@daim.com", "altsalts", "Daim");

                    /* Parse player */
                    $player = ucwords(parse_player($_POST["player"]));

                    /* Determine RuneMetrics profile status & icon */
                    $runemetrics_icon = '<i class="fa fa-unlock-alt has-text-success"></i>';
                    $player_activity = $r->get_player_activity($player);

                    if($player_activity == "PROFILE_PRIVATE")
                      $runemetrics_icon = '<i class="fa fa-lock has-text-danger"></i>';

                    /* Parse clan */
                    $clan = $r->get_player_details($player);
                    if(!isset($clan->clan))
                      $clan = "";
                    else
                      $clan = $clan->clan;

                    /* Make stats table */
                    $stats = $r->get_player_hiscores($player);


                ?>

              </div>

              <div class="card">
                <div class="card-content">
                  <div class="media">
                    <div class="media-left">
                      <figure class="image is-48x48">
                        <img src="<?php echo $r->get_player_avatar($player); ?>">
                      </figure>
                    </div>
                    <div class="media-content">
                      <p class="title is-4"><?php echo $player; ?></p>
                      <p class="subtitle is-6"><?php echo $clan; ?></p>
                    </div>
                  </div>
                </div>
                <footer class="card-footer">
                  <p class="card-footer-item">
                    <span>
                    <a href="https://apps.runescape.com/runemetrics/app/overview/player/<?php echo $player; ?>" target="_blank">RuneMetrics: <?php echo $runemetrics_icon; ?></a>
                    </span>
                  </p>
                  <p class="card-footer-item">
                    <span>
                      Account Type: <?php echo $r->get_player_account_type($player); ?>
                    </span>
                  </p>
                </footer>
              </div>

              <?php print_stats($stats); ?>


              <?php print_activity($player_activity); ?>



              <?php } ?>


            </div>
          </div>
        </div>
      </div>

    </section>

  </body>
</html>
