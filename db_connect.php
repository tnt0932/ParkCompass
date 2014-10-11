<?php

// Connect to the database
$db = mysql_connect($hostname, $username, $password);
if (!$db) {  die('Could not connect to db : ' . mysql_error());} 

// Select the database
$db_selected = mysql_select_db($database, $db);
if (!$db_selected) {
  die ('Couldn\'t select db : ' . mysql_error());
}

?>