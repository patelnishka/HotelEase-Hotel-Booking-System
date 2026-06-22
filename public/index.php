<?php 
  session_start(); // MUST BE LINE 1
  require_once('../config/db_config.php');
  include('../includes/header.php'); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HotelEase | Luxury Stay</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="home-hero-wrapper">
    <div id="hotelHeroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <div class="hero-overlay"></div> 
                <img src="../assets/images/slider1.jpg" class="d-block w-100 hero-img" alt="Luxury Room">
                <div class="carousel-caption">
                    <h1 class="display-3 fw-bold">Welcome to Our Luxury Hotel</h1>
                    <p class="lead">Your comfort is our priority — book your perfect stay today.</p>
                    <a href="rooms.php" class="btn btn-warning btn-lg fw-bold mt-3 px-5 py-3 shadow">Explore Rooms</a>
                </div>
            </div>
            <div class="carousel-item">
                <div class="hero-overlay"></div>
                <img src="../assets/images/slider2.jpg" class="d-block w-100 hero-img" alt="Hotel View">
                <div class="carousel-caption">
                    <h1 class="display-3 fw-bold">Where Every Moment Becomes a Memory</h1>
                    <p class="lead">Discover a peaceful escape with premium amenities.</p>
                    <a href="rooms.php" class="btn btn-warning btn-lg fw-bold mt-3 px-5 py-3 shadow">Explore Rooms</a>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#hotelHeroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#hotelHeroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>
</div>

<?php
include('../includes/about-section.php');
include('../includes/rooms-section.php');
include('../includes/services-section.php');
include('../includes/gallery-section.php');
include('../includes/feedback-section.php');
include('../includes/contact-section.php');
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function checkLoginToBook(login_status, room_id) {
        if (login_status == 1) {
            window.location.href = 'confirm_booking.php?id=' + room_id;
        } else {
            alert('Please login to book a room!');
            window.location.href = 'login.php';
        }
    }
</script>
<?php include('../includes/footer.php'); ?>

</body>
</html>