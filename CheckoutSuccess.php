<?php require_once('C:/wamp64/www/cd/Includes/Functions.php'); ?>

<html lang="en">
	<head>
		<title>CheckoutSuccess</title>
	</head>
	<body>
	<?php
		require_once('C:/wamp64/www/cd/Includes/Functions.php'); 
		session_start();
		UserPrintout();
		if(!loggedIn() || isAdmin())
		{
			die("Must be logged in as a user in order to view this page");
		}
		checkTimeout();
		clearCart($_SESSION['id']);
		echo '<div align="center">';
		echo "Order was successfully placed. ";
	?>
	</body>
</html>
