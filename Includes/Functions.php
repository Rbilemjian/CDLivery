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
			echo '<a href="Cart.php"><b>View Cart</b><a>';
			echo '&nbsp;&nbsp;&nbsp;&nbsp';
			echo '<a href="?logout=true"><b>Log out</b></a>';
			echo "<br />";
		}
		
		else
		{
			echo '<p align = "right"><a href="LoginPage.php">Log in</a>';
			echo "<br />";
		}
		echo '<p align = "left">';
	}
	function printList($query,$type)
	{
		if(isset($_GET['id']))
		{
			echo '<p align="left">';
			addToCart($_SESSION['id'],$_GET['id']);
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
		if(isset($_POST['Quantity']) && isset($_SESSION['tempid']) && isset($_SESSION['itemnum']))
		{
			changeQuantity($_POST['Quantity'], $_SESSION['tempid'],$_SESSION['itemnum']);
			unset($_SESSION['tempid']);
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
			echo $quantities[$i];
			echo "</td><td>";
			echo '<div align="center">';
			?>
			<input type="button" value="Remove" onClick="window.location='?remove=<?php echo $id?>'">
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
			<?php
			$_SESSION['tempid'] = $id;
			$_SESSION['itemnum'] = $i;
			?>
			<input type="submit" value="Apply" name="apply">
			</form>
			<?php
			echo "</td></tr>";
		}
		echo '</table>';;
		echo "<br />";
	}
	function removeFromCart($itemid,$userid)
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
		for($i = 0;$i<count($items);$i++)
		{
			if($items[$i] == $itemid) //If we've found the one we want to delete, unsets the item & quantity array keys and reindexes the arrays
			{	
				unset($items[$i]);
				$items = array_values($items);
				unset($quantities[$i]);
				$quantities = array_values($quantities);
				break;
			}
		}
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
			$query.="WHERE id=$userid";
			$result = mysqli_query($connection, $query);
		}
		if ($result && mysqli_affected_rows($connection) == 1) 
		{
			echo "Item successfully removed from cart.";
		} 
		else 
		{
			die("Database query failed 326 . " . mysqli_error($connection));
		}
		mysqli_close($connection);
		return;
	}
	function changeQuantity($quantity,$itemid,$itemnum)
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
		$query.="WHERE id=$id";
		$result = mysqli_query($connection, $query);
		if ($result && mysqli_affected_rows($connection) == 1) 
		{
			echo "Quantity successfully modified.";
		} 
		else 
		{
			die("Database query failed . " . mysqli_error($connection));
		}
		mysqli_close($connection);
		return;
		
	}
	
?>