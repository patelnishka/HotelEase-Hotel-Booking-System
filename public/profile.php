<?php 
    require('../config/db_config.php');
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if(!(isset($_SESSION['u_id']))){
        header("Location: index.php");
        exit;
    }

    // --- HELPER FUNCTIONS ---
    function filteration($data){
        foreach($data as $key => $value){
            $value = trim($value); $value = stripslashes($value);
            $value = strip_tags($value); $value = htmlspecialchars($value);
            $data[$key] = $value;
        }
        return $data;
    }

    function update($sql, $values, $datatypes) {
        global $conn;
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, $datatypes, ...$values);
            if (mysqli_stmt_execute($stmt)) {
                $res = mysqli_stmt_affected_rows($stmt);
                mysqli_stmt_close($stmt);
                return $res;
            }
            mysqli_stmt_close($stmt);
        }
        return false;
    }

    $u_id = $_SESSION['u_id'];
    $u_res = mysqli_query($conn, "SELECT * FROM `users` WHERE `u_id`='$u_id' LIMIT 1");
    $u_data = mysqli_fetch_assoc($u_res);

    // --- LOGIC: UPDATE PROFILE INFO ---
    if(isset($_POST['info_update'])) {
        $frm_data = filteration($_POST);
        $query = "UPDATE `users` SET `u_name`=?, `u_address`=?, `u_pincode`=?, `u_phone`=? WHERE `u_id`=?";
        $values = [$frm_data['name'], $frm_data['address'], $frm_data['pincode'], $frm_data['phonenum'], $u_id];

        if(update($query, $values, 'ssisi')) {
            $_SESSION['u_name'] = $frm_data['name']; 
            echo"<script>alert('Profile Updated Successfully!'); window.location.href='profile.php';</script>";
        }
    }

    // --- LOGIC: PROFILE PICTURE MANAGEMENT ---
    if(isset($_POST['profile_update'])) {
        $img = $_FILES['profile_img'];
        $ext = pathinfo($img['name'], PATHINFO_EXTENSION);
        $new_name = "USER_".rand(1000,9999).".".$ext;
        $path = "../assets/images/users/";

        if(!is_dir($path)){
            mkdir($path, 0777, true);
        }

        if(move_uploaded_file($img['tmp_name'], $path.$new_name)){
            if($u_data['profile_pic'] != 'default.jpg' && !empty($u_data['profile_pic'])){
                if(file_exists($path.$u_data['profile_pic'])){
                    unlink($path.$u_data['profile_pic']);
                }
            }
            mysqli_query($conn, "UPDATE `users` SET `profile_pic`='$new_name' WHERE `u_id`='$u_id'");
            echo"<script>alert('Picture Updated!'); window.location.href='profile.php';</script>";
        } else {
            echo"<script>alert('Failed to upload image.');</script>";
        }
    }

    // --- LOGIC: SECURE PASSWORD CHANGE ---
    if(isset($_POST['pass_update'])) {
        $frm_data = filteration($_POST);
        if($frm_data['new_pass'] !== $frm_data['confirm_pass']) {
            echo"<script>alert('New passwords do not match!');</script>";
        } else {
            $enc_pass = password_hash($frm_data['new_pass'], PASSWORD_BCRYPT);
            if(update("UPDATE `users` SET `u_pwd`=? WHERE `u_id`=?", [$enc_pass, $u_id], 'si')) {
                echo"<script>alert('Password Changed Successfully!'); window.location.href='profile.php';</script>";
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Ease - User Dashboard</title>
    <?php include('../admin/includes/links.php'); ?>
    <style>
        .custom-bg { background-color: #2ec1ac; border: 1px solid #2ec1ac; color: white; }
        .custom-bg:hover { background-color: #27a692; border-color: #27a692; color: white; }
        .nav-pills .nav-link.active { background-color: #2ec1ac !important; }
        .h-line { width: 100px; height: 3px; background-color: #2ec1ac; }
    </style>
</head>
<body class="bg-light">

    <?php include('../includes/header.php'); ?>

    <div class="container">
        <div class="row">
            <div class="col-12 my-5 px-4">
                <h2 class="fw-bold">USER DASHBOARD</h2>
                <div class="h-line"></div>
            </div>

            <div class="col-lg-3 col-md-4 mb-5 px-4">
                <div class="bg-white shadow-sm rounded p-4 border-top border-4 border-dark">
                    <div class="text-center mb-3">
                        <form method="POST" enctype="multipart/form-data">
                            <img src="../assets/images/users/<?php echo ($u_data['profile_pic'] == '') ? 'default.jpg' : $u_data['profile_pic']; ?>" 
                                 class="rounded-circle img-fluid border mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                            <h5 class="fw-bold"><?php echo $u_data['u_name']; ?></h5>
                            <input type="file" name="profile_img" class="form-control form-control-sm shadow-none mb-2" required>
                            <button type="submit" name="profile_update" class="btn btn-sm custom-bg w-100">Update Photo</button>
                        </form>
                    </div>
                    <hr>
                    <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist">
                        <button class="nav-link active shadow-none mb-2 text-start" id="v-pills-profile-tab" data-bs-toggle="pill" data-bs-target="#v-pills-profile" type="button" role="tab">Personal Details</button>
                        <button class="nav-link shadow-none mb-2 text-start" id="v-pills-bookings-tab" data-bs-toggle="pill" data-bs-target="#v-pills-bookings" type="button" role="tab">My Bookings</button>
                        <button class="nav-link shadow-none mb-2 text-start" id="v-pills-password-tab" data-bs-toggle="pill" data-bs-target="#v-pills-password" type="button" role="tab">Security Settings</button>
                        <hr>
                        <button onclick="delete_account()" class="btn btn-outline-danger btn-sm w-100 mt-2">Delete My Account</button>
                    </div>
                </div>
            </div>

            <div class="col-lg-9 col-md-8 px-4">
                <div class="tab-content" id="v-pills-tabContent">
                    
                    <div class="tab-pane fade show active" id="v-pills-profile" role="tabpanel">
                        <div class="bg-white shadow-sm rounded p-4">
                            <form method="POST">
                                <h5 class="mb-4 fw-bold">General Information</h5>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Full Name</label>
                                        <input name="name" type="text" value="<?php echo $u_data['u_name']; ?>" class="form-control shadow-none" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Phone</label>
                                        <input name="phonenum" type="number" value="<?php echo $u_data['u_phone']; ?>" class="form-control shadow-none" required>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Address</label>
                                        <textarea name="address" class="form-control shadow-none" rows="2" required><?php echo $u_data['u_address']; ?></textarea>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">Pincode</label>
                                        <input name="pincode" type="number" value="<?php echo $u_data['u_pincode']; ?>" class="form-control shadow-none" required>
                                    </div>
                                </div>
                                <button type="submit" name="info_update" class="btn custom-bg shadow-none">Save All Changes</button>
                            </form>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="v-pills-bookings" role="tabpanel">
                        <div class="bg-white shadow-sm rounded p-4">
                            <h5 class="mb-4 fw-bold">My Booking History</h5>
                            <div class="table-responsive">
                                <table class="table table-hover border align-middle text-center">
                                    <thead>
                                        <tr class="bg-dark text-white">
                                            <th>Order ID</th>
                                            <th>Room</th>
                                            <th>Check-In</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                            // Using 'bookings' table from your screenshot
                                            $bk_q = "SELECT b.*, r.name AS room_name FROM `bookings` b 
                                                     INNER JOIN `rooms` r ON b.room_id = r.id 
                                                     WHERE b.u_id = '$u_id' ORDER BY b.id DESC";
                                            $bk_res = mysqli_query($conn, $bk_q);
                                            
                                            if(mysqli_num_rows($bk_res) > 0){
                                                while($row = mysqli_fetch_assoc($bk_res)){
                                                    $date = date("d-m-Y", strtotime($row['check_in']));
                                                    
                                                    // Status Badges
                                                    $status_badge = "";
                                                    if($row['status'] == 'pending'){
                                                        $status_badge = "<span class='badge bg-warning text-dark'>Confirmation Pending</span>";
                                                    } else if($row['status'] == 'booked'){
                                                        $status_badge = "<span class='badge bg-success'>Booked</span>";
                                                    } else {
                                                        $status_badge = "<span class='badge bg-danger'>$row[status]</span>";
                                                    }

                                                    // Receipt Button (Only show if not pending)
                                                    $btn = "";
                                                    if($row['status'] != 'pending'){
                                                        $btn = "<a href='generate_pdf.php?gen_pdf=1&id=$row[id]' target='_blank' class='btn btn-sm btn-primary shadow-none'><i class='bi bi-file-earmark-pdf'></i> Receipt</a>";                                                    } else {
                                                        $btn = "<span class='text-muted small'>Available after confirmation</span>";
                                                    }

                                                    echo "<tr>
                                                        <td><span class='badge bg-light text-dark border'>$row[order_id]</span></td>
                                                        <td>$row[room_name]</td>
                                                        <td>$date</td>
                                                        <td class='fw-bold'>₹$row[total_pay]</td>
                                                        <td>$status_badge</td>
                                                        <td>$btn</td>
                                                    </tr>";
                                                }
                                            } else {
                                                echo "<tr><td colspan='6' class='p-4 text-secondary'>You haven't made any bookings yet.</td></tr>";
                                            }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="v-pills-password" role="tabpanel">
                        <div class="bg-white shadow-sm rounded p-4">
                            <form method="POST">
                                <h5 class="mb-4 fw-bold">Security Settings</h5>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">New Password</label>
                                        <input name="new_pass" type="password" class="form-control shadow-none" required minlength="6">
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">Confirm New Password</label>
                                        <input name="confirm_pass" type="password" class="form-control shadow-none" required minlength="6">
                                    </div>
                                </div>
                                <button type="submit" name="pass_update" class="btn custom-bg shadow-none">Update Security Password</button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <?php include('../includes/footer.php'); ?>

    <script>
        function delete_account() {
            if (confirm("WARNING: All your bookings and personal data will be deleted forever. Are you sure?")) {
                window.location.href = "delete_account.php";
            }
        }
    </script>
</body>
</html>