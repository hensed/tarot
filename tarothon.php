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

mysql_select_db($database_connect_db, $connect_db);
$query_RS_lasthands = "SELECT timestamp, players, p.first_name, p.last_name, ps.first_name as partnerfn, ps.last_name as partnerln, points, bid_amt, king_called FROM hand join players as p on p.id=`bidder_fk_id` left join players as ps on ps.id=`partner_fk_id` WHERE timestamp >= '2016-06-17 22:00:00' and timestamp <= '2016-06-18 07:00:00' ORDER BY timestamp DESC LIMIT 10";
$RS_lasthands = mysql_query($query_RS_lasthands, $connect_db) or die(mysql_error());
$row_RS_lasthands = mysql_fetch_assoc($RS_lasthands);
$totalRows_RS_lasthands = mysql_num_rows($RS_lasthands);

mysql_select_db($database_connect_db, $connect_db);
$query_RS_topscore = "SELECT p.first_name, p.last_name, count(`hand_fk_id`) as handsplayed, SUM(`points_earned`) as points FROM player_hand JOIN players as p on p.id=player_hand.`player_fk_id` WHERE player_hand.timestamp >= '2016-06-17 22:00:00' and timestamp <= '2016-06-18 07:00:00' GROUP BY `player_fk_id` ORDER BY points DESC";
$RS_topscore = mysql_query($query_RS_topscore, $connect_db) or die(mysql_error());
$row_RS_topscore = mysql_fetch_assoc($RS_topscore);
$totalRows_RS_topscore = mysql_num_rows($RS_topscore);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<title>Tarothon Dashboard</title>

<!-- Bootstrap -->
<link href="pt.blueprint.css" rel="stylesheet">

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body onload="JavaScript:timedRefresh(60000);">
<!-- Top Nav bar -->
<div class="navbar navbar-inverse navbar-static-top" role="navigation">
  <div class="navbar-logo"></div>
  <a href="index.php">
  <div class="navbar-brand">PALAN<span style="color:red;">TAROT </span><small>v2.0</small></div>
  </a> </div>
<!-- Top Nav bar -->
<div class="container text-center">
  <p>
  <h3><img src="tarot_image.png" width="241" height="172" class="img-rounded"><br>Tarothon Dashboard<br>
    <small>*** Next Tarothon time is Friday, June 17th at 7:00pm!! ***</small></h3>
  </p>
</div>
<div class="container text-center">
  <p>
  <div class="panel panel-default">
    <div class="panel-body">
      <p>
      <h3 style="color:#CC0000;">Tournament leaderboard <br><small>page refreshes every minute</small></h3>
      </p>
      <p>
      
      <table width="200" class=" table table-responsive table-striped">
        
          <th class="text-center">Player</th>
          <th class="text-center">Total Hands Played</th>
          <th class="text-center">Total Score</th>
          <th class="text-center">Avg Points Per Hand</th>
          <?php do { ?>
        <!--start loop here-->
        <tr>
          <td><?php echo $row_RS_topscore['first_name']; ?> <?php echo $row_RS_topscore['last_name']; ?></td>
          <td><?php echo $row_RS_topscore['handsplayed']; ?></td>
          <td><?php echo $row_RS_topscore['points']; ?></td>
          <td><?php $g_avg = ($row_RS_topscore['points'] / $row_RS_topscore['handsplayed']); ?>
            <?php echo number_format((float)$g_avg, 2, '.', ''); ?></td>
        </tr>
        <!--end loop here-->
        <?php } while ($row_RS_topscore = mysql_fetch_assoc($RS_topscore)); ?>
      </table>
    </div>
    </p>
  </div>
  <div class="panel panel-default">
    <div class="panel-body">
      <p>
      <h3 style="color:#CC0000;"> Last 10 hands </h3>
      </p>
      <table width="200" class=" table table-responsive table-striped">
        
          <th class="text-center">Time</th>
          <th class="text-center">Player amount</th>
          <th class="text-center">Bidder</th>
          <th class="text-center">Partner</th>
          <th class="text-center">Suit called</th>
          <th class="text-center">Bid</th>
          <th class="text-center">Points won/lost</th>
          <?php do { ?>
        <tr>
          <td><?php echo $row_RS_lasthands['timestamp']; ?></td>
          <td><?php echo $row_RS_lasthands['players']; ?></td>
          <td><?php echo $row_RS_lasthands['first_name']; ?> <?php echo $row_RS_lasthands['last_name']; ?></td>
          <td><?php echo $row_RS_lasthands['partnerfn']; ?> <?php echo $row_RS_lasthands['partnerln']; ?></td>
          <td><?php echo $row_RS_lasthands['king_called']; ?></td>
          <td><?php echo $row_RS_lasthands['bid_amt']; ?></td>
          <td><?php echo $row_RS_lasthands['points']; ?></td>
        </tr>
        <?php } while ($row_RS_lasthands = mysql_fetch_assoc($RS_lasthands)); ?>
      </table>
      </p>
    </div>
  </div>
  <a href="charts.php">
  <p><button type="button" class="btn btn-danger btn-lg">Back to Charts Menu</button></p>
  </a><br>
  <br>
</div>
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) --> 
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script> 
<!-- Include all compiled plugins (below), or include individual files as needed --> 
<script src="pt.blueprint.js"></script> 
<script type="text/JavaScript">
<!--
function timedRefresh(timeoutPeriod) {
	setTimeout("location.reload(true);",timeoutPeriod);
}
//   -->
</script>
</body>
</html>
<?php
mysql_free_result($RS_lasthands);
mysql_free_result($RS_topscore);
?>
