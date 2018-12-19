<?php
session_start();
echo "Session ID: " . session_id() . "<br>\n";
?>
<!doctype html>

<!--
Author: Ty Ary
Date: 11.15.18

Filename: AvailableOpportunities.php
-->

<html>
	<head>
		<title>Available Opportunities</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="initial-scale=1.0">
		<script src="modernizr.custom.65897.js"></script>
	</head>

	<body>
        <h1>College Internship</h1>
        <h2>Available Opportunities</h2>
        <?php
        //Requests the interns id number
//        if (isset($_REQUEST['internID'])) {
//            $internID = $_REQUEST['internID'];
//            //The user doesnt have a ID
//        } else {
//            $internID = -1;
//        }
        if (isset($_COOKIE['LastRequestDate'])) {
            $lastRequestDate = 
                urldecode($_COOKIE['LastRequestDate']);
        } else {
            $lastRequestDate = "";
        }
        //Debug
        echo "\$internID: " . $_SESSION['internID'] . "\n";
        //Global 
        $errors = 0;
        $hostName = "localhost";
        $username = "adminer";
        $password = "dress-front-35";
        $DBConnect = false;
        $DBName = "internships2";
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
        $TableName = "interns";
        if ($errors == 0) {
            $SQLstring = "SELECT * FROM $TableName" .
                " WHERE internID='" . $_SESSION['internID'] . "'";
            $queryResult = mysqli_query($DBConnect, $SQLstring);
            if (!$queryResult) {
                ++$errors;
                echo "<p>Unable to execute the query, error code: " . 
                    mysqli_errno($DBConnect) . ": " . 
                    mysqli_error($DBConnect) . "</p>\n";
            } else {
                if (mysqli_num_rows($queryResult) == 0) {
                    ++$errors;
                    echo "<p>Invalid intern id!</p>";
                }
            }
        }
        if ($errors == 0) {
            $row = mysqli_fetch_assoc($queryResult);
            $internName = $row['first'] . " " . $row['last'];
        } else {
            $internName = "";
        }
        //Debug
        echo "\$internName: $internName";
        $TableName = "assigned_opportunities";
        if ($errors == 0) {
            $SQLstring = "SELECT count(opportunityID)" . 
                " FROM $TableName" . 
                " WHERE internID='" . $_SESSION['internID'] . "'" . 
                " AND dateApproved IS NOT NULL";
            $queryResult = mysqli_query($DBConnect, $SQLstring);
            if (mysqli_num_rows($queryResult) > 0) {
                $row = mysqli_fetch_row($queryResult);
                $approvedOpportunities = $row[0];
                mysqli_free_result($queryResult);
            }
        }
        if ($errors == 0) {
            $selectedOpportunities = array();
            $SQLstring = "SELECT opportunityID FROM $TableName" . 
                " WHERE internID='" . $_SESSION['internID'] . "'";
            $queryResult = mysqli_query($DBConnect, $SQLstring);
            if (mysqli_num_rows($queryResult) > 0) {
                while (($row = mysqli_fetch_row($queryResult)) != false) {
                    $selectedOpportunities[] = $row[0];
                }
                mysqli_free_result($queryResult);
            }
            $assignedOpportunities = array();
            $SQLstring = "SELECT opportunityID FROM $TableName" . 
                " WHERE dateApproved IS NOT NULL";
            $queryResult = mysqli_query($DBConnect, $SQLstring);
            if (mysqli_num_rows($queryResult) > 0) {
                while (($row = mysqli_fetch_row($queryResult)) != false) {
                    $assignedOpportunities[] = $row[0];
                }
                mysqli_free_result($queryResult);
            }
        }
        $TableName = "opportunities";
        $opportunities = array();
        if ($errors == 0) {
            $SQLstring = "SELECT opportunityID, company, city," . 
                " startDate, endDate, position, description" . 
                " FROM $TableName";
            $queryResult = mysqli_query($DBConnect, $SQLstring);
            if (mysqli_num_rows($queryResult) > 0) {
                while (($row = mysqli_fetch_assoc($queryResult)) != false) {
                    $opportunities[] = $row;
                }
                mysqli_free_result($queryResult);
            }
        }
        if ($DBConnect) {
            echo "<p>Closing database connection</p>\n";
            mysqli_close($DBConnect);
        }
        if (!empty($lastRequestDate)) {
            echo "<p>You last requested an internship" . 
                " opportunity on $lastRequestDate.</p>\n";
        }
        echo "<table border='1' width='100%'>\n";
        echo "<tr>\n";
        echo "<th style='background-color: cyan'>Company</th>\n";
        echo "<th style='background-color: cyan'>City</th>\n";
        echo "<th style='background-color: cyan'>Start Date</th>\n";
        echo "<th style='background-color: cyan'>End Date</th>\n";
        echo "<th style='background-color: cyan'>Position</th>\n";
        echo "<th style='background-color: cyan'>Description</th>\n";
        echo "<th style='background-color: cyan'>Status</th>\n";
        echo "</tr>\n";
        foreach ($opportunities as $opportunity) {
            if (!in_array($opportunity['opportunityID'], $assignedOpportunities)) {
                echo "<tr>\n";
                echo "<td>" . htmlentities($opportunity['company']) . "</td>\n"; 
                echo "<td>" . htmlentities($opportunity['city']) . "</td>\n"; 
                echo "<td>" . htmlentities($opportunity['startDate']) . "</td>\n"; 
                echo "<td>" . htmlentities($opportunity['endDate']) . "</td>\n"; 
                echo "<td>" . htmlentities($opportunity['position']) . "</td>\n"; 
                echo "<td>" . htmlentities($opportunity['description']) . "</td>\n";
                echo "<td>\n";
                if (in_array($opportunity['opportunityID'], $selectedOpportunities)) {
                    echo "Selected";
                } else if ($approvedOpportunities) {
                    echo "Open";
                } else {
                    echo "<a href='RequestOpportunity.php?" . 
                        "PHPSESSID=" . session_id() .
                        "&opportunityID=" . 
                        $opportunity['opportunityID'] . 
                        "'>Available</a>";
                }
                echo "</td>\n";
                echo "</tr>\n";
            }
        }
        echo "</table>\n";
        echo "<p><a href='InternLogin.php'>Log Out</a></p>";
         ?>
	</body>
</html>