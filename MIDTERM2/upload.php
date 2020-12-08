<?php
echo "Name : Giang Duong. Student ID: 014533857. Midterm 2 CS 174 - Fall 2020 <br> \n<br>";
# require_once "login.php";
$hostname = 'localhost';
$username = 'giangduong';
$password = 'giang.duong';
$database = 'database';
# create connection to database
$connection = new mysqli($hostname, $username, $password, $database);
# check connection
if ($connection->connect_error) {
    mysql_fatal_error($connection->connect_error, $connection);
}
# start the session
session_start();
// session security
// preventing sesssion hijacking
# store the user IP address along with their other details
if($_SESSION['ip'] != $_SERVER['REMOTE_ADDR'] &&
    # calls the function different_user if the stored IP address doesn’t match the current one
    $_SESSION['check'] != hash('ripemd128', $_SERVER['REMOTE_ADDR']. $_SERVER['HTTP_USER_AGENT'])) {
    # combine the two checks like this and save the combination as a hash string
    different_user();
}
# compare the current agent string with the saved one
if(isset($_SESSION['username'])) {
    $usernames = $_SESSION['username'];
    $passwords = $_SESSION['password'];
    $firstname = $_SESSION['firstname'];
    $lastname = $_SESSION['lastname'];
    echo <<<_END
<html><head><title>Authentication is the key- MIDTERM</title></head><body>
                   Welcome!!! This is your account page
                    Welcome back $firstname.<br>
        Your full name is $lastname $firstname.<br><br>

    <form action="upload.php" method= "POST"  enctype="multipart/form-data">
    Content name: <input type='text' name='context' size ='10' maxlength="255">
    Upload File : <input type='file' name='selectedFile'>
<br> <input type ='submit' value='Submit'> </br>
</form>
_END;
    if (isset($_POST['context']) && $_FILES){
        if(file_tester($_FILES)){
            $name = get_post($connection, 'context');
            $filename = htmlentities($_FILES['selectedFile']['tmp_name']);
            $file_content = htmlentities(file_get_contents($filename));
            $query = "INSERT into credentials VALUES ('$usernames','$name','$file_content');";
            $result = $connection->query($query);
            if (!$result) die ("ERROR 11.\n");
        }
    }

    $query = "SELECT * FROM credentials";
    $result = $connection->query($query);
    if (!$result) die (print_error());

    $rows = $result -> num_rows;
    for ($j = 0; $j < $rows; ++$j){
        $result -> data_seek($j);
        $row = $result -> fetch_array(MYSQLI_NUM);
        echo <<<_END
<pre>
        Name: $row[1]
        Content: $row[2]
</pre>
_END;
    }
        if (isset($_POST['submit'])) {
            txtValidation();
        } else {
            echo "Please upload the correct txt file.";
        }
}

$connection->close();
# function for correct txt file
function txtValidation()
{
    if(isset($_FILES['selectedFile'])) //if sumbit button is pressed
    {
        $file = $_FILES['selectedFile']; //set selected uploaded file to a variable

        $file_error = $file['error']; //variable for upload error
        $file_name = $file['name']; //set file's name to a variable
        //Same as $_FILES["selectedFile"]["tmp_name"]
        //temp location of uploaded file without saving it locally
        $file_tmp = $file['tmp_name'];
        //Set each part of the file's name, seperated by ".", into an array
        //[0] = filename, [1] = file extension
        $filenameArray = explode(".", $file_name);
        //the array's last index value is the real file type extension for test case
        //just in case there is a similar file name like "filename.txt.pdf"
        $realFileExtension = strtolower(end($filenameArray));
        $allowExtension = array('txt'); //array to hold allow extension
        //If file extension and the allow extesion are the same and no upload error
        if(in_array($realFileExtension, $allowExtension) && $file_error == 0)
        {
            $document = file_get_contents($file_tmp);//get string or content of text tile
        }
        global $fileupload;
        $fileupload = $document;
        echo "Uploaded Text File String: "."<br>";
        echo $document."<br><br><br>";
        return $document;
    }
    else
    {
        echo ("ERROR");
    }
}

function validFileUpload() {
    if(!empty($_POST['inputBox'])){
        return false;
    } else{
        if($_FILES) {
            if($_FILES['filename']['type'] == "text/plain") {
                $name = $_FILES['filename']['name'];
                if(!validFile($name)) {
                    echo "Please upload file with numbers only.";
                    return false;
                } else {
                    move_uploaded_file($_FILES['filename']['tmp_name'], $name);
                    echo "Uploaded file $name <br><br>";
                    return true;
                }
            } else {
                echo "Please upload only file with .txt extension.";
                return false;
            }
        }
    }
}

function validInputBox() {
    if($_FILES) {
        return false;
    } else {
        if(!empty($_POST['inputBox'])) {
            $content = $_POST['inputBox'];
            $content = preg_replace('/[ ,]+/', '', $content);
            if(is_numeric($content)) {
                return true;
            } else {
                echo "Please give input with only numbers.";
                return false;
            }
        } else {
            echo "Please give some input with only numbers.";
            return false;
        }
    }
}

# inform the user to type a input again
function different_user() {
    destroy_session_and_data();
    die("Sorry, time out.<br> Please log in again <a href='welcome.php'>here</a>");
}

function destroy_session_and_data() {
    $_SESSION = array();
    setcookie(session_name(), '', time() - 2592000, '/');
    session_destroy();
}

function validFile($fileName) {
    $strInput = file_get_contents($fileName);
    if(is_numeric($strInput))
        return true;
    else
        return false;
}

function mysql_fix_string($connection, $string) {
    if(get_magic_quotes_gpc())
        $string = stripslashes($string);
    return $connection->real_escape_string($string);
}

# user friendly error
function mysql_fatal_error($message, $connect){
    $message2 = mysqli_error($connect);
    echo <<<_END
        We are sorry but it was not possible to complete the requested task. 
        The Error massage we got was: 
        <p>$message: $message2</p>
        Plesse click at the back button on your browser and try it again. 
        If you still have problems please <a href="mailto:admin@server.com"> email us</a>.
        Thank you. 
_END;
}
function mysql_entities_fix_string($connection, $string) {
    return htmlentities(mysql_fix_string($connection, $string));
}
function file_tester($file)
{
    if ((htmlentities($file['filename']['type']) == 'text/plain') || (htmlentities($file['filename']['type']) == 'txt'))
        return true;
    else {
        echo "Please submit correct file type (.txt)";
        return true;
    }
}

function print_error(){
    return "There might be an error";
}

function get_post($conn, $var)
{
    return $conn->real_escape_string($_POST[$var]);
}


# set the time to log out automatically
#ini_set('session.gc_maxlifetime', 60*60*24);

# know the time left
#function know_the_time_left(){
#    return ini_get('session.gc_maxlifetime');
#}
?>