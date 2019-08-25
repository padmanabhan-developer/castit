<?php
require '../libs/Slim/Slim.php';
require_once 'dbHelper.php';
require_once 'SimpleImage.php';

require '../../vendor/autoload.php';
use OpenCloud\Rackspace; 

// Get Slim instance
\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();
$app = \Slim\Slim::getInstance();
if (version_compare(PHP_VERSION, '5.4.0', '<')) {
        if(session_id() == '') {session_start();}
    } else  {
       if (session_status() == PHP_SESSION_NONE) {session_start();}
    }
// call our own dbHelper class
$db = new dbHelper();

$imageClass = new SimpleImage();

$app->post('/',function () use ($app) { 
    global $db;
});

/*****************************************
Purpose: Home page
Parameter : NIL
Type : POST
******************************************/
$app->post('/index',function () use ($app) { 
    global $db;
	
	$data =  json_decode($app->request->getBody(), true);
	
	$response = array();
	
    $rows = $db->select("ad_user","password, usertype", array('username' => $username), "");
	
	
	if(count($rows['data'])>0) {
		if($rows['data'][0]['password'] == md5($password)) {
			$response = array('success' => true, 'usertype' => $rows['data'][0]['usertype'] );
		}
		else {
			$response = array('success' => false, 'message' => 'Username or password is incorrect');
		}
	}
	else {
		$response['success'] = false;
		$response['message'] = 'Username or password is incorrect';
	}
	
	echoResponse(200, $response);
});
/******************************************
Purpose: Home page Landing page
Parameter : NIL
Type : POST
******************************************/
$app->get('/getprofiles',function () use ($app) { 
  global $db;
  $sql = "SELECT 
  p.*, m.profile_group_id, m.profile_number, m.profile_number_first_name_last_name, m.version, m.current, g.name 
  as gender_name, hc.name 
  as hair_color_name, ec.name 
  as eye_color_name 
FROM profiles p 
  INNER JOIN memberships m ON m.profile_id = p.id 
  INNER JOIN genders g ON g.id = p.gender_id 
  INNER JOIN hair_colors hc ON hc.id = p.hair_color_id 
  INNER JOIN eye_colors ec ON ec.id = p.eye_color_id  
WHERE ( p.profile_status_id = '1' OR  p.profile_status_id = '2' ) 
AND m.current ='1' 
AND ( m.profile_number LIKE 'C%' OR m.profile_number LIKE 'A%'OR m.profile_number LIKE 'J%' OR m.profile_number LIKE 'Y%' ) 
AND p.id IN ( SELECT profile_id from photos WHERE published ='1' GROUP by profile_id ) 
ORDER BY RAND()";

	$query = $db->prepare($sql); 

	// echo $query;exit;
	$query->execute();
	$rows = $query->fetchAll(PDO::FETCH_ASSOC);
	// echo 'sdsds';die;
	// print_r($rows[0]);exit;
	$profiles = array();
	if(count($rows)>0) {
		foreach($rows as $row){
			$birthdate = new DateTime($row['birthday']);
        	$today   = new DateTime('today');
        	$age = $birthdate->diff($today)->y;

			// Profile Image
			$profile_image ='';
			$query_image = $db->prepare("SELECT *, DATE_FORMAT(created_at, '%Y') as create_year, DATE_FORMAT(created_at, '%m') as create_month, DATE_FORMAT(created_at, '%d') as create_date FROM photos WHERE profile_id = '".$row['id']."' and published ='1' ORDER BY position ASC "); 
		$query_image->execute();
		$image= '';
    $rows_image = $query_image->fetchAll(PDO::FETCH_ASSOC);
		if(count($rows_image) > 0){
			$path = $rows_image[0]['create_year']."/".$rows_image[0]['create_month']."/".$rows_image[0]['create_date']."/".$rows_image[0]['id']."/big_";
			$profile_image = 'http://134.213.29.220/profile_images/'.$path.$rows_image[0]['image'];
			//$profile_image = 'http://castit.dk/assets/profile_images/'.$path.$rows_image[0]['image'];
		}


			$profiles[] = array('id' 			=> $row['id'],
								'bureau' 		=> $row['bureau'],
								'nationality' 	=> $row['nationality'],
								'name' 			=> $row['first_name'],
								'first_name' 	=> $row['first_name'],
								'last_name' 	=> $row['last_name'],
								'profile_number' 	=> $row['profile_number'],
								'profile_image' 	=> $profile_image,
								'gender_id' 	=> $row['gender_id'],
								'gender_name' 	=> $row['gender_name'],
								'age' 			=> $age,
								'height' 		=> $row['height'],
								'weight' 		=> $row['weight'],
								'address' 		=> $row['address'],
								'zipcode' 		=> $row['zipcode'],
								'city' 			=> $row['city'],
								'phone' 		=> $row['phone'],
								'cellphone' 	=> $row['cellphone'],
								'email' 		=> $row['email'],
								'job' 			=> $row['job'],

							);
		}
		$response = array('success' => true, 'profiles' => $profiles );
	}
	
	else {
		$response['success'] = false;
		$response['message'] = 'Unbale to load Profiles list';
	}
	
	echoResponse(200, $response);
});

/******************************************
Purpose: Home page Landing page Filter profiles
Parameter : NIL
Type : POST
******************************************/
$app->get('/getfilterprofiles',function () use ($app) { 
    global $db;
	
	$data = $app->request->get();
	//print_r($data);
	$search_text = ($data['search_text']) ? $data['search_text'] : '';
	$age_from = ($data['age_from']) ? $data['age_from'] : '';
	$age_to = ($data['age_to']) ? $data['age_to'] : '';
	$genderval = ($data['genderval']) ? $data['genderval'] : '';
	
	$date_debut = date("Y-m-d");
	
	$search_qry = '';
	
	if($age_from){
		$date1 = strtotime($date_debut); 
		$time1 = $age_from*31556926; 
		$dob1 = $date1 - $time1;
		$year_from = date("Y-m-d",$dob1);  
		$search_qry .= " AND p.birthday <= '".$year_from."'";
	}

	if($age_to){
		$date2 = strtotime($date_debut); 
		$time2 = $age_to*31556926; 
		$dob2 = $date2 - $time2; 
		$year_to = date("Y-m-d",$dob2); 
		$search_qry .= " AND p.birthday >= '".$year_to."'";
	}
	if($genderval){
		$search_qry .= " AND p.gender_id = '".$genderval."'";		
	}

	if($search_text){
		$search_qry .= " AND (p.first_name = '".$search_text."' OR p.last_name = '".$search_text."' OR m.profile_number like '%".$search_text."%')";		
	}
	$qry = "SELECT p.*, m.profile_group_id, m.profile_number, m.profile_number_first_name_last_name, m.version, m.current, g.name as gender_name, hc.name as hair_color_name, ec.name as eye_color_name FROM profiles p INNER JOIN memberships m ON m.profile_id = p.id INNER JOIN genders g ON g.id = p.gender_id INNER JOIN hair_colors hc ON hc.id = p.hair_color_id INNER JOIN eye_colors ec ON ec.id = p.eye_color_id  WHERE (p.profile_status_id = '1' OR  p.profile_status_id = '2') AND m.current ='1' AND p.id IN (SELECT profile_id from photos WHERE published ='1' GROUP by profile_id) ".$search_qry."  ORDER by case WHEN m.profile_number LIKE 'CF%' THEN 1 WHEN m.profile_number LIKE 'CM%' THEN 2 WHEN m.profile_number LIKE 'A%' THEN 3 WHEN m.profile_number LIKE 'J%' THEN 4  WHEN m.profile_number LIKE 'YF%' THEN 5 WHEN m.profile_number LIKE 'YM%' THEN 6 ELSE 7 END ";
	$query = $db->prepare($qry); 
	$query->execute();
    $rows = $query->fetchAll(PDO::FETCH_ASSOC);
    //echo 'sdsds';die;
//	print_r($rows[0]);exit;
    $profiles = array();
	if(count($rows)>0) {
		foreach($rows as $row){
			$birthdate = new DateTime($row['birthday']);
        	$today   = new DateTime('today');
        	$age = $birthdate->diff($today)->y;

			// Profile Image
			$profile_image ='';
			$query_image = $db->prepare("SELECT *, DATE_FORMAT(created_at, '%Y') as create_year, DATE_FORMAT(created_at, '%m') as create_month, DATE_FORMAT(created_at, '%d') as create_date FROM photos WHERE profile_id = '".$row['id']."' and published ='1' ORDER BY position ASC LIMIT 1"); 
	$query_image->execute();
	$image= '';
    $rows_image = $query_image->fetchAll(PDO::FETCH_ASSOC);

    $rows_count = count($rows_image); 
	if($rows_count > 0){
		$min = 0;
		$max = $rows_count - 1;
		$random_index = rand($min, $max);
		if (strpos($rows_image[$random_index]['path'], 'vhost') !== false) {
			$path = $rows_image[$random_index]['path'];
			$profile_image = 'http://134.213.29.220/images/uploads/'.$rows_image[$random_index]['image'];
		}
		else{
			$path = $rows_image[$random_index]['create_year']."/".$rows_image[$random_index]['create_month']."/".$rows_image[$random_index]['create_date']."/".$rows_image[$random_index]['id']."/big_";
			$profile_image = 'http://134.213.29.220/profile_images/'.$path.$rows_image[$random_index]['image'];
		}
		
		}
		$name = $row['first_name'];
	if($search_text){
		  if (mb_stristr($row['last_name'], $search_text, true)) {
			$name = $row['last_name'];
		}
		else{
			$name = $row['first_name'];
		}
	}

			$profiles[] = array('id' 			=> $row['id'],
								'bureau' 		=> $row['bureau'],
								'nationality' 	=> $row['nationality'],
								'name' 			=> $name,
								'first_name' 	=> $row['first_name'],
								'last_name' 	=> $row['last_name'],
								'profile_number' 	=> $row['profile_number'],
								'profile_image' 	=> $profile_image,
								'gender_id' 	=> $row['gender_id'],
								'gender_name' 	=> $row['gender_name'],
								'age' 			=> $age,
								'height' 		=> $row['height'],
								'weight' 		=> $row['weight'],
								'address' 		=> $row['address'],
								'zipcode' 		=> $row['zipcode'],
								'city' 			=> $row['city'],
								'phone' 		=> $row['phone'],
								'cellphone' 	=> $row['cellphone'],
								'email' 		=> $row['email'],
								'job' 			=> $row['job'],

							);
		}
		$response = array('success' => true, 'profiles' => $profiles );
	}
	
	else {
		$response['success'] = false;
		$response['message'] = 'Unbale to load Profiles list';
	}
	
	echoResponse(200, $response);
});

/******************************************
Purpose: Home page Landing page
Parameter : NIL
Type : POST
******************************************/
$app->get('/getsingleprofiles',function () use ($app) { 
    global $db;
	$profileid = $app->request->get('profileid');
	if($profileid)
	$query = $db->prepare("SELECT p.*, m.profile_group_id, m.profile_number, m.profile_number_first_name_last_name, m.version, m.current, g.name as gender_name, hc.name as hair_color_name, ec.name as eye_color_name FROM profiles p INNER JOIN memberships m ON m.profile_id = p.id INNER JOIN genders g ON g.id = p.gender_id INNER JOIN hair_colors hc ON hc.id = p.hair_color_id INNER JOIN eye_colors ec ON ec.id = p.eye_color_id  WHERE p.id='".$profileid."' AND (p.profile_status_id = '1' OR  p.profile_status_id = '2') AND m.current ='1' LIMIT 1"); 
	$query->execute();
    $rows = $query->fetchAll(PDO::FETCH_ASSOC);
    //echo 'sdsds';die;
//	print_r($rows[0]);exit;
    $profile_data = array();
	if(count($rows)>0) {
		$row = $rows[0];
		$birthdate = new DateTime($row['birthday']);
		$today   = new DateTime('today');
		$age = $birthdate->diff($today)->y;

		// Profile Image
		$profile_images = array();
		$query_image = $db->prepare("SELECT *, DATE_FORMAT(created_at, '%Y') as create_year, DATE_FORMAT(created_at, '%m') as create_month, DATE_FORMAT(created_at, '%d') as create_date FROM photos WHERE profile_id = '".$row['id']."' and published ='1' ORDER BY position ASC"); 
		$query_image->execute();
		$image= '';
		$rows_image = $query_image->fetchAll(PDO::FETCH_ASSOC);
		$imgc = 1;
		if(count($rows_image) > 0){
			foreach($rows_image as $row_image){
				if (strpos($row_image['path'], 'vhost') !== false) {
					$fullpath = 'http://134.213.29.220/images/uploads/'.$row_image['image'];
				}
				else{
					$path = $row_image['create_year']."/".$row_image['create_month']."/".$row_image['create_date']."/".$row_image['id']."/big_";
					$fullpath = 'http://134.213.29.220/profile_images/'.$path.$row_image['image'];
				}
				
				$profile_images[] = array('imgcnt' => $imgc, 'urloriginal' =>$row_image['image'], 'fullpath'=>$fullpath);
				
				$imgc++;
			}
		}
		
		// Profile Videos
		$profile_videos = array();
		$query_video = $db->prepare("SELECT * FROM videos WHERE profile_id = '".$row['id']."' and published ='1' ORDER BY position ASC"); 
		$query_video->execute();
		$video= '';
		$rows_video = $query_video->fetchAll(PDO::FETCH_ASSOC);
		$videoc = 1;
		if(count($rows_video) > 0){
			foreach($rows_video as $row_video){
				/*
				if (strpos($row_video['path'], 'vhost') !== false) {
					$vpath = 'http://134.213.29.220/images/uploads/'.$row_video['filename'];
					$thumbpath = 'http://134.213.29.220/images/uploads/'.$row_video['filename'];
				}
				else{
					$vpath = 'http://assets3.castit.dk'.$row_video['path']."/".$row_video['filename'];
					$thumbpath = 'http://assets3.castit.dk'.$row_video['thumbnail_photo_path']."/".$row_video['thumbnail_photo_filename'];
				} */
				$vpath = 'http://assets3.castit.dk'.$row_video['path']."/".$row_video['filename'];
					$thumbpath = 'http://assets3.castit.dk'.$row_video['thumbnail_photo_path']."/".$row_video['thumbnail_photo_filename'];
				$profile_videos[] = array('vidcnt' => $videoc, 'urloriginal' =>$vpath, 'img_thum'=>$thumbpath, 'fullpath'=>$vpath);
				$videoc++;
			}
		}


		$skills= '-';
		$query_skills = $db->prepare("SELECT s.name FROM skills s JOIN profiles_skills ps ON ps.skill_id = s.id WHERE ps.profile_id = '".$row['id']."'"); 
		$query_skills->execute();
		$rows_skills = $query_skills->fetchAll(PDO::FETCH_ASSOC);
		if(count($rows_skills) > 0){
			foreach($rows_skills as $rows_skill){
				$skillsa[] = $rows_skill['name'];
			}
			$skills=  implode(". ",$skillsa);
		}

		$categories= '-';
		$query_exp = $db->prepare("SELECT c.name FROM categories c JOIN categories_profiles cp ON cp.category_id = c.id WHERE cp.profile_id = '".$row['id']."'"); 
		$query_exp->execute();
		$rows_exp = $query_exp->fetchAll(PDO::FETCH_ASSOC);
		if(count($rows_exp) > 0){
			$expa=array();
			foreach($rows_exp as $row_exp){
				$expa[] = $row_exp['name'];
			}
			$categories=  implode(", ",$expa);
		}
		$licenses="-";
		$query_licenses= $db->prepare("SELECT l.name FROM drivers_licenses l JOIN drivers_licenses_profiles lp ON lp.drivers_license_id = l.id WHERE lp.profile_id = '".$row['id']."'"); 
		$query_licenses->execute();
		$rows_licenses = $query_licenses->fetchAll(PDO::FETCH_ASSOC);
		if(count($rows_licenses) > 0){
			$lic=array();
			foreach($rows_licenses as $rows_license){
				$lic[] = $rows_license['name'];
			}
			$licenses=  implode(", ",$lic);
		}


		$lang= array();
		//echo $row['id'];
		$query_lang = $db->prepare("SELECT lp.*, lpl.name FROM language_proficiencies lp, language_proficiency_languages lpl WHERE lpl.id = lp.language_proficiency_language_id AND lp.profile_id = '".$row['id']."' ORDER BY lp.id"); 
		$query_lang->execute();
		$rows_langs = $query_lang->fetchAll(PDO::FETCH_ASSOC);
		if(count($rows_langs) > 0){
			foreach($rows_langs as $rows_lang){
				$langstar = array();
				for($lc = 1; $lc <= 4;$lc++){
					$langstar[] = array('star'=>($lc <= $rows_lang['language_proficiency_rating_id']) ? 'star3.png': 'star4.png');
				}
				
				$lang[] = array('name' => $rows_lang['name'], 'rating' => $rows_lang['language_proficiency_rating_id'], 'langstar'=>$langstar);
				
			}
		}
		//print_r($lang);
		//die;
			$shoes = ($row['shoe_size_from']) ? $row['shoe_size_from'] : '';
			$shoes = ($row['shoe_size_to']) ? $shoes. " - ".$row['shoe_size_to'] : $shoes;

			$shirt = ($row['shirt_size_from']) ? $row['shirt_size_from'] : '';
			$shirt = ($row['shirt_size_to']) ? $shirt. " - ".$row['shirt_size_to'] : $shirt;

			$pants = ($row['pants_size_from']) ? $row['pants_size_from'] : '';
			$pants = ($row['pants_size_to']) ? $pants. " - ".$row['pants_size_to'] : $pants;

			$bra = ($row['bra_size']) ? $row['bra_size'] : '';

			$children = ($row['children_sizes']) ? $row['children_sizes'] : '';

			$profile_data = array('id' 			=> $row['id'],
								'bureau' 		=> $row['bureau'],
								'nationality' 	=> $row['nationality'],
								'name' 			=> $row['first_name'],
								'first_name' 	=> $row['first_name'],
								'last_name' 	=> $row['last_name'],
								'profile_number' 	=> $row['profile_number'],
								'profile_image' 	=> $profile_images,
								'gender_id' 	=> $row['gender_id'],
								'gender_name' 	=> $row['gender_name'],
								'age' 			=> $age,
								'height' 		=> $row['height'],
								'weight' 		=> $row['weight'],
								'hair_color_name' 	=> $row['hair_color_name'],
								'eye_color_name' 	=> $row['eye_color_name'],
								'shoes' 		=> $shoes,
								'shirt' 		=> $shirt,
								'pants' 		=> $pants,
								'bra' 			=> $bra,
								'children' 		=> $children,
								/*'address' 		=> $row['address'],
								'zipcode' 		=> $row['zipcode'],
								'city' 			=> $row['city'],
								'phone' 		=> $row['phone'],
								'cellphone' 	=> $row['cellphone'],
								'email' 		=> $row['email'],*/
								'experience' 			=> $row['job'],

							);
		
		$response = array('success' => true, 'profile' => $profile_data, 'profile_images' => $profile_images, 'profile_videos' => $profile_videos, 'skills' => $skills, 'categories' => $categories, 'licenses'=>$licenses, 'lang' => $lang );
	
	}
	
	else {
		$response['success'] = false;
		$response['message'] = 'Unbale to load Profiles list';
	}
	//print_r($response);die;
	
	echoResponse(200, $response);
});

