<?php
echo "Name : Giang Duong. Student ID: 014533857. Midterm 2 CS 174 - Fall 2020 <br> \n<br>";
# require_once "login.php";
$hostname = 'localhost';
$username = 'giangduong';
$password = 'giang.duong';
$database = 'database';
$connection = new mysqli($hostname, $username, $password, $database);

if ($connection->connect_error)  {
    mysql_fatal_error($connection->connect_error, $connection);
    die ("Something went wrong.\n");
}
# create a database that contains at least a table to store the information in input to the webpage
$query = "CREATE table IF NOT EXISTS information(
    firstname VARCHAR(255) NOT NULL,
	lastname VARCHAR(255) NOT NULL,
	email VARCHAR(255) NOT NULL,
	username VARCHAR(255) NOT NULL PRIMARY KEY,
	password VARCHAR(255) NOT NULL, 
	salt1 VARCHAR(255) NOT NULL, 
	salt2 VARCHAR(255) NOT NULL
)ENGINE InnoDB;";
$result = $connection->query($query);
if (!$result) die ("Database creation failed: " . $connection->error);
$query = "INSERT INTO information VALUES('giang', 'duong', 'giangdvt177@gmail.com', 'giang', 'duong', '111', '222')";
$result = $connection->query($query);
# create a database that contains at least a table to store the credentials information from user
$query = "CREATE table IF NOT EXISTS credentials(
    username varchar(255) NOT NULL, 
    fileupload varchar(255) NOT NULL, 
    textupload varchar(255) NOT NULL,
    foreign key (username) references information(username)
)ENGINE InnoDB;";
$result = $connection->query($query);
if (!$result) die ("Database creation failed: " . $connection->error);

echo <<<_END
    <html>
        <head><title>Midterm 2- </title></head>
        <body>
        New user?
        <br><br>
        <a href='signup.php'><button>Sign up</button></a>
        <br><br>Already have an account?<br><br>
        <form action="welcome.php" method="post">
        <div>
            <label for="username"><b>User name</b></label>
            <input type="text" placeholder="Enter username" name="username" required/>
            <br>
            <label for="password"><b>Password</b></label>
            <input type="password" placeholder="Enter password" name="password" required/>
            <br><br>
            <input type='submit' name='login' value='Login'/>
        </div>
        </form>
_END;
echo "</html></body>";


if (isset($_POST['login'])) {
    $un_temp = mysql_entities_fix_string($connection, $_POST['username']);
    $pw_temp = mysql_entities_fix_string($connection, $_POST['password']);
    $query = "SELECT * FROM information where username = '$un_temp';";
    $result = $connection->query($query);

    if (!$result) die ("Something went wrong");
    elseif ($result->num_rows) {
        $row = $result->fetch_array(MYSQLI_NUM);
        $result->close();
        $salt1 = $row[5];
        $salt2 = $row[6];
        $token = hash('ripemd128', "$salt1$pw_temp$salt2");

        if ($pw_temp == $row[4]) {
            session_start();
            $_SESSION['username'] = $un_temp;
            $_SESSION['password'] = $pw_temp;
            $_SESSION['firstname'] = $row[0];
            $_SESSION['lastname'] = $row[1];
            $_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];

            //redirect page after user authorization
            //to upload page
            header("Location: upload.php");
        } else
            die("Invalid password or username");
    } else
        die("Invalid password or username");
}

$connection->close();

function mysql_entities_fix_string($connection, $string)
{
    return htmlentities(mysql_fix_string($connection, $string));
}

function mysql_fix_string($connection, $string)
{
    if (get_magic_quotes_gpc())
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
?>




