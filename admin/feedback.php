<?php 
    require('../config/db_config.php');
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if(!(isset($_SESSION['adminLogin']) && $_SESSION['adminLogin'] == true)){
        header("Location: login.php"); exit;
    }

    // Handle Delete
    if(isset($_GET['del'])) {
        $f_id = mysqli_real_escape_string($conn, $_GET['del']);
        mysqli_query($conn, "DELETE FROM `feedback` WHERE `f_id`='$f_id'");
        echo "<script>alert('Removed!'); window.location.href='feedback.php';</script>";
    }

    // 1. Building the Filter Query
    $filter_query = "SELECT f.*, u.u_name FROM `feedback` f JOIN `users` u ON f.u_id = u.u_id WHERE 1";

    if(isset($_GET['search']) && !empty($_GET['search'])){
        $s = mysqli_real_escape_string($conn, $_GET['search']);
        $filter_query .= " AND (u.u_name LIKE '%$s%' OR f.message LIKE '%$s%')";
    }

    if(isset($_GET['rating_filter']) && $_GET['rating_filter'] != 'all'){
        $r = mysqli_real_escape_string($conn, $_GET['rating_filter']);
        $filter_query .= " AND f.rating = '$r'";
    }

    $filter_query .= " ORDER BY f.f_id DESC";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Feedback - Hotel Ease</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* THE UNIVERSAL MODAL FIX */
        .modal { z-index: 9999 !important; background: rgba(0,0,0,0.5); }
        .modal-backdrop { display: none !important; }
        body.modal-open { overflow: hidden; }
        
        #main-content { overflow: visible !important; }
        .star-rating { color: #ffc107; letter-spacing: 2px; }
    </style>
</head>
<body class="bg-light">

    <?php include('includes/admin_header.php'); ?>
    <?php include('includes/admin_sidebar.php'); ?>

    <div id="main-content">
        <div class="container-fluid">
            
            <div class="row bg-white shadow-sm rounded p-3 mb-4 d-flex align-items-center justify-content-between mx-0">
                <div class="col-md-4">
                    <h3 class="m-0"><i class="fas fa-comment-dots me-2"></i> GUEST FEEDBACK</h3>
                </div>
                <div class="col-md-8">
                    <form method="GET" class="d-flex justify-content-end gap-2">
                        <select name="rating_filter" class="form-select shadow-none w-25">
                            <option value="all">All Ratings</option>
                            <option value="5" <?php echo (@$_GET['rating_filter']=='5')?'selected':''; ?>>5 Stars</option>
                            <option value="4" <?php echo (@$_GET['rating_filter']=='4')?'selected':''; ?>>4 Stars</option>
                            <option value="3" <?php echo (@$_GET['rating_filter']=='3')?'selected':''; ?>>3 Stars</option>
                            <option value="2" <?php echo (@$_GET['rating_filter']=='2')?'selected':''; ?>>2 Stars</option>
                            <option value="1" <?php echo (@$_GET['rating_filter']=='1')?'selected':''; ?>>1 Star</option>
                        </select>
                        <input type="text" name="search" class="form-control shadow-none w-50" placeholder="Search message or user..." value="<?php echo @$_GET['search']; ?>">
                        <button type="submit" class="btn btn-dark shadow-none">Filter</button>
                        <a href="feedback.php" class="btn btn-light border shadow-none">Reset</a>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover border text-center m-0">
                            <thead class="table-dark">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Guest</th>
                                    <th scope="col">Rating</th>
                                    <th scope="col" width="45%">Message</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    $res = mysqli_query($conn, $filter_query);
                                    $i = 1;
                                    if(mysqli_num_rows($res) > 0) {
                                        while($row = mysqli_fetch_assoc($res)){
                                            $stars = str_repeat("<i class='fas fa-star'></i>", $row['rating']);
                                            $date = date('d-m-Y', strtotime($row['created_at']));
                                            echo "
                                                <tr class='align-middle'>
                                                    <td>$i</td>
                                                    <td class='fw-bold'>$row[u_name]</td>
                                                    <td class='star-rating'>$stars</td>
                                                    <td class='text-start'><small>$row[message]</small></td>
                                                    <td>$date</td>
                                                    <td>
                                                        <a href='?del=$row[f_id]' class='btn btn-danger btn-sm shadow-none' onclick=\"return confirm('Delete this feedback permanently?')\">
                                                            <i class='fas fa-trash'></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            ";
                                            $i++;
                                        }
                                    } else {
                                        echo "<tr><td colspan='6' class='p-4'>No feedback found.</td></tr>";
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