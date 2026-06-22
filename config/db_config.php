<?php
// 1. Database Connection Settings
$hname = 'localhost';
$uname = 'root';
$pass = '';
$db = 'hotel_ease_db'; // Ensure this matches your database name

$conn = mysqli_connect($hname, $uname, $pass, $db);

// Check if connection was successful
if (!$conn) {
    die("Cannot connect to database: " . mysqli_connect_error());
}

// 2. Define Site Paths
// This is the "Single Source of Truth" for your images
if (!defined('SITE_URL')) {
    define('SITE_URL', 'http://localhost/hotel_ease_bs/');
}

if (!defined('ROOMS_IMG_PATH')) {
    // This path is for the browser to display images
    define('ROOMS_IMG_PATH', SITE_URL . 'assets/images/rooms/');
}

// 3. Define Absolute Path for Uploading
if (!defined('UPLOAD_IMAGE_PATH')) {
    // This is for PHP to move files from your PC to the folder
    define('UPLOAD_IMAGE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/hotel_ease_bs/assets/images/rooms/');
}

// 4. Start Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>