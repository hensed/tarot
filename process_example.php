<?php require_once('../Connections/mysql_connect.php'); ?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "";
$MM_donotCheckaccess = "true";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strUsers == "") && true) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "../account_registration.php";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($QUERY_STRING) && strlen($QUERY_STRING) > 0) 
  $MM_referrer .= "?" . $QUERY_STRING;
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
}
?>
<?php
//decrypting querystring
$qstring = $_SERVER['QUERY_STRING'];
$qstring = base64_decode($qstring);
$values = explode(",",$qstring);
$username = $values[0];
$recordID = $values[1];
$price = $values[2];
$ordertype = $values[3];
if (isset($values[4])) {
$credit_used = $values[4];
}

if ($ordertype == 1 || $ordertype == 2) {
	$transtype = 1;
} else {
	$transtype = 0;
}

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
mysql_select_db($database_mysql_connect, $mysql_connect);
$query_rsGetRequested = "SELECT user_events.user_id, users.credit_balance FROM user_events JOIN users on users.user_id = user_events.user_id WHERE user_events.is_requested = 1 AND user_events.event_id = '$recordID'";
$rsGetRequested = mysql_query($query_rsGetRequested, $mysql_connect) or die(mysql_error());
$row_rsGetRequested = mysql_fetch_assoc($rsGetRequested);
$totalRows_rsGetRequested = mysql_num_rows($rsGetRequested);

mysql_select_db($database_mysql_connect, $mysql_connect);
$query_rsgetUserID = "SELECT user_id, first_name, last_name, email, users.credit_balance FROM users WHERE users.username = '$username'";
$rsgetUserID = mysql_query($query_rsgetUserID, $mysql_connect) or die(mysql_error());
$row_rsgetUserID = mysql_fetch_assoc($rsgetUserID);
$totalRows_rsgetUserID = mysql_num_rows($rsgetUserID);
$user_id = $row_rsgetUserID['user_id'];

mysql_select_db($database_mysql_connect, $mysql_connect);
$query_CheckUserEvents = "SELECT user_events.user_events_id FROM user_events WHERE user_events.user_id = '$user_id' and event_id = '$recordID'";
$CheckUserEvents = mysql_query($query_CheckUserEvents, $mysql_connect) or die(mysql_error());
$row_CheckUserEvents = mysql_fetch_assoc($CheckUserEvents);
$totalRows_CheckUserEvents = mysql_num_rows($CheckUserEvents);


 
//for video purchases
if ((isset($username)) && isset($recordID) && $totalRows_CheckUserEvents == 0 && $transtype == 0) {
  	$insertSQL = sprintf("INSERT INTO orders (user_id, event_id, price, order_type) VALUES ('$user_id', '$recordID', '$price', '$ordertype')");
	mysql_select_db($database_mysql_connect, $mysql_connect);
  	$Result1 = mysql_query($insertSQL, $mysql_connect) or die(mysql_error());																		  

	if ($ordertype == 2) {
		$insertSQL = sprintf("INSERT INTO user_events (user_id, event_id, is_purchased, streaming_only) VALUES ('$user_id', '$recordID', 1, 1)");
		mysql_select_db($database_mysql_connect, $mysql_connect);
  		$Result1 = mysql_query($insertSQL, $mysql_connect) or die(mysql_error());
	} Else {
		$insertSQL = sprintf("INSERT INTO user_events (user_id, event_id, is_purchased) VALUES ('$user_id', '$recordID', 1)");
		mysql_select_db($database_mysql_connect, $mysql_connect);
  		$Result1 = mysql_query($insertSQL, $mysql_connect) or die(mysql_error());
	}
	if (isset($credit_used)) {
		$new_bal = ($row_rsgetUserID['credit_balance'] - $credit_used);
		$updateSQL = sprintf("UPDATE users SET credit_balance = '$new_bal' where users.user_id = '$user_id'");
		mysql_select_db($database_mysql_connect, $mysql_connect);
  		$Result2 = mysql_query($updateSQL, $mysql_connect) or die(mysql_error());
		
		$updateSQL = sprintf("UPDATE orders SET credits_used = '$credit_used' where orders.user_id = '$user_id' ORDER BY order_id DESC LIMIT 1");
		mysql_select_db($database_mysql_connect, $mysql_connect);
  		$Result2 = mysql_query($updateSQL, $mysql_connect) or die(mysql_error());
	}
	
	if ($ordertype == 2) {
		$credit_amt = 1.00;
	} else {
		$credit_amt = 3.00;
	}
	$requester_id = $row_rsGetRequested['user_id'];
	$requester_bal = $row_rsGetRequested['credit_balance'];
	$add_credit = ($row_rsGetRequested['credit_balance'] + $credit_amt);
	$updateSQL = sprintf("UPDATE users, orders SET users.credit_balance = '$add_credit' where users.user_id = '$requester_id'");
	mysql_select_db($database_mysql_connect, $mysql_connect);
  	$Result1 = mysql_query($updateSQL, $mysql_connect) or die(mysql_error());	
} else {
	 header( 'Location: error.php' ) ;
}
//same as above, but for event requests
if ((isset($username)) && isset($recordID) && $totalRows_CheckUserEvents == 1 && $transtype == 0) {
  $insertSQL = sprintf("INSERT INTO orders (user_id, event_id, order_type, price) VALUES ('$user_id', '$recordID', '$ordertype', '$price')");
	mysql_select_db($database_mysql_connect, $mysql_connect);
  	$Result1 = mysql_query($insertSQL, $mysql_connect) or die(mysql_error());																		  
	
	$updateSQL = sprintf("UPDATE user_events SET is_purchased = 1 where user_events.user_id = '$user_id' and event_id = '$recordID'");
	mysql_select_db($database_mysql_connect, $mysql_connect);
  	$Result2 = mysql_query($updateSQL, $mysql_connect) or die(mysql_error());
	
	if (isset($credit_used)) {
		$new_bal = ($row_rsgetUserID['credit_balance'] - $credit_used);
		$updateSQL = sprintf("UPDATE users SET credit_balance = '$new_bal' where users.user_id = '$user_id'");
		mysql_select_db($database_mysql_connect, $mysql_connect);
  		$Result2 = mysql_query($updateSQL, $mysql_connect) or die(mysql_error());
		
		$updateSQL = sprintf("UPDATE orders SET credits_used = '$credit_used' where orders.user_id = '$user_id' ORDER BY order_id DESC LIMIT 1");
		mysql_select_db($database_mysql_connect, $mysql_connect);
  		$Result2 = mysql_query($updateSQL, $mysql_connect) or die(mysql_error());
	}
} else {
	 header( 'Location: error.php' ) ;
}
?>
<?php //send Notification e-mails
mysql_select_db($database_mysql_connect, $mysql_connect);
$query_rsEventVerify = "SELECT * FROM events WHERE event_id = '$recordID'";
$rsEventVerify = mysql_query($query_rsEventVerify, $mysql_connect) or die(mysql_error());
$row_rsEventVerify = mysql_fetch_assoc($rsEventVerify);
$totalRows_rsEventVerify = mysql_num_rows($rsEventVerify);
$event_type = $row_rsEventVerify['event_type'];
	
