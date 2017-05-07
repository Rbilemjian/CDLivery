<?php require_once('C:/wamp64/www/cd/Includes/Functions.php'); ?>

<html lang="en">
	<head>
		<title>RegisterSuccess</title>
	</head>
	<body>
	<?php
		if(loggedIn())
		{
			die("Error: must be logged out to view this page");
		}
		echo "Register Successful! Congratulations!";
	?>
	<a href="LoginPage.php">Log in</a>
	</body>
</html>
