<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">

<html lang="en">
	<head>
		<title>MoviesPage</title>
	</head>
	<body>
	<?php
		require_once('C:/wamp64/www/cd/Includes/Functions.php');
		session_start();
		if(loggedIn())
		{
			checkTimeout();
		}
		UserPrintout();
		if(isset($_SESSION['type']) && $_SESSION['type'] == 'admin')
		{
			AdminList("SELECT * FROM cds WHERE visible=1 AND type='Movie'",'Movie');
		}
		else
		{
			printList("SELECT * FROM cds WHERE visible=1 AND type='Movie' AND stock!=0",'Movie');
		}
	?>
	</body>
</html>
