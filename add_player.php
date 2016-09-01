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

// *** Redirect if username exists
$MM_flag="MM_insert";
if (isset($_POST[$MM_flag])) {
  $MM_dupKeyRedirect="add_player.php?ap=2";
  $loginUsername = $_POST['email'];
  $LoginRS__query = sprintf("SELECT email FROM players WHERE email=%s", GetSQLValueString($loginUsername, "text"));
  mysql_select_db($database_connect_db, $connect_db);
  $LoginRS=mysql_query($LoginRS__query, $connect_db) or die(mysql_error());
  $loginFoundUser = mysql_num_rows($LoginRS);

  //if there is a row in the database, the username was found - can not add the requested username
  if($loginFoundUser){
    $MM_qsChar = "?";
    //append the username to the redirect page
    if (substr_count($MM_dupKeyRedirect,"?") >=1) $MM_qsChar = "&";
    $MM_dupKeyRedirect = $MM_dupKeyRedirect . $MM_qsChar ."requsername=".$loginUsername;
    header ("Location: $MM_dupKeyRedirect");
    exit;
  }
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form")) {
  $insertSQL = sprintf("INSERT INTO players (first_name, last_name, email) VALUES (%s, %s, %s)",
                       GetSQLValueString($_POST['fname'], "text"),
                       GetSQLValueString($_POST['lname'], "text"),
                       GetSQLValueString($_POST['email'], "text"));

  mysql_select_db($database_connect_db, $connect_db);
  $Result1 = mysql_query($insertSQL, $connect_db) or die(mysql_error());

  $insertGoTo = "add_player.php?ap=1";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title>Add a Player</title>

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
  <div class="container text-center"><br>
  <?php if ($_GET["ap"] == 2) {?>
  <div class="alert alert-success" role="alert">Good News! You were already in our database!</div>
  <?php }?>
  <?php if ($_GET["ap"] == 1) {?>
  <div class="alert alert-success" role="alert">Great!! You are now ready to play French Tarot.</div>
  <?php }?>
    	<p><h3> Add a player to the French Tarot db</h3></p><br>
    <form method="POST" action="<?php echo $editFormAction; ?> " onsubmit="return validateForm()" name="form" role="form">
  			<div class="form-group">
    			<label for="lastname">First Name</label>
    			<input type="fname" class="form-control" name="fname" id="fname" placeholder="Enter First Name" required>
  			</div>
            <div class="form-group">
    			<label for="firstname">Last Name</label>
    			<input type="lname" class="form-control"  name="lname" id="lname" placeholder="Enter Last Name" required>
  			</div>
            <div class="form-group">
    			<label for="email">Palantir Email Address</label>
    			<input type="email" class="form-control"  name="email" id="email" placeholder="Enter Your Palantir Email" required>
  			</div>
            <button type="submit" class="btn btn-success">Save Player</button>
            <input type="hidden" name="MM_insert" value="form">
    </form>
  </div><br>
  <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="pt.blueprint.js"></script>
    <script>
	function validateForm() {
    var x = document.forms["form"]["fname"].value;
    if (x == null || x == "") {
        alert("First name must be filled out");
        return false;
    }
	var x = document.forms["form"]["lname"].value;
    if (x == null || x == "") {
        alert("last name must be filled out");
        return false;
	}
	var x = document.forms["form"]["email"].value;
    if (x == null || x == "") {
        alert("email must be filled out");
        return false;
	}
}
</script>
  </body>
</html>
<?php
mysql_free_result($RS_users);
?>