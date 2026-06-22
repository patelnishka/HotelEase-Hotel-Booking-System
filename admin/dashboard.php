<?php 
    require('../config/db_config.php');
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if(!(isset($_SESSION['adminLogin']) && $_SESSION['adminLogin'] == true)){
        header("Location: login.php"); exit;
    }

    // 1. Fetching Feedback & Queries
    $unread_queries = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) AS `count` FROM `contact_queries` WHERE `seen`=0"));
    $unread_reviews = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(f_id) AS `count` FROM `feedback`")); 
    
    // 2. Fetching User Stats
    $current_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT 
        COUNT(u_id) AS `total`,
        COUNT(CASE WHEN `status` = 1 THEN 1 END) AS `active`,
        COUNT(CASE WHEN `status` = 0 THEN 1 END) AS `inactive`
        FROM `users`"));

    // 3. New Bookings Count (Booked status but not arrived)
    $new_bookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) AS `count` FROM `bookings` WHERE `status`='booked' AND `arrival`=0"));

    // 4. Booking Analytics (Total, Active, and Cancelled)
    // Note: Ensure your bookings table has a 'total_pay' column for revenue calculation
    $total_bookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) AS `count`, SUM(total_pay) AS `amt` FROM `bookings` WHERE `status`='booked'"));
    $active_bookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) AS `count`, SUM(total_pay) AS `amt` FROM `bookings` WHERE `status`='booked' AND `arrival`=1"));
    $cancelled_bookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) AS `count`, SUM(total_pay) AS `amt` FROM `bookings` WHERE `status`='cancelled'"));

    $booking_stats = [
        'total' => $total_bookings['count'] ?? 0, 
        'total_amt' => $total_bookings['amt'] ?? 0,
        'active' => $active_bookings['count'] ?? 0, 
        'active_amt' => $active_bookings['amt'] ?? 0,
        'cancelled' => $cancelled_bookings['count'] ?? 0, 
        'cancelled_amt' => $cancelled_bookings['amt'] ?? 0
    ];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .modal { z-index: 9999 !important; background: rgba(0,0,0,0.5); }
        .modal-backdrop { display: none !important; }
        body.modal-open { overflow: hidden; }
        #main-content { overflow: visible !important; }

        .stat-card {
            padding: 20px;
            border-radius: 10px;
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-card h6 { font-weight: bold; text-transform: uppercase; font-size: 13px; }
        .stat-card h2 { font-weight: 700; margin: 10px 0; }
    </style>
</head>
<body class="bg-light">

    <?php include('includes/admin_header.php'); ?>
    <?php include('includes/admin_sidebar.php'); ?>

    <div id="main-content">
        <div class="container-fluid">
            
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h3 class="fw-bold m-0">DASHBOARD OVERVIEW</h3>
            </div>

            <div class="row g-4 mb-5">
                <div class="col-md-3">
                    <a href="booking.php" class="text-decoration-none">
                        <div class="card stat-card border-start border-4 border-success text-success bg-white">
                            <h6>New Bookings</h6>
                            <h2><?php echo $new_bookings['count']; ?></h2>
                        </div>
                    </a>
                </div>
                
                <div class="col-md-3">
                    <a href="contact.php" class="text-decoration-none">
                        <div class="card stat-card border-start border-4 border-info text-info bg-white">
                            <h6>User Queries</h6>
                            <h2><?php echo $unread_queries['count']; ?></h2>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="feedback.php" class="text-decoration-none">
                        <div class="card stat-card border-start border-4 border-primary text-primary bg-white">
                            <h6>Ratings & Reviews</h6>
                            <h2><?php echo $unread_reviews['count']; ?></h2>
                        </div>
                    </a>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-12"><h5 class="fw-bold text-secondary">Booking Analytics</h5></div>
            </div>
            <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <div class="card stat-card text-primary bg-white">
                        <h6>Total Bookings</h6>
                        <h2><?php echo $booking_stats['total']; ?></h2>
                        <span class="text-muted">Revenue: ₹ <?php echo number_format($booking_stats['total_amt']); ?></span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card text-success bg-white">
                        <h6>Active Bookings</h6>
                        <h2><?php echo $booking_stats['active']; ?></h2>
                        <span class="text-muted">Revenue: ₹ <?php echo number_format($booking_stats['active_amt']); ?></span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card text-danger bg-white">
                        <h6>Cancelled Bookings</h6>
                        <h2><?php echo $booking_stats['cancelled']; ?></h2>
                        <span class="text-muted">Loss: ₹ <?php echo number_format($booking_stats['cancelled_amt']); ?></span>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-12"><h5 class="fw-bold text-secondary">User Statistics</h5></div>
            </div>
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="card stat-card text-dark bg-white">
                        <h6>Total Users</h6>
                        <h2><?php echo $current_users['total']; ?></h2>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card text-success bg-white">
                        <h6>Active Users</h6>
                        <h2><?php echo $current_users['active']; ?></h2>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>