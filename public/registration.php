<?php
include('../config/db_config.php');

if(isset($_POST['register_btn'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    // 1. Check if email already exists
    $check_email = "SELECT u_email FROM users WHERE u_email = '$email'";
    $run_check = mysqli_query($conn, $check_email);

    $pincode = $_POST['u_pincode'];

    // PHP Validation for Pincode
    if (!preg_match('/^[1-9][0-9]{5}$/', $pincode)) {
        echo "<script>alert('Invalid Pincode! Please enter a valid 6-digit code.'); window.history.back();</script>";
        exit;
    }

    if(mysqli_num_rows($run_check) > 0) {
        echo "<script>alert('This email is already registered. Please use another or Login.'); window.location.href='registration.php';</script>";
    } else {
        // 2. Proceed with Registration
        $name      = mysqli_real_escape_string($conn, $_POST['fullname']);
        $address   = mysqli_real_escape_string($conn, $_POST['address']);
        $gender    = mysqli_real_escape_string($conn, $_POST['gender']);
        $phone     = mysqli_real_escape_string($conn, $_POST['phone']);
        $u_pincode = mysqli_real_escape_string($conn, $_POST['u_pincode']); 
        $password  = $_POST['password'];

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $query = "INSERT INTO users (u_name, u_address, u_pincode, u_gender, u_email, u_phone, u_pwd) 
                  VALUES ('$name', '$address', '$u_pincode', '$gender', '$email', '$phone', '$hashed_password')";

        if(mysqli_query($conn, $query)) {
            echo "<script>alert('Registration Successful!'); window.location.href='login.php';</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Registration | Hotel Ease</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: url("../assets/images/register.jfif") no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 20px 0;
        }
        .register-card {
            max-width: 800px;
            width: 100%;
            background: rgba(255, 255, 255, 0.98);
            border-radius: 15px;
        }
        .form-label { font-weight: 600; font-size: 0.9rem; }

        /* Fixes visibility of error messages inside Input Groups */
        .was-validated .input-group .form-control:invalid ~ .invalid-feedback {
            display: block !important;
        }
    </style>
</head>
<body>

<div class="container d-flex justify-content-center">
    <div class="card register-card shadow border-0">
        <div class="card-body p-4 p-md-5">
            <h3 class="text-center mb-4 fw-bold text-warning">Create Your Account</h3>

            <form action="registration.php" method="POST" id="regForm" class="needs-validation" novalidate>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="fullname" class="form-control" placeholder="Enter Name" 
                               pattern="^[A-Za-z\s]+$" required>
                        <div class="invalid-feedback">Letters only, please.</div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" placeholder="Enter Email" required>
                        <div class="invalid-feedback">Enter a valid email.</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone Number</label>
                        <div class="input-group has-validation">
                            <span class="input-group-text bg-light">+91</span>
                            <input type="tel" name="phone" class="form-control" placeholder="Enter Phone Number" 
                                   pattern="\d{10}" maxlength="10" required>
                            <div class="invalid-feedback">10 digits required.</div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Pincode</label>
                        <input type="text" name="u_pincode" class="form-control shadow-none" 
                               oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);" 
                               placeholder="Enter Pincode" pattern="^[1-9][0-9]{5}$" required>
                        <div class="invalid-feedback">Valid 6-digit pincode required.</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Gender</label>
                        <div class="mt-2">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="gender" id="Male" value="Male" required>
                                <label class="form-check-label small" for="Male">Male</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="gender" id="Female" value="Female">
                                <label class="form-check-label small" for="Female">Female</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="gender" id="Other" value="Other">
                                <label class="form-check-label small" for="Other">Other</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control" placeholder="Enter full address" rows="2" required></textarea>
                    <div class="invalid-feedback">Address is required.</div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Password</label>
                        <div class="input-group has-validation">
                            <input type="password" id="reg_pass" placeholder="Enter Password" name="password" class="form-control" 
                                   pattern="^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{6,12}$"   
                                   required>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePass('reg_pass')">👁️</button>
                            <div class="invalid-feedback">6-12 chars, mix letters/numbers/symbols.</div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Confirm Password</label>
                        <div class="input-group has-validation">
                            <input type="password" id="reg_cpass" class="form-control" placeholder="Enter Confirm Password" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePass('reg_cpass')">👁️</button>
                            <div class="invalid-feedback">Passwords do not match!</div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" name="register_btn" class="btn btn-warning w-100 fw-bold shadow-sm py-2">Create Account</button>
                </div>

                <p class="text-center mt-3 mb-0">
                    Already have an account? <a href="login.php" class="text-warning text-decoration-none fw-bold">Login</a>
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

// Validation Script
(function () {
  'use strict'
  var form = document.getElementById('regForm');
  var pass = document.getElementById('reg_pass');
  var cpass = document.getElementById('reg_cpass');

  // Validate confirm password as user types
  cpass.addEventListener('input', function() {
    if (this.value !== pass.value) {
        this.setCustomValidity("Invalid");
    } else {
        this.setCustomValidity("");
    }
  });

  // Validate on form submit
  form.addEventListener('submit', function (event) {
    if (pass.value !== cpass.value) {
        cpass.setCustomValidity("Invalid");
    } else {
        cpass.setCustomValidity("");
    }

    if (!form.checkValidity()) {
      event.preventDefault();
      event.stopPropagation();
    }
    
    form.classList.add('was-validated');
  }, false);
})();
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>