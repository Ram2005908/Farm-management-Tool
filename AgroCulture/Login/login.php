<?php
session_start();
require '../db.php';

// Function to filter user input
function dataFilter($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Fetching user inputs
$user = dataFilter($_POST['uname']);
$pass = $_POST['pass'];
$category = dataFilter($_POST['category']);

if ($category == 1) {
    // Farmer Login
    $stmt = $conn->prepare("SELECT * FROM farmer WHERE fusername = ?");
    $stmt->bind_param("s", $user);
} else {
    // Buyer Login
    $stmt = $conn->prepare("SELECT * FROM buyer WHERE busername = ?");
    $stmt->bind_param("s", $user);
}

$stmt->execute();
$result = $stmt->get_result();


if ($result->num_rows == 0) {
    $_SESSION['message'] = "Invalid Username or Password!";
    header("location: error.php");
    exit();
}

$User = $result->fetch_assoc();
$stmt->close();

// Verify password
if (!password_verify($pass, $User[$category == 1 ? 'fpassword' : 'bpassword'])) {
    $_SESSION['message'] = "Invalid Username or Password!";
    header("location: error.php");
    exit();
}

// Setting up session variables
$_SESSION['id'] = $User[$category == 1 ? 'fid' : 'bid'];
$_SESSION['Email'] = $User[$category == 1 ? 'femail' : 'bemail'];
$_SESSION['Name'] = $User[$category == 1 ? 'fname' : 'bname'];
$_SESSION['Username'] = $User[$category == 1 ? 'fusername' : 'busername'];
$_SESSION['Mobile'] = $User[$category == 1 ? 'fmobile' : 'bmobile'];
$_SESSION['Addr'] = $User[$category == 1 ? 'faddress' : 'baddress'];
$_SESSION['Active'] = $User[$category == 1 ? 'factive' : 'bactive'];
$_SESSION['logged_in'] = true;
$_SESSION['Category'] = $category;

// Profile Picture Handling
$_SESSION['picStatus'] = $User[$category == 1 ? 'picStatus' : 'picStatus'];
$_SESSION['picExt'] = $User[$category == 1 ? 'picExt' : 'picExt'];

if ($_SESSION['picStatus'] == 0) {
    $_SESSION['picName'] = "profile0.png";
} else {
    $_SESSION['picName'] = "profile" . $_SESSION['id'] . "." . $_SESSION['picExt'];
}

// Redirect to profile
header("location: profile.php");
?>
