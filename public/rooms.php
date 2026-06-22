<<?php 
  session_start(); // MUST BE LINE 1
  require_once('../config/db_config.php');
  include('../includes/header.php'); 
?>
<?php include('../includes/rooms-section.php'); ?>

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