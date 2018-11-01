<?php

  require_once '../dbHelper.php';
  require_once 'functions.php';
  $db = new dbHelper();

  $sort_param   = (($_GET['sort'] == 'name')) ? 'first_name' : $_GET['sort'];
  $filter_param = (string) $_GET['filter'];
  $filter_param = trim($filter_param);
  $search_text  = $_SESSION['search_text']  = (isset($_GET['search_text'])) ? $_GET['search_text'] : '';
  $offset       = (isset($_GET['offset'])) ? $_GET['offset'] : 0;
  $offset       = $offset * 10;
  $profiles_array = array();

  switch ($filter_param) {
    case 'online':
      $filter = " AND p.profile_status_id = '1' ";
      break;
    case 'offline':
      $filter = " AND p.profile_status_id = '2' ";
      break;
    case 'pending':
      $filter = " AND p.profile_status_id = '3' ";
      break;
    case 'slet':
      $filter = " AND p.profile_status_id = '4' ";  
      break;
    default:
      $filter = "";
      break;
  }

  if($sort_param == 'number'){
    $sort_param = 'first_name';    
  }

  $order  = " ORDER BY p.".$sort_param." ASC ";
  $limit  = " LIMIT 100 OFFSET ".$offset;

  $query = "SELECT * FROM profiles p WHERE 1 " . $filter . $order . $limit;

  if(isset($search_text) && $search_text!=""){
    $query = "SELECT * FROM profiles p INNER JOIN memberships m ON m.profile_id = p.id WHERE 1 " . $filter . " AND 
    (p.first_name = '".$search_text."' 
    OR p.last_name = '".$search_text."' 
    OR m.profile_number like '%".$search_text."%' 
    OR CONCAT(p.first_name,' ',p.last_name) like '%".$search_text."%' 
    OR p.first_name like '%".$search_text."%' 
    OR p.last_name like '%".$search_text."%' 
    )" . $order . $limit;
    
    /* if(in_array($search_text, ['CM','CF','YM','YF','BM','BF'])){ // A and J are excluded here as it too less for a search input
      $query = "SELECT * FROM profiles p INNER JOIN memberships m ON m.profile_id = p.id WHERE 1 " . $filter . " AND (m.profile_number like '%".$search_text."%')" . $order . $limit;
    } */

  }
  // pp($query);
  $user_profile_query = $db->prepare($query);
  $user_profile_query->execute();
  $row = $user_profile_query->rowCount();
  if ($row > 0){
    foreach ($user_profile_query->fetchAll(PDO::FETCH_ASSOC) as $key => $value) {
      $info['name'] = $value['first_name'] . ' ' . $value['last_name'];

      $info['profile_id'] = $profile_id = ($search_text!="") ? $value['profile_id'] : $value['id'];

      $profile_number = $db->prepare("SELECT profile_number from memberships where profile_id = ".$profile_id." order by version desc limit 1 ");
      $profile_number->execute();
      // pp($profile_number);exit;
      $info['profile_number'] = $profile_number->fetchColumn(0);

      $exists = array_search($info['profile_number'], array_column($profiles_array, 'profile_number'));
      // pp($exists);
      if(isset($exists) && is_numeric($exists)){
        continue;
      }

      $created_at = strtotime($value['created_at']);
      $info['created_at'] = $created_date = date("d.m.y", $created_at);
      $info['valid_till'] = $valid_till = "31.12.2018";
      $info['approved'] = $approved = $value['approved'];
    /*
      * online  : profile-status = 1
      * offline : profile-status = 2
      * pending : profile-status = 3
      * slet    : profile-status = 4
    */
      $info['profile_status'] = $profile_status = $value['profile_status_id'];
    /*
      * online   : approved = 1 ; profile-status = 1 ; profile-number = 1
      * offline  : approved = 1 ; profile-status = 0 ; profile-number = 1
      * pending  : approved = 0 ; profile-status = 1 ; profile-number = 1
      * slet     : approved = 0 ; profile-status = 0 ; profile-number = 1
    */
      $profiles_array[] = $info;
    }
  }
  // pp($profiles_array);  
  if($_GET['sort'] == 'number'){
    $profiles_array = array_sort($profiles_array, 'profile_number');
  }
  // pp($profiles_array);
  $html_data = '';
  if(isset($profiles_array) && count($profiles_array) > 0){
    foreach ($profiles_array as $key => $info) {
      // pp($info);
      if($info['profile_number']){
        switch ($info['profile_status']) {
          case '1':
            $status_info = '<td class="td5"><p><span class="green">Online</span> / Offline / Pending / Slet</p></td>';
            break;
          case '2':
            $status_info = '<td class="td5"><p>Online / <span class="pink">Offline</span> / Pending / Slet</p></td>';
            break;
          case '3':
            $status_info = '<td class="td5"><p>Online / Offline / <span class="orange">Pending</span> / Slet</p></td>';
            break;
          case '4':
            $status_info = '<td class="td5"><p>Online / Offline / Pending / <span class="navy">Slet</span></p></td>';
            break;
          default:
            $status_info = '<td class="td5"><p>Online / Offline / Pending / Slet</p></td>';
            break;
        }

        $html_data .= '<tr>
        <td class="td1"><p><strong>'. $info['name'] .'</strong></p></td>
        <td class="td2"><p>'. $info['profile_number'] .'</p></td>
        <td class="td3"><p>'. $info['created_at'] .'</p></td>
        <td class="td4"><p>'. $info['valid_till'] .'</p></td>
        <td class="td5"><p>'. $status_info .'</td>
        <td class="td6"><p><a href="/admin/profileinfo?id='. $info["profile_id"] .'">Info</a></p></td>
        <td class="td7"><p>Kalender</p></td>
        <td class="td8"><p>Castingsheet</p></td>
        <td class="td9"><p><a href="/admin/profilemedia?id='. $info["profile_id"] .'&type=images">Foto</a></p></td>
        <td class="td10"><p><a href="/admin/profilemedia?id='. $info["profile_id"] .'&type=videos">Video</a></p></td>
        </tr>';
      }
    }
  }
  echo $html_data;