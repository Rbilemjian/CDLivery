<?php require_once('C:/wamp64/www/cd/Includes/Functions.php'); ?>

<html lang="en">
	<head>
		<title>EditUserPrivileges</title>
	</head>
	<body>
	<?php
		require_once('C:/wamp64/www/cd/Includes/Functions.php'); 
		session_start();
		UserPrintout();
		if(!IsAdmin())
		{
			die("Must be logged in as an administrator in order to access this page");
		}
		echo '<table border="1">';
		echo "<tr><th>Username</th><th>Type</th><th>Edit</th><tr>";
		listUsers("admin");
		listUsers("user");
	?>
	</body>
</html>