<?php require_once('Connections/connect_db.php'); ?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}
?>
<?php 
// Hand variables
$bidder_id = $_POST["bidder"];
$partner_id = $_POST["partner"];
$bid = $_POST["bid"];
$king = $_POST["king"];
$players = $_POST["players"];
$slammed = $_POST["slammed"];
$points = $_POST["score"];
$called_self = $_POST["calledself"];

// Player Variables
$bidder_showed = $_POST["bidder_showed"];
$bidder_one_last = $_POST["bidder_one_last"];
$partner_showed = $_POST["partner_showed"];
$partner_one_last = $_POST["partner_one_last"];
$tm1_showed = $_POST["tm1_showed"];
$tm1_one_last = $_POST["tm1_one_last"];
$tm2_showed = $_POST["tm2_showed"];
$tm2_one_last = $_POST["tm2_one_last"];
$tm3_showed = $_POST["tm3_showed"];
$tm3_one_last = $_POST["tm3_one_last"];
$tm4_showed = $_POST["tm4_showed"];
$tm4_one_last = $_POST["tm4_one_last"];
$tm1_id = $_POST["tm1"];
$tm2_id = $_POST["tm2"];
$tm3_id = $_POST["tm3"];
$tm4_id = $_POST["tm4"];

// logic items

// if bidder calls himself, then there is no partner
if ($called_self == 1) { $partner_id = null; }

// if it's a 3 or 4 player game, then king is null
if ($players < 5) { $king == null; }

// calculate points for bidder
$bidder_points_award = ($points * 2);
if ($called_self == 1) {
	$bidder_points_award = ($points * 4);
}
// 4 player game
if ($players == 4) {
	$bidder_points_award = ($points * 3);
}


// Calculate points for partner
$partner_points_award = $points;

// calculate points for team
$team_points_award = $points - ($points * 2);

if (($bidder_id > 0) && ($partner_id > 0 || $called_self == 1 || $players <= 4) && ($bid !== 0) && ($points !== 0) && (!empty($king) || $players < 5) && ($tm1_id > 0) && ($tm2_id > 0 ) && ($tm3_id > 0 || ($players == 3)) && ($tm4_id == 0 || ($called_self == 1 && $players == 5))) {

// Enter hand information into hand
$insertSQL = sprintf("INSERT INTO hand (players, bidder_fk_id, partner_fk_id, bid_amt, king_called, points, slam) VALUES ('$players', '$bidder_id', '$partner_id', '$bid', '$king', '$points', '$slammed')");
mysql_select_db($database_connect_db, $connect_db);
$Result1 = mysql_query($insertSQL, $connect_db) or die(mysql_error());

// Get hand ID from the hand we just entered
mysql_select_db($database_connect_db, $connect_db);
$query_rsgetHandID = "SELECT id FROM hand ORDER BY id DESC LIMIT 1";
$rsgetHandID = mysql_query($query_rsgetHandID, $connect_db) or die(mysql_error());
$row_rsgetHandID = mysql_fetch_assoc($rsgetHandID);
$totalRows_rsgetHandID = mysql_num_rows($rsgetHandID);
$hand_id = $row_rsgetHandID['id'];

// Enter bidder information into player_hand
$insertSQL = sprintf("INSERT INTO player_hand (hand_fk_id, player_fk_id, was_bidder, showed_trump, one_last, points_earned) VALUES ('$hand_id', '$bidder_id', 1, '$bidder_showed', '$bidder_one_last', '$bidder_points_award')");
mysql_select_db($database_connect_db, $connect_db);
$Result1 = mysql_query($insertSQL, $connect_db) or die(mysql_error());

// Enter partner information into player_hand
if ($partner_id > 0) {
$insertSQL = sprintf("INSERT INTO player_hand (hand_fk_id, player_fk_id, was_partner, showed_trump, one_last, points_earned) VALUES ('$hand_id','$partner_id', 1, '$partner_showed', '$partner_one_last', '$partner_points_award')");
mysql_select_db($database_connect_db, $connect_db);
$Result1 = mysql_query($insertSQL, $connect_db) or die(mysql_error());
}

// Enter team member 1 information into player_hand
$insertSQL = sprintf("INSERT INTO player_hand (hand_fk_id, player_fk_id, showed_trump, one_last, points_earned) VALUES ('$hand_id', '$tm1_id', '$tm1_showed', '$tm1_one_last', '$team_points_award')");
mysql_select_db($database_connect_db, $connect_db);
$Result1 = mysql_query($insertSQL, $connect_db) or die(mysql_error());

// Enter team member 2 information into player_hand
$insertSQL = sprintf("INSERT INTO player_hand (hand_fk_id, player_fk_id, showed_trump, one_last, points_earned) VALUES ('$hand_id','$tm2_id', '$tm2_showed', '$tm2_one_last', '$team_points_award')");
mysql_select_db($database_connect_db, $connect_db);
$Result1 = mysql_query($insertSQL, $connect_db) or die(mysql_error());


// Enter team member 3 information into player_hand
if ($tm3_id > 0) {
$insertSQL = sprintf("INSERT INTO player_hand (hand_fk_id, player_fk_id, showed_trump, one_last, points_earned) VALUES ('$hand_id','$tm3_id', '$tm3_showed', '$tm3_one_last', '$team_points_award')");
mysql_select_db($database_connect_db, $connect_db);
$Result1 = mysql_query($insertSQL, $connect_db) or die(mysql_error());
}

// Enter team member 4 information into player_hand
if ($tm4_id > 0) {
$insertSQL = sprintf("INSERT INTO player_hand (hand_fk_id, player_fk_id, showed_trump, one_last, points_earned) VALUES ('$hand_id','$tm4_id', '$tm4_showed', '$tm4_one_last', '$team_points_award')");
mysql_select_db($database_connect_db, $connect_db);
$Result1 = mysql_query($insertSQL, $connect_db) or die(mysql_error());
}

mysql_free_result($rsgetHandID);
header( 'Location: index.php?rs=2' );	
} else {
	header( 'Location: score.php?rs=1' ) ;
}
?>
