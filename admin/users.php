<?php 
    require('../config/db_config.php');
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if(!(isset($_SESSION['adminLogin']) && $_SESSION['adminLogin'] == true)){
        header("Location: login.php"); exit;
    }

    // DELETE USER LOGIC
    if(isset($_GET['del'])) {
        $u_id = mysqli_real_escape_string($conn, $_GET['del']);
        
        // Fetch image to delete from folder
        $res = mysqli_query($conn, "SELECT `profile_pic` FROM `users` WHERE `u_id`='$u_id'");
        $data = mysqli_fetch_assoc($res);
        
        if($data['profile_pic'] != 'default.jpg'){
            unlink("../assets/images/users/".$data['profile_pic']);
        }

        $q = "DELETE FROM `users` WHERE `u_id`='$u_id'";
        if(mysqli_query($conn, $q)) {
            echo "<script>alert('User removed successfully!'); window.location.href='users.php';</script>";
        }
    }

    $query = "SELECT * FROM `users` WHERE 1"; 
    if(isset($_GET['search_user']) && trim($_GET['search_user']) != '') {
        $search = mysqli_real_escape_string($conn, $_GET['search_user']);
        $query .= " AND (`u_name` LIKE '%$search%' OR `u_email` LIKE '%$search%' OR `u_phone` LIKE '%$search%')";
    }
    $query .= " ORDER BY `u_id` DESC";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .profile-img { width: 40px; height: 40px; object-fit: cover; border-radius: 50%; }
    </style>
</head>
<body class="bg-light">

    <?php include('includes/admin_header.php'); ?>
    <?php include('includes/admin_sidebar.php'); ?>

    <div id="main-content">
        <div class="container-fluid">
            
            <div class="row bg-white shadow-sm rounded p-3 mb-4 d-flex align-items-center justify-content-between mx-0 mt-4">
                <div class="col-md-6">
                    <h3 class="m-0"><i class="fas fa-users me-2"></i>MANAGE USERS</h3>
                </div>
                <div class="col-md-6">
                    <form method="GET" class="d-flex justify-content-md-end mt-md-0 mt-3">
                        <input type="text" name="search_user" class="form-control shadow-none w-50 me-2" placeholder="Search users..." value="<?php echo isset($_GET['search_user']) ? $_GET['search_user'] : ''; ?>">
                        <button type="submit" class="btn btn-dark shadow-none">Search</button>
                        <?php if(isset($_GET['search_user'])): ?>
                            <a href="users.php" class="btn btn-light border ms-2">Clear</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover border text-center m-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Address</th>
                                    <th>Pincode</th>
                                    <th>Joined On</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    $res = mysqli_query($conn, $query);
                                    $i = 1;
                                    while($row = mysqli_fetch_assoc($res)) {
                                        // Path to user images
                                        $pic_path = "../assets/images/users/";
                                        $img = (empty($row['profile_pic'])) ? 'default.jpg' : $row['profile_pic'];

                                        echo "
                                            <tr class='align-middle'>
                                                <td>$i</td>
                                                <td>
                                                    <img src='$pic_path$img' class='profile-img border'>
                                                </td>
                                                <td class='fw-bold'>$row[u_name]</td>
                                                <td>$row[u_email]</td>
                                                <td>$row[u_phone]</td>
                                                <td class='text-start' style='max-width:200px;'>$row[u_address]</td>
                                                <td><span class='badge bg-light text-dark border'>$row[u_pincode]</span></td>
                                                <td>" . date('d-m-Y', strtotime($row['created_at'])) . "</td>
                                                <td>
                                                    <a href='?del=$row[u_id]' class='btn btn-danger btn-sm shadow-none' onclick=\"return confirm('Are you sure you want to remove this user? This will delete their profile picture too.')\">
                                                        <i class='fas fa-trash'></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        ";
                                        $i++;
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>