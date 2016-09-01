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
$today = date("d");
mysql_select_db($database_connect_db, $connect_db);
$query_delta_points = "create temporary table delta_calc as (SELECT COUNT( `hand_fk_id` ) AS handsplayed, player_fk_id as dpid, SUM(  `points_earned` ) AS deltapoints FROM player_hand WHERE MID( TIMESTAMP, 6, 2 ) = '$month' AND MID( TIMESTAMP, 1, 4 ) = '$year' AND MID( TIMESTAMP, 9, 2 ) between 1 and '$today' - 2 GROUP BY  `player_fk_id`)";
$delta_points = mysql_query($query_delta_points, $connect_db) or die(mysql_error());

mysql_select_db($database_connect_db, $connect_db);
$query_RS_points = "SELECT p.first_name, p.id, p.last_name, count(`hand_fk_id`) as handsplayed, SUM(`points_earned`) as points FROM player_hand JOIN players as p on p.id=player_hand.`player_fk_id` GROUP BY `player_fk_id` ORDER BY points DESC limit 5";
$RS_points = mysql_query($query_RS_points, $connect_db) or die(mysql_error());
$row_RS_points = mysql_fetch_assoc($RS_points);
$totalRows_RS_points = mysql_num_rows($RS_points);

mysql_select_db($database_connect_db, $connect_db);
$query_RS_topscore = "SELECT p.first_name, p.id, p.last_name, COUNT(  `hand_fk_id` ) AS handsplayed, SUM(  `points_earned` ) AS points, deltapoints FROM player_hand JOIN players AS p ON p.id = player_hand.`player_fk_id` JOIN delta_calc AS dp ON dp.dpid = player_hand.`player_fk_id` WHERE MID( TIMESTAMP, 6, 2 ) = '$month' AND MID( TIMESTAMP, 1, 4 ) = '$year' GROUP BY  `player_fk_id` ORDER BY points DESC";
$RS_topscore = mysql_query($query_RS_topscore, $connect_db) or die(mysql_error());
$row_RS_topscore = mysql_fetch_assoc($RS_topscore);
$totalRows_RS_topscore = mysql_num_rows($RS_topscore);

// last month's scores
mysql_select_db($database_connect_db, $connect_db);
$query_RS_monthscore = "SELECT p.first_name, p.id, p.last_name, count(`hand_fk_id`) as handsplayed, SUM(`points_earned`) as points FROM player_hand JOIN players as p on p.id=player_hand.`player_fk_id` WHERE MONTH(timestamp) = '$month' -1 AND YEAR(timestamp) = '$year' GROUP BY `player_fk_id` ORDER BY points DESC";
$RS_monthscore = mysql_query($query_RS_monthscore, $connect_db) or die(mysql_error());
$row_RS_monthscore = mysql_fetch_assoc($RS_monthscore);
$totalRows_RS_monthscore = mysql_num_rows($RS_monthscore);

mysql_select_db($database_connect_db, $connect_db);
$query_RS_bottomscore = "SELECT p.first_name, p.last_name, count(`hand_fk_id`) as handsplayed, SUM(`points_earned`) as points FROM player_hand JOIN players as p on p.id=player_hand.`player_fk_id` GROUP BY `player_fk_id` ORDER BY points ASC LIMIT 5";
$RS_bottomscore = mysql_query($query_RS_bottomscore, $connect_db) or die(mysql_error());
$row_RS_bottomscore = mysql_fetch_assoc($RS_bottomscore);
$totalRows_RS_bottomscore = mysql_num_rows($RS_bottomscore);

$rank_counter = 1;
?>
<?php 
	function delta($points, $deltapoints) {
		$deltascore = ($points - $deltapoints);
		if ($deltascore > 0) {
			echo '<span style="color:green;" class="glyphicon glyphicon-circle-arrow-up" aria-hidden="true"> '
			 . $deltascore . '</span>';
		} elseif ($deltascore < 0) {
			echo '<span style="color:red;" class="glyphicon glyphicon-circle-arrow-down" aria-hidden="true"> '
			 . $deltascore . '</span>';
		}
	}
 ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title>Points Graph</title>

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
    	<p> 
    	<h2><img src="tarot_image.png" width="241" height="172" class="img-rounded"><br>Points chart</h2></p>
    </div>
    
    <div class="container text-center">
    	<p>
        <div class="panel panel-default">
  <div class="panel-body">
  <p><h3 style="color:#CC0000;">Points this month, all players</h3></p>
  <p>
	<table width="200" class=" table table-responsive table-striped">
    	<th class="text-center">Rank</th>
    	<th class="text-center">Player <br>(click for player details)</th>
    	<th>Total Score <br>(delta from last 1-2 days)</th>
        <th class="text-center">Total Hands Played</th>
        <th class="text-center">Avg Points Per Hand</th>        
  <?php do { ?><tr>
  	  <td><?php echo $rank_counter++; ?></td>	
      <td><a href="pointsgo.php?fn=<?php echo $row_RS_topscore['first_name']; ?>&ln=<?php echo $row_RS_topscore['last_name']; ?>&id=<?php echo $row_RS_topscore['id']; ?>"><?php echo $row_RS_topscore['first_name']; ?> <?php echo $row_RS_topscore['last_name']; ?></a></td>
      <td align="left"><span style="font-weight:bolder; <?php if ($row_RS_topscore['points'] < 0) { echo ' color:red;';} ?>"><?php echo $row_RS_topscore['points']; ?></span> <?php delta($row_RS_topscore['points'], $row_RS_topscore['deltapoints']); ?></td>
      <td><?php echo $row_RS_topscore['handsplayed']; ?></td>
      <td><?php $g_avg = ($row_RS_topscore['points'] / $row_RS_topscore['handsplayed']); ?><?php echo number_format((float)$g_avg, 2, '.', ''); ?></td>
  </tr><?php } while ($row_RS_topscore = mysql_fetch_assoc($RS_topscore)); ?>
