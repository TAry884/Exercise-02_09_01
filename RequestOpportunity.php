<?php
session_start();
$body = "";
$errors = 0;
$internID = 0;
//if (isset($_GET['internID'])) {
//    $internID = $_GET['internID'];
//}
if (!isset($_SESSION['internID'])) {
    ++$errors;
    $body .= "<p>You have not logged in or registered." . 
        " Please return to the " . 
        " <a href='InternLogin.php'>" . 
        "Registration / Login Page</a></p>\n";
}
if ($errors == 0) {
    if (isset($_GET['opportunityID'])) {
        $opportunityID = $_GET['opportunityID'];
    } else {
        ++$errors;
        $body .= "<p>You have not selected a opportunity." . 
            " Please return to the " . 
            " <a href='AvailableOpportunities.php?" . 
            "PHPSESSID=" . session_id() . "'>" . 
            "Opportunities Page</a></p>\n";
    }
}

$hostName = "localhost";
$username = "adminer";
$password = "dress-front-35";
$DBConnect = false;
$DBName = "internships2";
$TableName = "interns";
if ($errors == 0) {
    //If there are zero erros then connect to the database
    if ($errors == 0) {
        $DBConnect = mysqli_connect($hostName, $username, $password);
        //if the database doesnt connect then send a error
        if (!$DBConnect) {
            ++$errors;
            $body .= "<p>Unable to connect to the database server" . 
                " error code:" . mysqli_connect_error($DBConnect) .
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
}
$displayDate = date("l, F j, Y, g:i A");
$body .= "\$displayDate: $displayDate<br>";
$dbDate = date("Y-m-d H:i:s");
$body .= "\$dbDate: $dbDate<br>";
if ($errors == 0) {
    $TableName = "assigned_opportunities";
    $SQLString = "INSERT INTO $TableName" . 
        " (opportunityID, internID, dateSelected)" .
        " VALUES($opportunityID, ". $_SESSION['internID'] . ", '$dbDate')";
    $queryResult = mysqli_query($DBConnect, $SQLString);
    if (!$queryResult) {
        ++$errors;
        $body .= "<p>Unable to execute the query, " . 
            "error code: " . mysqli_errno($DBConnect) . ": " . 
            mysqli_error($DBConnect) . "</p>\n";
    } else {
        $body .= "<p>Your results for opportunity #" . 
            "$opportunityID have been entered on " . 
            " $displayDate.</p>\n";
    }
}
if ($DBConnect) {
    $body .= "<p>Closing database connection</p>\n";
    mysqli_close($DBConnect);
}
if ($_SESSION['internID'] > 0) {
    $body .= "<p>Return to the " . 
        "<a href='AvailableOpportunities.php?" . 
        "PHPSESSID=" . session_id() . "'>Available Opportunities" . 
        "</a> page.</p>\n";
} else {
    $body .= "<p>Please " . 
        "<a href='InternLogin.php'>" . 
        "Register or login" . 
        "</a> to use this.</p>\n";
}
if ($errors == 0) {
    $body .= "Setting cookie<br>";
    setcookie("LastRequestDate", 
             urlencode($displayDate),
             time()+60*60*60*24*7);
}
?>
<!doctype html>

<!--
Author: Ty Ary
Date: 11.26.18

Filename: RequestOpportunity
-->

<html>
	<head>
		<title>Request Opportunity</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="initial-scale=1.0">
		<script src="modernizr.custom.65897.js"></script>
	</head>

	<body>
        <h1>College Internship</h1>
        <h2>Opportunity Requested</h2>
        <?php
        echo $body;  
        ?>
	</body>
</html>