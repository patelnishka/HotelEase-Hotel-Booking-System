<?php
    require_once('../../config/db_config.php');

    // Add Room
    if(isset($_POST['add_room'])) {
        $features = json_decode($_POST['features']);
        $frm_data = $_POST;
        $flag = 0;

        $q1 = "INSERT INTO `rooms`(`name`, `area`, `price`, `quantity`, `adult`, `children`, `description`) VALUES (?,?,?,?,?,?,?)";
        $values = [$frm_data['name'], $frm_data['area'], $frm_data['price'], $frm_data['quantity'], $frm_data['adult'], $frm_data['children'], $frm_data['desc']];

        if($stmt = mysqli_prepare($conn, $q1)) {
            mysqli_stmt_bind_param($stmt, "siiiiis", ...$values);
            if(mysqli_stmt_execute($stmt)) {
                $room_id = mysqli_insert_id($conn);
                
                // Insert Features
                $q2 = "INSERT INTO `room_features`(`room_id`, `f_id`) VALUES (?, ?)";
                if($stmt2 = mysqli_prepare($conn, $q2)) {
                    foreach($features as $f) {
                        mysqli_stmt_bind_param($stmt2, "ii", $room_id, $f);
                        mysqli_stmt_execute($stmt2);
                    }
                    mysqli_stmt_close($stmt2);
                }
                $flag = 1;
            }
            mysqli_stmt_close($stmt);
        }

        if($flag == 1){
            for($i=1; $i<=3; $i++) {
                if(isset($_FILES['image'.$i])){
                    $img_name = $_FILES['image'.$i]['name'];
                    $tmp_name = $_FILES['image'.$i]['tmp_name'];
                    $ext = pathinfo($img_name, PATHINFO_EXTENSION);
                    $r_img_name = "room_".$room_id."_".time()."_".$i.".".$ext;
                    $path = $_SERVER['DOCUMENT_ROOT']."/hotel_ease_bs/assets/images/rooms/".$r_img_name;
                    
                    if(move_uploaded_file($tmp_name, $path)) {
                        $thumb = ($i == 1) ? 1 : 0;
                        $q3 = "INSERT INTO `room_images`(`room_id`, `image`, `thumb`) VALUES (?,?,?)";
                        $stmt3 = mysqli_prepare($conn, $q3);
                        mysqli_stmt_bind_param($stmt3, "isi", $room_id, $r_img_name, $thumb);
                        mysqli_stmt_execute($stmt3);
                        mysqli_stmt_close($stmt3);
                    }
                }
            }
            echo 1;
        } else { echo 0; }
    }

    // Get All Rooms
    if(isset($_POST['get_all_rooms'])) {
        $res = mysqli_query($conn, "SELECT * FROM `rooms` ORDER BY `id` DESC");
        $i = 1;
        $data = "";

        while($row = mysqli_fetch_assoc($res)) {
            $features_q = mysqli_query($conn, "SELECT f.f_name FROM `features` f 
                INNER JOIN `room_features` rfac ON f.f_id = rfac.f_id 
                WHERE rfac.room_id = '$row[id]'");

            $features_data = "";
            while($f_row = mysqli_fetch_assoc($features_q)){
                $features_data .= "<span class='badge rounded-pill bg-light text-dark shadow-sm me-1 mb-1'>$f_row[f_name]</span>";
            }

            // Correct toggle_status call using status from DB
            if($row['status'] == 1) {
                $status = "<button onclick='toggle_status($row[id], 0)' class='btn btn-dark btn-sm shadow-none'>active</button>";
            } else {
                $status = "<button onclick='toggle_status($row[id], 1)' class='btn btn-warning btn-sm shadow-none'>inactive</button>";
            }

            $data .= "
                <tr class='align-middle'>
                    <td>$i</td>
                    <td>$row[name]</td>
                    <td>$row[area] sq. ft.</td>
                    <td>
                        <span class='badge rounded-pill bg-light text-dark'>Adult: $row[adult]</span><br>
                        <span class='badge rounded-pill bg-light text-dark'>Children: $row[children]</span>
                    </td>
                    <td>₹$row[price]</td>
                    <td>$row[quantity]</td>
                    <td style='width: 200px;'>$features_data</td>
                    <td><small class='text-muted'>".substr($row['description'],0,50)."...</small></td>
                    <td>$status</td>
                    <td>
                        <div class='d-flex flex-column gap-1'>
                            <button type='button' onclick='edit_details($row[id])' class='btn btn-primary btn-sm shadow-none' data-bs-toggle='modal' data-bs-target='#edit-room'>
                                <i class='bi bi-pencil-square'></i>
                            </button>
                            <button type='button' onclick='remove_room($row[id])' class='btn btn-danger btn-sm shadow-none'>
                                <i class='bi bi-trash'></i>
                            </button>
                        </div>
                    </td>
                </tr>";
            $i++;
        }
        echo $data;
    }

    // Toggle Status
    if(isset($_POST['toggle_status'])) {
        $q = "UPDATE `rooms` SET `status`=? WHERE `id`=?";
        $stmt = mysqli_prepare($conn, $q);
        // Matching parameters sent from JS
        mysqli_stmt_bind_param($stmt, "ii", $_POST['value'], $_POST['room_id']);
        if(mysqli_stmt_execute($stmt)) { echo 1; } else { echo 0; }
        mysqli_stmt_close($stmt);
    }

    // Remove Room
    if(isset($_POST['remove_room'])) {
        $res = mysqli_query($conn, "SELECT * FROM `room_images` WHERE `room_id`='$_POST[room_id]'");
        while($row = mysqli_fetch_assoc($res)){
            $path = $_SERVER['DOCUMENT_ROOT']."/hotel_ease_bs/assets/images/rooms/".$row['image'];
            if(file_exists($path)){
                unlink($path);
            }
        }
        
        $q = "DELETE FROM `rooms` WHERE `id`=?";
        $stmt = mysqli_prepare($conn, $q);
        mysqli_stmt_bind_param($stmt, "i", $_POST['room_id']);
        if(mysqli_stmt_execute($stmt)) { echo 1; } else { echo 0; }
        mysqli_stmt_close($stmt);
    }

    // Fetch individual room data
    if(isset($_POST['get_room'])) {
        $res1 = mysqli_query($conn, "SELECT * FROM `rooms` WHERE `id`='$_POST[get_room]'");
        $roomdata = mysqli_fetch_assoc($res1);

        $res2 = mysqli_query($conn, "SELECT * FROM `room_features` WHERE `room_id`='$_POST[get_room]'");
        $features = [];
        if(mysqli_num_rows($res2)>0){
            while($row = mysqli_fetch_assoc($res2)){
                $features[] = $row['f_id'];
            }
        }
        echo json_encode(["roomdata" => $roomdata, "features" => $features]);
    }

    // Update Room Details
    if(isset($_POST['edit_room'])) {
        $features = json_decode($_POST['features']);
        $frm_data = $_POST;
        
        $q1 = "UPDATE `rooms` SET `name`=?,`area`=?,`price`=?,`quantity`=?,`adult`=?,`children`=?,`description`=? WHERE `id`=?";
        $v1 = [$frm_data['name'], $frm_data['area'], $frm_data['price'], $frm_data['quantity'], $frm_data['adult'], $frm_data['children'], $frm_data['desc'], $frm_data['room_id']];

        if($stmt = mysqli_prepare($conn, $q1)) {
            mysqli_stmt_bind_param($stmt, "siiiiisi", ...$v1);
            if(mysqli_stmt_execute($stmt)) {
                mysqli_query($conn, "DELETE FROM `room_features` WHERE `room_id`='$frm_data[room_id]'");
                $q2 = "INSERT INTO `room_features`(`room_id`, `f_id`) VALUES (?, ?)";
                if($stmt2 = mysqli_prepare($conn, $q2)) {
                    foreach($features as $f) {
                        mysqli_stmt_bind_param($stmt2, "ii", $frm_data['room_id'], $f);
                        mysqli_stmt_execute($stmt2);
                    }
                    mysqli_stmt_close($stmt2);
                }
                echo 1;
            } else { echo 0; }
            mysqli_stmt_close($stmt);
        }
    }
?>