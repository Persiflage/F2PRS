<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>F2PRS / Tools / Evil Tree Tracker</title>
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

              <h1 class="title">Evil Tree Tracker</h1>
              <hr />
              <br />

              <table class="table is-fullwidth is-striped">
                <thead>
                  <tr>
                    <th>World</th>
                    <th>State</th>
                    <th>Tier</th>
                    <th>Spawn Time</th>
                    <th></th>
                  </tr>
                </thead>

                <tbody>

                  <?php

                    date_default_timezone_set('UTC');

                    require_once "../../assets/includes/php/db.php";

                    $link = database_connect();

                    $trees = $link->query("SELECT world, type, state, tier, UNIX_TIMESTAMP(spawn) as spawn FROM trees");

                    while($table = $trees->fetch_array(MYSQLI_ASSOC)) {
                      parse_world($table);
                    }

                    function format_world($world, $type) {
                      if($type == 1)
                        return '<i class="fa fa-globe legacy"></i> '.$world;
                      else
                        return '<i class="fa fa-globe" style="visibility: hidden"></i> '.$world;
                    }

                    function format_spawn($spawn_timestamp) {
                      if($spawn_timestamp != NULL) {
                        $spawn = date("H:i:00", $spawn_timestamp);
                        $spawn_hour = explode(":", $spawn)[0];
                        $spawn_min = explode(":", $spawn)[1];
                      } else {
                        $spawn_hour = "";
                        $spawn_min = "";
                      }

                      return '<div class="field has-addons"><p class="control" style="width: 35px"><input class="input is-small is-light" type="tel" placeholder="HH" maxlength="2" value="'.$spawn_hour.'"></p><p class="control" style="width: 35px"><input class="input is-small is-light" type="tel" placeholder="MM" maxlength="2" value="'.$spawn_min.'"></p></div>';
                    }

                    function format_tier($tier) {
                      $color = "danger";

                      switch($tier) {
                        case "-":
                          return '<div class="select is-small"><select onchange="new_tier(this);"><option>Select</option><option>Elder</option><option>Magic</option><option>Yew</option><option>Maple</option><option>Willow</option><option>Oak</option><option>Normal</option></select></div>';
                          break;
                        case "Elder":
                        case "Magic":
                          $color = "success";
                          break;
                      }


                      return '<div class="tags has-addons"><span class="tag is-'.$color.'">'.$tier.'</span><a onclick="edit_tier(this);"><i class="fa fa-pencil tag"></i></a></div>';
                    }


                    function parse_world($table) {
                      global $link;

                      $world = $table["world"];
                      $tier = $table["tier"];
                      $db_state = $table["state"];

                      $world_print = format_world($table["world"], $table["type"]);

                      $current_timestamp = time();
                      $spawn_timestamp = $table["spawn"];
                      $despawn_timestamp = strtotime("+30 minutes", $spawn_timestamp);

                      $alive_dropdown = '<div class="tags has-addons"><span class="tag is-light">Alive</span><a onclick="switch_state(this);"><i class="fa fa-undo tag"></i></a></div>';

                      echo $current_timestamp."<br/>".$despawn_timestamp;

                      // TREE LOGIC WOOOO

                      if($db_state == "Dead") {
                        if($spawn_timestamp != NULL) {

                          // CASE 1: not yet spawned
                          if($current_timestamp < $spawn_timestamp) {
                            echo "case 1";
                            // $link->query("UPDATE `trees` SET state = 'Dead', tier = '-' WHERE world = $world");

                            $state = "Dead";
                            $tier = "-";
                            $spawn = format_spawn($spawn_timestamp);
                          }

                          // CASE 2: spawn time has passed
                          else if($current_timestamp > $spawn_timestamp) {
                            echo "case 2";
                            // $link->query("UPDATE `trees` SET state = 'Alive' WHERE world = $world");

                            $state = "Alive";
                            $tier = format_tier($tier);
                            $spawn = "-";
                          }

                        } else {
                          // CASE 4-ish: no spawn time is set (occurs after case 4)
                          echo "case 4ish";
                          // $link->query("UPDATE `trees` SET state = 'Dead', tier = '-', spawn = NULL WHERE world = $world");

                          $state = "Dead";
                          $tier = "-";
                          $spawn = format_spawn($spawn_timestamp);

                        }
                      } else if($db_state == "Alive") {

                        // CASE 3: not yet despawned
                        if($current_timestamp < $despawn_timestamp) {
                          echo "case 3";
                          // $link->query("UPDATE `trees` SET state = 'Alive', spawn = NULL WHERE world = $world");

                          $state = $alive_dropdown;
                          $tier = format_tier($tier);
                          $spawn = "-";
                        }

                        // CASE 4: despawn time has passed
                        else if($current_timestamp >= $despawn_timestamp) {
                          echo "case 4";
                          // $link->query("UPDATE `trees` SET state = 'Dead', tier = '-', spawn = NULL WHERE world = $world");

                          $state = "Dead";
                          $tier = "-";
                          $spawn = format_spawn($spawn_timestamp);
                        }

                      }


                      print_row($world_print, $state, $tier, $spawn);


                    }

                    function print_row($world, $state, $tier, $spawn) {

                      echo '<tr>';
                      echo '<td>'.$world.'</td>';
                      echo '<td>'.$state.'</td>';
                      echo '<td>'.$tier.'</td>';
                      echo '<td>'.$spawn.'</td>';
                      echo '<td><input class="button is-small is-light" type="submit" value="Update" onclick="update(this);"></td>';
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
        var row = obj.parentElement.parentElement;

      }

      /**
       * Called when pencil icon is clicked to edit the tier.
       * Changes element into a dropdown.
       */

      function edit_tier(obj) {
        obj.parentElement.parentElement.innerHTML = '<div class="select is-small"><select onchange="new_tier(this);"><option>Select</option><option>Elder</option><option>Magic</option><option>Yew</option><option>Maple</option><option>Willow</option><option>Oak</option><option>Normal</option></select></div>';

      }

      /**
       * Called when an element of the tier dropdown is selected.
       * Changes element back into pencil icon.
       */

      function new_tier(obj) {
        var td = obj.parentElement.parentElement;
        var value = obj.options[obj.selectedIndex].value;
        var color = "danger";

        if(value == "Elder" || value == "Magic")
          color = "success";

        td.innerHTML = '<div class="tags has-addons"><span class="tag is-'+color+'">'+value+'</span><a onclick="edit_tier(this);"><i class="fa fa-pencil tag"></i></a></div>';
      }

      function switch_state(obj) {
        var td = obj.parentElement.parentElement;
        var state = obj.parentElement.children[0].innerHTML;

        if(state == "Alive")
          new_state = "Dead";
        else if(state == "Dead")
          new_state = "Alive";

        td.innerHTML = '<div class="tags has-addons"><span class="tag is-light">'+new_state+'</span><a onclick="switch_state(this);"><i class="fa fa-undo tag"></i></a></div>';
      }

    </script>

  </body>
