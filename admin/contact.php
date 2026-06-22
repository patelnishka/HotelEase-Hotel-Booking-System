<?php 
    require('../config/db_config.php');
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if(!(isset($_SESSION['adminLogin']) && $_SESSION['adminLogin'] == true)){
        header("Location: login.php"); exit;
    }

    // Handle Admin Reply Submission
    if(isset($_POST['send_reply'])) {
        $id = mysqli_real_escape_string($conn, $_POST['q_id']);
        $reply = mysqli_real_escape_string($conn, $_POST['reply_msg']);

        $q = "UPDATE `contact_queries` SET `admin_reply`='$reply', `status`=1, `seen`=1 WHERE `id`='$id'";
        if(mysqli_query($conn, $q)) {
            echo "<script>alert('Reply sent successfully!'); window.location.href='contact.php';</script>";
        }
    }

    // Handle Delete
    if(isset($_GET['del'])) {
        $id = mysqli_real_escape_string($conn, $_GET['del']);
        $q = "DELETE FROM `contact_queries` WHERE `id`='$id'";
        if(mysqli_query($conn, $q)) {
            echo "<script>alert('Query deleted!'); window.location.href='contact.php';</script>";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - User Queries</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .modal { z-index: 9999 !important; background: rgba(0,0,0,0.5); }
        .modal-backdrop { display: none !important; }
        body.modal-open { overflow: hidden; }
        #main-content { overflow: visible !important; }
        .unread-row { background-color: #f0f7ff !important; font-weight: 600; }
    </style>
</head>
<body class="bg-light">

    <?php include('includes/admin_header.php'); ?>
    <?php include('includes/admin_sidebar.php'); ?>

    <div id="main-content">
        <div class="container-fluid">
            
            <div class="row bg-white shadow-sm rounded p-3 mb-4 d-flex align-items-center justify-content-between mx-0">
                <div class="col-12">
                    <h3 class="m-0"><i class="fas fa-envelope-open-text me-2"></i> USER QUERIES</h3>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover border text-center m-0">
                            <thead class="table-dark">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Email</th>
                                    <th scope="col" width="15%">Subject</th>
                                    <th scope="col" width="25%">Message</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    $q = "SELECT * FROM `contact_queries` ORDER BY `id` DESC";
                                    $res = mysqli_query($conn, $q);
                                    $i = 1;

                                    if(mysqli_num_rows($res) > 0) {
                                        while($row = mysqli_fetch_assoc($res)) {
                                            $bg_class = ($row['seen'] == 0) ? "unread-row" : "";
                                            
                                            $status_badge = ($row['status'] == 1) 
                                                ? "<span class='badge bg-success'>Replied</span>" 
                                                : "<span class='badge bg-warning text-dark'>Pending</span>";

                                            echo "
                                                <tr class='align-middle $bg_class'>
                                                    <td>$i</td>
                                                    <td>$row[name]</td>
                                                    <td>$row[email]</td>
                                                    <td>$row[subject]</td>
                                                    <td class='text-start'><small>$row[message]</small></td>
                                                    <td>$row[date]</td>
                                                    <td>$status_badge</td>
                                                    <td style='min-width: 130px;'>
                                                        <button class='btn btn-sm btn-primary shadow-none mb-1 w-100' data-bs-toggle='modal' data-bs-target='#replyModal$row[id]'>
                                                            <i class='fas fa-reply'></i> Reply
                                                        </button>
                                                        <a href='?del=$row[id]' class='btn btn-danger btn-sm shadow-none w-100' onclick=\"return confirm('Delete this query?')\">
                                                            <i class='fas fa-trash'></i> Delete
                                                        </a>
                                                    </td>
                                                </tr>
                                            ";

                                            // MODAL FOR EACH ROW
                                            ?>
                                            <div class="modal fade" id="replyModal<?php echo $row['id']; ?>" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form method="POST">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Reply to <?php echo $row['name']; ?></h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body text-start">
                                                                <p class="mb-1"><strong>User Query:</strong></p>
                                                                <p class="p-2 bg-light rounded small"><?php echo $row['message']; ?></p>
                                                                <hr>
                                                                <input type="hidden" name="q_id" value="<?php echo $row['id']; ?>">
                                                                <div class="mb-3">
                                                                    <label class="form-label fw-bold">Admin Response</label>
                                                                    <textarea name="reply_msg" class="form-control" rows="4" required><?php echo $row['admin_reply']; ?></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="submit" name="send_reply" class="btn btn-success w-100">Send & Save Reply</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
                                            $i++;
                                        }
                                    } else {
                                        echo "<tr><td colspan='8' class='p-4'>No queries received yet.</td></tr>";
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