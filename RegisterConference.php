<?php
//Global variables
$errors = 0;
$body = "";
$compe = "";
//if empty then error out, if not stripslashes and then if the regex doesnt match then error out
if (empty($_POST['compe'])) {
    ++$errors;
    $body .= "<p>You need to enter an e-mail address";
} else {
    $compe = stripslashes($_POST['compe']);
    if (preg_match("/^[\w-]+(\.[\w-])*@[\w-]+(\.[\w-]+)*(\.[A-Za-z]{2,})$/i", $compe) == 0) {
        ++$errors;
        $body .= "<p>You need to enter a valid e-mail address.</p>\n";
        $compe = "";
    }
}
//if empty then error out if not then stripslashes
if (empty($_POST['compn'])) {
    ++$errors;
    $body .= "<p>You need to enter your first name.</p>\n";
} else {
    $compn = stripslashes($_POST['compn']);
}
//database global variables
$hostname = "localhost";
$username = "adminer";
$password = "dress-front-35";
$DBConnect = false;
$DBName = "conference";
$TableName = "attendee";
//if no errors the connect to the database
if ($errors == 0) {
    $DBConnect = mysqli_connect($hostname, $username, $password);
    //if it didnt connect then error out else select the database
    if (!$DBConnect) {
        ++$errors;
        $body .= "<p>Unable to connect to the database server" . 
            " error code:" . mysqli_connect_error($DBConnect) . 
            "</p>\n";
    } else {
        $result = mysqli_select_db($DBConnect, $DBName);
        //if not result then tell the user the error
        if (!$result) {
            $body .= "<p>Unable to select the database" . 
                "\"$DBName\" error code: " . 
                mysqli_error($DBConnect) . "</p>\n";
        }
    }
}
//if no errors then place mysql string in variable as well as the mysql query into a variable
if ($errors == 0) {
    $SQLstring = "SELECT count(*) FROM $TableName" . 
        " WHERE compEmail='$compe'";
    $queryResult = mysqli_query($DBConnect, $SQLstring);
    //if queryresult worked then fetch its row
    if ($queryResult) {
        $row = mysqli_fetch_row($queryResult);
        //if $row with a array of 1 is greater than 0 error out
        if ($row[0] > 0) {
            ++$errors;
            $body .= "<p>The e-mail address entered (" . 
                htmlentities($compe) . ") is already entered.</p>\n";
        }
    }
}
//if no errors the get the associative array in the query result and set the attendee name else attendee name is nothing
if ($errors == 0) {
    $row = mysqli_fetch_assoc($queryResult);
    $attendeeName = $row['first'] . " " . $row['last'];
} else {
    $attendeeName = "";
}
//change the table name
$TableName = "seminars";
//if no errors then get the seminars and set them in the row and free the query result
if ($errors == 0) {
    $assignedSeminars = array();
    $selectedSeminars = array();
    $SQLstring = "SELECT seminarID FROM $TableName";
    $queryResult = mysqli_query($DBConnect, $SQLstring);
    if (mysqli_num_rows($queryResult) > 0) {
        while (($row == mysqli_fetch_row($queryResult)) != false) {
            $assignedSeminars[] = $row[0];
            $selectedSeminars[] = $row[0];
        }
        mysqli_free_result($queryResult);
    }
}
//if no errors then get a mysql string into a variable and put a query into a variable
$seminars = array();
if ($errors == 0) {
    $SQLstring = "SELECT seminarID, seminar, description, time" . 
        " FROM $TableName";
    $queryResult = mysqli_query($DBConnect, $SQLstring);
    //if the rows are more than 0 then set the array into a row and free the queryresult
    if (mysqli_num_rows($queryResult) > 0) {
        while (($row = mysqli_fetch_assoc($queryResult)) != false) {
            $seminars[] = $row;
        }
        mysqli_free_result($queryResult);
    }
}
//if not errors then stripslash
if ($errors == 0) {
    $compe = stripslashes($_POST['compe']);
    $compn = stripslashes($_POST['compn']);
}
//if the database connects then set cookies and close the connection
if ($DBConnect) {
    setcookie("compName", "$compn");
    setcookie("compEmail", "$compe");
    $body .= "<p>closing database connection.</p>\n";
    mysqli_close($DBConnect);
}
//if there are more than 0 errors then error and display error text
if ($errors > 0) {
    ++$errors;
    $body .= "<p>You have not registered" . 
        " Please return to the " . 
        "<a href='Index.php'>" . 
        " Registration Page</p>\n";
}
//form for the seminars
$inc = 1;
$body .= "<form action='RelookData.php' method='post'>";
$body .= "<table border='1' width='100%'>\n";
$body .= "<tr>\n";
$body .= "<th style='background-color: cyan'>Seminar</th>\n";
$body .= "<th style='background-color: cyan'>Description</th>\n";
$body .= "<th style='background-color: cyan'>Time</th>\n";
$body .= "</tr>\n";
//foreach seminar that is in the database display it and put a check box on the end of it
foreach ($seminars as $seminar) {
    if (!in_array($seminar['seminarID'], $assignedSeminars)) {
        $body .= "<tr>\n";
        $body .= "<td>" . htmlentities($seminar['seminar']) . "</td>\n";
        $body .= "<td>" . htmlentities($seminar['description']) . "</td>\n";
        $body .= "<td>" . htmlentities($seminar['time']) . "</td>\n";
        $body .= "<td>\n";
        $body .= "<input type='checkbox' name='seminar" . $inc . "'>";
        $body .= "</td>\n";
        $body .= "</tr>\n";
        ++$inc;
    }
}
$body .= "</table>\n";
$body .= "<input type='submit' name='submit' value='Next Page' />";
$body .= "</form>";
?>
<!doctype html>

<!--
Author: Ty Ary
Date: 12.5.18

Filename: RegisterConference.php
-->

<html>
	<head>
		<title>Registered Conference</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="initial-scale=1.0">
		<script src="modernizr.custom.65897.js"></script>
	</head>

	<body>
        <h1>Register Conference</h1>
        <?php
        echo $body;
        ?>
	</body>
</html>