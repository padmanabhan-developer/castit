jQuery(document).ready(function() {
	jQuery('.menu-item-has-children > a > .arrow-submenu').click(function(e){
		e.preventDefault();
		jQuery(this).closest('.menu-item-has-children').toggleClass('open_submenu');
		jQuery(this).closest('.menu-item-has-children').find('ul.sub-menu').toggleClass('active_submenu');
	});
});

jQuery(window).scroll(function() {
    if (jQuery(this).scrollTop() > 165){  
        jQuery('header').addClass("sticky");
    }
    else{
        jQuery('header').removeClass("sticky");
    }
});
jQuery(document).ready(function(){
  // Add smooth scrolling to all links
  jQuery("a.scrollto").on('click', function(event) {

    // Make sure this.hash has a value before overriding default behavior
    if (this.hash !== "") {
      // Prevent default anchor click behavior
      event.preventDefault();

      // Store hash
      var hash = this.hash;

      // Using jQuery's animate() method to add smooth page scroll
      // The optional number (800) specifies the number of milliseconds it takes to scroll to the specified area
      jQuery('html, body').animate({
        scrollTop: jQuery(hash).offset().top
      }, 800, function(){
   
        // Add hash (#) to URL when done scrolling (default click behavior)
        window.location.hash = hash;
      });
    } // End if
  });
});

jQuery(document).ready(function(){
  // Add smooth scrolling to all links
  // jQuery("a").on('click', function(event) {

  //   // Make sure this.hash has a value before overriding default behavior
  //   if (this.hash !== "") {
  //     // Prevent default anchor click behavior
  //     event.preventDefault();

  //     // Store hash
  //     var hash = this.hash;

  //     // Using jQuery's animate() method to add smooth page scroll
  //     // The optional number (800) specifies the number of milliseconds it takes to scroll to the specified area
  //     jQuery('html, body').animate({
  //       scrollTop: jQuery(hash).offset().top
  //     }, 800, function(){
   
  //       // Add hash (#) to URL when done scrolling (default click behavior)
  //       window.location.hash = hash;
  //     });
  //   } // End if
  // });
});

