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
    <title>Admin Panel - Manage Bookings</title>
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
                <h3 class="mb-4 text-uppercase fw-bold">Manage Bookings</h3>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        
                        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                            <li class="nav-item">
                                <button class="nav-link active shadow-none" id="new-bookings-tab" data-bs-toggle="pill" data-bs-target="#new-bookings" type="button">New Bookings (Paid)</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link shadow-none" id="active-bookings-tab" data-bs-toggle="pill" data-bs-target="#active-bookings" type="button">Active Stays</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link shadow-none" id="cancelled-bookings-tab" data-bs-toggle="pill" data-bs-target="#cancelled-bookings" type="button">Cancelled Bookings</button>
                            </li>
                        </ul>

                        <div class="tab-content">
                            
                            <div class="tab-pane fade show active" id="new-bookings">
                                <div class="table-responsive">
                                    <table class="table table-hover border align-middle text-center" style="min-width: 900px;">
                                        <thead>
                                            <tr class="bg-dark text-light">
                                                <th>#</th><th>Order ID</th><th>Guest Name</th><th>Room Category</th><th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $q = "SELECT b.*, u.u_name, r.name AS r_name, r.id AS room_id 
                                                      FROM bookings b 
                                                      INNER JOIN users u ON b.u_id = u.u_id 
                                                      INNER JOIN rooms r ON b.room_id = r.id 
                                                      WHERE b.status='booked' AND b.arrival=0 
                                                      ORDER BY b.id DESC";
                                                $res = mysqli_query($conn, $q);
                                                $i = 1;
                                                if(mysqli_num_rows($res) > 0) {
                                                    while($data = mysqli_fetch_assoc($res)){
                                                        echo "<tr>
                                                            <td>$i</td>
                                                            <td><span class='badge bg-primary'>$data[order_id]</span></td>
                                                            <td>$data[u_name]</td>
                                                            <td>$data[r_name]</td>
                                                            <td>
                                                                <button onclick='assign_room_modal($data[id], $data[room_id])' class='btn btn-success btn-sm shadow-none mb-1' data-bs-toggle='modal' data-bs-target='#assign-room'>
                                                                    <i class='fas fa-check-circle me-1'></i>Assign Room
                                                                </button>
                                                                <button onclick='cancel_booking($data[id])' class='btn btn-outline-danger btn-sm shadow-none mb-1'>
                                                                    <i class='fas fa-times-circle me-1'></i>Cancel
                                                                </button>
                                                            </td>
                                                        </tr>"; 
                                                        $i++;
                                                    }
                                                } else {
                                                    echo "<tr><td colspan='5'>No new bookings found.</td></tr>";
                                                }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="active-bookings">
                                <div class="table-responsive">
                                    <table class="table table-hover border align-middle text-center">
                                        <thead>
                                            <tr class="bg-dark text-light">
                                                <th>#</th><th>Guest Name</th><th>Room No</th><th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $q = "SELECT b.*, u.u_name FROM bookings b 
                                                      INNER JOIN users u ON b.u_id = u.u_id 
                                                      WHERE b.arrival=1 AND b.status='booked' ORDER BY b.id DESC";
                                                $res = mysqli_query($conn, $q);
                                                $i = 1;
                                                if(mysqli_num_rows($res) > 0) {
                                                    while($data = mysqli_fetch_assoc($res)){
                                                        echo "<tr>
                                                            <td>$i</td>
                                                            <td>$data[u_name]</td>
                                                            <td><span class='badge bg-info text-dark'>$data[room_no]</span></td>
                                                            <td>
                                                                <button onclick='checkout_booking($data[id], \"$data[room_no]\")' class='btn btn-danger btn-sm shadow-none'>Checkout</button>
                                                            </td>
                                                        </tr>"; 
                                                        $i++;
                                                    }
                                                } else {
                                                    echo "<tr><td colspan='4'>No active stays found.</td></tr>";
                                                }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="cancelled-bookings">
                                <div class="table-responsive">
                                    <table class="table table-hover border align-middle text-center" style="min-width: 900px;">
                                        <thead>
                                            <tr class="bg-dark text-light">
                                                <th>#</th><th>Order ID</th><th>Guest Name</th><th>Room Category</th><th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $q = "SELECT b.*, u.u_name, r.name AS r_name 
                                                      FROM bookings b 
                                                      INNER JOIN users u ON b.u_id = u.u_id 
                                                      INNER JOIN rooms r ON b.room_id = r.id 
                                                      WHERE b.status='cancelled' 
                                                      ORDER BY b.id DESC";
                                                $res = mysqli_query($conn, $q);
                                                $i = 1;
                                                if(mysqli_num_rows($res) > 0) {
                                                    while($data = mysqli_fetch_assoc($res)){
                                                        echo "<tr>
                                                            <td>$i</td>
                                                            <td><span class='badge bg-primary'>$data[order_id]</span></td>
                                                            <td>$data[u_name]</td>
                                                            <td>$data[r_name]</td>
                                                            <td><span class='badge bg-danger'>Cancelled</span></td>
                                                        </tr>"; 
                                                        $i++;
                                                    }
                                                } else {
                                                    echo "<tr><td colspan='5'>No cancelled bookings found.</td></tr>";
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
        </div>
    </div>

    <div class="modal fade" id="assign-room" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form id="assign_room_form">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Finalize Assignment</h5>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Select Room Number</label>
                            <select name="room_no" class="form-select shadow-none" required></select>
                        </div>
                        <input type="hidden" name="booking_id">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn text-secondary" data-bs-dismiss="modal">CLOSE</button>
                        <button type="submit" class="btn btn-success text-white">COMPLETE CHECK-IN</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php require('includes/scripts.php'); ?>

    <script>
        let assign_room_form = document.getElementById('assign_room_form');

        // Fetch available rooms via AJAX
        function assign_room_modal(id, room_id) {
            assign_room_form.elements['booking_id'].value = id;
            let xhr = new XMLHttpRequest();
            xhr.open("POST", "ajax/booking_records.php", true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() { assign_room_form.elements['room_no'].innerHTML = this.responseText; }
            xhr.send('get_available_rooms=1&room_id=' + room_id);
        }

        // Handle Check-in Submission
        assign_room_form.addEventListener('submit', function(e){
            e.preventDefault();
            let data = new FormData();
            data.append('room_no', assign_room_form.elements['room_no'].value);
            data.append('booking_id', assign_room_form.elements['booking_id'].value);
            data.append('assign_room', '');

            let xhr = new XMLHttpRequest();
            xhr.open("POST", "ajax/booking_records.php", true);
            xhr.onload = function(){
                if(this.responseText == 1){
                    alert('success', 'Room Assigned & Check-in Complete!');
                    location.reload();
                } else { alert('error', 'Server error!'); }
            }
            xhr.send(data);
        });

        // Handle Checkout
        function checkout_booking(id, room_no) {
            if(confirm("Are you sure you want to checkout this guest?")) {
                let xhr = new XMLHttpRequest();
                xhr.open("POST", "ajax/booking_records.php", true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if(this.responseText == 1) {
                        alert('success', 'Guest Checked Out!');
                        location.reload();
                    } else { alert('error', 'Checkout failed!'); }
                }
                xhr.send('checkout_booking=1&booking_id=' + id + '&room_no=' + room_no);
            }
        }

        // Handle Cancellation
        function cancel_booking(id) {
            if(confirm("Are you sure you want to cancel this booking?")) {
                let xhr = new XMLHttpRequest();
                xhr.open("POST", "ajax/booking_records.php", true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if(this.responseText == 1) {
                        alert('success', 'Booking Cancelled!');
                        location.reload();
                    } else { alert('error', 'Cancellation failed!'); }
                }
                xhr.send('cancel_booking=1&booking_id=' + id);
            }
        }
    </script>
</body>
</html>