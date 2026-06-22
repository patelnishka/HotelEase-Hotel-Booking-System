<?php
    require_once('../../config/db_config.php');

    if(isset($_POST['add_feature'])) {
        $q = "INSERT INTO `features`(`f_name`) VALUES (?)";
        $v = [$_POST['name']];
        if($stmt = mysqli_prepare($conn, $q)) {
            mysqli_stmt_bind_param($stmt, "s", ...$v);
            echo mysqli_stmt_execute($stmt) ? 1 : 0;
            mysqli_stmt_close($stmt);
        }
    }

    if(isset($_POST['get_features'])) {
        $res = mysqli_query($conn, "SELECT * FROM `features` ORDER BY f_id DESC");
        $i = 1;
        while($row = mysqli_fetch_assoc($res)) {
            echo <<<data
                <tr>
                    <td>$i</td>
                    <td>$row[f_name]</td>
                    <td>
                        <button type="button" onclick="rem_feature($row[f_id])" class="btn btn-danger btn-sm shadow-none">
                            <i class="fa-solid fa-trash"></i> 
                        </button>
                    </td>
                </tr>
            data;
            $i++;
        }
    }

    if(isset($_POST['rem_feature'])) {
        $q = "DELETE FROM `features` WHERE `f_id`=?";
        $v = [$_POST['rem_feature']];
        if($stmt = mysqli_prepare($conn, $q)) {
            mysqli_stmt_bind_param($stmt, "i", ...$v);
            echo mysqli_stmt_execute($stmt) ? 1 : 0;
            mysqli_stmt_close($stmt);
        }
    }
?>