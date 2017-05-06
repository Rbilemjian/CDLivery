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
	function printList($query,$type)
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
		return;
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
		echo '<table border="1">';
		echo "<tr><th>Title</th><th>Genre</th><th>Year of Release</th><th>Stock</th><th>Type</th><th>Price</th><th>Quantity</th><th>Remove?</th><th>Change Quantity</th></tr>"; 
		for($i=0;$i<count($items);$i++)
		{
			$query="SELECT * FROM cds WHERE id={$items[$i]}";
			$result=mysqli_query($connection,$query);
			$cd=mysqli_fetch_assoc($result);
			$itemid=$cd["id"];
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
			echo "Item successfully removed from cart. ";
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
			echo $query;
		}
		else if($user['type'] == "user")
		{
			$query = "update users set type='admin' where id=$id;";
			echo $query;
		}
		$result = mysqli_query($connection,$query);
		if(!$result)
		{
			echo "Could not modify user privileges";
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
?>