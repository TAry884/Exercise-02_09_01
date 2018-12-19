<?php
//Global variables
$body = "";
$errors = 0;
$hostname = "localhost";
$username = "adminer";
$passwd = "dress-front-35";
$DBConnect = false;
$DBName = "conference";
//if no errors then connect to the database if not the error out else then select the database but if not then error out
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
//if certain cookies were not set then error out
if (!isset($_COOKIE['first']) || !isset($_COOKIE['last']) || !isset($_COOKIE['contact']) || !isset($_COOKIE['email']) || !isset($_COOKIE['compName']) || !isset($_COOKIE['compEmail'])) {
    ++$errors;
    $body .= "<p>You must complete all fields in order to view your information</p>\n";
}
//if the form was submitted and the seminars were empty then error out
if ($_POST) {
    if (empty($_POST['seminar1']) && empty($_POST['seminar2']) && empty($_POST['seminar3'])) {
        ++$errors;
        $body .= "You must check at least one of the options";
    }
}
//if no errors and if the seminars arent empty set the data they carry to a string
if ($errors == 0) {
    if (!empty($_POST['seminar1'])) {
        $seminar1 = $_POST['seminar1'];
        $seminar1 = "FBLA";
    } else {
        $seminar1 = "";
    }
    if (!empty($_POST['seminar2'])) {
        $seminar2 = $_POST['seminar2'];
        $seminar2 = "DECA";
    } else {
        $seminar2 = "";
    }
    if (!empty($_POST['seminar3'])) {
        $seminar3 = $_POST['seminar3'];
        $seminar3 = "FLEX";
    } else {
        $seminar3 = "";
    }
}
//if there are no errors then set cookies
if ($errors == 0) {
    setcookie("seminars[0]", "$seminar1");
    setcookie("seminars[1]", "$seminar2");
    setcookie("seminars[2]", "$seminar3");
    $seminar1 .= "<br>";
    $seminar2 .= "<br>";
    $seminar3 .= "<br>";
}
//if the database connects then close it
if ($DBConnect) {
    $body .= "<p>Closing database connection</p>\n";
    mysqli_close($DBConnect);
}

?>
<!doctype html>

<!--
Author: Ty Ary
Date: 12.6.18

Filename: RelookData.php
-->

<html>
	<head>
		<title>Page Title</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="initial-scale=1.0">
	</head>

	<body>
        <?php
        if ($errors == 0) {
            echo "<h1>Data Confirmation</h1>";
            echo "<h3>Personal Data</h3>";
            echo "<p>First Name " . $_COOKIE['first'] . "</p>\n";
            echo "<p>Last Name " . $_COOKIE['last'] . "</p>\n";
            echo "<p>Contact Informaiton " . $_COOKIE['contact'] . "</p>\n";
            echo "<p>Email " . $_COOKIE['email'] . "</p>\n";
            echo "<p><strong>If your informaion is wrong click <a href='Index.php'>Here</a></strong></p>\n";
            echo "<h3>Company Information</h3>\n";
            echo "<p>Company Name " . $_COOKIE['compName'] . "</p>\n";
            echo "<p>Company Email " . $_COOKIE['compEmail'] . "</p>\n";
            echo "<p><strong>If your company information is wrong click <a href='RegisterCompany.php'>Here</a></strong></p>\n";
            echo "<p>The seminar(s) you selected are <br>" . $seminar1 . $seminar2 . $seminar3 . "</p>\n";
            echo "<p><strong>If your would like to change your seminars click <a href='RegisterConference.php'>Here</a></strong></p>\n";
            echo "<br><p>If your information is correct please submit your application</p>\n";
            echo "<form action='Submission.php' method='post'>";
            echo "<input type='submit' name='submit' value='Submit Form' />";
            echo "</form>";
        } else {
            echo "<p>You have not completed all of the forms please go back and complete the forms";
        }
        ?>
	</body>
</html>