<?php require_once('C:/wamp64/www/cd/Includes/Functions.php'); ?>

<html lang="en">
	<head>
		<title>PaymentInfo</title>
	</head>
	<body>
	<?php
	session_start();
	UserPrintout();
	echo '<div align="center">';
	echo '<b><u>Order Information</b></u></br>';
	if(!loggedIn() || isAdmin())
	{
		die("Must be logged in as a user in order to view this page");
	}
	checkTimeout();
	if(isset($_POST['placeOrder']))
	{
		placeOrder($_SESSION['shippingInfo'],$_SESSION['paymentInfo'], $_SESSION['finalPrice']);
	}
	if(isset($_SESSION['confirmPage']) && $_SESSION['confirmPage'] == true)
	{
		goto finalPrice;
	}
	if(!isset($_SESSION['paymentInfo']) || !isset($_SESSION['shippingInfo']))
	{
		die("Error: Must go through checkout process to access this page.");
	}
	if(isset($_SESSION['promoCode']))
	{
		$_SESSION['promoPrice'] = sprintf('%0.2f',$_SESSION['totalPrice'] * .85);
	}
	finalPrice:
	if(isset($_SESSION['promoCode']))
	{
		echo "<b>Price before Promo Code: </b>". $_SESSION['totalPrice'];
		echo "<br/>";
		echo "<b>Price after Promo Code: </b>". $_SESSION['promoPrice'];
		echo "<br/>";
		$_SESSION['finalPrice'] = $_SESSION['promoPrice']*1.09;
		$_SESSION['finalPrice'] = sprintf('%0.2f',$_SESSION['finalPrice']);
		echo "<b>Price after tax: </b>". $_SESSION['finalPrice'];
	}
	else
	{
		echo "<b>Price before tax: </b>". $_SESSION['totalPrice'];
		echo "<br/>";
		$_SESSION['finalPrice'] = sprintf('%0.2f',$_SESSION['totalPrice'] * 1.09);
		echo "<b>Price after tax: </b>". $_SESSION['finalPrice'];
	}
	echo "<br/>";
	echo "<b><u>Shipping Information </b></u>";
	echo "<br/>";
	$shippingInfo = $_SESSION['shippingInfo'];
	echo "<b>Name:</b> ". $shippingInfo[0]. '<br/>'. "<b>Address: </b>". $shippingInfo[1]. ", ". $shippingInfo[2]. $shippingInfo[3]. " ". $shippingInfo[4]. " ". $shippingInfo[5];
	echo "<br/>";
	echo '<b><u>'."Payment Information ".'</b></u>';
	echo "<br/>";
	$paymentInfo = $_SESSION['paymentInfo'];
	echo "<b>Card Type </b>". $paymentInfo[0];
	echo "<br/><b>Name: </b>". $paymentInfo[1];
	echo "<br/><b>Last 4 digits of CC#:</b>". substr($paymentInfo[2], 12, 16);
	$_SESSION['confirmPage'] = true;
	?>
	<form method=post>
	<input type="submit" value="Place Order" name="placeOrder">
	</form>
	</body>
</html>
