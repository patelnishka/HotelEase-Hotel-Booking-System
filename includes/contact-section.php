<?php
include_once('../config/db_config.php');

$alert_message = "";

if (isset($_POST['send_inquiry'])) {
    $name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['user_email']);
    $subject = mysqli_real_escape_string($conn, $_POST['subject_line']);
    $message = mysqli_real_escape_string($conn, $_POST['message_body']);

    $query = "INSERT INTO `contact_queries` (`name`, `email`, `subject`, `message`) 
              VALUES ('$name', '$email', '$subject', '$message')";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Your query is successfully sent!'); window.location.href=window.location.href;</script>";
    } else {
        $alert_message = "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>";
    }
}
?>

<div class="contact-container container py-5">
    <?php echo $alert_message; ?>
    
    <div class="contact-header text-center mb-5">
        <h2 class="fw-bold">Contact Us</h2>
        <p class="text-muted">Reach out to our 24/7 support team.</p>
    </div>

    <div class="row g-4 mt-2">
        <div class="col-md-6">
            <div class="map-box shadow" style="height: 450px; width: 100%; background: #eee; border-radius: 15px; overflow: hidden;">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3671.69791572441!2d72.54146011496733!3d23.034856384947934!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x395e84f0430e380f%3A0xc3f9b233a040b2f1!2sHotel%20Ease!5e0!3m2!1sen!2sin!4v1644154823291!5m2!1sen!2sin" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
            </div>
        </div>

        <div class="col-md-6">
            <div class="contact-card shadow p-4 bg-white" style="border-radius: 15px;">
                <h4 class="mb-4 fw-bold">Send a Message</h4>
                
                <form id="contactForm" method="POST" onsubmit="return validateForm()">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="full_name" id="c_name" class="form-control shadow-none" placeholder="Your Name">
                        <small class="error-text text-danger" id="nameError"></small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="user_email" id="c_email" class="form-control shadow-none" placeholder="Email Address">
                        <small class="error-text text-danger" id="emailError"></small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Subject</label>
                        <input type="text" name="subject_line" id="c_subject" class="form-control shadow-none" placeholder="How can we help?">
                        <small class="error-text text-danger" id="subjectError"></small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Message</label>
                        <textarea name="message_body" id="c_message" rows="4" class="form-control shadow-none" placeholder="Type your message..."></textarea>
                        <small class="error-text text-danger" id="messageError"></small>
                    </div>

                    <button type="submit" name="send_inquiry" class="btn btn-primary w-100 py-2 fw-bold">Send Message</button>
                </form>
            </div>
        </div>
    </div>

    <div class="row justify-content-center mt-5">
        <div class="col-md-10">
            <div class="card border-0 shadow p-4" style="border-radius: 15px; border-top: 5px solid #0d6efd !important;">
                <h5 class="fw-bold mb-3"><i class="fas fa-search-envelope me-2"></i>Check Admin Response</h5>
                <p class="text-muted small mb-4">Have you already sent a query? Enter your email below to check for our reply.</p>
                
                <form action="contact.php" method="GET" class="row g-2">
                    <div class="col-sm-9">
                        <input type="email" name="check_email" class="form-control shadow-none" placeholder="Enter your email address" required>
                    </div>
                    <div class="col-sm-3">
                        <button type="submit" name="check_status" class="btn btn-dark w-100 fw-bold">Check Status</button>
                    </div>
                </form>

                <div id="status-result" class="mt-4">
                    <?php 
                    if(isset($_GET['check_status'])) {
                        $email = mysqli_real_escape_string($conn, $_GET['check_email']);
                        $res = mysqli_query($conn, "SELECT * FROM `contact_queries` WHERE `email`='$email' ORDER BY `id` DESC LIMIT 1");
                        
                        if($row = mysqli_fetch_assoc($res)) {
                            if($row['status'] == 1) {
                                echo "
                                <div class='alert alert-success border-0 shadow-sm p-4 position-relative'>
                                    <a href='contact.php' class='btn-close position-absolute top-0 end-0 m-3' aria-label='Close'></a>
                                    <h6 class='fw-bold mb-2 text-dark'><i class='fas fa-user-tie me-2'></i>Admin's Reply to your message:</h6>
                                    <p class='mb-3 text-dark' style='font-style: italic;'>\"$row[admin_reply]\"</p>
                                    <div class='d-flex justify-content-between align-items-center border-top pt-2 mt-2'>
                                        <small class='text-muted'>Original Subject: $row[subject]</small>
                                        <div class='d-flex gap-2 align-items-center'>
                                            <small class='badge bg-success'>Replied</small>
                                            <a href='contact.php' class='btn btn-sm btn-outline-success py-0 px-2' style='font-size: 0.75rem;'>OK, Clear</a>
                                        </div>
                                    </div>
                                </div>";
                            } else {
                                echo "
                                <div class='alert alert-info border-0 shadow-sm position-relative'>
                                    <a href='contact.php' class='btn-close position-absolute top-0 end-0 m-2' aria-label='Close' style='font-size: 0.8rem;'></a>
                                    <i class='fas fa-clock me-2'></i> Your query is currently <strong>Pending</strong>. Our team will respond soon!
                                </div>";
                            }
                        } else {
                            echo "
                            <div class='alert alert-danger border-0 shadow-sm position-relative'>
                                <a href='contact.php' class='btn-close position-absolute top-0 end-0 m-2' aria-label='Close' style='font-size: 0.8rem;'></a>
                                <i class='fas fa-times-circle me-2'></i> No records found for this email address.
                            </div>";
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// --- URL CLEANUP SCRIPT ---
// This removes '?check_email=...' from the URL so refresh doesn't keep showing the result.
if (window.history.replaceState && window.location.search.includes('check_status')) {
    const cleanUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
    window.history.replaceState({path: cleanUrl}, '', cleanUrl);
}

function validateForm() {
    let isValid = true;

    let name = document.getElementById("c_name").value.trim();
    let email = document.getElementById("c_email").value.trim();
    let subject = document.getElementById("c_subject").value.trim();
    let message = document.getElementById("c_message").value.trim();

    document.getElementById("nameError").innerText = "";
    document.getElementById("emailError").innerText = "";
    document.getElementById("subjectError").innerText = "";
    document.getElementById("messageError").innerText = "";

    if (name === "") {
        document.getElementById("nameError").innerText = "Name is required";
        isValid = false;
    }

    let emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (email === "") {
        document.getElementById("emailError").innerText = "Email is required";
        isValid = false;
    } else if (!emailPattern.test(email)) {
        document.getElementById("emailError").innerText = "Invalid email format";
        isValid = false;
    }

    if (subject === "") {
        document.getElementById("subjectError").innerText = "Subject is required";
        isValid = false;
    }

    if (message === "") {
        document.getElementById("messageError").innerText = "Message cannot be empty";
        isValid = false;
    }

    return isValid;
}
</script>