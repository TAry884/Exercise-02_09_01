<?php
session_start();
?>
<!doctype html>

<!--
Author: Ty Ary
Date: 11.15.18

Filename: VerifyLogin.php
-->

<html>
	<head>
		<title>Verify Intern Login</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="initial-scale=1.0">
		<script src="modernizr.custom.65897.js"></script>
	</head>

	<body>
        <h1>College Internship</h1>
        <h2>Verify Intern Login</h2>
        <?php
        //debug and server variables
        $errors = 0;
        $hostName = "localhost";
        $username = "adminer";
        $password = "dress-front-35";
        $DBConnect = false;
        $DBName = "internships2";
        $TableName = "interns";
        //If there are zero erros then connect to the database
        if ($errors == 0) {
            $DBConnect = mysqli_connect($hostName, $username, $password);
            //if the database doesnt connect then send a error
            if (!$DBConnect) {
                ++$errors;
                echo "<p>Unable to connect to the database server" . 
                    " error code:" . mysqli_connect_error($DBConnect) .
                    "</p>\n";
                //else select the database
            } else {
                $result = mysqli_select_db($DBConnect, $DBName);
                //if there was no result throw an error
                if (!$result) {
                    echo "<p>Unable to select the database" . 
                        "\"$DBName\" error code: " .
                        mysqli_error($DBConnect) .
                        "</p>\n";
                }
            }
        }
        if ($errors == 0) {
            $SQLstring = "SELECT internID, first, last FROM $TableName" . 
                " WHERE email='" . stripslashes($_POST['email']) . 
                "' AND password_md5='" . 
                md5(stripslashes($_POST['password'])) . "'";
            $queryResult = mysqli_query($DBConnect, $SQLstring);
            if (!$queryResult) {
                ++$errors;
                echo "<p>Query not executed, bad SQL syntax.</p>\n";
            }
            //if there are no errors and the number of rows is 0 throw an error
            if ($errors == 0) {
                if (mysqli_num_rows($queryResult) == 0) {
                    ++$errors;
                    echo "<p>The email address/password combination entered is not valid.</p>\n";
                } else {
                    $row = mysqli_fetch_assoc($queryResult);
//                    $internID = $row['internID'];
                    $_SESSION['internID'] = $row['internID'];
                    $internName = $row['first'] . " " . $row['last'];
                    mysqli_free_result($queryResult);
                    echo "<p>Welcome back $internName!</p>\n";
                }
            }
        }
        if ($DBConnect) {
            echo "<p>Closing database connection</p>\n";
            mysqli_close($DBConnect);
        }
        if ($errors == 0) {
//            echo "<form action='AvailableOpportunities.php'" . 
//                " method='post'>\n";
//            echo "<input type='hidden' name='internID' value='$internID'/>\n";
//            echo "<input type='submit' name='submit' value='View Available Opportunities'/>\n";
//            echo "</form>";
//            echo "<p><a href='AvailableOpportunities.php?" . 
//                "internID=$internID'>Available Oppportunities" . 
//                "</a></p>\n"; 
            echo "<p><a href='AvailableOpportunities.php?" . 
                "PHPSESSID=" . session_id() . "'>Available Oppportunities" . 
                "</a></p>\n";
        }
        if ($errors > 0) {
            echo "<p>Please use your browser's BACK button" .
                " to return to the form and fix the errors" .
                " indicated.</p>\n";
        }
        ?>
	</body>
</html>