</html>





<!--


loop through all worlds and analyze:

tree state
├── dead
1  ├── not yet spawned
   |   ├──state: Dead | tier: N/A | spawn: editable/DB
   |   └── DB/Table: world | dead | n/a | form
2  └── spawn time has passed
   |   ├── state: Alive | tier: editable | spawn: N/A
   |   └── DB/Table: world | alive | form | n/a
A  └── no spawn time is set (occurs after case 4)
      ├── state: Dead | tier: N/A | spawn: editable
      └── DB/Table: world | dead | n/a | form
├── alive
3  ├── not yet despawned
   |   ├── state: Alive | tier: editable | spawn: N/A
   |   └── DB/Table: world | alive | form | n/a
4  └── despawn time has passed
       ├── state: Dead | tier: - | spawn: editable/DB
       └── DB/Table: world | dead | n/a | form

================
MISC:


if a world has not been updated for a long time (>3 hours)
then it's state is "Unkown" but should be stored as "Dead"
then it's spawn time is "-"

this occurs after CASE4, so a null spawn_time needs to be handled


================
PSEUDO CODE:


$current_time
$spawn_time
$despawn_time

if($state is dead)

  if($spawn_time exists)
    CASE1   if($current_time < $spawn_time) # Not yet spawned

    CASE2   if($current_time > $spawn_time) # Spawn time has passed

  else if($spawn_time does not exist) # Old world

    CASEA

else if($state is alive)

  CASE3   if($current_time < $despawn_time) # Not yet despawned

  CASE4   if($current_time > $despawn_time) # Despawn time has passed


=================
EXAMPLE:


 [CASE 2]
 load up world 3 at 01:45
 database says its dead with spawn time 01:40
 state is now Alive, tier is editable, spawn is N/A
 print that, update the database with that

 [CASE 3]
 at 01:50 someone views the page
 world 3 loads as Alive, tier editable, spawn N/A
 no change to table or DB

 [CASE 3]
 at 02:00 someone loads the page

 [CASE 1]
 at 02:00 they mark it as dead
 javascript monitors state, and now allows spawn to be edited
 update database with Dead, tier is N/A, spawn is NULL or form
 javascript submits with AJAX, reload the page

 OR

 [CASE 4]
 at 1:15 someone views the page
 world 3 loads as Alive, tier editable, spawn N/A
 but despawn time has passed
 state is now Dead, tier is N/A, spawn is -/editable
 print that, update the database with that



 Error handling:

 Spawn time must be within < 2h 40min of current time
 Cannot set dead within 5 minutes of spawn time


-->