/******************************************
Purpose: Update in to lightbox profiles list
Parameter : profile id
Type : GET
******************************************/
$app->get('/updatelightboxprofiles', function () use ($app) { 
    global $db;
	$profileid =  $app->request->get('profileid');
	$lb_pprofiles = array();
	$reponse = array();
	$rowcount = 0;
	$profile_note =  $app->request->get('profile_notes');
	$profile_grouping =  $app->request->get('selectedgroupings');
	$grouptoken =  $app->request->get('grouptoken');

	if(isset($_SESSION["lightbox_token"])){
		$lightbox_token = $_SESSION["lightbox_token"];
	}else{
		$lightbox_token = generate_uuid();
		$_SESSION["lightbox_token"] = $lightbox_token;
	}
	
	
	$query_check_lb = $db->prepare("SELECT * FROM lightboxes where token = '".$lightbox_token."'"); 
	$query_check_lb->execute();
	$rows_lb = $query_check_lb->fetchAll(PDO::FETCH_ASSOC);
	if(count($rows_lb)>0) {
		$lbid = $rows_lb[0]['id'];
	}else{
			$q_lightbox = "INSERT INTO `lightboxes` ( `token`) VALUES ('".$lightbox_token."')"; 
			//echo $q; 
			$lbid = $db->exec($q_lightbox);
	}
	
	$rows_check = $db->select("lightboxes_profiles","lightbox_id, profile_id", array('lightbox_id' => $lbid, 'profile_id' => $profileid), "");
		
	if(count($rows_check['data'])>0) {
		// nothing update when profile ID already existing
	}else{
		// insert the profile ID in to lighbox list
		$q_insert_profile = "INSERT INTO `lightboxes_profiles` ( `lightbox_id`, `profile_id`) VALUES ('".$lbid."', '".$profileid."')"; 
		//echo $q; 
		$query_insert_profile = $db->prepare($q_insert_profile);
		$query_insert_profile->execute();

	}

	$_SESSION["lb_notes"][$profileid] = ($profile_note) ? $profile_note : '';
	
	if($profile_grouping){
		$selectedgroupings = explode(',',$profile_grouping);
		
		foreach($selectedgroupings as $key => $gidval){
			$query_check_gp = $db->prepare("SELECT * FROM profile_grouping where profile_id = '".$profileid."' AND group_id = '".$gidval."' "); 
			$query_check_gp->execute();
			$rows_gp = $query_check_gp->fetchAll(PDO::FETCH_ASSOC);
			if(count($rows_gp)>0) {
				
			}else{
				$q_insert_profilegp = "INSERT INTO `profile_grouping` ( `profile_id`, `group_id`) VALUES ('".$profileid."', '".$gidval."')"; 
				//echo $q; 
				$query_insert_profilegp = $db->prepare($q_insert_profilegp);
				$query_insert_profilegp->execute();
			}
		}
	}
		
			$query_lb_pprofiles = $db->prepare("SELECT * FROM lightboxes_profiles WHERE lightbox_id ='".$lbid."'"); 
			$query_lb_pprofiles->execute();
			$rows_lb_pprofiles = $query_lb_pprofiles->fetchAll(PDO::FETCH_ASSOC);
			$rowcount = count($rows_lb_pprofiles);
			if($rowcount > 0){
				foreach($rows_lb_pprofiles as $rowp) {
					//$lb_pprofiles[] = array('id' => $rowp['profile_id']);
					
						$query = $db->prepare("SELECT p.*, m.profile_group_id, m.profile_number, m.profile_number_first_name_last_name, m.version, m.current, g.name as gender_name, hc.name as hair_color_name, ec.name as eye_color_name FROM profiles p INNER JOIN memberships m ON m.profile_id = p.id INNER JOIN genders g ON g.id = p.gender_id INNER JOIN hair_colors hc ON hc.id = p.hair_color_id INNER JOIN eye_colors ec ON ec.id = p.eye_color_id  WHERE p.id='".$rowp['profile_id']."' AND (p.profile_status_id = '1' OR  p.profile_status_id = '2') AND m.current ='1' LIMIT 1"); 
						$query->execute();
						$rows = $query->fetchAll(PDO::FETCH_ASSOC);
						//echo 'sdsds';die;
					//	print_r($rows[0]);exit;
						if(count($rows)>0) {
							$row = $rows[0];
								$birthdate = new DateTime($row['birthday']);
								$today   = new DateTime('today');
								$age = $birthdate->diff($today)->y;
					
								// Profile Image
								$profile_image ='';
								$query_image = $db->prepare("SELECT *, DATE_FORMAT(created_at, '%Y') as create_year, DATE_FORMAT(created_at, '%m') as create_month, DATE_FORMAT(created_at, '%d') as create_date FROM photos WHERE profile_id = '".$row['id']."' and published ='1' ORDER BY position ASC LIMIT 1"); 
						$query_image->execute();
						$image= '';
						$rows_image = $query_image->fetchAll(PDO::FETCH_ASSOC);
						if(count($rows_image) > 0){
							if (strpos($rows_image[0]['path'], 'vhost') !== false) {
								$profile_image = 'http://134.213.29.220/images/uploads/'.$rows_image[0]['image'];
							}
							else{
								$path = $rows_image[0]['create_year']."/".$rows_image[0]['create_month']."/".$rows_image[0]['create_date']."/".$rows_image[0]['id']."/thumb_";
								$profile_image = 'http://134.213.29.220/profile_images/'.$path.$rows_image[0]['image'];
							}
							
							}
								$lb_note = isset($_SESSION["lb_notes"][$row['id']]) ? $_SESSION["lb_notes"][$row['id']]: '' ;
								$groupnamear = array();
							
								$lb_group_qry = "SELECT pg.*, g.group_name from profile_grouping pg JOIN grouping g ON pg.group_id = g.group_id AND g.token_id = '".$grouptoken."' WHERE pg.profile_id='".$row['id']."'";	
							
								$query_group = $db->prepare($lb_group_qry);
								$query_group->execute();
								$rows_group = $query_group->fetchAll(PDO::FETCH_ASSOC);
								if(count($rows_group) > 0){
									foreach($rows_group as $rowgp) {
										$groupnamear[] = 	$rowgp['group_name'];
									}
									$groupname = implode(",",$groupnamear);
								}
								else{
									$groupname = '-';
								}
					
								$lb_pprofiles[] = array('id' 			=> $row['id'],
													'bureau' 		=> $row['bureau'],
													'nationality' 	=> $row['nationality'],
													'name' 			=> $row['first_name'],
													'first_name' 	=> $row['first_name'],
													'last_name' 	=> $row['last_name'],
													'profile_number' 	=> $row['profile_number'],
													'profile_image' 	=> $profile_image,
													'gender_id' 	=> $row['gender_id'],
													'gender_name' 	=> $row['gender_name'],
													'age' 			=> $age,
													'height' 		=> $row['height'],
													'weight' 		=> $row['weight'],
													/*'address' 		=> $row['address'],
													'zipcode' 		=> $row['zipcode'],
													'city' 			=> $row['city'],
													'phone' 		=> $row['phone'],
													'cellphone' 	=> $row['cellphone'],
													'email' 		=> $row['email'], */
													'job' 			=> $row['job'],
													'lb_note' 			=> $lb_note,
													'group_name' 	=> $groupname,					
												);
							
							
						}
					
				}
			}

		
 			$reponse =  array('count'=> $rowcount, 'lbprofiles'=>$lb_pprofiles);
			echoResponse(200, $reponse);
  
});

/******************************************
Purpose: Remove profile from lightbox profiles list
Parameter : profile id
Type : GET
******************************************/
$app->get('/removelightboxprofiles', function () use ($app) { 
    global $db;
	$profileid =  $app->request->get('profileid');
	$lb_pprofiles = array();
	$reponse = array();
	$rowcount = 0;
	$grouptoken =  $app->request->get('grouptoken');

	if(isset($_SESSION["lightbox_token"])){
		$lightbox_token = $_SESSION["lightbox_token"];
	}
	
	if($lightbox_token){
		$query_check_lb = $db->prepare("SELECT * FROM lightboxes where token = '".$lightbox_token."'"); 
		$query_check_lb->execute();
		$rows_lb = $query_check_lb->fetchAll(PDO::FETCH_ASSOC);
		if(count($rows_lb)>0) {
			$lbid = $rows_lb[0]['id'];
			$q_del_profile = "DELETE FROM `lightboxes_profiles` WHERE `lightbox_id` = '".$lbid."' AND `profile_id` = '".$profileid."'"; 
			//echo $q; 
			$query_del_profile = $db->prepare($q_del_profile);
			$query_del_profile->execute();
		}
	}

			$query_lb_pprofiles = $db->prepare("SELECT * FROM lightboxes_profiles WHERE lightbox_id ='".$lbid."'"); 
			$query_lb_pprofiles->execute();
			$rows_lb_pprofiles = $query_lb_pprofiles->fetchAll(PDO::FETCH_ASSOC);
			$rowcount = count($rows_lb_pprofiles);
			if($rowcount > 0){
				foreach($rows_lb_pprofiles as $rowp) {
					//$lb_pprofiles[] = array('id' => $rowp['profile_id']);
					
						$query = $db->prepare("SELECT p.*, m.profile_group_id, m.profile_number, m.profile_number_first_name_last_name, m.version, m.current, g.name as gender_name, hc.name as hair_color_name, ec.name as eye_color_name FROM profiles p INNER JOIN memberships m ON m.profile_id = p.id INNER JOIN genders g ON g.id = p.gender_id INNER JOIN hair_colors hc ON hc.id = p.hair_color_id INNER JOIN eye_colors ec ON ec.id = p.eye_color_id  WHERE p.id='".$rowp['profile_id']."' AND (p.profile_status_id = '1' OR  p.profile_status_id = '2') AND m.current ='1' LIMIT 1"); 
						$query->execute();
						$rows = $query->fetchAll(PDO::FETCH_ASSOC);
						//echo 'sdsds';die;
					//	print_r($rows[0]);exit;
						if(count($rows)>0) {
							$row = $rows[0];
								$birthdate = new DateTime($row['birthday']);
								$today   = new DateTime('today');
								$age = $birthdate->diff($today)->y;
					
								// Profile Image
								$profile_image ='';
								$query_image = $db->prepare("SELECT *, DATE_FORMAT(created_at, '%Y') as create_year, DATE_FORMAT(created_at, '%m') as create_month, DATE_FORMAT(created_at, '%d') as create_date FROM photos WHERE profile_id = '".$row['id']."' and published ='1' ORDER BY position ASC LIMIT 1"); 
						$query_image->execute();
						$image= '';
						$rows_image = $query_image->fetchAll(PDO::FETCH_ASSOC);
						if(count($rows_image) > 0){
							if (strpos($rows_image[0]['path'], 'vhost') !== false) {
								$profile_image = 'http://134.213.29.220/images/uploads/'.$rows_image[0]['image'];
							}
							else{
								$path = $rows_image[0]['create_year']."/".$rows_image[0]['create_month']."/".$rows_image[0]['create_date']."/".$rows_image[0]['id']."/thumb_";
								$profile_image = 'http://134.213.29.220/profile_images/'.$path.$rows_image[0]['image'];
							}
							
							}
								$lb_note = isset($_SESSION["lb_notes"][$row['id']]) ? $_SESSION["lb_notes"][$row['id']]: '' ;
								$groupnamear = array();
								$lb_group_qry = "SELECT pg.*, g.group_name from profile_grouping pg JOIN grouping g ON pg.group_id = g.group_id  AND g.token_id = '".$grouptoken."' WHERE pg.profile_id='".$row['id']."'";	
								$query_group = $db->prepare($lb_group_qry);
								$query_group->execute();
								$rows_group = $query_group->fetchAll(PDO::FETCH_ASSOC);
								if(count($rows_group) > 0){
									foreach($rows_group as $rowgp) {
										$groupnamear[] = 	$rowgp['group_name'];
									}
									$groupname = implode(",",$groupnamear);
								}
								else{
									$groupname = '-';
								}
					
								$lb_pprofiles[] = array('id' 			=> $row['id'],
													'bureau' 		=> $row['bureau'],
													'nationality' 	=> $row['nationality'],
													'name' 			=> $row['first_name'],
													'first_name' 	=> $row['first_name'],
													'last_name' 	=> $row['last_name'],
													'profile_number' 	=> $row['profile_number'],
													'profile_image' 	=> $profile_image,
													'gender_id' 	=> $row['gender_id'],
													'gender_name' 	=> $row['gender_name'],
													'age' 			=> $age,
													'height' 		=> $row['height'],
													'weight' 		=> $row['weight'],
													/*'address' 		=> $row['address'],
													'zipcode' 		=> $row['zipcode'],
													'city' 			=> $row['city'],
													'phone' 		=> $row['phone'],
													'cellphone' 	=> $row['cellphone'],
													'email' 		=> $row['email'],*/
													'job' 			=> $row['job'],
													'lb_note' 			=> $lb_note,
													'group_name' 	=> $groupname,
												);
							
							
						}
					
				}
			}

		
 			$reponse =  array('count'=> $rowcount, 'lbprofiles'=>$lb_pprofiles);
			echoResponse(200, $reponse);
  
});


