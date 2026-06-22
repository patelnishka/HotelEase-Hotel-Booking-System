<?php require('../config/db_config.php');?>
<div class="section-header">
    <h2>Our Gallery</h2>
    <p>A visual journey through luxury and comfort at HotelEase.</p>
</div>
    <section class="gallery-section container pb-5">
    <div class="row g-4">
        <?php 
            $res = mysqli_query($conn, "SELECT * FROM `gallery` ORDER BY `g_id` DESC");
            while($data = mysqli_fetch_assoc($res)) {
                echo "
                <div class='col-lg-4 col-md-6'>
                    <div class='gallery-card shadow-sm'>
                        <div class='gallery-img-wrapper'>
                            <img src='../assets/images/$data[g_img]' alt='$data[g_desc]' class='img-fluid'>
                            <div class='gallery-overlay'>
                                <div class='overlay-text'>
                                    <h4>$data[g_desc]</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                ";
            }
        ?>
    </div>
</section>
