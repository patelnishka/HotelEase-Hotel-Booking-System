<?php
    require('../../config/db_config.php');

    // Fetch rooms specific to the category that are marked 'available'
    if(isset($_POST['get_available_rooms'])) {
        $room_id = $_POST['room_id'];
        $q = "SELECT * FROM `room_numbers` WHERE `room_id`=? AND `status`='available'";
        $stmt = mysqli_prepare($conn, $q);
        mysqli_stmt_bind_param($stmt, 'i', $room_id);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);

        if(mysqli_num_rows($res) > 0) {
            echo "<option value=''>Select Room Number</option>";
            while($row = mysqli_fetch_assoc($res)) {
                echo "<option value='$row[room_no]'>$row[room_no]</option>";
            }
        } else { 
            echo "<option value=''>No Rooms Available</option>"; 
        }
    }

    // Handle the actual assignment and marking as arrived (Moves to Active Stays)
    if(isset($_POST['assign_room'])) {
        $frm_data = $_POST;

        mysqli_begin_transaction($conn);
        try {
            // 1. Update booking: arrival status to 1 and set room_no
            $q1 = "UPDATE `bookings` SET `arrival` = 1, `room_no` = ? WHERE `id` = ?";
            $stmt1 = mysqli_prepare($conn, $q1);
            mysqli_stmt_bind_param($stmt1, 'si', $frm_data['room_no'], $frm_data['booking_id']);
            mysqli_stmt_execute($stmt1);

            // 2. Update room_numbers: status to 'occupied'
            $q2 = "UPDATE `room_numbers` SET `status` = 'occupied' WHERE `room_no` = ?";
            $stmt2 = mysqli_prepare($conn, $q2);
            mysqli_stmt_bind_param($stmt2, 's', $frm_data['room_no']);
            mysqli_stmt_execute($stmt2);

            mysqli_commit($conn);
            echo 1;
        } catch (Exception $e) {
            mysqli_rollback($conn);
            echo 0;
        }
    }

    // Handle Cancellation
    if(isset($_POST['cancel_booking'])) {
        $frm_data = $_POST;
        
        // Update status to 'cancelled' so it disappears from 'New Bookings'
        $q = "UPDATE `bookings` SET `status` = 'cancelled' WHERE `id` = ?";
        $stmt = mysqli_prepare($conn, $q);
        mysqli_stmt_bind_param($stmt, 'i', $frm_data['booking_id']);
        
        if(mysqli_stmt_execute($stmt)) {
            echo 1;
        } else {
            echo 0;
        }
    }

    // Checkout Logic
    if(isset($_POST['checkout_booking'])) {
        $frm_data = $_POST;

        mysqli_begin_transaction($conn);
        try {
            // arrival = 2 (Checked-out)
            $q1 = "UPDATE `bookings` SET `arrival` = 2 WHERE `id` = ?";
            $stmt1 = mysqli_prepare($conn, $q1);
            mysqli_stmt_bind_param($stmt1, 'i', $frm_data['booking_id']);
            mysqli_stmt_execute($stmt1);

            // room status back to 'available'
            $q2 = "UPDATE `room_numbers` SET `status` = 'available' WHERE `room_no` = ?";
            $stmt2 = mysqli_prepare($conn, $q2);
            mysqli_stmt_bind_param($stmt2, 's', $frm_data['room_no']);
            mysqli_stmt_execute($stmt2);

            mysqli_commit($conn);
            echo 1;
        } catch (Exception $e) {
            mysqli_rollback($conn);
            echo 0;
        }
    }
?>