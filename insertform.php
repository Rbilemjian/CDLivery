<?php
		require_once('C:/wamp64/www/cd/Includes/Functions.php'); 
		session_start();
		UserPrintout();
?>
<body>
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
	Visible: <input type="text" name="visible"></br>
	Price: <input type="text" name="price"></br>
<input type="submit" name="submit" value="Submit">
</form>
</body>
</html>
<?php  
	if(isset($_POST['name']))
	{
		echo newEntry($_POST["name"],$_POST["genre"],(int)$_POST['stock'],(int)$_POST['releaseyear'],(int)$_POST['visible'],$_POST['price'],(int)$_POST['ItemType']);
	}
?>