$(document).ready(function () {
  let data = '';
  let sitehost    = window.location.host;
  let sitepath    = window.location.pathname;
  let querystring = window.location.search;
  let params      = '';
  let id          = '';
  let mediatype   = '';
  let update_value= '';
  let media_id    = '';

  // load scripts based on page
  // alert(window.location.pathname);
  switch(sitepath){
    case "/admin/list-profiles" :
    case "/admin/" :

      document.querySelector('input[name=sort][value=first_name]').checked = true;
      document.querySelector('input[name=filter][value=all]').checked = true;
      
      // load values on page load
      data    = { sort: 'name', filter: 'all' };
      $.get("/admin/src/getprofiles", data,
        function (response, textStatus, jqXHR) {
          $(".table-sec").find("table").find("tbody").append(response);
          provide_clicks_for_profile_status_update();
        },
        "html"
      ); 
      
      // submit search and display values
      $("input.search-input").keypress(function (event) { 
        if(event.key == 'Enter'){
          let search_text = $("input.search-input").val();
          let sort    = $("input[name=sort]:checked").val();
          let filter  = $("input[name=filter]:checked").val();
          data    = { sort: sort, filter: filter, search_text: search_text };
          if(search_text != ''){
            $.get("/admin/src/getprofiles", data,
            function (response, textStatus, jqXHR) {
              $(".table-sec").find("table").find("tbody").html(response);
              provide_clicks_for_profile_status_update();
            },
            "html"
            );
          }
          else{
            window.location.reload(true);
          }


        }
      });

      // filter and sort values without search
      $("input[type=radio]").on('click', function(){
        let search_text = $("input.search-input").val();
        let sort    = $("input[name=sort]:checked").val();
        let filter  = $("input[name=filter]:checked").val();
        data    = { sort: sort, filter: filter, search_text: search_text };
        $.get("/admin/src/getprofiles", data,
          function (response, textStatus, jqXHR) {
            $(".table-sec").find("table").find("tbody").html(response);
            provide_clicks_for_profile_status_update();
          },
          "html"
        );
      });

      // infinite scrolling of lists till result set end
      let offset = 1;
      $(window).scroll(function(){
        if($(window).scrollTop() >= ($(document).height() - $(window).height() - 1)){
          let search_text = $("input.search-input").val();
          let sort    = $("input[name=sort]:checked").val();
          let filter  = $("input[name=filter]:checked").val();
          data    = { sort: sort, filter: filter, search_text: search_text, offset: offset };
          $.get("/admin/src/getprofiles", data,
            function (response) {
              $(".table-sec").find("table").find("tbody").append(response);
              provide_clicks_for_profile_status_update();
            },
            "html"
          );  
          offset = offset + 1;
        }
      });

      break;

    case "/admin/profileinfo":
    case "/admin/profileinfo/":
    /*
      params = querystring.split("=");
      id  = params[1].replace(/\D+/g, '');
      data = { id: id};
      $.get("/api/v1/getprofileinfo.php", data,
        function (response) {
          console.log(response);
          $(".firstname").val(response.first_name);
          $(".lastname").val(response.last_name);
          $(".password1").val(response.password);
          $(".password2").val(response.password);
          $(".zipcode").val(response.zipcode);
          $(".city").val(response.city);
          $(".address").val(response.address);
          $(".phone_at_work").val(response.phone_at_work);
          $(".phone").val(response.phone);
          $(".gender").val(response.gender);
          $(".email").val(response.email);
          $(".media_page_link").attr("href", "/admin/profilemedia?id=" + response.id);
        },
        "json"
      );
      */

      $(".ratings img").click(function(event){
        let rating = $(this).attr('ratevalue');
        $(this).siblings("input").val(rating);
        $(this).attr('src', 'images/star-white.png');
        $(this).prevAll('img').attr('src', 'images/star-white.png');
        $(this).nextAll('img').attr('src', 'images/star-gray.png');
      });

      // Categories selection
      $(".categories button").click(function(event){
        let clicked_button_class = $(this).attr('class');
        if(clicked_button_class == 'button1'){
          let categories = $('#selectedcategories').val();
          let this_category_id = $(this).attr('cid');
          if(categories != '' && categories != undefined){
            let categories_array = categories.split(',');
  
            if(categories_array.indexOf(this_category_id) == -1){
              categories_array.push(this_category_id);
            }
  
            categories = categories_array.join();
            $('#selectedcategories').val(categories);
          }
          else{
            $('#selectedcategories').val(this_category_id);
          }
          $(this).removeClass('button1').addClass('button2');
          $(this).find('span').removeClass('plus-icon').addClass('close-icon');
        }
        if(clicked_button_class == 'button2'){
          let categories = $('#selectedcategories').val();
          let this_category_id = $(this).attr('cid');
          let categories_array = categories.split(',');
          let index = categories_array.indexOf(this_category_id);
          if(index > -1){
            categories_array.splice(index, 1);
          }
          categories = categories_array.join();
          console.log(categories_array);
          $('#selectedcategories').val(categories);
          $(this).removeClass('button2').addClass('button1');
          $(this).find('span').removeClass('close-icon').addClass('plus-icon');
        }
      });

      // Skills selection
      $(".skills button").click(function(event){
        let clicked_button_class = $(this).attr('class');
        if(clicked_button_class == 'button1'){
          let skills = $('#selectedskills').val();
          let this_skill_id = $(this).attr('cid');
          if(skills != '' && skills != undefined){
            let skills_array = skills.split(',');
            if(skills_array.indexOf(this_skill_id) == -1){
              skills_array.push(this_skill_id);
            }
            skills = skills_array.join();
            $('#selectedskills').val(skills);
          }
          else{
            $('#selectedskills').val(this_skill_id);
          }
          $(this).removeClass('button1').addClass('button2');
          $(this).find('span').removeClass('plus-icon').addClass('close-icon');
        }
        if(clicked_button_class == 'button2'){
          let skills = $('#selectedskills').val();
          let this_skill_id = $(this).attr('cid');
          let skills_array = skills.split(',');
          let index = skills_array.indexOf(this_skill_id);
          if(index > -1){
            skills_array.splice(index, 1);
          }
          skills = skills_array.join();
          console.log(skills_array);
          $('#selectedskills').val(skills);
          $(this).removeClass('button2').addClass('button1');
          $(this).find('span').removeClass('close-icon').addClass('plus-icon');
        }
      });

      // Licences selection
      $(".licences button").click(function(event){
        let clicked_button_class = $(this).attr('class');
        if(clicked_button_class == 'button1'){
          let licences = $('#selectedlicences').val();
          let this_licence_id = $(this).attr('cid');
          if(licences != '' && licences != undefined){
            let licences_array = licences.split(',');
            if(licences_array.indexOf(this_licence_id) == -1){
              licences_array.push(this_licence_id);
            }
            licences = licences_array.join();
            $('#selectedlicences').val(licences);
          }
          else{
            $('#selectedlicences').val(this_licence_id);
          }
          $(this).removeClass('button1').addClass('button2');
          $(this).find('span').removeClass('plus-icon').addClass('close-icon');
        }
        if(clicked_button_class == 'button2'){
          let licences = $('#selectedlicences').val();
          let this_licence_id = $(this).attr('cid');
          let licences_array = licences.split(',');
          let index = licences_array.indexOf(this_licence_id);
          if(index > -1){
            licences_array.splice(index, 1);
          }
          licences = licences_array.join();
          console.log(licences_array);
          $('#selectedlicences').val(licences);
          $(this).removeClass('button2').addClass('button1');
          $(this).find('span').removeClass('close-icon').addClass('plus-icon');
        }
      });

      /*
      $(".cancel_update").click(function(event){
        querystring += '&clearhash=' + Math.random().toString(36).replace('0.', '');
        window.location.href = sitepath + querystring;
      });
      */
      $(".submit_update").click(function(){
        if( $(".password").val() === $(".password_primary").val() ){}
        let loaded_profile_info = JSON.parse($(".loaded_profile_info").val());
        let profile = {};

        profile.first_name  = "";
        profile.last_name  = "";
        profile.password  = "";
        profile.address  = "";
        profile.zipcode  = "";
        profile.city  = "";
        profile.country_id  = "";
        profile.phone_at_work  = "";
        profile.phone  = "";
        profile.email  = "";        
        profile.gender_id  = "";
        profile.hair_color_id  = "";
        profile.eye_color_id  = "";
        // profile.birthday  = "";
        profile.birth_day  = "";
        profile.birth_month  = "";
        profile.birth_year  = "";
        profile.height  = "";
        profile.weight  = "";
        profile.shoe_size_from  = "";
        profile.shoe_size_to  = "";
        profile.shirt_size_from  = "";
        profile.shirt_size_to  = "";
        profile.pants_size_from  = "";
        profile.pants_size_to  = "";
        profile.bra_size  = "";
        profile.job = "";
        // profile.children_sizes  = "";
        profile.notes  = "";
        // profile.updated_at  = "";
        profile.suite_size_from  = "";
        profile.suite_size_to  = "";
        profile.dealekter1  = "";
        profile.dealekter2  = "";
        profile.dealekter3  = "";
        profile.sports_hobby  = "";
        profile.ethnic_origin  = "";

        profile.marked_as_new_from = "";
        profile.marked_as_new_till = "";

        profile.marked_as_new_from_day  = "";
        profile.marked_as_new_from_month  = "";
        profile.marked_as_new_from_year  = "";
        profile.marked_as_new_till_day  = "";
        profile.marked_as_new_till_month  = "";
        profile.marked_as_new_till_year  = "";



        for(let property_name in profile){
          if($("."+property_name) != undefined){
            profile[property_name] = $("."+property_name).val();
          }
        }
        if(document.querySelector('.marked_as_new').checked){
          profile.marked_as_new  = "1";
        }
        else{
          profile.marked_as_new  = "0";
        }

        profile.licenses    = $('#selectedlicences').val().split(',');
        profile.skills      = $('#selectedskills').val().split(',');
        profile.categories  = $('#selectedcategories').val().split(',');

        let payments = [];
        for(let i=0; i<3; i++){
          let payment_type_id = i+1;
          let current_element = $(".payment-type-"+payment_type_id);
          payments[i] = {
            applies: (current_element.find(".active").prop('checked')) ? "1" : "0",
            description: current_element.find("input[type=text]").val(),
            paid: (current_element.find(".paid").prop('checked')) ? "1" : "0",
            payment_type_id: payment_type_id
          };
        }
        profile.payments = payments;

        let languages = [];
        for(let i=0; i<4; i++){
          let language_id = $("#language_id_"+i).val();
          let rating = $("#langrateval_"+i).val();
          if(language_id != '' && rating != ''){
            languages[i] = {
              language_id: language_id,
              rating: rating
            };
          }
        }
        profile.languages = languages;
        // console.log(profile);
        var updated_profile_info = Object.assign({}, loaded_profile_info, profile);
        // console.log(loaded_profile_info.payments);
        // console.log(updated_profile_info);
        var email_sent = false;
        $.post("/admin/src/updateprofile", updated_profile_info,
          function (returndata, textStatus, jqXHR) {
            let update_value = $("input[name=profile-status-value]").val();
            let email_data = {};

            if( (update_value == 1) && ($(".send_activation_email")[0].checked) ) {
              email_data = { type: "activation", first_name: profile.first_name, email: profile.email };
              $.post("/admin/src/emails", email_data,
                function (data, textStatus, jqXHR) {
                  email_sent = true;
                  alert("Profile Updated and eMail sent to user");
                  window.location = window.location;
                }
              );
            }
            
            if( (update_value == 2) && ($(".send_deactivation_email")[0].checked) ) {
              email_data = { type: "deactivation", first_name: profile.last_name, email: profile.email };
              $.post("/admin/src/emails", email_data,
                function (data, textStatus, jqXHR) {
                  email_sent = true;
                  alert("Profile Updated and eMail sent to user");
                  window.location = window.location;
                }
              );
            }

            if( !$(".send_activation_email")[0].checked && !$(".send_deactivation_email")[0].checked ) {
              alert("Profile Updated");
              window.location = window.location;
            }
          },
        );
      });

      /*
      $("input[name=profile-status-value]").change(function() {
          $(".profile-status-value").prop('checked',false);
          $(this).prop('checked',true);
          let profile_id = $(this).attr('profile_id');
          let update_value = $(this).val();
          let data = {profile_id: profile_id, update_value: update_value};
          $(".send-email-notification").attr("profile-status", $(this).val());
          $.post("/admin/src/updateprofilestatus", data, function (data, textStatus, jqXHR) {

          });
      }); 
      */

      $("input[name=profile-status-value]").change(function() {
        let profile_id = $(this).attr('profile_id');
        let update_value = $(this).val();
        let profile_number = '';
        profile_number = $("input[name=profile-number-selection]").val();
        // let update_value = 1;
        let data = {profile_id: profile_id, update_value: update_value, profile_number: profile_number};
        // $(".send-email-notification").attr("profile-status", $(this).val());
        $.post("/admin/src/updateprofilestatus", data, function (returndata, textStatus, jqXHR) {

        });
      });

      /*
      $(".send-email-notification").click(function() {
        let profile_id = $(this).attr('profile_id');
        let current_status = $(this).attr('profile-status');
        let data = {profile_id: profile_id, current_status: current_status, type: 'status'};
        $.post("/admin/src/emails", data,
          function (data, textStatus, jqXHR) {
            console.log(data);
          }
        );
      });
      */
      break;
    
    case "/admin/profilemedia":
      params    = querystring.split("&");
      id        = params[0].split('=')[1];
      if(params[1] != undefined){
        mediatype = params[1].split('=')[1];
      }
      else{
        mediatype = 'all';
      }
      
      data = { id: id, mediatype: mediatype};
      
      loadMediaData(data);

      $("input[type=radio][name=mediatype]").on('click', function(){
        if($(this).val() == 'all'){
          mediatype = 'all';
          data = { id: id, mediatype: mediatype};
          loadMediaData(data);
        }
        if($(this).val() == 'images'){
          mediatype = 'images';
          data = { id: id, mediatype: mediatype};
          loadMediaData(data);          
        }
        if($(this).val() == 'videos'){
          mediatype = 'videos';
          data = { id: id, mediatype: mediatype};
          loadMediaData(data);
        }
      });

      $(document).on('click.bs.toggle', 'div[data-toggle^=toggle]', function(e) {
        let toggle_state = $(this.firstChild.checked);
        media_id = $(this.firstChild).attr('mediaid');
        mediatype = $(this.firstChild).attr('mediatype');
        if(toggle_state[0] === true){
          update_value = 1;
        }
        else{
          update_value = 0;
        }
        data = {media_id: media_id, mediatype: mediatype, update_value: update_value}
        $.post("/admin/src/updatemediastatus", data,
          function (data, textStatus, jqXHR) {
            // 
          }
        );
        e.preventDefault();
      });

      $(document).on("click", ".slet-btn", function(event){
        console.log($(this));
        console.log(event.target);
        media_id  = $(this).attr('mediaid');
        mediatype = $(this).attr('mediatype');
        data = {media_id: media_id, mediatype: mediatype};
        $.post("/admin/src/markmediadeleted", data,
          function (data, textStatus, jqXHR) {
            window.location.reload();
          }
        );
        
      });

      break;
  }
});

