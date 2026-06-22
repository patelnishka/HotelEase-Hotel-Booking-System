<?php 
    require('../config/db_config.php');
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    if(!(isset($_SESSION['adminLogin']) && $_SESSION['adminLogin'] == true)){
        header("Location: login.php"); exit;
    }

    if(isset($_POST['add_image'])) {
        $desc = mysqli_real_escape_string($conn, $_POST['g_desc']);
        $img = $_FILES['g_img']['name'];
        $ext = pathinfo($img, PATHINFO_EXTENSION);
        $rename = "IMG_".random_int(11111, 99999).".".$ext;
        if(move_uploaded_file($_FILES['g_img']['tmp_name'], "../assets/images/".$rename)) {
            mysqli_query($conn, "INSERT INTO `gallery`(`g_desc`, `g_img`) VALUES ('$desc','$rename')");
            header("Location: gallery.php?success=1"); exit;
        }
    }

    if(isset($_POST['update_image'])) {
        $id = mysqli_real_escape_string($conn, $_POST['edit_id']);
        $desc = mysqli_real_escape_string($conn, $_POST['edit_desc']);
        mysqli_query($conn, "UPDATE `gallery` SET `g_desc`='$desc' WHERE `g_id`='$id'");
        header("Location: gallery.php?updated=1"); exit;
    }

    if(isset($_GET['del'])) {
        $id = mysqli_real_escape_string($conn, $_GET['del']);
        $res = mysqli_query($conn, "SELECT * FROM `gallery` WHERE `g_id`='$id'");
        if($row = mysqli_fetch_assoc($res)){
            unlink("../assets/images/".$row['g_img']);
            mysqli_query($conn, "DELETE FROM `gallery` WHERE `g_id`='$id'");
        }
        header("Location: gallery.php"); exit;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .modal { z-index: 9999 !important; background: rgba(0,0,0,0.5); }
        .modal-backdrop { display: none !important; }
        body.modal-open { overflow: hidden; }
        .gallery-img { height: 200px; object-fit: cover; width: 100%; }
    </style>
</head>
<body class="bg-light">

    <?php include('includes/admin_header.php'); ?>
    <?php include('includes/admin_sidebar.php'); ?>

    <div id="main-content">
        <div class="container-fluid">
            <div class="row bg-white shadow-sm rounded p-3 mb-4 d-flex align-items-center justify-content-between mx-0">
                <div class="col-md-6"><h3><i class="fas fa-images me-2"></i> GALLERY</h3></div>
                <div class="col-md-6 text-end">
                    <button type="button" class="btn btn-dark btn-sm" data-bs-toggle="modal" data-bs-target="#add-img">Add Photo</button>
                </div>
            </div>

            <div class="row">
                <?php 
                    $res = mysqli_query($conn, "SELECT * FROM `gallery` ORDER BY `g_id` DESC");
                    while($row = mysqli_fetch_assoc($res)){
                        echo "
                        <div class='col-md-3 mb-4'>
                            <div class='card border-0 shadow-sm'>
                                <img src='../assets/images/$row[g_img]' class='gallery-img'>
                                <div class='card-body'>
                                    <p>$row[g_desc]</p>
                                    <button onclick=\"edit_details('$row[g_id]', '$row[g_desc]')\" class='btn btn-primary btn-sm' data-bs-toggle='modal' data-bs-target='#edit-img'>Edit</button>
                                    <a href='?del=$row[g_id]' class='btn btn-danger btn-sm'>Delete</a>
                                </div>
                            </div>
                        </div>";
                    }
                ?>
            </div>
        </div>
    </div>

    <div class="modal fade" id="add-img" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-content">
                    <div class="modal-header"><h5>Upload Photo</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <div class="mb-3"><label class="form-label">Description</label><input type="text" name="g_desc" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label">Image</label><input type="file" name="g_img" class="form-control" required></div>
                    </div>
                    <div class="modal-footer"><button type="submit" name="add_image" class="btn btn-dark">UPLOAD</button></div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="edit-img" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST">
                <div class="modal-content">
                    <div class="modal-header"><h5>Edit Photo</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <input type="hidden" name="edit_id" id="edit_id">
                        <div class="mb-3"><label class="form-label">Description</label><input type="text" name="edit_desc" id="edit_desc" class="form-control" required></div>
                    </div>

                    
                    <div class="modal-footer"><button type="submit" name="update_image" class="btn btn-dark">SAVE</button></div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function edit_details(id, desc) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_desc').value = desc;
        }
    </script>
</body>
</html>