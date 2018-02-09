<?php

require_once '../assets/includes/php/db.php';

$link = database_connect("f2prs_test");


  $type = $_POST['type'];
  $user = $_POST['username'];

  if(!$type) {
    exit("Bad call");
  }

  function add($user, $link) {
    $link->query("INSERT INTO hs (rsn) VALUES('$user')");
		$link->query("INSERT INTO week (rsn) VALUES('$user')");
		$link->query("INSERT INTO week_stats (rsn) VALUES('$user')");
		$link->query("INSERT INTO month (rsn) VALUES('$user')");
		$link->query("INSERT INTO month_stats (rsn) VALUES('$user')");
    echo "Added ".$user;
  }

  function del($user, $link) {
    $link->query("DELETE FROM hs WHERE rsn='$user'");
    $link->query("DELETE FROM month WHERE rsn='$user'");
    $link->query("DELETE FROM month_stats WHERE rsn='$user'");
    $link->query("DELETE FROM week WHERE rsn='$user'");
    $link->query("DELETE FROM week_stats WHERE rsn='$user'");
    echo "Deleted ".$user;
  }

  function ban($user, $link) {
    del($user, $link);
    $link->query("INSERT INTO banlist (rsn) VALUES('$user')");
    echo "Deleted and banned ".$user;
  }

  function ren($user1, $user2, $link) {
    $link->query("UPDATE hs SET rsn='$user2' WHERE rsn='$user1'");
    $link->query("UPDATE month SET rsn='$user2' WHERE rsn='$user1'");
    $link->query("UPDATE month_stats SET rsn='$user2' WHERE rsn='$user1'");
    $link->query("UPDATE week SET rsn='$user2' WHERE rsn='$user1'");
    $link->query("UPDATE week_stats SET rsn='$user2' WHERE rsn='$user1'");


    echo "Renamed ".$user1." to ".$user2;
  }


  switch($type) {
    case 'add':
      add($user, $link);
      break;
    case 'del':
      del($user, $link);
      break;
    case 'ban':
      ban($user, $link);
      break;
    case 'rename':
      $user1 = $_POST[user1];
      $user2 = $_POST[user2];
      ren($user1, $user2, $link);
      break;
  }





?>
