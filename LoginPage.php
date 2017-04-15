<?php 
		require_once('C:/wamp64/www/cd/Includes/Functions.php'); 
		session_start();
		if(isset($_POST['submit']))
		{
			//$required_fields = array("username","password");
			//validatePresences($required_fields);
			if(empty($errors))
			{
				$username = $_POST["username"];
				$password = $_POST["password"];
				$user = findUser($username,$password);
				if($user!=null)
				{
					$_SESSION["username"] = $user["username"];
					$_SESSION["id"] = $user["id"];
					if(isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER']!=""
					&& $_SERVER['HTTP_REFERER']!="http://localhost/cd/LoginPage.php")
					{
						redirectTo($_SERVER['HTTP_REFERER']);
					}
					else
					{
						redirectTo("MainPage.php");
					}
				}
				else
				{
					echo "Invalid entry, please try again";
				}
			}
		}
	?>
	<div align="center">
	<html lang="en">
	<head>
		<title>LoginPage</title>
	</head>
	<body>
		<form action="LoginPage.php" method="post">
		Username: <input type="text" name="username" value="" /><br />
		Password: <input type="password" name="password" value="" /><br />
		<br />
		<input type="submit" name="submit" value="Submit" />
		</form>
		<form action="RegisterPage.php" method="post">
		<input type="submit" name="register" value="Register" />
		</form>
	</body>
</html>
