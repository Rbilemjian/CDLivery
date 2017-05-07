<?php
		require_once('C:/wamp64/www/cd/Includes/Functions.php'); 
		session_start();
		UserPrintout();
?>
<head>
	<title>ShippingInfo</title>
<body>

</body>
</html>
<?php
	if(isAdmin() || !loggedIn())
	{
		die("Must be logged in as user to access this page");
	}
	$_SESSION['confirmPage'] = false;
	$shippingInfo = shippingForm();
	$_SESSION['shippingInfo'] = $shippingInfo;
	if(isset($_SESSION['shippingInfo']) && !empty($_SESSION['shippingInfo']))
	{
		redirectTo("PaymentInfo.php");
	}
?>