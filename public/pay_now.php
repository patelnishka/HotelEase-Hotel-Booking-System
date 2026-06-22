<?php
require_once('../config/db_config.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Security: Redirect if not logged in or if form wasn't submitted
if(!isset($_SESSION['u_id']) || !isset($_POST['pay_now'])){
    echo "<script>window.location.href='index.php';</script>";
    exit;
}

// 1. Capture Data from POST
$u_id = $_SESSION['u_id'];
$room_id = mysqli_real_escape_string($conn, $_POST['room_id']);
$checkin = mysqli_real_escape_string($conn, $_POST['checkin']);
$checkout = mysqli_real_escape_string($conn, $_POST['checkout']);

// 2. Fetch Room Price & User Details
$room_res = mysqli_query($conn, "SELECT * FROM `rooms` WHERE `id`='$room_id' LIMIT 1");
$room_data = mysqli_fetch_assoc($room_res);

// 3. Re-calculate Amount (Server-side is safer)
$checkin_date = strtotime($checkin);
$checkout_date = strtotime($checkout);
$days = ($checkout_date - $checkin_date) / 86400;

$total_pay = $days * $room_data['price'];
$advance = $total_pay * 0.10; // 10% Advance

$order_id = 'ORD_' . $_SESSION['u_id'] . random_int(11111, 99999);

// 4. Insert Pending Booking into Database
$query = "INSERT INTO `bookings` (`u_id`, `room_id`, `check_in`, `check_out`, `total_pay`, `advance`, `order_id`, `status`) 
          VALUES ('$u_id', '$room_id', '$checkin', '$checkout', '$total_pay', '$advance', '$order_id', 'pending')";

if(mysqli_query($conn, $query)){
    $booking_id = mysqli_insert_id($conn);
} else {
    echo "Database Error: " . mysqli_error($conn);
    exit;
}

// 5. Razorpay Key
$key_id = "rzp_test_S6CSlO0jSV5JgO";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Processing Payment...</title>
    <style>
        body { background: #f8f9fa; display: flex; justify-content: center; align-items: center; height: 100vh; font-family: sans-serif; }
        .loader { text-align: center; }
    </style>
</head>
<body>

    <div class="loader">
        <h2>Please wait...</h2>
        <p>Connecting to secure payment gateway.</p>
    </div>

    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
        // Clean values for JS
        var amount_paise = Math.round(<?php echo $advance; ?> * 100);
        var u_name = "<?php echo isset($_SESSION['u_name']) ? $_SESSION['u_name'] : 'Guest'; ?>";
        var u_email = "<?php echo isset($_SESSION['u_email']) ? $_SESSION['u_email'] : ''; ?>";
        var u_phone = "<?php echo isset($_SESSION['u_phone']) ? $_SESSION['u_phone'] : ''; ?>";

        var options = {
            "key": "<?php echo $key_id; ?>",
            "amount": amount_paise,
            "currency": "INR",
            "name": "Hotel Ease",
            "description": "10% Advance Booking Fee",
            "handler": function (response){
                // Success! Redirect to verification page
                window.location.href = "verify_payment.php?booking_id=<?php echo $booking_id; ?>&payment_id=" + response.razorpay_payment_id;
            },
            "prefill": {
                "name": u_name,
                "email": u_email,
                "contact": u_phone
            },
            "theme": { "color": "#212529" },
            "modal": {
                "ondismiss": function(){
                    // If user cancels, take them back to the room page
                    window.location.href = "confirm_booking.php?id=<?php echo $room_id; ?>";
                }
            }
        };

        var rzp1 = new Razorpay(options);
        
        window.onload = function(){
            rzp1.open();
        };
    </script>
</body>
</html>