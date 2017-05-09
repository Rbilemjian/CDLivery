<?php
		require_once('C:/wamp64/www/cd/Includes/Functions.php'); 
		session_start();
		UserPrintout();
		echo '<div align="center">';
		echo '<b><u>Edit Account</b></u></br></br>';
		if(!loggedIn())
		{
			die("Must be logged in as a user in order to view this page");
		}
		checkTimeout();
		if(isset($_POST['submit']))
		{
			echo '<div align="center">';
			if(validateLength($_POST['newName']) == true && findUserByName($_POST['newName']) == null && checkPassword($_POST['pass']) == true)
			{
				modifyName($_POST['newName']);
			}
			else if(checkPassword($_POST['pass']) == false)
			{
				echo "Invalid password. Please try again.";
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
Enter a new username for your account, as well as your current password
</br>
<form method="post">
New Username: <input type="text" name="newName">
</br>
Current Password: <input type="password" name="pass">
</br>
<input type="submit" name="submit" value="Submit">
</form>
</body>
</html>