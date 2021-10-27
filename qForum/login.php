<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>qForum v1.0</title>
        <link rel="stylesheet" href="style.css" type="text/css">
        <link rel="stylesheet" href="login.css" type="text/css">
    </head>
    <body>
        <form action="" method="post">
            <span>Login:</span>
            <br/>
            <input name="login" type="text" required/>
            <br/>
            <br/>
            <span>Password: </span>
            <br/>
            <input name="password" type="password" required/>
            <br/>
            <input value="Log in" type="submit" />
        </form>
    </body>
</html>

<?php
// importing database connection details
require_once "connect.php";

// if the user is logged in the script will redirect to user panel
if(isset($_SESSION['zalogowany']))
{
    header('Location: panel.php');
    exit();
}

$connection = @new mysqli($host, $db_user, $db_password, $db_name);

// if an error will occur then the script will write the error number and quit
if($connection->connect_errno != 0)
{
    die("Error: " . $connection->connect_errno);
}
else if(isset($_POST['login']) && $_POST['password'] && $_POST['login'] != '' && $_POST['password'] != '') // if the login and password inputs are set and not empty proceed forward
{
    $login = $_POST['login'];
    $haslo = $_POST['password'];

    // change all special characters to html entities
    $login = htmlentities($login, ENT_QUOTES, "UTF-8");
    $haslo = htmlentities($haslo, ENT_QUOTES, "UTF-8");

    // call the query into the base and write the result as a collection of rows in the $result variable
    if($result = @$connection->query(sprintf("SELECT * FROM users WHERE BINARY login='%s' AND BINARY password=password('%s')",
        mysqli_real_escape_string($connection, $login),
        mysqli_real_escape_string($connection, $haslo))))
    {
        // if there are any matching results then proceed
        if($result->num_rows > 0)
        {
            // fetch all columns in the row to an assotiative table named $row
            $row = $result->fetch_assoc();

            // start a session
            session_start();
            $_SESSION['zalogowany'] = true;
            $_SESSION['login'] = $row['login'];
            $_SESSION['last_seen'] = $row['last_seen_date'];

            // update the user's last seen date
            $login_date = new DateTime();
            $new_login_date = $login_date->format('Y-m-d H:i:s');
            $temp_login = $_SESSION['login'];
            $nld_query = "UPDATE users SET last_seen_date='$new_login_date' WHERE login='$temp_login'";
            $connection->query($nld_query);

            // dissolve the result array
            $result->free_result();
            
            // proceed to next script
            header("Location: panel.php");
            exit();
        }
        else
        {
            // write this message if there are no matching results(no users with this credentials)
            die('<b class="error">Zły login lub hasło!</b>');
        }
    }
}

//close the database connection
$connection->close();

?>