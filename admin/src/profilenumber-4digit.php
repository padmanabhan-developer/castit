<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../dbHelper.php';
require_once 'functions.php';
$db = new dbHelper();

$sql = "select * from memberships";
$query = $db->prepare($sql);
$query->execute();
$results = $query->fetchAll(PDO::FETCH_OBJ);

// pp($results);

foreach ($results as $key => $value) {
    $profile_number = $value->profile_number;
    $prefix = substr($profile_number, 0, 2);
    $number = substr($profile_number, 2);
    $number = str_pad($number, 4, 0, STR_PAD_LEFT);

    $new_profile_number = $prefix.$number;
    $sql = "update memberships set `profile_number` = '".$new_profile_number."' where id = ".$value->id;
    $query = $db->prepare($sql);
    $query->execute();
}