<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>F2PRS / Tools / Users</title>
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

              <h1 class="title">Users</h1>
              <hr />

							<div class="content">
								<pre>

<?php

/* BEGIN PAGE GENERATION */
require_once '../../assets/includes/php/db.php';

/* Connect to database */
$link = database_connect();

$count = $link->query("SELECT COUNT(*) AS count FROM hs");
$count = $count->fetch_array(MYSQLI_ASSOC);
$count = $count['count'];

echo $count . " Total Users<br/><br/>";

$data = $link->query("SELECT rsn FROM hs");
while($row = $data->fetch_array(MYSQLI_ASSOC)) {
	echo $row['rsn']."<br/>";
}

?>

        		  </div>


            </div>
          </div>
        </div>
      </div>

    </section>

  </body>
</html>
