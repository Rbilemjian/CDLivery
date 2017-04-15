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
	function newEntry($name,$genre,$stock,$releaseyear,$visible,$price,$type)
	{
		if($type == 0)	$typename = 'Album';
		if($type == 1)	$typename = 'Movie';
		if($type == 2)	$typename = 'Game';
		$connection=mysqli_connect("localhost","cd_user","password","cd_livery");
		if (mysqli_connect_errno())
		{
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		else
		{	
			$query  = "INSERT INTO cds (";
			$query .= "  name, type, genre, stock, release_year, visible, price";
			$query .= ") VALUES (";
			$query .= "'{$name}','{$typename}','{$genre}',{$stock},{$releaseyear},{$visible},{$price}";
			$query .= ")";
		}
		$result = mysqli_query($connection, $query);

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
	function printList($query,$type)
	{
		$connection = mysqli_connect("localhost","cd_user","password","cd_livery");
		
		if(mysqli_connect_errno())
		{
			die("Database connection failed: " . mysqli_connect_error() . " (" . mysqli_connect_errno() . ")");
		}
		$result = mysqli_query($connection,$query);
		if(!$result)
		{
			die("Database query failed");
		}
		echo '<table border="1">';
		echo "<tr><th>Title</th><th>Genre</th><th>Year of Release</th><th>Stock</th><th>Type</th><th>Price</th></tr>"; 
		$cd = mysqli_fetch_assoc($result);
		
			echo "<tr><td>";
			echo $cd["name"];
			echo "</td><td>";
			echo $cd["genre"];
			echo "</td><td>";
			echo $cd["release_year"];
			echo "</td><td>";
			echo $cd["stock"];
			echo "</td><td>";
			echo $cd["type"];
			echo "</td><td>";
			echo '$'.$cd["price"];
			echo "</td></tr>";
		
		echo '</table>';
	}
?>