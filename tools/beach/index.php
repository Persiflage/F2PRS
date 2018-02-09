<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>F2PRS / Tools / Beach Tracker</title>
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

              <h1 class="title">Beach Tracker</h1>
              <hr />
              <br />

              <table class="table is-fullwidth is-striped">
                <thead>
                  <tr>
                    <th>World</th>
                    <th>Activity</th>
                    <th>Changed</th>
                    <th>Update</th>
                  </tr>
                </thead>

                <tbody>


                  <?php

                    date_default_timezone_set('UTC');

                    require_once "../../assets/includes/php/db.php";

                    $link = database_connect();

                    $trees = $link->query("SELECT world, type, activity, UNIX_TIMESTAMP(time) as time FROM beach");

                    while($table = $trees->fetch_array(MYSQLI_ASSOC)) {
                      parse_world($table);
                    }

                    function parse_world($table) {

                      /* Parse World */
                      if($table["type"] == 1)
                        $world = '<i class="fa fa-globe legacy"></i> <span>'.$table["world"].'</span>';
                      else
                        $world = '<i class="fa fa-globe" style="visibility: hidden"></i> <span>'.$table["world"].'</span>';

                      /* Parse activity */
                      $activity = $table["activity"];
                      switch($activity) {
                        case "Rock Pools":
                          $activity = '<p class="has-text-info">Rock Pools</p>';
                          break;
                        case "Barbeque":
                          $activity = '<p class="has-text-danger">Barbeque</p>';
                          break;
                        case "Weight Training":
                          $activity = '<p class="has-text-primary">Weight Training</p>';
                          break;
                        case "Coconut Shy":
                          $activity = '<p class="has-text-success">Coconut Shy</p>';
                          break;
                        default:
                          $activity = '<p>'.$activity.'</p>';
                          break;
                      }

                      /* Parse status */
                      $current_timestamp = time();
                      $fifteen_minutes = 900;

                      $time = $table["time"];

                      if($current_timestamp > $time + $fifteen_minutes) {
                        /* Activity has switched, outdated */

                        $status = "Outdated";
                        $activity = "<p>N/A</p>";

                      } else {
                        /* Activity is ongoing */

                        $minutes = floor(($current_timestamp - $time) / 60);
                        $seconds = (($current_timestamp - $time) - ($minutes * 60));
                        $status = $minutes . "m " . $seconds . "s";
                      }


                      print_row($world, $activity, $status);

                    }

                    function print_row($world, $activity, $status) {
                      echo '<tr>';
                      echo '<td>'.$world.'</td>';
                      echo '<td>'.$activity.'</td>';
                      echo '<td>'.$status.'</td>';
                      echo '<td><div class="select is-light is-small"><select onchange="update(this);"><option>Select</option><option>Members</option><option>Rock Pools</option><option>Barbeque</option><option>Weight Training</option><option>Coconut Shy</option></select></div></td>';
                      echo '</tr>';
                    }

                  ?>

                </tbody>
              </table>

            </div>
          </div>
        </div>
      </div>

    </section>

    <script>

      function update(obj) {
        var row = obj.parentElement.parentElement.parentElement;
        var world = row.children[0].children[1].innerHTML;
        var dropdown = row.children[3].children[0].children[0];
        var activity = dropdown.options[dropdown.selectedIndex].value;

        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
            location.reload();
          }
        };

        console.log(world);

        xmlhttp.open("GET", "beach_update.php?world=" + world + "&activity=" + activity, true);
        xmlhttp.send();

      }

    </script>

  </body>
</html>
