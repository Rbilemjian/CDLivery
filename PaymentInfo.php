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
	if(!loggedIn() || isAdmin())
	{
		die("Must be logged in as a user in order to view this page");
	}
	checkTimeout();
	$paymentInfo = paymentForm();
	if(isset($paymentInfo[0]))
	{
		if(checkCreditCard($paymentInfo[2], $paymentInfo[0]))
		{
			echo "Valid credit card was inputted";
			$_SESSION['paymentInfo'] = $paymentInfo;
			redirectTo("promoCodePage");
		}	
		else
		{
			echo "Error: Invalid credit card was inputted";
		}
	}
	?>
	</body>
</html>
