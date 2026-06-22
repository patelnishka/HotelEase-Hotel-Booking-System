<?php 
    require('../config/db_config.php');
    if (session_status() === PHP_SESSION_NONE) { session_start(); }

    // Admin Access Control
    if(!(isset($_SESSION['adminLogin']) && $_SESSION['adminLogin'] == true)){
        header("Location: login.php"); exit;
    }

    // 1. Handle Filters (Date, Month, Year Wise)
    $from_date = $_GET['from_date'] ?? date('Y-m-d', strtotime('-30 days'));
    $to_date = $_GET['to_date'] ?? date('Y-m-d');

    // 2. Handle User Wise Filter
    $u_id_filter = $_GET['u_id'] ?? '';
    $user_condition = ($u_id_filter != '') ? " AND b.u_id = '$u_id_filter' " : "";

    $f_date = mysqli_real_escape_string($conn, $from_date);
    $t_date = mysqli_real_escape_string($conn, $to_date);

    // 3. Fetch Users for Dropdown
    $user_q = "SELECT u_id, u_name FROM users ORDER BY u_name ASC";
    $user_res = mysqli_query($conn, $user_q);

    // 4. Metrics Query
    $q_metrics = "SELECT 
        COUNT(id) AS total_count,
        SUM(CASE WHEN status='booked' THEN total_pay ELSE 0 END) AS revenue,
        SUM(CASE WHEN status='cancelled' THEN total_pay ELSE 0 END) AS loss,
        SUM(CASE WHEN arrival=1 THEN 1 ELSE 0 END) AS active_stays
        FROM `bookings` b WHERE DATE(b.datentime) BETWEEN '$f_date' AND '$t_date' $user_condition";
    $metrics = mysqli_fetch_assoc(mysqli_query($conn, $q_metrics));

    // 5. Chart Data: Status Breakdown
    $q_status = "SELECT status, COUNT(id) as count FROM `bookings` b
                WHERE DATE(b.datentime) BETWEEN '$f_date' AND '$t_date' $user_condition GROUP BY status";
    $res_status = mysqli_query($conn, $q_status);
    $status_labels = []; $status_values = [];
    while($row = mysqli_fetch_assoc($res_status)){
        $status_labels[] = ucfirst($row['status']);
        $status_values[] = $row['count'];
    }

    // 6. Chart Data: Revenue Trend
    $q_trend = "SELECT DATE(datentime) AS date, SUM(total_pay) AS daily_rev 
                FROM `bookings` b WHERE status='booked' AND DATE(b.datentime) BETWEEN '$f_date' AND '$t_date' $user_condition
                GROUP BY DATE(datentime) ORDER BY datentime ASC";
    $res_trend = mysqli_query($conn, $q_trend);
    $trend_labels = []; $trend_values = [];
    while($row = mysqli_fetch_assoc($res_trend)){
        $trend_labels[] = date('d M', strtotime($row['date']));
        $trend_values[] = $row['daily_rev'];
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Ease - Analytics & Reports</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    
    <style>
        #main-content { margin-top: 70px; transition: all 0.3s; }
        .stat-icon { font-size: 2rem; opacity: 0.3; position: absolute; right: 15px; bottom: 10px; }
        .card { transition: transform 0.2s; border: none; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); }
        .card:hover { transform: translateY(-3px); }
        #pdf-template { display: none; background: white; padding: 40px; color: #000; font-family: Arial, sans-serif; }
        .pdf-header { border-bottom: 2px solid #333; margin-bottom: 20px; padding-bottom: 10px; }
        .pdf-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .pdf-table th, .pdf-table td { border: 1px solid #ddd; padding: 8px; font-size: 11px; text-align: center; }
    </style>
</head>
<body class="bg-light">

    <?php include('includes/admin_header.php'); ?>
    <?php include('includes/admin_sidebar.php'); ?>

    <div id="main-content" class="p-4">
        <div class="container-fluid">
            
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h3 class="fw-bold"><i class="fas fa-chart-line me-2"></i>Analytics & Reports</h3>
                <button class="btn btn-dark shadow-none" onclick="downloadProfessionalPDF()">
                    <i class="fas fa-file-pdf me-2"></i>Download Report PDF
                </button>
            </div>

            <div class="mb-3 d-flex gap-2">
                <a href="reports.php?from_date=<?php echo date('Y-m-d'); ?>&to_date=<?php echo date('Y-m-d'); ?>" class="btn btn-sm btn-outline-primary shadow-none">Today</a>
                <a href="reports.php?from_date=<?php echo date('Y-m-01'); ?>&to_date=<?php echo date('Y-m-t'); ?>" class="btn btn-sm btn-outline-primary shadow-none">This Month</a>
                <a href="reports.php?from_date=<?php echo date('Y-01-01'); ?>&to_date=<?php echo date('Y-12-31'); ?>" class="btn btn-sm btn-outline-primary shadow-none">This Year</a>
                <a href="reports.php" class="btn btn-sm btn-secondary shadow-none">Reset</a>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="small fw-bold">From Date</label>
                            <input type="date" name="from_date" value="<?php echo $from_date; ?>" class="form-control shadow-none">
                        </div>
                        <div class="col-md-3">
                            <label class="small fw-bold">To Date</label>
                            <input type="date" name="to_date" value="<?php echo $to_date; ?>" class="form-control shadow-none">
                        </div>
                        <div class="col-md-3">
                            <label class="small fw-bold">User Wise Filter</label>
                            <select name="u_id" class="form-select shadow-none">
                                <option value="">All Guests</option>
                                <?php 
                                    mysqli_data_seek($user_res, 0);
                                    while($u = mysqli_fetch_assoc($user_res)){
                                        $sel = ($u_id_filter == $u['u_id']) ? 'selected' : '';
                                        echo "<option value='$u[u_id]' $sel>$u[u_name]</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100 shadow-none">Apply Filters</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card p-3 bg-primary text-white position-relative">
                        <h6 class="small text-uppercase">Total Revenue</h6>
                        <h3 class="fw-bold m-0">₹<?php echo number_format($metrics['revenue'] ?? 0); ?></h3>
                        <i class="fas fa-wallet stat-icon"></i>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card p-3 bg-success text-white position-relative">
                        <h6 class="small text-uppercase">Total Bookings</h6>
                        <h3 class="fw-bold m-0"><?php echo $metrics['total_count'] ?? 0; ?></h3>
                        <i class="fas fa-calendar-check stat-icon"></i>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card p-3 bg-info text-white position-relative">
                        <h6 class="small text-uppercase">Active Stays</h6>
                        <h3 class="fw-bold m-0"><?php echo $metrics['active_stays'] ?? 0; ?></h3>
                        <i class="fas fa-user-clock stat-icon"></i>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card p-3 bg-danger text-white position-relative">
                        <h6 class="small text-uppercase">Cancellation Loss</h6>
                        <h3 class="fw-bold m-0">₹<?php echo number_format($metrics['loss'] ?? 0); ?></h3>
                        <i class="fas fa-exclamation-triangle stat-icon"></i>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-8">
                    <div class="card h-100">
                        <div class="card-header bg-white fw-bold">Revenue Trend Analysis</div>
                        <div class="card-body"><canvas id="revenueChart"></canvas></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-header bg-white fw-bold">Booking Distribution</div>
                        <div class="card-body"><canvas id="statusChart"></canvas></div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-dark text-white fw-bold">Detailed Audit Logs</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover text-center m-0">
                            <thead>
                                <tr>
                                    <th>#</th><th>Order ID</th><th>Guest Name</th><th>Room</th><th>Amount</th><th>Status</th><th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    $audit_q = "SELECT b.*, u.u_name, r.name as rname 
                                                FROM bookings b 
                                                JOIN users u ON b.u_id = u.u_id 
                                                JOIN rooms r ON b.room_id = r.id
                                                WHERE DATE(b.datentime) BETWEEN '$f_date' AND '$t_date' $user_condition 
                                                ORDER BY b.id DESC";
                                    $audit_res = mysqli_query($conn, $audit_q);
                                    $i = 1;
                                    while($row = mysqli_fetch_assoc($audit_res)){
                                        $badge = ($row['status']=='cancelled') ? 'bg-danger' : (($row['status']=='booked') ? 'bg-success' : 'bg-warning');
                                        echo "<tr>
                                            <td>$i</td>
                                            <td>$row[order_id]</td>
                                            <td>$row[u_name]</td>
                                            <td>$row[rname]</td>
                                            <td class='fw-bold'>₹".number_format($row['total_pay'])."</td>
                                            <td><span class='badge $badge'>".ucfirst($row['status'])."</span></td>
                                            <td>".date('d M Y', strtotime($row['datentime']))."</td>
                                        </tr>";
                                        $i++;
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div id="pdf-template">
        <div class="pdf-header">
            <h1 style="margin:0;">HOTEL EASE - AUDIT REPORT</h1>
            <p style="margin:5px 0;">Generated On: <?php echo date('d-m-Y h:i A'); ?></p>
            <p style="margin:0;">Report Period: <?php echo "$from_date to $to_date"; ?></p>
        </div>
        <div style="margin: 20px 0; background: #f9f9f9; padding: 15px; border: 1px solid #ddd; display: flex; justify-content: space-between;">
            <div>
                <p style="margin:2px 0;"><strong>Revenue:</strong> ₹<?php echo number_format($metrics['revenue'] ?? 0); ?></p>
                <p style="margin:2px 0;"><strong>Total Bookings:</strong> <?php echo $metrics['total_count'] ?? 0; ?></p>
            </div>
            <div style="text-align: right;">
                <p style="margin:2px 0; color: #d32f2f;"><strong>Loss:</strong> ₹<?php echo number_format($metrics['loss'] ?? 0); ?></p>
                <p style="margin:2px 0;"><strong>Active Stays:</strong> <?php echo $metrics['active_stays'] ?? 0; ?></p>
            </div>
        </div>
        <table class="pdf-table">
            <thead>
                <tr style="background: #eee;">
                    <th>Date</th><th>Order ID</th><th>Guest Name</th><th>Room</th><th>Amount</th><th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php mysqli_data_seek($audit_res, 0); while($row = mysqli_fetch_assoc($audit_res)): ?>
                <tr>
                    <td><?php echo date('d-m-Y', strtotime($row['datentime'])); ?></td>
                    <td><?php echo $row['order_id']; ?></td>
                    <td><?php echo $row['u_name']; ?></td>
                    <td><?php echo $row['rname']; ?></td>
                    <td>₹<?php echo number_format($row['total_pay']); ?></td>
                    <td style="font-weight:bold; color: <?php echo ($row['status']=='cancelled') ? '#d32f2f' : '#388e3c'; ?>;">
                        <?php echo strtoupper($row['status']); ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>
        // STATUS CHART WITH DYNAMIC COLOR FIX
        const statusLabels = <?php echo json_encode($status_labels); ?>;
        const statusData = <?php echo json_encode($status_values); ?>;
        
        const colorMap = {
            'Booked': '#198754',   // Green
            'Cancelled': '#dc3545', // Red
            'Pending': '#ffc107'    // Yellow
        };

        const backgroundColors = statusLabels.map(label => colorMap[label] || '#0dcaf0');

        new Chart(document.getElementById('statusChart'), {
            type: 'doughnut',
            data: {
                labels: statusLabels,
                datasets: [{
                    data: statusData,
                    backgroundColor: backgroundColors
                }]
            },
            options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
        });

        // REVENUE TREND CHART
        new Chart(document.getElementById('revenueChart'), {
            type: 'line',
            data: {
                labels: <?php echo json_encode($trend_labels); ?>,
                datasets: [{
                    label: 'Revenue (₹)',
                    data: <?php echo json_encode($trend_values); ?>,
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    fill: true,
                    tension: 0.3
                }]
            }
        });

        function downloadProfessionalPDF() {
            const element = document.getElementById('pdf-template');
            element.style.display = 'block';
            html2pdf().from(element).set({
                margin: 0.5,
                filename: 'Hotel_Ease_Report.pdf',
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
            }).save().then(() => element.style.display = 'none');
        }
    </script>
</body>
</html>