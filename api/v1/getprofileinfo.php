<?php
//include database connection file
require_once 'dbHelper.php';
$db = new dbHelper();

$email = explode("email=", $_POST['PHPSESSID'])[1];

$query_string = "SELECT * from profiles WHERE email='".$email."'";
$user_profile_query = $db->prepare($query_string);
$user_profile_query->execute();
$row = $user_profile_query->rowCount();
if ($row > 0){
  foreach ($user_profile_query->fetchAll(PDO::FETCH_ASSOC) as $key => $value) {
    // unset($value['password']);
    // unset($value['hashed_password']);
    
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

    $language_query = $db->prepare("SELECT id, language_proficiency_language_id, language_proficiency_rating_id FROM language_proficiencies where profile_id = $pid");
    $language_query->execute();
    foreach ($language_query->fetchAll(PDO::FETCH_ASSOC) as $lng_item){
      $value['languages'][] = ['lang_id'=>$lng_item['language_proficiency_language_id'], 'rating'=>$lng_item['language_proficiency_rating_id'],'lng_pro_id'=>$lng_item['id']];
    }

    $images_query = $db->prepare("SELECT * FROM photos where profile_id = $pid and position in (0,1,2) ORDER BY position");
    $images_query->execute();
    foreach ($images_query->fetchAll(PDO::FETCH_ASSOC) as $pic_item) {
      $value['pics'][] = [
        'path' => $pic_item['path'],
        'original_path' => $pic_item['original_path'],
        'filename' => $pic_item['filename'],
        'position' => $pic_item['position'],
        'phototype_id' => $pic_item['phototype_id'],
        'image' => $pic_item['image'],
      ];
    }


    $user_profile = json_encode($value);
    echo $user_profile;
  }

} 
else{
  echo 'wrong';
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