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
$query_RS_users = "SELECT players.first_name, players.last_name, players.id FROM players ORDER BY players.first_name ASC;";
$RS_users = mysql_query($query_RS_users, $connect_db) or die(mysql_error());
$row_RS_users = mysql_fetch_assoc($RS_users);
$totalRows_RS_users = mysql_num_rows($RS_users);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<title>Report a Score</title>

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
<div class="content">
<div class="navbar navbar-inverse navbar-static-top" role="navigation">
  <div class="navbar-logo"></div>
  <a href="index.php">
  <div class="navbar-brand">PALAN<span style="color:red;">TAROT </span><small>v2.0</small></div>
  </a> </div>
<?php if ($_GET["rs"] == 1) {?>
<div class="alert alert-danger text-center" role="alert">Oops! Some information was missing. Please try again.</div>
<?php }?>
<form action="hand_process.php" onsubmit="return validateForm()" method="post" name="form" role="form">
  <div class="center-block">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h2 class="panel-title text-center">Enter Tarot Hand Score</h2>
      </div>
      <div class="panel-body text-center"> How Many Players?<br>
        <div class="btn-group btn-group-lg radio-inline">
          <label class="radio-inline">
            <input type="radio"  name="players" id="3players" value="3">
            3 Players </label>
          <label class="radio-inline">
            <input type="radio"  name="players" id="4players" value="4">
            4 Players </label>
          <label class="radio-inline">
            <input type="radio" name="players" id="5players" value="5" checked>
            5 Players </label>
        </div>
      </div>
    </div>
    <div class="panel panel-default bg-primary">
      <div class="panel-heading">
        <h2 class="panel-title text-center">Bidder and Partner</h2>
      </div>
    </div>
    <div class="panel-body text-center"> Who Was The Bidder? <small><a href="add_player.php"> add new player</a></small><br>
      <select class="form-control center-block" name="bidder" id="bidder" style="width: 80%;">
        <option></option>
        <?php
do {  
?>
        <option value="<?php echo $row_RS_users['id']?>"><?php echo $row_RS_users['first_name']?> <?php echo $row_RS_users['last_name']?></option>
        <?php
} while ($row_RS_users = mysql_fetch_assoc($RS_users));
  $rows = mysql_num_rows($RS_users);
  if($rows > 0) {
      mysql_data_seek($RS_users, 0);
	  $row_RS_users = mysql_fetch_assoc($RS_users);
  }
?>
      </select>
      <label class="checkbox-inline">
        <input type="checkbox" id="inlineCheckbox1" name="bidder_showed" value="1">
        Showed Trump </label>
      <!--<label class="checkbox-inline">
        <input type="checkbox" id="inlineCheckbox2" name="bidder_one_last" value="1">
        Played 1-Trump Last </label>-->
      <label class="checkbox-inline">
        <input type="checkbox" id="inlineCheckbox3" name="slammed" value="1">
        Slammed? </label>
      <span id="selfcalled">
      <label class="checkbox-inline">
        <input type="checkbox" id="calledself" name="calledself" value="1">
        Called self? </label>
      </span>
      <p><br>
        Bid Amount: <!--<input type="number" class="form-control center-block" name="bid" placeholder="Bid" style="width: 200px;"><label class="checkbox-inline">-->
        <select name="bid" id="bid" class="form-control center-block" style="width: 200px;">
          <option></option>
          <option value="10">10</option>
          <option value="20">20</option>
          <option value="40">40</option>
          <option value="80">80</option>
          <option value="160">160</option>
        </select>
        <br>
        <span id="suitcalled">Bidder chose suit of:
        <select name="king" class="form-control center-block"  style="width: 200px;">
          <option></option>
          <option>Hearts</option>
          <option>Spades</option>
          <option>Diamonds</option>
          <option>Clubs</option>
        </select></span>
        </label>
      </p>
      <div class="panel-body text-center"> <span id="partner">Who Was The Partner? <small><a href="add_player.php"> add new player</a></small><br>
        <select class="form-control center-block" name="partner" id="partner" style="width: 80%;">
          <option></option>
          <?php
do {  
?>
          <option value="<?php echo $row_RS_users['id']?>"><?php echo $row_RS_users['first_name']?> <?php echo $row_RS_users['last_name']?></option>
          </option>
          <?php
} while ($row_RS_users = mysql_fetch_assoc($RS_users));
  $rows = mysql_num_rows($RS_users);
  if($rows > 0) {
      mysql_data_seek($RS_users, 0);
	  $row_RS_users = mysql_fetch_assoc($RS_users);
  }
?>
        </select>
        </span> 
        <!--<label class="checkbox-inline">
  <input type="checkbox" id="inlineCheckbox4" name="partner_showed" value="1"> Showed Trump
