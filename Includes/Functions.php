<?php
	function redirectTo($page)
	{
		header("Location: ".$page);
		exit;
	}
	
	function loggedIn()
	{
		if(isset($_SESSION['id']))
		{
			return true;
		}
		return false;
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
		$query = "INSERT INTO users(username,password,type)
				  VALUES ('{$username}','{$password}','user')";
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
		if($name=="" ||  $genre=="" || empty($stock) || empty($releaseyear) || $visible=="" || empty($price))
		{
			return "Error: All fields must be filled.";
		}
		if($visible == 'yes')
		{
			$visible = 1;
		}
		else if($visible == 'no')
		{
			$visible = 0;
		}
		else
		{
			return "Error: Only valid input for visible is yes/no";
		}
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
		echo '<p style="text-align:left;">';
		echo '<a href="MainPage.php"><b>Main Page</b><a>';
		if(isset($_GET['logout']) && $_GET['logout'] == "true")
		{
			$_SESSION['username'] = null;
			$_SESSION['id'] = null;
			$_SESSION['type'] = null;
		}
		if(isset($_SESSION['username']))
		{
			echo '<p style = "text-align:right; margin-top:-34px;">'."Logged in as: ".'<b>'.$_SESSION['username'].'</b>';
			echo '&nbsp;&nbsp;&nbsp;&nbsp';
			if($_SESSION['type'] == "user")
			{
				echo '<a href="Cart.php"><b>View Cart</b><a>';
				echo '&nbsp;&nbsp;&nbsp;&nbsp';
			}
			echo '<a href="?logout=true"><b>Log out</b></a>';
		}
		
		else
		{
			echo '<p style = "text-align:right; margin-top:-34px;"><a href="LoginPage.php">Log in</a>';
		}
		echo '</p>';
		echo '<br />';
		echo '<p align = "left">';
	}
	function printList($query,$type) //displays CDs of a certain database
	{
		if(isset($_GET['id']))
		{
			if(isset($_SESSION['id']))
			{
				echo '<p align="left">';
				addToCart($_SESSION['id'],$_GET['id']);
			}
			else
			{
				echo "Must be logged in to add item to cart.";
			}
		}
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
		echo "<tr><th>Title</th><th>Genre</th><th>Year of Release</th><th>Stock</th><th>Type</th><th>Price</th><th>Add to Cart</th></tr>"; 
		while($cd = mysqli_fetch_assoc($result))
		{
			$id=$cd["id"];
			echo "<tr><td>";
			echo '<div align="center">';
			echo $cd["name"];
			echo "</td><td>";
			echo '<div align="center">';
			echo $cd["genre"];
			echo "</td><td>";
			echo '<div align="center">';
			echo $cd["release_year"];
			echo "</td><td>";
			echo '<div align="center">';
			echo $cd["stock"];
			echo "</td><td>";
			echo '<div align="center">';
			echo $cd["type"];
			echo "</td><td>";
			echo '<div align="center">';
			echo '$'.$cd["price"];
			echo "</td><td>";
			echo '<div align="center">';
			?>
			<input type="button" value="Add to Cart" onClick="window.location='?id=<?php echo $id?>'">
			<?php
			echo "</td></tr>";
		}
		echo '</table>';
	}
	
	//Cart Functions
	
	function addToCart($userid,$itemid)
	{
		$connection = mysqli_connect("localhost","cd_user","password","cd_livery");
		if(mysqli_connect_errno())
		{
			die("Database connection failed: " . mysqli_connect_error() . " (" . mysqli_connect_errno() . ")");
		}
		$query = "select * from carts where id=$userid";
		$result = mysqli_query($connection,$query);
		if($result->num_rows==0)
		{
			$items =serialize(array($itemid));
			$quantities = serialize(array(1));
			$query  = "INSERT INTO carts (";
			$query .= "id,itemids,quantities";
			$query .= ") VALUES ( ";
			$query .= "{$userid},'{$items}','{$quantities}'";
			$query .= ")";
		}
		else
		{
			$cart=mysqli_fetch_assoc($result);
			$items=unserialize($cart['itemids']);
			for($i=0;$i<count($items);$i++)
			{
				if($items[$i] ==  $itemid)
				{
					echo "Selected item is already in the cart";
					return;
				}
			}
			array_push($items,$itemid);
			$quantities=unserialize($cart['quantities']);
			array_push($quantities,1);
			$items=serialize($items);
			$quantities=serialize($quantities);
			$query="UPDATE carts ";
			$query.="SET itemids='$items', quantities='$quantities' ";
			$query.="WHERE id=$cart[id]";
		}
		$result = mysqli_query($connection, $query);
		if ($result && mysqli_affected_rows($connection) == 1) 
		{
			echo "Item successfully added to cart.";
		} 
		else 
		{
			die("Database query failed. " . mysqli_error($connection));
		}
		mysqli_close($connection);
	}
	function displayCart($id)
	{
		if(isset($_POST['itemNum']) && isset($_POST['Quantity']))
		{
			changeQuantity($_POST['Quantity'],$_POST['itemNum']);
		}
		if(isset($_GET['remove']))
		{
			removeFromCart($_GET['remove'], $id);
		}
		$price = 0;
		$connection = mysqli_connect("localhost","cd_user","password","cd_livery");
		if(mysqli_connect_errno())
		{
			die("Database connection failed: " . mysqli_connect_error() . " (" . mysqli_connect_errno() . ")");
		}
		$query = "select * from carts where id=$id";
		$result = mysqli_query($connection,$query);
		if($result->num_rows==0)
		{
			echo 'Cart is empty.';
			die("");
		}
		$cart = mysqli_fetch_assoc($result);
		$items=unserialize($cart['itemids']);
		$quantities=unserialize($cart['quantities']);
		$size = count($items);
		echo '<table border="1">';
		echo "<tr><th>Title</th><th>Genre</th><th>Year of Release</th><th>Stock</th><th>Type</th><th>Price</th><th>Quantity</th><th>Remove?</th><th>Change Quantity</th></tr>"; 
		for($i=0;$i<$size;$i++)
		{
			$query="SELECT * FROM cds WHERE id={$items[$i]}";
			$result=mysqli_query($connection,$query);
			$cd=mysqli_fetch_assoc($result);
			if($cd['stock'] == 0 || $cd['visible'] == 0)
			{
				removeFromCart($i, $id);
				continue;
			}
			$itemid=$cd["id"];
			$price = $price + ($cd['price'] * $quantities[$i]);
			echo "<tr><td>";
			echo '<div align="center">';
			echo $cd["name"];
			echo "</td><td>";
			echo '<div align="center">';
			echo $cd["genre"];
			echo "</td><td>";
			echo '<div align="center">';
			echo $cd["release_year"];
			echo "</td><td>";
			echo '<div align="center">';
			echo $cd["stock"];
			echo "</td><td>";
			echo '<div align="center">';
			echo $cd["type"];
			echo "</td><td>";
			echo '<div align="center">';
			echo '$'.$cd["price"];
			echo "</td><td>";
			echo '<div align="center">';
			echo $quantities[$i];
			echo "</td><td>";
			echo '<div align="center">';
			?>
			<input type="button" value="Remove" onClick="window.location='?remove=<?php echo $i?>'">
			<?php
			echo "</td><td>";
			echo '<div align="center">';
			?>
			<form action="Cart.php" method=post>
			<select name = "Quantity">
			<option value=1>1</option>
			<option value=2>2</option>
			<option value=3>3</option>
			</select>
			<input type="submit" value="Apply" name="apply">
			<input type="hidden" name="itemNum" value=<?php echo $i?>>
			</form>
			<?php
			echo "</td></tr>";
		}
		echo '</table>';;
		echo "<br />";
		echo "Total Price: $". $price;
		$_SESSION['totalPrice'] = $price;
		?>
		<a href="ShippingInfo">Checkout</a>
		<?php
		
	}
	function removeFromCart($itemNum,$userid)
	{
		$connection = mysqli_connect("localhost","cd_user","password","cd_livery");
		if(mysqli_connect_errno())
		{
			die("Database connection failed: " . mysqli_connect_error() . " (" . mysqli_connect_errno() . ")");
		}
		$query = "select * from carts where id=$userid";
		$result = mysqli_query($connection,$query);
		if($result->num_rows == 0)
		{
			die("Cart is already empty. Somehow.");
		}
		$cart = mysqli_fetch_assoc($result);
		$quantities = unserialize($cart['quantities']);
		$items = unserialize($cart['itemids']);
		$index = $itemNum;
		unset($items[$index]);
		$items = array_values($items);
		unset($quantities[$index]);
		$quantities = array_values($quantities);
				
		if(count($items) == 0) //if no more items in cart, simply deletes cart row for user in database
		{
			$query = "DELETE FROM carts WHERE id=$userid";
			$result = mysqli_query($connection,$query);
		}
		else
		{
			$items = serialize($items);
			$quantities = serialize($quantities);
			$query="UPDATE carts ";
			$query.="SET itemids='$items', quantities='$quantities' ";
			$query.="WHERE id=$userid;";
			$result = mysqli_query($connection, $query);
		}
		if ($result && mysqli_affected_rows($connection) == 1) 
		{
		} 
		else 
		{
			die("Database query failed 355 . " . mysqli_error($connection));
		}
		mysqli_close($connection);
		return;
	}
	function changeQuantity($quantity,$itemnum)
	{
		$id = $_SESSION['id'];
		$connection = mysqli_connect("localhost","cd_user","password","cd_livery");
		if(mysqli_connect_errno())
		{
			die("Database connection failed: " . mysqli_connect_error() . " (" . mysqli_connect_errno() . ")");
		}
		$query = "select * from carts where id=$id";
		$result = mysqli_query($connection,$query);
		$cartitem = mysqli_fetch_assoc($result);
		//checking if any items' stocks are lower than requested amount in cart
		$badItems = checkStock($cartitem['itemids'],$cartitem['quantities']);
		if(count($badItems)>0)
		{
			reportStockDeficit($badItems);
			return;
		}
		$quantities = $cartitem['quantities'];
		$quantities = unserialize($quantities);
		$quantities[$itemnum] = $quantity;
		$quantities = serialize($quantities);
		$query="UPDATE carts ";
		$query.="SET quantities='$quantities'";
		$query.=" WHERE id=$id;";
		$result = mysqli_query($connection, $query);
		if ($result) 
		{
			if(mysqli_affected_rows($connection) == 1)
			{
				echo "Quantity successfully modified";
			}
			else
			{
				echo "No quantities were updated";
			}
		} 
		else 
		{
			die("Database query failed . " . mysqli_error($connection));
		}
		mysqli_close($connection);
		return;
		
	}
	
	//Administrator Functions
	
	function IsAdmin()
	{
		if(isset($_SESSION['type']) && $_SESSION['type'] == 'admin')
		{
			return true;
		}
		return false;
	}
	function ChangeStock($quantity,$id)
	{
		$connection = mysqli_connect("localhost","cd_user","password","cd_livery");
		if(mysqli_connect_errno())
		{
			die("Database connection failed: " . mysqli_connect_error() . " (" . mysqli_connect_errno() . ")");
		}
		$query = "update cds set stock=$quantity where id=$id";
		$result = mysqli_query($connection,$query);
		if(!$result)
		{
			echo "Error in modifying selected CD.";
		}
		else
		{
			echo "Successfully modified stock.";
		}
	}
	function AdminList($query,$type)
	{
		if(isset($_POST['modify']))
		{
			$_SESSION['itemID'] = $_POST['itemID'];
			redirectTo("ModifyItem");
		}
		if(isset($_GET['id']))
		{
			if(isset($_SESSION['id']))
			{
				echo '<p align="left">';
				RemoveItem($_GET['id']);
			}
		}
		$connection = mysqli_connect("localhost","cd_user","password","cd_livery");
		
		if(mysqli_connect_errno())
		{
			die("Database connection failed: " . mysqli_connect_error() . " (" . mysqli_connect_errno() . ")");
		}
		$result = mysqli_query($connection,$query);
		if($result->num_rows == 0)
		{
			echo "There are not currently any {$type}s available.";
			echo '<br />';
			return;
		}
		echo '<table border="1">';
		echo "<tr><th>Title</th><th>Genre</th><th>Year of Release</th><th>Stock</th><th>Type</th><th>Price</th><th>Change Stock</th><th>Remove Item</th></tr>"; 
		while($cd = mysqli_fetch_assoc($result))
		{
			$id=$cd["id"];
			echo "<tr><td>";
			echo '<div align="center">';
			echo $cd["name"];
			echo "</td><td>";
			echo '<div align="center">';
			echo $cd["genre"];
			echo "</td><td>";
			echo '<div align="center">';
			echo $cd["release_year"];
			echo "</td><td>";
			echo '<div align="center">';
			echo $cd["stock"];
			echo "</td><td>";
			echo '<div align="center">';
			echo $cd["type"];
			echo "</td><td>";
			echo '<div align="center">';
			echo '$'.$cd["price"];
			echo "</td><td>";
			echo '<div align="center">';
			?>
			<form method=post>
			<input type="submit" value="Modify" name="modify">
			<input type="hidden" name="itemID" value=<?php echo $id?>>
			</form>
			<?php
			echo "</td><td>";
			echo '<div align="center">';
			?>
			<input type="button" value="Remove Item" onClick="window.location='?id=<?php echo $id?>'">
			<?php
			echo "</td></tr>";
		}
		echo '</table>';
	}
	function ExecuteModifications($id)
	{
		$connection = mysqli_connect("localhost","cd_user","password","cd_livery");
		if(isset($_POST['ItemType']) && $_POST['ItemType']!=-1)
		{
			$type = $_POST['ItemType'];
			if($type == 0)
				$type = "Album";
			else if($type == 1)
				$type = "Movie";
			else if($type == 2)
				$type = "Game";
			$query = "update cds set type='$type' WHERE id=$id;";
			$result = mysqli_query($connection,$query);
			if(!$result)
			{
				die("Error: Item type could not be changed. <br/>");
			}
			echo "Successfully changed item type. <br/>";	
		}
		if(isset($_POST['name']) && $_POST['name']!="")
		{
			$name = $_POST['name'];
			$query = "update cds set name='$name' WHERE id=$id;";
			$result = mysqli_query($connection,$query);
			if(!$result)
			{
				die("Error: Name could not be changed. <br/ >");
			}
			echo "Successfully changed name. <br/ >";
		}
		if(isset($_POST['genre']) && $_POST['genre']!="")
		{
			$genre = $_POST['genre'];
			$query = "update cds set genre='$genre' WHERE id=$id;";
			$result = mysqli_query($connection,$query);
			if(!$result)
			{
				die("Error: Genre could not be change. <br/>");
			}
			echo "Successfully changed genre.<br/>";
		}
		if(isset($_POST['stock']) && $_POST['stock']!="")
		{
			$stock = $_POST['stock'];
			$query = "update cds set stock=$stock where id=$id";
			$result = mysqli_query($connection,$query);
			if(!$result)
			{
				die("Error: Stock could not be changed. <br/>");
			}
			echo "Successfully changed stock.<br/>";
		}
		if(isset($_POST['releaseyear']) && $_POST['releaseyear']!="")
		{
			$year = $_POST['releaseyear'];
			$query = "update cds set release_year=$year where id=$id";
			$result = mysqli_query($connection,$query);
			if(!$result)
			{
				die("Error: Release year could not be changed. <br/>");
			}
			echo "Successfully changed release year.<br/>";
		}
		if(isset($_POST['price']) && $_POST['price'] != "")
		{
			$price = $_POST['price'];
			$query = "update cds set price=$price where id=$id";
			$result = mysqli_query($connection,$query);
			if(!$result)
			{
				die("Error: Price could not be changed. <br/>");
			}
			echo "Successfully changed price.<br/>";
		}
	}
	function ModifyItem($id)
	{
		$connection = mysqli_connect("localhost","cd_user","password","cd_livery");
		ExecuteModifications($id);
		if(mysqli_connect_errno())
		{
			die("Database connection failed: " . mysqli_connect_error() . " (" . mysqli_connect_errno() . ")");
		}
		$query = "select * from cds where id=$id;";
		$result = mysqli_query($connection,$query);
		if($result->num_rows == 0)
		{
			echo "There are not currently any {$type}s available.";
			echo '<br />';
			return;
		}
		$cd = mysqli_fetch_assoc($result);
		echo '<table border="1">';
		echo "<tr><th>Title</th><th>Genre</th><th>Year of Release</th><th>Stock</th><th>Type</th><th>Price</th></tr>"; 
		echo "<tr><td>";
		echo '<div align="center">';
		echo $cd["name"];
		echo "</td><td>";
		echo '<div align="center">';
		echo $cd["genre"];
		echo "</td><td>";
		echo '<div align="center">';
		echo $cd["release_year"];
		echo "</td><td>";
		echo '<div align="center">';
		echo $cd["stock"];
		echo "</td><td>";
		echo '<div align="center">';
		echo $cd["type"];
		echo "</td><td>";
		echo '<div align="center">';
		echo '$'.$cd["price"];
		echo "</td><tr>";
		echo "</table>";
		echo "</br>";
		?>
		Make any modification needed
		<form action="ModifyItem.php" method="post">
		<select id="ItemType" name="ItemType" method="post">
		<option value="-1">Select one</option>
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
		<input type="submit" name="submit" value="Submit">
		</form>
		<?php
	}
	function RemoveItem($id)
	{
		$connection = mysqli_connect("localhost","cd_user","password","cd_livery");
		if(mysqli_connect_errno())
		{
			die("Database connection failed: " . mysqli_connect_error() . " (" . mysqli_connect_errno() . ")");
		}
		$query = "update cds set visible=0 where id=$id;";
		$result = mysqli_query($connection,$query);
		if(!$result)
		{
			die("Item does not exist. Somehow.");
		}
		else
		{
			echo "Item successfully removed. ";
			echo '</br>';
		}
	}
	function ListRemoved()
	{
		if(isset($_GET['id']))
		{
			RestoreItem($_GET['id']);
		}
		$connection = mysqli_connect("localhost","cd_user","password","cd_livery");
		if(mysqli_connect_errno())
		{
			die("Database connection failed: " . mysqli_connect_error() . " (" . mysqli_connect_errno() . ")");
		}
		$query = "select * from cds where visible=0;";
		$result = mysqli_query($connection,$query);
		if($result->num_rows == 0)
		{
			die("There are not currently any deleted items");
		}
		echo '<table border="1">';
		echo "<tr><th>Title</th><th>Genre</th><th>Year of Release</th><th>Stock</th><th>Type</th><th>Price</th><th>Remove Item</th></tr>"; 
		while($cd = mysqli_fetch_assoc($result))
		{
			$id=$cd["id"];
			echo "<tr><td>";
			echo '<div align="center">';
			echo $cd["name"];
			echo "</td><td>";
			echo '<div align="center">';
			echo $cd["genre"];
			echo "</td><td>";
			echo '<div align="center">';
			echo $cd["release_year"];
			echo "</td><td>";
			echo '<div align="center">';
			echo $cd["stock"];
			echo "</td><td>";
			echo '<div align="center">';
			echo $cd["type"];
			echo "</td><td>";
			echo '<div align="center">';
			echo '$'.$cd["price"];
			echo "</td><td>";
			echo '<div align="center">';
			?>
			<input type="button" value="Restore Item" onClick="window.location='?id=<?php echo $id?>'">
			<?php
			echo "</td></tr>";
		}
		echo '</table>';
	}
	function RestoreItem($id)
	{
		$connection = mysqli_connect("localhost","cd_user","password","cd_livery");
		if(mysqli_connect_errno())
		{
			die("Database connection failed: " . mysqli_connect_error() . " (" . mysqli_connect_errno() . ")");
		}
		$query = "update cds set visible=1 where id=$id;";
		$result = mysqli_query($connection,$query);
		if(!$result)
		{
			die("Selected item could not be found in database.");
		}
		else
		{
			echo "Item was successfully restored.";
		}
	}
	
	function listUsers($type)
	{
		if(isset($_POST['userID']))
		{
			changeAccountPrivileges($_POST['userID']);
			unset($_POST['userID']);
		}
		$connection = mysqli_connect("localhost","cd_user","password","cd_livery");
		if(mysqli_connect_errno())
		{
			die("Database connection failed: " . mysqli_connect_error() . " (" . mysqli_connect_errno() . ")");
		}
		$query = "select * from users where type='$type';";
		$result = mysqli_query($connection,$query);
		if(!$result)
		{
			die("Database query failed");
		}
		while($user = mysqli_fetch_assoc($result))
		{
			$id = $user['id'];
			echo "<tr><td>";
			echo '<div align="center">';
			echo $user["username"];
			echo "</td><td>";
			echo '<div align="center">';
			echo $user["type"];
			echo "</td><td>";
			echo '<div align="center">';
			if($user['type'] == "admin")
			{
				?>
				<form method="post">
				</br>
				<input type="hidden" name="userID" value=<?php echo $id?>>
				<input type="submit" value="Revoke Administrator Status">
				</form>
				<?php
			}
			else if($user['type'] == "user")
			{
				?>
				<form method="post">
				</br>
				<input type="hidden" name="userID" value=<?php echo $id?>>
				<input type="submit" value="Make Administrator">
				</form>
				<?php
			}
			echo "</td></tr>";
		}
	}
	function changeAccountPrivileges($id)
	{
		$connection = mysqli_connect("localhost","cd_user","password","cd_livery");
		if(mysqli_connect_errno())
		{
			die("Database connection failed: " . mysqli_connect_error() . " (" . mysqli_connect_errno() . ")");
		}
		$query = "select * from users where id=$id;";
		$result = mysqli_query($connection,$query);
		if(!$result)
		{
			die("Database query failed");
		}
		$user = mysqli_fetch_assoc($result);
		if($user['type'] == "admin")
		{
			$query = "update users set type='user' where id=$id;";
		}
		else if($user['type'] == "user")
		{
			$query = "update users set type='admin' where id=$id;";
		}
		$result = mysqli_query($connection,$query);
		if(!$result)
		{
			echo "Could not modify user privileges";
		}
	}
	
	function displayOrders($status)
	{
		if(isset($_POST['submit']))
		{
			flipStatus($_POST['orderID'], $_POST['status']);
		}
		if(isset($_POST['submit1']))
		{
			$_SESSION['orderID'] = $_POST['orderID'];
			redirectTo("ViewOrderItems");
		}
		$connection = mysqli_connect("localhost","cd_user","password","cd_livery");
		if(mysqli_connect_errno())
		{
			die("Database connection failed: " . mysqli_connect_error() . " (" . mysqli_connect_errno() . ")");
		}
		$query = "select * from orders where status='$status';";
		$result = mysqli_query($connection,$query);
		if(!$result)
		{
			die("Database query failed");
		}
		
		while($order = mysqli_fetch_assoc($result))
		{
			echo "<tr><td>";
			echo '<div align="center">';
			echo $order['shippingName'];
			echo "</td><td>";
			echo '<div align="center">';
			echo $order['addressLine1'];
			echo "</td><td>";
			echo '<div align="center">';
			if($order['addressLine2'] == "")
			{
				echo "N/A";
			}
			else
			{
				echo $order['addressLine2'];
			}
			echo "</td><td>";
			echo '<div align="center">';
			echo $order['city'];
			echo "</td><td>";
			echo '<div align="center">';
			echo $order['state'];
			echo "</td><td>";
			echo '<div align="center">';
			echo $order['zipCode'];
			echo "</td><td>";
			echo '<div align="center">';
			echo $order['paymentName'];
			echo "</td><td>";
			echo '<div align="center">';
			echo $order['cardType'];
			echo "</td><td>";
			echo '<div align="center">';
			echo $order['cardNumber'];
			echo "</td><td>";
			echo '<div align="center">';
			echo $order['CVC'];
			echo "</td><td>";
			echo '<div align="center">';
			echo $order['expDate'];
			echo "</td><td>";
			echo '<div align="center">';
			echo $order['status'];
			echo "</td><td>";
			echo '<div align="center">';
			if($order['promoCode'] == "")
			{
				echo "N/A";
			}
			else
			{
				echo $order['promoCode'];
			}
			echo "</td><td>";
			echo '<div align="center">';
			echo "$". $order['price'];
			echo "</td><td>";
			echo '<div align="center">';
			if($status=="unfulfilled")
			{
				?>
				<form method="post">
					</br>
					<input type="hidden" name="orderID" value=<?php echo $order['id']?>>
					<input type="hidden" name="status" value="unfulfilled">
					<input type="submit" value="Mark Fulfilled" name="submit">
					</form>
				<?php
			}
			else if($status=="fulfilled")
			{
				?>
				<form method="post">
					</br>
					<input type="hidden" name="orderID" value=<?php echo $order['id']?>>
					<input type="hidden" name="status" value="fulfilled">
					<input type="submit" value="Mark Unfulfilled" name="submit">
					</form>
				<?php
			}
			echo "<br/>";
			echo "</td><td>";
			echo '<div align="center">';
			?>
			<form method="post">
					</br>
					<input type="hidden" name="orderID" value=<?php echo $order['id']?>>
					<input type="submit" value="View Items" name="submit1">
					</form>
			<?php
			echo "<br/>";
			
		}
	}
	
	function displayOrderItems($id)
	{
		$connection = mysqli_connect("localhost","cd_user","password","cd_livery");
		if(mysqli_connect_errno())
		{
			die("Database connection failed: " . mysqli_connect_error() . " (" . mysqli_connect_errno() . ")");
		}
		$query = "select * from orders where id=$id";
		$result = mysqli_query($connection,$query);	
		if(!$result)
		{
			die("Database query to display order items failed");
		}
		$order = mysqli_fetch_assoc($result);
		$itemids = $order['items'];
		$quantities = $order['quantities'];
		$itemids = unserialize($itemids);
		$quantities = unserialize($quantities);
		echo '<table border="1">';
		echo "<tr><th>Title</th><th>Genre</th><th>Year of Release</th><th>Quantity</th><th>Type</th><th>Price</th></tr>"; 
		for($i = 0;$i<count($itemids);$i++)
		{
			$id=$itemids[$i];
			$query = "select * from cds where id=$id;";
			$result = mysqli_query($connection, $query);
			if(!$result)
			{
				die("Database query to display item with id $id failed");
			}
			$cd = mysqli_fetch_assoc($result);
			echo "<tr><td>";
			echo '<div align="center">';
			echo $cd['name'];
			echo "</td><td>";
			echo '<div align="center">';
			echo $cd["genre"];
			echo "</td><td>";
			echo '<div align="center">';
			echo $cd["release_year"];
			echo "</td><td>";
			echo '<div align="center">';
			echo $quantities[$i];
			echo "</td><td>";
			echo '<div align="center">';
			echo $cd["type"];
			echo "</td><td>";
			echo '<div align="center">';
			echo '$'.$cd["price"];
			echo "</td></tr>";
		}
		echo '</table>';
		
	}
	
	function flipStatus($id, $status)
	{
		$connection = mysqli_connect("localhost","cd_user","password","cd_livery");
		if(mysqli_connect_errno())
		{
			die("Database connection failed: " . mysqli_connect_error() . " (" . mysqli_connect_errno() . ")");
		}
		if($status == "fulfilled")
		{
			$query = "update orders set status='unfulfilled' where id=$id;";
		}
		else if($status == "unfulfilled")
		{
			$query = "update orders set status='fulfilled' where id=$id;";
		}
		$result = mysqli_query($connection,$query);
		if(!$result)
		{
			die("Database query failed 927");
		}
		
	}
	
	//checkout functions
	
	function paymentForm()
	{
		if(isset($_POST['submit']))
		{
			if($_POST['ItemType'] == ""||$_POST['name']==""||$_POST['CCnum']==""
			||$_POST['CVCnum']==""||$_POST['expDate']=="")
			{
				echo "Error: All fields must be filled";
			}
			else
			{
				return array($_POST['ItemType'], $_POST['name'], $_POST['CCnum'], $_POST['CVCnum'], $_POST['expDate']);
			}
		}
		?>
		<form method="post">
		Select Credit Card Type
		<select id="ItemType" name="ItemType" method="post">
		<option value="American Express">American Express</option>
		<option value="Diners Club Carte Blanche">Diners Club Carte Blanche</option>
		<option value="Discover">Discover</option>
		<option value="Diners Club Enroute">Diners Club Enroute</option>
		<option value="JCB">JCB</option>
		<option value="Maestro">Maestro</option>
		<option value="MasterCard">MasterCard</option>
		<option value="Solo">Solo</option>
		<option value="Switch">Switch</option>
		<option value="VISA">VISA</option>
		<option value="VISA Electron">VISA Electron</option>
		<option value="LaserCard">LaserCard</option>
		</select>
		</br>
		Name as it appears on the card: <input type="text" name="name"></br>
		Credit Card Number: <input type="text" name="CCnum"></br>
		CVC Number: <input type="text" name="CVCnum"></br>
		Expiration Date: <input type="text" name="expDate"></br>
		<input type="submit" name="submit" value="Next">
		</form>
		<?php
	}
	
	function shippingForm()
	{
		//check if there is sufficient stock for items selected here
		if(isset($_POST['submit']))
		{
			if($_POST['name']==""||$_POST['adLine1']==""||$_POST['city']==""||
			$_POST['state']==""||$_POST['zipCode']=="")
			{
				echo "Error: All fields must be filled";
			}
			else
			{
				return array($_POST['name'],$_POST['adLine1'],$_POST['adLine2'],$_POST['city'],$_POST['state'],$_POST['zipCode']);
			}
		}
		?>
		<form action="ShippingInfo.php" method="post">
		<b>Enter an Address Within the United States</b>
		</br>
		Full Name: <input type="text" name="name"></br>
		Address Line 1: <input type="text" name="adLine1"></br>
		Address Line 2: <input type="text" name="adLine2"></br>
		City: <input type="text" name="city"></br>
		State: <input type="text" name="state"></br>
		ZIP/Postal Code: <input type="text" name="zipCode"></br>
		<input type="submit" name="submit" value="Next">
		</form>
		<?php
	}
	
	function PromoForm()
	{
		$codes = array("15OFF","WANG","HAM","RAFFISPLACE","ALEKINS","EDDIE");
		if(isset($_POST['submit']))
		{
			if($_POST['promoCode'] == "")
			{
				echo "Error: Must enter promo code in order to redeem promo code.";
			}
			else
			{
				if(in_array($_POST['promoCode'], $codes))
				{
					return $_POST['promoCode'];
				}
				else
				{
					echo "Error: Invalid promo code.";
				}
			}
		}
		?>
		<form action="PromoCodePage.php" method="post">
		<b>Enter a Promo Code if you have one</b>
		</br>
		Promo Code: <input type="text" name="promoCode"></br>
		<input type="submit" name="submit" value="Submit">
		</form>
		<a href="ConfirmationPage">Skip</a>
		<?php
	}
	
	function placeOrder($shippingInfo, $paymentInfo, $price)
	{
		$connection = mysqli_connect("localhost","cd_user","password","cd_livery");
		if(mysqli_connect_errno())
		{
			die("Database connection failed: " . mysqli_connect_error() . " (" . mysqli_connect_errno() . ")");
		}
		$query = "select * from carts where id={$_SESSION['id']};";
		$result = mysqli_query($connection,$query);
		if(!$result)
		{
			die("Database query failed");
		}
		$cart = mysqli_fetch_assoc($result);
		$quantities = $cart['quantities'];
		$items = $cart['itemids'];
		//check if there is enough stock and modify stock count here
		$badItems = checkStock($items,$quantities);
		if(count($badItems)>0)
		{
			reportStockDeficit($badItems);
			die();
		}
		$query = "insert into orders (userid, shippingName, addressLine1, addressLine2, city, state, zipcode, paymentName, cardType, cardNumber, CVC, expDate, items, quantities,price,promoCode) ";
		$query.= "values({$_SESSION['id']}, '{$shippingInfo[0]}', '{$shippingInfo[1]}', '{$shippingInfo[2]}', '{$shippingInfo[3]}', '{$shippingInfo[4]}', {$shippingInfo[5]}, ";
		$query.= "'{$paymentInfo[1]}', '{$paymentInfo[0]}', '{$paymentInfo[2]}', {$paymentInfo[3]}, '{$paymentInfo[4]}', '{$items}','{$quantities}',$price, '{$_SESSION['promoCode']}');";
		$result = mysqli_query($connection,$query);
		if(!$result)
		{
			die("Database query failed");
		}
		modifyStocks($items,$quantities);
		redirectTo("CheckoutSuccess");
	}
	
	
	function checkStock($itemids, $quantities)
	{
		$itemids =  unserialize($itemids);
		$quantities = unserialize($quantities);
		$badItems = array();
		$connection = mysqli_connect("localhost","cd_user","password","cd_livery");
		if(mysqli_connect_errno())
		{
			die("Database connection failed: " . mysqli_connect_error() . " (" . mysqli_connect_errno() . ")");
		}
		for($i = 0;$i<count($quantities);$i++)
		{
			$query = "select * from cds where id={$itemids[$i]};";
			$result = mysqli_query($connection,$query);
			if(!$result)
			{
				die("Database query failed");
			}
			$currItem = mysqli_fetch_assoc($result);
			if($currItem['stock']<$quantities[$i])
			{
				array_push($badItems, $itemids[$i]);
			}
		}
		return $badItems;
	}
	
	function modifyStocks($items,$quantities)
	{
		$connection = mysqli_connect("localhost","cd_user","password","cd_livery");
		if(mysqli_connect_errno())
		{
			die("Database connection failed: " . mysqli_connect_error() . " (" . mysqli_connect_errno() . ")");
		}
		$itemids = unserialize($items);
		$quantitites = unserialize($quantities);
		$stock = 0;
		for($i = 0;$i<count($quantities);$i++)
		{
			$currItem = fetchItem($itemids[$i]);
			$stock = currItem['stock'];
			$stock = $stock-$quantities[$i];
			$query = "update cds set stock=$stock where id={$itemids[$i]};";
			$result = mysqli_query($connection, $query);
			if(!$result)
			{
				die("Database query failed");
			}
		}
	}
	
	function reportStockDeficit($badItems)
	{
		$connection = mysqli_connect("localhost","cd_user","password","cd_livery");
		if(mysqli_connect_errno())
		{
			die("Database connection failed: " . mysqli_connect_error() . " (" . mysqli_connect_errno() . ")");
		}
		echo "Sorry, the following items are not in stock in the quantity desired:";
			echo "<br/>";
			for($i = 0;$i<count($badItems);$i++)
			{
				$query = "select * from cds where id={$badItems[$i]};";
				$result = mysqli_query($connection,$query);
				if(!$result)
				{
					die("Database query failed");
				}
				$cd = mysqli_fetch_assoc($result);
				echo $cd['name']. "<br/>";
			}
	}
	
	function fetchItem($id)
	{
		$connection = mysqli_connect("localhost","cd_user","password","cd_livery");
		if(mysqli_connect_errno())
		{
			die("Database connection failed: " . mysqli_connect_error() . " (" . mysqli_connect_errno() . ")");
		}
		$query = "select * from cds where id=$id;";
		$result = mysqli_query($connection,$query);
		$item = mysqli_fetch_assoc($result);
		return $item;
	}
	
	function clearCart($id)
	{
		$connection = mysqli_connect("localhost","cd_user","password","cd_livery");
		if(mysqli_connect_errno())
		{
			die("Database connection failed: " . mysqli_connect_error() . " (" . mysqli_connect_errno() . ")");
		}
		$query = "delete from carts where id=$id;";
		$result = mysqli_query($connection,$query);
		if(!$result)
		{
			die("Database query failed");
		}
	}
	
	function checkTimeout()
	{
		if(time() - $_SESSION['last_time']>300)
		{
			$_SESSION['username'] = NULL;
			$_SESSION['id'] = NULL;
			$_SESSION['type'] = NULL;
			$_SESSION['timedOut'] = true;
			redirectTo('LoginPage');
		}
		else
		{
			$_SESSION['last_time'] = time();
		}
	}
	
	
	
