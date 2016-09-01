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
$month = date("m");
$year = date("Y");
$pid = $_GET['id'];
mysql_select_db($database_connect_db, $connect_db);
$query_RS_maketemp = "Create temporary table loser_hands as (SELECT hand_fk_id FROM `player_hand` WHERE player_fk_id = $pid and points_earned < 0 and MID( TIMESTAMP, 6, 2 ) = '$month' AND MID( TIMESTAMP, 1, 4 ) = '$year')";
$RS_maketemp = mysql_query($query_RS_maketemp, $connect_db) or die(mysql_error());

mysql_select_db($database_connect_db, $connect_db);
$query_RS_maketemp1 = "Create temporary table winner_hands as (SELECT hand_fk_id FROM `player_hand` WHERE player_fk_id = $pid and points_earned > 0 and MID( TIMESTAMP, 6, 2 ) = '$month' AND MID( TIMESTAMP, 1, 4 ) = '$year')";
$RS_maketemp1 = mysql_query($query_RS_maketemp1, $connect_db) or die(mysql_error());

mysql_select_db($database_connect_db, $connect_db);
$query_RS_pointsgo = "Select ph.player_fk_id, SUM(ph.points_earned) as points, p.first_name, p.last_name from player_hand as ph join loser_hands as lh on ph.hand_fk_id = lh.hand_fk_id join players as p on ph.player_fk_id = p.id where ph.points_earned > 0 and MID( TIMESTAMP, 6, 2 ) = '$month' AND MID( TIMESTAMP, 1, 4 ) = '$year' group by ph.player_fk_id order by points desc limit 5";
$RS_pointsgo = mysql_query($query_RS_pointsgo, $connect_db) or die(mysql_error());
$row_RS_pointsgo = mysql_fetch_assoc($RS_pointsgo);
$totalRows_RS_pointsgo = mysql_num_rows($RS_pointsgo);

mysql_select_db($database_connect_db, $connect_db);
$query_RS_pointscome = "Select ph.player_fk_id, sum(ph.points_earned) as points, p.first_name, p.last_name from player_hand as ph join winner_hands as wh on ph.hand_fk_id = wh.hand_fk_id join players as p on ph.player_fk_id = p.id where ph.points_earned < 0 and MID( TIMESTAMP, 6, 2 ) = '$month' AND MID( TIMESTAMP, 1, 4 ) = '$year' group by ph.player_fk_id order by points asc limit 5";
$RS_pointscome = mysql_query($query_RS_pointscome, $connect_db) or die(mysql_error());
$row_RS_pointscome = mysql_fetch_assoc($RS_pointscome);
$totalRows_RS_pointscome = mysql_num_rows($RS_pointscome);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title>Stolen Points</title>

    <!-- Bootstrap -->
    <link href="pt.blueprint.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
  	<!-- Top Nav bar -->
  	<div class="navbar navbar-inverse navbar-static-top" role="navigation">
      	<div class="navbar-logo"></div>
      	<a href="index.php"><div class="navbar-brand">PALAN<span style="color:red;">TAROT </span><small>v2.0</small></div></a>
    </div>
    <!-- Top Nav bar -->
    <div class="container text-center">
    	<p> <h3><img src="tarot_image.png" width="241" height="172" class="img-rounded"><br>
    	Who  Points go to &amp; come from</h3></p>
    </div>
    <div class="container text-center">
    <div class="panel panel-default">
  <div class="panel-body">
  <p>
  <h3 style="color:#CC0000;">Top 5 point winners of <?php echo $_GET['fn']; ?> <?php echo $_GET['ln']; ?> losses</h3>
  </p>
  <p>
    	<p>
	  <table width="200" class=" table table-responsive table-striped">
    	<th class="text-center">Players</th>
    	<th class="text-center">Points Earned</th>
  <?php do { ?><tr>
     <td><?php echo $row_RS_pointsgo['first_name']; ?> <?php echo $row_RS_pointsgo['last_name']; ?></td>
     <td><?php echo $row_RS_pointsgo['points']; ?>
  </tr><?php } while ($row_RS_pointsgo = mysql_fetch_assoc($RS_pointsgo)); ?>
</table></p>
</div>
  </div>
  <div class="panel panel-default">
  <div class="panel-body">
  <p><h3 style="color:#CC0000;">Top 5 point losers of <?php echo $_GET['fn']; ?> <?php echo $_GET['ln']; ?> wins</h3></p>
  <p>
    	<p>
	  <table width="200" class=" table table-responsive table-striped">
    	<th class="text-center">Player</th>
    	<th class="text-center">Points Lost</th>
  <?php do { ?><tr>
     <td><?php echo $row_RS_pointscome['first_name']; ?> <?php echo $row_RS_pointscome['last_name']; ?></td>
     <td><?php echo $row_RS_pointscome['points']; ?>
  </tr><?php } while ($row_RS_pointscome = mysql_fetch_assoc($RS_pointscome)); ?>
</table></p>
</div>
  </div>
  
		<p><a href="points.php"><button type="button" class="btn btn-danger btn-md">Back to Points Chart</button></a> <a href="charts.php"><button type="button" class="btn btn-success btn-md">Back to Charts Menu</button></a></p>
    </div>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="pt.blueprint.js"></script>
  </body>
</html>
<?php
mysql_free_result($RS_pointsgo);
mysql_free_result($RS_pointscome);
?>
