<?php

require_once '../dbHelper.php';
require_once 'functions.php';
$db = new dbHelper();

// pp($_POST);
$extract_post_variables = extract($_POST);
if($extract_post_variables > 0){
  foreach($_POST as $key => $value){
    if(!is_array($value)){
      if($db->check_column($key, 'profiles')){
        if($key == 'approved'){ $value = 1 ;}
        $sql_query = "UPDATE `profiles` SET ".$key."='".$value."' WHERE id = ".$_POST['id'];
        $prepared_query = $db->prepare($sql_query);
        $prepared_query->execute();
      }
      /*
      $birth_day                  =   $_POST['birth_day'];
      $birth_month                =   $_POST['birth_month'];
      $birth_year                 =   $_POST['birth_year'];

      $marked_as_new_from_day     =   $_POST['marked_as_new_from_day'];
      $marked_as_new_from_month   =   $_POST['marked_as_new_from_month'];
      $marked_as_new_from_year    =   $_POST['marked_as_new_from_year'];
      
      $marked_as_new_till_day     =   $_POST['marked_as_new_till_day'];
      $marked_as_new_till_month   =   $_POST['marked_as_new_till_month'];
      $marked_as_new_till_year    =   $_POST['marked_as_new_till_year'];

      $birthday = $birth_year .'-'. $birth_month .'-'. $birth_day;
      $marked_as_new_from = strtotime("$marked_as_new_from_year-$marked_as_new_from_month-$marked_as_new_from_day 00:00");
      $marked_as_new_till = strtotime("$marked_as_new_till_year-$marked_as_new_till_month-$marked_as_new_till_day 23:59");

      $sql_query = "UPDATE `profiles` birthday='".$birthday."', marked_as_new_from='".$marked_as_new_from."', marked_as_new_till='".$marked_as_new_till."' WHERE id = ".$_POST['id'];
      $prepared_query = $db->prepare($sql_query);
      $prepared_query->execute();
      */
    }
  }
  pp($_POST['payments']);
  if(isset($_POST['languages']) && count($_POST['languages']) > 0){
    $clear_existance_query = $db->prepare("delete from language_proficiencies where profile_id = ".$_POST['id']);
    $clear_existance_query->execute();
    foreach($_POST['languages'] as $key => $value){
      if($value['language_id'] != 0 && $value['language_id'] != ''){
        $sql_query = "INSERT INTO `language_proficiencies` (`language_proficiency_language_id`,`profile_id`,`language_proficiency_rating_id`,`created_at`,`updated_at`) VALUES ('".$value['language_id']."','".$_POST['id']."','".$value['rating']."',now(),now())";
        $prepared_query = $db->prepare($sql_query);
        $prepared_query->execute();
      }
    }
  }
  if(isset($_POST['payments']) && count($_POST['payments']) > 0){
    $clear_existance_query = $db->prepare("delete from payments where profile_id = ".$_POST['id']);
    $clear_existance_query->execute();
    foreach($_POST['payments'] as $key => $value){
      $sql_query = "INSERT INTO `payments` (`profile_id`,`payment_type_id`,`applies`,`paid`,`description`) VALUES ('".$_POST['id']."','".$value['payment_type_id']."','".$value['applies']."','".$value['paid']."','".$value['description']."')";
      $prepared_query = $db->prepare($sql_query);
      $prepared_query->execute();
    }
  }
  if(isset($_POST['licenses']) && count($_POST['licenses']) > 0){
    $clear_existance_query = $db->prepare("delete from drivers_licenses_profiles where profile_id = ".$_POST['id']);
    $clear_existance_query->execute();
    if(is_array($_POST['licenses'])){
      foreach($_POST['licenses'] as $key => $value){
        $sql_query = "INSERT INTO `drivers_licenses_profiles` (`profile_id`,`drivers_license_id`) VALUES ('".$_POST['id']."','".$value."')";
        $prepared_query = $db->prepare($sql_query);
        $prepared_query->execute();
      }
    }
    else{
      $sql_query = "INSERT INTO `drivers_licenses_profiles` (`profile_id`,`drivers_license_id`) VALUES ('".$_POST['id']."','".$_POST['licenses']."')";
      $prepared_query = $db->prepare($sql_query);
      $prepared_query->execute();
    }
  }

  if(isset($_POST['skills']) && count($_POST['skills']) > 0){
    $clear_existance_query = $db->prepare("delete from profiles_skills where profile_id = ".$_POST['id']);
    $clear_existance_query->execute();
    if(is_array($_POST['skills'])){
      foreach($_POST['skills'] as $key => $value){
        $sql_query = "INSERT INTO `profiles_skills` (`profile_id`,`skill_id`) VALUES ('".$_POST['id']."','".$value."')";
        $prepared_query = $db->prepare($sql_query);
        $prepared_query->execute();
      }
    }
    else{
      $sql_query = "INSERT INTO `profiles_skills` (`profile_id`,`skill_id`) VALUES ('".$_POST['id']."','".$_POST['skills']."')";
      $prepared_query = $db->prepare($sql_query);
      $prepared_query->execute();
    }
  }

  if(isset($_POST['categories']) && count($_POST['categories']) > 0){
    $clear_existance_query = $db->prepare("delete from categories_profiles where profile_id = ".$_POST['id']);
    $clear_existance_query->execute();
    if(is_array($_POST['categories'])){
      foreach($_POST['categories'] as $key => $value){
        $sql_query = "INSERT INTO `categories_profiles` (`profile_id`,`category_id`) VALUES ('".$_POST['id']."','".$value."')";
        $prepared_query = $db->prepare($sql_query);
        $prepared_query->execute();
      }
    }
    else{
      $sql_query = "INSERT INTO `categories_profiles` (`profile_id`,`category_id`) VALUES ('".$_POST['id']."','".$_POST['categories']."')";
      $prepared_query = $db->prepare($sql_query);
      $prepared_query->execute();
    }
  }

  $sql_query = "UPDATE `profiles` SET marked_as_new = ".$_POST['marked_as_new']." WHERE id = ".$_POST['id'];
  $prepared_query = $db->prepare($sql_query);
  $prepared_query->execute();
}

