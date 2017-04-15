<?php
	function redirectTo($page)
	{
		header("Location: ".$page);
		exit;
	}
	function findUser($username,$password)
	{
		$connection = mysqli_connect("localhost","cd_user","password","cd_livery");
		$result = mysqli_query($connection,"SELECT * FROM users");
		$password = hash('sha512',$_POST["password"]);
		while($user=mysqli_fetch_assoc($result))
		{
			if($username == $user['username']&&strcmp($password,$user['password'])==0)
			{
				return $user;
			}
		}
		return null;
	}
	function findUserbyName($username)
	{
		$connection = mysqli_connect("localhost","cd_user","password","cd_livery");
		$result = mysqli_query($connection,"SELECT * FROM users");
		while($user=mysqli_fetch_assoc($result))
		{
			if($username == $user['username'])
			{
				return $user;
			}
		}
		return null;
	}
	/*function validatePresences($required_fields)
	{
		if($_POST["username"]=="" || $_POST["password"]=="")
		{
			echo "You need to enter both a username and password";
			//header("");
		}
		else
		{
			return;
		}
	}*/
	function validatePresences($required_fields)
	{
		if($_POST["username"]=="" || $_POST["password"]==""||$_POST["password2"]=="")
		{
			return false;
		}
		return true;
	}

	function validateLength($string)
	{
		if(strlen($string)>=5)
		{
			return true;
		}
		return false;
	}
	function addUser($username,$password)
	{
		$password = hash('sha512',$password);
		$connection = mysqli_connect("localhost","cd_user","password","cd_livery");
		$query = "INSERT INTO users(username,password)
				  VALUES ('{$username}','{$password}')";
		$result = mysqli_query($connection,$query);
		if($result == false)
			return false;
		else
		{
			return true;
		}
	}
	function newEntry($name,$genre,$visible,$stock,$releaseyear,$itemType)
	{
		$connection=mysqli_connect("localhost","cd_user","password","cd_livery");
		if (mysqli_connect_errno())
		{
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		if($itemType==0)
		{
			$sql="INSERT INTO albums (album_name,genre,visible,stock,release_year)VALUES
			({$name},{$genre},{$visible},{$stock},{$releaseyear})";
		}
		if($itemType==1)
		{
			$sql="INSERT INTO movies (movie_name,genre,visible,stock,release_year)VALUES
			({$name},{$genre},{$visible},{$stock},{$releaseyear})";
		}
		if($itemType==2)
		{
			$sql="INSERT INTO games (game_name,genre,visible,stock,release_year)VALUES";
			$sql.=" ('{$name}','{$genre}',{$visible},{$stock},{$releaseyear})";
		}
		$result = mysqli_query($connection, $sql);

		if ($result && mysqli_affected_rows($connection) == 1) 
		{
			$result= "Success!";
		} 
		else 
		{
			die("Database query failed. " . mysqli_error($connection));
		}
		mysqli_close($connection);
		return $result;
	}
	function UserPrintout()
	{
		if(isset($_GET['logout']) && $_GET['logout'] == "true")
		{
			$_SESSION['username'] = null;
			$_SESSION['id'] = null;
		}
		if(isset($_SESSION['username']))
		{
			echo '<p align =  "right">'."Logged in as: ".'<b>'.$_SESSION['username'].'</b>';
			echo '&nbsp;&nbsp;&nbsp;&nbsp';
			echo '<a href="?logout=true"><b>Log out</b></a>';
		}
		else
		{
			echo '<p align = "right"><a href="LoginPage.php">Log in</a>';
		}
	}
?>