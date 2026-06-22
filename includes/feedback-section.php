<?php
global $conn;
$root = dirname(__DIR__);
if (!$conn) { include_once($root . '/config/db_config.php'); }

// Get current filename to check if we are on index.php
$current_page = basename($_SERVER['PHP_SELF']);
$is_logged_in = isset($_SESSION['u_id']) || isset($_SESSION['user_id']);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_feedback'])) {
    $session_id = $_SESSION['u_id'] ?? $_SESSION['user_id'] ?? null;

    if ($session_id) {
        $u_id = $session_id;
        $rating = mysqli_real_escape_string($conn, $_POST['rating']);
        $message = mysqli_real_escape_string($conn, $_POST['message']);
        $message = substr($message, 0, 150); 

        $sql = "INSERT INTO feedback (u_id, rating, message) VALUES ('$u_id', '$rating', '$message')";
        
        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('Feedback posted!'); window.location.href='index.php';</script>";
            exit();
        }
    }
}
?>

<style>
    /* Styling for the swiper/carousel */
    .feedback-carousel .carousel-item {
        padding: 10px;
    }
    .carousel-control-prev, .carousel-control-next {
    z-index: 10; /* Ensures buttons stay on top of the cards */
}
    .avatar-circle {
        width: 45px; height: 45px; 
        background: #5b2c83; color: white; 
        border-radius: 50%; display: flex; 
        align-items: center; justify-content: center; 
        font-weight: bold; font-size: 1.1rem;
    }
    .feedback-card {
        transition: transform 0.3s ease;
        border-radius: 15px;
        min-height: 180px;
    }
    .feedback-card:hover {
        transform: translateY(-5px);
    }
    /* Hide carousel indicators on small screens if desired */
    .carousel-indicators [button] {
        background-color: #5b2c83;
    }
</style>

<section class="feedback-section container py-5" id="feedback-section">
    <div class="text-center mb-5">
        <h2 class="fw-bold">Guest Experience</h2>
        <hr class="mx-auto" style="width: 50px; height: 3px; background: #5b2c83;">
    </div>

    <div class="row g-5">
        <?php if ($is_logged_in || $current_page !== 'index.php'): ?>
        <div class="col-lg-5">
            <?php if ($is_logged_in): ?>
                <div class="card border-0 shadow-sm p-4 feedback-card">
                    <h5 class="fw-bold mb-3">Leave a Review</h5>
                    <form action="" method="POST">
                        <div class="mb-3">
                            <label class="small fw-bold">Your Rating</label>
                            <select name="rating" class="form-select border-0 bg-light">
                                <option value="5">⭐⭐⭐⭐⭐ (Excellent)</option>
                                <option value="4">⭐⭐⭐⭐ (Good)</option>
                                <option value="3">⭐⭐⭐ (Average)</option>
                                <option value="2">⭐⭐ (Below Average)</option>
                                <option value="1">⭐ (Poor)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="small fw-bold">Message (Max 150)</label>
                            <textarea name="message" class="form-control border-0 bg-light" rows="4" maxlength="150" placeholder="How was your stay?" required></textarea>
                        </div>
                        <button type="submit" name="submit_feedback" class="btn btn-feedback w-100 py-2" style="background: #5b2c83; color: white; border-radius: 10px;">Post Review</button>
                    </form>
                </div>
            <?php else: ?>
                <div class="card border-0 shadow-sm p-5 text-center feedback-card">
                    <div class="mb-3"><i class="fa-solid fa-lock fa-3x" style="color: #5b2c83;"></i></div>
                    <h5 class="fw-bold">Login Required</h5>
                    <p class="text-muted small">Only registered guests can share their experience.</p>
                    <a href="login.php" class="btn btn-outline-dark btn-sm rounded-pill px-4">Login to Review</a>
                </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="<?php echo (!$is_logged_in && $current_page == 'index.php') ? 'col-lg-12' : 'col-lg-7'; ?>">
            
            <div id="feedbackCarousel" class="carousel slide feedback-carousel" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <?php
                    // Adjust limit based on page/login status
                    $limit = (!$is_logged_in && $current_page == 'index.php') ? 6 : 4;
                    
                    $query = "SELECT feedback.*, users.u_name FROM feedback 
                              JOIN users ON feedback.u_id = users.u_id 
                              ORDER BY created_at DESC LIMIT $limit";
                    $result = mysqli_query($conn, $query);

                    if ($result && mysqli_num_rows($result) > 0):
                        $feedbacks = mysqli_fetch_all($result, MYSQLI_ASSOC);
                        
                        // If logged out on home, we show 2 items per slide (grid style)
                        // Otherwise, we show 1 item per slide (sidebar style)
                        $items_per_slide = (!$is_logged_in && $current_page == 'index.php') ? 2 : 1;
                        $chunks = array_chunk($feedbacks, $items_per_slide);

                        foreach ($chunks as $index => $chunk):
                    ?>
                        <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                            <div class="row">
                                <?php foreach ($chunk as $row): ?>
                                <div class="col-md-<?php echo 12 / $items_per_slide; ?> mb-3">
                                    <div class="card border-0 shadow-sm p-3 h-100 feedback-card">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="avatar-circle">
                                                <?php echo strtoupper(substr($row['u_name'], 0, 1)); ?>
                                            </div>
                                            <div class="ms-3">
                                                <h6 class="mb-0 fw-bold"><?php echo htmlspecialchars($row['u_name']); ?></h6>
                                                <div class="text-warning small">
                                                    <?php echo str_repeat('⭐', $row['rating']); ?>
                                                </div>
                                            </div>
                                            <div class="ms-auto">
                                                <small class="text-muted" style="font-size: 0.7rem;">
                                                    <?php echo date('M d', strtotime($row['created_at'])); ?>
                                                </small>
                                            </div>
                                        </div>
                                        <p class="mb-0 text-muted fst-italic" style="font-size: 0.9rem;">
                                            "<?php echo htmlspecialchars($row['message']); ?>"
                                        </p>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; else: ?>
                        <div class="text-center py-5">
                            <p class="text-muted">No reviews yet. Stay with us and be the first!</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <button class="carousel-control-prev" type="button" data-bs-target="#feedbackCarousel" data-bs-slide="prev" style="width: 5%; filter: invert(1);">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Previous</span>
</button>
<button class="carousel-control-next" type="button" data-bs-target="#feedbackCarousel" data-bs-slide="next" style="width: 5%; filter: invert(1);">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Next</span>
</button>
            </div>
        </div>
    </div>
</section>