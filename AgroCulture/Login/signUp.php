<?php 
session_start();
require '../db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = dataFilter($_POST['name']);
    $mobile = dataFilter($_POST['mobile']);
    $user = dataFilter($_POST['uname']);
    $email = dataFilter($_POST['email']);
    $pass = password_hash(dataFilter($_POST['pass']), PASSWORD_BCRYPT);
    $hash = md5(rand(0, 1000));
    $category = dataFilter($_POST['category']);
    $addr = dataFilter($_POST['addr']);

    // Validate Mobile Number
    if (strlen($mobile) != 10) {
        $_SESSION['message'] = "Invalid Mobile Number!";
        header("location: error.php");
        exit();
    }

    if ($category == 1) { // Farmer Registration
        $sql = "SELECT * FROM farmer WHERE femail='$email'";
        $result = mysqli_query($conn, $sql);

        if ($result->num_rows > 0) {
            $_SESSION['message'] = "User with this email already exists!";
            header("location: error.php");
            exit();
        } else {
            $sql = "INSERT INTO farmer (fname, fusername, fpassword, fhash, fmobile, femail, faddress)
                    VALUES ('$name', '$user', '$pass', '$hash', '$mobile', '$email', '$addr')";
            
            if (mysqli_query($conn, $sql)) {
                $_SESSION['message'] = "Registration successful! Please log in.";
                $_SESSION['category'] = 1; // Store category to recognize user type
                header("location: success.php"); // Redirect to success page
                exit();
            }
        }
    } 
    else { // Buyer Registration
        $sql = "SELECT * FROM buyer WHERE bemail='$email'";
        $result = mysqli_query($conn, $sql);

        if ($result->num_rows > 0) {
            $_SESSION['message'] = "User with this email already exists!";
            header("location: error.php");
            exit();
        } else {
            $sql = "INSERT INTO buyer (bname, busername, bpassword, bhash, bmobile, bemail, baddress)
                    VALUES ('$name', '$user', '$pass', '$hash', '$mobile', '$email', '$addr')";
            
            if (mysqli_query($conn, $sql)) {
                $_SESSION['message'] = "Registration successful! Please log in.";
                $_SESSION['category'] = 2; // Store category to recognize user type
                header("location: success.php"); // Redirect to success page
                exit();
            }
        }
    }

    $_SESSION['message'] = "Registration failed!";
    header("location: error.php");
    exit();
}

function dataFilter($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}
?>
