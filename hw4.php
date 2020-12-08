<?php
echo "Name : Giang Duong Student ID: 014533857 <br> \n";
echo <<<_END
<html><head><title> Homework 4: CS 174 </title></head><body>
<form action='hw4.php' method= "post"  enctype='multipart/form-data'>

<br> Name <input type= "text" name = "name"> </br>
Select text file (.txt) need to upload: 
<input type='file' name="filename"  size ='10'>
<input type ='submit' value='Submit'>
</form>
_END;

# require_once "login.php";
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'username');
define('DB_PASSWORD', 'password');
define('DB_NAME', 'database');
# create a connection to database
$connect = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
# check connection
if ($connect->connect_error) {
    mysql_fatal_error($connect->connect_error, $connect);
}
# create a table with 2 columns, name column for input a string from the user
# TextFile column for store a file in a table
$query = "CREATE table IF NOT EXISTS homework4(
    Name varchar(255) NOT NULL, 
    TextFile  MEDIUMTEXT NOT NULL, 
    PRIMARY KEY (Name)
)ENGINE InnoDB;";

$query = 'SELECT * FROM homework4';
$result = $connect->query($query);
if (!$result) die ("Database access failed: " . $connect->error);

$rows = $result->num_rows;
for($j = 0; $j <$rows; ++$j)
{
    $result->data_seek($j);
    $row = $result->fetch_array(MYSQLI_BOTH);
    Echo <<<_END
         <pre>
         Name $row[0]
         Content $row[1]
         </pre>
         <form action="hw4.php" method="post">
         <input type="hidden" name="delete" value="yes">
         <input type="hidden" name="name" value="$row[0]">
         <input type="submit" value="DELETE RECORD"></form>
         _END;
}

# result from the query
$result = $connect->query($query);
if(!$result) mysql_fatal_error($connect->error, $connect);

if ($_FILES['filename']['size'][0] == 0 ) {
    $name = $_FILES['filename']['name'];
    # sanitizer the user input file name
    $name = santizerString($name);
    # this will check the file type that is text file txt or not
    switch ($_FILES['filename']['type']) {
        case 'text/txt':
            $ext = 'txt';
            break;
        case 'text/plain':
            $ext = 'txt';
            break;
        default:
            $ext = '';
            break;
    }
    if ($ext) { // if $ext is not empty
        $n = 'filetext.txt';
        move_uploaded_file($_FILES['filename']['tmp_name'], $n);
        echo "Upload text file on server $name as '$n': \n <br>";
        echo "<txt src='$n'>";
        $name2 = $_FILES['filename']['name'];
        echo("Now open file : '$name2'. \n <br>");
        $filehandle = fopen("$n", 'r+') or die ("File does not exist! \n<br>");
        $line = "";
        # read whole file, remove newline, and concatenate all into 1 line
        while (!feof($filehandle)) {
            $line2 = fgetc($filehandle);
            $line2 = str_replace(array("\n", "\r", " "), "", $line2);
            $line = $line . $line2;
        }
        $line = santizerString($line);
        $line = check_upperString($line);
        $line = fix_string($line);
        fclose($filehandle);

        if(isset($_POST['delete']) && isset($_POST['name']))
            # isset() function to check whether values for all the fields have been posted to the program
            # get_post() use to fetching input from the browser
        {
            $name = get_post($connect, 'name');
            $query = "DELETE FROM homework4 WHERE Name = '$name';";
            $result = $connect->query($query);
            if (!$result) echo "DELETE failed: $query<br>". $connect->connect_error . "<br><br>";
        }

        if (isset($_POST['name']) && isset($_POST['content']))
        {
            echo " Name and File from User Input. <br>";
            $name = get_post($connect, 'name');
            $name = santizerString($name);
            $name = check_upperString($name);
            $name = fix_string($name);
            $query = "INSERT into homework4 VALUES " . "('$name', '$line')";
            $result = $connect->query($query);
            if (!$result) echo "INSERT failed: $query<br>" . $connect->error . "<br><br>";
        }
    }
}

$result->close();
$connect->close();


# user friendly error
/**
 * @param $message
 * @param $connect
 */
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

# santizer the output
function santizerString($string){
    return htmlentities(fix_string($string));
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

?>