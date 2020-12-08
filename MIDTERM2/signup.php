<?php
echo "Name : Giang Duong. Student ID: 014533857. Midterm 2 CS 174 - Fall 2020 <br> \n<br>";
# require_once "login.php";
$hostname = 'localhost';
$username = 'giangduong';
$password = 'giang.duong';
$database = 'database';
$connection = new mysqli($hostname, $username, $password, $database);

echo <<<_END
        <html><head><title>Sign up an account here.</title></head>
        <body>
        <h2>Please fill out the form with your information.</h2>
        <form action='signup.php' method='post'>
        <div>
            <label for='firstname'><i>First name: </i></label>
            <input type='text' placeholder='First name' name='firstname' required/><br>
            <label for='lastname'><i>Last name : </i></label>
            <input type='text' placeholder='Last name' name='lastname' required/><br>
            <label for='email'><i>Email     :</i></label>
            <input type='text' placeholder='Email' name='email' required/><br>
            <label for='username'><i>User name :</i></label>
            <input type='text' placeholder='User name' name='username' required/><br>
            <label for='password'><i>Password     :</i></label>
            <input type='password' placeholder='Password' name='password' required/><br><br>
            <input type='submit' name='signup' value='Sign up'><br><br>
            <a href='welcome.php'>Already have an account?</a><br>
        </div>
        </form>
        </body></html>
_END;

if(isset($_POST['signup'])) {
    $firstname = mysql_entities_fix_string($connection, $_POST['firstname']);
    $lastname = mysql_entities_fix_string($connection, $_POST['lastname']);
    $email = mysql_entities_fix_string($connection, $_POST['email']);
    $usernames = mysql_entities_fix_string($connection, $_POST['username']);
    $passwords = mysql_entities_fix_string($connection, $_POST['password']);

    //user's information validation and insertion, check if users input is correct or not
    if(preg_match('/^[a-zA-Z]*$/', $firstname)) {
        if(preg_match('/^[a-zA-Z]*$/', $lastname)) {
            if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
                if(preg_match('/^[a-zA-Z0-9_-]*$/', $usernames)) {
                    if(strlen($_POST['password']) >= 8) {
                        $salt1 = rand();
                        $salt2 = rand();
                        $token = hash('ripemd128', "$salt1$passwords$salt2"); # hash function
                        $query = "INSERT INTO credentials VALUES('$firstname', '$lastname', '$email', '$usernames', '$token', '$salt1', '$salt2')";
                        $result = $connection->query($query);

                        if($result) {
                            die("Account created! <br><a href=welcome.php>Click here to continue</a>");
                        } else {
                            die ("Something went wrong");
                        }
                    } else {
                        echo "Password must contain at least 8 characters.";
                    }
                } else {
                    echo "Username can only contain: <br>
                            <p><i>English characters<br>digits<br>underscore(_) and dash(-)<br></i></p>";
                }
            } else {
                echo "Please enter a valid email address.";
            }
        } else {
            echo "Please enter a legit last name.";
        }
    } else {
        echo "Please enter a legit first name.  ";
    }
}


$connection->close();

function mysql_entities_fix_string($connection, $string) {
    return htmlentities(mysql_fix_string($connection, $string));
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
?>