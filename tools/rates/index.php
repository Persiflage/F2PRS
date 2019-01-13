<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>F2PRS / Tools / Rates</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.5.3/css/bulma.min.css">

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
              <h1 class="title">Rates</h1>
              <hr />

              <p class="content">This page shows the EHP rates used by F2PRS to calculate EHP and Skiller EHP. The format is <code>threshold:rate</code> where <code>rate</code> is the xp/h used at a skill experience of <code>threshold</code>. An extremely high rate value (ex: <code>166,636:199,833,365</code> for Magic) indicates the skill is 0 time from that point onwards, and is given a 1 hour buffer to achieve 200m.</p>

              <p class="content">Skilling outfits (like the Shark and Ethereal outfits), repeatable activites (like Daily Challenges and the Giant Oyster), and Bonus Experience Weekends are not included in any rates.</p>

              <?php

                require_once '../../assets/includes/php/rates.php';

                print_rates($rates, 0);
                echo "<hr />";
                print_rates($sk_rates, 1);

                function print_rates($rates, $type) {
                  if($type == 0)
                    echo '<h2 class="subtitle">Rates</h2>';
                  else
                    echo '<h2 class="subtitle">Skiller Rates</h2>';

                  echo '<aside class="menu">';

                  foreach($rates as $key => $skill) {

                    if(isset($skill)) {

                      echo '<p class="menu-label">'.$key.'</p>';
                      echo '<ul class="menu-list">';

                      for($thresh = 0; $thresh < count($skill); $thresh = $thresh + 2) {
                        $threshold = $skill[$thresh];
                        $rate = $skill[$thresh + 1];

                        echo '<li><a>'.number_format($threshold).': '.number_format($rate).'</a></li>';

                      }

                      echo '</ul></aside>';
                    }

                  }
                }

              ?>

            </div>
          </div>
        </div>
      </div>

    </section>

  </body>
</html>