$(document).ready(function(){
  let uploadmediatype = $('input[type=radio][name=uploadmediatype]').val();
  
  $('input[type=radio][name=uploadmediatype]').change(function(){
    uploadmediatype = $(this).val();
    if(uploadmediatype == "image"){
      $(".uploadfilefield").attr("accept", 'image/*');
    }
    if(uploadmediatype == "video"){
      $(".uploadfilefield").attr("accept", '.mp4, .avi, .mkv, .mpeg, .mov, .m4v, .wmv, video/*');
    }
  });

  $('input[type=radio][name=profile-number-selection]').change(function(){
    let new_profile_number = $(this).val();
    if(new_profile_number != undefined){
      let profileid = $(this).attr('profile_id');
      let data = {new_profile_number: new_profile_number, profileid: profileid};
      $.post("/admin/src/updateprofilenumber", data,
        function (data, textStatus, jqXHR) { 
          window.location.reload();
        }
      );
    }
  });
  
  $('#complete').click(function(){
    window.location.reload();
  });

  $(".upload-btn").click(function(){
    document.getElementById('uploadmediaform').reset();
  });
 
  $('#upload').click(function(){
      $(".ajax_loading_container").fadeIn('fast');
      $("#upload").attr("disabled","true");
      var fd = new FormData();
      var files = $('#file')[0].files[0];
      let profileId = $(this).attr('profile-id');
      
      fd.append('file',files);
      fd.append('request',1);
      fd.append('profile_id', profileId);

      
      // AJAX request
      $.ajax({
          url: '/admin/src/'+ uploadmediatype +'upload',
          type: 'post',
          data: fd,
          contentType: false,
          processData: false,
          success: function(response){
            // console.log(response);
            $(".ajax_loading_container").hide();
            $("#upload").removeAttr("disabled");
            response = JSON.parse(response);
            // console.log(response);
            // console.log(response.imgpath);
            if(response != 0){
                  // Show image preview
                  if(uploadmediatype == 'image'){
                    $('#preview').append("<img src='"+response.imgpath+"' width='100' height='100' style='display: inline-block;'>");
                    
                  }
                  else{
                    $('#preview').append("<div class='video_upload_message'><p>Video Uploaded and is being processed by Zencoder. <p>This video will become available in short time.</p></p><p>Please continue uploading new videos or close this form.</p></div>");
                    
                    setTimeout(function(){
                      $('.video_upload_message').fadeOut();
                      $('.video_upload_message').html("");
                    }, 10000)
                  }
              }else{
                  alert('file not uploaded');
              }
          },
          complete: function (jqXHR, status) {
            $('#file').val(''); 
            $("#upload").removeAttr("disabled");
          }
        
      });
  });


});

