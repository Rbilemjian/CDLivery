<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">

<html lang="en">
	<head>
		<title>View User Items</title>
	</head>
	<body>
	<?php
		require_once('C:/wamp64/www/cd/Includes/Functions.php');
		session_start();
		UserPrintout();
		if(!loggedIn())
		{
			echo "Must be logged in as a user in order to view this page.";
			die();
		}
		echo '<div align="center">';
		checkTimeout();
		if(!isset($_POST['submit1']))
		{
			echo "Must navigate to this page from user order list page.";
			die();
		}
		echo "<b><u>Your Order Items </b></u></br></br>";
		displayOrderItems($_POST['orderID']);
		unset($_SESSION['orderID']);
		?>
	</body>
</html>
