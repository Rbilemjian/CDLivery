<?php
		require_once('C:/wamp64/www/cd/Includes/Functions.php'); 
		session_start();
		UserPrintout();
		echo '<div align="center">';
		if(!IsAdmin())
		{
			die("Must be logged in as an administrator in order to add item.");
		}
		checkTimeout();
?>
<body>
<div align="center">
<b><u>New Database Entry</b></u></br></br>
<form action="InsertForm.php" method="post">
Select an item type!
<select id="ItemType" name="ItemType" method="post">
  <option value="0">Album</option>
  <option value="1">Movie</option>
  <option value="2">Game</option>
</select>
	</br>
	Name: <input type="text" name="name"></br>
	Genre: <input type="text" name="genre"></br>
	Stock: <input type="text" name="stock"></br>
	Release Year: <input type="text" name="releaseyear"></br>
	Price: <input type="text" name="price"></br>
	Visible: <select id="visible" name="visible" method="post">
	<option value="1">Yes</option>
	<option value="0">No</option>
	</select><br />
<input type="submit" name="submit" value="Submit">
</form>
</body>
</html>
<?php
	if(isset($_POST['submit']))
	{
		echo newEntry($_POST["name"],$_POST["genre"],(int)$_POST['stock'],(int)$_POST['releaseyear'],$_POST['visible'],$_POST['price'],(int)$_POST['ItemType']);
	}
?>