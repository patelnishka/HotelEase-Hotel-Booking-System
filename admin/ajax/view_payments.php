<?php
    require('../../config/db_config.php');

    // Section 1: Fetching and Displaying Data
    if(isset($_POST['get_payments'])) {
        $search = $_POST['search'];
        
        $q = "SELECT b.*, u.u_name FROM `bookings` b 
              INNER JOIN `users` u ON b.u_id = u.u_id 
              WHERE (b.order_id LIKE ? OR u.u_name LIKE ?) 
              ORDER BY b.id DESC";
        
        $search_val = "%$search%";
        $stmt = mysqli_prepare($conn, $q);
        mysqli_stmt_bind_param($stmt, 'ss', $search_val, $search_val);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);

        $i = 1;
        $table_data = "";

        if(mysqli_num_rows($res) == 0) {
            echo "<tr><td colspan='8' class='text-danger fw-bold p-4'>No Transactions Found!</td></tr>";
            exit;
        }

        while($row = mysqli_fetch_assoc($res)) {
            $date = date("d-m-Y", strtotime($row['datentime']));
            $total_amt = $row['total_pay'];
            $advance = $total_amt * 0.10; 

            $status_badge = "";
            $action_btn = "";

            if($row['status'] == 'pending'){
                $status_badge = "<span class='badge bg-warning text-dark'>Pending</span>";
                // Pass the unique row ID to the JS function
                $action_btn = "<button type='button' onclick='accept_payment($row[id])' class='btn btn-sm btn-success shadow-none me-2'>Accept Payment</button>";
            } else if($row['status'] == 'booked'){
                $status_badge = "<span class='badge bg-success'>Paid & Booked</span>";
            } else if($row['status'] == 'cancelled'){
                $status_badge = "<span class='badge bg-danger'>Cancelled</span>";
            } else {
                $status_badge = "<span class='badge bg-secondary'>$row[status]</span>";
            }

            $table_data .= "
                <tr>
                    <td>$i</td>
                    <td><span class='badge bg-light text-dark border'>$row[order_id]</span></td>
                    <td class='fw-bold'>$row[u_name]</td>
                    <td class='text-success fw-bold'>₹".number_format($total_amt)."</td>
                    <td class='text-primary fw-bold'>₹".number_format($advance)."</td>
                    <td>$status_badge</td>
                    <td>$date</td>
                    <td>
                        <div class='d-flex justify-content-center'>
                            $action_btn
                            <a href='../public/generate_pdf.php?gen_pdf=1&id=$row[id]' target='_blank' class='btn btn-sm btn-primary shadow-none'>
                                <i class='bi bi-file-earmark-pdf'></i>
                            </a>
                        </div>
                    </td>
                </tr>
            ";
            $i++;
        }
        echo $table_data;
    }

    // Section 2: Updating Data (Accept Payment)
    if(isset($_POST['accept_payment'])) {
        $booking_id = $_POST['booking_id'];
        
        $q = "UPDATE `bookings` SET `status` = 'booked' WHERE `id` = ?";
        $stmt = mysqli_prepare($conn, $q);
        mysqli_stmt_bind_param($stmt, 'i', $booking_id);
        
        if(mysqli_stmt_execute($stmt)){
            echo 1; // Success response for AJAX
        } else {
            echo 0; // Error response for AJAX
        }
    }
?>