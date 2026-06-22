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
    <title>Admin Panel - View Payments</title>
    <?php require('includes/links.php'); ?>
    <style>
        #main-content { margin-left: 250px; transition: all 0.3s; }
        @media screen and (max-width: 991px) { #main-content { margin-left: 0; } }
        .table > :not(caption) > * > * { vertical-align: middle; }
    </style>
</head>
<body class="bg-light">
    <?php require('includes/admin_header.php'); ?>

    <div class="container-fluid">
        <div class="row">
            <?php require('includes/admin_sidebar.php'); ?>

            <div class="p-4" id="main-content">
                <h3 class="mb-4 text-uppercase fw-bold"><i class="bi bi-cash-stack"></i> Payment Transactions</h3>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5 class="card-title m-0">Transaction Logs</h5>
                            <div class="w-25">
                                <input type="text" oninput="get_payments(this.value)" class="form-control shadow-none" placeholder="Search Order ID or Name...">
                            </div>
                        </div>

                        <div class="table-responsive" style="height: 500px; overflow-y: scroll;">
                            <table class="table table-hover border text-center mb-0">
                                <thead>
                                    <tr class="bg-dark text-light sticky-top">
                                        <th scope="col">#</th>
                                        <th scope="col">Order ID</th>
                                        <th scope="col">Guest Name</th>
                                        <th scope="col">Total Amount</th>
                                        <th scope="col">10% Advance</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="payments-data"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require('includes/scripts.php'); ?>

    <script>
        // Fetches the table data
        function get_payments(search='') {
            let xhr = new XMLHttpRequest();
            xhr.open("POST", "ajax/view_payments.php", true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                document.getElementById('payments-data').innerHTML = this.responseText;
            }
            xhr.send('get_payments=1&search=' + search);
        }

        // UPDATED: Function to handle payment acceptance
        function accept_payment(id) {
            if(confirm("Are you sure you want to accept this payment?")) {
                let xhr = new XMLHttpRequest();
                xhr.open("POST", "ajax/view_payments.php", true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

                xhr.onload = function() {
                    if(this.responseText == 1) {
                        alert('Payment updated successfully!');
                        get_payments(); // Refresh table data automatically
                    } else {
                        alert('Error: Server could not update status.');
                    }
                }
                xhr.send('accept_payment=1&booking_id=' + id);
            }
        }

        window.onload = function() { get_payments(); }
    </script>
</body>
</html>