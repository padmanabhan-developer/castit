<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'dbHelper.php';
$db = new dbHelper();

/*
 * Activate a profile
 *
 */

	/*
profiles   	: profile status id, approved --> 1
membership 	: manual all fields
photos 			: verify exist
	*/
	$email 							= $_REQUEST['emailid'];
	$query_string 			= "SELECT * from profiles WHERE email='".$email."'";
	$user_profile_query = $db->prepare($query_string);
	$user_profile_query->execute();
	$row = $user_profile_query->rowCount();
	if ($row > 0){
	  foreach ($user_profile_query->fetchAll(PDO::FETCH_ASSOC) as $key => $value) {
	  	$profile_id = $value['id'];
	  	$update_profile_query 		= "UPDATE profiles SET approved = '1', profile_status_id = '1' WHERE id = $profile_id";
	  	$activate_profile 				= $db->prepare($update_profile_query)->execute();
	  	
	  	$check_membership_query		= "SELECT * from memberships where profile_id = $profile_id";
	  	$check_membership 				= $db->prepare($check_membership_query);
	  	$check_membership->execute();
	  	$m_count = $check_membership->rowCount();
	  	pp($m_count);
	  	if($m_count > 0){
	  		$membership_table_query = "UPDATE 
	  				memberships 
	  			SET
	  				current = '1'
  				WHERE
  					profile_id = $profile_id
	  		";

	  	}
	  	else{
	  		// 1 male - CM; 2 female - CF
	  		$profile_number_prefix = ($value['gender_id'] == 2) ? "CF":"CM";
	  		$max_existing = $db->prepare("SELECT MAX(CAST(SUBSTRING(profile_number, 3) AS UNSIGNED)) as max_profile FROM memberships where profile_number LIKE 'CF%' OR profile_number LIKE 'CM%' ");
	  		$max_existing->execute();
	  		$max_profile = $max_existing->fetchAll(PDO::FETCH_ASSOC);
	  		
	  		$profile_number = $profile_number_prefix . ($max_profile[0]['max_profile'] + 1);
	  		$profile_number_first_name_last_name = $profile_number . " " . $value['first_name'] . " " . $value['last_name'];
	  		$version = 1;
	  		$created_at = date("o-m-d H:i:s");
	  		$current = 1;
	  		$set_to_current_at = date("o-m-d");
	  		$previous_profile_group_id = 1;
	  		$previous_profile_number = '';


	  		$membership_table_query = "INSERT INTO
	  			memberships(
	  			`profile_id`, 
	  			`profile_group_id`, 
	  			`profile_number`, 
	  			`profile_number_first_name_last_name`, 
	  			`version`, 
	  			`created_at`, 
	  			`current`, 
	  			`set_to_current_at`, 
	  			`previous_profile_group_id`, 
	  			`previous_profile_number`)
	  			VALUES
	  			('$profile_id', 1, '$profile_number', '$profile_number_first_name_last_name', '$version', '$created_at', '$current', '$set_to_current_at', '$previous_profile_group_id', '$previous_profile_number' )";
	  	}
	  	$activation = $db->prepare($membership_table_query);
	  	$activation->execute();
	  }
	  pp("Profile ".$profile_number_first_name_last_name." is activated");
	}
	else{
		pp('No profile with mentioned Email ID');
	}



function pp($q){
  echo '<pre>';
  print_r($q);
  echo '</pre>';
}

function ppe($q){
  pp($q);exit;
}
?>