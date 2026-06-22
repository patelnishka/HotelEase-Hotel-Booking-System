<div class="my-5 px-4">
    <h2 class="fw-bold h-font text-center">OUR ROOMS</h2>
    <div class="h-line bg-dark"></div>
</div>

<div class="container">
    <div class="row">
        <?php
            // Fetch only active rooms
            $room_res = mysqli_query($conn, "SELECT * FROM `rooms` WHERE `status`='1' ORDER BY `id` DESC");

            while ($room_data = mysqli_fetch_assoc($room_res)) {
                
                // 1. Fetch Features
                $fea_q = mysqli_query($conn, "SELECT f.f_name FROM `features` f 
                    INNER JOIN `room_features` rfea ON f.f_id = rfea.f_id 
                    WHERE rfea.room_id = '$room_data[id]'");

                $features_data = "";
                while ($fea_row = mysqli_fetch_assoc($fea_q)) {
                    $features_data .= "<span class='badge rounded-pill bg-light text-dark text-wrap me-1 mb-1'>$fea_row[f_name]</span>";
                }

                // 2. Fetch Thumbnail
                $room_thumb = "room_sample.jpg"; 
                $thumb_q = mysqli_query($conn, "SELECT * FROM `room_images` 
                    WHERE `room_id`='$room_data[id]' AND `thumb`='1'");

                if (mysqli_num_rows($thumb_q) > 0) {
                    $thumb_res = mysqli_fetch_assoc($thumb_q);
                    $room_thumb = $thumb_res['image'];
                }

                // 3. THE FIX: Check for u_id session
                // If u_id exists in the session, we pass 1 to the JS function
                $login = 0;
                if(isset($_SESSION['u_id'])){
                    $login = 1;
                }

                echo <<<data
                <div class="col-lg-12 col-md-12 px-4">
                    <div class="card mb-4 border-0 shadow">
                        <div class="row g-0 p-3 align-items-center">
                            <div class="col-md-5 mb-lg-0 mb-md-0 mb-3 text-center">
                                <img src="../assets/images/rooms/$room_thumb" class="img-fluid rounded" style="width: 100%; height: 250px; object-fit: cover;">
                            </div>
                            <div class="col-md-5 px-lg-3 px-md-3 px-0">
                                <h5 class="mb-3">$room_data[name]</h5>
                                <div class="features mb-3">
                                    <h6 class="mb-1">Features</h6>
                                    $features_data
                                </div>
                                <div class="guests mb-3">
                                    <h6 class="mb-1">Guests</h6>
                                    <span class="badge rounded-pill bg-light text-dark">$room_data[adult] Adults</span>
                                    <span class="badge rounded-pill bg-light text-dark">$room_data[children] Children</span>
                                </div>
                            </div>
                            <div class="col-md-2 mt-lg-0 mt-md-0 mt-4 text-center">
                                <h6 class="mb-4">₹$room_data[price] per night</h6>
                                <a href="room_details.php?id=$room_data[id]" class="btn btn-sm w-100 btn-outline-dark shadow-none mb-2">More Details</a>
                                <button onclick='checkLoginToBook($login, $room_data[id])' class="btn btn-sm w-100 btn-dark shadow-none">Book Now</button>
                            </div>
                        </div>
                    </div>
                </div>
data;
            }
        ?>
    </div>
</div>