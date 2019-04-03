<?php

function pp($q) {
  echo '<pre>'; print_r($q); echo '</pre>';
}

function array_sort($array, $on, $order=SORT_ASC) {
  $new_array = array();
  $sortable_array = array();

  if (count($array) > 0) {
      foreach ($array as $k => $v) {
          if (is_array($v)) {
              foreach ($v as $k2 => $v2) {
                  if ($k2 == $on) {
                      $sortable_array[$k] = $v2;
                  }
              }
          } else {
              $sortable_array[$k] = $v;
          }
      }

      switch ($order) {
          case SORT_ASC:
              asort($sortable_array);
          break;
          case SORT_DESC:
              arsort($sortable_array);
          break;
      }

      foreach ($sortable_array as $k => $v) {
          $new_array[$k] = $array[$k];
      }
  }

  return $new_array;
}

function form_media_inner_html($media_thumb_src = '', $profile_name = '', $profile_number = '', $status = '', $mediatype ='', $media_id='', $gallery_id = 1){
  $checked_status = ($status) ? 'checked="checked"' : '';
  $onclick = ($mediatype == 'image') ? 'openModal();currentSlide('.$gallery_id.')' : '';
  $onclick  = 'openModal();currentSlide('.$gallery_id.')';
  $output = '<div class="product-box" id="'.$media_id.'">
              <div class="product-box-inner">
                <div class="img-box"><img class="profileimg" src="'. $media_thumb_src .'" alt=""><img onclick="'.$onclick.'" class="overlay" src="images/'.$mediatype.'.png" alt=""></div>
                <h3>'. $profile_name .'. '. $profile_number .'</h3>
                <div class="btn-sec">
                  <div class="btnleft">';
                  if($status < 2){
                    $btnleft = '<div class="example">
                      <input type="checkbox" data-toggle="toggle" '.$checked_status.' mediatype="'.$mediatype.'" mediaid="'.$media_id.'">
                    </div>';
                  }
                  else{
                    $btnleft = '<a class="pending-btn" href="#" onclick="openModal();currentSlide('.$gallery_id.')">Pending</a>';
                  }

  $output .=  $btnleft;
  $output .=      '</div>
                  <div class="btnright"><a class="slet-btn" mediaid="'.$media_id.'" mediatype="'.$mediatype.'" href="#">Slet</a></div>
                </div>
              </div>
              
              <div id="profile_media_popup" class="profile_media_popup_hidden" mediaid="'.$media_id.'" mediatype="'.$mediatype.'">
                <div class="popup_profileimg"><img src="'. $media_thumb_src .'"></div>
              </div>
            </div>';
  return $output;
}