function loadMediaData(data){
  $.get("/admin/src/getmedia", data,
    function (data, jsondata) {
      $(".profile_caption").html(data.jsonvalue.name + " ." + data.jsonvalue.profile_number);
      $(".mediarow").html(data.htmloutput);  
      $("[data-toggle='toggle']").bootstrapToggle('destroy');
      $("[data-toggle='toggle']").bootstrapToggle();
    }, "json"
  );

  $("input[type=radio][name=mediatype][value="+data.mediatype+"]").attr('checked', true);
  if(data.mediatype == 'images' || data.mediatype == 'videos'){
    let mediatype = data.mediatype;
    $(".mediarow").addClass("sortable-media");
    $(".sortable-media").sortable({
      tolerance: "pointer",
      helper : 'clone', // somehow this prevents unwanted click event when element is drag-dropped
      items : '> :not(#myModal)',
      revert: true,
      update: function( event, ui ) {
        $("img.overlay").addClass("dont-click");
        let new_order = $('.sortable-media').sortable('toArray');
        let data = {new_order: new_order, mediatype: mediatype};
        $.post("/admin/src/updatemediaposition", data,
          function (data, textStatus, jqXHR) { }
        );
      }
    });
  }
  else{
    $(".mediarow").removeClass("sortable-media");
  }
  
}

