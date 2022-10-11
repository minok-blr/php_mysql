<?php

function get_ad($id){
    global $conn;
    $id = mysqli_real_escape_string($conn, $id);
    $query = "SELECT ads.*, users.username FROM ads LEFT JOIN users ON users.id = ads.user_id WHERE ads.id = $id;";
    $res = $conn->query($query);
    if($obj = $res->fetch_object()){
        return $obj;
    }
    return null;
}

