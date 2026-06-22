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
    <title>Admin Panel - Room Features</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
                    <h3 class="m-0"><i class="fas fa-star me-2"></i> ROOM FEATURES</h3>
                </div>
                <div class="col-md-6 text-end">
                    <button type="button" class="btn btn-dark btn-sm" data-bs-toggle="modal" data-bs-target="#add-feature">
                        <i class="fas fa-plus"></i> Add Feature
                    </button>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover border text-center">
                            <thead>
                                <tr class="table-dark">
                                    <th scope="col" width="10%">#</th>
                                    <th scope="col">Feature Name</th>
                                    <th scope="col" width="15%">Action</th>
                                </tr>
                            </thead>
                            <tbody id="features-data"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="add-feature" tabindex="-1">
        <div class="modal-dialog">
            <form id="add_feature_form">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Feature</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Name</label>
                            <input type="text" name="feature_name" class="form-control shadow-none" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">CANCEL</button>
                        <button type="submit" class="btn btn-dark">SUBMIT</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let add_feature_form = document.getElementById('add_feature_form');

        function get_features() {
            let xhr = new XMLHttpRequest();
            xhr.open("POST", "ajax/features_crud.php", true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() { document.getElementById('features-data').innerHTML = this.responseText; }
            xhr.send('get_features');
        }

        add_feature_form.addEventListener('submit', function(e){
            e.preventDefault();
            let data = new FormData();
            data.append('name', add_feature_form.elements['feature_name'].value);
            data.append('add_feature', '');

            let xhr = new XMLHttpRequest();
            xhr.open("POST", "ajax/features_crud.php", true);
            xhr.onload = function() {
                if(this.responseText == 1) {
                    alert('Feature added!');
                    add_feature_form.reset();
                    bootstrap.Modal.getInstance(document.getElementById('add-feature')).hide();
                    get_features();
                } else { alert('Error!'); }
            }
            xhr.send(data);
        });

        function rem_feature(val) {
            if(confirm("Are you sure?")){
                let xhr = new XMLHttpRequest();
                xhr.open("POST", "ajax/features_crud.php", true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() { if(this.responseText == 1) get_features(); }
                xhr.send('rem_feature=' + val);
            }
        }
        window.onload = function() { get_features(); }
    </script>
</body>
</html>