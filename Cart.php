<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">

<html lang="en">
	<head>
		<title>Cart</title>
	</head>
	<body>
	<?php
		require_once('C:/wamp64/www/cd/Includes/Functions.php');
		session_start();
		UserPrintout();
		if(loggedIn())
		{
			checkTimeout();
			displayCart($_SESSION['id']);
		}
		else
		{
			echo "Must be logged in to view cart.";
		}
		
		
	?>
	</body>
</html>
