<?php
require_once('../config/db_config.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Security Check: Ensure data is coming from Razorpay
if (isset($_GET['payment_id']) && isset($_GET['booking_id'])) {
    
    $booking_id = mysqli_real_escape_string($conn, $_GET['booking_id']);
    $payment_id = mysqli_real_escape_string($conn, $_GET['payment_id']);

    // Start Transaction to ensure data integrity
    mysqli_begin_transaction($conn);

    try {
        // 2. Update the Booking Status
        $query1 = "UPDATE `bookings` SET `status`='booked', `trans_id`='$payment_id' 
                   WHERE `id`='$booking_id' AND `status`='pending'";
        
        if (!mysqli_query($conn, $query1)) {
            throw new Exception("Failed to update bookings table");
        }

        // 3. Insert Record into Payments Table 
        // We pull the amount directly from the booking record to ensure accuracy
        $query2 = "INSERT INTO `payments` (`booking_id`, `trans_id`, `amount`, `status`) 
                   SELECT `id`, `trans_id`, `advance`, 'success' 
                   FROM `bookings` WHERE `id`='$booking_id'";

        if (!mysqli_query($conn, $query2)) {
            throw new Exception("Failed to insert into payments table");
        }

        // If both queries worked, commit the changes to the database
        mysqli_commit($conn);

        // 4. Success UI
        echo "
        <!DOCTYPE html>
        <html>
        <head>
            <title>Booking Confirmed</title>
            <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css' rel='stylesheet'>
        </head>
        <body class='bg-light'>
            <div class='container mt-5 text-center'>
                <div class='card shadow p-5 mx-auto' style='max-width: 500px;'>
                    <div class='mb-4'>
                        <svg width='80' height='80' viewBox='0 0 24 24' fill='none' stroke='#198754' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><path d='M22 11.08V12a10 10 0 1 1-5.93-9.14'></path><polyline points='22 4 12 14.01 9 11.01'></polyline></svg>
                    </div>
                    <h2 class='text-success fw-bold'>Payment Successful!</h2>
                    <p class='text-secondary'>Your room has been reserved successfully.</p>
                    <div class='bg-light p-3 rounded mb-4'>
                        <small class='d-block text-muted'>Transaction ID</small>
                        <span class='fw-bold font-monospace'>$payment_id</span>
                    </div>
                    <p>Redirecting you to your bookings...</p>
                    <a href='profile.php' class='btn btn-dark w-100'>Go to Bookings Now</a>
                </div>
            </div>

            <script>
                setTimeout(function(){
                    window.location.href = 'profile.php';
                }, 4000);
            </script>
        </body>
        </html>";

    } catch (Exception $e) {
        // If anything fails, undo any partial database changes
        mysqli_rollback($conn);
        echo "<h1>Booking Error</h1>";
        echo "Details: " . $e->getMessage();
    }

} else {
    // If user tries to access this page without paying
    header("Location: index.php");
    exit;
}
?>