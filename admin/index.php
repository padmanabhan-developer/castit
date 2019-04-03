<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Castit</title>
<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
<link href="css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
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
                                <li><a href="#">opret job</a></li>
                                <li><a href="#">too do</a></li>
                                <li><a href="#">tekst</a></li>
                                <li><a href="#">intro billeder</a></li>
                                <li><a href="#">alle profiler</a></li>	
                            </ul>
                        </div><!-- /.navbar-collapse -->
                    </nav>
            </div>
            
            </div>
  </header><!--close header-->
	
  
  <div id="content"> 
	  
       <div class="page-top">
       		<div class="container">
            	 <h2>Profiler</h2>
                 <div class="search-box">
                 	  <input type="text" class="search-input">
                 </div>
            </div>
       </div><!--close page-top-->
       
       
       <div class="page-bottom">
       		<div class="container">
            	 <div class="toolbar">
                 	  <div class="radio radio-info">
                           <input type="radio" name="sort" id="Radios1" value="first_name">
                           <label>Navn</label>
                      </div>
                      
                      <div class="radio radio-info">
                           <input type="radio" name="sort" id="Radios2" value="number">
                           <label>Nr.</label>
                      </div>
                      
                      <div class="radio radio-info">
                           <input type="radio" name="sort" id="Radios3" value="created_at">
                           <label>Oprettet</label>
                      </div>
                      
                      <div class="radio radio-info">
                           <input type="radio" name="sort" id="Radios4" value="show_as_new_until">
                           <label>Opdateret</label>
                      </div>
                      
                      <div class="radio radio-info white-label green-radio">
                           <input type="radio" name="filter" id="Radios5" value="online">
                           <label>Online</label>
                      </div>
                      
                      <div class="radio radio-info white-label pink-radio">
                           <input type="radio" name="filter" id="Radios6" value="offline">
                           <label>Offline</label>
                      </div>
                      
                      <div class="radio radio-info white-label orange-radio">
                           <input type="radio" name="filter" id="Radios7" value="pending">
                           <label>Pending</label>
                      </div>
                      
                      <div class="radio radio-info white-label">
                           <input type="radio" name="filter" id="Radios8" value="bureau">
                           <label>Bureau</label>
                      </div>
                      
                      <div class="radio radio-info white-label">
                           <input type="radio" name="filter" id="Radios9" value="slet">
                           <label>Slettet</label>
                      </div>

                      <div class="radio radio-info white-label">
                           <input type="radio" name="filter" id="Radios10" value="all">
                           <label>Alle</label>
                      </div>
                      
                      <div class="radio radio-info">
                           <input type="radio" name="filter" id="Radios11" value="recent">
                           <label>Recently Updated</label>
                      </div>
                      
                      <div class="right-small-txt"></div>
                 </div>
                 
            	 <div class="table-sec">
                 	  <table cellpadding="0" cellspacing="0" width="100%">
                      <tbody>
                      </tbody>
                    </table>
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
</body>
</html>