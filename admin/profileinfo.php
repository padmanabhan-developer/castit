<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//include database connection file
require_once 'dbHelper.php';
require_once 'src/functions.php';
$db = new dbHelper();

if(isset($_GET['id']) && is_numeric($_GET['id'])){
  $id = $_GET['id'];
  $query_string = "SELECT * from profiles WHERE id='".$id."'";
  $user_profile_query = $db->prepare($query_string);
  $user_profile_query->execute();
  $row = $user_profile_query->rowCount();
}
else{
  $email = explode("email=", $_POST['PHPSESSID'])[1];
  $query_string = "SELECT * from profiles WHERE email='".$email."'";
  $user_profile_query = $db->prepare($query_string);
  $user_profile_query->execute();
  $row = $user_profile_query->rowCount();
}

  $query_country = $db->prepare("SELECT * FROM countries order by name_dk"); 
	$query_country->execute();
	$country_list = $query_country->fetchAll(PDO::FETCH_ASSOC);

  $eye_colors = $db->prepare("SELECT * FROM eye_colors ORDER BY sortby"); 
	$eye_colors->execute();
  $eye_colors_list = $eye_colors->fetchAll(PDO::FETCH_ASSOC);

	$hair_colors = $db->prepare("SELECT * FROM hair_colors ORDER BY sortby"); 
	$hair_colors->execute();
  $hair_colors_list = $hair_colors->fetchAll(PDO::FETCH_ASSOC);

	$gender = $db->prepare("SELECT * FROM genders"); 
	$gender->execute();
  $gender_list = $gender->fetchAll(PDO::FETCH_ASSOC);

	$category = $db->prepare("SELECT * FROM categories"); 
	$category->execute();
  $category_list = $category->fetchAll(PDO::FETCH_ASSOC);

	$skills = $db->prepare("SELECT * FROM skills"); 
	$skills->execute();
  $skills_list = $skills->fetchAll(PDO::FETCH_ASSOC);

  $licences = $db->prepare("SELECT * FROM drivers_licenses"); 
	$licences->execute();
  $licences_list = $licences->fetchAll(PDO::FETCH_ASSOC);

  $language = $db->prepare("SELECT * FROM language_proficiency_languages"); 
	$language->execute();
  $language_list = $language->fetchAll(PDO::FETCH_ASSOC);


  if ($row > 0){
  foreach ($user_profile_query->fetchAll(PDO::FETCH_ASSOC) as $key => $value) {
    
    $pid = $value['id'];
    $category_query = $db->prepare("SELECT category_id FROM categories_profiles where profile_id = $pid");
    $category_query->execute();
    foreach ($category_query->fetchAll(PDO::FETCH_ASSOC) as $ct_item){
      $value['categories'][] = $ct_item['category_id'];
    }

    $skill_query = $db->prepare("SELECT skill_id FROM profiles_skills where profile_id = $pid");
    $skill_query->execute();
    foreach ($skill_query->fetchAll(PDO::FETCH_ASSOC) as $sk_item){
      $value['skills'][] = $sk_item['skill_id'];
    }

    $license_query = $db->prepare("SELECT drivers_license_id FROM drivers_licenses_profiles where profile_id = $pid");
    $license_query->execute();
    foreach ($license_query->fetchAll(PDO::FETCH_ASSOC) as $lc_item){
      $value['licenses'][] = $lc_item['drivers_license_id'];
    }

    $language_query = $db->prepare("SELECT distinct language_proficiency_language_id as lpli, id, language_proficiency_rating_id FROM language_proficiencies where profile_id = $pid group by lpli");
    $language_query->execute();
    foreach ($language_query->fetchAll(PDO::FETCH_ASSOC) as $lng_item){
      $value['languages'][] = ['lang_id'=>$lng_item['lpli'], 'rating'=>$lng_item['language_proficiency_rating_id'],'lng_pro_id'=>$lng_item['id']];
    }

    $payment_query = $db->prepare("SELECT * from payments where profile_id = $pid");
    $payment_query->execute();
    foreach($payment_query->fetchAll(PDO::FETCH_ASSOC) as $payment){
      $value['payments'][] = ['payment_type_id'=>$payment['payment_type_id'], 'applies'=>$payment['applies'], 'paid'=>$payment['paid'], 'description'=>$payment['description']];
    }

    $name_query = $db->prepare("SELECT CONCAT(first_name, ' ', last_name) as name from profiles where id = $pid");
    $name_query->execute();
    $name = $name_query->fetch(0)['name'];
  
    $profile_number_query = $db->prepare("SELECT profile_number, profile_id from memberships where profile_id = $pid");
    $profile_number_query->execute();
    $profile_number = $profile_number_query->fetch(0)['profile_number'];

    $birthday = explode('-', $value['birthday']);
    $value['birth_day'] = $birthday[2];
    $value['birth_month'] = $birthday[1];
    $value['birth_year'] = $birthday[0];

    $marked_as_new_from = $value['marked_as_new_from'];
    $marked_as_new_till = $value['marked_as_new_till'];

    $user_profile = json_encode($value);
  }
  $json_profile_info = json_encode($value);
} 
else{
  // echo 'wrong';
}
// pp($value);

$payment_0_description = (isset($value['payments'][0]['description'])) ? $value['payments'][0]['description'] : "";
$payment_1_description = (isset($value['payments'][1]['description'])) ? $value['payments'][1]['description'] : "";
$payment_2_description = (isset($value['payments'][2]['description'])) ? $value['payments'][2]['description'] : "";

