<?php require('../config/db_config.php');?>

<section class="services-wrapper">

    <div class="services-header">
        <h2>Our Services</h2>
        <p>Comfort, convenience and luxury for a perfect stay</p>
    </div>

    <div class="services-grid">
        <?php 
            // 2. Fetch Services from Database
            $res = mysqli_query($conn, "SELECT * FROM `services` ORDER BY `s_id` DESC");
            
            // 3. Loop through each service
            if(mysqli_num_rows($res) > 0) {
                while($data = mysqli_fetch_assoc($res)) {
                    ?>
                    <div class="service-card">
                        <div class="service-text">
                            <h3><?php echo $data['s_name']; ?></h3>
                            <p><?php echo $data['s_desc']; ?></p>
                        </div>
                        <div class="service-icon">
                            <i class="fa-solid <?php echo $data['s_icon']; ?>"></i>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "<p class='text-center'>No services available at the moment.</p>";
            }
        ?>
    </div>
</section>