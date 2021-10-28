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
$id = $_GET['id'];

// query that selects selected post parent section name from database
$query_get_section_name = "SELECT name FROM sections WHERE IDsection=(SELECT SectionID FROM posts WHERE IDpost=$id)";

if($result_section_name = $connection->query($query_get_section_name))
{
    // if there are any rows in the result variable then proceed
    if($result_section_name->num_rows > 0)
    {
        // fetch all columns in the row to an assotiative table named $row
        $row = $result_section_name->fetch_assoc();
        
        // set the post topic to a variable
        $name = $row['name'];

        // dissolve the result array
        $result_section_name->free_result();
    }
    else
    {
        // if the post id will be incorrect then write error msg to variable
        $name = "BŁĄD! Nie można znaleźć tematu postu!";
    }
}
// dissolve the $row table
unset($row);

// query that selects selected post info from database
$query_get_post_info = "SELECT * FROM posts WHERE IDpost = $id";

if($result_post_info = $connection->query($query_get_post_info))
{
    // if there are any rows in the result variable then proceed
    if($result_post_info->num_rows > 0)
    {
        // fetch all columns in the row to an assotiative table named $row
        $row = $result_post_info->fetch_assoc();
        
        // set the post topic to a variable
        $topic = $row['topic'];
        $section_id = $row['SectionID'];

        // dissolve the result array
        $result_post_info->free_result();
    }
    else
    {
        // if the post id will be incorrect then write error msg to variable
        $topic = "BŁĄD! Nie można znaleźć tematu postu!";
    }
}
// dissolve the $row table
unset($row);


class Comment
{
    private $id;
    private $author_nick;
    private $author_photo_link;
    private $author_rank;
    private $author_last_seen_date;
    private $comment_date;
    private $comment_content;
    private $topic;

    public function __construct($id, $author_nick, $author_photo_link, $author_rank, $author_last_seen_date, $comment_date, $comment_content, $topic)
    {
        $this->id = $id;
        $this->author_nick = $author_nick;
        $this->author_photo_link = $author_photo_link;
        $this->author_rank = $author_rank;
        $this->author_last_seen_date = $author_last_seen_date;
        $this->comment_date = $comment_date;
        $this->comment_content = $comment_content;
        $this->topic = $topic;
    }

    public function print()
    {
        if($this->author_rank == 'admin')
        {
            $this->author_rank = '<span style="color: red; font-weight: 900">admin</span>';
        }
        else if($this->author_rank == 'moderator')
        {
            $this->author_rank = '<span style="color: blue; font-weight: 600">moderator</span>';
        }

        echo <<<POST
            <div class="user_borderless">
                <p class="author_nick">
                    $this->author_nick
                </p>
                <img src="$this->author_photo_link" class="user_photo" alt="photo of $this->author_nick">
                <p class="author_rank">
                    Ranga: $this->author_rank
                </p>
                <p class="author_last_seen">
                    Ostatnio widziano: $this->author_last_seen_date
                </p>
            </div>
            <div class="comment_info">
                <span>
                #$this->id &nbsp;<b>|</b>&nbsp;
                $this->comment_date &nbsp;<b>|</b> &nbsp;
                $this->topic
            </span>
            <hr>
            <br>
            <div class="comment_msg">
                $this->comment_content
            </div>
            </div>
        POST;
    }
}

?>

<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="utf-8">
        <title><?php echo $topic; ?> - qForum</title>
        <link rel="stylesheet" href="style.css" type="text/css">
        <link rel="stylesheet" href="panel.css" type="text/css">
        <link rel="stylesheet" href="section.css" type="text/css">
        <link rel="stylesheet" href="post.css" type="text/css">
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
                <a href="section.php?id=<?php echo $section_id; ?>">
                    <?php echo $name; ?>
                </a>
                <span> -> </span>
                <a href="post.php?id=<?php echo $id; ?>">
                    <?php echo $topic; ?>
                </a>
            </div>
            <br>
            <div id="post_topic">
                <?php echo $topic ?>
            </div>

            <?php
            $query_get_comments = "SELECT * FROM comments, users WHERE PostID = $id AND AuthorID = IDuser";
            if($result_get_comments = $connection->query($query_get_comments))
            {
                $comments = array();
                if($result_get_comments->num_rows > 0)
                {
                    $post_index = 1;
                    while($row = $result_get_comments->fetch_assoc())
                    {
                        $comment = new Comment($post_index++, $row['login'], $row['photo_link'], $row['rank'], $row['last_seen_date'], $row['comment_date'], $row['content'], $topic);
                        array_push($comments, $comment);
                    }

                    for($i=0; $i<count($comments); $i++)
                    {
                        echo '<div class="table_border">';
                        $comments[$i]->print();
                        echo '</div>';
                    }
                }
                else{
                    echo "Brak komentarzy w tym poście?! DZIWNE.. :O";
                }
            }
            ?>

        </div>
        <script>
            clock_update();
        </script>
    </body>
</html>