$age = date_diff(date_create($value['birthday']), date_create('today'))->y;
$is_kid     = FALSE;
$is_male    = FALSE;
$is_female  = FALSE;

if($age <= 14){
  $is_kid = TRUE; 
}

if($value['gender_id'] == '1' && !$is_kid){
  $is_male = TRUE;
}

if($value['gender_id'] == '2' && !$is_kid){
  $is_female = TRUE;
}
// pp($value);

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Castit</title>
<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
<meta http-equiv="cache-control" content="no-store" />
<meta http-equiv="expires" content="0">
<link href="css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<link href="css/bootstrap-toggle.css" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>
	
<body>
<div id="wrapper">
  <header id="header">
  		<div class="container">
  		<div class="logo"><a href="#"><img src="images/logo.png" alt=""></a></div>
        
    	<div id="navbar">    
              <nav class="navbar navbar-default navbar-static-top" role="navigation">
                        <div class="navbar-header">
                            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse-1">
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                            </button>
                        </div>
                        
                        <div class="collapse navbar-collapse" id="navbar-collapse-1">
                            <ul class="nav navbar-nav">
                                <li class="current-menu-item"><a href="/admin">Profiler</a></li>
                                <li><a href="./profileinfo?id=<?php echo $id; ?>">opret job</a></li>
                                <li><a href="./profileinfo?id=<?php echo $id; ?>">too do</a></li>
                                <li><a href="./profileinfo?id=<?php echo $id; ?>">tekst</a></li>
                                <li><a href="./profileinfo?id=<?php echo $id; ?>">intro billeder</a></li>
                                <li><a href="./profileinfo?id=<?php echo $id; ?>">alle profiler</a></li>	
                                <li><a href="./profileinfo?id=<?php echo $id; ?>">Castingsheet</a></li>
                                <li><a href="./profileinfo?id=<?php echo $id; ?>">KALENDER</a></li>
                                <li><a class="media_page_link" href="/admin/profilemedia?id=<?php echo $id; ?>&type=all">FOTO/VIDEO</a></li>
                            </ul>
                        </div><!-- /.navbar-collapse -->
                    </nav>
            </div>
            
            </div>
  </header><!--close header-->
	
	
	
	
  
  <div id="content"> 
	  
       <div class="page-top page-top2">
       		<div class="container">
            	 <h2><?php echo $name; ?>. <?php echo $profile_number;?></h2>
                 <div class="upload-sec">
                    <?php
                      $online_checked = '';
                      $offline_checked = '';
                      if($value['profile_status_id'] == 1){
                        $online_checked = 'checked="checked"';
                      }
                      else{
                        $offline_checked = 'checked="checked"';
                      }
                    ?>
                
                <?php
                  $prefix_profilenumber = substr($profile_number , 0 , 1);
                  $number_profilenumber = substr($profile_number , 1 );
                  if(in_array(strtolower($prefix_profilenumber), array("c","y","b"))){ 
                    $c_checked = (strtolower($prefix_profilenumber) == "c") ? 'checked="checked"' : '';
                    $y_checked = (strtolower($prefix_profilenumber) == "y") ? 'checked="checked"' : '';
                    $b_checked = (strtolower($prefix_profilenumber) == "b") ? 'checked="checked"' : '';
                  ?>


                  <div class="box0" style="width: 150px;">
                    <div class="radio radio-info" style="width: 100%;">
                      <input type="radio" name="profile-number-selection" id="Radios1" value="<?php echo "C".$number_profilenumber; ?>" profile_id=<?php echo $id; ?> <?php echo $c_checked;?>>
                      <label><?php echo "C".$number_profilenumber; ?></label>
                    </div>

                    <div class="radio radio-info" style="width: 100%;">
                      <input type="radio" name="profile-number-selection" id="Radios1" value="<?php echo "Y".$number_profilenumber; ?>" profile_id=<?php echo $id; ?> <?php echo $y_checked;?>>
                      <label><?php echo "Y".$number_profilenumber; ?></label>
                    </div>

                    <div class="radio radio-info" style="width: 100%;">
                      <input type="radio" name="profile-number-selection" id="Radios1" value="<?php echo "B".$number_profilenumber; ?>" profile_id=<?php echo $id; ?> <?php echo $b_checked;?>>
                      <label><?php echo "B".$number_profilenumber; ?></label>
                    </div>
                  </div>
                <?php
                  }
                ?>                
                 <div class="box0">
                        <div class="radio radio-info">
                        <input type="radio" name="profile-status-value" id="Radios1" value="1" profile_id=<?php echo $id; ?> <?php echo $online_checked;?>>
                        <label>Online</label>
                        </div>
                        
                        <div class="check-area">
                            <label class="chek-box">sendmail
                                    <input class="send_activation_email" type="checkbox" value="send_online">
                                    <span class="checkmark"></span>
                            </label>
                        </div>
                        
                        <div class="radio radio-info">
                        <input type="radio" name="profile-status-value" id="Radios3" value="2" profile_id=<?php echo $id; ?> <?php echo $offline_checked;?>>
                        <label>Offline</label>
                        </div>
                        
                        <div class="check-area">
                            <label class="chek-box">sendmail
                                    <input class="send_deactivation_email" type="checkbox"  value="send_offline">
                                    <span class="checkmark"></span>
                            </label>
                        </div>
                        <div class="form-row">
                          <label style="float: left;width: 30%;" class="chek-box">Bureau</label>
                            <input style="width: 65%;height: 35px;" type="text" class="form-input1 disabled bureau" placeholder="" value="<?php echo $value['bureau'] ;?>">
                        </div>
                      </div>
                 	  <div class="box1">
                      	   <div class="check-area">
                                <label class="chek-box">Vis som ny profil
                                <?php 
                                $checked_new = '';
                                if($value['marked_as_new']){
                                  $checked_new = 'checked="checked"';
                                }
                                ?>
                                       <input type="checkbox" <?php echo $checked_new; ?> class="marked_as_new">
                                       <span class="checkmark"></span>
                                </label>
                           </div>
                      </div>
                      
                      <div class="box2">
                      	   <div class="row">
                               <div class="col3"><label>Fra:</label></div>
                               <?php
                               /*
                                if(isset($value['marked_as_new_from']) && $value['marked_as_new_from'] != NULL && $value['marked_as_new_from'] != '' && $value['marked_as_new_from'] != 0){
                                  $marked_as_new_from_day   = date("d", $value['marked_as_new_from']);
                                  $marked_as_new_from_month = date("m", $value['marked_as_new_from']);
                                  $marked_as_new_from_year  = date("Y", $value['marked_as_new_from']);
                                }
                                else{
                                  $marked_as_new_from_day   = "";
                                  $marked_as_new_from_month = "";
                                  $marked_as_new_from_year  = "";
                                }
                                */
                               ?>
                               <?php /* ?>
                                <div class="col3"><input value="<?php echo $marked_as_new_from_day;?>" type="text" class="form-input1 marked_as_new_from_day" placeholder="DD" maxlength=2></div>
                                <div class="col3"><input value="<?php echo $marked_as_new_from_month;?>" type="text" class="form-input1 marked_as_new_from_month" placeholder="MM" maxlength=2></div>
                                <div class="col3"><input value="<?php echo $marked_as_new_from_year;?>" type="text" class="form-input1 marked_as_new_from_year" placeholder="YYYY" maxlength=4></div>
                                <?php */ ?>

                                <div class="col6" style="padding:0; width:185px; float:right;"><input type="date" name="marked_as_new_from" class="form-input1 marked_as_new_from" style="padding-left: 25%; letter-spacing:1px; font-weight:bold" value="<?php echo $marked_as_new_from;?>"></div>
                                

                           </div>
                           
                           <div class="row">
                               <div class="col3"><label>Til:</label></div>
                               <?php
                               /*
                                if(isset($value['marked_as_new_till']) && $value['marked_as_new_till'] != NULL && $value['marked_as_new_till'] != '' && $value['marked_as_new_till'] != 0){
                                  $marked_as_new_till_day   = date("d", $value['marked_as_new_till']);
                                  $marked_as_new_till_month = date("m", $value['marked_as_new_till']);
                                  $marked_as_new_till_year  = date("Y", $value['marked_as_new_till']);
                                }
                                else{
                                  $marked_as_new_till_day   = "";
                                  $marked_as_new_till_month = "";
                                  $marked_as_new_till_year  = "";
                                }
                                */
                               ?>
                               <?php /* ?>
                                <div class="col3"><input value="<?php echo $marked_as_new_till_day;?>" type="text" class="form-input1 marked_as_new_till_day" placeholder="DD" maxlength=2></div>
                                <div class="col3"><input value="<?php echo $marked_as_new_till_month;?>" type="text" class="form-input1 marked_as_new_till_month" placeholder="MM" maxlength=2></div>
                                <div class="col3"><input value="<?php echo $marked_as_new_till_year;?>" type="text" class="form-input1 marked_as_new_till_year" placeholder="YYYY" maxlength=4></div>
                                <?php */ ?>
                                <div class="col6" style="padding:0; width:185px; float:right"><input type="date" name="marked_as_new_till" class="form-input1 marked_as_new_till" style="padding-left: 25%; letter-spacing:1px; font-weight:bold" value="<?php echo $marked_as_new_till;?>"></div>
                           </div>
                           <button class="btn_2 submit_update godkend_button">Godkend</button>
                      </div>
                      
                    <?php
                    $close_link = ($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "/admin";
                    ?>
                 	  <a class="upload-close" href="<?php echo $close_link ?>" style="background-position-y: top"></a>
                 </div>
            </div>
       </div><!--close page-top-->
       
       
       <div class="page-bottom">
       		<div class="container">
            	 
                 <div class="form-sec">
                 	  <div class="form-header">
                      <h2>Profil informationer</h2>
                      <?php /* ?>
                      <div class="profile-status-actions">
                        <div class="check-area">
                          <button class="btn_2 send-email-notification" profile-status="<?php echo $value['profile_status_id']?>" profile_id=<?php echo $id; ?> >Send eMail</button>
                        </div> 
                        <?php
                          $online_checked = '';
                          $offline_checked = '';
                          if($value['profile_status_id'] == 1){
                            $online_checked = 'checked="checked"';
                          }
                          else{
                            $offline_checked = 'checked="checked"';
                          }
                        ?>
                        <div class="check-area">
                          <label class="chek-box">Offline
                            <input <?php echo $offline_checked ; ?> type="checkbox" class="profile-status-value" value="2" profile_id=<?php echo $id; ?> >
                            <span class="checkmark"></span>
                          </label>
                        </div>
                        <div class="check-area">
                          <label class="chek-box">Online
                            <input <?php echo $online_checked ; ?> type="checkbox" class="profile-status-value" value="1"
                            profile_id=<?php echo $id; ?> >
                            <span class="checkmark"></span>
                          </label>
                        </div>
                        <h3>Profile Status: </h3>
                      </div>
                      <?php */ ?>
                    </div>
                 </div><!--close form-sec-->
                 
                 <div class="form-area">
                 	    <div class="black-right">
                    	 <a href="#" class="black-edit"></a>
                    	</div>
                      
                 	  <div class="form-inner1">
                      	   <div class="row">
                           		<div class="col6">
                                	 <h3>PERSONLIG INFO</h3>
                                     <div class="form-row">
                                     	  <input type="text" class="form-input1 first_name" placeholder="Anders" value="<?php echo $value['first_name'] ;?>" >
                                     </div>
                                     
                                     <div class="form-row">
                                     	  <input type="text" class="form-input1 last_name" placeholder="Andersen"  value="<?php echo $value['last_name'] ;?>" >
                                     </div>
                                     
                                     <div class="form-row">
                                     	  <input type="text" class="form-input1 password_primary" placeholder="***********"  value="<?php echo $value['password'] ;?>" >
                                     </div>
                                     
                                     <div class="form-row">
                                     	  <input type="text" class="form-input1 password" placeholder="***********" value="<?php echo $value['password'] ;?>" >
                                     </div>
                                </div>
                                
                                <div class="col6">
                                	 <h3>ADRESSE</h3>
                                     <div class="form-row">
                                     	  <div class="row">
                                          	   <div class="col6"><input type="text" class="form-input1 zipcode" placeholder="4220"  value="<?php echo $value['zipcode'] ;?>" ></div>
                                               <div class="col6"><input type="text" class="form-input1 city" placeholder="Korsør" value="<?php echo $value['city'] ;?>" ></div>
                                          </div>
                                     </div>
                                     
                                     <div class="form-row">
                                     	  <input type="text" class="form-input1 address" placeholder="address: eg. Skovvejen 11" value="<?php echo $value['address'] ;?>" >
                                     </div>
                                     
                                     <div class="form-row">
                                     	  <?php // <input type="text" class="form-input1 country" placeholder="Country: eg. Danmark" value="$value['country_id'];" >  ?>
                                         <div class="custom-select">
                                                  <!-- country -->
                                                  <select class="country_id">
                                                    <?php   
                                                      foreach ($country_list as $key => $country) {
                                                        $selected_country = '';
                                                        if($country['id'] == $value['country_id']){
                                                          $selected_country = 'selected=selected';
                                                        }
                                                        echo "<option value='".$country['id']."' ".$selected_country.">".$country['name_dk']."</option>";
                                                      }
                                                    ?>
                                                  </select>
                                                </div>
                                     </div>

                                     <div class="form-row">
                                     	  <input type="text" class="form-input1 address" placeholder="Occupation/Beskæftigelse" value="<?php echo $value['job'] ;?>" >
                                     </div>
                                     
                                </div>
                           </div>
                      </div>
                 </div><!--close form-area-->
                 
                 <div class="form-area">
                 	  <div class="form-inner1">
                      	   <div class="row">
                           		<div class="col6">
                                	 <h3>PERSONLIG INFO</h3>
                                     <div class="form-row">
                                     	  <input type="text" class="form-input1 email" placeholder="hej@hej.dk" value="<?php echo $value['email'] ;?>" >
                                     </div>
                                     
                                     <div class="form-row">
                                     	  <div class="row">
                                          	   <div class="col6"><input type="text" class="form-input1 phone" placeholder="tlf.1" value="<?php echo $value['phone'] ;?>" ></div>
                                               <div class="col6"><input type="text" class="form-input1 phone_at_work" placeholder="tlf.2" value="<?php echo $value['phone_at_work'] ;?>" ></div>
                                          </div>
                                     </div>
                                     
                                     <div class="form-row">
                                     	  <div class="row">
                                              <div class="col6">
                                                <?php // <input type="text" class="form-input1 gender" placeholder="Mand" value="echo $value['gender_id']" > ?>
                                                <div class="custom-select">
                                                  <!-- gender -->
                                                  <select class="gender_id">
                                                    <?php   
                                                      foreach ($gender_list as $key => $gender) {
                                                        $selected_gender = '';
                                                        if($gender['id'] == $value['gender_id']){
                                                          $selected_gender = 'selected=selected';
                                                        }
                                                        echo "<option value='".$gender['id']."' ".$selected_gender.">".$gender['name']."</option>";
                                                      }
                                                    ?>
                                                  </select>
                                                </div>
                                              </div>
                                          </div>
                                     </div>
                                </div>
                                
                                <div class="col6">
                                	 <h3>FØDT</h3>
                                     <div class="form-row">
                                     	  <div class="row">
                                          	   <div class="col4"><input type="text" class="form-input1 birth_day" placeholder="DD" value="<?php echo $value['birth_day'] ;?>" ></div>
                                               <div class="col4"><input type="text" class="form-input1 birth_month" placeholder="MM" value="<?php echo $value['birth_month'] ;?>" ></div>
                                               <div class="col4"><input type="text" class="form-input1 birth_year" placeholder="YYYY" value="<?php echo $value['birth_year'] ;?>" ></div>
                                          </div>
                                     </div>
                                     
                                     <div class="form-row">
                                     	  <input type="text" class="form-input1 ethnic_origin" placeholder="Etninsk oprindelse" value="<?php echo $value['ethnic_origin'] ;?>" >
                                     </div>
                                     
                                </div>
                           </div>
                      </div>
                 </div><!--close form-area-->
                 
                 <div class="form-area">
                 	  <div class="form-inner1 form-inner2">
                      	   <div class="row">
                           		<div class="col12">
                                	 <h3>BETALING</h3>
                                     <div class="form-row">
                                     	  <div class="row payment-type-1">
                                          	   <div class="col3"><span class="percent">25 %</span></div>
                                               <div class="col6"><input type="text" class="form-input1" placeholder="Skriv her.." value="<?php echo $payment_0_description ;?>" ></div>
                                               <div class="col3">
                                               <?php
                                                    $checked_active = "";
                                                    $checked_paid = "";
                                                    if(isset($value['payments']) && count($value['payments'])>0){
                                                      $checked_active = ($value['payments'][0]['applies']) ? 'checked="checked"' : '';
                                                      $checked_paid = ($value['payments'][0]['paid']) ? 'checked="checked"' : '';
                                                    }
                                                    ?>
                                               <div class="check-area">
                                                        <label class="chek-box">Aktiv
                                                          <input class="active" type="checkbox" <?php echo $checked_active;?>>
                                                          <span class="checkmark"></span>
                                                        </label>
                                                    </div>
                                                    
                                                    <div class="check-area">
                                                        <label class="chek-box">Betalt
                                                          <input class="paid" type="checkbox" <?php echo $checked_paid;?>>
                                                          <span class="checkmark"></span>
                                                        </label>
                                                    </div>
                                               </div>
                                          </div>
                                     </div>
                                     
                                     <div class="form-row">
                                     	  <div class="row payment-type-3">
                                          	   <div class="col3"><span class="percent">20 %</span></div>
                                               <div class="col6"><input type="text" class="form-input1" placeholder="Skriv her.." value="<?php echo $payment_2_description ;?>" ></div>
                                               <div class="col3">
                                               <?php
                                                    $checked_active = "";
                                                    $checked_paid = "";
                                                    if(isset($value['payments']) && count($value['payments'])>0){                                               
                                                      $checked_active = ($value['payments'][2]['applies']) ? 'checked="checked"' : '';
                                                      $checked_paid = ($value['payments'][2]['paid']) ? 'checked="checked"' : '';
                                                    }
                                                    ?>
                                               <div class="check-area">
                                                        <label class="chek-box">Aktiv
                                                          <input class="active" type="checkbox" <?php echo $checked_active;?>>
                                                          <span class="checkmark"></span>
                                                        </label>
                                                    </div>
                                                    
                                                    <div class="check-area">
                                                        <label class="chek-box">Betalt
                                                          <input class="paid" type="checkbox" <?php echo $checked_paid;?>>
                                                          <span class="checkmark"></span>
                                                        </label>
                                                    </div>
                                               </div>
                                          </div>
                                     </div>
                                     
                                     <div class="form-row">
                                     	  <div class="row payment-type-2">
                                          	   <div class="col3"><span class="percent">7 %</span></div>
                                               <div class="col6"><input type="text" class="form-input1" placeholder="Skriv her.." value="<?php echo $payment_1_description ;?>" ></div>
                                               <div class="col3">
                                                    <?php
                                                    $checked_active = "";
                                                    $checked_paid = "";
                                                    if(isset($value['payments']) && count($value['payments'])>0){                                                    
                                                      $checked_active = ($value['payments'][1]['applies']) ? 'checked="checked"' : '';
                                                      $checked_paid = ($value['payments'][1]['paid']) ? 'checked="checked"' : '';
                                                    }
                                                    ?>
                                               	    <div class="check-area">
                                                        <label class="chek-box">Aktiv
                                                          <input class="active" type="checkbox" <?php echo $checked_active;?>>
                                                          <span class="checkmark"></span>
                                                        </label>
                                                    </div>
                                                    
                                                    <div class="check-area">
                                                        <label class="chek-box">Betalt
                                                          <input class="paid" type="checkbox" <?php echo $checked_paid;?>>
                                                          <span class="checkmark"></span>
                                                        </label>
                                                    </div>
                                               </div>
                                          </div>
                                     </div>
                                     
                                </div>
                                
                                
                           </div>
                      </div>
                 </div><!--close form-area-->
                 
                 
                 <div class="form-area">
                 	  <div class="form-inner1 form-inner3" style="margin-top:30px;">
                      	   <div class="row physical-info">
                           		
                               <?php
                               if($is_male){
                                 include_once("male_sizes.php");
                               }
                               if($is_female){
                                 include_once("female_sizes.php");
                               }
                               if($is_kid){
                                 include_once("kid_sizes.php");
                               }
                               ?>
                                <div class = "common_sizes" style="clear: both;">
                                <div class="col3">
                                <label class="ansog_select_label">højde</label>
                                	 <div class="">
                                      <!-- height / hojde -->
                                      <input type="text" class="form-input1 height" placeholder="Højde" value="<?php echo $value['height'];?>" >
                                    </div>
                                </div>                                
                                <div class="col3">
                                <label class="ansog_select_label">vægt</label>
                                	 <div class="">
                                      <!-- weight / vaegt -->
                                      <input type="text" class="form-input1 weight" placeholder="Vægt" value="<?php echo $value['weight'];?>" >
                                    </div>
                                </div>
                                <div class="col3">
                                <label class="ansog_select_label">harfarve</label>
                                	 <div class="custom-select">
                                      <!-- Hair color / harfarve -->
                                      <select class="hair_color_id">
                                        <?php   
                                        echo "<option value=''> - </option>";
                                          foreach ($hair_colors_list as $key => $color) {
                                            $selected = '';
                                            if($color['id'] == $value['hair_color_id']){
                                              $selected = 'selected=selected';
                                            }
                                            echo "<option value='".$color['id']."' ".$selected.">".$color['name']."</option>";
                                          }
                                        ?>
                                      </select>
                                    </div>
                                </div>

                                <div class="col3">
                                <label class="ansog_select_label">Øjen farve</label>
                                	 <div class="custom-select">
                                      <!-- eye color / ojen farve -->
                                      <select class="eye_color_id">
                                        <?php   
                                        echo "<option value=''> - </option>";
                                          foreach ($eye_colors_list as $key => $color) {
                                            $selected = '';
                                            if($color['id'] == $value['eye_color_id']){
                                              $selected = 'selected=selected';
                                            }
                                            echo "<option value='".$color['id']."' ".$selected.">".$color['name']."</option>";
                                          }
                                        ?>
                                      </select>
                                    </div>
                                </div>
                              </div>
                           </div>
                      </div>
                 </div><!--close form-area-->
                 
                 
                 <div class="form-area">
                 	  <div class="form-inner5">
                      	   <div class="row">
                           		
                                <div class="textarea-sec">
                                        	 <h3>LIDT OM MIG</h3>
                                             
                                             <div class="form_box">
                                             	  <textarea name="" cols="" rows="" placeholder="Skriv her..." class="textarea2 notes"><?php echo $value['notes']?></textarea>
                               					  <a href="#" class="edit2"></a>
                                                  <span class="words">72</span>
                                             </div>
                                             <h3>SPORT &amp; HOBBY</h3>
                                             <div class="form_box">
                                             	  <textarea name="" cols="" rows="" placeholder="Skriv her..." class="textarea2 sports_hobby"><?php echo $value['sports_hobby']?></textarea>
                               					  <a href="#" class="edit2"></a>
                                                  <span class="words">72</span>
                                             </div>
                                </div>
                                
                                
                                <div class="categories-sec">
                                        	<h3><strong>KATEGORI</strong> <small>/ Vælg én eller flere</small></h3>
                                        	<div class="form_box categories">
                                              <?php
                                                $input_value   =  array();
                                                foreach ($category_list as $key => $category) {
                                                  $element_class = 'button1';
                                                  $span_class    = 'plus-icon';
                                                  if(isset($value['categories']) && in_array($category['id'], $value['categories'])){
                                                    $element_class = 'button2';
                                                    $span_class    = 'close-icon';
                                                    $input_value[] = $category['id'];
                                                  }
                                                  echo '<button href="#" cid="'.$category['id'].'" class="'.$element_class.'">'.$category['name'].' <span class="'.$span_class.'"></span></button>';
                                                }
                                                $category_value = implode(',', $input_value);
                                              ?>
                                              <input type="hidden" name="selectedcategories" id="selectedcategories" value="<?php echo $category_value;?>" class="category_value">
                                          </div>
                                             
                                             <h3>ERFARING</h3>
                                             <div class="form_box skills">
                                                  <?php
                                                    $input_value    = array();
                                                    foreach ($skills_list as $key => $skill) {
                                                      $element_class = 'button1';
                                                      $span_class    = 'plus-icon';
                                                      if(isset($value['skills']) && in_array($skill['id'], $value['skills'])){
                                                        $element_class = 'button2';
                                                        $span_class    = 'close-icon';
                                                        $input_value[] = $skill['id'];
                                                      }
                                                      echo '<button href="#" cid="'.$skill['id'].'" class="'.$element_class.'">'.$skill['name'].' <span class="'.$span_class.'"></span></button>';
                                                    }
                                                    $skill_value  = implode(',', $input_value);
                                                  ?>
                                                  <input type="hidden" name="selectedskills" id="selectedskills" value="<?php echo $skill_value;?>" class="skill_value">
                                             </div>
                                             
                                             <h3>KØREKORT</h3>
                                             <div class="form_box licences">
                                                  <?php
                                                    $input_value   =  array();
                                                    foreach ($licences_list as $key => $licence) {
                                                      $element_class = 'button1';
                                                      $span_class    = 'plus-icon';
                                                      if(isset($value['licenses']) && in_array($licence['id'], $value['licenses'])){
                                                        $element_class = 'button2';
                                                        $span_class    = 'close-icon';
                                                        $input_value[] = $licence['id'];
                                                      }
                                                      echo '<button href="#" cid="'.$licence['id'].'" class="'.$element_class.'">'.$licence['name'].' <span class="'.$span_class.'"></span></button>';
                                                    }
                                                    $licence_value  = implode(',', $input_value);
                                                  ?>
                                                  <input type="hidden" name="selectedlicences" id="selectedlicences" value="<?php echo $licence_value;?>" class="licence_value">
                                             </div>
                                        </div>
                                
                                
                           </div>
                      </div>
                 </div><!--close form-area-->
                 
                 
                 <div class="form-area">
                 	  <div class="form-inner5">
                      	   <div class="row">
                           		
                                <div class="form-area1">
                                	 <h3>SPROG</h3>
                                     <div class="fields">
                                     <?php /* ?>
                                     	  <div class="form-row">
                                     	  	   <input type="text" class="form-input1" placeholder="Dansk*">
                                     	  </div>
                                          <div class="form-row">
                                          	   <div class="custom-select">
                                                  <select>
                                                    <option value="0">Sprog</option>
                                                    <option value="1">Skjorte </option>
                                                    <option value="2">Skjorte </option>
                                                    <option value="3">Skjorte </option>
                                                  </select>
                                                </div>
                                          </div>
                                          
                                          <div class="form-row">
                                          	   <div class="custom-select">
                                                  <select>
                                                    <option value="0">Sprog</option>
                                                    <option value="1">Skjorte </option>
                                                    <option value="2">Skjorte </option>
                                                    <option value="3">Skjorte </option>
                                                  </select>
                                                </div>
                                          </div>
                                          
                                          <div class="form-row">
                                          	   <div class="custom-select">
                                                  <select>
                                                    <option value="0">Sprog</option>
                                                    <option value="1">Skjorte </option>
                                                    <option value="2">Skjorte </option>
                                                    <option value="3">Skjorte </option>
                                                  </select>
                                                </div>
                                          </div>
                                          <?php */ ?>
                                          <?php
                                          $lang_html = '';
                                          // pp($language_list);
                                          // pp($value['languages']);
                                          // echo "\n";
                                          for($i=0; $i < 4; $i++){
                                            $lang_html .= '<div class="form-row">
                                              <div class="custom-select">
                                                <select id="language_id_'. $i .'">
                                                  <option value="0">Sprog</option>';
                                                  foreach($language_list as $key => $lang){
                                                    $selected = '';
                                                    if(isset($value['languages'][$i]) && $lang['id'] == $value['languages'][$i]['lang_id']){
                                                      $selected = 'selected=selected';
                                                      // echo $selected;
                                                    }
                                                    $lang_html .= '<option value="'.$lang['id'].'" '.$selected.'>'.$lang['name'].' </option>';
                                                  }
                                                  $lang_html .= '</select>
                                              </div>
                                            </div>';
                                          }
                                          echo $lang_html;
                                          ?>
                                     </div>
                                </div>
                                
                                
                                <div class="form-area2">
                                	 <h3>Vælg niveau</h3>
                                              <?php
                                              $lang_rate_html = '';
                                              for($i=0; $i < 4; $i++){
                                                if(!isset($value['languages'][$i]) ){
                                                  $value['languages'][$i]['rating'] = 0;
                                                }
                                                  $lang_rate_html .= '<div class="form_box">
                                                    <span class="ratings"><input type="hidden" name="langrateval_'.$i.'" id="langrateval_'.$i.'" value="'.$value['languages'][$i]['rating'].'" class="language_rating_'.$i.'">';
                                                    for($j=1; $j<=4; $j++){
                                                      $img_src = "images/star-gray.png";
                                                      if($value['languages'][$i]['rating'] >= $j ){
                                                        $img_src = "images/star-white.png";
                                                      }
                                                      $lang_rate_html .=  '<img src="'.$img_src.'" ratevalue="'.$j.'">';
                                                    }
                                                    $lang_rate_html .=  '</span>
                                                  </div>';
                                              }
                                              echo $lang_rate_html;
                                              ?>
                                              <?php /*
                                              <div class="form_box">
                                             	  <span class="ratings">
                                                  	    <img src="images/star-white.png">
                                                        <img src="images/star-white.png">
                                                        <img src="images/star-white.png">
                                                        <img src="images/star-white.png">
                                                  </span>
                                              </div>
                                             
                                             <div class="form_box">
                                             	  <span class="ratings">
                                                  	    <img src="images/star-gray.png">
                                                        <img src="images/star-gray.png">
                                                        <img src="images/star-black.png">
                                                        <img src="images/star-black.png">
                                                  </span>
                                             </div>
                                             
                                             <div class="form_box">
                                             	  <span class="ratings">
                                                  	    <img src="images/star-gray.png">
                                                        <img src="images/star-gray.png">
                                                        <img src="images/star-gray.png">
                                                        <img src="images/star-gray.png">
                                                  </span>
                                             </div>
                                             
                                             <div class="form_box">
                                             	  <span class="ratings">
                                                  	    <img src="images/star-gray.png">
                                                        <img src="images/star-gray.png">
                                                        <img src="images/star-gray.png">
                                                        <img src="images/star-gray.png">
                                                  </span>
                                             </div>
                                             <?php */ ?>

                                </div>
                                
                                
                                <div class="form-area3">
                                	 <h3>&nbsp;</h3>
                                     <div class="form_box4">
                                             	  Begynder
                                                  <span class="ratings2">
                                                  	    <img src="images/star-white.png">
                                                        <img src="images/star-gray.png">
                                                        <img src="images/star-gray.png">
                                                        <img src="images/star-gray.png">
                                                  </span>
                                             </div>
                                             
                                             <div class="form_box4">
                                             	  Mellem
                                                  <span class="ratings2">
                                                  	    <img src="images/star-white.png">
                                                        <img src="images/star-white.png">
                                                        <img src="images/star-gray.png">
                                                        <img src="images/star-gray.png">
                                                  </span>
                                             </div>
                                             
                                             <div class="form_box4">
                                             	  Flydende
                                                  <span class="ratings2">
                                                  	    <img src="images/star-white.png">
                                                        <img src="images/star-white.png">
                                                        <img src="images/star-white.png">
                                                        <img src="images/star-gray.png">
                                                  </span>
                                             </div>
                                             
                                             <div class="form_box4">
                                             	  Perfekt
                                                  <span class="ratings2">
                                                  	    <img src="images/star-white.png">
                                                        <img src="images/star-white.png">
                                                        <img src="images/star-white.png">
                                                        <img src="images/star-white.png">
                                                  </span>
                                             </div>
                                             
                                             <h3 style="margin-top:30px;">Dealekter</h3>
                                             <div class="form_box">
                                             	  <input name="dealekter1" type="text" class="form-input1 dealekter1" placeholder="Skriv her" value="<?php echo $value['dealekter1']?>">
                                             </div>
                                             
                                             <div class="form_box">
                                             	  <input name="dealekter2" type="text" class="form-input1 dealekter2" placeholder="Skriv her" value="<?php echo $value['dealekter2']?>">
                                             </div>
                                             
                                             <div class="form_box">
                                             	  <input name="dealekter3" type="text" class="form-input1 dealekter3" placeholder="Skriv her" value="<?php echo $value['dealekter3']?>">
                                             </div>
                                </div>
                                
                           </div>
                      </div>
                 </div><!--close form-area-->
                 
                 <div class="button-area">
                 	<!-- <a class="btn_1 cancel_update" href="/admin">Afvis</a> -->
                  <!-- <a class="btn_2 submit_update" href="#">Godkend</a> -->
                  <button class="btn_2 submit_update godkend_button">Godkend</button>
                  <textarea style="display:none" class="loaded_profile_info"><?php echo $json_profile_info;?></textarea>
                 </div>
                 
                 <div class="toolbar-bottom">
                      <div class="tool-left">
                           <a class="back-btn" href="/admin">Tilbage</a>
                      </div>
                      <div class="tool-right">
                      		<ul>
                            	<li><a href="#">Castingsheet</a></li>
                                <li><a href="#">KALENDER</a></li>
                                <li><a class="media_page_link" href="/admin/profilemedia?id=<?php echo $id; ?>&type=all">FOTO/VIDEO</a></li>
                            </ul>
                      </div>
                 </div>
            </div>
       </div><!--close page-bottom-->
	  
  </div><!--close content-->
 
</div><!--close wrapper--> 

<script src="js/jquery-3.3.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/scripts.js"></script>
<script src="js/jquery.simplePopup.js" type="text/javascript"></script>
<script type="text/javascript">

$(document).ready(function(){

    $('.show1').click(function(){
	$('#pop1').simplePopup();
    });
    
});

</script>

<script src="js/bootstrap-toggle.js"></script>
<script>
var x, i, j, selElmnt, a, b, c;
/*look for any elements with the class "custom-select":*/
x = document.getElementsByClassName("custom-select");
for (i = 0; i < x.length; i++) {
  selElmnt = x[i].getElementsByTagName("select")[0];
  /*for each element, create a new DIV that will act as the selected item:*/
  a = document.createElement("DIV");
  a.setAttribute("class", "select-selected");
  a.innerHTML = selElmnt.options[selElmnt.selectedIndex].innerHTML;
  x[i].appendChild(a);
  /*for each element, create a new DIV that will contain the option list:*/
  b = document.createElement("DIV");
  b.setAttribute("class", "select-items select-hide");
  for (j = 0; j < selElmnt.length; j++) {
    /*for each option in the original select element,
    create a new DIV that will act as an option item:*/
    c = document.createElement("DIV");
    c.innerHTML = selElmnt.options[j].innerHTML;
    c.addEventListener("click", function(e) {
        /*when an item is clicked, update the original select box,
        and the selected item:*/
        var y, i, k, s, h;
        s = this.parentNode.parentNode.getElementsByTagName("select")[0];
        h = this.parentNode.previousSibling;
        for (i = 0; i < s.length; i++) {
          if (s.options[i].innerHTML == this.innerHTML) {
            s.selectedIndex = i;
            h.innerHTML = this.innerHTML;
            y = this.parentNode.getElementsByClassName("same-as-selected");
            for (k = 0; k < y.length; k++) {
              y[k].removeAttribute("class");
            }
            this.setAttribute("class", "same-as-selected");
            break;
          }
        }
        h.click();
    });
    b.appendChild(c);
  }
  x[i].appendChild(b);
  a.addEventListener("click", function(e) {
      /*when the select box is clicked, close any other select boxes,
      and open/close the current select box:*/
      e.stopPropagation();
      closeAllSelect(this);
      this.nextSibling.classList.toggle("select-hide");
      this.classList.toggle("select-arrow-active");
    });
}
function closeAllSelect(elmnt) {
  /*a function that will close all select boxes in the document,
  except the current select box:*/
  var x, y, i, arrNo = [];
  x = document.getElementsByClassName("select-items");
  y = document.getElementsByClassName("select-selected");
  for (i = 0; i < y.length; i++) {
    if (elmnt == y[i]) {
      arrNo.push(i)
    } else {
      y[i].classList.remove("select-arrow-active");
    }
  }
  for (i = 0; i < x.length; i++) {
    if (arrNo.indexOf(i)) {
      x[i].classList.add("select-hide");
    }
  }
}
/*if the user clicks anywhere outside the select box,
then close all select boxes:*/
document.addEventListener("click", closeAllSelect);
</script>

</body>
</html>