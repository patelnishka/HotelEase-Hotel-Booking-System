<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    require('../config/db_config.php');

    $lib_path = 'inc/dompdf/autoload.inc.php';
    if (!file_exists($lib_path)) {
        die("FATAL ERROR: Dompdf library not found at $lib_path.");
    }
    
    require_once($lib_path);
    use Dompdf\Dompdf;
    use Dompdf\Options;

    session_start();

    if(!(isset($_SESSION['u_id']) || isset($_SESSION['adminLogin']))){
        die("Access Denied: Please log in.");
    }

    if(isset($_GET['gen_pdf']) && isset($_GET['id']))
    {
        $id = mysqli_real_escape_string($conn, $_GET['id']);

        $q = "SELECT b.*, u.u_name, u.u_phone, u.u_address, r.name AS room_name 
              FROM `bookings` b 
              INNER JOIN `users` u ON b.u_id = u.u_id 
              INNER JOIN `rooms` r ON b.room_id = r.id 
              WHERE b.id = '$id' LIMIT 1";

        $res = mysqli_query($conn, $q);
        
        if(mysqli_num_rows($res) == 0){
            die("Error: Booking record not found.");
        }

        $data = mysqli_fetch_assoc($res);

        $total_amt = $data['total_pay'];
        $advance_amt = $total_amt * 0.10; 
        $balance_amt = $total_amt - $advance_amt;

        $date = date("d-M-Y", strtotime($data['datentime']));
        $checkin = date("d-M-Y", strtotime($data['check_in']));
        $checkout = date("d-M-Y", strtotime($data['check_out']));

        // Use the HTML Entity &#8377; for the Rupee Symbol
        $rupee = "&#8377;";

        $html = "
        <html>
        <head>
        <meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>
        <style>
            /* DejaVu Sans is the best font for rendering Unicode/Rupee symbols in Dompdf */
            body { font-family: 'DejaVu Sans', sans-serif; color: #444; line-height: 1.5; font-size: 13px; }
            .header { border-bottom: 3px solid #2ec1ac; padding-bottom: 15px; margin-bottom: 30px; }
            .title { color: #2ec1ac; font-size: 26px; font-weight: bold; }
            .company-info { float: right; text-align: right; font-size: 11px; color: #777; }
            
            h4 { color: #2ec1ac; border-bottom: 1px solid #eee; padding-bottom: 5px; margin-top: 25px; font-size: 16px; }
            
            table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
            th, td { padding: 10px; border: 1px solid #f2f2f2; text-align: left; }
            .bg-light { background-color: #fafafa; color: #666; width: 30%; font-weight: bold; }
            
            .payment-table { margin-top: 20px; width: 45%; float: right; }
            .payment-table td { border: none; padding: 4px 10px; }
            .text-right { text-align: right; }
            .bold { font-weight: bold; }
            
            .footer-box { clear: both; margin-top: 40px; background-color: #2ec1ac; color: white; padding: 15px; border-radius: 5px; }
            .footer-box table { margin: 0; border: none; }
            .footer-box td { border: none; padding: 2px 0; color: white; font-size: 15px; }
            
            .notice { font-size: 10px; margin-top: 10px; opacity: 0.9; color: #eee; }
        </style>
        </head>
        <body>

        <div class='header'>
            <div class='company-info'>
                <strong>Hotel Ease Ltd.</strong><br>
                Ahmedabad, Gujarat<br>
                info@hotelease.com
            </div>
            <span class='title'>INVOICE</span><br>
            <strong>Order ID:</strong> $data[order_id]<br>
            <strong>Date:</strong> $date
        </div>

        <h4>Guest Information</h4>
        <table>
            <tr><td class='bg-light'>Guest Name</td><td>$data[u_name]</td></tr>
            <tr><td class='bg-light'>Contact Number</td><td>$data[u_phone]</td></tr>
            <tr><td class='bg-light'>Billing Address</td><td>$data[u_address]</td></tr>
        </table>

        <h4>Booking Summary</h4>
        <table>
            <thead>
                <tr style='background-color: #f9f9f9;'>
                    <th style='border: 1px solid #eee;'>Room Type</th>
                    <th style='border: 1px solid #eee;'>Check-in Date</th>
                    <th style='border: 1px solid #eee;'>Check-out Date</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>$data[room_name]</td>
                    <td>$checkin</td>
                    <td>$checkout</td>
                </tr>
            </tbody>
        </table>

        <h4>Payment Details</h4>
        <table class='payment-table'>
            <tr>
                <td class='text-right'>Total Room Rent:</td>
                <td class='text-right'>$rupee ".number_format($total_amt, 2)."</td>
            </tr>
            <tr>
                <td class='text-right'>Advance Paid (10%):</td>
                <td class='text-right' style='color: #28a745;'>- $rupee ".number_format($advance_amt, 2)."</td>
            </tr>
            <tr style='border-top: 1px solid #ddd;'>
                <td class='text-right bold'>Balance Due at Hotel:</td>
                <td class='text-right bold'>$rupee ".number_format($balance_amt, 2)."</td>
            </tr>
        </table>

        <div class='footer-box'>
            <table width='100%'>
                <tr>
                    <td class='bold'>Grand Total:</td>
                    <td class='text-right bold' style='font-size: 20px;'>$rupee ".number_format($total_amt, 2)."</td>
                </tr>
            </table>
            <div class='notice'>
                * Note: The 10% advance payment is non-refundable as per hotel policy.
            </div>
        </div>

        </body>
        </html>
        ";

        try {
            // Options are required to enable remote fonts and proper Unicode handling
            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true);
            $options->set('defaultFont', 'DejaVu Sans');

            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            
            if (ob_get_length()) ob_end_clean(); 
            
            $dompdf->stream("Invoice_$data[order_id].pdf", ["Attachment" => 1]);
        } catch (Exception $e) {
            die("PDF Generation Error: " . $e->getMessage());
        }
    }
?>