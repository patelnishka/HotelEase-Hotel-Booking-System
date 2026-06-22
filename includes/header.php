<?php
// We only start session if it hasn't been started already
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<?php 
    // Get the current file name
    $current_file = basename($_SERVER['PHP_SELF']);
    // Decide which class to add to the body
    $body_class = ($current_file == 'index.php') ? 'home-page' : 'inner-page';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>HotelEase | Luxury Stay</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="<?php echo $body_class; ?>">
<nav class="navbar navbar-expand-lg fixed-top py-3 bg-white shadow-sm">
    <div class="container">

        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <img src="../assets/images/logo.jpg" alt="Logo" style="height: 50px;">
        </a>

        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link px-3" href="../public/index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="../public/about.php">About</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="../public/rooms.php">Rooms</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="../public/services.php">Services</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="../public/gallery.php">Gallery</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="../public/contact.php">Contact</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="../public/feedback.php">Feedback</a></li>
            </ul>

            <div class="d-flex align-items-center gap-2 nav-auth-btns">
                
                <?php if(isset($_SESSION['u_id'])): ?>
                    
                    <div class="dropdown">
                        <button class="btn btn-warning dropdown-toggle fw-bold" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-user-circle me-1"></i> Hi, <?php echo $_SESSION['u_name']; ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="userMenu">
                            <li><a class="dropdown-item" href="profile.php"><i class="fa-solid fa-id-card me-2"></i>My Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="logout.php"><i class="fa-solid fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </div>

                <?php else: ?>
                    <a href="../public/login.php" class="btn btn-outline-dark px-4 border-0 fw-semibold">
                        <i class="fa-solid fa-right-to-bracket me-1"></i> Login
                    </a>
                    <a href="../public/registration.php" class="btn btn-warning px-4 shadow-sm fw-bold">
                        <i class="fa-solid fa-user-plus"></i>
                        Register
                    </a>
                <?php endif; ?>

            </div>
        </div>
    </div>
</nav>

<!-- <div style="margin-top: 90px;"></div> -->

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>