<?php
echo "Name : Giang Duong. Student ID: 014533857. Homework 5 CS 174 - Fall 2020 <br> \n<br>";
echo <<<_END
<html><head><title>Upload a text file and return greatest product of four adjacent numbers in 4 posible direction.</title></head><body>
<form action='hw5.php' method= "post"  enctype='multipart/form-data'>
 ADD information : 
 Advisor name   : <input type='text' name='advisor' size ='10' maxlength="255">
 Student name   : <input type='text' name='studentname'size ='10' maxlength="255">
 Student ID     : <input type='text' name='studentid'size ='10' maxlength="255">
 Class Code     : <input type='text' name='classcode'size ='10' maxlength="255">
<br> <input type ='submit' value='Submit'> </br>
 Search information: 
 Advisor name   : <input type='text' name='sadvisor' size ='10' maxlength="255">
 Student name   : <input type='text' name='sstudentname'size ='10' maxlength="255">
 Student ID     : <input type='text' name='sstudentid'size ='10' maxlength="255">
 Class Code     : <input type='text' name='sclasscode'size ='10' maxlength="255">
 <br> <input type ='Search' value='search'> </br>
</form>
_END;

# require_once "login.php";
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'username');
define('DB_PASSWORD', 'password');
define('DB_NAME', 'database');
define('table', 'homework5');
$table = 'homework5';
# create a connection to database
$connect = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
# check connection
if ($connect->connect_error) {
    mysql_fatal_error($connect->connect_error, $connect);
}

# create a database that contains at least a table to store the information in input to the webpage
$query = "CREATE table IF NOT EXISTS homework5(
    advisor varchar(255) NOT NULL, 
    studentname  varchar(255) NOT NULL, 
    studentid int AUTO_INCREMENT,
    classcode varchar(255) NOT NULL ,
    PRIMARY KEY (studentid)
)ENGINE InnoDB;";

$result = $connect->query($query);
if (!$result) die ("Database creation failed: " . $connect->error);




# insert the information from user input to the database after sanitizer the user input
addrecord($connect);

# search the information from user input
usersearch($connect);

# delete the record
deleterecord($connect);

$result->close();
$connect->close();


# user friendly error
function mysql_fatal_error($message, $connect){
    $message2 = mysqli_error($connect);
    echo <<<_END
        We are sorry but it was not possible to complete the requested task. 
        The Error massage we got was: 
        <p>$message: $message2</p>
        Plesse click at the back button on your browser and try it again. 
        If you still have problems pease <a href="mailto:admin@server.com"> email us</a>.
        Thank you. 
_END;
}

# sanitizer the output and unwanted slashes
function santizerString($string){
    $string = strip_tags($string);
    return  htmlentities(fix_string($string));
}

function fix_string($string){
    if (get_magic_quotes_gpc()) $string = stripslashes($string);
    return real_escape_string($string);
}

# convert all chacter into uppercase
function check_upperString($string){
    return str_replace(" ", "", strtoupper($string));
}

# method of the connection object to strip out abt characters that hackers can insert to break into your database
function get_post($connect, $var){
    return $connect->real_escape_string($_POST[$var]);
}
function usersearch($connect)
{
    $sadvisor = get_post($connect, 'sadvisor');
    $sstudentname = get_post($connect, 'sstudentname');
    $sstudentid = get_post($connect, 'sstudentid');
    $sclasscode = get_post($connect, 'sclasscode');
    $sadvisor = santizerString($sadvisor);
    $sstudentname = santizerString($sstudentname);
    $sstudentid = santizerString($sstudentid);
    $sclasscode = santizerString($sclasscode);
    $query = "SELECT * FROM homework5 WHERE advisor = '$sadvisor', studentname= '$sstudentname', 
studentid = '$ssttudentid', classcode = '$sslasscode';";
    $result = $connect->query($query);
    if (!$result) die ("Database access failed: " . $connect->error);
    $rows = $result->num_rows;
    for ($j = 0; $j < $rows; ++$j) {
        $result->data_seek($j);
        $row = $result->fetch_array(MYSQLI_BOTH);
        echo <<<_END
         <pre>
         Advisor name:  $row[0]
         Student name:  $row[1]
         Student ID  :  $row[2]
         Class Code  :  $row[3]
         </pre>
         <form action="hw5.php" method="post">
         <input type="hidden" name="delete" value="yes">
         <input type="hidden" name="advisor" value="$row[0]">
         <input type="submit" value="DELETE RECORD"></form>
_END;
    }
}

# adding record to the database function from user input value 
function addrecord($connect){
    if (isset($_POST['advisor']) && isset($_POST['studentname'])
        && isset($_POST['studentid'])  && isset($_POST['classcode']))
    {
        echo " List . <br>";
        $advisor = get_post($connect, 'advisor');
        $studentname = get_post($connect, 'studentname');
        $studentid = get_post($connect, 'studentid');
        $classcode = get_post($connect, 'classcode');
        $advisor = santizerString($advisor);
        $studentname = santizerString($studentname);
        $studentid = santizerString($studentid);
        $classcode = santizerString($classcode);

        $query = "INSERT into homework5 VALUES " . "('$advisor', '$studentname','$studentid','$classcode')";
        $result = $connect->query($query);
        if (!$result) echo "INSERT failed: $query<br>" . $connect->error . "<br><br>";
    }
}

# delete record from the database from user input value
function deleterecord($connect){
    if(isset($_POST['delete']) && isset($_POST['advisor']))
        # isset() function to check whether values for all the fields have been posted to the program
        # get_post() use to fetching input from the browser
    {
        $advisor = get_post($connect, 'advisor');
        $advisor = santizerString($advisor);
        $query = "DELETE FROM homework5 WHERE advisor = '$advisor';";
        $result = $connect->query($query);
        if (!$result) echo "DELETE failed: $query<br>". $connect->connect_error . "<br><br>";
    }
}

# function to show the all records from the database with the input value
function showrecord($connect){
    $query = 'SELECT * FROM homework5';
    $result = $connect->query($query);
    if (!$result) die ("Database access failed: " . $connect->error);
    $rows = $result->num_rows;
    for($j = 0; $j <$rows; ++$j)
    {
        $result->data_seek($j);
        $row = $result->fetch_array(MYSQLI_BOTH);
        Echo <<<_END
         <pre>
         Advisor name:  $row[0]
         Student name:  $row[1]
         Student ID  :  $row[2]
         Class Code  :  $row[3]
         </pre>
         <form action="hw5.php" method="post">
         <input type="hidden" name="delete" value="yes">
         <input type="hidden" name="advisor" value="$row[0]">
         <input type="submit" value="DELETE RECORD"></form>
_END;
    }
}
?>