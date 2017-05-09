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
		echo '<div align="center">';
		echo '<b><u>Movies</b></u></br></br>';
		contextSearchBar();
		echo "</br>";
		if(isset($_POST['submit']))
		{
			if($_POST['search']=="")
			{
				die("Must type a query in order to search");
			}
			else
			{
				contextGetResults($_POST['search'], 'Movie');
				die();
			}
		}
		if(isset($_SESSION['type']) && $_SESSION['type'] == 'admin')
		{
			AdminList("SELECT * FROM cds WHERE visible=1 AND type='Movie'");
		}
		else
		{
			printList("SELECT * FROM cds WHERE visible=1 AND type='Movie' AND stock!=0");
		}
	?>
	</body>
</html>
