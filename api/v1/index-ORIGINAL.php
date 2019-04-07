<?php
require '../libs/Slim/Slim.php';
require_once 'dbHelper.php';
require_once 'SimpleImage.php';

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


	$query = $db->prepare("SELECT p.*, m.profile_group_id, m.profile_number, m.profile_number_first_name_last_name, m.version, m.current, g.name as gender_name, hc.name as hair_color_name, ec.name as eye_color_name FROM profiles p INNER JOIN memberships m ON m.profile_id = p.id INNER JOIN genders g ON g.id = p.gender_id INNER JOIN hair_colors hc ON hc.id = p.hair_color_id INNER JOIN eye_colors ec ON ec.id = p.eye_color_id  WHERE (p.profile_status_id = '1' OR  p.profile_status_id = '2') AND m.current ='1'AND (m.profile_number LIKE 'C%' OR m.profile_number LIKE 'A%'OR m.profile_number LIKE 'J%' OR m.profile_number LIKE 'Y%') ORDER BY m.profile_number "); 

	//echo $query;exit;
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
	if(count($rows_image) > 0){
		$path = $rows_image[0]['create_year']."/".$rows_image[0]['create_month']."/".$rows_image[0]['create_date']."/".$rows_image[0]['id']."/big_";
		$profile_image = 'https://castit.dk/profile_images/'.$path.$rows_image[0]['image'];
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
		$search_qry .= " AND (p.first_name LIKE '%".$search_text."%' OR p.last_name LIKE '%".$search_text."%' OR m.profile_number LIKe '%".$search_text."%')";		
	}
	$qry = "SELECT p.*, m.profile_group_id, m.profile_number, m.profile_number_first_name_last_name, m.version, m.current, g.name as gender_name, hc.name as hair_color_name, ec.name as eye_color_name FROM profiles p INNER JOIN memberships m ON m.profile_id = p.id INNER JOIN genders g ON g.id = p.gender_id INNER JOIN hair_colors hc ON hc.id = p.hair_color_id INNER JOIN eye_colors ec ON ec.id = p.eye_color_id  WHERE (p.profile_status_id = '1' OR  p.profile_status_id = '2') AND m.current ='1' ".$search_qry."  ORDER BY p.created_at";
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
	if(count($rows_image) > 0){
		$path = $rows_image[0]['create_year']."/".$rows_image[0]['create_month']."/".$rows_image[0]['create_date']."/".$rows_image[0]['id']."/big_";
		$profile_image = 'https://castit.dk/profile_images/'.$path.$rows_image[0]['image'];
		
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
				$path = $row_image['create_year']."/".$row_image['create_month']."/".$row_image['create_date']."/".$row_image['id']."/big_";
				$profile_images[] = array('imgcnt' => $imgc, 'urloriginal' =>$row_image['image'], 'fullpath'=>'https://castit.dk/profile_images/'.$path.$row_image['image']);
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
							$path = $rows_image[0]['create_year']."/".$rows_image[0]['create_month']."/".$rows_image[0]['create_date']."/".$rows_image[0]['id']."/thumb_";
							$profile_image = 'https://castit.dk/profile_images/'.$path.$rows_image[0]['image'];
							
							}
								$lb_note = isset($_SESSION["lb_notes"][$row['id']]) ? $_SESSION["lb_notes"][$row['id']]: '' ;
								$groupnamear = array();
								$lb_group_qry = "SELECT pg.*, g.group_name from profile_grouping pg JOIN grouping g ON pg.group_id = g.group_id WHERE pg.profile_id='".$row['id']."'";	
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
							$path = $rows_image[0]['create_year']."/".$rows_image[0]['create_month']."/".$rows_image[0]['create_date']."/".$rows_image[0]['id']."/thumb_";
							$profile_image = 'https://castit.dk/profile_images/'.$path.$rows_image[0]['image'];
							
							}
								$lb_note = isset($_SESSION["lb_notes"][$row['id']]) ? $_SESSION["lb_notes"][$row['id']]: '' ;
								$groupnamear = array();
								$lb_group_qry = "SELECT pg.*, g.group_name from profile_grouping pg JOIN grouping g ON pg.group_id = g.group_id WHERE pg.profile_id='".$row['id']."'";	
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
					$path = $rows_image[0]['create_year']."/".$rows_image[0]['create_month']."/".$rows_image[0]['create_date']."/".$rows_image[0]['id']."/thumb_";
					$profile_image = 'https://castit.dk/profile_images/'.$path.$rows_image[0]['image'];
					
					}
				$groupnamear = array();
				$lb_group_qry = "SELECT pg.*, g.group_name from profile_grouping pg JOIN grouping g ON pg.group_id = g.group_id WHERE pg.profile_id='".$row['id']."'";	
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

	$query_check_gb = $db->prepare("SELECT *, date_format(added_on , '%d.%m.%Y') as addedon  FROM grouping where status = '1' order by group_name asc"); 
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
							$path = $rows_image[0]['create_year']."/".$rows_image[0]['create_month']."/".$rows_image[0]['create_date']."/".$rows_image[0]['id']."/thumb_";
							$profile_image = 'https://castit.dk/profile_images/'.$path.$rows_image[0]['image'];
							
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

	if($groupid){
		$query_check_gb = $db->prepare("SELECT * FROM grouping where group_id = '".$groupid."'"); 
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

	$query_check_gb = $db->prepare("SELECT *, date_format(added_on , '%d.%m.%Y') as addedon  FROM grouping where status = '1' order by group_name asc"); 
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
							$path = $rows_image[0]['create_year']."/".$rows_image[0]['create_month']."/".$rows_image[0]['create_date']."/".$rows_image[0]['id']."/thumb_";
							$profile_image = 'https://castit.dk/profile_images/'.$path.$rows_image[0]['image'];
							
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

	$grouping_token = generate_uuid();
	
	$query_check_gp = $db->prepare("SELECT * FROM `grouping` where `group_name` LIKE '%".$groupname."%'"); 
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

	$query_check_gb = $db->prepare("SELECT *, date_format(added_on , '%d.%m.%Y') as addedon  FROM grouping where status = '1' order by group_name asc"); 
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
							$path = $rows_image[0]['create_year']."/".$rows_image[0]['create_month']."/".$rows_image[0]['create_date']."/".$rows_image[0]['id']."/thumb_";
							$profile_image = 'http://castit.dk/assets/profile_images/'.$path.$rows_image[0]['image'];
							
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
	$response = array();
	global $imageClass;

	$_SESSION["step1"]["status"] =1;
	$_SESSION["step1"]["first_name"]= $data['first_name'];
	$_SESSION["step1"]["last_name"]= $data['last_name'];
	$_SESSION["step1"]["password"]= $data['password'];
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
	$eye_colors = $db->prepare("SELECT * FROM eye_colors"); 
	$eye_colors->execute();
	$eye_colors_list = $eye_colors->fetchAll(PDO::FETCH_ASSOC);
	$response['eye_colors']=$eye_colors_list;

	$hair_colors = $db->prepare("SELECT * FROM hair_colors"); 
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
	$query_categories = $db->prepare("SELECT * FROM categories order by name"); 
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
	$query_skills = $db->prepare("SELECT * FROM skills order by name"); 
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
	$query_licence = $db->prepare("SELECT * FROM drivers_licenses order by name"); 
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
	if(isset($_SESSION["step5"]["dealekter1"]) && $_SESSION["step5"]["dealekter1"]!='')
		$dealekter1=$_SESSION["step5"]["dealekter1"];
	if(isset($_SESSION["step5"]["dealekter2"]) && $_SESSION["step5"]["dealekter2"]!='')
		$dealekter2=$_SESSION["step5"]["dealekter2"];
	if(isset($data['dealekter3']) && $_SESSION["step5"]["dealekter3"]!='')
		$dealekter3=$_SESSION["step5"]["dealekter3"];

		$agreed_to_these_terms=1;
		$q_chip = "INSERT INTO `profiles` ( `first_name`, `last_name`, `gender_id`, `hair_color_id`,`eye_color_id`, `birthday`, `height`, `weight`, `shoe_size_from`, `shoe_size_to`, 	`shirt_size_from`,`shirt_size_to`,`pants_size_from`,`pants_size_to`,`bra_size`,`children_sizes`,`address`,`zipcode`,`city`,`country_id`,`phone`,`phone_at_work`,`email`,`job`,`notes`,`agreed_to_these_terms`,`password`,`hashed_password`,`created_at`,`updated_at`,`suite_size_from`,`suite_size_to`) VALUES ('".$first_name."', '".$last_name."','".$gender_id."',".$hair_color_id.",".$eye_color_id.",'".$birthday."',".$height.",".$weight.",".$shoe_size_from.",".$shoe_size_to.",".$shirt_size_from.",".$shirt_size_to.",".$pants_size_from.",".$pants_size_from.",".$bra_size.",".$children_sizes.",'".$address."','".$zipcode."','".$city."','".$country_id."','".$phone."','".$phone_at_work."','".$email."','".$job."','".$notes."','".$agreed_to_these_terms."','".$password."','".$hashed_password."',now(),now(),".$suite_size_from.",".$suite_size_to.")";
				$profile_id = $db->exec($q_chip);
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
					if(!empty($selectedlicences)){
						foreach($languages as $language){
							$query = "INSERT INTO `language_proficiencies` (`language_proficiency_language_id`,`profile_id`,`language_proficiency_rating_id`,`created_at`,`updated_at`) VALUES ('".$language['language_id']."','".$profile_id."','".$language['rating_id']."',now(),now())";
							//echo $query;
							$db->exec($query);
						}
					}
						if(isset($_FILES['Image_file']['name'][0])){
							foreach($_FILES['Image_file']['name'] as $key=>$value){
								$filename = $_FILES['Image_file']['name'][$key];
								$location = $_SERVER['DOCUMENT_ROOT'].'/images/uploads/';
								move_uploaded_file($_FILES['Image_file']['tmp_name'][$key],$location.$filename);
								$query = "INSERT INTO `photos` (`path`,`original_path`,`profile_id`,`filename`,`published`,`position`,`phototype_id`,`image`,`created_at`,`updated_at`,`image_tmp`,`image_processing`,`image_token`) VALUES ('".$location."','".$location."','".$profile_id."','".$filename."','1','".$key."','1','".$filename."',now(),now(),'".$filename."','1','".$filename."')";
								$db->exec($query);
							}	
						}
					
					if(isset($_FILES['Video_file']['name'][0])){
						//echo "ok";die;
						foreach($_FILES['Video_file']['name'] as $key=>$value){
							$filename = $_FILES['Video_file']['name'][$key];
							$location = $_SERVER['DOCUMENT_ROOT'].'/images/uploads/';
							move_uploaded_file($_FILES['Video_file']['tmp_name'][$key],$location.$filename);
							$query = "INSERT INTO `videos` (`profile_id`,`path`,`uploaded_as_filename`,`filename`,`video_original_path`,`video_original_filename`,`video_original_file_basename`,`thumbnail_original_photo_path`,`thumbnail_photo_path`,`thumbnail_photo_filename`,`thumbnail_at_time`,`published`,`position`) VALUES ('".$profile_id."','".$location."','".$location."','".$filename."','".$filename."','".$filename."','".$filename."','".$filename."','".$filename."','".$filename."','3','1','".$key."')";
							//echo $query;die;
							$db->exec($query);
						}	
					
					}
				session_destroy();
				echoResponse(200,array('status'=>true,'msg'=>'Registered Sucessfully'));

				}
				else{
					echoResponse(200,array('status'=>false,'msg'=>'Couldn\'t Register, Please try again later'));
				}
			
				
		
	

	
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
	$mail_body ='';$to_cc ='';
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
							<td align="left"><img alt="Mailtoplogo" src="http://castit.dk/assets/mailTopLogo.jpg" ></td>
							<td style="width:270px;padding-top:18px" align="left" valign="top"><b style="color:#696969">Castit <span class="il">Lightbox</span>:</b><br>'.$mail_body.'</td>
							</tr>
							<tr>
							<td colspan="2"><img alt="Mailtopborder" src="http://castit.dk/assets/mailTopBorder.jpg"></td>
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
							$path = $rows_image[0]['create_year']."/".$rows_image[0]['create_month']."/".$rows_image[0]['create_date']."/".$rows_image[0]['id']."/lightbox_";
							$profile_image = 'http://castit.dk/assets/profile_images/'.$path.$rows_image[0]['image'];
							
							}
			
						$html .= '<td style="vertical-align:top">
							<table style="width:100%">
							<tbody>
							<tr>';
						$html .= '<td style="height:230px;width:139px" valign="top">
							<a href="https://castit.dk/?l='.$lbid.'" target="_blank">
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
							<td align="left"><img alt="Mailtoplogo" src="http://castit.dk/assets/mailTopLogo.jpg" ></td>
							<td style="width:270px;padding-top:18px" align="left" valign="top"><b style="color:#696969">Castit <span class="il">Lightbox</span>:</b><br>'.$mail_body.'</td>
							</tr>
							<tr>
							<td colspan="2"><img alt="Mailtopborder" src="http://castit.dk/assets/mailTopBorder.jpg"></td>
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
							$path = $rows_image[0]['create_year']."/".$rows_image[0]['create_month']."/".$rows_image[0]['create_date']."/".$rows_image[0]['id']."/lightbox_";
							$profile_image = 'http://castit.dk/assets/profile_images/'.$path.$rows_image[0]['image'];
							
							}
			
						$html .= '<td style="vertical-align:top">
							<table style="width:100%">
							<tbody>
							<tr>';
						$html .= '<td style="height:230px;width:139px" valign="top">
							<a href="https://castit.dk" target="_blank">
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
			$headers .= "BCC: hidden@example.com\r\n";
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
							<td align="left"><img alt="Mailtoplogo" src="http://castit.dk/assets/mailTopLogo.jpg" ></td>
							<td style="width:270px;padding-top:18px" align="left" valign="top"><b style="color:#696969">Castit <span class="il">Lightbox</span>:</b><br>'.$mail_body.'</td>
							</tr>
							<tr>
							<td colspan="2"><img alt="Mailtopborder" src="http://castit.dk/assets/mailTopBorder.jpg"></td>
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
							$path = $rows_image[0]['create_year']."/".$rows_image[0]['create_month']."/".$rows_image[0]['create_date']."/".$rows_image[0]['id']."/lightbox_";
							$profile_image = 'http://castit.dk/assets/profile_images/'.$path.$rows_image[0]['image'];
							
							}
			
						$html .= '<td style="vertical-align:top">
							<table style="width:100%">
							<tbody>
							<tr>';
						$html .= '<td style="height:230px;width:139px" valign="top">
							<a href="https://castit.dk/?l='.$lbid.'" target="_blank">
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
	$query_grouping = $db->prepare("SELECT * FROM grouping WHERE status ='1' order by group_name"); 
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

	$grouping_token = generate_uuid();
	
	$query_check_gp = $db->prepare("SELECT * FROM `grouping` where `group_name` LIKE '%".$groupname."%'"); 
	$query_check_gp->execute();
	$rows_gp = $query_check_gp->fetchAll(PDO::FETCH_ASSOC);
	if(count($rows_gp)>0) {
		$gpid = $rows_gp[0]['group_id'];
	}else{
			$q_gruping = "INSERT INTO `grouping` ( `token_id`, `group_name`, `status`) VALUES ('".$grouping_token."', '".$groupname."','1')"; 
			//echo $q; 
			$gpid = $db->exec($q_gruping);
	}
	
	$query_grouping = $db->prepare("SELECT * FROM grouping WHERE status ='1' order by group_name"); 
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

