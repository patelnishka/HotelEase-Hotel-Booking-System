<?php
    require('../config/db_config.php');
    session_start();
    if(!(isset($_SESSION['adminLogin']) && $_SESSION['adminLogin']==true)){
        header("Location: index.php"); exit;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Room Units</title>
    <?php require('includes/links.php'); ?>
    <style>
        /* Essential Fix: Pushes content to the right of the fixed sidebar */
        #main-content {
            margin-left: 250px; 
            transition: all 0.3s;
        }

        @media screen and (max-width: 991px) {
            #main-content {
                margin-left: 0;
            }
        }

        .table > :not(caption) > * > * {
            vertical-align: middle;
        }
    </style>
</head>
<body class="bg-light">
    <?php require('includes/admin_header.php'); ?>

    <div class="container-fluid">
        <div class="row">
            <?php require('includes/admin_sidebar.php'); ?>

            <div class="p-4" id="main-content">
                <h3 class="mb-4 text-uppercase fw-bold">Manage Room Units</h3>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <form id="add_unit_form">
                            <div class="row align-items-end">
                                <div class="col-md-5 mb-3">
                                    <label class="form-label fw-bold">Select Room Category</label>
                                    <select name="room_id" class="form-select shadow-none" required>
                                        <option value="">Select Category</option>
                                        <?php
                                            $res = mysqli_query($conn, "SELECT id, name FROM rooms");
                                            while($row = mysqli_fetch_assoc($res)){
                                                echo "<option value='$row[id]'>$row[name]</option>";
                                            }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">Room Number</label>
                                    <input type="text" name="room_no" class="form-control shadow-none" placeholder="e.g. 101" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <button type="submit" class="btn btn-success shadow-none w-100">
                                        <i class="fas fa-plus"></i> Add Unit
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive" style="height: 500px; overflow-y: auto;">
                            <table class="table table-hover border text-center mb-0">
                                <thead>
                                    <tr class="bg-dark text-light sticky-top">
                                        <th scope="col">#</th>
                                        <th scope="col">Category</th>
                                        <th scope="col">Room No</th>
                                        <th scope="col">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $q = "SELECT ru.*, r.name AS r_name FROM room_numbers ru 
                                              JOIN rooms r ON ru.room_id = r.id ORDER BY ru.id DESC";
                                        $res = mysqli_query($conn, $q);
                                        $i=1;
                                        while($row = mysqli_fetch_assoc($res)){
                                            $status_class = ($row['status'] == 'available') ? "bg-success" : "bg-danger";
                                            echo "<tr>
                                                <td>$i</td>
                                                <td class='fw-bold'>$row[r_name]</td>
                                                <td><span class='badge bg-light text-dark border'>$row[room_no]</span></td>
                                                <td><span class='badge $status_class text-uppercase px-3'>$row[status]</span></td>
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
    </div>

    <?php require('includes/scripts.php'); ?>

    <script>
        let add_unit_form = document.getElementById('add_unit_form');

        add_unit_form.addEventListener('submit', function(e){
            e.preventDefault();
            let data = new FormData(add_unit_form);
            data.append('add_unit', '');

            let xhr = new XMLHttpRequest();
            xhr.open("POST", "ajax/room_units_ajax.php", true);
            
            xhr.onload = function(){
                if(this.responseText == 1){
                    alert('success', 'New Room Unit Added!');
                    add_unit_form.reset();
                    // Optional: Instead of reload, you could fetch data again to be smoother
                    setTimeout(() => { location.reload(); }, 1000);
                } else {
                    alert('error', 'Room Number already exists or server error!');
                }
            }
            xhr.send(data);
        });
    </script>
</body>
</html>