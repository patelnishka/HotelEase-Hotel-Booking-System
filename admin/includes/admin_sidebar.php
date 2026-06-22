<?php 
    // Get the current file name (e.g., 'users.php')
    $current_page = basename($_SERVER['SCRIPT_NAME']); 
?>
<aside class="sidebar">
    <div class="sidebar-header py-3 border-bottom border-secondary mb-3">
        <h5 class="text-white text-center m-0" style="letter-spacing: 1px;">ADMIN PANEL</h5>
    </div>

    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>" href="dashboard.php">
                <i class="fas fa-chart-line"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo ($current_page == 'users.php') ? 'active' : ''; ?>" href="users.php">
                <i class="fas fa-users"></i> Manage Users
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo ($current_page == 'gallery.php') ? 'active' : ''; ?>" href="gallery.php">
                <i class="fas fa-images"></i> Manage Gallery
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo ($current_page == 'services.php') ? 'active' : ''; ?>" href="services.php">
                <i class="fas fa-concierge-bell"></i> Manage Services
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo ($current_page == 'rooms.php') ? 'active' : ''; ?>" href="rooms.php">
                <i class="fas fa-bed"></i> Manage Rooms
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo ($current_page == 'features.php') ? 'active' : ''; ?>" href="features.php">
                <i class="fas fa-star"></i> Room Features
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo ($current_page == 'room_units.php') ? 'active' : ''; ?>" href="room_units.php">
                <i class="fas fa-door-open"></i> Room Availability
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo ($current_page == 'booking.php') ? 'active' : ''; ?>" href="booking.php">
                <i class="fas fa-calendar-check"></i> Manage Bookings
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo ($current_page == 'view_payments.php') ? 'active' : ''; ?>" href="view_payments.php">
                <i class="fas fa-credit-card"></i> View Payments
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo ($current_page == 'view_invoices.php') ? 'active' : ''; ?>" href="view_invoices.php">
                <i class="fas fa-file-invoice-dollar"></i> Invoices
            </a>
        </li>
        
        
        <li class="nav-item">
            <a class="nav-link <?php echo ($current_page == 'feedback.php') ? 'active' : ''; ?>" href="feedback.php">
                <i class="fas fa-comments"></i> Manage Feedback
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo ($current_page == 'contact.php') ? 'active' : ''; ?>" href="contact.php">
                <i class="fas fa-envelope"></i> Contact Us
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo ($current_page == 'reports.php') ? 'active' : ''; ?>" href="reports.php">
                <i class="fas fa-file-export"></i> Generate Report
            </a>
        </li>
    </ul>
</aside>