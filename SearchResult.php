<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">

<html lang="en">
	<head>
		<title>SearchResult</title>
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
		if(isset($_POST['search']))
		{
			$_SESSION['search'] = $_POST['search'];
		}
		if(!isset($_SESSION['search']))
		{
			die("Must search for something in order to view this page");
		}
		searchBar();
		//show results here
		if($_SESSION['search'] == "")
		{
			echo "</br>Please type something into the search bar in order to search";
		}
		else
		{
			getResults($_SESSION['search']);
		}
		
	?>
	
	</body>
</html>
