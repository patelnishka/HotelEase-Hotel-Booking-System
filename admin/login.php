<?php 
require('../config/db_config.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(isset($_SESSION['adminLogin']) && $_SESSION['adminLogin'] == true){
    header("Location: dashboard.php");
    exit;
}

$error_msg = ""; 

if(isset($_POST['login_btn'])) {
    $user_input = trim($_POST['admin_name']);
    $pass = trim($_POST['admin_pass']);

    // 1. Specific Field Validation
    if(empty($user_input) && empty($pass)) {
        $error_msg = "All fields are empty!";
    } elseif(empty($user_input)) {
        $error_msg = "Email/Username field was empty!";
    } elseif(empty($pass)) {
        $error_msg = "Password field was empty!";
    } else {
        // 2. Database Verification
        $u_escaped = mysqli_real_escape_string($conn, $user_input);
        $p_escaped = mysqli_real_escape_string($conn, $pass);

        // Check if the user exists first
        $user_check = "SELECT * FROM `admin` WHERE `a_name`='$u_escaped' OR `a_email`='$u_escaped'";
        $res = mysqli_query($conn, $user_check);

        if(mysqli_num_rows($res) == 1) {
            $row = mysqli_fetch_assoc($res);
            
            // Check if password matches
            if($row['a_pwd'] == $p_escaped) {
                $_SESSION['adminLogin'] = true;
                $_SESSION['adminId'] = $row['a_id'];
                $_SESSION['adminName'] = $row['a_name'];

                header("Location: dashboard.php");
                exit;
            } else {
                $error_msg = "Incorrect password!";
            }
        } else {
            $error_msg = "Incorrect email or username!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Hotel Ease</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background: #121212; height: 100vh; display: flex; align-items: center; justify-content: center; margin: 0; }
        .login-box { width: 100%; max-width: 400px; background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 15px 35px rgba(0,0,0,0.5); }
        .btn-primary { background-color: #0d6efd; border: none; padding: 10px; font-weight: 600; }
        .form-control:focus { box-shadow: none; border-color: #0d6efd; }
        .logo-text { font-weight: 700; letter-spacing: 2px; color: #212529; }
    </style>
</head>
<body>

<div class="login-box mx-3">
    <div class="text-center mb-4">
        <h2 class="logo-text">HOTEL EASE</h2>
        <p class="text-muted">Admin Authentication</p>
    </div>

    <?php if($error_msg != ""): ?>
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="bi bi-exclamation-circle-fill me-2"></i>
            <div><?php echo $error_msg; ?></div>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div class="mb-3">
            <label class="form-label fw-bold">Username or Email</label>
            <input name="admin_name" type="text" class="form-control" placeholder="Enter name or email" 
                   value="<?php echo isset($_POST['admin_name']) ? htmlspecialchars($_POST['admin_name']) : ''; ?>">
        </div>
        <div class="mb-4">
            <label class="form-label fw-bold">Password</label>
            <input name="admin_pass" type="password" class="form-control" placeholder="Enter password">
        </div>
        <button name="login_btn" type="submit" class="btn btn-primary w-100 shadow-none">LOGIN</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>