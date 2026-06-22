<?php 
  session_start();
  require_once('../config/db_config.php');
  include('../includes/header.php'); 

  // Check for room id in URL
  if(!isset($_GET['id'])){
      echo "<script>window.location.href='rooms.php';</script>";
      exit;
  }

  $room_id = mysqli_real_escape_string($conn, $_GET['id']);
  
  // Fetch specific room data
  $room_res = mysqli_query($conn, "SELECT * FROM `rooms` WHERE `id`='$room_id' AND `status`='1'");

  if(mysqli_num_rows($room_res) == 0){
      echo "<script>window.location.href='rooms.php';</script>";
      exit;
  }

  $room_data = mysqli_fetch_assoc($room_res);
?>

<div class="container mt-5">
    <div class="row">

        <div class="col-12 my-5 mb-4 px-4">
            <h2 class="fw-bold"><?php echo $room_data['name'] ?></h2>
            <div style="font-size: 14px;">
                <a href="index.php" class="text-secondary text-decoration-none">HOME</a>
                <span class="text-secondary"> > </span>
                <a href="rooms.php" class="text-secondary text-decoration-none">ROOMS</a>
            </div>
        </div>

        <div class="col-lg-7 col-md-12 px-4">
            <div id="roomCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <?php 
                        $img_q = mysqli_query($conn, "SELECT * FROM `room_images` WHERE `room_id`='$room_data[id]'");
                        if(mysqli_num_rows($img_q) > 0){
                            $i = 0;
                            while($img_res = mysqli_fetch_assoc($img_q)){
                                $active = ($i == 0) ? "active" : "";
                                echo "
                                <div class='carousel-item $active'>
                                    <img src='../assets/images/rooms/$img_res[image]' class='d-block w-100 rounded' style='height: 450px; object-fit: cover;'>
                                </div>";
                                $i++;
                            }
                        } else {
                            echo "<div class='carousel-item active'><img src='../assets/images/rooms/room_sample.jpg' class='d-block w-100 rounded'></div>";
                        }
                    ?>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#roomCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#roomCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                </button>
            </div>
        </div>

        <div class="col-lg-5 col-md-12 px-4">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body">
                    <?php 
                        echo "<h4>₹$room_data[price] per night</h4>";

                        // Features
                        $fac_q = mysqli_query($conn, "SELECT f.f_name FROM `features` f 
                            INNER JOIN `room_features` rfea ON f.f_id = rfea.f_id 
                            WHERE rfea.room_id = '$room_data[id]'");

                        echo "<div class='mb-3 mt-3'><h6 class='mb-1'>Features</h6>";
                        while($fac_row = mysqli_fetch_assoc($fac_q)){
                            echo "<span class='badge rounded-pill bg-light text-dark text-wrap me-1 mb-1'>$fac_row[f_name]</span>";
                        }
                        echo "</div>";

                        echo "
                        <div class='mb-3'>
                            <h6 class='mb-1'>Area</h6>
                            <span class='badge rounded-pill bg-light text-dark'>$room_data[area] sq. ft.</span>
                        </div>
                        <div class='mb-4'>
                            <h6 class='mb-1'>Guests</h6>
                            <span class='badge rounded-pill bg-light text-dark'>$room_data[adult] Adults</span>
                            <span class='badge rounded-pill bg-light text-dark'>$room_data[children] Children</span>
                        </div>
                        ";

                        $login = (isset($_SESSION['u_id'])) ? 1 : 0;
                        echo "<button onclick='checkLoginToBook($login, $room_data[id])' class='btn w-100 btn-dark shadow-none mb-1'>Book Now</button>";
                    ?>
                </div>
            </div>
        </div>

        <div class="col-12 mt-4 px-4">
            <div class="mb-5">
                <h5>Description</h5>
                <p><?php echo $room_data['description'] ?></p>
            </div>
        </div>

    </div>
</div>

<?php include('../includes/footer.php'); ?>

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