/******************************************
Purpose: Get lightbox profiles list
Parameter : null
Type : GET
******************************************/
$app->get('/getlightboxprofiles', function () use ($app) { 
    global $db;
	$profileid =  $app->request->get('profileid');
	$lb_pprofiles = array();
	$reponse = array();
	$rowcount = 0;
	$grouptoken =  $app->request->get('grouptoken');

	if(isset($_SESSION["lightbox_token"])){
		$lightbox_token = $_SESSION["lightbox_token"];
	}else{
		$lightbox_token = generate_uuid();
		$_SESSION["lightbox_token"] = $lightbox_token;
	}
	
	
	$query_check_lb = $db->prepare("SELECT * FROM lightboxes where token = '".$lightbox_token."'"); 
	$query_check_lb->execute();
	$rows_lb = $query_check_lb->fetchAll(PDO::FETCH_ASSOC);
	if(count($rows_lb)>0) {
		$lbid = $rows_lb[0]['id'];
	}else{
			$q_lightbox = "INSERT INTO `lightboxes` ( `token`) VALUES ('".$lightbox_token."')"; 
			//echo $q; 
			$lbid = $db->exec($q_lightbox);
	}
			
	$query_lb_pprofiles = $db->prepare("SELECT * FROM lightboxes_profiles WHERE lightbox_id ='".$lbid."'"); 
	$query_lb_pprofiles->execute();
	$rows_lb_pprofiles = $query_lb_pprofiles->fetchAll(PDO::FETCH_ASSOC);
	$rowcount = count($rows_lb_pprofiles);
	if($rowcount > 0){
		foreach($rows_lb_pprofiles as $rowp) {
			
				$query = $db->prepare("SELECT p.*, m.profile_group_id, m.profile_number, m.profile_number_first_name_last_name, m.version, m.current, g.name as gender_name, hc.name as hair_color_name, ec.name as eye_color_name FROM profiles p INNER JOIN memberships m ON m.profile_id = p.id INNER JOIN genders g ON g.id = p.gender_id INNER JOIN hair_colors hc ON hc.id = p.hair_color_id INNER JOIN eye_colors ec ON ec.id = p.eye_color_id  WHERE p.id='".$rowp['profile_id']."' AND (p.profile_status_id = '1' OR  p.profile_status_id = '2') AND m.current ='1' LIMIT 1"); 

				$query->execute();
				$rows = $query->fetchAll(PDO::FETCH_ASSOC);
				if(count($rows)>0) {
					$row = $rows[0];
						$birthdate = new DateTime($row['birthday']);
						$today   = new DateTime('today');
						$age = $birthdate->diff($today)->y;
			
						// Profile Image
						$profile_image ='';
						$query_image = $db->prepare("SELECT *, DATE_FORMAT(created_at, '%Y') as create_year, DATE_FORMAT(created_at, '%m') as create_month, DATE_FORMAT(created_at, '%d') as create_date FROM photos WHERE profile_id = '".$row['id']."' and published ='1' ORDER BY position ASC LIMIT 1"); 
				$query_image->execute();
				$image= '';
				$rows_image = $query_image->fetchAll(PDO::FETCH_ASSOC);
				if(count($rows_image) > 0){
					if (strpos($rows_image[0]['path'], 'vhost') !== false) {
						$profile_image = 'http://134.213.29.220/images/uploads/'.$rows_image[0]['image'];
					}
					else{
						$path = $rows_image[0]['create_year']."/".$rows_image[0]['create_month']."/".$rows_image[0]['create_date']."/".$rows_image[0]['id']."/thumb_";
						$profile_image = 'http://134.213.29.220/profile_images/'.$path.$rows_image[0]['image'];
					}
				}
				$groupnamear = array();
				$lb_group_qry = "SELECT pg.*, g.group_name from profile_grouping pg JOIN grouping g ON pg.group_id = g.group_id AND g.token_id = '".$grouptoken."' WHERE pg.profile_id='".$row['id']."'";	
				$query_group = $db->prepare($lb_group_qry);
				$query_group->execute();
				$rows_group = $query_group->fetchAll(PDO::FETCH_ASSOC);
				if(count($rows_group) > 0){
					foreach($rows_group as $rowgp) {
						$groupnamear[] = 	$rowgp['group_name'];
					}
					$groupname = implode(",",$groupnamear);
				}
				else{
					$groupname = '-';
				}
				
				
				$lb_note = isset($_SESSION["lb_notes"][$row['id']]) ? $_SESSION["lb_notes"][$row['id']]: '' ;
				$lb_pprofiles[] = array('id' 			=> $row['id'],
									'bureau' 		=> $row['bureau'],
									'nationality' 	=> $row['nationality'],
									'name' 			=> $row['first_name'],
									'first_name' 	=> $row['first_name'],
									'last_name' 	=> $row['last_name'],
									'profile_number' 	=> $row['profile_number'],
									'profile_image' 	=> $profile_image,
									'gender_id' 	=> $row['gender_id'],
									'gender_name' 	=> $row['gender_name'],
									'age' 			=> $age,
									'height' 		=> $row['height'],
									'weight' 		=> $row['weight'],
									/*'address' 		=> $row['address'],
									'zipcode' 		=> $row['zipcode'],
									'city' 			=> $row['city'],
									'phone' 		=> $row['phone'],
									'cellphone' 	=> $row['cellphone'],
									'email' 		=> $row['email'],*/
									'job' 			=> $row['job'],
									'lb_note' 		=> $lb_note,
									'group_name' 	=> $groupname,
								);
				}
		}
	}

	$reponse =  array('count'=> $rowcount, 'lbprofiles'=>$lb_pprofiles);
	echoResponse(200, $reponse);
  
});

/******************************************
Purpose: Get Grouping profiles list
Parameter : null
Type : GET
******************************************/
$app->get('/getgroupingprofiles', function () use ($app) { 
    global $db;
	$reponse = array();
	$rowcount = 0;
	$grouping_profile = array();
	$grouptoken =  $app->request->get('grouptoken');

	$query_check_gb = $db->prepare("SELECT *, date_format(added_on , '%d.%m.%Y') as addedon  FROM grouping where token_id= '".$grouptoken."' AND status = '1' order by group_name asc"); 
	$query_check_gb->execute();
	$rows_gb = $query_check_gb->fetchAll(PDO::FETCH_ASSOC);
	if(count($rows_gb)>0) {
		$rowcount = count($rows_gb);
		foreach($rows_gb as $rowgp){

			$lb_pprofiles = array();
			$gpid = 	$rowgp['group_id'];
			$query_lb_pprofiles = $db->prepare("SELECT * FROM profile_grouping WHERE group_id ='".$gpid."'"); 
			$query_lb_pprofiles->execute();
			$rows_lb_pprofiles = $query_lb_pprofiles->fetchAll(PDO::FETCH_ASSOC);
			$rowcountpg = count($rows_lb_pprofiles);
			if($rowcountpg > 0){
				foreach($rows_lb_pprofiles as $rowp) {
					
						$query = $db->prepare("SELECT p.*, m.profile_group_id, m.profile_number, m.profile_number_first_name_last_name, m.version, m.current, g.name as gender_name, hc.name as hair_color_name, ec.name as eye_color_name FROM profiles p INNER JOIN memberships m ON m.profile_id = p.id INNER JOIN genders g ON g.id = p.gender_id INNER JOIN hair_colors hc ON hc.id = p.hair_color_id INNER JOIN eye_colors ec ON ec.id = p.eye_color_id  WHERE p.id='".$rowp['profile_id']."' AND (p.profile_status_id = '1' OR  p.profile_status_id = '2') AND m.current ='1' LIMIT 1"); 
						$query->execute();
						$rows = $query->fetchAll(PDO::FETCH_ASSOC);
						if(count($rows)>0) {
							$row = $rows[0];
								$birthdate = new DateTime($row['birthday']);
								$today   = new DateTime('today');
								$age = $birthdate->diff($today)->y;
					
								// Profile Image
								$profile_image ='';
								$query_image = $db->prepare("SELECT *, DATE_FORMAT(created_at, '%Y') as create_year, DATE_FORMAT(created_at, '%m') as create_month, DATE_FORMAT(created_at, '%d') as create_date FROM photos WHERE profile_id = '".$row['id']."' and published ='1' ORDER BY position ASC LIMIT 1"); 
						$query_image->execute();
						$image= '';
						$rows_image = $query_image->fetchAll(PDO::FETCH_ASSOC);
						if(count($rows_image) > 0){
							if (strpos($rows_image[0]['path'], 'vhost') !== false) {
								$profile_image = 'http://134.213.29.220/images/uploads/'.$rows_image[0]['image'];
							}
							else{
								$path = $rows_image[0]['create_year']."/".$rows_image[0]['create_month']."/".$rows_image[0]['create_date']."/".$rows_image[0]['id']."/thumb_";
								$profile_image = 'http://134.213.29.220/profile_images/'.$path.$rows_image[0]['image'];
							}
							
						}
						$lb_note = isset($_SESSION["lb_notes"][$row['id']]) ? $_SESSION["lb_notes"][$row['id']]: '' ;
						$lb_pprofiles[] = array('id' 			=> $row['id'],
											'bureau' 		=> $row['bureau'],
											'nationality' 	=> $row['nationality'],
											'name' 			=> $row['first_name'],
											'first_name' 	=> $row['first_name'],
											'last_name' 	=> $row['last_name'],
											'profile_number' 	=> $row['profile_number'],
											'profile_image' 	=> $profile_image,
											'gender_id' 	=> $row['gender_id'],
											'gender_name' 	=> $row['gender_name'],
											'age' 			=> $age,
											'height' 		=> $row['height'],
											'weight' 		=> $row['weight'],
											/*'address' 		=> $row['address'],
											'zipcode' 		=> $row['zipcode'],
											'city' 			=> $row['city'],
											'phone' 		=> $row['phone'],
											'cellphone' 	=> $row['cellphone'],
											'email' 		=> $row['email'],*/
											'job' 			=> $row['job'],
											'lb_note' 			=> $lb_note,
										);
						}
				}
			}
			$grouping_profile[] = array('group_id' => $rowgp['group_id'],
										'group_name'=>$rowgp['group_name'],
										'group_token'=>$rowgp['token_id'],
										'addedon' => $rowgp['addedon'],
										'group_profiles'=> $lb_pprofiles);
		}
	}

	$reponse =  array('count'=> $rowcount, 'gpprofiles'=>$grouping_profile);
	echoResponse(200, $reponse);
  
});

/******************************************
Purpose: Remove group from Grouping profiles list
Parameter : null
Type : GET
******************************************/
$app->get('/removegroupfromgrouping', function () use ($app) { 
    global $db;
	$groupid =  $app->request->get('groupid');
	$grouptoken =  $app->request->get('grouptoken');

	if($groupid){
		$query_check_gb = $db->prepare("SELECT * FROM grouping where group_id = '".$groupid."' AND token_id = '".$grouptoken."'"); 
		$query_check_gb->execute();
		$rows_gb = $query_check_gb->fetchAll(PDO::FETCH_ASSOC);
		if(count($rows_gb)>0) {
			//$gbid = $rows_gb[0]['group_id'];
			$q_del_gp = "DELETE FROM `grouping` WHERE `group_id` = '".$groupid."'"; 
			$query_del_grp = $db->prepare($q_del_gp);
			$query_del_grp->execute();

			$q_del_gp1 = "DELETE FROM `profile_grouping` WHERE `group_id` = '".$groupid."'"; 
			$query_del_grp1 = $db->prepare($q_del_gp1);
			$query_del_grp1->execute();

		}
	}


	$reponse = array();
	$rowcount = 0;
	$grouping_profile = array();

	$query_check_gb = $db->prepare("SELECT *, date_format(added_on , '%d.%m.%Y') as addedon  FROM grouping where status = '1' AND token_id = '".$grouptoken."' order by group_name asc"); 
	$query_check_gb->execute();
	$rows_gb = $query_check_gb->fetchAll(PDO::FETCH_ASSOC);
	if(count($rows_gb)>0) {
		$rowcount = count($rows_gb);
		foreach($rows_gb as $rowgp){

			$lb_pprofiles = array();
			$gpid = 	$rowgp['group_id'];
			$query_lb_pprofiles = $db->prepare("SELECT * FROM profile_grouping WHERE group_id ='".$gpid."'"); 
			$query_lb_pprofiles->execute();
			$rows_lb_pprofiles = $query_lb_pprofiles->fetchAll(PDO::FETCH_ASSOC);
			$rowcountpg = count($rows_lb_pprofiles);
			if($rowcountpg > 0){
				foreach($rows_lb_pprofiles as $rowp) {
					
						$query = $db->prepare("SELECT p.*, m.profile_group_id, m.profile_number, m.profile_number_first_name_last_name, m.version, m.current, g.name as gender_name, hc.name as hair_color_name, ec.name as eye_color_name FROM profiles p INNER JOIN memberships m ON m.profile_id = p.id INNER JOIN genders g ON g.id = p.gender_id INNER JOIN hair_colors hc ON hc.id = p.hair_color_id INNER JOIN eye_colors ec ON ec.id = p.eye_color_id  WHERE p.id='".$rowp['profile_id']."' AND (p.profile_status_id = '1' OR  p.profile_status_id = '2') AND m.current ='1' LIMIT 1"); 
						$query->execute();
						$rows = $query->fetchAll(PDO::FETCH_ASSOC);
						if(count($rows)>0) {
							$row = $rows[0];
								$birthdate = new DateTime($row['birthday']);
								$today   = new DateTime('today');
								$age = $birthdate->diff($today)->y;
					
								// Profile Image
								$profile_image ='';
								$query_image = $db->prepare("SELECT *, DATE_FORMAT(created_at, '%Y') as create_year, DATE_FORMAT(created_at, '%m') as create_month, DATE_FORMAT(created_at, '%d') as create_date FROM photos WHERE profile_id = '".$row['id']."' and published ='1' ORDER BY position ASC LIMIT 1"); 
						$query_image->execute();
						$image= '';
						$rows_image = $query_image->fetchAll(PDO::FETCH_ASSOC);
						if(count($rows_image) > 0){
							if (strpos($rows_image[0]['path'], 'vhost') !== false) {
								$profile_image = 'http://134.213.29.220/images/uploads/'.$rows_image[0]['image'];
							}
							else{
								$path = $rows_image[0]['create_year']."/".$rows_image[0]['create_month']."/".$rows_image[0]['create_date']."/".$rows_image[0]['id']."/thumb_";
								$profile_image = 'http://134.213.29.220/profile_images/'.$path.$rows_image[0]['image'];
							}
						}
						$lb_note = isset($_SESSION["lb_notes"][$row['id']]) ? $_SESSION["lb_notes"][$row['id']]: '' ;
						$lb_pprofiles[] = array('id' 			=> $row['id'],
											'bureau' 		=> $row['bureau'],
											'nationality' 	=> $row['nationality'],
											'name' 			=> $row['first_name'],
											'first_name' 	=> $row['first_name'],
											'last_name' 	=> $row['last_name'],
											'profile_number' 	=> $row['profile_number'],
											'profile_image' 	=> $profile_image,
											'gender_id' 	=> $row['gender_id'],
											'gender_name' 	=> $row['gender_name'],
											'age' 			=> $age,
											'height' 		=> $row['height'],
											'weight' 		=> $row['weight'],
											/*'address' 		=> $row['address'],
											'zipcode' 		=> $row['zipcode'],
											'city' 			=> $row['city'],
											'phone' 		=> $row['phone'],
											'cellphone' 	=> $row['cellphone'],
											'email' 		=> $row['email'],*/
											'job' 			=> $row['job'],
											'lb_note' 			=> $lb_note,
										);
						}
				}
			}
			$grouping_profile[] = array('group_id' => $rowgp['group_id'],
										'group_name'=>$rowgp['group_name'],
										'group_token'=>$rowgp['token_id'],
										'addedon' => $rowgp['addedon'],
										'group_profiles'=> $lb_pprofiles);
		}
	}

	$reponse =  array('count'=> $rowcount, 'gpprofiles'=>$grouping_profile);
	echoResponse(200, $reponse);
  
});

