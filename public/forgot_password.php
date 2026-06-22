<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card border-0 shadow-sm mt-5">
                    <div class="card-body p-4">
                        <h4 class="text-center mb-4">Forgot Password?</h4>
                        <p class="text-muted text-center small">Enter your registered email and we'll send you a link to reset your password.</p>
                        
                        <form action="send_reset_logic.php" method="POST">
                            <div class="mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="u_email" class="form-control" placeholder="example@mail.com" required>
                            </div>
                            <button type="submit" name="send_link" class="btn btn-dark w-100">Send Reset Link</button>
                        </form>
                        
                        <div class="text-center mt-3">
                            <a href="login.php" class="text-decoration-none text-muted small">Back to Login</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>