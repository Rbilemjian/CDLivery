<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">

<html lang="en">
	<head>
		<title>MoviesPage</title>
	</head>
	<body>
	<?php
		require_once('C:/wamp64/www/cd/Includes/Functions.php');
		session_start();
		UserPrintout();
		$connection = mysqli_connect("localhost","cd_user","password","cd_livery");
		
		if(mysqli_connect_errno())
		{
			die("Database connection failed: " . mysqli_connect_error() . " (" . mysqli_connect_errno() . ")");
		}
		
		$query = "SELECT * FROM movies WHERE visible = 1";
		$result = mysqli_query($connection,$query);
		if(!$result)
		{
			die("Database query failed");
		}
	?>
	<table border="1">
	<?php
		echo "<tr><th>Title</th><th>Genre</th><th>Year of Release</th><th>Stock</th></tr>"; 
		while($movie = mysqli_fetch_assoc($result))
		{
			echo "<tr><td>";
			echo $movie["movie_name"];
			echo "</td><td>";
			echo $movie["genre"];
			echo "</td><td>";
			echo $movie["release_year"];
			echo "</td><td>";
			echo $movie["stock"];
			echo "</td></tr>";
		}
		mysqli_free_result($result);
		mysqli_close($connection);
	?>
	</table>
	</body>
</html>
