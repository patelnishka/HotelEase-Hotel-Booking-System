<?php 
  session_start();
  require_once('../config/db_config.php');
  include('../includes/header.php'); 

  if(!isset($_SESSION['u_id'])){
      echo "<script>window.location.href='login.php';</script>"; exit;
  }

  if(!isset($_GET['id'])){
      echo "<script>window.location.href='rooms.php';</script>"; exit;
  }

  $room_id = mysqli_real_escape_string($conn, $_GET['id']);
  $room_res = mysqli_query($conn, "SELECT * FROM `rooms` WHERE `id`='$room_id' AND `status`='1'");
  
  if(mysqli_num_rows($room_res) == 0){
      echo "<script>window.location.href='rooms.php';</script>"; exit;
  }

  $room_data = mysqli_fetch_assoc($room_res);

  // Fetch User Details to pre-fill the form
  $u_id = $_SESSION['u_id'];
  $user_res = mysqli_query($conn, "SELECT * FROM `users` WHERE `u_id`='$u_id' LIMIT 1");
  $user_data = mysqli_fetch_assoc($user_res);
?>

<div class="container mt-5">
    <div class="row">

        <div class="col-12 mb-4 px-4">
            <h2 class="fw-bold h-font">CONFIRM BOOKING</h2>
            <div style="font-size: 14px;">
                <a href="index.php" class="text-secondary text-decoration-none">HOME</a>
                <span class="text-secondary"> > </span>
                <a href="rooms.php" class="text-secondary text-decoration-none">ROOMS</a>
                <span class="text-secondary"> > </span>
                <span class="text-secondary">CONFIRM</span>
            </div>
        </div>

        <div class="col-lg-7 col-md-12 px-4">
            <div class="card p-3 shadow-sm rounded-4 border-0 mb-4">
                <?php 
                    $room_thumb = "room_sample.jpg"; 
                    $thumb_q = mysqli_query($conn, "SELECT * FROM `room_images` WHERE `room_id`='$room_data[id]' AND `thumb`='1'");
                    if(mysqli_num_rows($thumb_q) > 0){
                        $thumb_res = mysqli_fetch_assoc($thumb_q);
                        $room_thumb = $thumb_res['image'];
                    }
                ?>
                <img src="../assets/images/rooms/<?php echo $room_thumb ?>" class="img-fluid rounded-3 mb-3" style="height: 350px; object-fit: cover;">
                <h4 class="fw-bold"><?php echo $room_data['name'] ?></h4>
                <h5 class="text-dark">₹<?php echo $room_data['price'] ?> <small class="text-muted">/ per night</small></h5>
                <hr>
                <p class="text-muted"><?php echo $room_data['description'] ?></p>
            </div>
        </div>

        <div class="col-lg-5 col-md-12 px-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                        <form action="pay_now.php" method="POST" id="booking_form">
                            <input type="hidden" name="room_id" value="<?php echo $room_data['id'] ?>">
                            <h5 class="mb-3 fw-bold"><i class="bi bi-person-bounding-box me-2"></i>Personal Details</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-secondary small fw-bold">Full Name</label>
                                <input name="name" type="text" value="<?php echo $user_data['u_name'] ?>" class="form-control shadow-none bg-light" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-secondary small fw-bold">Phone Number</label>
                                <input name="phone" type="number" value="<?php echo $user_data['u_phone'] ?>" class="form-control shadow-none bg-light" readonly>
                            </div>
                            <div class="col-12 mb-4">
                                <label class="form-label text-secondary small fw-bold">Email Address</label>
                                <input name="email" type="email" value="<?php echo $user_data['u_email'] ?>" class="form-control shadow-none bg-light" readonly>
                            </div>

                            <h5 class="mb-3 fw-bold"><i class="bi bi-calendar-check me-2"></i>Stay Duration</h5>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-secondary small fw-bold">Check-in</label>
                                <input name="checkin" type="date" id="checkin_input" onchange="check_availability()" class="form-control shadow-none border-dark" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-secondary small fw-bold">Check-out</label>
                                <input name="checkout" type="date" id="checkout_input" onchange="check_availability()" class="form-control shadow-none border-dark" required>
                            </div>
                            
                            <div class="col-12 mt-3">
                                <div id="status_msg" class="mb-3"></div>
                                
                                <div class="bg-dark text-white p-4 rounded-4 mb-3 shadow-sm">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Total Stay:</span>
                                        <span id="total_amt" class="fw-bold">₹0</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-warning">10% Advance (Pay Now):</span>
                                        <span id="advance_amt" class="fw-bold text-warning">₹0</span>
                                    </div>
                                    <hr>
                                    <p class="mb-0 small opacity-75">* This 10% advance is non-refundable and secures your room immediately.</p>
                                </div>
                                
<button type="submit" name="pay_now" id="pay_btn" class="btn btn-warning w-100 fw-bold py-3 text-uppercase shadow-none" style="letter-spacing: 1px;" disabled>Confirm & Pay Now</button>                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    // Set Minimum Date to Today
    let today = new Date().toISOString().split('T')[0];
    document.getElementById('checkin_input').setAttribute('min', today);
    document.getElementById('checkout_input').setAttribute('min', today);

    let booking_form = document.getElementById('booking_form');
    let pay_btn = document.getElementById('pay_btn');
    let status_msg = document.getElementById('status_msg');
    let room_price = <?php echo $room_data['price'] ?>;

    function check_availability() {
    let checkin_val = booking_form.elements['checkin'].value;
    let checkout_val = booking_form.elements['checkout'].value;

    if (checkin_val != '' && checkout_val != '') {
        status_msg.innerHTML = "<div class='alert alert-info py-2 m-0'>Checking availability...</div>";
        pay_btn.disabled = true;

        let data = new FormData();
        data.append('check_availability', '');
        data.append('checkin', checkin_val);
        data.append('checkout', checkout_val);
        data.append('room_id', <?php echo $room_data['id'] ?>);

        fetch('ajax/booking_helper.php', {
            method: 'POST',
            body: data
        })
        .then(res => res.json())
        .then(data => {
            if(data.status == 'available') {
                status_msg.innerHTML = "<div class='alert alert-success py-2 m-0'>Room is available for these dates!</div>";
                pay_btn.disabled = false;
                
                // Final Price Calculation
                let nights = (new Date(checkout_val) - new Date(checkin_val)) / (24 * 60 * 60 * 1000);
                document.getElementById('total_amt').innerText = "₹" + (nights * room_price);
                document.getElementById('advance_amt').innerText = "₹" + (nights * room_price * 0.10).toFixed(2);
            } else {
                status_msg.innerHTML = "<div class='alert alert-danger py-2 m-0'>Sorry, room already booked for these dates.</div>";
                pay_btn.disabled = true;
            }
        });
    }
}
</script>

<?php include('../includes/footer.php'); ?>