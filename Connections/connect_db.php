<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
if ($_SERVER['SERVER_NAME'] == "palantarot.com") {
	$hostname_connect_db = "localhost:8889";
	$database_connect_db = "palantarot";
	$username_connect_db = "root";
	$password_connect_db = "root";
	
	$hostname_connect_db = "localhost";
	$database_connect_db = "palantar_tarot";
	$username_connect_db = "palantar";
	$password_connect_db = "zV24wuu97F";
} else {
	$hostname_connect_db = "localhost";
	$database_connect_db = "palantar_tarot";
	$username_connect_db = "palantar";
	$password_connect_db = "zV24wuu97F";
}
$connect_db = mysql_pconnect($hostname_connect_db, $username_connect_db, $password_connect_db) or trigger_error(mysql_error(),E_USER_ERROR); 
?>