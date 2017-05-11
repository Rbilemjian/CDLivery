<?php require_once('C:/wamp64/www/cd/Includes/Functions.php');
session_start(); ?>

<html lang="en">
	<head>
		<title>RenameSuccess</title>
	</head>
	<body>
	<?php
		if(loggedIn()==false)
		{
			die("Must be logged in to view this page");
		}
		checkTimeout();
		userPrintout();
		echo '<div align="center">';
		echo "Account information successfully changed.";
	?>
	</body>
</html>
