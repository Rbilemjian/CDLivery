<div align = "center">
<?php 
		session_start();
		require_once('C:/wamp64/www/cd/Includes/Functions.php');
		if(loggedIn())
		{
			checkTimeout();
			echo "You are already logged in";
			echo '</br>';
			echo '<a href="MainPage.php"><b>Main Page</b></a>';
			die();
		}
		echo '<p align = "left">';
		echo '<a href="MainPage.php"><b>Main Page</b></a>';
		echo '</br>';
		echo '<p align="center">';
		if(isset($_POST['register1']))
		{
			$required_fields = array("username","password","password2");
			if(!validatePresences($required_fields)) //checks that fields were filled
			{
				echo ("You need to enter a username and your password twice");
				echo "<br />";
				goto errorcheckingdone;
			}
			if(!validateLength($_POST["username"]) || !validateLength($_POST["password"]))
				//checks length of username and password
			{
				echo "Entered username/password was too short";
				echo "<br />";
				goto errorcheckingdone;
			}
			$user = findUserbyName($_POST["username"]);
			if($user!=null) //checks that username already exists (invalid then)
			{
				echo "User already exists. Please choose another name.";
				echo "<br />";
				goto errorcheckingdone;
			}
			if(strcmp($_POST["password"],$_POST["password2"])!=0)
			{
				echo "Passwords do not match.";
				echo "<br />";
				goto errorcheckingdone;
			}
			if(addUser($_POST["username"],$_POST["password"]))
			{
				redirectTo("RegisterSuccess.php");
			}
			errorcheckingdone:
		}
		echo '<p align = "center">';
		echo "Choose a username and password. Minimum length: 6 characters"
		?>
	<html lang="en">
	<head>
		<title>RegisterPage</title>
	</head>
	<body>
	<form action="RegisterPage.php" method="post">
	Username:<input type="text" name="username" value=""/>
	<br />
	Password: <input type="password" name="password" value=""/>
	<br />
	Confirm Password: <input type="password" name="password2" value=""/>
	<br />
	<input type="submit" name="register1" value="Register" />
	</form>
</html>
