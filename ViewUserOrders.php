<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">

<html lang="en">
	<head>
		<title>User Orders</title>
	</head>
	<body>
	<?php
		require_once('C:/wamp64/www/cd/Includes/Functions.php');
		session_start();
		UserPrintout();
		echo '<div align="center">';
		echo '<b><u>Your Orders</b></u></br></br>';
		if(loggedIn())
		{
			checkTimeout();
			displayUserOrders();
		}
		else
		{
			echo "Must be logged in to view cart.";
		}
		
		
	?>
	</body>
</html>
