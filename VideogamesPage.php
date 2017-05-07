<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">

<html lang="en">
	<head>
		<title>VideogamesPage</title>
	</head>
	<body>
	<?php
		require_once('C:/wamp64/www/cd/Includes/Functions.php');
		session_start();
		UserPrintout();
		if(isset($_SESSION['type']) && $_SESSION['type'] == 'admin')
		{
			AdminList("SELECT * FROM cds WHERE visible=1 AND type='Game'",'Game');
		}
		else
		{
			printList("SELECT * FROM cds WHERE visible=1 AND type='Game' AND stock!=0",'Game');
		}
	?>
	
	</body>
</html>