/******************************************
Purpose: Remove group from Grouping profiles list
Parameter : null
Type : GET
******************************************/
$app->get('/addgroupintogrouping', function () use ($app) { 
    global $db;
	$groupname =  $app->request->get('groupname');
	$grouping = array();
	$reponse = array();
	$rowcount = 0;

	//$grouping_token = generate_uuid();
	$grouping_token = $app->request->get('grouptoken');
	
	$query_check_gp = $db->prepare("SELECT * FROM `grouping` where `group_name` LIKE '%".$groupname."%' AND token_id = '".$grouping_token."'"); 
	$query_check_gp->execute();
	$rows_gp = $query_check_gp->fetchAll(PDO::FETCH_ASSOC);
	if(count($rows_gp)>0) {
		$gpid = $rows_gp[0]['group_id'];
	}else{
			$q_gruping = "INSERT INTO `grouping` ( `token_id`, `group_name`, `status`) VALUES ('".$grouping_token."', '".$groupname."','1')"; 
			//echo $q; 
			$gpid = $db->exec($q_gruping);
	}


	$reponse = array();
	$rowcount = 0;
	$grouping_profile = array();

	$query_check_gb = $db->prepare("SELECT *, date_format(added_on , '%d.%m.%Y') as addedon  FROM grouping where status = '1' AND token_id = '".$grouping_token."' order by group_name asc"); 
	$query_check_gb->execute();
	$rows_gb = $query_check_gb->fetchAll(PDO::FETCH_ASSOC);
	if(count($rows_gb)>0) {
		$rowcount = count($rows_gb);
		foreach($rows_gb as $rowgp){

			$lb_pprofiles = array();
			$gpid = 	$rowgp['group_id'];
			$query_lb_pprofiles = $db->prepare("SELECT * FROM profile_grouping WHERE group_id ='".$gpid."'"); 
			$query_lb_pprofiles->execute();
			$rows_lb_pprofiles = $query_lb_pprofiles->fetchAll(PDO::FETCH_ASSOC);
			$rowcountpg = count($rows_lb_pprofiles);
			if($rowcountpg > 0){
				foreach($rows_lb_pprofiles as $rowp) {
					
						$query = $db->prepare("SELECT p.*, m.profile_group_id, m.profile_number, m.profile_number_first_name_last_name, m.version, m.current, g.name as gender_name, hc.name as hair_color_name, ec.name as eye_color_name FROM profiles p INNER JOIN memberships m ON m.profile_id = p.id INNER JOIN genders g ON g.id = p.gender_id INNER JOIN hair_colors hc ON hc.id = p.hair_color_id INNER JOIN eye_colors ec ON ec.id = p.eye_color_id  WHERE p.id='".$rowp['profile_id']."' AND (p.profile_status_id = '1' OR  p.profile_status_id = '2') AND m.current ='1' LIMIT 1"); 
						$query->execute();
						$rows = $query->fetchAll(PDO::FETCH_ASSOC);
						if(count($rows)>0) {
							$row = $rows[0];
								$birthdate = new DateTime($row['birthday']);
								$today   = new DateTime('today');
								$age = $birthdate->diff($today)->y;
					
								// Profile Image
								$profile_image ='';
								$query_image = $db->prepare("SELECT *, DATE_FORMAT(created_at, '%Y') as create_year, DATE_FORMAT(created_at, '%m') as create_month, DATE_FORMAT(created_at, '%d') as create_date FROM photos WHERE profile_id = '".$row['id']."' and published ='1' ORDER BY position ASC LIMIT 1"); 
						$query_image->execute();
						$image= '';
						$rows_image = $query_image->fetchAll(PDO::FETCH_ASSOC);
						if(count($rows_image) > 0){
							if (strpos($rows_image[0]['path'], 'vhost') !== false) {
								$profile_image = 'http://134.213.29.220/images/uploads/'.$rows_image[0]['image'];
							}
							else{
								$path = $rows_image[0]['create_year']."/".$rows_image[0]['create_month']."/".$rows_image[0]['create_date']."/".$rows_image[0]['id']."/thumb_";
								$profile_image = 'http://134.213.29.220/profile_images/'.$path.$rows_image[0]['image'];
							}
						}
						$lb_note = isset($_SESSION["lb_notes"][$row['id']]) ? $_SESSION["lb_notes"][$row['id']]: '' ;
						$lb_pprofiles[] = array('id' 			=> $row['id'],
											'bureau' 		=> $row['bureau'],
											'nationality' 	=> $row['nationality'],
											'name' 			=> $row['first_name'],
											'first_name' 	=> $row['first_name'],
											'last_name' 	=> $row['last_name'],
											'profile_number' 	=> $row['profile_number'],
											'profile_image' 	=> $profile_image,
											'gender_id' 	=> $row['gender_id'],
											'gender_name' 	=> $row['gender_name'],
											'age' 			=> $age,
											'height' 		=> $row['height'],
											'weight' 		=> $row['weight'],
											/*'address' 		=> $row['address'],
											'zipcode' 		=> $row['zipcode'],

											'city' 			=> $row['city'],
											'phone' 		=> $row['phone'],
											'cellphone' 	=> $row['cellphone'],
											'email' 		=> $row['email'],*/
											'job' 			=> $row['job'],
											'lb_note' 			=> $lb_note,
										);
						}
				}
			}
			$grouping_profile[] = array('group_id' => $rowgp['group_id'],
										'group_name'=>$rowgp['group_name'],
										'group_token'=>$rowgp['token_id'],
										'addedon' => $rowgp['addedon'],
										'group_profiles'=> $lb_pprofiles);
		}
	}

	$reponse =  array('count'=> $rowcount, 'gpprofiles'=>$grouping_profile);
	echoResponse(200, $reponse);
  
});



/******************************************
Purpose: Get all available Countries details
Parameter : 
Type : GET
******************************************/
$app->get('/countries', function () use ($app) { 
    global $db;
	$query_country = $db->prepare("SELECT * FROM countries order by name"); 
	$query_country->execute();
	$rows_countries = $query_country->fetchAll(PDO::FETCH_ASSOC);
	
    foreach($rows_countries as $row) {
		$countries[] = array('id' => $row['id'],
						  'name' => $row['name']
						);
	}
	echoResponse(200, $countries);
});




/******************************************
Purpose: Register user step 1
Parameter : nil
Type : POST
******************************************/
$app->post('/step1Create',function () use ($app) { 
    global $db;
	$data =  json_decode($app->request->getBody(), true);
  // echo '<pre>';print_r($_SESSION["step1"]);exit;
	$response = array();
	global $imageClass;

	$_SESSION["step1"]["status"] =1;
	$_SESSION["step1"]["first_name"]= $data['first_name'];
	$_SESSION["step1"]["last_name"]= $data['last_name'];
	if (isset($data['password'])) {
		$_SESSION["step1"]["password"]= $data['password'];
	}
	$_SESSION["step1"]["address"]= $data['address'];
	$_SESSION["step1"]["zipcode"]= $data['zipcode'];
	$_SESSION["step1"]["city"]= $data['city'];
	$_SESSION["step1"]["country_id"]= $data['country_id'];

	$response = array('success' => true);
			
	echoResponse(200, $response);
});

/******************************************
Purpose: Clear restration step 1 if needed
Parameter : id
Type : GET
******************************************/

$app->get('/step1Clear',function () use ($app) { 
    global $db;
	$user_id =  $app->request->get('id');
	
	if(isset($_SESSION["step1"])){
		unset ($_SESSION["step1"]);
		$response = array('success' => true);
	}
	else{
		$response = array('success' => false);
	}
	//print_r($rows);
	
	echoResponse(200, $response);
});

/******************************************
Purpose: Check restration step 1 completed or not
Parameter : id
Type : GET
******************************************/
$app->get('/checkstep1',function () use ($app) { 
    global $db;
	$user_id =  $app->request->get('id');
	$response = array('success' => false);
	if(isset($_SESSION["step1"])){
		if(isset($_SESSION["step1"]["status"])){
			$response = array('success' => true, 'step1' => $_SESSION["step1"]);
		}else{
			$response = array('success' => false, 'step1' => '' );
		}
	}
	else{
		$response = array('success' => false, 'step1' => '' );
	}
	//print_r($rows);
	
	echoResponse(200, $response);
});



/******************************************
Purpose: Get years for dropdown
Parameter : 
Type : GET
******************************************/
$app->get('/years', function () use ($app) { 
    global $db;
	for($i = 1925 ; $i < date('Y'); $i++){
		$years[] = array('id' => $i,
						  'name' => $i
						);
   }	
	echoResponse(200, $years);
});


/******************************************
Purpose: Register user step 2
Parameter : Form post parameters
Type : POST
******************************************/
$app->post('/step2Create',function () use ($app) { 
    global $db;
	$data =  json_decode($app->request->getBody(), true);
	$response = array();
	global $imageClass;

	$_SESSION["step2"]["status"] =1;
	$_SESSION["step2"]["email"]= $data['email'];
	$_SESSION["step2"]["phone"]= $data['phone'];
	$_SESSION["step2"]["phone_at_work"]= $data['phone_at_work'];
	$_SESSION["step2"]["gender_id"]= $data['gender_id'];
	$_SESSION["step2"]["birth_day"]= $data['birth_day'];
	$_SESSION["step2"]["birth_month"]= $data['birth_month'];
	$_SESSION["step2"]["birth_year"]= $data['birth_year'];
	$_SESSION["step2"]["ethinic_origin"]= $data['ethinic_origin'];

	$response = array('success' => true);
			
	echoResponse(200, $response);
});

/******************************************
Purpose: Clear restration step 2 if needed
Parameter : id
Type : GET
******************************************/

$app->get('/step2Clear',function () use ($app) { 
    global $db;
	
	if(isset($_SESSION["step2"])){
		unset ($_SESSION["step2"]);
		$response = array('success' => true);
	}
	else{
		$response = array('success' => false);
	}
	//print_r($rows);
	
	echoResponse(200, $response);
});

/******************************************
Purpose: Check restration step 2 completed or not
Parameter : id
Type : GET
******************************************/
$app->get('/checkstep2',function () use ($app) { 
    global $db;
	$user_id =  $app->request->get('id');
	$response = array('success' => false);
	if(isset($_SESSION["step1"])){
		if(isset($_SESSION["step1"]["status"])){
			$response['step1status'] = true;
		}else{
			$response['step1status'] = false;
		}
	}
	else{
		$response['step1status'] = false;
	}
	if(isset($_SESSION["step2"])){
		if(isset($_SESSION["step2"]["status"])){
			$response['step2status'] = true;
			$response['step2'] = $_SESSION["step2"];
			
			$dateOfBirth = $_SESSION["step2"]["birth_year"]."-".$_SESSION["step2"]["birth_month"]."-".$_SESSION["step2"]["birth_day"];
			$today = date("Y-m-d");
			$diff = date_diff(date_create($dateOfBirth), date_create($today));
			$response['age']=$diff->format('%y');
		}else{
			$response['step2status'] = false;
		}
	}
	else{
		$response['step2status'] = false;
	}
	//print_r($rows);
	
	echoResponse(200, $response);
});


/******************************************
Purpose: get required data for step 3 
Parameter : id
Type : GET
******************************************/
$app->get('/getstep3data',function () use ($app) { 
    global $db;
	$user_id =  $app->request->get('id');
	$response = array();
	$eye_colors = $db->prepare("SELECT * FROM eye_colors ORDER BY sortby"); 
	$eye_colors->execute();
	$eye_colors_list = $eye_colors->fetchAll(PDO::FETCH_ASSOC);
	$response['eye_colors']=$eye_colors_list;

	$hair_colors = $db->prepare("SELECT * FROM hair_colors ORDER BY sortby"); 
	$hair_colors->execute();
	$hair_colors_list = $hair_colors->fetchAll(PDO::FETCH_ASSOC);
	$response['hair_colors']=$hair_colors_list;

	$gender = $db->prepare("SELECT * FROM genders"); 
	$gender->execute();
	$gender_list = $gender->fetchAll(PDO::FETCH_ASSOC);
	$response['gender']=$gender_list;
	echoResponse(200, $response);
	
});

$app->get('/getstep2data',function () use ($app) { 
    global $db;
	$user_id =  $app->request->get('id');
	$response = array();
	$gender = $db->prepare("SELECT * FROM genders"); 
	$gender->execute();
	$gender_list = $gender->fetchAll(PDO::FETCH_ASSOC);
	$response['gender']=$gender_list;
	echoResponse(200, $response);
	
});

/******************************************
Purpose: Register user step 3
Parameter : Form post parameters
Type : POST
******************************************/
$app->post('/step3Create',function () use ($app) { 
    global $db;
	$data =  json_decode($app->request->getBody(), true);
	$response = array();
	global $imageClass;

	$_SESSION["step3"]["status"] 			=1;
	$_SESSION["step3"]["shirt_size_from"]	= $data['shirt_size_from'];
	$_SESSION["step3"]["shirt_size_to"]		= $data['shirt_size_to'];
	$_SESSION["step3"]["pants_size_from"]	= $data['pants_size_from'];
	$_SESSION["step3"]["pants_size_to"]		= $data['pants_size_to'];
	$_SESSION["step3"]["shoe_size_from"]	= $data['shoe_size_from'];
	$_SESSION["step3"]["shoe_size_to"]		= $data['shoe_size_to'];
	$_SESSION["step3"]["suite_size_from"]	= $data['suite_size_from'];
	$_SESSION["step3"]["suite_size_to"]		= $data['suite_size_to'];
	$_SESSION["step3"]["children_sizes"]	= $data['children_sizes'];
	$_SESSION["step3"]["eye_color_id"]		= $data['eye_color_id'];
	$_SESSION["step3"]["hair_color_id"]		= $data['hair_color_id'];
	$_SESSION["step3"]["bra_size"]			= $data['bra_size'];
	$_SESSION["step3"]["height"]			= $data['height'];
	$_SESSION["step3"]["weight"]			= $data['weight'];

	$response = array('success' => true);
			
	echoResponse(200, $response);
});

/******************************************
Purpose: Clear restration step 3 if needed
Parameter : id
Type : GET
******************************************/

$app->get('/step3Clear',function () use ($app) { 
    global $db;
	
	if(isset($_SESSION["step3"])){
		unset ($_SESSION["step3"]);
		$response = array('success' => true);
	}
	else{
		$response = array('success' => false);
	}
	//print_r($rows);
	
	echoResponse(200, $response);
});

/******************************************
Purpose: Check restration step 3 completed or not
Parameter : id
Type : GET
******************************************/
$app->get('/checkstep3',function () use ($app) { 
    global $db;
	$user_id =  $app->request->get('id');
	$response = array('success' => false);
	
	// Check Step 1 is valid
	if(isset($_SESSION["step1"])){
		if(isset($_SESSION["step1"]["status"])){
			$response['step1status'] = true;
		}else{
			$response['step1status'] = false;
		}
	}
	else{
		$response['step1status'] = false;
	}
	
	//Check Step 2 is valid
	if(isset($_SESSION["step2"])){
		if(isset($_SESSION["step2"]["status"])){
			$response['step2status'] = true;
		}else{
			$response['step2status'] = false;
		}
	}
	else{
		$response['step2status'] = false;
	}	

	//Check Step 3 is valid
	if(isset($_SESSION["step3"])){
		if(isset($_SESSION["step3"]["status"])){
			$response['step3status'] = true;
			$response['step3'] = $_SESSION["step3"];
		}else{
			$response['step3status'] = false;
		}
	}
	else{
		$response['step3status'] = false;
	}
	//print_r($rows);
	
	echoResponse(200, $response);
});

/******************************************
Purpose: Get categories for dropdown
Parameter : 
Type : GET
******************************************/
$app->get('/getcategories', function () use ($app) { 
    global $db;
	$query_categories = $db->prepare("SELECT * FROM categories order by sortby"); 
	$query_categories->execute();
	$rows_categories = $query_categories->fetchAll(PDO::FETCH_ASSOC);
	
    foreach($rows_categories as $row) {
		$categories[] = array('id' => $row['id'],
						  'name' => $row['name'],
						);
	}
	echoResponse(200, $categories);
});

/******************************************
Purpose: Get skills for dropdown
Parameter : 
Type : GET
******************************************/
$app->get('/getskills', function () use ($app) { 
    global $db;
	$query_skills = $db->prepare("SELECT * FROM skills order by sortby"); 
	$query_skills->execute();
	$rows_skills = $query_skills->fetchAll(PDO::FETCH_ASSOC);
	
    foreach($rows_skills as $row) {
		$skills[] = array('id' => $row['id'],
						  'name' => $row['name']
						);
	}
	echoResponse(200, $skills);
});


/******************************************
Purpose: Get Drivers licences for dropdown
Parameter : 
Type : GET
******************************************/
$app->get('/getlicences', function () use ($app) { 
    global $db;
	$query_licence = $db->prepare("SELECT * FROM drivers_licenses order by sortby"); 
	$query_licence->execute();
	$rows_licences = $query_licence->fetchAll(PDO::FETCH_ASSOC);
	
    foreach($rows_licences as $row) {
		$licences[] = array('id' => $row['id'],
						  'name' => $row['name']
						);
	}
	echoResponse(200, $licences);
});

/******************************************
Purpose: Register user step 4
Parameter : Form post parameters
Type : POST
******************************************/
$app->post('/step4Create',function () use ($app) { 
    global $db;
	$data =  json_decode($app->request->getBody(), true);
	$response = array();
	global $imageClass;

	$_SESSION["step4"]["status"] 			=1;
	$_SESSION["step4"]["notes"]	= $data['notes'];
	$_SESSION["step4"]["job"]		= $data['job'];
	$_SESSION["step4"]["selectedcategories"]	= $data['selectedcategories'];
	$_SESSION["step4"]["selectedskills"]	= $data['selectedskills'];
	$_SESSION["step4"]["selectedlicences"]	= $data['selectedlicences'];

	$response = array('success' => true);
			
	echoResponse(200, $response);
});

/******************************************
Purpose: Clear restration step 4 if needed
Parameter : id
Type : GET
******************************************/

$app->get('/step4Clear',function () use ($app) { 
    global $db;
	
	if(isset($_SESSION["step4"])){
		unset ($_SESSION["step4"]);
		$response = array('success' => true);
	}
	else{
		$response = array('success' => false);
	}
	//print_r($rows);
	
	echoResponse(200, $response);
});

/******************************************
Purpose: Check restration step 4 completed or not
Parameter : id
Type : GET
******************************************/
$app->get('/checkstep4',function () use ($app) { 
    global $db;
	$user_id =  $app->request->get('id');
	$response = array('success' => false);
	
	// Check Step 1 is valid
	if(isset($_SESSION["step1"])){
		if(isset($_SESSION["step1"]["status"])){
			$response['step1status'] = true;
		}else{
			$response['step1status'] = false;
		}
	}
	else{
		$response['step1status'] = false;
	}
	
	//Check Step 2 is valid
	if(isset($_SESSION["step2"])){
		if(isset($_SESSION["step2"]["status"])){
			$response['step2status'] = true;
		}else{
			$response['step2status'] = false;
		}
	}
	else{
		$response['step2status'] = false;
	}	

	//Check Step 3 is valid
	if(isset($_SESSION["step3"])){
		if(isset($_SESSION["step3"]["status"])){
			$response['step3status'] = true;
		}else{
			$response['step3status'] = false;
		}
	}
	else{
		$response['step3status'] = false;
	}	

	//Check Step 4 is valid
	if(isset($_SESSION["step4"])){
		if(isset($_SESSION["step4"]["status"])){
			$response['step4status'] = true;
			$response['step4'] = $_SESSION["step4"];
		}else{
			$response['step4status'] = false;
		}
	}
	else{
		$response['step4status'] = false;
	}
	//print_r($rows);
	
	echoResponse(200, $response);
});

