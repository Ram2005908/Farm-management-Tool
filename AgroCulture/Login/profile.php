<?php
    session_start();

    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != 1) {
        $_SESSION['message'] = "You must log in before viewing your profile page!";
        header("location: error.php");
        exit();
    } else {
        $email = isset($_SESSION['Email']) ? $_SESSION['Email'] : '';
        $name = isset($_SESSION['Name']) ? $_SESSION['Name'] : '';
        $user = isset($_SESSION['Username']) ? $_SESSION['Username'] : '';
        $mobile = isset($_SESSION['Mobile']) ? $_SESSION['Mobile'] : '';
        $active = isset($_SESSION['Active']) ? $_SESSION['Active'] : 0;
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>AgroCulture</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="../bootstrap/js/bootstrap.min.js"></script>
    <script src="../js/jquery.min.js"></script>
    <script src="../js/skel.min.js"></script>
    <script src="../js/skel-layers.min.js"></script>
    <script src="../js/init.js"></script>
    <link rel="stylesheet" href="../css/skel.css" />
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" href="../css/style-xlarge.css" />
</head>

<body>
    <?php require 'menu.php'; ?>

    <section id="banner" class="wrapper">
        <div class="container">
            <header class="major">
                <h2>Welcome</h2>
            </header>
            <p>
                <?php
                    if (isset($_SESSION['message'])) {
                        echo $_SESSION['message'];
                        unset($_SESSION['message']);
                    }
                ?>
            </p>

            <?php if (!$active): ?>
                <div>
                    
                </div>
            <?php endif; ?>

            <h2><?php echo htmlspecialchars($name); ?></h2>
            

            <?php if (isset($_SESSION['Category']) && $_SESSION['Category'] == 1): ?>
                <div class="row uniform">
                    <div class="6u 12u$(xsmall)">
                        <a href="../profileView.php" class="button special">My Profile</a>
                    </div>
                    <div class="6u 12u$(xsmall)">
                        <a href="logout.php" class="button special">LOG OUT</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="row uniform">
                    <div class="6u 12u$(xsmall)">
                        <a href="../market.php" class="button special">Digital Market</a>
                    </div>
                    <div class="6u 12u$(xsmall)">
                        <a href="logout.php" class="button special">LOG OUT</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>
</body>
</html>
