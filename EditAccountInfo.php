<?php
		require_once('C:/wamp64/www/cd/Includes/Functions.php'); 
		session_start();
		UserPrintout();
		if(!loggedIn())
		{
			die("Must be logged in as a user in order to view this page");
		}
		checkTimeout();
		if(isset($_POST['submit']))
		{
			if(validateLength($_POST['newName']) == true && findUserByName($_POST['newName']) == null)
			{
				modifyName($_POST['newName']);
			}
			else
			{
				echo "Username is invalid or already taken. Please enter another.</br>";
			}
		}
?>
<html>
<body>
<div align="center">
Enter a new username for your account
</br>
<form method="post">
<input type="text" name="newName">
<input type="submit" name="submit" value="Submit">
</form>
</body>
</html>