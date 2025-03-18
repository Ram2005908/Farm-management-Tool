<?php
session_start();
require 'db.php';

if (!isset($_SESSION['id'])) {
    die("Error: Buyer is not logged in.");
}

$pid = $_GET['pid'] ?? null;

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $name = trim($_POST['name'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $mobile = trim($_POST['mobile'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pincode = trim($_POST['pincode'] ?? '');
    $addr = trim($_POST['addr'] ?? '');
    $bid = $_SESSION['id'];
    $payment_method = $_POST['payment_method'] ?? 'Cash';
    $upi_id = trim($_POST['upi_id'] ?? null);

    // Validation
    if (!$name || !$city || !$mobile || !$email || !$pincode || !$addr) {
        die("Error: All fields are required!");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Error: Invalid email format!");
    }

    if (!preg_match('/^\d{10}$/', $mobile)) {
        die("Error: Mobile number must be 10 digits!");
    }

    if (!preg_match('/^\d{6}$/', $pincode)) {
        die("Error: Pincode must be 6 digits!");
    }

    // Validate Buyer
    $buyerCheck = $conn->prepare("SELECT bid FROM buyer WHERE bid = ?");
    $buyerCheck->bind_param("i", $bid);
    $buyerCheck->execute();
    $result = $buyerCheck->get_result();
    
    if ($result->num_rows == 0) {
        die("Error: Buyer ID ($bid) does not exist.");
    }

    // Insert into transaction table
    $stmt = $conn->prepare("INSERT INTO transaction (bid, pid, name, city, mobile, email, pincode, addr, payment_method, upi_id) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iissssssss", $bid, $pid, $name, $city, $mobile, $email, $pincode, $addr, $payment_method, $upi_id);

    if (!$stmt->execute()) {
        die("Error in Transaction: " . $stmt->error);
    }

    $_SESSION['message'] = "Order Successfully placed! <br /> Thanks for shopping with us!";
    header('Location: Login/success.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Buy Now - AgroCulture</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background: linear-gradient(rgb(26, 176, 56),rgb(37, 162, 252));
            color: white;
        }
        
        .container {
            max-width: 550px;
            margin-top: 50px; 
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            padding: 20px;
            background: white;
            color: black;
        }
        .btn-buy {
            background-color: #28a745;
            color: white;
            font-size: 18px;
            font-weight: bold;
            transition: 0.3s;
        }
        .btn-buy:hover {
            background-color: #218838;
        }
        .error {
            color: red;
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <h2 class="text-center">Transaction Details</h2>
        <hr>
        <form id="orderForm" method="post" action="buyNow.php?pid=<?= $pid; ?>">
            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" id="name" class="form-control" required>
                <div id="nameError" class="error"></div>
            </div>
            <div class="mb-3">
                <label class="form-label">City</label>
                <input type="text" name="city" id="city" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Mobile Number</label>
                <input type="text" name="mobile" id="mobile" class="form-control" required>
                <div id="mobileError" class="error"></div>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control" required>
                <div id="emailError" class="error"></div>
            </div>
            <div class="mb-3">
                <label class="form-label">Pincode</label>
                <input type="text" name="pincode" id="pincode" class="form-control" required>
                <div id="pincodeError" class="error"></div>
            </div>
            <div class="mb-3">
                <label class="form-label">Address</label>
                <textarea name="addr" id="addr" class="form-control" required></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Payment Method</label>
                <select class="form-select" name="payment_method" id="payment_method">
                    <option value="Cash">Cash on Delivery</option>
                    <option value="Online">Online Payment</option>
                </select>
            </div>
            <div id="online-payment-details" class="mb-3" style="display:none;">
                <label class="form-label">UPI ID</label>
                <input type="text" name="upi_id" class="form-control" placeholder="Enter Your UPI ID">
                <img src="js/images/qr.jpeg" class="img-fluid mt-2" width="200" style="display:none;" id="qr-code">
                <button type="button" class="btn btn-primary mt-2" onclick="showQRCode()">Show QR Code</button>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-buy">Confirm Order</button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('payment_method').addEventListener('change', function() {
    let paymentDetails = document.getElementById('online-payment-details');
    if (this.value === 'Online') {
        paymentDetails.style.display = 'block';
    } else {
        paymentDetails.style.display = 'none';
        document.getElementById('qr-code').style.display = 'none';
    }
});

function showQRCode() {
    document.getElementById('qr-code').style.display = 'block';
}

document.getElementById('orderForm').addEventListener('submit', function(e) {
    let mobile = document.getElementById('mobile').value;
    let email = document.getElementById('email').value;
    let pincode = document.getElementById('pincode').value;

    if (!/^\d{10}$/.test(mobile)) {
        document.getElementById('mobileError').textContent = "Mobile number must be 10 digits!";
        e.preventDefault();
    }
    if (!/^\d{6}$/.test(pincode)) {
        document.getElementById('pincodeError').textContent = "Pincode must be 6 digits!";
        e.preventDefault();
    }
});
</script>

</body>
</html>