/*
Array
(
    [id] => 3857
    [update_token] => 9d6ab60b-02c8-4a3b-9d2b-df3490aa52ba
    [bureau] => 
    [nationality] => Svensk
    [first_name] => Malin
    [last_name] => Öhman
    [gender_id] => 2
    [hair_color_id] => 3
    [eye_color_id] => 2
    [height] => 176
    [weight] => 55
    [shoe_size_from] => 39
    [shoe_size_to] => 15
    [shirt_size_from] => 36
    [shirt_size_to] => 35
    [address] => Drejøgade 35, 4 sal lej 39
    [zipcode] => 2100
    [city] => København Ø
    [phone] => 0045 61 30 57 15
    [phone_at_work] => 
    [phone_contact_person] => 
    [cellphone] => 
    [fax] => 
    [email] => malin.c_ohman@hotmail.com
    [job] => Læge
    [en_job] => 
    [notes] => asdlskd ljasd jasjd;ask ;ldsad asdlskd ljasd jasjd;ask ;ldsad 

132 asdlskd ljasd jasjd;ask ;ldsad asdasd
    [hands] => 0
    [feet] => 0
    [agreed_to_these_terms] => 1
    [twin] => 0
    [show_as_new_until] => 2014-06-18
    [profile_status_id] => 1
    [approved] => 
    [approved_ip] => 130.225.236.48
    [approved_at] => 2010-02-25 10:33:45
    [password] => 3857lxVA
    [hashed_password] => 0dc12c758beee70517f4752a8e1828de
    [login_info_email_sent] => 1
    [created_at] => 2009-03-09 14:38:33
    [updated_from_frontpage_at] => 2010-02-25 10:34:02
    [profile_status_wish_type_id] => 1
    [suite_size_from] => 44
    [suite_size_to] => 44
    [dealekter2] => 
    [dealekter1] => 
    [dealekter3] => 
    [sports_hobby] => 
    [categories] => Array
        (
            [0] => 6
        )

    [skills] => Array
        (
            [0] => 13
            [1] => 13
            [2] => 13
            [3] => 13
        )

    [licenses] => Array
        (
            [0] => 2
        )

    [languages] => Array
        (
            [0] => Array
                (
                    [lang_id] => 28
                    [rating] => 3
                    [lng_pro_id] => 6398
                )

            [1] => Array
                (
                    [lang_id] => 30
                    [rating] => 4
                    [lng_pro_id] => 6399
                )

            [2] => Array
                (
                    [lang_id] => 113
                    [rating] => 4
                    [lng_pro_id] => 6400
                )

        )

    [payments] => Array
        (
            [0] => Array
                (
                    [payment_type_id] => 1
                    [applies] => 1
                    [paid] => 1
                    [description] => Batalt 17 Libratone
                )

            [1] => Array
                (
                    [payment_type_id] => 2
                    [applies] => 1
                    [paid] => 0
                    [description] => X1
                )

            [2] => Array
                (
                    [payment_type_id] => 3
                    [applies] => 0
                    [paid] => 0
                    [description] => 
                )

        )

    [birth_day] => 00
    [birth_month] => 00
    [birth_year] => 0000
)
*/
