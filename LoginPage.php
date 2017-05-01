<?php 
		require_once('C:/wamp64/www/cd/Includes/Functions.php'); 
		session_start();
		if(isset($_GET['logout']) && $_GET['logout'] == true) //if user was logged in and chose to log out
		{
			$_SESSION['username'] = null;
			$_SESSION['id'] = null;
		}
		if(isset($_SESSION['username'])) //if user is logged in already
		{
			echo "You are already logged in.";
			echo "<br />";
			echo '<a href="?logout=true"><b>Log out</b></a>';
			echo "<br />";
			echo '<a href="MainPage.php"><b>Main Page</b></a>';
			die();
		}
		if(isset($_POST['submit']))
		{
			if(empty($errors))
			{
				$username = $_POST["username"];
				$password = $_POST["password"];
				$user = findUser($username,$password);
				if($user!=null)
				{
					$_SESSION["username"] = $user["username"];
					$_SESSION["id"] = $user["id"];
					$_SESSION["type"] = $user["type"];
					redirectTo("MainPage.php");
					//}
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
