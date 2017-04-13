<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">

<html lang="en">
	<head>
		<title>VideogamesPage</title>
	</head>
	<body>
	<?php
		$connection = mysqli_connect("localhost","cd_user","password","cd_livery");
		
		if(mysqli_connect_errno())
		{
			die("Database connection failed: ". mysqli_connect_error(). " (". mysqli_connect_errno(). ")");
		}
		
		$result=mysqli_query($connection,"SELECT * FROM games WHERE visible=1");
		if(!$result)
		{
			die("Database query failed");
		}
	?>
	<table border="1">
	<?php
		echo "<tr><th>Title</th><th>Genre</th><th>Year of Release</th><th>Stock</th></tr>"; 
		while($game = mysqli_fetch_assoc($result))
		{
			echo "<tr><td>";
			echo $game["game_name"];
			echo "</td><td>";
			echo $game["genre"];
			echo "</td><td>";
			echo $game["release_year"];
			echo "</td><td>";
			echo $game["stock"];
			echo "</td></tr>";
		}
	?>
	</body>
</html>
