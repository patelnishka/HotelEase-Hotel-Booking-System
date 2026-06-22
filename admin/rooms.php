<?php 
    require_once('../config/db_config.php'); 
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if(!(isset($_SESSION['adminLogin']) && $_SESSION['adminLogin'] == true)){
        header("Location: login.php"); exit;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Manage Rooms</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .modal { z-index: 9999 !important; background: rgba(0,0,0,0.5); }
        .modal-backdrop { display: none !important; } 
        body.modal-open { overflow: hidden; }
    </style>
</head>
<body class="bg-light">

    <?php require('includes/admin_header.php'); ?>
    <?php require('includes/admin_sidebar.php'); ?>

    <div id="main-content">
        <div class="container-fluid">
            <div class="row bg-white shadow-sm rounded p-3 mb-4 d-flex align-items-center justify-content-between mx-0">
                <div class="col-md-6">
                    <h3 class="m-0"><i class="fas fa-bed me-2"></i> ROOMS MANAGEMENT</h3>
                </div>
                <div class="col-md-6 text-end">
                    <button type="button" class="btn btn-dark shadow-none btn-sm" data-bs-toggle="modal" data-bs-target="#add-room">
                        <i class="bi bi-plus-square"></i> Add Room
                    </button>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover border text-center">
                            <thead class="table-dark">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Area</th>
                                    <th scope="col">Guests</th>
                                    <th scope="col">Price</th>
                                    <th scope="col">Quantity</th>
                                    <th scope="col">Features</th> 
                                     <th scope="col">Description</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody id="room-data"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="add-room" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form id="add_room_form">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Room</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3"><label class="form-label fw-bold">Name</label><input type="text" name="name" class="form-control shadow-none" required></div>
                            <div class="col-md-6 mb-3"><label class="form-label fw-bold">Area</label><input type="number" name="area" class="form-control shadow-none" required></div>
                            <div class="col-md-6 mb-3"><label class="form-label fw-bold">Price</label><input type="number" name="price" class="form-control shadow-none" required></div>
                            <div class="col-md-6 mb-3"><label class="form-label fw-bold">Quantity</label><input type="number" name="quantity" class="form-control shadow-none" required></div>
                            <div class="col-md-6 mb-3"><label class="form-label fw-bold">Adult</label><input type="number" name="adult" class="form-control shadow-none" required></div>
                            <div class="col-md-6 mb-3"><label class="form-label fw-bold">Children</label><input type="number" name="children" class="form-control shadow-none" required></div>
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">Features</label>
                                <div class="row">
                                    <?php 
                                        $res = mysqli_query($conn, "SELECT * FROM `features` ORDER BY f_name ASC");
                                        while($row = mysqli_fetch_assoc($res)){
                                            echo "<div class='col-md-3 mb-1'><label><input type='checkbox' name='features' value='$row[f_id]' class='form-check-input shadow-none'> $row[f_name]</label></div>";
                                        }
                                    ?>
                                </div>
                            </div>
                            <div class="col-12 mb-3"><label class="form-label fw-bold">Description</label><textarea name="desc" rows="4" class="form-control shadow-none" required></textarea></div>
                            <div class="col-md-4 mb-3"><label class="form-label fw-bold">Image 1</label><input type="file" name="image1" accept=".jpg,.png" class="form-control shadow-none" required></div>
                            <div class="col-md-4 mb-3"><label class="form-label fw-bold">Image 2</label><input type="file" name="image2" accept=".jpg,.png" class="form-control shadow-none" required></div>
                            <div class="col-md-4 mb-3"><label class="form-label fw-bold">Image 3</label><input type="file" name="image3" accept=".jpg,.png" class="form-control shadow-none" required></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="reset" class="btn btn-secondary" data-bs-dismiss="modal">CANCEL</button>
                        <button type="submit" class="btn btn-dark">SUBMIT</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="edit-room" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form id="edit_room_form">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Room</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <input type="hidden" name="room_id">
                            <div class="col-md-6 mb-3"><label class="form-label fw-bold">Name</label><input type="text" name="name" class="form-control shadow-none" required></div>
                            <div class="col-md-6 mb-3"><label class="form-label fw-bold">Area</label><input type="number" name="area" class="form-control shadow-none" required></div>
                            <div class="col-md-6 mb-3"><label class="form-label fw-bold">Price</label><input type="number" name="price" class="form-control shadow-none" required></div>
                            <div class="col-md-6 mb-3"><label class="form-label fw-bold">Quantity</label><input type="number" name="quantity" class="form-control shadow-none" required></div>
                            <div class="col-md-6 mb-3"><label class="form-label fw-bold">Adult</label><input type="number" name="adult" class="form-control shadow-none" required></div>
                            <div class="col-md-6 mb-3"><label class="form-label fw-bold">Children</label><input type="number" name="children" class="form-control shadow-none" required></div>
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">Features</label>
                                <div class="row">
                                    <?php 
                                        mysqli_data_seek($res, 0);
                                        while($row = mysqli_fetch_assoc($res)){
                                            echo "<div class='col-md-3 mb-1'><label><input type='checkbox' name='features' value='$row[f_id]' class='form-check-input shadow-none'> $row[f_name]</label></div>";
                                        }
                                    ?>
                                </div>
                            </div>
                            <div class="col-12 mb-3"><label class="form-label fw-bold">Description</label><textarea name="desc" rows="4" class="form-control shadow-none" required></textarea></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">CANCEL</button>
                        <button type="submit" class="btn btn-dark">UPDATE</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let add_room_form = document.getElementById('add_room_form');
        let edit_room_form = document.getElementById('edit_room_form');

        function get_all_rooms() {
            let xhr = new XMLHttpRequest();
            xhr.open("POST", "ajax/rooms_crud.php", true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() { document.getElementById('room-data').innerHTML = this.responseText; }
            xhr.send('get_all_rooms');
        }

        function toggle_status(id, val) {
            let xhr = new XMLHttpRequest();
            xhr.open("POST", "ajax/rooms_crud.php", true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (this.responseText == 1) {
                    get_all_rooms();
                } else {
                    alert('Status update failed!');
                }
            }
            // Fix: Send as key-value pairs that PHP expects
            xhr.send('toggle_status=1&room_id=' + id + '&value=' + val);
        }

        add_room_form.addEventListener('submit', function(e){
            e.preventDefault();
            let data = new FormData(add_room_form);
            data.append('add_room', '');
            let features = [];
            add_room_form.elements['features'].forEach(el => { if(el.checked) features.push(el.value); });
            data.append('features', JSON.stringify(features));

            let xhr = new XMLHttpRequest();
            xhr.open("POST", "ajax/rooms_crud.php", true);
            xhr.onload = function() {
                if(this.responseText == 1) {
                    alert('Room Added!');
                    add_room_form.reset();
                    bootstrap.Modal.getInstance(document.getElementById('add-room')).hide();
                    get_all_rooms();
                } else { alert('Server Error!'); }
            }
            xhr.send(data);
        });

        function edit_details(id) {
            let xhr = new XMLHttpRequest();
            xhr.open("POST", "ajax/rooms_crud.php", true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                let data = JSON.parse(this.responseText);
                edit_room_form.elements['name'].value = data.roomdata.name;
                edit_room_form.elements['area'].value = data.roomdata.area;
                edit_room_form.elements['price'].value = data.roomdata.price;
                edit_room_form.elements['quantity'].value = data.roomdata.quantity;
                edit_room_form.elements['adult'].value = data.roomdata.adult;
                edit_room_form.elements['children'].value = data.roomdata.children;
                edit_room_form.elements['desc'].value = data.roomdata.description;
                edit_room_form.elements['room_id'].value = data.roomdata.id;
                edit_room_form.elements['features'].forEach(el => {
                    el.checked = data.features.includes(el.value);
                });
            }
            xhr.send('get_room=' + id);
        }

        edit_room_form.addEventListener('submit', function(e) {
            e.preventDefault();
            let data = new FormData(edit_room_form);
            data.append('edit_room', '');
            let features = [];
            edit_room_form.elements['features'].forEach(el => { if(el.checked) features.push(el.value); });
            data.append('features', JSON.stringify(features));

            let xhr = new XMLHttpRequest();
            xhr.open("POST", "ajax/rooms_crud.php", true);
            xhr.onload = function() {
                if (this.responseText == 1) {
                    alert('Updated!');
                    bootstrap.Modal.getInstance(document.getElementById('edit-room')).hide();
                    get_all_rooms();
                } else { alert('Failed!'); }
            }
            xhr.send(data);
        });

        function remove_room(id) {
            if(confirm("Are you sure?")){
                let xhr = new XMLHttpRequest();
                xhr.open("POST", "ajax/rooms_crud.php", true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() { if(this.responseText == 1) get_all_rooms(); }
                xhr.send('remove_room=1&room_id='+id);
            }
        }

        window.onload = function() { get_all_rooms(); }
    </script>
</body>
</html>