$app->post('/petcreatestep',function () use ($app) { 
    global $db;
	$data = json_decode($app->request->getBody(), true);
	if($_SESSION['pet'] = $data){
			$_SESSION['step3'] = 1;
			if($_SESSION["microchip_type"]!='tailtracking'){
				$pet_id = "NULL";
				$owner_id =  "NULL";
				$q_chip = "INSERT INTO `micro_chip` ( `chip_code`, `chip_brand`, `chip_type`, `barcode_image`, `purchased_from`, `status`, `pet_id`, `owner_id`, `added_on`) VALUES ('".$_SESSION["microchip"]."', '".$_SESSION["chip_brand"]."','".$_SESSION["microchip_type"]."','".$_SESSION["chip_barcodemiage"]."','".$_SESSION["purchase_name"]."', '1', ".$pet_id.", ".$owner_id.", NOW())";
				$query_chip = $db->exec($q_chip);
				if($query_chip){
					$chipid = $query_chip;
	
				}
			}
			else{
				$rowschip = $db->select("micro_chip","chip_id, chip_type, status", array('chip_code' => $_SESSION["microchip"]), "");
		
				if(count($rowschip['data'])>0) {
					$chipid = $rowschip['data'][0]['chip_id'];
				}
			}
			//echo 'CHIPID'.$chipid;
						if(isset($_SESSION['customer']['address_2'])) $address_2 = $_SESSION['customer']['address_2'];else$address_2 = '';
						if(isset($_SESSION['customer']['phone_2'])) $phone_2 = $_SESSION['customer']['phone_2'];else $phone_2 = '';
						//print_r($_SESSION);
						//$_SESSION['customer']['fname'] ='sdsdsd';
						//$_SESSION['customer']['country'] ='101';
						//$_SESSION['customer']['region'] ='35';
						$userstatus = ($_SESSION["microchip_type"]=='tailtracking')?1:0;
						
						$q_user = "INSERT INTO `users` ( `fname`, `lname`, `email`, `password`, `address_1`, `address_2`, `city`, `post`, `country`, `region`, `phone_1`, `phone_2`, `added_on`, `updated_on`, `status`) VALUES ('".$_SESSION['customer']['fname']."', '".$_SESSION['customer']['lname']."', '".$_SESSION['customer']['email']."', '".md5($_SESSION['customer']['password'])."', '".$_SESSION['customer']['address_1']."', '".$address_2."', '".$_SESSION['customer']['city']."', '".$_SESSION['customer']['post']."', '".$_SESSION['customer']['country']."', '".$_SESSION['customer']['region']."', '".$_SESSION['customer']['phone_1']."', '".$phone_2."', NOW(), NOW(), '".$userstatus."')";
						//echo $q_user; exit;
						$query_user = $db->exec($q_user);
						//$new_user_id = '23';
						if(isset($data['special_marking'])) $special_marking = $data['special_marking'];else $special_marking = '';
						if(isset($data['description'])) $description = $data['description'];else $description = '';
						if(isset($data['image'])) 
						{
							$upload_path = BASE_PATH.'/images/pet/';
							$upload_url = BASE_URL.'/images/pet/';
							
							$profilePicture = explode(',', $data['image']);
							$pic_type = explode('/', $profilePicture[0]);
							$type = explode(';', $pic_type[1]);
							$rnd = time();
							$fileName = $rnd.'.'.$type[0]; // random number
							
							if(file_put_contents($upload_path.$fileName, base64_decode($profilePicture[1]))) { 
								$image = $upload_url.$fileName;
							}
							else {
								$image = '';
							}
						}
						else {
							$image = '';
						}		
						$status = 0;
						$paystatus = true;
						//$status = ($_SESSION["microchip_type"]=='tailtracking')?1:0;
						//$paystatus = ($_SESSION["microchip_type"]=='tailtracking')?false:true;
						
						$q_pet = "INSERT INTO `pets` ( `pet_name`, `species_id`, `owner_id`, `microchip_no`, `breed_type`, `gender`, `dob`, `arv_date`, `primary_color`, `special_marking`, `description`, `image`, `added_on`, `status`) VALUES ('".$data['pet_name']."', '".$data['species_id']."', '".$query_user."', '".$_SESSION["microchip"]."', '".$data['breed_type']."', '".$data['gender']."', '".$data['dob']."', '".$data['arvdate']."', '".$data['primary_color']."', '".$special_marking."', '".$description."', '".$image."', NOW(), '".$status."')";
						//echo $q; 
						$query_pet = $db->exec($q_pet);
						if(($query_pet) && ($query_user) && ($chipid)){
							$q_update_chip = "UPDATE `micro_chip` SET `status` = '1', `pet_id` = ".$query_pet.", `owner_id` = ".$query_user." WHERE chip_id = '".$chipid."'";
							//echo $q_update_chip; 
							$query_update_chip = $db->prepare($q_update_chip);
							$query_update_chip->execute();
						}
						unset ($_SESSION["step1"]);
						unset ($_SESSION["step2"]);
						unset ($_SESSION["step3"]);
						unset ($_SESSION["customer"]);
			
						$response = array('sucess' => true, 'message' => 'You have successfully created your account. <br/> Please login in to you account.', 'petid' => $query_pet, 'userid' => $query_user, 'paystatus' => $paystatus);
	}else{
			//$_SESSION['step2'] = 0;
			$response = array('sucess' => false, 'message' => 'There is some problem to store data. Please try again');
	}
	
	echoResponse(200, $response);
});
/******************************************
Purpose: Create Pet for Logged in Users
Parameter : NIL
Type : POST
******************************************/
$app->post('/petcreatestepuser', function () use ($app) { 
    global $db;
	global $imageClass;
	$data = json_decode($app->request->getBody(), true);
	$create_pet = 0;	
	$chipnumber = $data['chipnumber'];
		if($data['chiptype']==1){
		$rows = $db->select("micro_chip","chip_id, chip_type, status, pet_id, owner_id, added_on", array('chip_code' => $chipnumber), "");
		
		if(count($rows['data'])>0) {
			if($rows['data'][0]['status'] == 1) {
				if(($rows['data'][0]['pet_id']) && ($rows['data'][0]['pet_id'])){
					$response = array('success' => false, 'message' => 'This Microchip you entered is already registered with some pet');
				} 
				else{
					$create_pet = 1;
					$_SESSION["microchip"]= $chipnumber;
					$_SESSION["microchip_type"]= 'tailtracking';
				}
			}
			else {
				$response = array('success' => false, 'message' => 'The Microchip you entered is not activated yet. check and try again');
			}
		}
		else {
			$response['success'] = false;
			$response['message'] = 'The Microchip you entered did not match with our database. Please check and try again.';
		}
	}
	else{
			$create_pet = 1;
			$_SESSION["microchip"]= $chipnumber;
			$_SESSION["microchip_type"]= ($data['chiptype']==2)?'KCI':'other';
	}
	
	if($create_pet ==1 ){
		$currentuser = $_SESSION['login_userid'];
		if($_SESSION['pet'] = $data){
				if($_SESSION["microchip_type"]!='tailtracking'){
					$pet_id = "NULL";
					$owner_id =  "NULL";
	
					$q_chip = "INSERT INTO `micro_chip` ( `chip_code`, `chip_type`, `status`, `pet_id`, `owner_id`, `added_on`) VALUES ('".$_SESSION["microchip"]."', '".$_SESSION["microchip_type"]."', '1', ".$pet_id.", ".$owner_id.", NOW())";
					$query_chip = $db->exec($q_chip);
					if($query_chip){
						$chipid = $query_chip;
		
					}
				}
				else{
					
					$rowschip = $db->select("micro_chip","chip_id, chip_type, status", array('chip_code' => $_SESSION["microchip"]), "");
			
					if(count($rowschip['data'])>0) {
						$chipid = $rowschip['data'][0]['chip_id'];
					}
				}
							if(isset($data['special_marking'])) $special_marking = $data['special_marking'];else $special_marking = '';
							if(isset($data['description'])) $description = $data['description'];else $description = '';
							if(isset($data['image'])) 
							{
								$upload_path = BASE_PATH.'/images/pet/';
								$upload_url = BASE_URL.'/images/pet/';
								
								$profilePicture = explode(',', $data['image']);
								$pic_type = explode('/', $profilePicture[0]);
								$type = explode(';', $pic_type[1]);
								$rnd = time();
								$fileName = $rnd.'.'.$type[0]; // random number
								
								if(file_put_contents($upload_path.$fileName, base64_decode($profilePicture[1]))) { 
									$originalpath = $upload_path.$fileName;
									$target_file = $upload_path.'thumb_'.$fileName;
									$imageClass->load($originalpath);
									$imageClass->resizeToHeight(200);
									$imageClass->save($target_file);
									
									$image = $upload_url.$fileName;
									$image_thumb = $target_file;
								}
								else {
									$image = '';
								}
							}	
							else {
								$image = '';
							}
							
							$status = ($data['chiptype']==1)?1:0;
							$paystatus = ($data['chiptype']==1)?false:true;
							
							$q_pet = "INSERT INTO `pets` ( `pet_name`, `species_id`, `owner_id`, `microchip_no`, `breed_type`, `gender`, `dob`, `arv_date`, `primary_color`, `special_marking`, `description`, `image`, `added_on`, `status`) VALUES ('".$data['pet_name']."', '".$data['species_id']."', '".$currentuser."', '".$_SESSION["microchip"]."', '".$data['breed_type']."', '".$data['gender']."', '".$data['dob']."', '".$data['arvdate']."', '".$data['primary_color']."', '".$special_marking."', '".$description."', '".$image."', NOW(), '".$status."')";
							//echo $q; 
							$query_pet = $db->exec($q_pet);
	
							if(($query_pet) && ($currentuser) && ($chipid)){
								$q_update_chip = "UPDATE `micro_chip` SET `status` = '1', `pet_id` = ".$query_pet.", `owner_id` = ".$currentuser." WHERE chip_id = '".$chipid."'";
								//echo $q_update_chip; 
								$query_update_chip = $db->prepare($q_update_chip);
								$query_update_chip->execute();
							}
							
							$response = array('sucess' => true, 'message' => 'You have successfully registered your pet. <br/> Please get in to Payment gateway.', 'petid' => $query_pet, 'paystatus' => $paystatus);
		}else{
				//$_SESSION['step2'] = 0;
				$response = array('sucess' => false, 'message' => 'There is some problem to store data. Please try again');
		}
	}
	
	echoResponse(200, $response);
});
/******************************************
Purpose: Update Pet details for Logged in Users
Parameter : NIL
Type : POST
******************************************/
$app->post('/peteditstepuser',function () use ($app) { 
    global $db;
	global $imageClass;
	$data = json_decode($app->request->getBody(), true);
	$create_pet = 0;	
	
		$currentuser = $_SESSION['login_userid'];
							if(isset($data['special_marking'])) $special_marking = $data['special_marking'];else $special_marking = '';
							if(isset($data['description'])) $description = $data['description'];else $description = '';
							if(isset($data['image_new'])) 
							{
								$upload_path = BASE_PATH.'/images/pet/';
								$upload_url = BASE_URL.'/images/pet/';
								
								$profilePicture = explode(',', $data['image_new']);
								$pic_type = explode('/', $profilePicture[0]);
								$type = explode(';', $pic_type[1]);
								$rnd = time();
								$fileName = $rnd.'.'.$type[0]; // random number
								
								if(file_put_contents($upload_path.$fileName, base64_decode($profilePicture[1]))) { 
									
									$image = $upload_url.$fileName;
									$imageqry = "`image` ='".$image."', ";
								}
								else {
									$imageqry = '';
								}
							}	
							else {
								$imageqry = '';
							}
							
							$q_pet_update = "UPDATE `pets` SET `pet_name`='".$data['pet_name']."', `species_id`='".$data['species_id']."', `owner_id`='".$currentuser."', `breed_type` = '".$data['breed_type']."', `gender` = '".$data['gender']."', `dob` = '".$data['dob']."', `arv_date`= '".$data['arvdate']."', `primary_color`= '".$data['primary_color']."', `special_marking`= '".$special_marking."', `description`= '".$description."', ".$imageqry."`updated_on` = NOW() WHERE pet_id='".$data['pet_id']."'"; 
							//echo $q; 
							$query_update = $db->prepare($q_pet_update);
							if($query_update->execute()){
							$response = array('sucess' => true, 'message' => 'Pet details updated successfully.');
							}else{
									$response = array('sucess' => false, 'message' => 'There is some problem to update data. Please try again');
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
Purpose: Get list of all Pets by user 
Parameter : NULL
Type : GET
******************************************/
$app->get('/mypetdetails', function () use ($app) { 
    global $db;
	$limit =  $app->request->get('limit');
	$order = "ORDER BY pet_id DESC";
	if($limit!='') { $order.=" Limit 0, ".$limit; }
	
	$pets = array();
	
	$pet_qry = $db->prepare("select * from pets WHERE status = '1' AND paid_status='1' AND owner_id='".$_SESSION['login_userid']."' ".$order);
	$pet_qry->execute();
	$petArray = $pet_qry->fetchAll(PDO::FETCH_ASSOC);
	//print_r($lostpetArray);
	$i = 1;
	if(count($petArray)>0){
		foreach($petArray as $pet) {
			
			$species_qry = $db->prepare("select * from species where type_id='".$pet['species_id']."'");
			$species_qry->execute();
			$speciesArray = $species_qry->fetchAll(PDO::FETCH_ASSOC);
			if(count($speciesArray)>0){
				$sepciesName = $speciesArray[0]['type_name'];
			}else{
				$sepciesName = '';
			}
			$breed_qry = $db->prepare("select * from breed_type where breed_id='".$pet['breed_type']."'");
			$breed_qry->execute();
			$breedArray = $breed_qry->fetchAll(PDO::FETCH_ASSOC);
			if(count($breedArray)>0){
				$breedName = $breedArray[0]['breed_name'];
			}else{
				$breedName = '';
			}
			//print_r($lostpet); 
			$petstaus = ($pet['status'] == 1) ? 'glyphicon glyphicon-ok-sign green' : 'glyphicon glyphicon-ok-sign red';
			$pets[] = array('slno' => $i,
								'pet_id' => $pet['pet_id'],
								'species' =>$sepciesName,
							  	'pet_name' =>$pet['pet_name'],
							  	'microchip_no' =>$pet['microchip_no'],
							  	'image' =>$pet['image'],	
							  	'breed' =>$breedName,
							  	'primary_color' =>$pet['primary_color'],
							  	'gender' =>$pet['gender'],
							  	'dob' =>date("d-m-Y",strtotime($pet['dob'])),	
							  	'arv_date' =>date("d-m-Y",strtotime($pet['arv_date'])),	
							  	'status' => $petstaus,
								'microchip_no' =>	$pet['microchip_no'],
								'special_marking'	=>	$pet['special_marking'],
								'description'	=>	$pet['description'],
								'added_on'	=>	date("d-m-Y",strtotime($pet['added_on'])),
							);
			$i++;				
		}
	}
	
	echoResponse(200, $pets);
});
/******************************************
Purpose: Get list of all Pets list by user for displaying in REgister lost per page
Parameter : NULL
Type : GET
******************************************/
$app->get('/mypetlistlost', function () use ($app) { 
    global $db;
	$limit =  $app->request->get('limit');
	$order = "ORDER BY pet_id DESC";
	if($limit!='') { $order.=" Limit 0, ".$limit; }
	$lostpetsArray = array();
	$pets = array();
	
	$lostpets = $db->prepare("select pet_id from lost_pets where owner_id='".$_SESSION['login_userid']."' and pet_id > '0'");
	$lostpets->execute();
	$lostArray = $lostpets->fetchAll(PDO::FETCH_ASSOC);
	
	if(count($lostArray) > 0){
		foreach($lostArray as $lostid){
		//print_r($lostid['pet_id']);
		$lostpetsArray[] = $lostid['pet_id'];
		}
		$lostpetslist = implode(',',$lostpetsArray);
		$not_in = " AND pet_id NOT IN (".$lostpetslist.") ";
	}else{
		$not_in = " ";
	}
	
	$pet_qry = $db->prepare("select * from pets WHERE status = '1' AND paid_status='1' AND owner_id='".$_SESSION['login_userid']."' ".$not_in.$order);
	$pet_qry->execute();
	$petArray = $pet_qry->fetchAll(PDO::FETCH_ASSOC);
	//print_r($petArray);
	//print_r($lostpetArray);
	$i = 1;
	if(count($petArray)>0){
		foreach($petArray as $pet) {
			
			$petstaus = ($pet['status'] == 1) ? 'glyphicon glyphicon-ok-sign green' : 'glyphicon glyphicon-ok-sign red';
			$pets[] = array(	'pet_id' => $pet['pet_id'],
							  	'pet_name' =>$pet['pet_name'],
							);
			$i++;				
		}
	}
	
	echoResponse(200, $pets);
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
Purpose: Get total count of customer, tshirt and tryout. 
Parameter : 
Type : GET
******************************************/
$app->get('/totalrec', function() { 
    global $db;
    $users = $db->select("users", "*", array(), "");
	$foundpetscount = $db->select("found_pets","*", array(), "");
	$lostpetscount = $db->select("lost_pets","*", array(), "");
	$petscount = $db->select("pets","*", array(), "");
	
	$totalrec = array('usertotal'=> count($users['data']), 'foundpetscount' => count($foundpetscount['data']), 'lostpetscount' => count($lostpetscount['data']), 'petscount' => count($petscount['data']));
	//print_r($totalrec);
	
	echoResponse(200, $totalrec);
});
/******************************************
Purpose: Get all customer details
Parameter : 
Type : GET
******************************************/
$app->get('/customer', function () use ($app) { 
    global $db;
	$limit =  $app->request->get('limit');
	$order = "ORDER BY user_id DESC";
	if($limit!='') { $order.=" Limit 0, ".$limit; }
    $rows = $db->select("users","user_id, fname,lname,email,gender,city,region, status", array(), $order);
	$customer = array();
	
    foreach($rows['data'] as $row) {
		if($row['status'] == 1) {
			$status_short = 'glyphicon glyphicon-user green';
			$status = 'glyphicon glyphicon-user green';
		}
		else{
			$status_short = 'glyphicon glyphicon-user red';
			$status = 'glyphicon glyphicon-user red';
		}
		$state_qry = $db->prepare("select * from states where id='".$row['region']."'");
		$state_qry->execute();
		$stateArray = $state_qry->fetchAll(PDO::FETCH_ASSOC);
		if(count($stateArray)>0){
			$stateName = $stateArray[0]['name'];
		}
		else $stateName = 'Unknown';
		$customer[] = array('id' => $row['user_id'],
						  'name' => $row['fname'].' '.$row['lname'],
						  'email' => $row['email'],	
						  'city' => $row['city'],	
						  'region' => $stateName,	
						  'status' => $status,
						  'status_short' => $status_short,
						);
	}
	//print_r($customer);exit;
	echoResponse(200, $customer);
});
/******************************************
Purpose: Check email exists for customer
Parameter : email
Type : GET
******************************************/
$app->get('/checkemailexists',function () use ($app) { 
    global $db;
	$email =  $app->request->get('email');
	
	$query = $db->prepare("SELECT * FROM users WHERE email='".$email."'"); 
	$query->execute();
    $rows = $query->fetchAll(PDO::FETCH_ASSOC);
	//print_r($rows);die;
	if((isset($rows)) && count($rows)> 0){
		
		$customer = array('customer' => $email);
	}
	else{
		$customer ='';
		}
//	
	
	echoResponse(200, $customer);
});
/******************************************
Purpose: Check mobile exists for customer
Parameter : mobile
Type : GET
******************************************/
$app->get('/checkmobileexists',function () use ($app) { 
    global $db;
	$mobile =  $app->request->get('mobile');
	
	$query = $db->prepare("SELECT * FROM users WHERE phone_1='".$mobile."'"); 
	$query->execute();
    $rows = $query->fetchAll(PDO::FETCH_ASSOC);
	//print_r($rows);die;
	if((isset($rows)) && count($rows)> 0){
		
		$customer = array('customer' => $mobile);
	}
	else{
		$customer ='';
		}
//	
	
	echoResponse(200, $customer);
});
/******************************************
Purpose: Check microchip exists for customer
Parameter : microchip
Type : GET
******************************************/
$app->get('/checkmicrochipexists',function () use ($app) { 
    global $db;
	$microchip =  $app->request->get('microchip');
	
	$query = $db->prepare("SELECT * FROM pets WHERE microchip_no='".$microchip."' and status='1'"); 
	$query->execute();
    $rows = $query->fetchAll(PDO::FETCH_ASSOC);
	//print_r($rows);die;
	if((isset($rows)) && count($rows)> 0){
		
		$customer = array('customer' => $microchip);
	}
	else{
		$customer ='';
		}
//	
	
	echoResponse(200, $customer);
});
/******************************************
Purpose: Get all customer details by ID
Parameter : id
Type : GET
******************************************/
$app->get('/getprofiledetails',function () use ($app) { 
    global $db;
	$user_id = $_SESSION['login_userid'];
	
	$query = $db->prepare("SELECT * FROM users WHERE user_id='".$user_id."' "); 
	$query->execute();
    $rows = $query->fetchAll(PDO::FETCH_ASSOC);
	//print_r($rows);
	
	$customer = array('customer' => $rows[0]);
	
	echoResponse(200, $customer);
});
/******************************************
Purpose: Get all customer details by ID
Parameter : id
Type : GET
******************************************/
$app->get('/customerbyid',function () use ($app) { 
    global $db;
	$user_id =  $app->request->get('id');
	
	$query = $db->prepare("SELECT * FROM users WHERE user_id='".$user_id."' "); 
	$query->execute();
    $rows = $query->fetchAll(PDO::FETCH_ASSOC);
	//print_r($rows);
	
	$customer = array('customer' => $rows[0]);
	
	echoResponse(200, $customer);
});
/******************************************
Purpose: Create customer details through ID
Parameter : form field
Type : POST
******************************************/
$app->post('/customercreate',function () use ($app) { 
    global $db;
	$data = json_decode($app->request->getBody(), true);
	$response = array();
	
	//print_r($data);
	if(isset($data['address_2'])) $address_2 = $data['address_2'];else$address_2 = '';
	if(isset($data['phone_2'])) $phone_2 = $data['phone_2'];else$phone_2 = '';
	$q = "INSERT INTO `users` ( `fname`, `lname`, `email`, `password`, `gender`, `address_1`, `address_2`, `city`, `post`, `country`, `region`, `phone_1`, `phone_2`, `added_on`, `updated_on`) VALUES ('".$data['fname']."', '".$data['lname']."', '".$data['email']."', '".md5($data['password'])."', '".$data['gender']."', '".$data['address_1']."', '".$address_2."', '".$data['city']."', '".$data['post']."', '".$data['country']."', '".$data['region']."', '".$data['phone_1']."', '".$phone_2."', NOW(), '')";
	//echo $q; 
	$query = $db->prepare($q);
	$query->execute();
	
	
	if($data['submittype']=='create') {
			$response = array('sucess' => true, 'message' => 'Customer created Successfully !', 'type' => $data['submittype']);
	}
	
	echoResponse(200, $response);
});
/******************************************
Purpose: Update customer details through ID
Parameter : form field
Type : POST
******************************************/
$app->post('/customerupdate',function () use ($app) { 
    global $db;
	$data = json_decode($app->request->getBody(), true);
	
	//print_r($data);
	if(isset($data['address_2'])) $address_2 = $data['address_2'];else$address_2 = '';
	if(isset($data['phone_2'])) $phone_2 = $data['phone_2'];else$phone_2 = '';
	
	$q = "UPDATE `users` SET `fname`='".$data['fname']."', `lname`='".$data['lname']."',`gender`='".$data['gender']."',`address_1`='".$data['address_1']."',`address_2`='".$address_2."',`city`='".$data['city']."',`post`='".$data['post']."',`country`='".$data['country']."',`region`='".$data['region']."',`phone_1`='".$data['phone_1']."',`phone_2`='".$phone_2."',`status`='".$data['status']."' 
	WHERE `user_id`='".$data['user_id']."'";
	
	$query = $db->prepare($q);
	$query->execute();
	
	$response = array();
	if($data['submittype']=='save') {
			$response = array('sucess' => true, 'message' => 'Customer info updated Sucessfully !', 'type' => $data['submittype']);
	}
	echoResponse(200, $response);
});
/******************************************
Purpose: Update customer details through ID
Parameter : form field
Type : POST
******************************************/
$app->post('/updateprofile',function () use ($app) { 
    global $db;
	$data = json_decode($app->request->getBody(), true);
	$user_id = $_SESSION['login_userid'];
	//print_r($data);
	if(isset($data['address_2'])) $address_2 = $data['address_2'];else$address_2 = '';
	if(isset($data['phone_2'])) $phone_2 = $data['phone_2'];else$phone_2 = '';
	if(isset($data['password'])){
		$newpassword = md5($data['password']);
		$updatepwd = " `password` = '".$newpassword."', ";
	}
	else{
		$updatepwd = "";
	}
	
	$q = "UPDATE `users` SET `fname`='".$data['fname']."', `lname`='".$data['lname']."',".$updatepwd." `gender`='".$data['gender']."',`address_1`='".$data['address_1']."',`address_2`='".$address_2."',`city`='".$data['city']."',`post`='".$data['post']."',`country`='".$data['country']."',`region`='".$data['region']."',`phone_1`='".$data['phone_1']."',`phone_2`='".$phone_2."',`status`='".$data['status']."' 
	WHERE `user_id`='".$user_id."'";
	
	$query = $db->prepare($q);
	$query->execute();
	
	$response = array();
	if($data['submittype']=='save') {
			$response = array('sucess' => true, 'message' => 'Customer info updated Sucessfully !', 'type' => $data['submittype']);
	}
	echoResponse(200, $response);
});
/******************************************
Purpose: Get all Pets details
Parameter : 
Type : GET
******************************************/
$app->get('/pets', function () use ($app) { 
    global $db;
	$limit =  $app->request->get('limit');
	$order = "ORDER BY pet_id DESC";
	if($limit!='') { $order.=" Limit 0, ".$limit; }
    $rows = $db->select("pets","*", array(), $order);
	$customer = array();
	
	$i = 1;
    foreach($rows['data'] as $row) {
	
		$species_qry = $db->prepare("select * from species where type_id='".$row['species_id']."'");
		$species_qry->execute();
		$speciesArray = $species_qry->fetchAll(PDO::FETCH_ASSOC);
		if(count($speciesArray)>0){
			$sepciesName = $speciesArray[0]['type_name'];
		}else{
			$sepciesName = '';
		}
		$user_qry = $db->prepare("select * from users where user_id='".$row['owner_id']."'");
		$user_qry->execute();
		$userArray = $user_qry->fetchAll(PDO::FETCH_ASSOC);
		if(count($userArray)>0){
			$userName = $userArray[0]['fname']." ".$userArray[0]['lname'];
		}else{
			$userName = '';
		}
		if($row['status'] == 1){
			$status = 'glyphicon glyphicon-ok-sign green';
			$status_short = 'glyphicon glyphicon-ok-sign green' ;
		}else{
			$status = 'glyphicon glyphicon-ok-sign red';
			$status_short = 'glyphicon glyphicon-ok-sign red';
		} 
		$pets[] = array('id' => $i,
						  'pet_id' => $row['pet_id'],
						  'species' => $sepciesName,
						  'pet_name' => $row['pet_name'],	
						  'owner_name' => $userName,
						  'chip_no' => $row['microchip_no'],	
						  'status' => $status,
						  'status_short' => $status_short,
						);
		$i++;				
	}
	
	echoResponse(200, $pets);
});
/******************************************
Purpose: Get all Pets details
Parameter : 
Type : GET
******************************************/
$app->get('/petwithoutchip', function () use ($app) { 
    global $db;
	$petnochip =  array();
	$query_species = $db->prepare("SELECT p.pet_id, p.pet_name, u.fname, u.lname FROM pets p, users u WHERE u.user_id= p.owner_id AND p.microchip_no =''"); 
	$query_species->execute();
	$rows_species = $query_species->fetchAll(PDO::FETCH_ASSOC);
	
    foreach($rows_species as $row) {
		$petnochip[] = array('petid' => $row['pet_id'],
						  'petname' => $row['pet_name']." --- ". $row['fname']." ".$row['lname']
						);
	}
	echoResponse(200, $petnochip);
});
$app->get('/getpetwithoutchip', function () use ($app) { 
    global $db;
	$chipid =  $app->request->get('id');
	$petnochip =  array();
	$query_species = $db->prepare("SELECT p.pet_id, p.pet_name, u.fname, u.lname FROM pets p, users u, micro_chip mc WHERE ((u.user_id= mc.owner_id AND p.microchip_no = mc.chip_code AND mc.chip_id ='".$chipid."') OR (u.user_id= p.owner_id AND p.microchip_no =''))"  ); 
	$query_species->execute();
	$rows_species = $query_species->fetchAll(PDO::FETCH_ASSOC);
	
    foreach($rows_species as $row) {
		$petnochip[] = array('petid' => $row['pet_id'],
						  'petname' => $row['pet_name']." --- ". $row['fname']." ".$row['lname']
						);
	}
	echoResponse(200, $petnochip);
});
/******************************************
Purpose: Get Pet details by ID
Parameter : id
Type : GET
******************************************/
$app->get('/petbyid',function () use ($app) { 
    global $db;
	$pet_id =  $app->request->get('id');
				
	$query = $db->prepare("SELECT * FROM pets WHERE pet_id='".$pet_id."' "); 
	$query->execute();
    $rows = $query->fetchAll(PDO::FETCH_ASSOC);
	$pet = $rows[0];
	
	echoResponse(200, $pet);
});
$app->get('/petbyidall',function () use ($app) { 
    global $db;
	$pet_id =  $app->request->get('id');
				
	$query = $db->prepare("SELECT * FROM pets WHERE pet_id='".$pet_id."' "); 
	$query->execute();
    $rows = $query->fetchAll(PDO::FETCH_ASSOC);
	$pet = $rows[0];
	if($rows[0]['microchip_no']){
			$querychip = $db->prepare("SELECT * FROM micro_chip WHERE chip_code='".$rows[0]['microchip_no']."' "); 
			$querychip->execute();
			$rowschip = $querychip->fetchAll(PDO::FETCH_ASSOC);
			if(count($rowschip) > 0){
			$chip = $rowschip[0];
			$pet['purchased_from'] =  $chip['chip_type'];
			}
			else{$pet['purchased_from'] = 'Nil';}
	}
	else{$pet['purchased_from'] = 'Nil';}
	echoResponse(200, $pet);
});
/******************************************
Purpose: Create Pet details
Parameter : form field
Type : POST
******************************************/
$app->post('/petcreate',function () use ($app) { 
    global $db;
	$data = json_decode($app->request->getBody(), true);
	$response = array();
	
	//print_r($data);
	if(isset($data['special_marking'])) $special_marking = $data['special_marking'];else $special_marking = '';
	if(isset($data['description'])) $description = $data['description'];else $description = '';
	if(isset($data['image'])) $image = $data['image'];else $image = '';
	$q = "INSERT INTO `pets` ( `pet_name`, `species_id`, `owner_id`, `microchip_no`, `breed_type`, `gender`, `primary_color`, `special_marking`, `description`, `image`, `added_on`, `status`) VALUES ('".$data['pet_name']."', '".$data['species_id']."', '".$data['owner_id']."', '".$data['microchip_no']."', '".$data['breed_type']."', '".$data['gender']."', '".$data['primary_color']."', '".$special_marking."', '".$description."', '".$image."', NOW(), '".$data['status']."')";
	//echo $q; 
	$query = $db->prepare($q);
	$query->execute();
	
	
	if($data['submittype']=='create') {
			$response = array('sucess' => true, 'message' => 'Customer created Successfully !', 'type' => $data['submittype']);
	}
	
	echoResponse(200, $response);
});
/******************************************
Purpose: Update Pet details by ID
Parameter : form field
Type : POST
******************************************/
$app->post('/petupdate',function () use ($app) { 
    global $db;
	$data = json_decode($app->request->getBody(), true);
	
	//print_r($data);
	
	$q = "UPDATE `pets` SET `pet_name` = '".$data['pet_name']."', `species_id` == '".$data['species_id']."', `owner_id` = '".$data['owner_id']."' `microchip_no` = '".$data['microchip_no']."', `breed_type` = '".$data['breed_type']."', `gender` = '".$data['gender']."', `primary_color` = '".$data['primary_color']."', `special_marking` = '".$data['special_marking']."', `description` = '".$data['description']."', `image` = '".$data['image']."', `updated_on` = now() WHERE `pet_id` = '".$data['pet_id']."';";
	
	//echo $q; 
	$query = $db->prepare($q);
	$query->execute();
	
	$response = array();
	$response = array('sucess' => true, 'message' => 'Pet info updated Sucessfully !', 'type' => $data['submittype']);
	echoResponse(200, $response);
});
/******************************************
Purpose: Get all Chips details
Parameter : 
Type : GET
******************************************/
$app->get('/chips', function () use ($app) { 
    global $db;
	$limit =  $app->request->get('limit');
	$order = "ORDER BY chip_id DESC";
	if($limit!='') { $order.=" Limit 0, ".$limit; }
    $rows = $db->select("micro_chip","*", array(), $order);
	$customer = array();
		$chips = array();
	
	$i = 1;
    foreach($rows['data'] as $row) {
	
		$pets_qry = $db->prepare("select * from pets where pet_id='".$row['pet_id']."'");
		$pets_qry->execute();
		$petsArray = $pets_qry->fetchAll(PDO::FETCH_ASSOC);
		if(count($petsArray)>0){
			$petName = $petsArray[0]['pet_name'];
		}else{
			$petName = '';
		}
		$user_qry = $db->prepare("select * from users where user_id='".$row['owner_id']."'");
		$user_qry->execute();
		$userArray = $user_qry->fetchAll(PDO::FETCH_ASSOC);
		if(count($userArray)>0){
			$userName = $userArray[0]['fname']." ".$userArray[0]['lname'];
		}else{
			$userName = '';
		}
		if($row['status'] == 1){
			$status = 'Active';
			$status_short = 'glyphicon glyphicon-ok-sign green' ;
		}else{
			$status = 'Inactive';
			$status_short = 'glyphicon glyphicon-ok-sign red';
		} 
		$chips[] = array('id' => $i,
						  'chip_id' => $row['chip_id'],
						  'chip_code' => $row['chip_code'],
						  'pet_name' => $petName,	
						  'owner_name' => $userName,
						  'status' => $status,
						  'status_short' => $status_short,
						);
		$i++;				
	}
	
	echoResponse(200, $chips);
});
/******************************************
Purpose: Create New chip details
Parameter : form field
Type : POST
******************************************/
$app->post('/chipcreate',function () use ($app) { 
    global $db;
	$data = json_decode($app->request->getBody(), true);
	$response = array();
	$petid = '';
	//print_r($data);
	if(isset($data['pet_id'])) { 
		$petid = $data['pet_id'];
		$pet_id = $owner_id = "'".$data['pet_id']."'";;
		$query_cus = $db->prepare("SELECT * FROM pets WHERE pet_id='".$data['pet_id']."' "); 
		$query_cus->execute();
		$rowcus = $query_cus->fetchAll(PDO::FETCH_ASSOC);
		if(count($rowcus) > 0){
			$owner_id = "'".$rowcus[0]['owner_id']."'";
		}else {
			$owner_id =  "NULL";
		}
	}else {
		$pet_id = "NULL";
		$owner_id =  "NULL";
	}
	
	$q = "INSERT INTO `micro_chip` ( `chip_code`, `chip_type`, `status`, `pet_id`, `owner_id`, `added_on`) VALUES ('".$data['chip_code']."', '".$data['chip_type']."', '".$data['status']."', ".$pet_id.", ".$owner_id.", NOW())";
	//echo $q; 
	$query = $db->prepare($q);
	//$query->execute();
	if($query->execute()){
		if($petid){
			$q_update = "UPDATE pets SET microchip_no = '".$data['chip_code']."' WHERE pet_id='".$petid."'";
			$query_update = $db->prepare($q_update);
			$query_update->execute();
		}
		if($data['submittype']=='Create') {
				$response = array('sucess' => true, 'message' => 'Micro chip added Successfully !', 'type' => $data['submittype']);
		}
	}
	else{
				$response = array('sucess' => true, 'message' => 'Micro chip added Successfully !', 'type' => $data['submittype']);
		
	}
	
	
	echoResponse(200, $response);
});
/******************************************
Purpose: Update chip details
Parameter : form field
Type : POST
******************************************/
$app->post('/chipupdate',function () use ($app) { 
    global $db;
	$data = json_decode($app->request->getBody(), true);
	$response = array();
	$petid ='';
	//print_r($data);
	if(isset($data['pet_id'])) { 
		$petid = $data['pet_id'];
		$pet_id = $owner_id = "'".$data['pet_id']."'";;
		$query_cus = $db->prepare("SELECT * FROM pets WHERE pet_id='".$data['pet_id']."' "); 
		$query_cus->execute();
		$rowcus = $query_cus->fetchAll(PDO::FETCH_ASSOC);
		if($rowcus){
			$owner_id = "'".$rowcus[0]['owner_id']."'";
		}else {
			$owner_id =  "NULL";
		}
	}else {
		$pet_id = "NULL";
		$owner_id =  "NULL";
	}
	
	$q = "UPDATE `micro_chip` SET `chip_code` ='".$data['chip_code']."', `chip_type` ='".$data['chip_type']."' , `status` = '".$data['status']."', `pet_id` = ".$pet_id.", `owner_id` = ".$owner_id." WHERE chip_id = '".$data['chip_id']."'";
	//echo $q; 
	$query = $db->prepare($q);
	//$query->execute();
	if($query->execute()){
		if($petid){
			$q_update = "UPDATE pets SET microchip_no = '".$data['chip_code']."' WHERE pet_id='".$petid."'";
			$query_update = $db->prepare($q_update);
			$query_update->execute();
		}
		else{
			$q_update = "UPDATE pets SET microchip_no = '' WHERE microchip_no='".$data['chip_code']."'";
			$query_update = $db->prepare($q_update);
			$query_update->execute();
		}
	}
	
	if($data['submittype']=='Update') {
			$response = array('sucess' => true, 'message' => 'Micro chip updated Successfully !', 'type' => $data['submittype']);
	}
	
	echoResponse(200, $response);
});
/******************************************
Purpose: Get Chip details by ID
Parameter : form field
Type : POST
******************************************/
$app->get('/chipbyid', function () use ($app) { 
    global $db;
	$chip_id =  $app->request->get('id');
	$chip = array();			
	$query = $db->prepare("SELECT * FROM micro_chip WHERE chip_id='".$chip_id."' "); 
	$query->execute();
    $rows = $query->fetchAll(PDO::FETCH_ASSOC);
	$chip = $rows[0];
	
	echoResponse(200, $chip);
});
/******************************************
Purpose: Get all Breeds details
Parameter : 
Type : GET
******************************************/
$app->get('/breeds', function () use ($app) { 
    global $db;
	$limit =  $app->request->get('limit');
	$order = "ORDER BY breed_name ASC";
	if($limit!='') { $order.=" Limit 0, ".$limit; }
    $rows = $db->select("breed_type","*", array(), $order);
	$customer = array();
	$breeds = array();
	
	$i = 1;
    foreach($rows['data'] as $row) {
	
	
		$species_qry = $db->prepare("select * from species where type_id='".$row['species_id']."'");
		$species_qry->execute();
		$speciesArray = $species_qry->fetchAll(PDO::FETCH_ASSOC);
		if(count($speciesArray)>0){
			$speciesName = $speciesArray[0]['type_name'];
		}else{
			$speciesName = '--';
		}
			$status_short = 'spec'.$row['status'].$row['species_id'] ;
		if($row['status'] == 1){
			$status = 'Active';
		}else{
			$status = 'Inactive';
		} 
		$breeds[] = array('id' => $i,
						  'breed_name' => $row['breed_name'],
						  'breed_id' => $row['breed_id'],
						  'speciesname' => $speciesName,	
						  'status' => $status,
						  'status_short' => $status_short,
						);
		$i++;				
	}
	
	echoResponse(200, $breeds);
});
/******************************************
Purpose: Get Breed type details by ID
Parameter : form field
Type : POST
******************************************/
$app->get('/breedbyid', function () use ($app) { 
    global $db;
	$breed_id =  $app->request->get('id');
	$breed = array();			
	$query = $db->prepare("SELECT * FROM breed_type WHERE breed_id='".$breed_id."' "); 
	$query->execute();
    $rows = $query->fetchAll(PDO::FETCH_ASSOC);
	$breed = $rows[0];
	
	echoResponse(200, $breed);
});
/******************************************
Purpose: Update Breed type details
Parameter : form field
Type : POST
******************************************/
$app->post('/breedupdate',function () use ($app) { 
    global $db;
	$data = json_decode($app->request->getBody(), true);
	$response = array();
	//print_r($data);
	
	$q = "UPDATE `breed_type` SET `breed_name` ='".$data['breed_name']."', `species_id` ='".$data['species_id']."' , `status` = '".$data['status']."' WHERE breed_id = '".$data['breed_id']."'";
	//echo $q; 
	$query = $db->prepare($q);
	//$query->execute();
	if($query->execute()){
			$response = array('sucess' => true, 'message' => 'Breed type updated Successfully !', 'type' => $data['submittype']);
	}
	echoResponse(200, $response);
});
/******************************************
Purpose: Create New Breed type details
Parameter : form field
Type : POST
******************************************/
$app->post('/breedcreate',function () use ($app) { 
    global $db;
	$data = json_decode($app->request->getBody(), true);
	$response = array();
	
	$q = "INSERT INTO `breed_type` ( `breed_name`, `species_id`, `status`) VALUES ('".$data['breed_name']."', '".$data['species_id']."', '".$data['status']."')";
	//echo $q; 
	$query = $db->prepare($q);
	//$query->execute();
	if($query->execute()){
				$response = array('sucess' => true, 'message' => 'Breed Type added Successfully !', 'type' => $data['submittype']);
	}
	else{
				$response = array('sucess' => false, 'message' => 'There is some problem. Try later !', 'type' => $data['submittype']);
		
	}
	
	
	echoResponse(200, $response);
});
$app->get('/tryoutbyid', function () use ($app) { 
    global $db;
	$user_id =  $app->request->get('id');
				
	$query = $db->prepare("SELECT * FROM ad_tryout_detail WHERE player_id='".$user_id."' "); 
	$query->execute();
    $rows = $query->fetchAll(PDO::FETCH_ASSOC);
	$tryout = $rows[0];
	
	echoResponse(200, $tryout);
});
$app->post('/tryoutupdate',function () use ($app) { 
    global $db;
	$data = json_decode($app->request->getBody(), true);
	
	//print_r($data);
	
	$q = "UPDATE `ad_tryout_detail` SET `division`='".$data['division']."', `name`='".$data['name']."',`additional_info`='".$data['additional_info']."',`date`='".$data['date']."',`time`='".$data['time']."',`special_request`='".$data['special_request']."',`league_comments`='".$data['league_comments']."',`status`='Completed'
	WHERE `player_id`='".$data['user_id']."'";
	
	//echo $q; 
	$query = $db->prepare($q);
	$query->execute();
	
	//Email send to Accountant and Parents
	$query1 = $db->prepare("SELECT * FROM ad_user WHERE usertype='1'"); 
	$query1->execute();
	$rows1 = $query1->fetchAll(PDO::FETCH_ASSOC);
	//Get parents Email's
	$query2 = $db->prepare("SELECT father_email, mother_email FROM ad_player_parents_info WHERE `player_id`='".$data['user_id']."'"); 
	$query2->execute();
	$rows2 = $query2->fetchAll(PDO::FETCH_ASSOC);
	//echo $rows1[0]['email']; 
	$time = date('H:i', strtotime($data['time']));
	$content = '';
	$content .= '<!DOCTYPE html>';
	$content .= '<html lang="en">';
	$content .= '<head>';
	$content .= '<meta charset="utf-8">';
	$content .= '</head>';
	$content .= '<body style="background:#fff; margin:0; padding:0;">';
	$content .= '<div style="float:left; width:100%;">';
	$content .= '<div style="width:100%; margin:auto">';
	$content .= '<p style="color:#6f6f6f; font-family:Arial, Helvetica, sans-serif; font-size:14px; line-height:18px; margin:0; padding:0 0 15px 0;"><strong>Name :</strong> '.$data['name'].'<br/><br/><strong>Division :</strong> '.$data['division'].'<br/><br/><strong>Date :</strong> '.$data['date'].'<br/><br/><strong>Time :</strong> '.$time.'<br/><br/><strong>Additional info :</strong> '.$data['additional_info'].'<br/><br/>
	</p>';
	$content .= '</div></div></body></html>';
			
	$to = $rows1[0]['email'];
	$subject = "Tryout Confirrmation on TailTracking";
	$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
	$headers .= 'From: TailTracking<noreplay@tailtracking.com>' . "\r\n";
	mail( $to, $subject, $content, $headers ); // Accountant
	mail( $rows2[0]['father_email'], $subject, $content, $headers ); 
	mail( $rows2[0]['mother_email'], $subject, $content, $headers );
			
	$response = array('sucess' => true, 'message' => 'Tryout info updated Sucessfully !');
	echoResponse(200, $response);
});
/******************************************
Purpose: Get all Completed Tryout details
Parameter : 
Type : GET
******************************************/
$app->get('/reportTryout', function() { 
    global $db;
	$query = $db->prepare("SELECT td.*, pi.shirt_size,pi.dob,pi.nationality,pi.email,pi.amount,pi.payment_status , ppi.father_mobile, ppi.father_email, ppi.father_volunteer, ppi.mother_mobile,ppi.mother_email, ppi.mother_volunteer FROM ad_tryout_detail as td JOIN users as pi on td.player_id=pi.user_id JOIN ad_player_parents_info as ppi ON pi.user_id=ppi.player_id WHERE td.status='Completed' ORDER BY tryout_id DESC "); 
	$query->execute();
    $rows = $query->fetchAll(PDO::FETCH_ASSOC);
	
   // $rows = $db->select("ad_tryout_detail","*", array('status' => 'Completed'), "ORDER BY tryout_id DESC");
	$tryout = array();
	
	$i = 1;
    foreach($rows as $row) {
		$status = ($row['payment_status']==0)?'Not Paid':'Paid';
		$tryout[] = array('id' => $i,
						  'name' => $row['name'],
						  'email' => $row['email'],
						  'dob' => $row['dob'],
						  'nationality' => $row['nationality'],
						  'shirt_size' => $row['shirt_size'],
						  'division' => $row['division'],
						  'father_email' => $row['father_email'],
						  'father_mobile' => $row['father_mobile'],
						  'father_volunteer' => $row['father_volunteer'],
						  'mother_email' => $row['mother_email'],
						  'mother_mobile' => $row['mother_mobile'],
						  'mother_volunteer' => $row['mother_volunteer'],
						  'date' => $row['date'],	
						  'time' => date("H:i", strtotime($row['time'])),
						  'additional_info' => $row['additional_info'],
						  'special_request' => $row['special_request'],
						  'league_comments' => $row['league_comments'],
						  'amount' => $row['amount'],
						  'payment_status' => $status,
						);
		$i++;				
	}
	
	echoResponse(200, $tryout);
});
/******************************************
Purpose: Get all Lost pets details
Parameter : 
Type : GET
******************************************/
$app->get('/lostpets', function () use ($app) { 
    global $db;
	$limit =  $app->request->get('limit');
	$order = "ORDER BY lost_pet_id DESC";
	if($limit!='') { $order.=" Limit 0, ".$limit; }
	
    $rows = $db->select("lost_pets","*", array(), $order);
	$lostpets = array();
	
	$i = 1;
    foreach($rows['data'] as $row) {
		$species_qry = $db->prepare("select * from species where type_id='".$row['species_id']."'");
		$species_qry->execute();
		$speciesArray = $species_qry->fetchAll(PDO::FETCH_ASSOC);
		if(count($speciesArray)>0){
			$sepciesName = $speciesArray[0]['type_name'];
		}else{
			$sepciesName = '';
		}
		$petstaus = ($row['status'] == 1) ? 'glyphicon glyphicon-ok-sign green' : 'glyphicon glyphicon-ok-sign red';
		$lostpets[] = array('slno' => $i,'id' => $row['lost_pet_id'],
						  'species' => $sepciesName,	
						  'lost_on' =>date("d-m-Y",strtotime($row['last_seen_on'])),	
						  'status' => $petstaus,
						);
		$i++;				
	}
	
	echoResponse(200, $lostpets);
});
/******************************************
Purpose: Get list of Lost Pets with all details
Parameter : NULL
Type : GET
******************************************/
$app->get('/lostpetlist', function () use ($app) { 
    global $db;
	$limit =  $app->request->get('limit');
	$order = "ORDER BY lost_pet_id DESC";
	if($limit!='') { $order.=" Limit 0, ".$limit; }
	
	$lostpets = array();
	
	$lostpet_qry = $db->prepare("select * from lost_pets WHERE status = '1' ".$order);
	$lostpet_qry->execute();
	$lostpetArray = $lostpet_qry->fetchAll(PDO::FETCH_ASSOC);
	//print_r($lostpetArray);
	$i = 1;
	if(count($lostpetArray)>0){
		foreach($lostpetArray as $lostpet) {
			
			$species_qry = $db->prepare("select * from species where type_id='".$lostpet['species_id']."'");
			$species_qry->execute();
			$speciesArray = $species_qry->fetchAll(PDO::FETCH_ASSOC);
			if(count($speciesArray)>0){
				$sepciesName = $speciesArray[0]['type_name'];
			}else{
				$sepciesName = '';
			}
			$breed_qry = $db->prepare("select * from breed_type where breed_id='".$lostpet['breed_type']."'");
			$breed_qry->execute();
			$breedArray = $breed_qry->fetchAll(PDO::FETCH_ASSOC);
			if(count($breedArray)>0){
				$breedName = $breedArray[0]['breed_name'];
			}else{
				$breedName = '';
			}
			//print_r($lostpet); 
			$petstaus = ($lostpet['status'] == 1) ? 'glyphicon glyphicon-ok-sign green' : 'glyphicon glyphicon-ok-sign red';
			$lostpets[] = array('slno' => $i,
								'id' => $lostpet['lost_pet_id'],
								'species' =>$sepciesName,
							  	'breed' =>$breedName,
							  	'nick_name' =>$lostpet['nick_name'],
							  	'image' =>$lostpet['image'],	
							  	'gender' =>$lostpet['gender'],
							  	'primary_color' =>$lostpet['primary_color'],
							  	'last_seen_at' =>$lostpet['last_seen_at'],
							  	'lost_from' =>date("d-m-Y",strtotime($lostpet['last_seen_on'])),	
							  	'status' => $petstaus,
								'microchip_no' =>	$lostpet['microchip_no'],
								'spay_neuter_status'	=>	$lostpet['spay_neuter_status'],
								'special_marking'	=>	$lostpet['special_marking'],
								'description'	=>	$lostpet['description'],
								'contact_address'	=>	$lostpet['contact_address'],
								'contact_address1'	=>	$lostpet['contact_address1'],
								'contact_email'	=>	$lostpet['contact_email'],
								'contact_phone'	=>	$lostpet['contact_phone'],
								'reward_offered'	=>	($lostpet['reward_offered']==1) ? 'Yes': 'No',
								'rewards'	>	$lostpet['rewards'],
								'flyer_id'	=>	$lostpet['flyer_id']
							);
			$i++;				
		}
	}
	
	echoResponse(200, $lostpets);
});
/******************************************
Purpose: Get Lost Pet details by ID
Parameter : id
Type : GET
******************************************/
$app->get('/lostpetbyid',function () use ($app) { 
    global $db;
	$pet_id =  $app->request->get('id');
				
	$query = $db->prepare("SELECT * FROM lost_pets WHERE lost_pet_id='".$pet_id."' "); 
	$query->execute();
    $rows = $query->fetchAll(PDO::FETCH_ASSOC);
	$pet = $rows[0];
	
	echoResponse(200, $pet);
});
/******************************************
Purpose: Create Lost Pet details
Parameter : form field
Type : POST
******************************************/
$app->post('/lostpetcreate',function () use ($app) { 
    global $db;
	global $imageClass;
	$data = json_decode($app->request->getBody(), true);
	$response = array();
	
	//print_r($data);
	if(isset($data['special_marking'])) $special_marking = $data['special_marking'];else $special_marking = '';
	if(isset($data['description'])) $description = $data['description'];else $description = '';
	if(isset($data['image'])) $image = $data['image'];else $image = '';
	if(isset($data['reward_amount'])) $rewards = $data['reward_amount'];else $rewards = '';
	if(isset($data['status'])) $status = $data['status'];else $status = 1;
	if(isset($data['microchip_no'])) $microchip_no = $data['microchip_no'];else $microchip_no = '';
	if(isset($data['owner_id'])) $owner_id = $data['owner_id'];else $owner_id = 0;
	if(isset($data['pet_id'])) $pet_id = $data['pet_id'];else $pet_id = 0;
	if(isset($data['address_2'])) $address_2 = $data['address_2'];else $address_2 = '';
	if(isset($data['spay_neuter_status'])) $spay_neuter_status = $data['spay_neuter_status'];else $spay_neuter_status = '';
	if(isset($data['image'])) 
		{
		$upload_path = BASE_PATH.'/images/lostpet/';
		$upload_url = BASE_URL.'/images/lostpet/';
			
	 	if(is_array($data['image'])){
			$profilePicture = explode(',', $data['image']);
			$pic_type = explode('/', $profilePicture[0]);
			$type = explode(';', $pic_type[1]);
			$rnd = time();
			$fileName = $rnd.'.'.$type[0]; // random number
	
			if(file_put_contents($upload_path.$fileName, base64_decode($profilePicture[1]))) { 
					$originalpath = $upload_url.$fileName;
					$target_file = $upload_path.'thumb_'.$fileName;
					$imageClass->load($originalpath);
					$imageClass->resize(143, 143);
					$imageClass->save($target_file);
					
					$image = $upload_url.$fileName;
					$image_thumb = $target_file;
			}
			else {
				$image = '';
			}
		 }
		 else{
			 $image = '';
		 }
	}	
	
	$sql = "insert into wp_flyer set `type`='Lost Dog', `have_photo`='1', `photo_url`='" . $image. "', `seen_found_date`='" . $data['last_seen_on']	. "', `name`='" . $data['nick_name']	. "', `age`='', `sex`='" . $data['gender']	. "', `spa_neuter_status`='" . $spay_neuter_status	. "', `description`='" . $special_marking	. "', `location`='" . $data['last_seen_at']	. "', `comments`='" . $description	. "', `contact_email`='" . $data['email']	. "', `contact_phone`='" . $data['contact_number']	. "', `reward_offered`='" . $data['reward_offered']	. "', `reward_amount` = '".$rewards. "', `created_on`=NOW();";
	$lostpetid = $db->exec($sql);
	$q = "INSERT INTO `lost_pets` ( `species_id`, `breed_type`, `nick_name`, `microchip_no`, `gender`, `spay_neuter_status`, `primary_color`, `special_marking`, `description`, `image`, `last_seen_on`, `last_seen_at`, `owner_id`, `pet_id`, `contact_address`, `contact_address1`, `contact_email`, `contact_phone`, `status`, `reward_offered`, `rewards`, `flyer_id`) VALUES ('".$data['species_id']."', '".$data['breed_type']."', '".$data['nick_name']."', '".$microchip_no."', '".$data['gender']."', '".$spay_neuter_status."', '".$data['primary_color']."', '".$special_marking."', '".$description."', '".$image."', '".$data['last_seen_on']."', '".$data['last_seen_at']."', '".$owner_id."', '".$pet_id."', '".$data['address_1']."', '".$address_2."', '".$data['email']."', '".$data['contact_number']."', '".$status."', '".$data['reward_offered']."', '".$rewards."', '".$lostpetid."')";
	//echo $q; 
	$query = $db->prepare($q);
	$query->execute();
	//$query = $db->prepare($sql);
	//$query->execute();
	
	if($data['submittype']=='create') {
			$response = array('sucess' => true, 'message' => 'Lost Pet created Successfully !', 'type' => $data['submittype']);
	}
	if($data['submittype']=='createflyer') {
			$response =		array('sucess' => true, 
								'message' => 'Lost Pet created Successfully !',
								'type' => $data['submittype'],
								'flyerid' => $lostpetid,
								'redirect' => BASE_URL.'/blog/create-flyer/?flyer='.$lostpetid);
	}
	
	echoResponse(200, $response);
});
/******************************************
Purpose: Update Lost Pet details by ID
Parameter : form field
Type : POST
******************************************/
$app->post('/lostpetupdate',function () use ($app) { 
    global $db;
	$data = json_decode($app->request->getBody(), true);
	
	
	$q = "UPDATE `lost_pets` SET `nick_name` = '".$data['nick_name']."', `species_id` = '".$data['species_id']."', `owner_id` = '".$data['owner_id']."', `microchip_no` = '".$data['microchip_no']."', `breed_type` = '".$data['breed_type']."', `gender` = '".$data['gender']."', `primary_color` = '".$data['primary_color']."', `special_marking` = '".$data['special_marking']."', `description` = '".$data['description']."', `image` = '".$data['image']."', `last_seen_on` = '".$data['last_seen_on']."', `last_seen_at` = '".$data['last_seen_at']."', `status` = '".$data['status']."', `updated_at` = now() WHERE `lost_pet_id` = '".$data['lost_pet_id']."';";
	
	//echo $q; 
	$query = $db->prepare($q);
	$query->execute();
	
	$response = array();
	$response = array('sucess' => true, 'message' => 'Pet info updated Sucessfully !', 'type' => $data['submittype']);
	echoResponse(200, $response);
});
/******************************************
Purpose: Get list of Found Pets with all details
Parameter : NULL
Type : GET
******************************************/
$app->get('/foundpetlist', function () use ($app) { 
    global $db;
	$limit =  $app->request->get('limit');
	$order = "ORDER BY found_pet_id DESC";
	if($limit!='') { $order.=" Limit 0, ".$limit; }
	
	$foundpets = array();
	
	$foundpet_qry = $db->prepare("select * from found_pets where status ='1' ".$order);
	$foundpet_qry->execute();
	$foundpetArray = $foundpet_qry->fetchAll(PDO::FETCH_ASSOC);
	//print_r($foundpetArray);
	$i = 1;
	if(count($foundpetArray)>0){
		foreach($foundpetArray as $foundpet) {
			if($foundpet['species_id'] > 1)
				$species =  $db->getspecies($foundpet['species_id']);
			else
				$species = 'Nil';
			if($foundpet['breed_type'] > 1)
				$breed_type =  $db->getbreedname($foundpet['breed_type']);
			else
				$breed_type = 'Nil';
			$petstaus = ($foundpet['status'] == 1) ? 'glyphicon glyphicon-ok-sign green' : 'glyphicon glyphicon-ok-sign red';
			$foundpets[] = array('slno' => $i,
								  'id' => $foundpet['found_pet_id'],
								  'species' =>$species,
								  'breed' =>$breed_type,
								  'nick_name' =>$foundpet['nick_name'],	
								  'gender' =>$foundpet['gender'],
								  'primary_color' =>$foundpet['primary_color'],
								  'microchip_no' =>$foundpet['microchip_no'],
								  'special_marking' =>$foundpet['special_marking'],
								  'description' =>$foundpet['description'],
								  'image' =>$foundpet['image'],
								  'found_at' =>$foundpet['found_at'],
								  'found_on' =>date("d-m-Y",strtotime($foundpet['found_on'])),	
								  'found_by' =>$foundpet['found_by'],
								  'address_1' =>$foundpet['address_1'],
								  'address_2' =>$foundpet['address_2'],
								  'contact_email' =>$foundpet['contact_email'],
								  'contact_phone' =>$foundpet['contact_phone'],
								  'status' => $petstaus,);
			$i++;				
		}
	}
	
	//print_r($foundpets);
	echoResponse(200, $foundpets);
});
/******************************************
Purpose: Get all Found pets details
Parameter : 
Type : GET
******************************************/
$app->get('/foundpets', function () use ($app) { 
    global $db;
	$limit =  $app->request->get('limit');
	$order = "ORDER BY found_pet_id DESC";
	if($limit!='') { $order.=" Limit 0, ".$limit; }
	
    $rows = $db->select("found_pets","*", array(), $order);
	$lostpets = array();
	
	$i = 1;
    foreach($rows['data'] as $row) {
		$species_qry = $db->prepare("select * from species where type_id='".$row['species_id']."'");
		$species_qry->execute();
		$speciesArray = $species_qry->fetchAll(PDO::FETCH_ASSOC);
		if(count($speciesArray)>0){
			$sepciesName = $speciesArray[0]['type_name'];
		}else{
			$sepciesName = '--';
		}
		$petstaus = ($row['status'] == 1) ? 'glyphicon glyphicon-ok-sign green' : 'glyphicon glyphicon-ok-sign red';
		$lostpets[] = array('slno' => $i,'id' => $row['found_pet_id'],
						  'species' => $sepciesName,	
						  'found_on' =>date("d-m-Y",strtotime($row['found_on'])),	
						  'status' => $petstaus,
						);
		$i++;				
	}
	
	echoResponse(200, $lostpets);
});
/******************************************
Purpose: Get Found Pet details by ID
Parameter : id
Type : GET
******************************************/
$app->get('/foundpetbyid',function () use ($app) { 
    global $db;
	$pet_id =  $app->request->get('id');
				
	$query = $db->prepare("SELECT * FROM found_pets WHERE found_pet_id='".$pet_id."' "); 
	$query->execute();
    $rows = $query->fetchAll(PDO::FETCH_ASSOC);
	$pet = $rows[0];
	
	echoResponse(200, $pet);
});
/******************************************
Purpose: Create Found Pet details
Parameter : form field
Type : POST
******************************************/
$app->post('/foundpetcreate',function () use ($app) { 
    global $db;
	$data = json_decode($app->request->getBody(), true);
	$response = array();
	
	//print_r($data);
	if(isset($data['special_marking'])) $special_marking = $data['special_marking'];else $special_marking = '';
	if(isset($data['description'])) $description = $data['description'];else $description = '';
	if(isset($data['image'])) $image = $data['image'];else $image = '';
	if(isset($data['nick_name'])) $nick_name = $data['nick_name'];else $nick_name = '';
	if(isset($data['microchip_no'])) $microchip_no = $data['microchip_no'];else $microchip_no = '';
	if(isset($data['species_id'])) $species_id = $data['species_id'];else $species_id = "NULL";
	if(isset($data['breed_type'])) $breed_type = $data['breed_type'];else $breed_type = "NULL";
	if(isset($data['found_by'])) $found_by = $data['found_by'];else $found_by =  "NULL";
	if(isset($data['address_2'])) $address_2 = $data['address_2'];else $address_2 =  "NULL";
	if(isset($data['owner_id'])) $owner_id = $data['owner_id'];else $owner_id =  0;
	if(isset($data['image'])) 
						{
							$upload_path = BASE_PATH.'/images/foundpet/';
							$upload_url = BASE_URL.'/images/foundpet/';
							
							$profilePicture = explode(',', $data['image']);
							$pic_type = explode('/', $profilePicture[0]);
							$type = explode(';', $pic_type[1]);
							$rnd = time();
							$fileName = $rnd.'.'.$type[0]; // random number
							
							if(file_put_contents($upload_path.$fileName, base64_decode($profilePicture[1]))) { 
								$image = $upload_url.$fileName;
							}
							else {
								$image = '';
							}
	}	
	$q = "INSERT INTO `found_pets` ( `species_id`, `breed_type`, `nick_name`, `microchip_no`, `gender`, `primary_color`, `special_marking`, `description`, `image`, `found_on`, `found_at`, `found_by`, `address_1`, `address_2`, `contact_email`, `contact_phone`, `status`, `owner_id`) VALUES ('".$species_id."', '".$breed_type."', '".$nick_name."', '".$microchip_no."', '".$data['gender']."', '".$data['primary_color']."', '".$special_marking."', '".$description."', '".$image."', '".$data['found_on']."', '".$data['found_at']."', ".$found_by.", '".$data['address_1']."', '".$address_2."', '".$data['email']."', '".$data['contact_number']."', '1', '".$owner_id."')";
	//echo $q; 
	$query = $db->prepare($q);
	$query->execute();
	
	if($data['submittype']=='create') {
			$response = array('sucess' => true, 'message' => 'Created Successfully !', 'type' => $data['submittype']);
	}
	
	echoResponse(200, $response);
});
/******************************************
Purpose: Update Found Pet details by ID
Parameter : form field
Type : POST
******************************************/
$app->post('/foundpetupdate',function () use ($app) { 
    global $db;
	$data = json_decode($app->request->getBody(), true);
	
	//print_r($data);
	if(isset($data['nick_name']) || ($data['nick_name'])) $nick_name = " `nick_name` = '".$data['nick_name']."', ";else $nick_name = " `nick_name` = NULL, ";
	if(isset($data['species_id']) || ($data['species_id'])) $species_id = " `species_id` = '".$data['species_id']."', ";else $species_id = " `species_id` = NULL, ";
	if(isset($data['found_by']) || ($data['found_by'])) $found_by = " `found_by` = '".$data['found_by']."', ";else $found_by =  "`found_by` = NULL, ";
	
	$q = "UPDATE `found_pets` SET ".$nick_name.$species_id.$found_by."`microchip_no` = '".$data['microchip_no']."', `breed_type` = '".$data['breed_type']."', `gender` = '".$data['gender']."', `primary_color` = '".$data['primary_color']."', `special_marking` = '".$data['special_marking']."', `description` = '".$data['description']."', `image` = '".$data['image']."', `found_on`='".$data['found_on']."', `found_at`='".$data['found_at']."', `updated_at` = now() WHERE `found_pet_id` = '".$data['found_pet_id']."'";
	
	//echo $q; 
	$query = $db->prepare($q);
	$query->execute();
	
	$response = array();
	$response = array('sucess' => true, 'message' => 'Pet info updated Sucessfully !', 'type' => $data['submittype']);
	echoResponse(200, $response);
});


$app->get('/species', function () use ($app) { 
    global $db;
	$query_species = $db->prepare("SELECT * FROM species"); 
	$query_species->execute();
	$rows_species = $query_species->fetchAll(PDO::FETCH_ASSOC);
	$i=1;
	
    foreach($rows_species as $row) {
		$status_short = 'spec'.$row['status'].$row['type_id'] ;
		$species[] = array('id' => $i,
							'type_id' => $row['type_id'],
						  'type_name' => $row['type_name'],
						  'status_short' => $status_short
						);
						$i++;
	}
	echoResponse(200, $species);
});
/******************************************
Purpose: Get all available Species details
Parameter : 
Type : GET
******************************************/
$app->get('/breedlist', function () use ($app) { 
    global $db;
	$query_breed = $db->prepare("SELECT * FROM breed_type WHERE status='1' and species_id='1'"); 
	$query_breed->execute();
	$rows_breed = $query_breed->fetchAll(PDO::FETCH_ASSOC);
	
    foreach($rows_breed as $row) {
		$breeds[] = array('breedtype_id' => $row['breed_id'],
						  'breedtype_name' => $row['breed_name']
						);
	}
	echoResponse(200, $breeds);
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
Purpose: Payment Trigger
Parameter : 
Type : POST
******************************************/
$app->post('/paymentInstamojo', function () use ($app) { 
    global $db;
	$data = json_decode($app->request->getBody(), true);
	$userid = $data['userid'];
	$petid  = $data['petid'];
	
	$user_details = $db->select("users", "user_id, fname, lname, email, phone_1", array('user_id' => $userid), "");
	
	$name = $user_details['data'][0]['fname'].' '.$user_details['data'][0]['lname'];
	
	$api_key = api_key;		
	$auth_token = auth_token ; 
	$endpoint = endpoint ;
	require '.././libs/Instamojo/Instamojo.php';
	
	$api = new Instamojo( $api_key, $auth_token, $endpoint );
	
	try {
		$response = $api->paymentRequestCreate(array(
			"purpose" => "TailTracking Pet Registration",
			"buyer_name" => $name,
			"amount" => 100,
			"send_email" => false,
			"send_sms" => false,
			"email" => $user_details['data'][0]['email'],
			"phone" => $user_details['data'][0]['phone_1'],
			"redirect_url" => "http://dev.tailtracking.com/#/payment-response"
			));
			
			$response = array( 'success' => true, 'url' => $response['longurl'] );
	}
	catch (Exception $e) {
		$response = array( 'success' => false, 'message' => $e->getMessage() );
		//print('Error: ' . $e->getMessage());
	}
	
	echoResponse(200, $response);
});
/******************************************
Purpose: Payment Response
Parameter : 
Type : POST
******************************************/
$app->post('/paymentResponse', function () use ($app) { 
    global $db;
	$data = json_decode($app->request->getBody(), true);
	$payment_request_id = $data['payment_request_id'];
	$payment_id = $data['payment_id'];
	$petid = $data['petid'];
	$userid = $data['userid'];
	
	$api_key = api_key;		
	$auth_token = auth_token ; 
	$endpoint = endpoint ;
	require '.././libs/Instamojo/Instamojo.php';
	
	$api = new Instamojo($api_key, $auth_token, $endpoint);
	
	try {
		$response = $api->paymentRequestPaymentStatus($payment_request_id, $payment_id);
		//echo "<pre>";
		//print_r($response); die;
		
		if( count($response['payment'])>0) { 
			
			$transcation_id = $response['id'];
			$payment_id = $response['payment']['payment_id'];
			$buyer_name = $response['payment']['buyer_name'];
			$buyer_phone = $response['payment']['buyer_phone'];
			$buyer_email = $response['payment']['buyer_email'];
			$amount = $response['payment']['amount'];
			$fees = $response['payment']['fees'];
			$status = $response['payment']['status'];
			
			$q_payment = "INSERT INTO `pet_payments`(`petid`, `buyer_name`, `buyer_phone`, `buyer_email`, `amount`, `payment_id`, `transcation_id`, `fees`, `status`) VALUES ('".$petid."', '".$buyer_name."', '".$buyer_phone."', '".$buyer_email."', '".$amount."', '".$payment_id."', '".$transcation_id."', '".$fees."', '".$status."' )";
			//echo $q_payment; die;
			$query_payment = $db->exec($q_payment);
			
			if($query_payment) {
				$q_update_payment = "UPDATE `pets` SET `status` = '1' WHERE pet_id = '".$petid."'";
				//echo $q_update_chip; 
				$query_update_payment = $db->prepare($q_update_payment);
				$query_update_payment->execute();
				
				//Mail send to user - Activation Mail
				$to = $buyer_email;
				$subject = 'TailTracking - Welcome Email';
				$headers = "From: " . strip_tags('info@tailtracking.com'). "\r\n";
				//$headers .= "CC: susan@example.com\r\n";
				$headers .= "MIME-Version: 1.0\r\n";
				$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
				$activateurl = 'http://dev.tailtracking.com/#/account-activate?code='.base64_encode($userid);
				/*$message = file_get_contents('../email/signup_welcome.php');
				$message = str_replace('$name', '$buyer_name', $message);
				$message = str_replace('$url', '$activateurl', $message);*/
				$message = '<!DOCTYPE html><html lang="en"><head>
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
				$message.='<h5 style="color: #646e78;font-size: 16px;padding:0;margin: 0; text-align:left;">Hi '.
						   $buyer_name.' ,</h5>';				
				$message.=' <p style="color: #646e78;font-size: 16px;text-align:left;">Greetings!</p>
							<p style="color: #646e78;font-size: 16px;padding:0; line-height:18px; text-align:left; 
							">You’ve successfully registered with TailTracking. <br/>
							</p>
							<p style="color: #646e78;font-size: 15px;text-align:left;"> Please click below link to 
							activate your account.</p>
							<p style="color: #646e78;font-size: 15px;text-align:left;"><a href='.$activateurl.'>'.
							$activateurl.'</a> </p>';
				$message.='<p style="color: #646e78;text-align:left;font-size: 16px;padding:0 0 45px 0; margin:51px 0 0; 
							line-height:20px; text-align:left;font-family:Calibri">Thank you!<br><br>Best 
							regards,<br>TailTracking.</p>
        					<div style="float:left; width:100%; margin:40px 0 0 0; border-top:solid 1px #dddddd; 	
							padding:20px 0 0 0;"> <span style="color: #646e78;font-size: 12px; 
							float:left;line-height:34px;">Copyrights @ 2017. All Rights Reserved.</span> <span 
							style="float:right;"> <a href="#"><img 
							src="http://dev.tailtracking.com/assets/img/facebook.png" style="float:left; margin:0 0
							0 12px;width: 45px;height: 45px;" /></a> <a href="#"><img 
							src="http://dev.tailtracking.com/assets/img/google_plus.png" style="float:left; 
							margin:0 0 0 12px;width: 45px;height: 45px;" /></a> <a href="#"><img 
							src="http://dev.tailtracking.com/assets/img/instagram.png" style="float:left; margin:0 
							0 0 12px;width: 45px;height: 45px;"/></a>
							<a href="#"><img src="http://dev.tailtracking.com/assets/img/twitter.png" style=
							"float:left; margin:0 0 0 12px;width: 45px;height: 45px;" /></a>';
				$message.= '</span> </div></div></div></div></div></body></html>';			
							 	
				mail($to, $subject, $message, $headers);
				/*$q_update_user = "UPDATE `users` SET `status` = '1' WHERE user_id = '".$userid."'";
				$query_update_user = $db->prepare($q_update_user);
				$query_update_user->execute();*/
				
				$output = array( 'success' => true, 'message' => 'Your payment completed successfully', 'title' => 'Payment Success', 'paymentid' => $payment_id, 'error' => '' );
			}
		}
		else {
			$output = array( 'success' => false, 'message' => 'Your payment failed. Please try again later!', 'title' => 'Payment Failed', 'paymentid' => $payment_id, 'error' => '' );
		}
	}
	catch (Exception $e) {
		$del_q = "DELETE * FROM `pets` WHERE pet_id='".$petid."' ";
		$delete_q = $db->prepare($q_update_payment);
		$delete_q->execute();
		
		//print('Error: ' . $e->getMessage());
		$output = array( 'success' => false, 'message' => 'Your payment failed. Please try again later!', 'title' => 'Payment Failed', 'paymentid' => $payment_id, 'error' => $e->getMessage() );
	}
		
	echoResponse(200, $output);
});
/******************************************
Purpose: Payment Trigger Register Page
Parameter : 
Type : POST
******************************************/
$app->post('/paymentRegisterInstamojo', function () use ($app) { 
    global $db;
	$data = json_decode($app->request->getBody(), true);
	$userid = $data['userid'];
	$petid  = $data['petid'];
	
	$user_details = $db->select("users", "user_id, fname, lname, email, phone_1", array('user_id' => $userid), "");
	
	$name = $user_details['data'][0]['fname'].' '.$user_details['data'][0]['lname'];
	
	$api_key = api_key;		
	$auth_token = auth_token ; 
	$endpoint = endpoint ;
	require '.././libs/Instamojo/Instamojo.php';
	
	$api = new Instamojo( $api_key, $auth_token, $endpoint );
	
	try {
		$response = $api->paymentRequestCreate(array(
			"purpose" => "TailTracking Pet Registration",
			"buyer_name" => $name,
			"amount" => 100,
			"send_email" => false,
			"send_sms" => false,
			"email" => $user_details['data'][0]['email'],
			"phone" => $user_details['data'][0]['phone_1'],
			"redirect_url" => "http://dev.tailtracking.com/#/register-payment-response"
			));
			
			$response = array( 'success' => true, 'url' => $response['longurl'] );
	}
	catch (Exception $e) {
		$response = array( 'success' => false, 'message' => $e->getMessage() );
		//print('Error: ' . $e->getMessage());
	}
	
	echoResponse(200, $response);
});
/******************************************
Purpose: Registration step 2 verify OTP
Parameter : otp
Type : POST
******************************************/
$app->post('/verifyOTP',function () use ($app) { 
    global $db;
	$data = json_decode($app->request->getBody(), true);
	$sessionotp = $_SESSION['otp'];
	$otp = $data['otp'];
	if($otp == $sessionotp) {
		
		unset( $_SESSION['otp'] );
		unset( $_SESSION['mobile'] );
		
		$response = array( 'success' => true);
	}
	else {
		$response = array( 'success' => false, 'message' => 'Please enter valid OTP');
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

?>