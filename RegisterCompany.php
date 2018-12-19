<?php
//global variables
$body = "";
$errors = 0;
$email = "";
$cookie = false;
//if specific cookies are set then set $cookie to true
if (isset($_COOKIE['compName']) && isset($_COOKIE['compEmail'])) {
    $cookie = true;
}
//if the reset button is pressed then reset the form and delete the cookies
if (isset($_POST['resetComp'])) {
    setcookie("compn", "");
    setcookie("compe", "");
}
//if the email is empty then error out else stripslashes and if it doesnt match the regex then error out
if (empty($_POST['email'])) {
    ++$errors;
    $body .= "<p>You need to enter an e-mail address";
} else {
    $email = stripslashes($_POST['email']);
    if (preg_match("/^[\w-]+(\.[\w-])*@[\w-]+(\.[\w-]+)*(\.[A-Za-z]{2,})$/i", $email) == 0) {
        ++$errors;
        $body .= "<p>You need to enter a valid e-mail address.</p>\n";
        $email = "";
    }
}
//if the fields are empty error out
if (empty($_POST['first'])) {
    ++$errors;
    $body .= "<p>You need to enter your first name.</p>\n";
} else {
    $first = stripslashes($_POST['first']);
}
if (empty($_POST['last'])) {
    ++$errors;
    $body .= "<p>You need to enter your last name.</p>\n";
} else {
    $last = stripslashes($_POST['last']);
}
if (empty($_POST['contact'])) {
    ++$errors;
    $body .= "<p>You need to enter your contact information.</p>\n";
} else {
    $contact = stripslashes($_POST['contact']);
}
//database global variables
$hostname = "localhost";
$username = "adminer";
$passwd = "dress-front-35";
$DBConnect = false;
$DBName = "conference";
//if no errors then connect to the database
if ($errors == 0) {
    $DBConnect = mysqli_connect($hostname, $username, $passwd);
    //if it doesnt connect then error out else select the database
    if (!$DBConnect) {
        ++$errors;
        $body .= "<p>Unable to connect to the database server" . 
            " error code: " . mysqli_connect_error() . 
            "</p>\n";
    } else {
        $result = mysqli_select_db($DBConnect, $DBName);
        //if the result variable doesnt set then error out
        if (!$result) {
            $body .= "<p>Unable to select the database" . 
                "\"$DBName\" error code: " . 
                mysqli_error($DBConnect) . "</p>\n";        
        }
    }
}
//if no errors then select everything in the email field and create a queryresult variable with the query from the database
$TableName = "attendee";
if ($errors == 0) {
    $SQLstring = "SELECT count(*) FROM $TableName" . 
        " WHERE email='$email'";
    $queryResult = mysqli_query($DBConnect, $SQLstring);
    //if query result works then fetch a row from the database
    if ($queryResult) {
        $row = mysqli_fetch_row($queryResult);
        //if the first row is greater than 0 then error out
        if ($row[0] > 0) {
            ++$errors;
            $body .= "<p>The e-mail address entered (" . 
                htmlentities($email) . ") is already entered.</p>\n";
        }
    }
}
//if no errors then stripslashes
if ($errors == 0) {
    $first = stripslashes($_POST['first']);
    $last = stripslashes($_POST['last']);
    $attendeeName = $first . " " . $last;
    $body .= "<p>Thank you, $attendeeName.</p>\n";
}
//if the database connects then set cookies and close the connection
if ($DBConnect) {
    setcookie("first", "$first");
    setcookie("last", "$last");
    setcookie("email", "$email");
    setcookie("contact", "$contact");
    $body .= "<p>Closing database connection.</p>\n";
    mysqli_close($DBConnect);
}
//if there are no errors then display the form
if ($errors == 0) {
    displayForm($cookie);
}
//if the errors are greater than 0 error out
if ($errors > 0) {
        ++$errors;
        $body .= "<p>You have not registered" . 
        " Please return to the " . 
        "<a href='Index.php'>" . 
        " Registration Page</p>\n";
}
?>
<!doctype html>

<!--
Author: Ty Ary
Date: 12.4.18

Filename: RegisterCompany.php
-->

<html>
	<head>
		<title>Register Company</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="initial-scale=1.0">
	</head>

	<body>
	<?php
        function displayForm($cookie) {
        ?>
        <form action="RegisterConference.php" method="post">
        <h2>Company Information</h2>
        <p>
            Company Name:
            <input type="text" name="compn" value="<?php if ($cookie) { echo $_COOKIE['compName']; } ?>"/>
        </p>
        <p>
            Company Email:
            <input type="text" name="compe" value="<?php if ($cookie) { echo $_COOKIE['compEmail']; } ?>"/>
        </p>
        <input type="submit" name="submit" value="Submit"/>
        </form>
        <form><input type="submit" name="resetComp" value="Reset form"/></form>
        <?php
        }
        echo $body;
        ?>
	</body>
</html>