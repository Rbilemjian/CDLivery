<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">

<html lang="en">
	<head>
		<title>AlbumsPage</title>
	</head>
	<body>
	<?php
		require_once('C:/wamp64/www/cd/Includes/Functions.php');
		session_start();
		UserPrintout();
		echo '<div align = "center">';
		echo '<b><u>Albums</b></u></br></br>';
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
				contextGetResults($_POST['search'], 'Album');
				die();
			}
		}
		if(loggedIn())
		{
			checkTimeout();
		}
		if(isset($_SESSION['type']) && $_SESSION['type'] == 'admin')
		{
			AdminList("SELECT * FROM cds WHERE visible=1 AND type='Album'");
		}
		else
		{
			printList("SELECT * FROM cds WHERE visible=1 AND type='Album' AND stock!=0");
		}
		?>
	</body>
</html>