function checkCreditCard ($cardnumber, $cardname) 
{
	$errornumber;
	$errortext;
  // Define the cards we support. You may add additional card types.
  
  //  Name:      As in the selection box of the form - must be same as user's
  //  Length:    List of possible valid lengths of the card number for the card
  //  prefixes:  List of possible prefixes for the card
  //  checkdigit Boolean to say whether there is a check digit
  
  // Don't forget - all but the last array definition needs a comma separator!
  
  $cards = array (  array ('name' => 'American Express', 
                          'length' => '15', 
                          'prefixes' => '34,37',
                          'checkdigit' => true
                         ),
                   array ('name' => 'Diners Club Carte Blanche', 
                          'length' => '14', 
                          'prefixes' => '300,301,302,303,304,305',
                          'checkdigit' => true
                         ),
                   array ('name' => 'Diners Club', 
                          'length' => '14,16',
                          'prefixes' => '36,38,54,55',
                          'checkdigit' => true
                         ),
                   array ('name' => 'Discover', 
                          'length' => '16', 
                          'prefixes' => '6011,622,64,65',
                          'checkdigit' => true
                         ),
                   array ('name' => 'Diners Club Enroute', 
                          'length' => '15', 
                          'prefixes' => '2014,2149',
                          'checkdigit' => true
                         ),
                   array ('name' => 'JCB', 
                          'length' => '16', 
                          'prefixes' => '35',
                          'checkdigit' => true
                         ),
                   array ('name' => 'Maestro', 
                          'length' => '12,13,14,15,16,18,19', 
                          'prefixes' => '5018,5020,5038,6304,6759,6761,6762,6763',
                          'checkdigit' => true
                         ),
                   array ('name' => 'MasterCard', 
                          'length' => '16', 
                          'prefixes' => '51,52,53,54,55',
                          'checkdigit' => true
                         ),
                   array ('name' => 'Solo', 
                          'length' => '16,18,19', 
                          'prefixes' => '6334,6767',
                          'checkdigit' => true
                         ),
                   array ('name' => 'Switch', 
                          'length' => '16,18,19', 
                          'prefixes' => '4903,4905,4911,4936,564182,633110,6333,6759',
                          'checkdigit' => true
                         ),
                   array ('name' => 'VISA', 
                          'length' => '16', 
                          'prefixes' => '4',
                          'checkdigit' => true
                         ),
                   array ('name' => 'VISA Electron', 
                          'length' => '16', 
                          'prefixes' => '417500,4917,4913,4508,4844',
                          'checkdigit' => true
                         ),
                   array ('name' => 'LaserCard', 
                          'length' => '16,17,18,19', 
                          'prefixes' => '6304,6706,6771,6709',
                          'checkdigit' => true
                         )
                );

  $ccErrorNo = 0;

  $ccErrors [0] = "Unknown card type";
  $ccErrors [1] = "No card number provided";
  $ccErrors [2] = "Credit card number has invalid format";
  $ccErrors [3] = "Credit card number is invalid";
  $ccErrors [4] = "Credit card number is wrong length";
               
  // Establish card type
  $cardType = -1;
  for ($i=0; $i<sizeof($cards); $i++) {

    // See if it is this card (ignoring the case of the string)
    if (strtolower($cardname) == strtolower($cards[$i]['name'])) {
      $cardType = $i;
      break;
    }
  }
  
  // If card type not found, report an error
  if ($cardType == -1) {
     $errornumber = 0;     
     $errortext = $ccErrors [$errornumber];
     return false; 
  }
   
  // Ensure that the user has provided a credit card number
  if (strlen($cardnumber) == 0)  {
     $errornumber = 1;     
     $errortext = $ccErrors [$errornumber];
     return false; 
  }
  
  // Remove any spaces from the credit card number
  $cardNo = str_replace (' ', '', $cardnumber);  
   
  // Check that the number is numeric and of the right sort of length.
  if (!preg_match("/^[0-9]{13,19}$/",$cardNo))  {
     $errornumber = 2;     
     $errortext = $ccErrors [$errornumber];
     return false; 
  }
       
  // Now check the modulus 10 check digit - if required
  if ($cards[$cardType]['checkdigit']) {
    $checksum = 0;                                  // running checksum total
    $mychar = "";                                   // next char to process
    $j = 1;                                         // takes value of 1 or 2
  
    // Process each digit one by one starting at the right
    for ($i = strlen($cardNo) - 1; $i >= 0; $i--) {
    
      // Extract the next digit and multiply by 1 or 2 on alternative digits.      
      $calc = $cardNo{$i} * $j;
    
      // If the result is in two digits add 1 to the checksum total
      if ($calc > 9) {
        $checksum = $checksum + 1;
        $calc = $calc - 10;
      }
    
      // Add the units element to the checksum total
      $checksum = $checksum + $calc;
    
      // Switch the value of j
      if ($j ==1) {$j = 2;} else {$j = 1;};
    } 
  
    // All done - if checksum is divisible by 10, it is a valid modulus 10.
    // If not, report an error.
    if ($checksum % 10 != 0) {
     $errornumber = 3;     
     $errortext = $ccErrors [$errornumber];
     return false; 
    }
  }  

  // The following are the card-specific checks we undertake.

  // Load an array with the valid prefixes for this card
  $prefix = explode(',',$cards[$cardType]['prefixes']);
      
  // Now see if any of them match what we have in the card number  
  $PrefixValid = false; 
  for ($i=0; $i<sizeof($prefix); $i++) {
    $exp = '/^' . $prefix[$i] . '/';
    if (preg_match($exp,$cardNo)) {
      $PrefixValid = true;
      break;
    }
  }
      
  // If it isn't a valid prefix there's no point at looking at the length
  if (!$PrefixValid) {
     $errornumber = 3;     
     $errortext = $ccErrors [$errornumber];
     return false; 
  }
    
  // See if the length is valid for this card
  $LengthValid = false;
  $lengths = explode(',',$cards[$cardType]['length']);
  for ($j=0; $j<sizeof($lengths); $j++) {
    if (strlen($cardNo) == $lengths[$j]) {
      $LengthValid = true;
      break;
    }
  }
  
  // See if all is OK by seeing if the length was valid. 
  if (!$LengthValid) {
     $errornumber = 4;     
     $errortext = $ccErrors [$errornumber];
     return false; 
  };   
  
  // The credit card is in the required format.
  return true;
}
	
	
	
	
	
	
	
	
	
	
	
	
?>