/******************************************
Purpose: Get available languages for dropdown
Parameter : Nil
Type : GET
******************************************/
$app->get('/getlanguages', function () use ($app) { 
    global $db;
	$query_language = $db->prepare("SELECT * FROM language_proficiency_languages order by name"); 
	$query_language->execute();
	$rows_language = $query_language->fetchAll(PDO::FETCH_ASSOC);
	
    foreach($rows_language as $row) {
		$languages[] = array('id' => $row['id'],
						  'name' => $row['name']
						);
	}
	echoResponse(200, $languages);
});

/******************************************
Purpose: Get rating icons for available languages
Parameter : 
Type : GET
******************************************/

$app->get('/getlanguageratings', function () use ($app) { 
    global $db;
		$langratings = array();
		$ratingar = array();
	$ratingid =  $app->request->get('ratingid');
	if($ratingid)
		$ratingar = explode(',',$ratingid);
	for($lc = 0; $lc < 4;$lc++){
		$langstar = array();
		$ratingval  = (isset($ratingar[$lc])) ? $ratingar[$lc] : 0 ;
		$langrateid = $lc+1;
		for($lr = 1; $lr <= 4;$lr++){
			$langstar[] = array('starid' => $lr,'star'=>($lr <= $ratingval) ? 'star-white.png': 'star-black.png');
		}
		$langratings[$lc]=array('langrateid'=> $langrateid, 'rating'=>$ratingval,'ratingicon'=>$langstar);
	}
	echoResponse(200, $langratings);

});

/******************************************
Purpose: Register user step 5
Parameter : Form post parameters
Type : POST
******************************************/
$app->post('/step5Create',function () use ($app) { 
    global $db;
	$data =  json_decode($app->request->getBody(), true);
	$response = array();
	global $imageClass;
	$_SESSION['operation'] = isset($data['operation']) ? $data['operation'] : "insert";
	$_SESSION['user_profile_id'] = isset($data['user_profile_id']) ? $data['user_profile_id'] : "";

	$_SESSION["step5"]["status"] 			=1;

	if(isset($data['lang1']))
		$_SESSION["step5"]["lang1"]	= $data['lang1'];
	if(isset($data['lang2']))
		$_SESSION["step5"]["lang2"]	= $data['lang2'];
	if(isset($data['lang3']))
		$_SESSION["step5"]["lang3"]	= $data['lang3'];
	if(isset($data['lang4']))
		$_SESSION["step5"]["lang4"]	= $data['lang4'];

	if(isset($data['langrateval1']))
		$_SESSION["step5"]["langrateval1"]	= $data['langrateval1'];
	if(isset($data['langrateval2']))
		$_SESSION["step5"]["langrateval2"]	= $data['langrateval2'];
	if(isset($data['langrateval3']))
		$_SESSION["step5"]["langrateval3"]	= $data['langrateval3'];
	if(isset($data['langrateval4']))
		$_SESSION["step5"]["langrateval4"]	= $data['langrateval4'];

	if(isset($data['lng_pro_id1']))
		$_SESSION["step5"]["lng_pro_id1"]	= $data['lng_pro_id1'];
	if(isset($data['lng_pro_id2']))
		$_SESSION["step5"]["lng_pro_id2"]	= $data['lng_pro_id2'];
	if(isset($data['lng_pro_id3']))
		$_SESSION["step5"]["lng_pro_id3"]	= $data['lng_pro_id3'];
	if(isset($data['lng_pro_id4']))
		$_SESSION["step5"]["lng_pro_id4"]	= $data['lng_pro_id4'];

	if(isset($data['dealekter1']))
		$_SESSION["step5"]["dealekter1"]	= $data['dealekter1'];
	if(isset($data['dealekter2']))
		$_SESSION["step5"]["dealekter2"]	= $data['dealekter2'];
	if(isset($data['dealekter3']))
		$_SESSION["step5"]["dealekter3"]	= $data['dealekter3'];

	$response = array('success' => true);
			
	echoResponse(200, $response);
});


/******************************************
Purpose: Clear restration step 5 if needed
Parameter : id
Type : GET
******************************************/

$app->get('/step5Clear',function () use ($app) { 
    global $db;
	
	if(isset($_SESSION["step4"])){
		unset ($_SESSION["step4"]);
		$response = array('success' => true);
	}
	else{
		$response = array('success' => false);
	}
	//print_r($rows);
	
	echoResponse(200, $response);
});

/******************************************
Purpose: Check restration step 5 completed or not
Parameter : id
Type : GET
******************************************/
$app->get('/checkstep5',function () use ($app) { 
    global $db;
	$user_id =  $app->request->get('id');
	$response = array('success' => false);
	
	// Check Step 1 is valid
	if(isset($_SESSION["step1"])){
		if(isset($_SESSION["step1"]["status"])){
			$response['step1status'] = true;
		}else{
			$response['step1status'] = false;
		}
	}
	else{
		$response['step1status'] = false;
	}
	
	//Check Step 2 is valid
	if(isset($_SESSION["step2"])){
		if(isset($_SESSION["step2"]["status"])){
			$response['step2status'] = true;
		}else{
			$response['step2status'] = false;
		}
	}
	else{
		$response['step2status'] = false;
	}	

	//Check Step 3 is valid
	if(isset($_SESSION["step3"])){
		if(isset($_SESSION["step3"]["status"])){
			$response['step3status'] = true;
		}else{
			$response['step3status'] = false;
		}
	}
	else{
		$response['step3status'] = false;
	}	
	//Check Step 4 is valid
	if(isset($_SESSION["step4"])){
		if(isset($_SESSION["step4"]["status"])){
			$response['step4status'] = true;
		}else{
			$response['step4status'] = false;
		}
	}
	else{
		$response['step4status'] = false;
	}	

	//Check Step 5 is valid
	if(isset($_SESSION["step5"])){
		if(isset($_SESSION["step5"]["status"])){
			$response['step5status'] = true;
			$response['step5'] = $_SESSION["step5"];
		}else{
			$response['step5status'] = false;
		}
	}
	else{
		$response['step5status'] = false;
	}
	//print_r($rows);
	
	echoResponse(200, $response);
});

/******************************************
Purpose: Register user step 5
Parameter : Form post parameters
Type : POST
******************************************/
$app->post('/step6Create',function () use ($app) { 
	unset($_SESSION['Image_file_location']);
	unset($_SESSION['Video_file_location']);
	unset($_SESSION['Video_file']['name']);
	unset($_SESSION['Image_file']['name']);
	// ppe($_SESSION);
	$operation = (isset($_SESSION['operation'])) ? $_SESSION['operation'] : 'insert';
	$user_profile_id = (isset($_SESSION['user_profile_id'])) ? $_SESSION['user_profile_id'] : '';
	global $db;
	$first_name=$_SESSION["step1"]["first_name"];
	$last_name=$_SESSION["step1"]["last_name"];
	$password=$_SESSION["step1"]["password"];
	$hashed_password=md5($password);
	$address=$_SESSION["step1"]["address"];
	$zipcode=$_SESSION["step1"]["zipcode"];
	$city=$_SESSION["step1"]["city"];
	$country_id=$_SESSION["step1"]["country_id"];

	
	$email=$_SESSION["step2"]["email"];
	$phone=$_SESSION["step2"]["phone"];
	$phone_at_work=$_SESSION["step2"]["phone_at_work"];
	$gender_id=$_SESSION["step2"]["gender_id"];
	$birth_day=$_SESSION["step2"]["birth_day"];
	$birth_month=$_SESSION["step2"]["birth_month"];
	$birth_year=$_SESSION["step2"]["birth_year"];
	$birthday=$birth_year."-".$birth_month."-".$birth_day;
	$ethinic_origin=$_SESSION["step2"]["ethinic_origin"];

	$shirt_size_from=$_SESSION["step3"]["shirt_size_from"]!=''?(int)$_SESSION["step3"]["shirt_size_from"]:'NULL';	
	$shirt_size_to=$_SESSION["step3"]["shirt_size_to"]!=''?(int)$_SESSION["step3"]["shirt_size_to"]:'NULL';
	$pants_size_from=$_SESSION["step3"]["pants_size_from"]!=''?(int)$_SESSION["step3"]["pants_size_from"]:'NULL';
	$pants_size_to=$_SESSION["step3"]["pants_size_to"]!=''?(int)$_SESSION["step3"]["pants_size_to"]:'NULL';
	$shoe_size_from=$_SESSION["step3"]["shoe_size_from"]!=''?(int)$_SESSION["step3"]["shoe_size_from"]:'NULL';
	$shoe_size_to=$_SESSION["step3"]["shoe_size_to"]!=''?(int)$_SESSION["step3"]["shoe_size_to"]:'NULL';
	$suite_size_from=$_SESSION["step3"]["suite_size_from"]!=''?(int)$_SESSION["step3"]["suite_size_from"]:'NULL';
	$suite_size_to=$_SESSION["step3"]["suite_size_to"]!=''?(int)$_SESSION["step3"]["suite_size_to"]:'NULL';
	$children_sizes=$_SESSION["step3"]["children_sizes"]!=''?(int)$_SESSION["step3"]["children_sizes"]:'NULL';
	$eye_color_id=$_SESSION["step3"]["eye_color_id"]!=''?(int)$_SESSION["step3"]["eye_color_id"]:'NULL';
	$hair_color_id=$_SESSION["step3"]["hair_color_id"]!=''?(int)$_SESSION["step3"]["hair_color_id"]:'NULL';
	$bra_size=$_SESSION["step3"]["bra_size"]!=''?(int)$_SESSION["step3"]["bra_size"]:'NULL';
	$height=$_SESSION["step3"]["height"]!=''?(int)$_SESSION["step3"]["height"]:'NULL';
	$weight=$_SESSION["step3"]["weight"]!=''?(int)$_SESSION["step3"]["weight"]:'NULL';

	$notes=$_SESSION["step4"]["notes"];
	$job=$_SESSION["step4"]["job"];
	$selectedcategories=$_SESSION["step4"]["selectedcategories"];
	$selectedskills=$_SESSION["step4"]["selectedskills"];
	$selectedlicences=$_SESSION["step4"]["selectedlicences"];
	$languages=array();
	if(isset($_SESSION["step5"]["lang1"])&& $_SESSION["step5"]["lang1"]!='')
		$languages[0]['language_id']=$_SESSION["step5"]["lang1"];
	if(isset($_SESSION["step5"]["lang2"])&& $_SESSION["step5"]["lang2"]!='')
		$languages[1]['language_id']=$_SESSION["step5"]["lang2"];
	if(isset($_SESSION["step5"]["lang3"])&& $_SESSION["step5"]["lang3"]!='')
		$languages[2]['language_id']=$_SESSION["step5"]["lang3"];
	if(isset($_SESSION["step5"]["lang4"])&& $_SESSION["step5"]["lang4"]!='')
		$languages[3]['language_id']=$_SESSION["step5"]["lang4"];

	if(isset($_SESSION["step5"]["langrateval1"]) && $_SESSION["step5"]["langrateval1"]!='')
		$languages[0]['rating_id']=$_SESSION["step5"]["langrateval1"];
	if(isset($_SESSION["step5"]["langrateval2"]) && $_SESSION["step5"]["langrateval2"]!='')
		$languages[1]['rating_id']=$_SESSION["step5"]["langrateval2"];
	if(isset($_SESSION["step5"]["langrateval3"]) && $_SESSION["step5"]["langrateval3"]!='')
		$languages[2]['rating_id']=$_SESSION["step5"]["langrateval3"];
	if(isset($_SESSION["step5"]["langrateval4"]) && $_SESSION["step5"]["langrateval4"]!='')
		$languages[3]['rating_id']=$_SESSION["step5"]["langrateval4"];

	if(isset($_SESSION["step5"]["lng_pro_id1"]) && $_SESSION["step5"]["lng_pro_id1"]!='')
		$languages[0]['lng_pro_id']=$_SESSION["step5"]["lng_pro_id1"];
	if(isset($_SESSION["step5"]["lng_pro_id2"]) && $_SESSION["step5"]["lng_pro_id2"]!='')
		$languages[1]['lng_pro_id']=$_SESSION["step5"]["lng_pro_id2"];
	if(isset($_SESSION["step5"]["lng_pro_id3"]) && $_SESSION["step5"]["lng_pro_id3"]!='')
		$languages[2]['lng_pro_id']=$_SESSION["step5"]["lng_pro_id3"];
	if(isset($_SESSION["step5"]["lng_pro_id4"]) && $_SESSION["step5"]["lng_pro_id4"]!='')
		$languages[3]['lng_pro_id']=$_SESSION["step5"]["lng_pro_id4"];

	if(isset($_SESSION["step5"]["dealekter1"]) && $_SESSION["step5"]["dealekter1"]!='')
		$dealekter1=$_SESSION["step5"]["dealekter1"];
	if(isset($_SESSION["step5"]["dealekter2"]) && $_SESSION["step5"]["dealekter2"]!='')
		$dealekter2=$_SESSION["step5"]["dealekter2"];
	if(isset($data['dealekter3']) && $_SESSION["step5"]["dealekter3"]!='')
		$dealekter3=$_SESSION["step5"]["dealekter3"];

		$agreed_to_these_terms=1;

		if($operation == 'insert'){
			$q_chip = "INSERT INTO `profiles` ( `first_name`, `last_name`, `gender_id`, `hair_color_id`,`eye_color_id`, `birthday`, `height`, `weight`, `shoe_size_from`, `shoe_size_to`, 	`shirt_size_from`,`shirt_size_to`,`pants_size_from`,`pants_size_to`,`bra_size`,`children_sizes`,`address`,`zipcode`,`city`,`country_id`,`phone`,`phone_at_work`,`email`,`job`,`notes`,`agreed_to_these_terms`,`password`,`hashed_password`,`created_at`,`updated_at`,`suite_size_from`,`suite_size_to`) VALUES ('".$first_name."', '".$last_name."','".$gender_id."',".$hair_color_id.",".$eye_color_id.",'".$birthday."',".$height.",".$weight.",".$shoe_size_from.",".$shoe_size_to.",".$shirt_size_from.",".$shirt_size_to.",".$pants_size_from.",".$pants_size_from.",".$bra_size.",".$children_sizes.",'".$address."','".$zipcode."','".$city."','".$country_id."','".$phone."','".$phone_at_work."','".$email."','".$job."','".$notes."','".$agreed_to_these_terms."','".$password."','".$hashed_password."',now(),now(),".$suite_size_from.",".$suite_size_to.")";

			$profile_id = $db->exec($q_chip);
			$user_profile_id = $profile_id;
			if($profile_id){
				if($selectedcategories!=''){
					$cat_arr= explode(",",$selectedcategories);
					foreach($cat_arr as $cat){
						$query = "INSERT INTO `categories_profiles` (`profile_id`,`category_id`) VALUES ('".$profile_id."','".$cat."')";
						$db->exec($query);
					}
				}

				if($selectedskills!=''){
					$skill_arr= explode(",",$selectedskills);
					foreach($skill_arr as $skill){
						$query = "INSERT INTO `profiles_skills` (`profile_id`,`skill_id`) VALUES ('".$profile_id."','".$skill."')";
						$db->exec($query);
					}
				}

				if($selectedlicences){
					$license_arr= explode(",",$selectedlicences);
					foreach($license_arr as $license){
					$query = "INSERT INTO `drivers_licenses_profiles` (`profile_id`,`drivers_license_id`) VALUES ('".$profile_id."','".$license."')";
					$db->exec($query);
					}
				}

				if(!empty($languages)){
					foreach($languages as $language){
						$query = "INSERT INTO `language_proficiencies` (`language_proficiency_language_id`,`profile_id`,`language_proficiency_rating_id`,`created_at`,`updated_at`) VALUES ('".$language['language_id']."','".$profile_id."','".$language['rating_id']."',now(),now())";
						//echo $query;
						$db->exec($query);
					}
				}

				if(isset($_SESSION['Image_file'])){
					foreach($_SESSION['Image_file'] as $key => $image){
						$filename = $image['name'][0];
						$location = $_SERVER['DOCUMENT_ROOT'].'/images/uploads/';
						move_uploaded_file($image['tmp_name'][0],$location.$filename);
						$query = "INSERT INTO `photos` (`path`,`original_path`,`profile_id`,`filename`,`published`,`position`,`phototype_id`,`image`,`created_at`,`updated_at`,`image_tmp`,`image_processing`,`image_token`) VALUES ('".$location."','".$location."','".$profile_id."','".$filename."','1','".$key."','1','".$filename."',now(),now(),'".$filename."','1','".$filename."')";
						$db->exec($query);
					}
				unset($_SESSION['Image_file']);
				unset($_SESSION['Image_file_location']);
				}

				if(isset($_SESSION['Video_file'])){
					foreach($_SESSION['Video_file'] as $key=>$video){
						$filename = $video['cdnfilename'];
						$location = $video['cdnfilepath'];
						$thumbnail = $video['thumbnail'];
						$cloud_orig_path = str_replace('/videos',"", $location);
							$query = "INSERT INTO `videos` (
									`profile_id`,
									`path`,
									`uploaded_as_filename`,
									`filename`,
									`video_original_path`,
									`video_original_filename`,
									`video_original_file_basename`,
									`thumbnail_original_photo_path`,
									`thumbnail_photo_path`,
									`thumbnail_photo_filename`,
									`thumbnail_at_time`,
									`published`,
									`position`) 
								VALUES (
									'".$user_profile_id."',
									'".$location."',
									'".$video["name"][0]."',
									'".$filename."',
									'".$cloud_orig_path."',
									'".$filename."',
									'".$filename."',
									'".$location."',
									'".$location."',
									'".$thumbnail."',
									'3',
									'1',
									'".$key."')";
						$query_prepared = $db->prepare($query);
						$query_prepared->execute();
					}
				unset($_SESSION['Video_file']);
				unset($_SESSION['Video_file_location']);	
				}
				echoResponse(200,array('status'=>true,'msg'=>'Registered Sucessfully'));

			}
			else{
				echoResponse(200,array('status'=>false,'msg'=>'Couldn\'t Register, Please try again later'));
			}
		}
		if($operation == "update"){
			if($user_profile_id != ""){
				$q_chip =  "UPDATE profiles 
								 		SET
								 		first_name = '$first_name', 
										last_name = '$last_name', 
										gender_id = $gender_id, 
										hair_color_id = $hair_color_id,
										eye_color_id = $eye_color_id, 
										birthday = $birthday,
										height = $height, 
										weight = $weight, 
										shoe_size_from = $shoe_size_from, 
										shoe_size_to = $shoe_size_to,
										shirt_size_from = $shirt_size_from,
										shirt_size_to = $shirt_size_to,
										pants_size_from = $pants_size_from,
										pants_size_to = $pants_size_to,
										bra_size = '$bra_size',
										children_sizes = $children_sizes,
										address = '$address',
										zipcode = '$zipcode',
										city = '$city',
										country_id = $country_id,
										phone = '$phone',
										phone_at_work = '$phone_at_work',
										email = '$email',
										job = '$job',
										notes = '$notes',
										agreed_to_these_terms = $agreed_to_these_terms,
										password = '$password',
										hashed_password = '$hashed_password',
										updated_at = now(),
										suite_size_from = $suite_size_from,
										suite_size_to = $suite_size_to

									WHERE id = $user_profile_id";
						$query_prepared = $db->prepare($q_chip);
						$query_prepared->execute();
				}

				if($selectedcategories!=''){
					if(!(is_array($selectedcategories))){
						$cat_arr= explode(",",$selectedcategories);
					}
					else{
						$cat_arr= $selectedcategories;
					}
					
					foreach($cat_arr as $cat){
						$query = "UPDATE categories_profiles SET category_id = $cat WHERE profile_id = $user_profile_id";
						$query_prepared = $db->prepare($query);
						$query_prepared->execute();
					}
				}

				if($selectedskills!=''){
					if(!(is_array($selectedskills))){
						$skill_arr= explode(",",$selectedskills);
					}
					else{
						$skill_arr= $selectedskills;
					}
					foreach($skill_arr as $skill){
						$query = "UPDATE profiles_skills SET skill_id = $skill WHERE profile_id = $user_profile_id";
						$query_prepared = $db->prepare($query);
						$query_prepared->execute();
					}
				}

				if($selectedlicences){
					if(!(is_array($selectedlicences))){
						$license_arr= explode(",",$selectedlicences);
					}
					else{
						$license_arr= $selectedlicences;
					}
					foreach($license_arr as $license){
						$query = "UPDATE drivers_licenses_profiles SET drivers_license_id = $license WHERE profile_id = $user_profile_id";
						$query_prepared = $db->prepare($query);
						$query_prepared->execute();
					}
				}

				if(!empty($languages)){
					foreach($languages as $language){
						$language_id = $language['language_id'];
						$rating_id = $language['rating_id'];
						
						if(isset($language['lng_pro_id'])){
							$query = "INSERT INTO `language_proficiencies` (`language_proficiency_language_id`,`profile_id`,`language_proficiency_rating_id`,`created_at`,`updated_at`) VALUES ('".$language['language_id']."','".$user_profile_id."','".$language['rating_id']."',now(),now())";
						}else{
							$lng_pro_id = $language['lng_pro_id'];
							$query = "UPDATE language_proficiencies SET language_proficiency_language_id = $language_id, language_proficiency_rating_id = $rating_id, updated_at = now() WHERE id = $lng_pro_id";
						}
						$query_prepared = $db->prepare($query);
						$query_prepared->execute();
					}
				}

				if(isset($_SESSION['Image_file'])){
					foreach($_SESSION['Image_file'] as $key => $image){
						$filename = $image['name'][0];
						$location = $_SERVER['DOCUMENT_ROOT'].'/images/uploads/';
						move_uploaded_file($image['tmp_name'][0],$location.$filename);
						$check_existing_record = "SELECT profile_id from photos where profile_id = $user_profile_id AND position = $key";
						$check_existance = $db->prepare($check_existing_record);
						$check_existance->execute();
						$rowcount = $check_existance->rowCount();
						if($rowcount > 0){
							$query = "UPDATE photos 
												SET
													path = '$location',
													original_path = '$location',
													filename = '$filename',
													published = 1,
													phototype_id = 1,
													image = '$filename',
													updated_at = now(),
													image_tmp = '$filename',
													image_processing = 1,
													image_token = '$filename'
												WHERE
													profile_id = $user_profile_id AND
													position = $key";
						}
						else{
							$query = "INSERT INTO `photos` (`path`,`original_path`,`profile_id`,`filename`,`published`,`position`,`phototype_id`,`image`,`created_at`,`updated_at`,`image_tmp`,`image_processing`,`image_token`) VALUES ('".$location."','".$location."','".$user_profile_id."','".$filename."','1','".$key."','1','".$filename."',now(),now(),'".$filename."','1','".$filename."')";
						}
						$query_prepared = $db->prepare($query);
						$query_prepared->execute();
					}
				unset($_SESSION['Image_file']);
				unset($_SESSION['Image_file_location']);
				}

				if(isset($_SESSION['Video_file'])){
					foreach($_SESSION['Video_file'] as $key=>$video){
						$filename = $video['cdnfilename'];
						$location = $video['cdnfilepath'];
						$thumbnail = $video['thumbnail'];

						$cloud_orig_path = str_replace('/videos',"", $location);
						$check_existing_record = "SELECT profile_id from videos where profile_id = $user_profile_id AND position = $key";
						$check_existance = $db->prepare($check_existing_record);
						$check_existance->execute();
						$rowcount = $check_existance->rowCount();
						if($rowcount > 0){
							$query = "UPDATE videos 
												SET
													`path`='$location',
													`uploaded_as_filename`='".$video['name'][0]."',
													`filename`='$filename',
													`video_original_path`='$cloud_orig_path',
													`video_original_filename`='$filename',
													`video_original_file_basename`='$filename',
													`thumbnail_original_photo_path`='$location',
													`thumbnail_photo_path`='$location',
													`thumbnail_photo_filename`='$thumbnail',
													`thumbnail_at_time`=3,
													`published`=1
												WHERE 
													`profile_id`=$user_profile_id AND
													`position`=$key";
						}
						else{
							$query = "INSERT INTO `videos` (
									`profile_id`,
									`path`,
									`uploaded_as_filename`,
									`filename`,
									`video_original_path`,
									`video_original_filename`,
									`video_original_file_basename`,
									`thumbnail_original_photo_path`,
									`thumbnail_photo_path`,
									`thumbnail_photo_filename`,
									`thumbnail_at_time`,
									`published`,
									`position`) 
								VALUES (
									'".$user_profile_id."',
									'".$location."',
									'".$video['name'][0]."',
									'".$filename."',
									'".$cloud_orig_path."',
									'".$filename."',
									'".$filename."',
									'".$location."',
									'".$location."',
									'".$thumbnail."',
									'3',
									'1',
									'".$key."')";
						}
						$query_prepared = $db->prepare($query);
						$query_prepared->execute();
					}
				unset($_SESSION['Video_file']);
				unset($_SESSION['Video_file_location']);	
				}
				echoResponse(200,array('status'=>true,'msg'=>'Updated Sucessfully'));
		}
});

