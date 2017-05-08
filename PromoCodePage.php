<?php require_once('C:/wamp64/www/cd/Includes/Functions.php'); ?>

<html lang="en">
	<head>
		<title>PromoCodePage</title>
	</head>
	<body>
	<?php
		session_start();
		UserPrintout();
		if(!loggedIn() || isAdmin())
		{
			die("Must be logged in as a user in order to access this page");
		}
		checkTimeout();
		$_SESSION['promoCode'] = PromoForm();
		if(isset($_SESSION['promoCode']))
		{
			redirectTo("confirmationPage");
		}
	?>
	</body>
</html>
