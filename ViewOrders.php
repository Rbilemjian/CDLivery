<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">

<html lang="en">
	<head>
		<title>VideogamesPage</title>
	</head>
	<body>
	<?php
		require_once('C:/wamp64/www/cd/Includes/Functions.php');
		session_start();
		UserPrintout();
		echo '<div align="center">';
		if(!isAdmin())
		{
			echo "Must be logged in as an administrator in order to view this page.";
			die();
		}
		checkTimeout();
		?>
		<div align="center">
		<b><u>View Orders</b></u></br></br>
		<form method=post>
			<select name = "Dropdown">
			<option value=0>View Both</option>
			<option value=1>View Fulfilled</option>
			<option value=2>View Unfulfilled</option>
			</select>
			<input type="submit" value="Apply" name="apply">
			</form>
		<?php
		echo '<table border="1">';
		echo "<tr><th>Shipping Name</th><th>Address Line 1</th><th>Address Line 2</th><th>City</th><th>State</th><th>Zip Code</th><th>Payment Name</th><th>Card Type</th><th>Card Number</th><th>CVC</th><th>Expiration Date</th><th>Status</th><th>Promo Code</th><th>Price</th><th>Edit Status</th><th>View Items</th></tr>"; 
		if(isset($_POST['apply']))
		{
			if($_POST['Dropdown'] == 1)
			{
				displayOrders("fulfilled");
			}
			else if($_POST['Dropdown'] == 2)
			{
				displayOrders("unfulfilled");
			}
			else if($_POST['Dropdown'] == 0)
			{
				displayOrders("unfulfilled");
				displayOrders("fulfilled");
			}
		}
		else
		{
			displayOrders("unfulfilled");
			displayOrders("fulfilled");
		}
		echo '</td></tr>';
		echo '</table>';
		?>
	</body>
</html>