/******************************************
Purpose: Send to email id
Parameter : form field
Type : POST
******************************************/
/* // disabled this as it is handled in another section
$app->post('/fileuploadparser', function () use ($app) {

	$fileName = $_FILES["Image_file"]["name"][0]; // The file name
	$fileTmpLoc = $_FILES["Image_file"]["tmp_name"][0]; // File in the PHP tmp folder
	$fileType = $_FILES["Image_file"]["type"]; // The type of file it is
	$fileSize = $_FILES["Image_file"]["size"]; // File size in bytes
	$fileErrorMsg = $_FILES["Image_file"]["error"]; // 0 for false... and 1 for true

	// $file_name = $_FILES['Image_file']['name'][$key];
	$file_name = $_FILES['Image_file']['name'];
	$location = $_SERVER['DOCUMENT_ROOT'].'/images/uploads/';
// var_dump($_FILES);
	if (!$fileTmpLoc) { // if file not chosen
		
	    echo "ERROR: Please browse for a file before clicking the upload button.";
	    exit();
	}
	if(move_uploaded_file($fileTmpLoc, $location.$fileName)){
	    echo json_encode(['status_message'=>'file upload success', 'filename'=>$fileName]);
	} else {
	    echo "move_uploaded_file function failed";
	}

});
*/
/******************************************
Purpose: Send to email id
Parameter : form field
Type : POST
******************************************/

$app->post('/sendemail', function () use ($app) { 
  global $db;
  $data = json_decode($app->request->getBody(), true);
  $response = array();
  $to_email = $data['to_email'];
  $from_email = $data['from_email'];
  $mail_body ='';$to_cc ='';
  if(isset($data['mail_body'])){
    $mail_body = $data['mail_body'];
  }
  if(isset($data['to_cc'])){
    $to_cc = $data['to_cc'];
  }

  $html = '<table style="text-align:left" cellspacing="0" cellpadding="0" width="556" border="0">
    <tbody>
    <tr>
    <td>
    <table style="width:100%" border="0">
    <tbody>
    <tr>
    <td align="left"><img alt="Mailtoplogo" src="http://134.213.29.220/assets/mailTopLogo.png" ></td>
    <td style="width:270px;padding-top:18px" align="left" valign="top"><b style="color:#696969">Castit <span class="il">Lightbox</span>:</b><br>'.$mail_body.'</td>
    </tr>
    <tr>
    <td colspan="2"><img alt="Mailtopborder" src="http://134.213.29.220/assets/mailTopBorder.jpg"></td>
    </tr>
    </tbody>
    </table>
    </td>
    </tr>
    <tr>';

  $subject = "Castit Workshop enquiry";
  $headers = "MIME-Version: 1.0" . "\r\n";
  $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
  $headers .= 'From: Castit <info@ldsstage.in>' . "\r\n";
  //$headers .= 'Reply-To: <'.$from_email.'>' . "\r\n";
  if($to_cc){
    $headers .= 'CC: <'.$to_cc.'>' ."\r\n";
  }

	// $headers .= 'BCC: cat@castit.dk' . "\r\n";
	$headers .= 'BCC: padmanabhann@mailinator.com, vs@anewnative.com' . "\r\n";

  //$html .= 'testemail';
  mail( $to_email, $subject, $html, $headers ); // Accountant
  $response['success'] = true;
  $response['message'] = 'Email er sendt!';

  echoResponse(200, $response);
});


/******************************************
Purpose: Send Lighbox profiles to email id
Parameter : form field
Type : POST
******************************************/

$app->post('/sendlightbox', function () use ($app) { 
    global $db;
	$data = json_decode($app->request->getBody(), true);
	$response = array();
	$to_email = $data['to_email'];
	$from_email = $data['form_email'];
	$mail_body ='';
	$to_cc ='';
	$to_bcc='';
	if(isset($data['mail_body'])){
		$mail_body = $data['mail_body'];
	}
	if(isset($data['to_cc'])){
		$to_cc = $data['to_cc'];
	}
	if(isset($_SESSION["lightbox_token"])){
		$lightbox_token = $_SESSION["lightbox_token"];

		$query_check_lb = $db->prepare("SELECT * FROM lightboxes where token = '".$lightbox_token."'"); 
		$query_check_lb->execute();
		$rows_lb = $query_check_lb->fetchAll(PDO::FETCH_ASSOC);
		if(count($rows_lb)>0) {
			$lbid = $rows_lb[0]['id'];
				
			$query_lb_pprofiles = $db->prepare("SELECT * FROM lightboxes_profiles WHERE lightbox_id ='".$lbid."'"); 
			$query_lb_pprofiles->execute();
			$rows_lb_pprofiles = $query_lb_pprofiles->fetchAll(PDO::FETCH_ASSOC);
			$rowcount = count($rows_lb_pprofiles);
			if($rowcount > 0){
				$html = '<table style="text-align:left" cellspacing="0" cellpadding="0" width="556" border="0">
							<tbody>
							<tr>
							<td>
							<table style="width:100%" border="0">
							<tbody>
							<tr>
							<td align="left"><img alt="Mailtoplogo" src="http://134.213.29.220/assets/mailTopLogo.png" ></td>
							<td style="width:270px;padding-top:18px" align="left" valign="top"><b style="color:#696969">Castit <span class="il">Lightbox</span>:</b><br>'.$mail_body.'</td>
							</tr>
							<tr>
							<td colspan="2"><img alt="Mailtopborder" src="http://134.213.29.220/assets/mailTopBorder.jpg"></td>
							</tr>
							</tbody>
							</table>
							</td>
							</tr>
							<tr>';

				
				foreach($rows_lb_pprofiles as $rowp) {
					
						$query = $db->prepare("SELECT p.*, m.profile_group_id, m.profile_number, m.profile_number_first_name_last_name, m.version, m.current, g.name as gender_name, hc.name as hair_color_name, ec.name as eye_color_name FROM profiles p INNER JOIN memberships m ON m.profile_id = p.id INNER JOIN genders g ON g.id = p.gender_id INNER JOIN hair_colors hc ON hc.id = p.hair_color_id INNER JOIN eye_colors ec ON ec.id = p.eye_color_id  WHERE p.id='".$rowp['profile_id']."' AND (p.profile_status_id = '1' OR  p.profile_status_id = '2') AND m.current ='1' LIMIT 1"); 
						$query->execute();
						$rows = $query->fetchAll(PDO::FETCH_ASSOC);
						if(count($rows)>0) {
							$row = $rows[0];
								$birthdate = new DateTime($row['birthday']);
								$today   = new DateTime('today');
								$age = $birthdate->diff($today)->y;
					
								// Profile Image
								$profile_image ='';
								$query_image = $db->prepare("SELECT *, DATE_FORMAT(created_at, '%Y') as create_year, DATE_FORMAT(created_at, '%m') as create_month, DATE_FORMAT(created_at, '%d') as create_date FROM photos WHERE profile_id = '".$row['id']."' and published ='1' ORDER BY position ASC LIMIT 1"); 
						$query_image->execute();
						$image= '';
						$rows_image = $query_image->fetchAll(PDO::FETCH_ASSOC);
						if(count($rows_image) > 0){
							if (strpos($rows_image[0]['path'], 'vhost') !== false) {
								$profile_image = 'http://134.213.29.220/images/uploads/'.$rows_image[0]['image'];
							}
							else{
								$path = $rows_image[0]['create_year']."/".$rows_image[0]['create_month']."/".$rows_image[0]['create_date']."/".$rows_image[0]['id']."/thumb_";
								$profile_image = 'http://134.213.29.220/profile_images/'.$path.$rows_image[0]['image'];
							}
						}
			
						$html .= '<td style="vertical-align:top">
							<table style="width:100%">
							<tbody>
							<tr>';
						$html .= '<td style="height:230px;width:139px" valign="top">
							<a href="http://134.213.29.220/?l='.$lbid.'" target="_blank">
							<img src="'.$profile_image.'" alt="'.$row['first_name'].'">
							</a>
							<span style="display:block;background-color:Black;width:131px;color:#ffffff;font-weight:bold;font-size:11px;margin-left:1px">
							'.$row['first_name'].'&nbsp;'.$row['profile_number'].';
							</span>
							<span>
							</span>
							</td>';
						}
				}
				$html .= ' </tr>
					</tbody></table>
					</td>
					</tr>
					</tbody></table>';

			$subject = "Castit Lighbox";
			$headers = "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
			$headers .= 'From: Castit <info@ldsstage.in>' . "\r\n";
			//$headers .= 'Reply-To: <'.$from_email.'>' . "\r\n";
			if($to_cc){
				$headers .= 'CC: <'.$to_cc.'>' ."\r\n";
			}
			// $headers .= 'BCC: cat@castit.dk' . "\r\n";
  		$headers .= 'BCC: padmanabhann@mailinator.com, vs@anewnative.com' . "\r\n";

			//$html .= 'testemail';
			mail( $to_email, $subject, $html, $headers ); // Accountant
			$response['success'] = true;
			$response['message'] = 'Lightbox er sendt!';
			}
			else{
			$response['success'] = true;
			$response['message'] = 'No Profiles in Lightbox';
			}
		}
		else{
			$response['success'] = true;
			$response['message'] = 'No Profiles in Lightbox';
		}

	}
	else{
			$response['success'] = true;
			$response['message'] = 'No Profiles in Lightbox';
	}
	echoResponse(200, $response);
});