function show_image_popup(element) {
  // console.log(element);
  // console.log($(element).parent().siblings());
  let popup_element = $(element).parent().siblings();
  popup_element.removeClass('profile_media_popup_hidden');
  popup_element.addClass('profile_media_popup_display');

  $("#profile_media_popup").click(function(){
    $(this).removeClass('profile_media_popup_display');
    $(this).addClass('profile_media_popup_hidden');
  });

}

function openModal() {
    document.getElementById('myModal').style.display = "block";
    $(".radio_container input[checked=checked]").prop('checked', true);
}

function closeModal() {
    document.getElementById('myModal').style.display = "none";
}

// var slideIndex = 1;
// showSlides(slideIndex);

function plusSlides(n) {
  showSlides(slideIndex += n);
}

function currentSlide(n) {
  showSlides(slideIndex = n);
}

function showSlides(n) {
  var i;
  var slides = document.getElementsByClassName("mySlides");
  // var dots = document.getElementsByClassName("demo");
  // var captionText = document.getElementById("caption");
  if (n > slides.length) {slideIndex = 1}
  if (n < 1) {slideIndex = slides.length}
  for (i = 0; i < slides.length; i++) {
      slides[i].style.display = "none";
  }
  // for (i = 0; i < dots.length; i++) {
  //     dots[i].className = dots[i].className.replace(" active", "");
  // }
  slides[slideIndex-1].style.display = "block";
  // dots[slideIndex-1].className += " active";
  // captionText.innerHTML = dots[slideIndex-1].alt;
}

