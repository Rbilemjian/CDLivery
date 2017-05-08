<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">

<html lang="en">
	<head>
		<title>ViewOrderItems</title>
	</head>
	<body>
	<?php
		require_once('C:/wamp64/www/cd/Includes/Functions.php');
		session_start();
		UserPrintout();
		if(!isAdmin())
		{
			echo "Must be logged in as an administrator in order to view this page.";
			die();
		}
		checkTimeout();
		if(!isset($_SESSION['orderID']))
		{
			echo "Must navigate to this page from order list page.";
			die();
		}
		displayOrderItems($_SESSION['orderID']);
		unset($_SESSION['orderID']);
		?>
	</body>
</html>