$app->post('/sendgroup', function () use ($app) { 
    global $db;
	$data = json_decode($app->request->getBody(), true);

	$response = array();
	$to_email = $data['to_email'];
	$from_email = $data['form_email'];
	//$gid = $app->request->get('gpid');
	$gid = $data['gpid'];
	$mail_body ='';$to_cc ='';
	if(isset($data['mail_body'])){
		$mail_body = $data['mail_body'];
	}
	if(isset($data['to_cc'])){
		$to_cc = $data['to_cc'];
	}
	
		
				
			$query_lb_pprofiles = $db->prepare("SELECT * FROM profile_grouping WHERE group_id = '".$gid."'"); 
			$query_lb_pprofiles->execute();
			$rows_lb_pprofiles = $query_lb_pprofiles->fetchAll(PDO::FETCH_ASSOC);
			$rowcount = count($rows_lb_pprofiles);
			if($rowcount > 0){
				$html = '<table style="text-align:left" cellspacing="0" cellpadding="0" width="556" border="0">
							<tbody>
							<tr>
							<td>
							<table style="width:100%" border="0">
							<tbody>
							<tr>
							<td align="left"><img alt="Mailtoplogo" src="http://134.213.29.220/assets/mailTopLogo.png" ></td>
							<td style="width:270px;padding-top:18px" align="left" valign="top"><b style="color:#696969">Castit <span class="il">Lightbox</span>:</b><br>'.$mail_body.'</td>
							</tr>
							</tbody>
							</table>
							</td>
							</tr>
							<tr>';

				
				foreach($rows_lb_pprofiles as $rowp) {
					
						$query = $db->prepare("SELECT p.*, m.profile_group_id, m.profile_number, m.profile_number_first_name_last_name, m.version, m.current, g.name as gender_name, hc.name as hair_color_name, ec.name as eye_color_name FROM profiles p INNER JOIN memberships m ON m.profile_id = p.id INNER JOIN genders g ON g.id = p.gender_id INNER JOIN hair_colors hc ON hc.id = p.hair_color_id INNER JOIN eye_colors ec ON ec.id = p.eye_color_id  WHERE p.id='".$rowp['profile_id']."' AND (p.profile_status_id = '1' OR  p.profile_status_id = '2') AND m.current ='1' LIMIT 1"); 
						$query->execute();
						$rows = $query->fetchAll(PDO::FETCH_ASSOC);
						if(count($rows)>0) {
							$row = $rows[0];
								$birthdate = new DateTime($row['birthday']);
								$today   = new DateTime('today');
								$age = $birthdate->diff($today)->y;
					
								// Profile Image
								$profile_image ='';
								$query_image = $db->prepare("SELECT *, DATE_FORMAT(created_at, '%Y') as create_year, DATE_FORMAT(created_at, '%m') as create_month, DATE_FORMAT(created_at, '%d') as create_date FROM photos WHERE profile_id = '".$row['id']."' and published ='1' ORDER BY position ASC LIMIT 1"); 
						$query_image->execute();
						$image= '';
						$rows_image = $query_image->fetchAll(PDO::FETCH_ASSOC);
						if(count($rows_image) > 0){
							if (strpos($rows_image[0]['path'], 'vhost') !== false) {
								$profile_image = 'http://134.213.29.220/images/uploads/'.$rows_image[0]['image'];
							}
							else{
								$path = $rows_image[0]['create_year']."/".$rows_image[0]['create_month']."/".$rows_image[0]['create_date']."/".$rows_image[0]['id']."/thumb_";
								$profile_image = 'http://134.213.29.220/profile_images/'.$path.$rows_image[0]['image'];
							}
						}
			
						$html .= '<td style="vertical-align:top">
							<table style="width:100%">
							<tbody>
							<tr>';
						$html .= '<td style="height:230px;width:139px" valign="top">
							<a href="http://134.213.29.220" target="_blank">
							<img src="'.$profile_image.'" alt="'.$row['first_name'].'">
							</a>
							<span style="display:block;background-color:Black;width:131px;color:#ffffff;font-weight:bold;font-size:11px;margin-left:1px">
							'.$row['first_name'].'&nbsp;'.$row['profile_number'].';
							</span>
							<span>
							</span>
							</td>';
						}
				}
				$html .= ' </tr>
					</tbody></table>
					</td>
					</tr>
					</tbody></table>';

			$subject = "Castit Lighbox";
			$headers = "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
			$headers .= 'From: Castit <info@ldsstage.in>' . "\r\n";
			$headers .= 'Reply-To: <'.$from_email.'>' . "\r\n";


			//$headers = "From: info@ldsstage.in\r\n";
		
  $headers .=  'Return-Path: <<info@ldsstage.in>' ."\r\n";
  $headers .= "Organization: Sender Organization"."\r\n";
  $headers .= "X-Priority: 3\r\n";
  $headers .= "X-Mailer: PHP". phpversion() ."\r\n" ;



			//$headers .= "Reply-To: myplace2@example.com\r\n";
			//$headers .= 'Return-Path: <'.$from_email.'>' ."\r\n";
			//$headers .= "CC: sombodyelse@example.com\r\n";
			// $headers .= "BCC: hidden@example.com\r\n";
			// $headers .= 'BCC: cat@castit.dk' . "\r\n";
  		$headers .= 'BCC: padmanabhann@mailinator.com, vs@anewnative.com' . "\r\n";

			if($to_cc){
				$headers .= 'CC: <'.$to_cc.'>' ."\r\n";
			}
			//$html .= 'testemail';
			mail( $to_email, $subject, $html, $headers ); // Accountant
			$response['success'] = true;
			$response['message'] = 'Group er sendt!';
			}
			else{
			$response['success'] = true;
			$response['message'] = 'No Profiles in Group';
			}
		

	echoResponse(200, $response);
});

/*$app->post('/sendgroup', function () use ($app) { 
    global $db;
	$data = json_decode($app->request->getBody(), true);
	$response = array();
	$to_email = $data['to_email'];
	$from_email = $data['form_email'];
	$mail_body ='';$to_cc ='';
	if(isset($data['mail_body'])){
		$mail_body = $data['mail_body'];
	}
	if(isset($data['to_cc'])){
		$to_cc = $data['to_cc'];
	}
	
			$lbid = 14;
				
			$query_lb_pprofiles = $db->prepare("SELECT * FROM profile_grouping WHERE group_id ='".$lbid."'"); 
			$query_lb_pprofiles->execute();
			$rows_lb_pprofiles = $query_lb_pprofiles->fetchAll(PDO::FETCH_ASSOC);
			$rowcount = count($rows_lb_pprofiles);
			alert($rowcount);
			if($rowcount > 0){
				$html = '<table style="text-align:left" cellspacing="0" cellpadding="0" width="556" border="0">
							<tbody>
							<tr>
							<td>
							<table style="width:100%" border="0">
							<tbody>
							<tr>
							<td align="left"><img alt="Mailtoplogo" src="http://134.213.29.220/assets/mailTopLogo.png" ></td>
							<td style="width:270px;padding-top:18px" align="left" valign="top"><b style="color:#696969">Castit <span class="il">Lightbox</span>:</b><br>'.$mail_body.'</td>
							</tr>
							<tr>
							<td colspan="2"><img alt="Mailtopborder" src="http://134.213.29.220/assets/mailTopBorder.jpg"></td>
							</tr>
							</tbody>
							</table>
							</td>
							</tr>
							<tr>';

				
				foreach($rows_lb_pprofiles as $rowp) {
					
						$query = $db->prepare("SELECT p.*, m.profile_group_id, m.profile_number, m.profile_number_first_name_last_name, m.version, m.current, g.name as gender_name, hc.name as hair_color_name, ec.name as eye_color_name FROM profiles p INNER JOIN memberships m ON m.profile_id = p.id INNER JOIN genders g ON g.id = p.gender_id INNER JOIN hair_colors hc ON hc.id = p.hair_color_id INNER JOIN eye_colors ec ON ec.id = p.eye_color_id  WHERE p.id='".$rowp['profile_id']."' AND (p.profile_status_id = '1' OR  p.profile_status_id = '2') AND m.current ='1' LIMIT 1"); 
						$query->execute();
						$rows = $query->fetchAll(PDO::FETCH_ASSOC);
						if(count($rows)>0) {
							$row = $rows[0];
								$birthdate = new DateTime($row['birthday']);
								$today   = new DateTime('today');
								$age = $birthdate->diff($today)->y;
					
								// Profile Image
								$profile_image ='';
								$query_image = $db->prepare("SELECT *, DATE_FORMAT(created_at, '%Y') as create_year, DATE_FORMAT(created_at, '%m') as create_month, DATE_FORMAT(created_at, '%d') as create_date FROM photos WHERE profile_id = '".$row['id']."' and published ='1' ORDER BY position ASC LIMIT 1"); 
						$query_image->execute();
						$image= '';
						$rows_image = $query_image->fetchAll(PDO::FETCH_ASSOC);
						if(count($rows_image) > 0){
							if (strpos($rows_image[0]['path'], 'vhost') !== false) {
								$profile_image = 'http://134.213.29.220/images/uploads/'.$rows_image[0]['image'];
							}
							else{
								$path = $rows_image[0]['create_year']."/".$rows_image[0]['create_month']."/".$rows_image[0]['create_date']."/".$rows_image[0]['id']."/thumb_";
								$profile_image = 'http://134.213.29.220/profile_images/'.$path.$rows_image[0]['image'];
							}
							
						}
			
						$html .= '<td style="vertical-align:top">
							<table style="width:100%">
							<tbody>
							<tr>';
						$html .= '<td style="height:230px;width:139px" valign="top">
							<a href="http://134.213.29.220/?l='.$lbid.'" target="_blank">
							<img src="'.$profile_image.'" alt="'.$row['first_name'].'">
							</a>
							<span style="display:block;background-color:Black;width:131px;color:#ffffff;font-weight:bold;font-size:11px;margin-left:1px">
							'.$row['first_name'].'&nbsp;'.$row['profile_number'].';
							</span>
							<span>
							</span>
							</td>';
						}
				}
				$html .= ' </tr>
					</tbody></table>
					</td>
					</tr>
					</tbody></table>';

			$subject = "Castit Lighbox";
			$headers = "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
			$headers .= 'From: Castit <info@ldsstage.in>' . "\r\n";
			//$headers .= 'Reply-To: <'.$from_email.'>' . "\r\n";
			if($to_cc){
				$headers .= 'CC: <'.$to_cc.'>' ."\r\n";
			}
			//$html .= 'testemail';
			mail( $to_email, $subject, $html, $headers ); // Accountant
			$response['success'] = true;
			$response['message'] = 'Lightbox er sendt!';
			}
			else{
			$response['success'] = true;
			$response['message'] = 'No Profiles in Lightbox';
			}
		}
		else{
			$response['success'] = true;
			$response['message'] = 'No Profiles in Lightbox';
		}

	
	
	echoResponse(200, $response);
});
*/

/******************************************
Purpose: Get grouping list for dropdown
Parameter : 
Type : GET
******************************************/
$app->get('/getgroupinglist', function () use ($app) { 
    global $db;
	$grouping = array();
	$reponse =  array();
	$grouptoken =  $app->request->get('grouptoken');

	$query_grouping = $db->prepare("SELECT * FROM grouping WHERE token_id='".$grouptoken."' AND status ='1' order by group_name"); 
	$query_grouping->execute();
	$rows_grouping = $query_grouping->fetchAll(PDO::FETCH_ASSOC);
	$rowcount = count($rows_grouping);
    foreach($rows_grouping as $row) {
		$grouping[] = array('id' => $row['group_id'],
						  'name' => $row['group_name'],
						);
	}
	$reponse =  array('count'=> $rowcount, 'grouping'=>$grouping);
	echoResponse(200, $reponse);

});
/******************************************
Purpose: Add new grouping
Parameter : 
Type : GET
******************************************/
$app->get('/addnewgrouping', function () use ($app) { 
    global $db;
	$groupname =  $app->request->get('groupname');
	$grouping = array();
	$reponse = array();
	$rowcount = 0;

	$grouping_token = $app->request->get('grouptoken');
	
	$query_check_gp = $db->prepare("SELECT * FROM `grouping` where `group_name` LIKE '%".$groupname."%' AND token_id ='".$grouping_token."'"); 
	$query_check_gp->execute();
	$rows_gp = $query_check_gp->fetchAll(PDO::FETCH_ASSOC);
	if(count($rows_gp)>0) {
		$gpid = $rows_gp[0]['group_id'];
	}else{
			$q_gruping = "INSERT INTO `grouping` ( `token_id`, `group_name`, `status`) VALUES ('".$grouping_token."', '".$groupname."','1')"; 
			//echo $q; 
			$gpid = $db->exec($q_gruping);
	}
	
	$query_grouping = $db->prepare("SELECT * FROM grouping WHERE status ='1' AND token_id ='".$grouping_token."' order by group_name"); 
	$query_grouping->execute();
	$rows_grouping = $query_grouping->fetchAll(PDO::FETCH_ASSOC);
	$rowcount = count($rows_grouping);
    foreach($rows_grouping as $row) {
		$grouping[] = array('id' => $row['group_id'],
						  'name' => $row['group_name'],
						);
	}
	$reponse =  array('count'=> $rowcount, 'grouping'=>$grouping);
	echoResponse(200, $reponse);

  
});







// Landing Page data

$app->get('/homepage',function () use ($app) { 
    global $db;
	$query = $db->prepare("SELECT p.*,  FROM profiles p WHERE 1=1 AND ( p.profile_status_id = 1' OR p.profile_status_id ='' 2' ) LIMIT 10"); 
	$query->execute();
    $rows = $query->fetchAll(PDO::FETCH_ASSOC);
//	print_r($rows[0]);exit;
	if(count($rows)>0) {
		
		foreach($rows as $row)
			$response = array('success' => true, 'datahome' => $rows[0] );
	}
	else {
		$response['success'] = false;
		$response['message'] = 'Unbale to load profiles';
	}
	
	echoResponse(200, $response);
});

/******************************************
Purpose: Registration step 2 store data @session
Parameter : form field
Type : POST
******************************************/
$app->post('/customercreatestep', function () use ($app) { 
    global $db;
	$data = json_decode($app->request->getBody(), true);
	if($_SESSION['customer'] = $data){
			
			$_SESSION['step2'] = 1;
			/* SpringEdge - SMS Integration
			   OTP send to user mobile	
			*/
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,"https://instantalerts.co/api/web/send");
			curl_setopt($ch, CURLOPT_POST, 1);
			
			$otp = rand(1000,9999);
			$mobile = $data['phone_1'];
			
			$message = 'Your One Time Password is '.$otp;
			//$postval = array('method' => 'sms', 'api_key' => 'A17b14ff38f28bcda16b304cd3c924f66', 'sender' => 'SPEDGE', 'to' => $mobile, 'message' => $message, 'format' => 'json');
			$postval = array('method' => 'sms', 'apikey' => '69iq54a4m4s4ib0agg135o3y0yfbkbmbu', 'sender' => 'SEDEMO', 'to' => $mobile, 'message' => $message, 'format' => 'json');
			
			curl_setopt($ch, CURLOPT_POSTFIELDS, 
			http_build_query($postval));
			
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$server_output = curl_exec ($ch);
			curl_close ($ch);
			
			$_SESSION['otp'] = $otp;
			$_SESSION['mobile'] = $mobile;
			
			//print_r($server_output);
			
			$response = array('sucess' => true, 'message' => 'Session stored');
	}else{
			//$_SESSION['step2'] = 0;
			$response = array('sucess' => false, 'message' => 'There is some problem to store data. Please try again');
	}
	
	echoResponse(200, $response);
});