function updatemedia_on(media_id, element){
  element = $(element);
  media_type = element.attr("media_type");
  data = {media_id: media_id, mediatype: media_type, update_value: 1}
  $.post("/admin/src/updatemediastatus", data,
    function (data, textStatus, jqXHR) {
      // 
    }
  );
}
function updatemedia_off(media_id, element){
  element = $(element);
  media_type = element.attr("media_type");
  data = {media_id: media_id, mediatype: media_type, update_value: 0}
  $.post("/admin/src/updatemediastatus", data,
    function (data, textStatus, jqXHR) {
      // 
    }
  );
}
function updatemedia_pending(media_id, element){
  element = $(element);
  media_type = element.attr("media_type");
  data = {media_id: media_id, mediatype: media_type, update_value: 2}
  $.post("/admin/src/updatemediastatus", data,
    function (data, textStatus, jqXHR) {
      // 
    }
  );
}
function updatemedia_delete(media_id, element){
  element = $(element);
  media_type = element.attr("media_type");
  data = {media_id: media_id, mediatype: media_type};
  $.post("/admin/src/markmediadeleted", data,
    function (data, textStatus, jqXHR) {
      window.location.reload();
    }
  );
}

function provide_clicks_for_profile_status_update(){
  $(".set-online").unbind('click').click(function (event) {
    let tr_element = $(this).parent().parent().parent();
    let profile_id = $(this).parent().attr('profileid');
    let update_value = 1;
    let data = {profile_id: profile_id, update_value: update_value}
    $.post("/admin/src/updateprofilestatus", data,function (data, textStatus, jqXHR) {
      data_2    = {profile_id: profile_id, singleselect: 1};
      $.get("/admin/src/getprofiles", data_2,
        function (response, textStatus, jqXHR) {
          tr_element.replaceWith(response);
          provide_clicks_for_profile_status_update();
        },
        "html"
      );
    } );
  });
  $(".set-offline").unbind('click').click(function (event) {
    let tr_element = $(this).parent().parent().parent();
    let profile_id = $(this).parent().attr('profileid');
    let update_value = 2;
    let data = {profile_id: profile_id, update_value: update_value}
    $.post("/admin/src/updateprofilestatus", data,function (data, textStatus, jqXHR) {
      data_2    = {profile_id: profile_id, singleselect: 1};
      $.get("/admin/src/getprofiles", data_2,
        function (response, textStatus, jqXHR) {
          tr_element.replaceWith(response);
          provide_clicks_for_profile_status_update();
        },
        "html"
      );
    } );
  });
  $(".set-pending").unbind('click').click(function (event) {
    let tr_element = $(this).parent().parent().parent();
    let profile_id = $(this).parent().attr('profileid');
    let update_value = 3;
    let data = {profile_id: profile_id, update_value: update_value}
    $.post("/admin/src/updateprofilestatus", data,function (data, textStatus, jqXHR) {
      data_2    = {profile_id: profile_id, singleselect: 1};
      $.get("/admin/src/getprofiles", data_2,
        function (response, textStatus, jqXHR) {
          tr_element.replaceWith(response);
          provide_clicks_for_profile_status_update();
        },
        "html"
      );
    } );
  });
  $(".set-slet").unbind('click').click(function (event) {
    if(window.confirm("Are you sure you want to DELETE this profile?")){
      let tr_element = $(this).parent().parent().parent();
      let profile_id = $(this).parent().attr('profileid');
      let update_value = 4;
      let data = {profile_id: profile_id, update_value: update_value}
      $.post("/admin/src/updateprofilestatus", data,function (data, textStatus, jqXHR) {
        data_2    = {profile_id: profile_id, singleselect: 1};
        $.get("/admin/src/getprofiles", data_2,
          function (response, textStatus, jqXHR) {
            tr_element.replaceWith(response);
            provide_clicks_for_profile_status_update();
          },
          "html"
        );
      } );
    }
  });
}