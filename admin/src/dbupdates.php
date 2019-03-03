<?php
echo 'padmanabhann';

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../dbHelper.php';
require_once 'functions.php';
$db = new dbHelper();

$max_number = find_max_y_c_value();
$max_number = $max_number[0]['max_profile'];

$query_only_A_J_profiles = 'SELECT id from memberships where profile_id IN (SELECT profile_id  FROM `memberships` WHERE profile_number like "A%" or profile_number like "J%") and profile_number not like "Y%" and profile_number not like "C%" and profile_number not like "B%" ORDER by profile_id ASC';
$exec_query_only_A_J_profiles = $db->prepare($query_only_A_J_profiles);
$exec_query_only_A_J_profiles->execute();

// $A_J_ids = $exec_query_only_A_J_profiles->fetchAll(PDO::FETCH_ASSOC);
/*
foreach($A_J_ids as $item){
  $id = $item['id'];
  $sql = "UPDATE memberships set profile_number = 'YM".$max_number."' where id =".$id;
  $query_update_profile_number = $db->prepare($sql);
  $exec_query_update_profile_number = $query_update_profile_number->execute();
  $max_number = $max_number + 1;
}
*/

$select_multiple_profile_number_sql = "SELECT * from memberships 
INNER JOIN (SELECT id, profile_id, profile_number 
               FROM  memberships
               GROUP BY profile_id
               HAVING COUNT(profile_id) > 1) dup
           ON memberships.profile_id = dup.profile_id";
$query_select_multiple_profile_number = $db->prepare($select_multiple_profile_number_sql);
$query_select_multiple_profile_number->execute();

$multiple_profileIDs = $query_select_multiple_profile_number->fetchAll(PDO::FETCH_ASSOC);
pp($multiple_profileIDs);



function find_max_y_c_value(){
  global $db;
    $max_existing = $db->prepare("SELECT MAX(CAST(SUBSTRING(profile_number, 3) AS UNSIGNED)) as max_profile FROM memberships where profile_number LIKE 'Y%' OR profile_number LIKE 'C%' ");
			$max_existing->execute();
			return $max_profile = $max_existing->fetchAll(PDO::FETCH_ASSOC);
}
