<?php require_once('C:/wamp64/www/cd/Includes/Functions.php'); ?>

<html lang="en">
	<head>
		<title>PromoCodes</title>
	</head>
	<body>
	<?php
		session_start();
		UserPrintout();
		$_SESSION['promoCode'] = PromoForm();
		if(isset($_SESSION['promoCode']))
		{
			redirectTo("confirmationPage");
		}
	?>
	</body>
</html>
