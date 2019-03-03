<?php
echo 'Successfully completed the update ';

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../dbHelper.php';
require_once 'functions.php';
$db = new dbHelper();

function find_max_y_c_value(){
  global $db;
    $max_existing = $db->prepare("SELECT MAX(CAST(SUBSTRING(profile_number, 3) AS UNSIGNED)) as max_profile FROM memberships where profile_number LIKE 'Y%' OR profile_number LIKE 'C%' ");
			$max_existing->execute();
			return $max_profile = $max_existing->fetchAll(PDO::FETCH_ASSOC);
}

$sql_profiles_table = "select id, gender_id from profiles";
$query_profiles_table = $db->prepare($sql_profiles_table);
$query_profiles_table->execute();
$profile_ID_profiles_table = $query_profiles_table->fetchAll(PDO::FETCH_OBJ);
foreach($profile_ID_profiles_table as $key=>$value){
    $profile_id = $value->id;
    $gender_id  = $value->gender_id;
    $sql_memberships_table = 'select * from memberships where profile_id ='.$profile_id;
    $query_memberships_table = $db->prepare($sql_memberships_table);
    $query_memberships_table->execute();

    $profiles_in_memberships_table = $query_memberships_table->fetchAll(PDO::FETCH_OBJ);

    $c_present = $b_present = $y_present = $a_present = $j_present = false;
    $c_profilenumber = $b_profilenumber = $y_profilenumber = $a_profilenumber = $j_profilenumber = '';

    if(count($profiles_in_memberships_table) > 0){
        $previous_profile_number = '';
        foreach($profiles_in_memberships_table as $k=>$item){
            if(substr($item->profile_number, 0, 1 ) == 'C'){
                $c_present = true;
                $c_profilenumber = $item->profile_number;
                $c_previous = $item->previous_profile_number;
            }
            if(substr($item->profile_number, 0, 1 ) == 'Y'){
                $y_present = true;
                $y_profilenumber = $item->profile_number;
                $y_previous = $item->previous_profile_number;
            }
            if(substr($item->profile_number, 0, 1 ) == 'b'){
                $b_present = true;
                $b_profilenumber = $item->profile_number;
                $previous_profile_number .= $item->profile_number.", ";
                $b_previous = $item->previous_profile_number;
            }
            if(substr($item->profile_number, 0, 1 ) == 'A'){
                $a_present = true;
                $a_profilenumber = $item->profile_number;
                $previous_profile_number .= $item->profile_number.", ";
                $a_previous = $item->previous_profile_number;
            }
            if(substr($item->profile_number, 0, 1 ) == 'J'){
                $j_present = true;
                $j_profilenumber = $item->profile_number;
                $previous_profile_number .= $item->profile_number.", ";
                $j_previous = $item->previous_profile_number;
            }
        }
        if($c_present){
            // $update_c_sql = "update memberships set previous_profile_number = '".$y_profilenumber.",".$b_profilenumber.",".$a_profilenumber.",".$j_profilenumber."' where profile_number = '".$c_profilenumber."'";
            // $update_c = $db->prepare($update_c_sql);
            // $update_c->execute();

            $delete_others_sql = "delete from memberships where profile_number != '".$c_profilenumber."' and profile_id = ".$profile_id;
            $delete_others = $db->prepare($delete_others_sql);
            $delete_others->execute();
        }
        if($y_present && !$c_present){
            // $update_y_sql = "update memberships set previous_profile_number = '".$b_profilenumber.",".$a_profilenumber.",".$j_profilenumber."' where profile_number = '".$y_profilenumber."'";
            // $update_y = $db->prepare($update_y_sql);
            // $update_y->execute();

            $delete_others_sql = "delete from memberships where profile_number != '".$y_profilenumber."' and profile_id = ".$profile_id;
            $delete_others = $db->prepare($delete_others_sql);
            $delete_others->execute();
        }
        if($b_present && !$y_present && !$c_present){
            // $update_b_sql = "update memberships set previous_profile_number = '".$a_profilenumber.",".$j_profilenumber."' where profile_number = '".$b_profilenumber."'";
            // $update_b = $db->prepare($update_b_sql);
            // $update_b->execute();

            $delete_others_sql = "delete from memberships where profile_number != '".$b_profilenumber."' and profile_id = ".$profile_id;
            $delete_others = $db->prepare($delete_others_sql);
            $delete_others->execute();
        }
        if($a_present && !$j_present && !$b_present && !$y_present && !$c_present){
            // $update_a_sql = "update memberships set previous_profile_number = '".$a_previous."-".$previous_profile_number."'";
            // $update_a = $db->prepare($update_a_sql);
            // $update_a->execute();

            $max_number_set = find_max_y_c_value();
            $max_number = $max_number_set[0]['max_profile'];
            $current_number = $max_number+1;
            $gender_char = ($gender_id == 1)?'M':'F';

            // $new_profile_number = 'Y'.$gender_char.$current_number;
            $new_profile_number = 'CF'.$current_number;

            $update_a_sql = "update memberships set profile_number = '".$new_profile_number."' where profile_id = ".$profile_id;
            $update_a = $db->prepare($update_a_sql);
            $update_a->execute();
        }
        if(!$a_present && $j_present && !$b_present && !$y_present && !$c_present){
            // $update_j_sql = "update memberships set previous_profile_number = '".$j_previous."-".$previous_profile_number."'";
            // $update_j = $db->prepare($update_j_sql);
            // $update_j->execute();

            $max_number_set = find_max_y_c_value();
            $max_number = $max_number_set[0]['max_profile'];
            $current_number = $max_number+1;
            $gender_char = ($gender_id == 1)?'M':'F';

            $new_profile_number = 'CM'.$current_number;

            $update_j_sql = "update memberships set profile_number = '".$new_profile_number."' where profile_id = ".$profile_id;
            $update_j = $db->prepare($update_j_sql);
            $update_j->execute();
        }
        if($a_present && $j_present && !$b_present && !$y_present && !$c_present){
            // $update_j_sql = "update memberships set previous_profile_number = '".$j_previous."-".$a_profilenumber.", ".$j_profilenumber.", ".$previous_profile_number."' where profile_id = ".$profile_id;
            // $update_j = $db->prepare($update_j_sql);
            // $update_j->execute();

            $delete_others_sql = "delete from memberships where profile_number != '".$j_profilenumber."' and profile_id = ".$profile_id;
            $delete_others = $db->prepare($delete_others_sql);
            $delete_others->execute();

            $max_number_set = find_max_y_c_value();
            $max_number = $max_number_set[0]['max_profile'];
            $current_number = $max_number+1;
            $gender_char = ($gender_id == 1)?'M':'F';

            $new_profile_number = 'CF'.$current_number;

            $update_j_sql = "update memberships set profile_number = '".$new_profile_number."' where profile_id = ".$profile_id;
            $update_j = $db->prepare($update_j_sql);
            $update_j->execute();
        }

    }

}
$delete_others_sql = "delete from memberships where profile_number = '' ";
$delete_others = $db->prepare($delete_others_sql);
$delete_others->execute();

$max_number_set = find_max_y_c_value();
echo $max_number_set[0]['max_profile'];