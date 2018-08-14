<?php
// Get cURL resource
$curl = curl_init();
// Set some options - we are passing in a useragent too here
curl_setopt_array($curl, array(
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => 'http://134.213.29.220/api/v1/getsingleprofiles?profileid=646',
    CURLOPT_USERAGENT => 'Codular Sample cURL Request'
));
// Send the request & save response to $resp
$resp = curl_exec($curl);
echo json_encode($resp);
// Close request to clear up some resources
curl_close($curl);

?>
<link rel="stylesheet" type="text/css" href="/assets/css/style1change.css">
<link rel="stylesheet" type="text/css" href="/assets/css/responsive.css">
<link rel="stylesheet" type="text/css" href="/assets/css/custom.css">
<div>


<div id="profile_popup" class="popup popup-video">
 <div class="video_popup">
 
 <div class="video_section1 poup-close3">
      <div class="logo2"><img src="images/logo2.png" alt=""></div>
      <a href="#" class="poup-closeno">luk</a>

 </div><!--close video_section1-->
 
 <div class="video_section2">
  
      
      <div class="video-rightbar">
           <div class="video-rightbar_inner">
           
           
           
                <div class="rightbar-row">
                
                <div class="video-fullscreen">
                     <div id="row-res" class="video_call4 row-res">
                          <div class="rightbar-row">
                            <div class="rightbar-row-row "> 
                            <div class="video-leftbar video-res poup-close3">
                               <div id="imagediv" class="video_side">
                                    <h3>Billeder</h3>
                                    <h4>{{profile_images.length}}</h4>
                               </div>
                  </div> 
                               <div id="image_icons" class="image_icons"> 
                                   <div class="video_call3" ng-repeat="proimage in profile_images">
                                        <span class="video-thumb" ng-style="{'background-image':'url({{proimage.fullpath}})'}">
                                            <!--<img src="images/video-thumb.png" alt="">-->
                                            <span class="img_layer" id="{{'progallerythumb'+ $index }}" ng-click ="changeSingleImageBig(proimage.fullpath)">
                                                <span class="number">{{proimage.imgcnt}}</span>
                                            </span>
                                        </span>
                                   </div>
                               </div>
                           </div>
                           <div class="rightbar-row-row">
                           
                           <div class="video-leftbar video-res poup-close3">
                     <div id="videodiv" class="video_side inactive">
                                    <h3>Videoer</h3>
                                    <h4>{{profile_videos.length}}</h4>
                               </div>
                </div>
                           <div id="video_icons" class="video_icons">
                               <div class="video_call6" ng-repeat="provideo in profile_videos">
                                    <span class="video-thumb" ng-style="{'background-image':'url({{provideo.img_thum}})'}">
                                        <!--<img src="images/video-thumb2.png" alt="">-->
                                        <a href="javascript:;" class="play-button" id="{{'progalleryvideo'+ $index }}" ng-click ="changeSingleVideoBig(provideo.fullpath)"></a>
                                        
                                    </span>
                               </div>
                           </div>    
                           </div>
                          </div><!--close rightbar-row-->
                     </div><!--close video_call4-->
                      </div>
                      
                    <div class="change-video" >
                    
                    <div id="videodiv" class="videodiv video_call4 ">
                    
                      <h3 class="res-name">{{singleprofile.name}}. <strong>{{singleprofile.profile_number}}</strong></h3>
                    
                          <div ng-show = "IsProfileImage" class="videobig_thumb" fade-in style="background-image:url({{singleimage}})">
                         <!-- <img src="images/model-info.png" alt="" id="thumb1" style="margin-top: -20px;">
                           <img src="images/video2.png" alt="" id="thumb2">-->
                          </div>
                          <div  ng-show = "IsProfileVideo" class="videobig_thumb video_margin" >
                          
                          
                              <video id="pro_video"  style="width:100%;max-width:100%;">
                                    <source ng-src="{{currVideoUrl | trusted}}" type="video/mp4">
                                      
                            </video>
                            
                          </div>
                     </div></div><!--close video_call4-->
                     <div  ng-show = "responsiveProfileDetail" class="video_call4 text-res poup-close3">
                          <div class="video_text">
                               <h3>{{singleprofile.name}}. <strong>{{singleprofile.profile_number}}</strong> <span class="user-icon"><img src="images/icon11.png" alt=""></span></h3>
                               <h4>{{singleprofile.job}}</h4>
                               <ul class="list">
                                    <li><span class="list_span1">Alder</span><span class="list_span2">{{singleprofile.age}}</span></li>
                                    <li><span class="list_span1">Højde</span><span class="list_span2">{{singleprofile.height}} cm</span></li>
                                    <li><span class="list_span1">Vægt</span><span class="list_span2">{{singleprofile.weight}} kg</span></li>
                                    <li><span class="list_span1">Øjenfarve</span><span class="list_span2">{{singleprofile.eye_color_name}}</span></li>
                                    <li><span class="list_span1">Hårfarve</span><span class="list_span2">{{singleprofile.hair_color_name}}</span></li>
                                    <li><span class="list_span1">Bluse</span><span class="list_span2">{{singleprofile.shirt}}</span></li>
                                    <li><span class="list_span1">Bukser</span><span class="list_span2">{{singleprofile.pants}}</span></li>
                                    <li><span class="list_span1">Sko</span><span class="list_span2">{{singleprofile.bra}}</span></li>
                               </ul>
                               
                               <h5>FRITID</h5>
                               <p>{{skills}}</p>
                               
                               <h5>Erfaring</h5>
                               <p>{{experiences}}</p>
                               
                               <h5>sprog</h5>
                               <ul class="list2">
                                    <li  ng-repeat="language in lang">
                                    <span class="list_span1">{{language.name}}</span>
                                    <span class="list_span2">
                                         <span ng-repeat="starimg in language.langstar">{{value}}
                                             <img ng-src="images/{{starimg.star}}" alt="" >
                                         </span>
                                    </span>
                                    </li>
                               </ul>
                          </div><!--close video_text-->
                     </div><!--close video_call4-->
                </div><!--close rightbar-row-->
           </div><!--close video-rightbar_inner-->
      </div><!--close video-rightbar-->
      
      </div><!--close video_section2-->
      
 <div class="video_section3">
           <div class="rightbar-row">
                <div class="video_call4">
                     &nbsp;
                </div>
                <div id="photo_nav" class="video_call4" ng-show="IsProfileImage">
                     <div class="video_controller">
                      <a href="#" ng-click="prev()" class="vide_prev"></a>
                      <a href="#"  ng-click="next()" class="vide_next"></a>
                     </div>
                     <span class="span3"><em>{{currentIndex+1}}</em>/{{profile_images.length}}</span>
                </div>
                <div id="video_nav" class="video_call4" style="display:none;" ng-show="profile_videos.length > 1 && IsProfileVideo">
                     <div class="video_controller">
                      <a href="#" ng-click="prev_video()" class="vide_prev"></a>
                      <a href="#"  ng-click="next_video()" class="vide_next"></a>
                     </div>
                      <span class="span3"><em>{{currentIndexVideo+1}}</em>/{{profile_videos.length}}</span>
                </div>
                
                <div class="video_call4">
                     
               
      
 </div>
           </div>
      </div><!--close video_section3-->
      
      
 </div><!--close video_popup-->
</div><!--video popup-->





