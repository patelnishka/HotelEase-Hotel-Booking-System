<?php
    require('../config/db_config.php');
    session_start();
    if(!(isset($_SESSION['adminLogin']) && $_SESSION['adminLogin']==true)){
        header("Location: index.php");
        exit;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Invoices</title>
    <?php require('includes/links.php'); ?>
    <style>
        #main-content { margin-left: 250px; transition: all 0.3s; }
        @media screen and (max-width: 991px) { #main-content { margin-left: 0; } }
    </style>
</head>
<body class="bg-light">
    <?php require('includes/admin_header.php'); ?>

    <div class="container-fluid">
        <div class="row">
            <?php require('includes/admin_sidebar.php'); ?>

            <div class="p-4" id="main-content">
                <h3 class="mb-4 text-uppercase fw-bold"><i class="bi bi-receipt"></i> Invoice Archive</h3>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover border align-middle text-center">
                                <thead>
                                    <tr class="bg-dark text-light">
                                        <th>#</th>
                                        <th>Order ID</th>
                                        <th>Guest Name</th>
                                        <th>Room</th>
                                        <th>Amount</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        // Only show bookings that are confirmed or finalized
                                        $q = "SELECT b.*, u.u_name, r.name AS r_name 
                                              FROM `bookings` b 
                                              INNER JOIN `users` u ON b.u_id = u.u_id 
                                              INNER JOIN `rooms` r ON b.room_id = r.id 
                                              WHERE b.status != 'pending' 
                                              ORDER BY b.id DESC";
                                        
                                        $res = mysqli_query($conn, $q);
                                        $i = 1;

                                        while($data = mysqli_fetch_assoc($res)){
                                            echo "<tr>
                                                <td>$i</td>
                                                <td><span class='badge bg-light text-dark border'>$data[order_id]</span></td>
                                                <td><b>$data[u_name]</b></td>
                                                <td>$data[r_name]</td>
                                                <td class='text-success fw-bold'>₹$data[total_pay]</td>
                                                <td>
                                                    <a href='../public/generate_pdf.php?gen_pdf=1&id=$data[id]' target='_blank' class='btn btn-outline-primary btn-sm shadow-none'>
            <i class='bi bi-file-earmark-pdf'></i> Download PDF
        </a>
                                                </td>
                                            </tr>";
                                            $i++;
                                        }

                                        if(mysqli_num_rows($res) == 0){
                                            echo "<tr><td colspan='6' class='p-4 text-secondary'>No confirmed invoices found.</td></tr>";
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require('includes/scripts.php'); ?>
</body>
</html>