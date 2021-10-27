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
$connection = @new mysqli($host, $db_user, $db_password, $db_name);
?>

<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="utf-8">
        <title>Panel Główny - qForum</title>
        <link rel="stylesheet" href="style.css" type="text/css">
        <link rel="stylesheet" href="panel.css" type="text/css">
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
            <br>
            <div id="forum">
                <?php
                // definion of a section
                class Section
                {
                    public $id;
                    public $name;
                    public $posts; // posts in the section
                    private $connection;

                    function __construct($id, $name, $connection)
                    {
                        $this->id = $id;
                        $this->name = $name;
                        $this->connection = $connection;

                        // query that reads all info about first 3 posts from that section
                        $query_get_posts = "SELECT * FROM posts WHERE SectionID = $this->id LIMIT 3";

                        // call the query into the base and write the result as a collection of rows in the variable
                        if($result_posts = $connection->query($query_get_posts))
                        {
                            // create an array to store post obiects
                            $this->posts = array();

                            // if there are any matching results then proceed
                            if($result_posts->num_rows > 0)
                            {
                                // fetch all columns in the row to an assotiative table named $row
                                while($row = $result_posts->fetch_assoc())
                                {
                                    // create a new object of post type with arguments given from the row taked
                                    $post = new Post($row['IDpost'], $row['AuthorID'], $row['topic'], $row['views'], $row['comments'], $this->connection);
                                    
                                    // add the post object at the end of the posts array
                                    array_push($this->posts, $post);
                                }
                                // dissolve the result array
                                $result_posts->free_result();
                            }
                            else
                            {
                            // write this message if there are no matching results(no posts in this section)
                            echo "Brak postów w tym dziale!";
                            }
                        }

                    }
                    
                    // print out the section table with first 3 posts in it
                    public function print()
                    {
                        echo "<div class='table_border'><p class='section_name'><a href=section.php?id=" . $this->id . ">" . $this->name . "</a></p>";
                        echo "<table><tr><td>";
                        // print out all posts one by one with the loop
                        for($i=0; $i<count($this->posts); $i++)
                        {
                            $this->posts[$i]->print();
                        }
                        echo "</td></tr></table>";
                        echo "</div>";
                    }
                    
                }

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

                    // print out the post header with infos like number of views and number of answers
                    public function print()
                    {
                        echo "<div class='table_border'><table><tr>";
                        echo "<td class='post_title'><a href=post.php?id=" . $this->id . ">" . $this->topic . "</a></td>";
                        echo "<td class='post_views'>" . "Wyświetleń: " . $this->views . "</td>";
                        echo "<td class='post_answers'>" . "Odpowiedzi: " . $this->comments . "</td>";
                        echo "</tr></table></div>";
                    }
                }
                
                // query that selects all info from all existing sections in the database
                $query_get_sections = "SELECT * FROM sections";

                // create an array for sections
                $sections = array();

                if($result_sections = $connection->query($query_get_sections))
                {
                    // if there are any rows in the result variable then proceed
                    if($result_sections->num_rows > 0)
                    {
                        // fetch all columns in the row to an assotiative table named $row
                        while($row = $result_sections->fetch_assoc())
                        {
                            // create a new object of section type with arguments given from the row
                            $section = new Section($row['IDsection'], $row['name'], $connection);

                            // add the section object at the end of the sections array
                            array_push($sections, $section);
                        }
                        
                        // dissolve the result array
                        $result_sections->free_result();

                        echo "<table>";
                        // print out all sections one by one using the loop
                        for($i=0; $i<count($sections); $i++)
                        {
                            echo "<tr><td>";
                            $sections[$i]->print();
                            echo "</td></tr>";
                        }
                        echo "</table>";
                    }
                    else
                    {
                        // print this when no sections are found
                        die("Nie znaleziono żadnych działów!");
                    }
                }

                //close the database connection
                $connection->close();
                ?>
            </div>
        </div>
        <script>
            clock_update();
        </script>
    </body>
</html>