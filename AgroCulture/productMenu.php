<?php
    session_start();
    require 'db.php';

    function dataFilter($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AgroCulture</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="login.css"/>
    <script src="js/jquery.min.js"></script>
    <script src="js/skel.min.js"></script>
    <script src="js/skel-layers.min.js"></script>
    <script src="js/init.js"></script>
    <noscript>
        <link rel="stylesheet" href="css/skel.css" />
        <link rel="stylesheet" href="css/style.css" />
        <link rel="stylesheet" href="css/style-xlarge.css" />
    </noscript>
</head>
<body>

<?php require 'menu.php'; ?>

<section id="main" class="wrapper style1 align-center">
    <div class="container">
        <h2>Welcome to digital market</h2>

        <?php if (isset($_GET['n']) && $_GET['n'] == 1): ?>
            <h3>Select Filter</h3>
            <form method="GET" action="productMenu.php">
                <input type="hidden" value="1" name="n" />
                <center>
                    <div class="row">
                        <div class="col-sm-4"></div>
                        <div class="col-sm-2">
                            <div class="select-wrapper" style="width: auto">
                                <select name="type" id="type" required style="background-color:white;color: black;">
                                    <option value="all">List All</option>
                                    <option value="fruit">Fruit</option>
                                    <option value="vegetable">Vegetable</option>
                                    <option value="grain">Grains</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <input class="button special" type="submit" style="background-color: blue; color: white;" value="Go!" />
                        </div>
                        <div class="col-sm-4"></div>
                    </div>
                </center>
            </form>
        <?php endif; ?>

        <section id="two" class="wrapper style2 align-center">
            <div class="container">
                <?php
                    $type = isset($_GET['type']) ? mysqli_real_escape_string($conn, $_GET['type']) : 'all';

                    if ($type == "all") {
                        $sql = "SELECT * FROM fproduct";
                    } elseif ($type == "fruit") {
                        $sql = "SELECT * FROM fproduct WHERE pcat = 'Fruit'";
                    } elseif ($type == "vegetable") {
                        $sql = "SELECT * FROM fproduct WHERE pcat = 'Vegetable'";
                    } elseif ($type == "grain") {
                        $sql = "SELECT * FROM fproduct WHERE pcat = 'Grains'";
                    } else {
                        $sql = "SELECT * FROM fproduct";
                    }

                    $result = mysqli_query($conn, $sql);
                    if (!$result) {
                        die("Error fetching products: " . mysqli_error($conn));
                    }
                ?>
                <div class="row">
                    <?php while ($row = $result->fetch_array()): 
                        $picDestination = "images/productImages/" . $row['pimage'];
                        if (!file_exists($picDestination) || empty($row['pimage'])) {
                            $picDestination = "images/default.png";
                        }
                    ?>
                        <div class="col-md-4">
                            <section>
                                <strong><h2 class="title" style="color:black;"><?php echo $row['product']; ?></h2></strong>
                                <a href="review.php?pid=<?php echo $row['pid']; ?>">
                                    <img class="image fit" src="<?php echo $picDestination; ?>" height="220px;" />
                                </a>
                                <div>
                                    <blockquote>
                                        <?php echo "Type: " . $row['pcat']; ?><br>
                                        <?php echo "Price: " . $row['price'] . " /-"; ?>
                                    </blockquote>
                                </div>
                            </section>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </section>
    </div>
</section>
</body>
</html>
