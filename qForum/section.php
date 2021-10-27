<?php
// start the session to use the session variables
session_start();

// if the user is logged in the script will redirect to user panel
if(!isset($_SESSION['zalogowany']))
{
    header("Location: login.php");
    exit();
}

// importing database connection details
require_once "connect.php";

// set a connection with a database
$connection = new mysqli($host, $db_user, $db_password, $db_name);
$id = $_GET['id'];

// query that selects selected section name from database
$query_get_section_name = "SELECT name FROM sections WHERE IDsection = $id";

if($result_section_name = $connection->query($query_get_section_name))
{
    // if there are any rows in the result variable then proceed
    if($result_section_name->num_rows > 0)
    {
        // fetch all columns in the row to an assotiative table named $row
        $row = $result_section_name->fetch_assoc();
        
        // set the section name to a variable
        $name = $row['name'];

        // dissolve the result array
        $result_section_name->free_result();
    }
    else
    {
        // if the section id will be incorrect then write error msg to variable
        $name = "BŁĄD! Nie można znaleźć tytułu działu!";
    }
}
// dissolve the $row table
unset($row);
?>

<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="utf-8">
        <title><?php echo $name; ?> - qForum</title>
        <link rel="stylesheet" href="style.css" type="text/css">
        <link rel="stylesheet" href="panel.css" type="text/css">
        <link rel="stylesheet" href="section.css" type="text/css">
        <script src="clock.js"></script>
    </head>
    <body>
        <div id="container">
            <header>
                <table id="head_table">
                    <tr>
                        <td id="logged_as">
                            Zalogowany jako: 
                            <?php
                            // show login of the logged user
                            echo $_SESSION['login'];
                            ?>
                        </td>
                        <td id="clock">
                        </td>
                        <td id="last_seen">
                            Ostatnie logowanie: 
                            <?php
                            // show the date of user's last login
                            echo $_SESSION['last_seen'];
                            ?>
                        </td>
                    </tr>
                </table>
            </header>
            <hr>

            <div id="link_chain">
            <a href="panel.php">Forum</a>
            <span> -> </span>
            <a href="section.php?id=<?php echo $id; ?>">
            <?php echo $name; ?>
            </a>
            </div>
            <br>

            <?php

            // definition of a post
            class Post
            {
                public $id;
                public $author;
                public $topic;
                public $views;
                public $comments;
                private $connection;

                function __construct($id, $author, $topic, $views, $comments, $connection)
                {
                    $this->id = $id;
                    $this->author = $author;
                    $this->topic = $topic;
                    $this->views = $views;
                    $this->comments = $comments;
                    $this->connection = $connection;
                }
                public function print()
                {
                    echo "<div class='table_border'><table><tr>";
                    echo "<td class='post_title'><a href=post.php?id=" . $this->id . ">" . $this->topic . "</a></td>";
                    echo "<td class='post_views'>" . "Wyświetleń: " . $this->views . "</td>";
                    echo "<td class='post_answers'>" . "Odpowiedzi: " . $this->comments . "</td>";
                    echo "</tr></table></div>";
                }
            }

            echo "<p class='section_name'>Posty w dziale: " . $name . "</p><br>";

            // query that selects all info abouts posts from this section
            $query_get_posts = "SELECT * FROM posts WHERE SectionID = $id";
            if($result_posts = $connection->query($query_get_posts))
            {
                // create an array for posts
                $posts = array();

                // if there are any rows in the result variable then proceed
                if($result_posts->num_rows > 0)
                {
                    // fetch all columns in the row to an assotiative table named $row
                    while($row = $result_posts->fetch_assoc())
                    {
                        // create a new object of post type with arguments given from the row
                        $post = new Post($row['IDpost'], $row['AuthorID'], $row['topic'], $row['views'], $row['comments'], $connection);
                        
                        // add the section object at the end of the sections array
                        array_push($posts, $post);
                    }

                    // dissolve the result array
                    $result_posts->free_result();

                    // print out all posts one by one using the loop
                    for($i=0; $i<count($posts); $i++)
                    {
                        $posts[$i]->print();
                    }
                }
                else
                {
                    // print this when no posts are found
                    echo "Brak postów w tym dziale!";
                }
            }

            //close the database connection
            $connection->close();

            ?>
        </div>
        <script>
            clock_update();
        </script>
    </body>
</html>