<?php
    session_start();
    require 'db.php';

    // Ensure user is logged in
    if(!isset($_SESSION['logged_in']) OR $_SESSION['logged_in'] == 0) {
        $_SESSION['message'] = "You need to first login to access this page !!!";
        header("Location: Login/error.php");
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_SESSION['logged_in']) AND $_SESSION['logged_in'] == 1) {
        // Handle like button click
        if (isset($_POST['like'])) {
            $blogId = $_POST['blogId']; // Get the blog ID from the form

            $likeCheck = "isLiked" . $blogId;
            if (!isset($_SESSION[$likeCheck]) OR $_SESSION[$likeCheck] == 0) {
                // Insert like into likedata table
                $id = $_SESSION['id'];
                $sql = "SELECT * FROM likedata WHERE blogId = '$blogId' AND blogUserId = '$id'";
                $result = mysqli_query($conn, $sql);
                $num_rows = mysqli_num_rows($result);

                if ($num_rows == 0) {
                    // Insert like data into the table
                    $sql = "INSERT INTO likedata (blogId, blogUserId) VALUES('$blogId', '$id')";
                    $result = mysqli_query($conn, $sql);

                    // Update likes count in blogdata table
                    $sql = "UPDATE blogdata SET likes = likes + 1 WHERE blogId = '$blogId'";
                    $result = mysqli_query($conn, $sql);

                    // Set session variable to track that the user has liked this post
                    $_SESSION[$likeCheck] = 1;
                }
            }
        }

        // Handle comment submission
        if (isset($_POST['comment']) AND $_POST['comment'] != "") {
            $sql = "SELECT * FROM blogdata ORDER BY blogId DESC";
            $result = mysqli_query($conn, $sql);

            while ($row = $result->fetch_array()) {
                $check = "submit" . $row['blogId'];
                if (isset($_POST[$check])) {
                    $blogId = $row['blogId'];
                    break;
                }
            }

            $comment = dataFilter($_POST['comment']);
            if (isset($_SESSION['logged_in']) AND $_SESSION['logged_in'] == 1) {
                $commentUser = $_SESSION['Username'];
                $pic = $_SESSION['picName'];
            } else {
                $commentUser = "Anonymous";
                $pic = "profile0.png";
            }
            if (isset($blogId)) {
                $sql = "INSERT INTO blogfeedback (blogId, comment, commentUser, commentPic)
                        VALUES ('$blogId' ,'$comment', '$commentUser', '$pic');";
                $result = mysqli_query($conn, $sql);
            }
        }
    }

    function dataFilter($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $sql = "SELECT * FROM blogdata ORDER BY blogId DESC";
    $result = mysqli_query($conn, $sql);

    function formatDate($date) {
        return date('g:i a', strtotime($date));
    }
?>

<!DOCTYPE HTML>
<html>
    <head>
        <title>AgroCulture : Blogs</title>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script src="bootstrap/js/bootstrap.min.js"></script>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <meta name="description" content="" />
        <meta name="keywords" content="" />
        <link rel="stylesheet" href="css/skel.css" />
        <link rel="stylesheet" href="css/style.css" />
        <link rel="stylesheet" href="css/style-xlarge.css" />
        <link rel="stylesheet" href="Blog/commentBox.css" />
    </head>
    <body class="subpage">

        <?php require 'menu.php'; ?>

        <section id="main" class="wrapper">
            <div class="inner">
                <div class="container" style="width: 70%">
                    <div class="row uniform">
                        <div class="9u 12u$(small)"></div>
                        <div class="3u 12u$(small)">
                            <a href="blogWrite.php" class="button special fit"><span class="glyphicon glyphicon-pencil"></span> Write a Blog</a>
                        </div>
                    </div>
                    <br />
                    <?php
                        while ($row = $result->fetch_array()) :
                            $id = $row['blogId'];
                            $sql = "SELECT * FROM blogfeedback WHERE blogId = '$id'";
                            $result1 = mysqli_query($conn, $sql);
                            $numComment = mysqli_num_rows($result1);
                    ?>
                    <div class="box">
                        <h2><?= $row['blogTitle']; ?></h2>
                        <blockquote>
                            <?= $row['blogContent']; ?>
                            <p>--- <?= $row['blogUser']; ?></p>
                            <p><?= $row['blogTime']; ?></p>
                        </blockquote>

                        <form method="post" action="blogView.php">
                            <div class="row">
                                <div class="6u 12u$(xsmall)">
                                    <button class="button special small" name="like" value="like" type="submit">
                                        <span class="glyphicon glyphicon-thumbs-up"></span> Like
                                    </button>
                                    <span><?= $row['likes'] ?></span>
                                    <input type="hidden" name="blogId" value="<?= $id ?>" />
                                </div>
                                <div class="6u 12u$(xsmall)">
                                    <span class="glyphicon glyphicon-pencil"></span> Comments : <?= $numComment ?>
                                </div>
                                <div class="12u$">
                                    <br>
                                    <textarea name="comment" id="comment" rows="1" placeholder="Write a Comment!"></textarea>
                                </div>
                                <div class="12u$">
                                    <center>
                                    <br>
                                    <input type="submit" name="<?php echo 'submit'.$id; ?>" class="button special small" value="Submit"/>
                                    </center>
                                </div>
                            </div>
                        </form>

                        <?php
                            if ($result1) :
                                while ($row1 = $result1->fetch_array()) :
                        ?>
                        <div class="con darker">
                            <img src="<?php echo 'images/profileImages/'.$row1['commentPic']?>" alt="Avatar"><span><em><?= $row1['commentUser']; ?></em></span>
                            <br>
                            <?= $row1['comment']; ?>
                            <span class="time-right"><?= formatDate($row1['commentTime']); ?></span>
                        </div>
                        <?php endwhile; ?>
                        <?php endif; ?>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </section>

    </body>
</html>
