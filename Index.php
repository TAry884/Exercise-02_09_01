<?php
//if there is certain cookies set then set cookie to true
$cookie = false;
if (isset($_COOKIE['first']) && isset($_COOKIE['last']) && isset($_COOKIE['email']) && isset($_COOKIE['contact'])) {
    $cookie = true;
}
//if the reset button is pushed then reset the cookies and the form
if (isset($_POST['reset'])) {
    setcookie("first", "");
    setcookie("last", "");
    setcookie("email", "");
    setcookie("contact", "");
}
?>
<!doctype html>

<html>
	<head>
		<title>Index</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="initial-scale=1.0">
		<script src="modernizr.custom.65897.js"></script>
	</head>

	<body>
    <h1>Register Login</h1>
    <h2>Contact information</h2>
    <!-- if there are specific cookies then display them in the input fields -->
    <form action="RegisterCompany.php" method="post">
        <p>Enter Your Name: First
           <input type="text" name="first" value="<?php if ($cookie) { echo $_COOKIE['first']; } ?>"/>
           Last
           <input type="text" name="last" value="<?php if ($cookie) { echo $_COOKIE['last']; } ?>" />
        </p>
        <p>
            Enter your contact info:
            <input type="text" name="contact" value="<?php if ($cookie) { echo $_COOKIE['contact']; } ?>"/>
        </p>
        <p>
            Enter your e-mail address:
            <input type="text" name="email" value="<?php if ($cookie) { echo $_COOKIE['email']; } ?>"/>
        </p>
        <input type="submit" name="submit" value="Register"/>
    </form>
    <form action="Index.php" method="post">
        <input type="submit" name="reset" value="Reset Registration Form"/>
    </form>
	</body>
</html>