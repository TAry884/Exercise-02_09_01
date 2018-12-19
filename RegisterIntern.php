<?php
session_start();
$body = "";
//error count and email field
$errors = 0;
$email = "";
//if the email field is empty throw an error
if (empty($_POST['email'])) {
    ++$errors;
    $body .= "<p>Your need to enter an e-mail address</p>\n";
    //else strip the email of slashes and validate that it is a proper email
} else {
    $email = stripslashes($_POST['email']);
    if (preg_match("/^[\w-]+(\.[\w-])*@[\w-]+(\.[\w-]+)*(\.[A-Za-z]{2,})$/i", $email) == 0) {
        ++$errors;
        $body .= "<p>You need to enter a valid e-mail address.</p>\n";
        $email = "";
    }
}
//if the password is empty throw an error
if (empty($_POST['password'])) {
    ++$errors;
    $body .= "<p>You need to enter a password.</p>\n";
    //strip the slashes from the password
} else {
    $password = stripslashes($_POST['password']);
}
//if the second password field is empty throw an error
if (empty($_POST['password2'])) {
    ++$errors;
    $body .= "<p>You need to enter a confirmation password.</p>\n";
    //strip the slashes from the confirm password field
} else {
    $password2 = stripslashes($_POST['password2']);
}
//if both password fields are not empty and the string length is less than 6 throw a error
if (!empty($password) && !empty($password2)) {
    if (strlen($password) < 6) {
        ++$errors;
        $body .= "<p>The password is too short</p>\n";
        $password = "";
        $password2 = "";            
    }
    //if the passwords do no equal each other throw an error
        if ($password <> $password2) {
        ++$errors;
        $body .= "<p>The passwords do not match</p>\n";
        $password = "";
        $password2 = "";
    }
}
//database global variables
$hostname = "localhost";
$username = "adminer";
$passwd = "dress-front-35";
$DBConnect = false;
$DBName = "internships2";
//if there are no errors connect to the database
if ($errors == 0) {
    $DBConnect = mysqli_connect($hostname, $username, $passwd);
    //if the database didnt connect throw a error with the error codes
    if (!$DBConnect) {
        ++$errors;
        $body .= "<p>Unable to connect to the database server" . 
            " error code: " . mysqli_connect_error() . 
            "</p>\n";
        //else select the database
    } else {
        $result = mysqli_select_db($DBConnect, $DBName);
        //if there was no result throw an error
        if (!$result) {
            $body .= "<p>Unable to select the database" . 
                "\"$DBName\" error code: " .
                mysqli_error($DBConnect) .
                "</p>\n";
        }
    }
}
//table name for the table in the database
$TableName = "interns";
//if there are no errors select the email row from the tablename no matter the count and get the strings
if ($errors == 0) {
    $SQLstring = "SELECT count(*) FROM $TableName" . 
        " WHERE email='$email'";
    $queryResult = mysqli_query($DBConnect, $SQLstring);
    //if it gets the query result then fetch the email row from it
    if ($queryResult) {
        $row = mysqli_fetch_row($queryResult);
        //if the email is a duplicate then throw a error
        if ($row[0] > 0) {
            ++$errors;
            $body .= "<p>The e-mail address entered (" . 
                htmlentities($email) . ") is already entered.</p>\n";
        }
    }
}
//if there are no errors in the code strip the first and last names and insert all form information into the database
if ($errors == 0) {
    $first = stripslashes($_POST['first']);
    $last = stripslashes($_POST['last']);
    $SQLstring = "INSERT INTO $TableName" . 
        " (first, last, email, password_md5)" . 
        " VALUES('$first', '$last', '$email', '" . 
        md5($password) . "')";
    //gets the strings from the database
    $queryResult = mysqli_query($DBConnect, $SQLstring);
    //if it didnt get the strings from the database then throw an error
    if (!$queryResult) {
        ++$errors;
        $body .= "<p>Unable to save your registration" . 
            " information error code: " . 
            mysqli_error($DBConnect) . "</p>\n";
        //insert a new intern id through the database
    } else {
//        $internID = mysqli_insert_id($DBConnect);
        $_SESSION['internID'] = mysqli_insert_id($DBConnect);
    }
}
//if there are no errors then thank the intern and give them their id
if ($errors == 0) {
    $internName = $first . " " . $last;
    $body .= "<p>Thank you, $internName. ";
    $body .= "Your new intern ID is <strong>" . $_SESSION['internID'] . "</strong>.</p>\n";
}
if ($DBConnect) {
    setcookie("internID", $_SESSION['internID']);
    $body .= "<p>Closing database connection.</p>\n";
    mysqli_close($DBConnect);
}
if ($errors == 0) {
//    $body .= "<form action='AvailableOpportunities.php'" . 
//        " method='post'>\n";
//    $body .= "<input type='hidden' name='internID' value='$internID'/>\n";
//    $body .= "<input type='submit' name='submit' value='View Available Opportunities'/>\n";
//    $body .= "</form>";
    $body .= "<p><a href='AvailableOpportunities.php?" . 
        "PHPSESSID=" . session_id() . "'>" . 
        "View Available Opportunities</a></p>\n";
}
//if there are errors then throw a error 
if ($errors > 0) {
    $body .= "<p>Please use your browser's back button" . 
        " to return to the form and fix the errors" . 
        " indicated</p>\n";
}
?>
<!doctype html>

<!--
Author: Ty Ary
Date: 11.13.18

Filename: RegisterIntern.php
-->

<html>
	<head>
		<title>Internship Registration</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="initial-scale=1.0">
		<script src="modernizr.custom.65897.js"></script>
	</head>

	<body>
        <h1>Internship</h1>
        <h2>Intern Registration</h2>
        <?php
        echo $body;
        ?>
	</body>
</html>