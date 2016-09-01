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
mysql_select_db($database_connect_db, $connect_db);
$query_RS_partner = "SELECT p.first_name, p.last_name, sum(points_earned) as points, count(p.id) as hands, (sum(points_earned) / count(p.id)) as per_hand FROM player_hand join players as p on p.id=`player_fk_id` WHERE was_partner=1 and MID( TIMESTAMP, 6, 2 ) = '$month' AND MID( TIMESTAMP, 1, 4 ) = '$year' group by p.id order by points desc";
$RS_partner = mysql_query($query_RS_partner, $connect_db) or die(mysql_error());
$row_RS_partner = mysql_fetch_assoc($RS_partner);
$totalRows_RS_partner = mysql_num_rows($RS_partner);

mysql_select_db($database_connect_db, $connect_db);
$query_RS_bidder = "SELECT p.first_name, p.last_name, sum(points_earned) as points, count(p.id) as hands, (sum(points_earned) / count(p.id)) as per_hand FROM player_hand join players as p on p.id=`player_fk_id` WHERE was_bidder=1 and MID( TIMESTAMP, 6, 2 ) = '$month' AND MID( TIMESTAMP, 1, 4 ) = '$year' group by p.id order by points desc";
$RS_bidder = mysql_query($query_RS_bidder, $connect_db) or die(mysql_error());
$row_RS_bidder = mysql_fetch_assoc($RS_bidder);
$totalRows_RS_bidder = mysql_num_rows($RS_bidder);

mysql_select_db($database_connect_db, $connect_db);
$query_RS_team = "SELECT p.first_name, p.last_name, sum(points_earned) as points, count(p.id) as hands, (sum(points_earned) / count(p.id)) as per_hand FROM player_hand join players as p on p.id=`player_fk_id` WHERE was_bidder is null and was_partner is null and MID( TIMESTAMP, 6, 2 ) = '$month' AND MID( TIMESTAMP, 1, 4 ) = '$year' group by p.id order by points desc";
$RS_team = mysql_query($query_RS_team, $connect_db) or die(mysql_error());
$row_RS_team = mysql_fetch_assoc($RS_team);
$totalRows_RS_team = mysql_num_rows($RS_team);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title>Results by Role</title>

    <!-- Bootstrap -->
    <link href="pt.blueprint.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
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
    	<p> <h2><img src="tarot_image.png" width="241" height="172" class="img-rounded"><br>Results by Role played in hands</h2></p>
    </div>
    <div class="container text-center">
    <div class="panel panel-default">
  <div class="panel-body">
  <p><h3 style="color:#CC0000;">Role as partner</h3></p>
    	<p>
  		<table width="200" class=" table table-responsive table-striped">
    	<th class="text-center">Player</th>
    	<th class="text-center">Amount of points won/lost</th>
    	<th class="text-center">Hands as partner</th>
        <th class="text-center">Avg per hand</th>
  <?php do { ?><tr>
      <td><?php echo $row_RS_partner['first_name']; ?> <?php echo $row_RS_partner['last_name']; ?></td>
      <td><?php echo $row_RS_partner['points']; ?></td>
      <td><?php echo $row_RS_partner['hands']; ?></td>
      <td><?php echo number_format((float)$row_RS_partner['per_hand'], 2, '.', ''); ?></td>
  </tr><?php } while ($row_RS_partner = mysql_fetch_assoc($RS_partner)); ?>
</table>
		</p></div></div>
        <!--beginning--><p>
        <div class="panel panel-default">
  <div class="panel-body">
  <p><h3 style="color:#CC0000;">Role as bidder</h3></p>
  <p>
	<table width="200" class=" table table-responsive table-striped">
    	<th class="text-center">Player</th>
    	<th class="text-center">Amount of points won/lost</th>
    	<th class="text-center">Hands bidded</th>
        <th class="text-center">Avg per hand</th>        
  <?php do { ?><tr>
      <td><?php echo $row_RS_bidder['first_name']; ?> <?php echo $row_RS_bidder['last_name']; ?></td>
      <td><?php echo $row_RS_bidder['points']; ?></td>
      <td><?php echo $row_RS_bidder['hands']; ?></td>
      <td><?php echo number_format((float)$row_RS_bidder['per_hand'], 2, '.', ''); ?></td>
  </tr><?php } while ($row_RS_bidder = mysql_fetch_assoc($RS_bidder)); ?>
</table>
         </div></div>
         </p><!--end-->
         <!--beginning--><p>
        <div class="panel panel-default">
  <div class="panel-body">
  <p><h3 style="color:#CC0000;">Role as Team Member</h3></p>
  <p>
	<table width="200" class=" table table-responsive table-striped">
    	<th class="text-center">Player</th>
    	<th class="text-center">Amount of points won/lost</th>
    	<th class="text-center">Hands as team</th>
        <th class="text-center">Avg per hand</th>        
  <?php do { ?><tr>
      <td><?php echo $row_RS_team['first_name']; ?> <?php echo $row_RS_team['last_name']; ?></td>
      <td><?php echo $row_RS_team['points']; ?></td>
      <td><?php echo $row_RS_team['hands']; ?></td>
      <td><?php echo number_format((float)$row_RS_team['per_hand'], 2, '.', ''); ?></td>
  </tr><?php } while ($row_RS_team = mysql_fetch_assoc($RS_team)); ?>
</table>
         </div></div>
         </p><!--end-->
        <p><a href="charts.php"><button type="button" class="btn btn-danger btn-lg">Back to Charts Menu</button></a></p>
    </div>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="pt.blueprint.js"></script>
  </body>
</html>
<?php
mysql_free_result($RS_partner);

mysql_free_result($RS_bidder);

mysql_free_result($RS_team);
?>