</table>
         </div>
         </p><!--end-->
</div>
	<div class="panel panel-default">
  <div class="panel-body">
  <p>
  <h3 style="color:#CC0000;">Points last month, all players</h3></p>
  <p>
	<table width="200" class=" table table-responsive table-striped">
    	<th class="text-center">Player</th>
    	<th class="text-center">Total Hands Played</th>
    	<th class="text-center">Total Score</th>
        <th class="text-center">Avg Points Per Hand</th>        
  <?php do { ?><!--start loop here--><tr>
      <td><?php echo $row_RS_monthscore['first_name']; ?> <?php echo $row_RS_monthscore['last_name']; ?></td>
      <td><?php echo $row_RS_monthscore['handsplayed']; ?></td>
      <td><?php echo $row_RS_monthscore['points']; ?></td>
      <td><?php $g_avg = ($row_RS_monthscore['points'] / $row_RS_monthscore['handsplayed']); ?><?php echo number_format((float)$g_avg, 2, '.', ''); ?></td>
  </tr><!--end loop here--><?php } while ($row_RS_monthscore = mysql_fetch_assoc($RS_monthscore)); ?>
</table>
         </div>
         </p>
</div>
    	<p>
        <div class="panel panel-default">
  <div class="panel-body">
  <p>
  <h3 style="color:#CC0000;"> all-time bottom 5 point losers </h3></p>
  <p>
	<table width="200" class=" table table-responsive table-striped">
    	<th class="text-center">Player</th>
    	<th class="text-center">Total Hands Played</th>
    	<th class="text-center">Total Score</th>
        <th class="text-center">Avg Points Per Hand</th>        
  <?php do { ?><!--start loop here--><tr>
      <td><?php echo $row_RS_bottomscore['first_name']; ?> <?php echo $row_RS_bottomscore['last_name']; ?></td>
      <td><?php echo $row_RS_bottomscore['handsplayed']; ?></td>
      <td><?php echo $row_RS_bottomscore['points']; ?></td>
      <td><?php $g_avg = ($row_RS_bottomscore['points'] / $row_RS_bottomscore['handsplayed']); ?><?php echo number_format((float)$g_avg, 2, '.', ''); ?></td>
  </tr><!--end loop here--><?php } while ($row_RS_bottomscore = mysql_fetch_assoc($RS_bottomscore)); ?>
</table>
         </div>
         </p>
</div>
    	<!--beginning--><p>
        <div class="panel panel-default">
  <div class="panel-body">
  <p>
  <h3 style="color:#CC0000;"> Top 5 all-time point winners </h3></p>
  <p>
	<table width="200" class=" table table-responsive table-striped">
    	<th class="text-center">Player</th>
    	<th class="text-center">Total Hands Played</th>
    	<th class="text-center">Total Score</th>
        <th class="text-center">Avg Points Per Hand</th>        
  <?php do { ?><!--start loop here--><tr>
      <td><?php echo $row_RS_points['first_name']; ?> <?php echo $row_RS_points['last_name']; ?></td>
      <td><?php echo $row_RS_points['handsplayed']; ?></td>
      <td><?php echo $row_RS_points['points']; ?></td>
      <td><?php $g_avg = ($row_RS_points['points'] / $row_RS_points['handsplayed']); ?><?php echo number_format((float)$g_avg, 2, '.', ''); ?></td>
  </tr><!--end loop here--><?php } while ($row_RS_points = mysql_fetch_assoc($RS_points)); ?>
</table>
         </div>
         </p>
</div>
<p><a href="charts.php"><button type="button" class="btn btn-danger btn-lg">Back to Charts Menu</button></a></p>
    </div>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="pt.blueprint.js"></script>
  </body>
</html>
<?php
mysql_free_result($RS_points);

mysql_free_result($RS_topscore);

mysql_free_result($RS_bottomscore);

mysql_free_result($RS_monthscore);
?>