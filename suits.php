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
$query_RS_suits = "SELECT `king_called`, count(`king_called`) as king_amount, sum(`points`) as points FROM hand WHERE 'king_called' IS NOT NULL and MID( TIMESTAMP, 6, 2 ) = '$month' AND MID( TIMESTAMP, 1, 4 ) = '$year' GROUP BY `king_called` ORDER BY king_amount DESC limit 5";
$RS_suits = mysql_query($query_RS_suits, $connect_db) or die(mysql_error());
$row_RS_suits = mysql_fetch_assoc($RS_suits);
$totalRows_RS_suits = mysql_num_rows($RS_suits);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title>Frequency of Suit Choosen</title>

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
    	<p> <h3><img src="tarot_image.png" width="241" height="172" class="img-rounded"><br>Graph of Frequency of suit choosen by bidder</h3></p>
    </div>
    <div class="container text-center">
    <div class="panel panel-default">
  <div class="panel-body">
    	<p>
  		<table width="200" class=" table table-responsive table-striped">
    	<th class="text-center">Suit called <br>(blank represents 3/4 player or self-call)</th>
    	<th class="text-center">Times choosen by bidder</th>
    	<th class="text-center">Amount of points won/lost</th>
  <?php do { ?><tr>
      <td><?php echo $row_RS_suits['king_called']; ?></td>
      <td><?php echo $row_RS_suits['king_amount']; ?></td>
      <td><?php echo $row_RS_suits['points']; ?></td>
  </tr><?php } while ($row_RS_suits = mysql_fetch_assoc($RS_suits)); ?>
</table>
		</p></div></div>
        <p><a href="charts.php"><button type="button" class="btn btn-danger btn-lg">Back to Charts Menu</button></a></p>
    </div>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="pt.blueprint.js"></script>
  </body>
</html>
<?php
mysql_free_result($RS_suits);
?>
