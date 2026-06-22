<?php
include_once('../config/db_config.php');
date_default_timezone_set('Asia/Kolkata');

$is_link_valid = false;
$email_from_url = "";

if(isset($_GET['token']) && isset($_GET['email'])) {
    $token = mysqli_real_escape_string($conn, $_GET['token']);
    $email = mysqli_real_escape_string($conn, $_GET['email']);
    $now = date("Y-m-d H:i:s");

    // Check if token exists and is not expired
    $query = "SELECT * FROM `password_resets` WHERE `email`='$email' AND `token`='$token' AND `expiry` > '$now'";
    $result = mysqli_query($conn, $query);

    if(mysqli_num_rows($result) > 0) {
        $is_link_valid = true;
        $email_from_url = $email;
    } else {
        echo "<script>alert('Link invalid or expired!'); window.location.href='forgot_password.php';</script>";
        exit();
    }
}

// Handle the actual password update
if(isset($_POST['update_password'])) {
    // We use password_hash for security
    $new_pass = password_hash($_POST['pass'], PASSWORD_BCRYPT);
    $user_email = mysqli_real_escape_string($conn, $_POST['email_hidden']);

    // Update the u_pass in your users table
    $update = "UPDATE `users` SET `u_pwd` = '$new_pass' WHERE `u_email` = '$user_email'";
    
    if(mysqli_query($conn, $update)) {
        // SUCCESS: Delete the token so it cannot be used a second time
        mysqli_query($conn, "DELETE FROM `password_resets` WHERE `email` = '$user_email'");
        echo "<script>alert('Password updated successfully!'); window.location.href='login.php';</script>";
    } else {
        echo "Error updating password: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Set New Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php if($is_link_valid): ?>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow border-0 mt-5">
                    <div class="card-body p-4">
                        <h4 class="text-center mb-4">Create New Password</h4>
                        <form method="POST">
                            <input type="hidden" name="email_hidden" value="<?php echo $email_from_url; ?>">
                            <div class="mb-3">
                                <label class="form-label">New Password</label>
                                <input type="password" name="pass" class="form-control" placeholder="Minimum 6 characters" required minlength="6">
                            </div>
                            <button type="submit" name="update_password" class="btn btn-success w-100">Update Password</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</body>
</html>