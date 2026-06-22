<?php
    require('../../config/db_config.php');

    if(isset($_POST['add_unit'])) {
        $frm_data = $_POST;

        // Check if room number already exists to prevent duplicates
        $check_q = "SELECT * FROM `room_numbers` WHERE `room_no` = ?";
        $res = mysqli_prepare($conn, $check_q);
        mysqli_stmt_bind_param($res, 's', $frm_data['room_no']);
        mysqli_stmt_execute($res);
        $check_res = mysqli_stmt_get_result($res);

        if(mysqli_num_rows($check_res) > 0) {
            echo 0; // Room number already exists
        } else {
            $q = "INSERT INTO `room_numbers` (`room_id`, `room_no`) VALUES (?, ?)";
            $stmt = mysqli_prepare($conn, $q);
            mysqli_stmt_bind_param($stmt, 'is', $frm_data['room_id'], $frm_data['room_no']);
            
            if(mysqli_stmt_execute($stmt)) {
                echo 1; // Success
            } else {
                echo 0; // Failure
            }
            mysqli_stmt_close($stmt);
        }
    }
?>