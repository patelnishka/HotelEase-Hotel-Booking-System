<?php
require_once('../../config/db_config.php');
if(isset($_POST['check_availability'])) {
    $frm_data = $_POST;
    
    // 1. Prepare the dates
    $checkin = $frm_data['checkin'];
    $checkout = $frm_data['checkout'];
    $room_id = $frm_data['room_id'];

    // 2. The Overlap Logic: 
    // This query finds any booking that is NOT cancelled and overlaps with selected dates
    $query = "SELECT * FROM `bookings` WHERE `room_id` = ? 
              AND `status` != 'cancelled' 
              AND (`check_in` < ? AND `check_out` > ?)";

    // We use 'iss' for: (i)nt room_id, (s)tring checkout, (s)tring checkin
    // Note: Use your existing database helper function if you have one, 
    // otherwise use standard mysqli:
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'iss', $room_id, $checkout, $checkin);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    if(mysqli_num_rows($res) > 0) {
        // Overlap found!
        echo json_encode(['status' => 'unavailable']);
    } else {
        // Room is free!
        echo json_encode(['status' => 'available']);
    }
}
?>