</label>
					<label class="checkbox-inline">
  <input type="checkbox" id="inlineCheckbox5" name="partner_one_last" value="1"> Played 1-Trump Last
</label>--><br>
        <br>
        <p><b>Points won or lost by THE PARTNER (ex, 40 or -40 Whole numbers only). Bidder and team scores will be auto-calculated):</b></p>
        <button type="button" class="btn btn-sm" aria-label="Left Align"><span class="glyphicon glyphicon-plus" aria-hidden="true"> won</span>
</button><button type="button" class="btn btn-sm" aria-label="Left Align"><span class="glyphicon glyphicon-minus" aria-hidden="true"> lost</span>
</button>
        <input type="number" class="form-control center-block" id="score" name="score" placeholder="# /per player" style="width: 200px;">
      </div>
    </div>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h2 class="panel-title text-center bg-primary">Second Team</h2>
      </div>
    </div>
    <div class="panel-body text-center"> Team Member <small><a href="add_player.php"> add new player</a></small><br>
      <select class="form-control center-block" id="tm1" name="tm1" style="width: 80%;">
        <option></option>
        <?php
do {  
?>
        <option value="<?php echo $row_RS_users['id']?>"><?php echo $row_RS_users['first_name']?> <?php echo $row_RS_users['last_name']?></option>
        </option>
        <?php
} while ($row_RS_users = mysql_fetch_assoc($RS_users));
  $rows = mysql_num_rows($RS_users);
  if($rows > 0) {
      mysql_data_seek($RS_users, 0);
	  $row_RS_users = mysql_fetch_assoc($RS_users);
  }
?>
      </select>
      <!--<label class="checkbox-inline text-center">
  <input type="checkbox" id="inlineCheckbox6" name="tm1_showed" value="1"> Showed Trump
</label>
<label class="checkbox-inline">
  <input type="checkbox" id="inlineCheckbox7" name="tm1_one_last" value="1"> Played 1-Trump Last
</label>--> 
    </div>
    <div class="panel-body text-center"> Team Member <small><a href="add_player.php"> add new player</a></small><br>
      <select class="form-control center-block" id="tm2" name="tm2" style="width: 80%;">
        <option></option>
        <?php
do {  
?>
        <option value="<?php echo $row_RS_users['id']?>"><?php echo $row_RS_users['first_name']?> <?php echo $row_RS_users['last_name']?></option>
        </option>
        <?php
} while ($row_RS_users = mysql_fetch_assoc($RS_users));
  $rows = mysql_num_rows($RS_users);
  if($rows > 0) {
      mysql_data_seek($RS_users, 0);
	  $row_RS_users = mysql_fetch_assoc($RS_users);
  }
?>
      </select>
      <!--<label class="checkbox-inline text-center">
  <input type="checkbox" id="inlineCheckbox8" name="tm2_showed" value="1"> Showed Trump
</label>
<label class="checkbox-inline">
  <input type="checkbox" id="inlineCheckbox9" name="tm2_one_last" value="1"> Played 1-Trump Last
</label>--> 
    </div>
    <span id="addplayer3">
    <div class="panel-body text-center"> Team Member <small><a href="add_player.php"> add new player</a></small><br>
      <select class="form-control center-block" id="tm3" name="tm3" style="width: 80%;">
        <option></option>
        <?php
do {  
?>
        <option value="<?php echo $row_RS_users['id']?>"><?php echo $row_RS_users['first_name']?> <?php echo $row_RS_users['last_name']?></option>
        </option>
        <?php
} while ($row_RS_users = mysql_fetch_assoc($RS_users));
  $rows = mysql_num_rows($RS_users);
  if($rows > 0) {
      mysql_data_seek($RS_users, 0);
	  $row_RS_users = mysql_fetch_assoc($RS_users);
  }
?>
      </select>
      <!--<label class="checkbox-inline text-center">
  <input type="checkbox" id="inlineCheckbox10" name="tm3_showed" value="1"> Showed Trump
</label>
<label class="checkbox-inline">
  <input type="checkbox" id="inlineCheckbox11" name="tm3_one_last" value="1"> Played 1-Trump Last
</label>--></div>
    </span> <span id="addplayer4">
    <div class="panel-body text-center"> Team Member <small><a href="add_player.php"> add new player</a></small><br>
      <select class="form-control center-block" id="tm4" name="tm4" style="width: 80%;">
        <option></option>
        <?php
