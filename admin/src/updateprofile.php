<?php

require_once '../dbHelper.php';
require_once 'functions.php';
$db = new dbHelper();


$extract_post_variables = extract($_POST);
if($extract_post_variables > 0){
  foreach($_POST as $key => $value){
    if(!is_array($value)){
      if($db->check_column($key, 'profiles')){
        $sql_query = "UPDATE `profiles` SET ".$key."='".$value."' WHERE id = ".$_POST['id'];
        $prepared_query = $db->prepare($sql_query);
        $prepared_query->execute();
      }
      else{
        // pp($key);
        /*
        birth_day
        birth_month
        birth_year
        marked_as_new_from_day
        marked_as_new_from_month
        marked_as_new_from_year
        marked_as_new_till_day
        marked_as_new_till_month
        marked_as_new_till_year 
        */
        
      }
    }
    
  }
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
