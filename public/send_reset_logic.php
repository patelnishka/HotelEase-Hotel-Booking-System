<?php
// 1. Include PHPMailer files (Update these paths if your folder name is different)
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include_once('../config/db_config.php');
date_default_timezone_set('Asia/Kolkata'); 

if(isset($_POST['send_link'])) {
    $email = mysqli_real_escape_string($conn, $_POST['u_email']);

    // Check if user exists
    $res = mysqli_query($conn, "SELECT * FROM `users` WHERE `u_email` = '$email'");

    if(mysqli_num_rows($res) > 0) {
        
        $token = bin2hex(random_bytes(16)); 
        $expiry = date("Y-m-d H:i:s", strtotime("+15 minutes"));

        // Clean up old tokens
        mysqli_query($conn, "DELETE FROM `password_resets` WHERE `email` = '$email'");

        // Insert new token
        $query = "INSERT INTO `password_resets` (`email`, `token`, `expiry`) 
                  VALUES ('$email', '$token', '$expiry')";
        
        if(mysqli_query($conn, $query)) {
            
            // --- START OF EMAIL SENDING ---
            $mail = new PHPMailer(true);

            try {
                // SMTP Server settings
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'nishkapatel2003@gmail.com'; // CHANGE THIS
                $mail->Password   = 'tpbwqblgnnfuameu'; 
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                // XAMPP SSL Certificate Fix
                $mail->SMTPOptions = array(
                    'ssl' => array(
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    )
                );

                // Recipients
                $mail->setFrom('nishkapatel2003@gmail.com', 'Hotel Management'); // CHANGE THIS
                $mail->addAddress($email);

                // Email Content
                $mail->isHTML(true);
                $mail->Subject = 'Password Reset Link';
                
                // Replace 'hotel_ease_bs' if your folder name is different
                $reset_link = "http://localhost/hotel_ease_bs/public/reset_password.php?token=$token&email=$email";
                
                $mail->Body = "
                    <div style='font-family: Arial, sans-serif; border: 1px solid #ddd; padding: 20px; max-width: 600px;'>
                        <h2 style='color: #333;'>Reset Your Password</h2>
                        <p>Click the button below to set a new password. This link is valid for 15 minutes.</p>
                        <a href='$reset_link' style='background: #000; color: #fff; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>Reset Password</a>
                    </div>";

                $mail->send();
                echo "<script>alert('Reset link has been sent to your email!'); window.location.href='forgot_password.php';</script>";
                
            } catch (Exception $e) {
               
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
         
        }

    } else {
        echo "<script>alert('Email not found!'); window.location.href='forgot_password.php';</script>";
    }
}
?>