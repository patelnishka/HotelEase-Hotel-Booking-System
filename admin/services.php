<?php 
    require('../config/db_config.php');
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if(!(isset($_SESSION['adminLogin']) && $_SESSION['adminLogin'] == true)){
        header("Location: login.php"); exit;
    }

    // 1. Handle Add Service
    if(isset($_POST['add_service'])) {
        $name = mysqli_real_escape_string($conn, $_POST['s_name']);
        $desc = mysqli_real_escape_string($conn, $_POST['s_desc']);
        $icon = mysqli_real_escape_string($conn, $_POST['s_icon']);

        $q = "INSERT INTO `services`(`s_name`, `s_desc`, `s_icon`) VALUES ('$name','$desc','$icon')";
        if(mysqli_query($conn, $q)) {
            echo "<script>alert('Success: Service added!'); window.location.href='services.php';</script>";
        }
    }

    // 2. Handle Update Service
    if(isset($_POST['update_service'])) {
        $id = mysqli_real_escape_string($conn, $_POST['edit_id']);
        $name = mysqli_real_escape_string($conn, $_POST['s_name']);
        $desc = mysqli_real_escape_string($conn, $_POST['s_desc']);
        $icon = mysqli_real_escape_string($conn, $_POST['s_icon']);

        $q = "UPDATE `services` SET `s_name`='$name', `s_desc`='$desc', `s_icon`='$icon' WHERE `s_id`='$id'";
        if(mysqli_query($conn, $q)) {
            echo "<script>alert('Success: Service updated!'); window.location.href='services.php';</script>";
        }
    }

    // 3. Handle Delete Service
    if(isset($_GET['del'])) {
        $id = mysqli_real_escape_string($conn, $_GET['del']);
        mysqli_query($conn, "DELETE FROM `services` WHERE `s_id`='$id'");
        header("Location: services.php");
        exit;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* THE UNIVERSAL MODAL FIX */
        .modal { z-index: 9999 !important; background: rgba(0,0,0,0.5); }
        .modal-backdrop { display: none !important; }
        body.modal-open { overflow: hidden; }
        
        #main-content { overflow: visible !important; }
        .icon-box { 
            width: 50px; 
            height: 50px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            background: #f8f9fa; 
            border-radius: 8px;
        }
    </style>
</head>
<body class="bg-light">

    <?php include('includes/admin_header.php'); ?>
    <?php include('includes/admin_sidebar.php'); ?>

    <div id="main-content">
        <div class="container-fluid">
            
            <div class="row bg-white shadow-sm rounded p-3 mb-4 d-flex align-items-center justify-content-between mx-0">
                <div class="col-md-6">
                    <h3 class="m-0"><i class="fas fa-concierge-bell me-2"></i> SERVICES MANAGEMENT</h3>
                </div>
                <div class="col-md-6 text-end">
                    <button type="button" class="btn btn-dark shadow-none btn-sm" data-bs-toggle="modal" data-bs-target="#add-service">
                        <i class="fas fa-plus"></i> Add Service
                    </button>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover border text-center m-0">
                            <thead class="table-dark">
                                <tr>
                                    <th scope="col" width="10%">Icon</th>
                                    <th scope="col" width="20%">Name</th>
                                    <th scope="col" width="45%">Description</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    $res = mysqli_query($conn, "SELECT * FROM `services` ORDER BY `s_id` DESC");
                                    if(mysqli_num_rows($res) > 0) {
                                        while($row = mysqli_fetch_assoc($res)){
                                            echo "
                                                <tr class='align-middle'>
                                                    <td>
                                                        <div class='icon-box mx-auto'>
                                                            <i class='fa-solid $row[s_icon] fa-2x'></i>
                                                        </div>
                                                    </td>
                                                    <td class='fw-bold'>$row[s_name]</td>
                                                    <td class='text-start'><small>$row[s_desc]</small></td>
                                                    <td>
                                                        <button onclick=\"edit_service('$row[s_id]', '$row[s_name]', '$row[s_desc]', '$row[s_icon]')\" class='btn btn-primary btn-sm shadow-none' data-bs-toggle='modal' data-bs-target='#edit-service'>
                                                            <i class='fas fa-edit'></i>
                                                        </button>
                                                        <a href='?del=$row[s_id]' class='btn btn-danger btn-sm shadow-none' onclick=\"return confirm('Are you sure you want to delete this service?')\">
                                                            <i class='fas fa-trash'></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            ";
                                        }
                                    } else {
                                        echo "<tr><td colspan='4' class='p-4'>No services added yet.</td></tr>";
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="add-service" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog">
            <form method="POST">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Service</h5>
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Name</label>
                            <input type="text" name="s_name" class="form-control shadow-none" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Icon Class (FontAwesome)</label>
                            <input type="text" name="s_icon" class="form-control shadow-none" placeholder="e.g. fa-wifi" required>
                            <small class="text-muted">Example: fa-spa, fa-utensils, fa-swimming-pool</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Description</label>
                            <textarea name="s_desc" class="form-control shadow-none" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn text-secondary shadow-none" data-bs-dismiss="modal">CANCEL</button>
                        <button type="submit" name="add_service" class="btn btn-dark shadow-none">SUBMIT</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="edit-service" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog">
            <form method="POST">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Service Details</h5>
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="edit_id" id="edit_id">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Name</label>
                            <input type="text" name="s_name" id="edit_name" class="form-control shadow-none" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Icon Class</label>
                            <input type="text" name="s_icon" id="edit_icon" class="form-control shadow-none" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Description</label>
                            <textarea name="s_desc" id="edit_desc" class="form-control shadow-none" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn text-secondary shadow-none" data-bs-dismiss="modal">CANCEL</button>
                        <button type="submit" name="update_service" class="btn btn-dark shadow-none">SAVE CHANGES</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function edit_service(id, name, desc, icon) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_desc').value = desc;
            document.getElementById('edit_icon').value = icon;
        }
    </script>
</body>
</html>