do {  
?>
        <option value="<?php echo $row_RS_users['id']?>"><?php echo $row_RS_users['first_name']?> <?php echo $row_RS_users['last_name']?></option>
        </option>
        <?php
} while ($row_RS_users = mysql_fetch_assoc($RS_users));
  $rows = mysql_num_rows($RS_users);
  if($rows > 0) {
      mysql_data_seek($RS_users, 0);
	  $row_RS_users = mysql_fetch_assoc($RS_users);
  }
?>
      </select>
      <!--<label class="checkbox-inline text-center">
  <input type="checkbox" id="inlineCheckbox8" name="tm2_showed" value="1"> Showed Trump
</label>
<label class="checkbox-inline">
  <input type="checkbox" id="inlineCheckbox9" name="tm2_one_last" value="1"> Played 1-Trump Last
</label>--> 
    </div>
    </span> <br>
    <br>
    <div class="center-block text-center">
      <input class="btn btn-primary btn-lg active" type="submit" value="Submit Score">
    </div>
  </div>
  <br>
</form>

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) --> 
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script> 
<!-- Include all compiled plugins (below), or include individual files as needed --> 
<script src="pt.blueprint.js"></script> 
<script>
		/* since the default is 5 players, we are hding the extra team member*/
		$( "#addplayer4" ).hide();
		
		/* we need to hide element if the bidder called herself... */
		$( "#calledself" ).on ( "click", function() {
			$( "#partner" ).toggle().val('');
			$( "#addplayer4" ).toggle().val('');
		});
		
		/* we need to hide elements if there's 4 players */
		$( "#4players" ).on ( "click", function() {
			$( "#partner" ).hide().val('');
			$( "#addplayer4" ).hide().val('');
			$( "#addplayer3" ).show();
			$( "#selfcalled" ).hide().val('');
			$( "#suitcalled" ).hide().val('');
			$('#partner option:eq(0)').attr('selected','selected');
			$('#addplayer4 option:eq(0)').attr('selected','selected');
			$('#suitcalled option:eq(0)').attr('selected','selected');
			$('#calledself').removeAttr('checked');
		});
		
		/* we need to hide elements if there's 3 players */
		$( "#3players" ).on ( "click", function() {
			$( "#partner" ).hide().val('');
			$( "#addplayer4" ).hide().val('');
			$( "#addplayer3" ).hide().val('');
			$( "#selfcalled" ).hide().val('');
			$( "#suitcalled" ).hide().val('');
			$('#partner option:eq(0)').attr('selected','selected');
			$('#addplayer4 option:eq(0)').attr('selected','selected');
			$('#addplayer3 option:eq(0)').attr('selected','selected');
			$('#suitcalled option:eq(0)').attr('selected','selected');
			$('#calledself').removeAttr('checked');
		});
		
		/* we need to show elements if there's 5 players, in case user toggles */
		$( "#5players" ).on ( "click", function() {
			$( "#partner" ).show();
			$( "#addplayer4" ).hide().val('');
			$( "#addplayer3" ).show();
			$( "#selfcalled" ).show();
			$( "#suitcalled" ).show();
			//$('#mySelect').val('');
		});
		
	function validateForm() {
    var x = document.forms["form"]["bidder"].value;
    if (x == null || x == "") {
        alert("choose the bidder");
        return false;
    }
	var x = document.forms["form"]["bid"].value;
    if (x == null || x == "") {
        alert("What was the bid?");
        return false;
	}
	if (document.getElementById("4players").checked == false && document.getElementById("3players").checked == false) {
	var x = document.forms["form"]["king"].value;
    if (x == null || x == "") {
        alert("Which suit was choosen?");
        return false;
	}}
	if (document.getElementById("4players").checked == false && document.getElementById("calledself").checked == false && document.getElementById("3players").checked == false) {
	var x = document.forms["form"]["partner"].value;
   	 if (x == null || x == "") {
        alert("choose the partner");
        return false;
    }}
	var x = document.forms["form"]["score"].value;
    if (x == null || x == "") {
        alert("how many points were won/lost?");
        return false;
    }
	var x = document.forms["form"]["tm1"].value;
    if (x == null || x == "") {
        alert("missing a team member");
        return false;
    }
	var x = document.forms["form"]["tm2"].value;
    if (x == null || x == "") {
        alert("missing a team member");
        return false;
    }
	if (document.getElementById("3player").checked == false) {
	var x = document.forms["form"]["tm3"].value;
    if (x == null || x == "") {
        alert("missing a team member");
        return false;
    }}
	if (document.getElementById("4players").checked == false && document.getElementById("calledself").checked == true && document.getElementById("3player").checked == false) {
	var x = document.forms["form"]["tm4"].value;
    if (x == null || x == "") {
        alert("missing a team member");
        return false;
    }}
}
	</script>
</body>
</html>
<?php
mysql_free_result($RS_users);
?>