mysql_select_db($database_mysql_connect, $mysql_connect);
$query_rsEventType = "SELECT event_type.type FROM event_type WHERE event_type.event_type_id = '$event_type'";
$rsEventType = mysql_query($query_rsEventType, $mysql_connect) or die(mysql_error());
$row_rsEventType = mysql_fetch_assoc($rsEventType);
$totalRows_rsEventType = mysql_num_rows($rsEventType);


//db to e-mail variables
$eventdate = $row_rsEventVerify['event_date'];
$venue = $row_rsEventVerify['venue'];
$eventtype = $row_rsEventType['type'];
$start_time = $row_rsEventVerify['event_time'];
$member_email = $row_rsgetUserID['email'];
$firstname = $row_rsgetUserID['first_name'];
$lastname = $row_rsgetUserID['last_name'];
$eventname = $row_rsEventVerify['event_name']; 
$team1 = $row_rsEventVerify['team_1']; 
$team2 = $row_rsEventVerify['team_2']; 
$address = $row_rsEventVerify['venue_address'];
$city = $row_rsEventVerify['venue_city'];
$zip = $row_rsEventVerify['venue_zip'];


//notification to mtv support
$to = "support@myteamvids.com";
if ($ordertype == 0) {
	$subject = "URGENT: An EVENT VIDEOGRAPHY has been requested!!";
	$message = "An event Videography has been requested: \n\n Event Name: $eventname \n Event Date: $eventdate \n Start Time: $start_time \n Venue Name: $venue \n Venue Address: $address \n City: $city \n Zip: $zip \n Teams: $team1 (home team) vs $team2 \n Sport: $eventtype";
} else {
	$subject = "An Event Video has been purchased!!";
	$message = "YEY!! we have customers!! $eventname has been purchased by user ID: $user_id";
}
	
$from = "support@myteamvids.com";
$headers = "From: $from";
mail($to,$subject,$message,$headers);

//notification to customer
$to = "$member_email";
if ($ordertype == 0) {
	$subject = "myTeamVids.com Videography request confirmation";
	$message = "Hello $firstname $lastname ,\n\n MyTeamVids.com thanks you for your order. Please read the details of your request below to assure it's accuracy: \n\n Event Name: $eventname \n Event Date: $eventdate \n Start Time: $start_time \n Venue Name: $venue \n Venue Address: $address \n City: $city \n Zip: $zip \n Teams: $team1 (home team) vs $team2 \n Sport: $eventtype \n\n If you have any questions about your request, please read our FAQ page or simply reply to this email. We will reply as soon as possible.";
} else {
	$subject = "myTeamVids.com Video purchase confirmation";
	$message = "MyTeamVids.com thanks you for your order. Please read the details of your request below to assure it's accuracy: \n\n You have purchased acces to Event: $eventname \n\n If you have any questions about your request, please read our FAQ page or simply reply to this email. We will reply as soon as possible.";
}

$from = "support@myteamvids.com";
$headers = "From: $from";
mail($to,$subject,$message,$headers);

?>
<?php
mysql_free_result($rsgetUserID);
mysql_free_result($CheckUserEvents);
mysql_free_result($rsEventType);
mysql_free_result($rsGetRequested);
mysql_free_result($rsEventVerify);
?>
<?php if ($transtype < 10) {
	  header( 'Location: myportal.php' ) ;
} 
?>