<?php
//global database variables
$hostname = "localhost";
$username = "adminer";
$passwd = "dress-front-35";
$DBConnect = false;
$DBName = "conference";
$errors = 0;
$body = "";
//if no errors then connect to the database
if ($errors == 0) {
    $DBConnect = mysqli_connect($hostname, $username, $passwd);
    if (!$DBConnect) {
        ++$errors;
        $body .= "<p>Unable to connect to the database server" . 
            " error code: " . mysqli_connect_error() . 
            "</p>\n";
    } else {
        $result = mysqli_select_db($DBConnect, $DBName);
        if (!$result) {
            $body .= "<p>Unable to select the database" . 
                "\"$DBName\" error code: " . 
                mysqli_error($DBConnect) . "</p>\n";        
        }
    }
}
//sets a variable for each of the cookies
$first = $_COOKIE['first'];
$last = $_COOKIE['last'];
$contact = $_COOKIE['contact'];
$email = $_COOKIE['email'];
$compName = $_COOKIE['compName'];
$compEmail = $_COOKIE['compEmail'];
//if any of the seminars arent empty then set a variable if not then set it to nothing
if (!empty($_COOKIE['seminars'][0])) {
    $seminar1 = $_COOKIE['seminars'][0];
} else {
    $seminar1 = "";
}
if (!empty($_COOKIE['seminars'][1])) {
    $seminar2 = $_COOKIE['seminars'][1];
} else {
    $seminar2 = "";
}
if (!empty($_COOKIE['seminars'][2])) {
    $seminar3 = $_COOKIE['seminars'][2];
} else {
    $seminar3 = "";
}
//if the user got to the form from another page without any of the information error out
if (!$_POST) {
    ++$errors;
    $body .= "<p>You have not completed the form, please return to the <a href='Index.php'>Beginning Page</a> And complete your form</p>";
}
//if no errors insert all the data into the database
if ($errors == 0) {
    $TableName = "attendee";
    $SQLString = "INSERT INTO $TableName" . 
        " (first, last, contact, email, compName, compEmail, seminar1, seminar2, seminar3)" . 
        " VALUES('$first', '$last', '$email', '$contact', '$compName', '$compEmail', '$seminar1', '$seminar2', '$seminar3')";
    $queryResult = mysqli_query($DBConnect, $SQLString);
    if (!$queryResult) {
        ++$errors;
        $body .= "<p>Unable to execute the query, " . 
            "error code: " . mysqli_errno($DBConnect) . ": " . 
            mysqli_error($DBConnect) . "</p>\n";
        //if successfull delete the cookies
    } else {
        $body .= "<p>Your application has been successfully submitted!</p>\n";
        setcookie("first", "");
        setcookie("last", "");
        setcookie("contact", "");
        setcookie("email", "");
        setcookie("compName", "");
        setcookie("compEmail", "");
        setcookie("seminars[0]", "");
        setcookie("seminars[1]", "");
        setcookie("seminars[2]", "");
    }
}
//close the database
if ($DBConnect) {
    $body .= "<p>Closing database connection</p>\n";
    mysqli_close($DBConnect);
}
?>
<!doctype html>

<html>
	<head>
		<title>Page Title</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="initial-scale=1.0">
	</head>

	<body>
        <?php
        echo $body;
        
        if ($errors == 0) {
        ?>
        <p>Thank your for submitting your application!</p>
        <p>To submit a new application click <a href="Index.php" >Here!</a></p>
        <?php
        }
        ?>
	</body>
</html>