function form_images_html($value = array('pics'=>'') , $name, $profile_number, $sortable = false){
    $output = '';
    $mediatype = 'image';
    $count = 0;
    $sort_class = '';
    if($sortable = true){
      $sort_class = "sortable-container";
    }
    
    // $wrap_begin = '<div class="product-sec"><div class="product-row '.$sort_class.'">';
    $wrap_begin = '';
    // $wrap_end   = '</div></div>';
    $wrap_end   = '';
    $modal_begin = '<div id="myModal" class="modal-gallery">
    <span class="close cursor" onclick="closeModal()">&times;</span>
    <div class="modal-gallery-content">';
    $modal_end  = '<a class="prev" onclick="plusSlides(-1)">&#8592;</a>
    <a class="next" onclick="plusSlides(1)">&#8594;</a>
    </div>
    </div>';
    $modal_content = '';
    $output .= $wrap_begin;
    if(isset($value['pics']) && count($value['pics']) > 0){
      foreach ($value['pics'] as $key=>$pic){
        $new_img_file = $_SERVER['DOCUMENT_ROOT'] . '/images/uploads/' . $pic["image"];
        // $new_img_file = 'https://castit.dk/images/uploads/' . $pic["image"];
        // pp($new_img_file);
        if(file_exists($new_img_file)){
          $img_src = '/images/uploads/' . $pic["image"];
        }
        else{
          $img_timestamp = strtotime($pic['created_at']);
          $img_year   = date('Y', $img_timestamp);
          $img_month  = date('m', $img_timestamp);
          $img_day    = date('d', $img_timestamp);
          $img_id     = $pic['id'];
          // $img_src = 'http://' . $_SERVER['SERVER_NAME'] . '/profile_images/' . $img_year . '/' . $img_month . '/' . $img_day . '/' . $img_id . '/big_' . $pic['image'];
          $img_src = 'https://castit.dk/profile_images/' . $img_year . '/' . $img_month . '/' . $img_day . '/' . $img_id . '/big_' . $pic['image'];
          // if(file_exists($old_img_file)){
          //   $img_src = $old_img_file;
          // }
        }
        $status = $pic['published'];
        $checked_status = ($status) ? 'checked="checked"' : '';
        if($img_src != ''){
          $modal_content .= '<div class="mySlides">
          <span class="radio_container">
            <input type="radio" name="update_media_radio" value="on" id="update_media_on" '.$checked_status.' onclick="updatemedia_on('.$pic['id'].', this)" media_type="image">
          </span>
          <span class="radio_container">
          <input type="radio" name="update_media_radio" value="off" id="update_media_off" '.$checked_status.' onclick="updatemedia_off('.$pic['id'].', this)" media_type="image">
            
          </span>
          <span class="radio_container">
          <input type="radio" name="update_media_radio" value="pending" id="update_media_pending" '.$checked_status.' onclick="updatemedia_pending('.$pic['id'].', this)" media_type="image">
            
          </span>
          <span><i class="fas fa-trash-alt"  onclick="updatemedia_delete('.$pic['id'].', this)" media_type="image"></i></span>
          <div class="gallery-image" style="background-image: url(\''.$img_src.'\')"></div>
          <div class="numbertext">'.($key+1).' / '.count($value['pics']).'</div>
          </div>';

          if($count < 600){
            $output .= form_media_inner_html($img_src, $name, $profile_number, $status, $mediatype, $pic['id'], $key+1);
            $count++;
          }
          else{
            $output .= $wrap_end . $wrap_begin;
            $count = 0;
          }
          
        }
      }
    }
    $profile_id = is_numeric($_GET['id']) ? $_GET['id'] : "";

    $output .= $modal_begin.$modal_content.$modal_end;
    return $output;
  }
  
  function form_videos_html($value = array('vids'=>''), $name, $profile_number){
    $output = '';
    $mediatype = 'video';
    $modal_content = '';
    $modal_begin = '<div id="myModal" class="modal-gallery">
    <span class="close cursor" onclick="closeModal()">&times;</span>
    <div class="modal-gallery-content">';
    $modal_end  = '<a class="prev" onclick="plusSlides(-1)">&#8592;</a>
    <a class="next" onclick="plusSlides(1)">&#8594;</a>
    </div>
    </div>';

    if(isset($value['vids']) && count($value['vids']) > 0){
      foreach($value['vids'] as $key => $vid){
        $status = $vid['published'];
        $checked_status = ($status) ? 'checked="checked"' : '';
        $modal_content .= '<div class="mySlides">
        <span class="radio_container">
          <input type="radio" name="update_media_radio" value="on" id="update_media_on" '.$checked_status.' onclick="updatemedia_on('.$vid['id'].', this)" media_type="video">
        </span>
        
        <span class="radio_container">
          <input type="radio" name="update_media_radio" value="off" id="update_media_off" '.$checked_status.' onclick="updatemedia_off('.$vid['id'].', this)" media_type="video">
        </span>
        
        <span class="radio_container">
          <input type="radio" name="update_media_radio" value="pending" id="update_media_pending" '.$checked_status.' onclick="updatemedia_pending('.$vid['id'].', this)" media_type="video">
        </span>
        
        <span><i class="fas fa-trash-alt"  onclick="updatemedia_delete('.$vid['id'].', this)" media_type="video"></i></span>
        
        <video controls id="pro_video" style="width:100%;max-width:100%;" width="640" height="360">
          <source type="video/mp4" src="http://assets3.castit.dk' . $vid['path'] . '/' . $vid['filename'] . '">
        </video>
  
        <div class="numbertext">'.($key+1).' / '.count($value['vids']).'</div>
        </div>';

        $status = $vid['published'];
        $video_dir_path = $vid['path'];
        $video_filename = $vid['filename'];
        $video_thumbnail_path = $vid['thumbnail_photo_path'];
        $video_thumbnail_filename = $vid['thumbnail_photo_filename'];
        $vdo_thumb_url = "http://assets3.castit.dk" . $video_thumbnail_path . '/' . $video_thumbnail_filename;
        $output .= form_media_inner_html($vdo_thumb_url, $name, $profile_number, $status, $mediatype, $vid['id'], $key+1);
      }
      // $vdo_thumb_url = "http://assets3.castit.dk/videos/profiles/2016-03-29/f71e9bde-9521-4a00-a81b-c4329b6a6e70_Maja_v_full.jpg";
  
    }
    $output .= $modal_begin.$modal_content.$modal_end;
    return $output; 
  }

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

function unique_code($limit)
{
  return substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, $limit);
}