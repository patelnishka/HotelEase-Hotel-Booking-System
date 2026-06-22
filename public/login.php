<?php
session_start();
include('../config/db_config.php');

// Redirect if already logged in
if(isset($_SESSION['u_id'])){
    header("Location: index.php");
    exit();
}

if(isset($_POST['login_btn'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Search for the user
    $query = "SELECT * FROM users WHERE u_email = '$email'";
    $result = mysqli_query($conn, $query);

    if(mysqli_num_rows($result) > 0) {
        $user_data = mysqli_fetch_assoc($result);
        
        // Verify the hashed password against the DB
        if(password_verify($password, $user_data['u_pwd'])) {
            $_SESSION['u_id'] = $user_data['u_id'];
            $_SESSION['u_name']  = $user_data['u_name'];

            // --- REMEMBER ME LOGIC START ---
            if(isset($_POST['remember'])) {
                // Store email and raw password (optional/decide based on security) for 30 days
                setcookie('user_email', $email, time() + (86400 * 30), "/");
                setcookie('user_pass', $password, time() + (86400 * 30), "/");
            } else {
                // Delete cookies if not checked
                setcookie('user_email', '', time() - 3600, "/");
                setcookie('user_pass', '', time() - 3600, "/");
            }
            // --- REMEMBER ME LOGIC END ---
            
            header("Location: index.php");
            exit();
        } else {
            echo "<script>alert('Incorrect Password!');</script>";
        }
    } else {
        echo "<script>alert('No account found with this email. Please Register first.'); window.location.href='registration.php';</script>";
    }
}

// Check for existing cookies to pre-fill form
$cookie_email = isset($_COOKIE['user_email']) ? $_COOKIE['user_email'] : '';
$cookie_pass = isset($_COOKIE['user_pass']) ? $_COOKIE['user_pass'] : '';
$cookie_checked = isset($_COOKIE['user_email']) ? 'checked' : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login | Hotel Ease</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: url("../assets/images/login.jfif") no-repeat center center fixed;
            background-size: cover;
            height: 100vh;
            display: flex;
            align-items: center;
        }
        .login-card {
            max-width: 400px;
            width: 100%;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card login-card shadow border-0 mx-auto">
        <div class="card-body p-4">
            <h3 class="text-center mb-4 fw-bold text-warning">User Login</h3>

            <form action="login.php" method="POST" id="loginForm" class="needs-validation" novalidate>
                
                <div class="mb-3">
                    <label class="form-label fw-semibold">Email Address</label>
                    <input type="email" name="email" value="<?php echo $cookie_email; ?>" class="form-control" placeholder="Enter your email" required>
                    <div class="invalid-feedback">Please enter a valid email address.</div>
                </div>

                <div class="mb-3">
    <label class="form-label fw-semibold">Password</label>
    <div class="input-group has-validation">
        <input type="password" id="login_pass" name="password" 
               value="<?php echo htmlspecialchars($cookie_pass); ?>" 
               class="form-control" 
               placeholder="Enter password" 
               pattern="^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{6,12}$" 
               required>
        <button class="btn btn-outline-secondary" type="button" id="toggleBtn" onclick="togglePass('login_pass')" style="z-index: 3;">👁️</button>
        <div class="invalid-feedback">6-12 chars (Mix of Letters, Numbers & Symbols).</div>
    </div>
</div>

                <div class="d-flex justify-content-between mb-4 small">
                    <div class="form-check">
                        <input class="form-check-input shadow-none" type="checkbox" name="remember" id="remember" <?php echo $cookie_checked; ?>>
                        <label class="form-check-label" for="remember">Remember Me</label>
                    </div>
                    <a href="forgot_password.php" class="text-decoration-none text-warning fw-bold">Forgot Password?</a>
                </div>

                <button type="submit" name="login_btn" class="btn btn-warning w-100 fw-bold shadow-sm py-2">Login</button>

                <p class="text-center mt-3 mb-0">
                    Don’t have an account? 
                    <a href="registration.php" class="text-warning text-decoration-none fw-bold">Register</a>
                    <a href=""></a>
                </p>
            </form>
        </div>
    </div>
</div>

<script>
// Toggle Password Visibility
function togglePass(id) {
    const input = document.getElementById(id);
    input.type = input.type === "password" ? "text" : "password";
}


(function () {
  'use strict'
  var forms = document.querySelectorAll('.needs-validation')
  Array.prototype.slice.call(forms).forEach(function (form) {
    form.addEventListener('submit', function (event) {
      if (!form.checkValidity()) {
        event.preventDefault()
        event.stopPropagation()
      }
      form.classList.add('was-validated')
    }, false)
  })
})()
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>