<?php 
    // Go up one directory to find the config folder
    require('../config/db_config.php');
    session_start();

    // Check if user is logged in using your specific session variable
    if(!(isset($_SESSION['u_id']))){
        header("Location: index.php");
        exit;
    }

    $u_id = $_SESSION['u_id']; //

    // 1. Fetch profile picture name to remove the file from the server
    $res = mysqli_query($conn, "SELECT `profile_pic` FROM `users` WHERE `u_id`='$u_id' LIMIT 1");
    $data = mysqli_fetch_assoc($res);

    // 2. Delete the physical image file if it's not the default placeholder
    if($data['profile_pic'] != 'default_user.jpg' && !empty($data['profile_pic'])){
        $path = "images/users/".$data['profile_pic'];
        if(file_exists($path)){
            unlink($path);
        }
    }

    // 3. Delete the user record from the database
    // Note: If you have a bookings table, you might want to delete user bookings first 
    // or use ON DELETE CASCADE in your database foreign keys.
    $q = "DELETE FROM `users` WHERE `u_id`='$u_id'";
    
    if(mysqli_query($conn, $q)){
        // Clear all session variables and destroy the session
        session_unset();
        session_destroy();
        
        echo "<script>
            alert('Your account and personal data have been permanently removed.');
            window.location.href='index.php';
        </script>";
    } else {
        echo "<script>
            alert('Error: Could not complete the request. Please contact support.');
            window.location.href='profile.php';
        </script>";
    }
?>