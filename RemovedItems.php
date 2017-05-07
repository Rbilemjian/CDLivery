<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">
<?php
	require_once('C:/wamp64/www/cd/Includes/Functions.php');
	session_start();
	UserPrintout();
	if(!isAdmin)
	{
		die("Must be logged in as an administrator to view this page");
	}
	ListRemoved();
?>
<html lang="en">
	<head>
		<title>Removed Items</title>
	</head>
</html>