/******************************************
Purpose: Home page
Parameter : NIL
Type : POST
******************************************/
$app->post('/aboutus',function () use ($app) { 
    global $db;
	
	$data =  json_decode($app->request->getBody(), true);
	
	$response = array();
	
    $rows = $db->select("ad_user","password, usertype", array('username' => $username), "");
	
	if(count($rows['data'])>0) {
		if($rows['data'][0]['password'] == md5($password)) {
			$response = array('success' => true, 'usertype' => $rows['data'][0]['usertype'] );
		}
		else {
			$response = array('success' => false, 'message' => 'Username or password is incorrect');
		}
	}
	else {
		$response['success'] = false;
		$response['message'] = 'Username or password is incorrect';
	}
	
	echoResponse(200, $response);
});
/******************************************
Purpose: Login check 
Parameter : username and password
Type : POST
******************************************/
$app->post('/login',function () use ($app) { 
    global $db;
	
	$data =  json_decode($app->request->getBody(), true);
	
	$email = $data['email']; // Email / username
	$password = $data['password']; // password
	$response = array();
    $rows = $db->select("users","user_id, fname, lname, email, password, status, payment_status", array('email' => $email), "");
	//$response = array('success' => false, 'message' => count($rows['data']));
	//echoResponse(200, $response);exit;
	
	if(count($rows['data'])>0) {
		if($rows['data'][0]['password'] == md5($password)) {
			if($rows['data'][0]['status'] ==1){
				$_SESSION['login_user'] = 1;
				$_SESSION['login_userid'] = $rows['data'][0]['user_id'];
				$_SESSION['login_userfullname'] = $rows['data'][0]['fname']." ".$rows['data'][0]['lname'];
				$_SESSION['login_useremail'] = $rows['data'][0]['email'];
				$response = array('success' => true, 'userstatus' => $rows['data'][0]['status'] , 'userid' => $rows['data'][0]['user_id'] );
			}
			else{
				$response = array('success' => false, 'message' => 'You account is not activated yet');
			}
		}
		else {
			$response = array('success' => false, 'message' => 'Username or password is incorrect');
		}
	}
	else {
		$response['success'] = false;
		$response['message'] = 'Username or password is incorrect';
	}
	
	echoResponse(200, $response);
});
/******************************************
Purpose: Get Profile detail
Parameter : null
Type : GET
******************************************/
$app->get('/myprofiledetails',function () use ($app) { 
    global $db;
	
	$query = $db->prepare("SELECT u.*, s.name as state, c.name as countryname FROM users u, states s, countries c WHERE s.id= u.region and c.id=u.country and u.user_id='".$_SESSION['login_userid']."' "); 
	$query->execute();
    $rows = $query->fetchAll(PDO::FETCH_ASSOC);
	//print_r($rows[0]);
	$customer = array();
	if(count($rows[0])>0){
		$customer['owner_name'] = $rows[0]['fname']." ".$rows[0]['lname'];
		$customer['owner_email'] = $rows[0]['email'];
		$customer['owner_phone'] = $rows[0]['phone_1'];
		$customer['owner_phone1'] = $rows[0]['phone_2'];
		$address = $rows[0]['address_1'];
		if($rows[0]['address_2']) {
			$address .= "-".$rows[0]['address_2'];
		}
		$address .= "-".$rows[0]['city'];
		
		$address .= "-".$rows[0]['city'].", ".$rows[0]['state'].", ".$rows[0]['post'];
		$address .= "<br>".$rows[0]['countryname'];
		$customer['owner_address'] = $address;
		
	}
	//print_r($customer);
	//$customers = array('customer' => $rows[0]);
	$query_pets = $db->prepare("SELECT p.*, s.type_name as species_name, bt.breed_name as breedname FROM pets p, species s, breed_type bt WHERE s.type_id = p.species_id AND bt.breed_id = p.breed_type AND p.owner_id='".$_SESSION['login_userid']."' "); 
	$query_pets->execute();
    $rows_pets = $query_pets->fetchAll(PDO::FETCH_ASSOC);
	//print_r($rows_pets);
	//$pet = $rows_pets[0];
	$customers = array('customer' => $rows[0], 'custpets' => $rows_pets);
	
	echoResponse(200, $customers);
});
/******************************************
Purpose: Get User details
Parameter : 
Type : GET
******************************************/
$app->get('/account', function () use ($app) { 
    global $db;
	$userid =  $app->request->get('userid');
    $rows = $db->select("users","fname,lname,email", array('user_id' => $userid), '');
	$account = $rows['data'][0];
	
	echoResponse(200, $account);
});
/******************************************
Purpose: Update User details
Parameter : Form Fields
Type : POST
******************************************/
$app->post('/accountupdate', function () use ($app) { 
    global $db;
	$data = json_decode($app->request->getBody(), true);
	
	if($data['password']=='') {
		$rows = $db->select("ad_user","password", array('usertype' => $data['usertype']), '');
		$password = $rows['data'][0]['password'];
	}
	else {
		$password = md5($data['password']);
	}
	
    $q = "UPDATE `ad_user` SET `email`='".$data['email']."', `password`='".$password."'
	WHERE `usertype`='".$data['usertype']."'";
	
	$query = $db->prepare($q);
	if($query->execute()) {
		$response = array('sucess' => true, 'message' => 'Customer info updated Sucessfully!');
	}
	else {
		$response = array('sucess' => false, 'message' => 'Customer info not updated. Please try again later');
	}
	echoResponse(200, $response);
});
/******************************************
Purpose: Newsletter Subscription
Parameter : form field
Type : POST
******************************************/
$app->post('/newsletterSubscribe',function () use ($app) { 
    global $db;
	$data = json_decode($app->request->getBody(), true);
	$email = $data['email'];
	$emailarr = explode('@',$email);
	$domain = $emailarr[1];
	$ip = getIP();
	$confirmed_ip = getIP();
	$created_at = strtotime("now");
	$confirmed_at = strtotime("now");
	$status =1;
	//print_r($data);
	
	$query = $db->prepare("SELECT * FROM wp_wysija_user WHERE email='".$email."'"); 
	$query->execute();
    $rows = $query->fetchAll(PDO::FETCH_ASSOC);
	if((isset($rows)) && count($rows)<= 0){
		$q_news = "INSERT INTO `wp_wysija_user` ( `email`, `ip`, `confirmed_ip`, `confirmed_at`, `created_at`, `status`, `domain`) VALUES ('".$email."', '".$ip."', '".$confirmed_ip."', '".$confirmed_at."', '".$created_at."', '".$status."', '".$domain."')";
		$query_news = $db->exec($q_news);
		if($query_news){
			$subscribeid = $query_news;
			$q_news1 = "INSERT INTO `wp_wysija_user_list` (`list_id`, `user_id`, `sub_date`, `unsub_date`)VALUES ('1', '".$subscribeid."', '".$confirmed_at."', '0')";
			$query_news1 = $db->exec($q_news1);
		}
	
	$response = array('sucess' => true, 'message' => 'Thank you for your subscription !', 'type' => 'subscribe');
	}
	else{
	$response = array('sucess' => false, 'message' => 'You have already subscribed with this email !', 'type' => 'subscribe');
	}
	echoResponse(200, $response);
});
/******************************************
Purpose: Registration step 2 verify OTP
Parameter : otp
Type : POST
******************************************/
$app->post('/resetpassword',function () use ($app) { 
    global $db;
	$data = json_decode($app->request->getBody(), true);
	$email = $data['email'];
	$query = $db->prepare("SELECT * FROM users WHERE email='".$email."'"); 
	$query->execute();
    $rows = $query->fetchAll(PDO::FETCH_ASSOC);
	//print_r($rows);die;
	if((isset($rows)) && count($rows)> 0){
		if($rows[0]['status'] ==1){

			$alphabet = 'abdefghklmnpqrstuvwxyzABDEFGHKLMNPQRSTUVWXYZ23456789';
			$pass = array(); //remember to declare $pass as an array
			$alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
			for ($i = 0; $i < 8; $i++) {
				$n = rand(0, $alphaLength);
				$pass[] = $alphabet[$n];
			}
			$newpass = implode($pass);
			$randompass = md5($newpass);
			$q_pwd = "UPDATE `users` SET `password`='".$randompass."' WHERE `email`='".$email."'";
			$query = $db->prepare($q_pwd);
			$query->execute();
				$content = '';
				$content .= '<!DOCTYPE html><html lang="en"><head>
								<meta content="text/html; charset=UTF-8" http-equiv="content-type">
								</head>
								<body style="background:#fff; font-family:Calibri;">
								<div style="background:#fff;width:100%;float:left;">
							  <div style="width:100%; margin:auto; text-align:center;">
								<div style="display:inline-block; background:#fff; border:solid 3px #313743; 	
								width:580px;-webkit-border-radius: 8px;-moz-border-radius: 8px;border-radius: 8px; 	
								padding:0 0 13px; margin:18px 0 50px 0;">
								<div style="color: #20be93;font-size: 23px;font-family: Calibri; 
								background:#313743;float:left; width:100%; text-align:center; margin:0 0 16px 0; 
								padding:8px 0 4px;"><img src="http://dev.tailtracking.com/assets/img/logo.png" 
								width="90px"/> </div>
								<div style="padding:0 30px;">';
					$content .= '<h5 style="color: #646e78;font-size: 16px;padding:0;margin: 0; text-align:left;">Hi '.$rows[0]['fname'].' '.$rows[0]['lname'].',</h5>';
				
					$content .= '<p style="color: #646e78;font-size: 16px;text-align:left;">Your new requested password is,</p>';
					$content .= '<p style="color: #646e78;font-size: 16px;padding:0; line-height:18px; text-align:left; 
								">Username :'.$email.'<br/>Password :'.$newpass.'<br/></p>
								<p style="color: #646e78;font-size: 16px;padding:0; line-height:18px; text-align:left; 
								">Please login in to your account and change the password<br/></p>';
					$content .= '<p style="color: #646e78;text-align:left;font-size: 16px;padding:0 0 45px 0; margin:51px 0 0; 
								line-height:20px; text-align:left;font-family:Calibri">Thank you!<br><br>Best 
								regards,<br>TailTracking.</p>
								<div style="float:left; width:100%; margin:40px 0 0 0; border-top:solid 1px #dddddd; 	
								padding:20px 0 0 0;"> <span style="color: #646e78;font-size: 12px; 
								float:left;line-height:34px;">Copyrights @ 2017. All Rights Reserved.</span> ';
					$content.= '</div></div></div></div></div></body></html>';	
				//echo 	$content;	
				$to = $email;
				$subject = "Tailtracking password request";
				$headers = "MIME-Version: 1.0" . "\r\n";
				$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
				$headers .= 'From: TailTracking Admin<noreplay@tailtracking.com>' . "\r\n";
				mail( $to, $subject, $content, $headers ); // Accountant
			
			$response = array( 'success' => true, 'message' => 'Your new password has been sent to your mentioned mail id. Please check your mail.');
		}
		else{
			$response = array( 'success' => false, 'message' => 'Your account is in inactive status. Please contact Administrator');
		}
	}
	else{
		$customer ='';
			$response = array( 'success' => false, 'message' => 'There is no data for this email in our database. Please check your email.');
		}
	echoResponse(200, $response);
});

/******************************************
Purpose: Create and get Group Token
Parameter : null
Type : GET
******************************************/
$app->get('/getgrouptoken',function () use ($app) { 
    global $db;
	$group_token = generate_uuid();
	$response = array( 'success' => true, 'grouptoken' => $group_token);
	echoResponse(200, $response);
});

$app->post('/fileuploadparser', function () use ($app) {
	// unset($_SESSION['Video_file']);
	// unset($_SESSION['Image_file']);
  $cdnfilepath	=	'';
  $time 				=	time();  
	if(!isset($_REQUEST["uploaded_file_type"])){
		$_SESSION["Image_file"][]	= $_FILES["Image_file"];
	  $fileName     = $_FILES["Image_file"]["name"][0];
	  $fileTmpLoc   = $_FILES["Image_file"]["tmp_name"][0];
	  $fileType     = $_FILES["Image_file"]["type"];
	  $fileSize     = $_FILES["Image_file"]["size"];
	  $fileErrorMsg = $_FILES["Image_file"]["error"];
	  $location = $_SERVER['DOCUMENT_ROOT'].'/images/uploads/';

	  if (!$fileTmpLoc) { // if file not chosen
	      echo "ERROR: Please browse for a file before clicking the upload button.";
	      exit();
	  }
	  if(move_uploaded_file($fileTmpLoc, $location.$fileName)){
	    echo json_encode(['status_message'=>'file upload success', 'filename'=>$fileName]);
	  } 
	  else {
	    echo "move_uploaded_file function failed";
	  }
	}	
  
  if(isset($_REQUEST["uploaded_file_type"]) && $_REQUEST["uploaded_file_type"] == "video"){
	  unset($_SESSION["Video_file"]);
	  $fileTmpLoc   = $_FILES["Video_file"]["tmp_name"][0];

    $client = new Rackspace(Rackspace::UK_IDENTITY_ENDPOINT, array(
      'username' => 'castit',
      'apiKey'   => '187a515209d0affd473fedaedd6d770b'
    ));

    $location 					= $_SERVER['DOCUMENT_ROOT'].'/images/uploads/';
    $objectStoreService = $client->objectStoreService(null, 'LON');
    $container          = $objectStoreService->getContainer('video_original_files');
    $date_dir           = date("o-m-d");
    $fileName						= $_FILES["Video_file"]["name"][0];
    $localFileName      = $location.$fileName;
    $remoteFileName     = "/profiles/".$date_dir."/".$time."__".$fileName;
    $cdnfilepath     		= "/videos/profiles/".$date_dir;
	  $cdnfilename 				= $time."__".$fileName;
	  $thumbnail 					= "thumb_".$cdnfilename.".png";
		$_FILES["Video_file"]["cdnfilepath"] = $cdnfilepath;
		$_FILES["Video_file"]["cdnfilename"] = $cdnfilename;
		$_FILES["Video_file"]["thumbnail"] = $thumbnail;


  	$_SESSION["Video_file"][]	= $_FILES["Video_file"];
		move_uploaded_file($fileTmpLoc, $location.$fileName);
    
    $handle = fopen($localFileName, 'r');
    $container->uploadObject($remoteFileName, $handle);
    unset($handle);


    $zencoder_input   	= "cf+uk://castit:187a515209d0affd473fedaedd6d770b@video_original_files".$remoteFileName;
    $zencoder_output  	= "cf+uk://castit:187a515209d0affd473fedaedd6d770b@videos_public/videos".$remoteFileName;
    $zencoder_base_url  = "cf+uk://castit:187a515209d0affd473fedaedd6d770b@videos_public".$cdnfilepath;

    $zencoder_array = [
      "input_file"		=> $zencoder_input,
      "output_file"		=> $zencoder_output,
      "base_url"			=> $zencoder_base_url,
      "filename"			=> $cdnfilename,
    ];

    // $zencoder_json = json_encode($zencoder_array);
    $zencoder_json = build_json_zencoder($zencoder_array);

    // ppe($zencoder_json);
    $url = 'https://app.zencoder.com/api/v2/jobs';
    $ch = curl_init( $url );
    curl_setopt( $ch, CURLOPT_POST, 1);
    curl_setopt( $ch, CURLOPT_POSTFIELDS, $zencoder_json);
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER , 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json ',
      'Zencoder-Api-Key: 9477541a57e1eb2471b1ff256ca4b92c'
    ));

    $response = curl_exec( $ch );
    echo json_encode(['status_message'=>'file upload success', 'filename'=>$fileName, 'cdnfilepath'=>$cdnfilepath, 't'=>$time, 'thumbnail'=>$thumbnail]);
  }
});

function build_json_zencoder($data_array){
	$json = '{
	"input": "'.$data_array["input_file"].'",
	"outputs": [{
		"thumbnails": [
				{
					"base_url": "'.$data_array["base_url"].'",
					"label": "regular",
					"number": 1,
					"filename": "thumb_'.$data_array["filename"].'",
					"public": "true"
				}
			]
		},
    {"label": "mp4 high"},
    {"url": "'.$data_array["output_file"].'"},
    {"h264_profile": "high"}
	]
	}';
	return $json;
}

//JSON coneverion
function echoResponse($status_code, $response) {
    global $app;
    $app->status($status_code);
    $app->contentType('application/json');
    echo json_encode($response);
}
function getIP() {
        $ip = '';
        // cloudFlare IP check
        if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            $ip = strip_tags($_SERVER['HTTP_CF_CONNECTING_IP']);
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']) AND strlen($_SERVER['HTTP_X_FORWARDED_FOR']) > 6) {
            $ip = strip_tags($_SERVER['HTTP_X_FORWARDED_FOR']);
        } elseif (!empty($_SERVER['HTTP_CLIENT_IP']) AND strlen($_SERVER['HTTP_CLIENT_IP']) > 6) {
            $ip = strip_tags($_SERVER['HTTP_CLIENT_IP']);
        } elseif (!empty($_SERVER['REMOTE_ADDR']) AND strlen($_SERVER['REMOTE_ADDR']) > 6) {
            $ip = strip_tags($_SERVER['REMOTE_ADDR']);
        }//endif
        if (empty($ip))
            $ip = '127.0.0.1';
        return strip_tags($ip);
    }
	
//Related functions

function generate_uuid() {
    return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
        mt_rand( 0, 0xffff ),
        mt_rand( 0, 0x0fff ) | 0x4000,
        mt_rand( 0, 0x3fff ) | 0x8000,
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
    );
}	
	
	
$app->run();

function pp($q){
  echo '<pre>';
  print_r($q);
  echo '</pre>';
}

function ppe($q){
  pp($q);exit;
}
?>