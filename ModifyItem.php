<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">
<?php
	require_once('C:/wamp64/www/cd/Includes/Functions.php');
	session_start();
	UserPrintout();
	if(!isAdmin)
	{
		die("Must be logged in as an admin to view this page");
	}
	if(!isset($_SESSION['itemID']))
	{
		die("Must navigate to this page from administrator database page");
	}
	else
	{
		ModifyItem($_SESSION['itemID']);
	}
?>
<html lang="en">
	<head>
		<title>Modify Item</title>
	</head>
</html>
