<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">
<?php
	require_once('C:/wamp64/www/cd/Includes/Functions.php');
	session_start();
	UserPrintout();
	if(loggedIn())
	{
		checkTimeout();
	}
?>
<html lang="en">
	<head>
		<title>MainPage</title>
	</head>
	<body>
		<div align="center">
		<b><u>CDlivery</b></u></br></br>
		<?php
		searchBar();
		?>
		<br />
		<a href="MoviesPage.php">Movies Database</a>
		<br />
		<a href="AlbumsPage.php">Albums Database</a>
		<br />
		<a href="VideogamesPage.php">Videogames Database</a>
		<?php
		if(IsAdmin())
		{
			?>
		<br />
		<br />
		<a href="InsertForm.php">New Database Entry</a>
		<br />
		<a href="ViewOrders">View Orders</a>
		<br />
		<a href="RemovedItems.php">Removed Items</a>
		<br />
		<a href="EditUserPrivileges">Edit User Privileges</a>
		<?php
		}
		echo '<br /><br /><b><u>Recently Added</b></u><br />';
		recentlyAdded();
		?>
	</body>
</html>
