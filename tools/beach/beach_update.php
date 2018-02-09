<?php

  date_default_timezone_set('UTC');

  $world = $_GET["world"];
  $activity = $_GET["activity"];
  $time = time();

  require_once '../../assets/includes/php/db.php';

  $link = database_connect();
  $link->query("UPDATE beach SET activity = '".$activity."', time = NOW() WHERE world = $world");

?>
