<?php require_once("../db_config.php"); ?>
<?php require_once("../db_connect.php"); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Park Compass | A Vancouver Park Finder</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="description" content="The easiest way to discover Vancouver's parks. Park Compass helps you find parks in Vancouver using geolocation, search, and filtering by park facility."/>
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1.0"> -->
    <!--[if lt IE 9]> <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
    <link rel="shortcut icon" type="image/x-icon" href="img/favicon.ico">
    <meta http-equiv="x-ua-compatible" content="IE=edge" />
    <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />

    <!--(if target dev)><!--><link rel="stylesheet" type="text/css" href="../css/html5-reset.css">
     <link rel="stylesheet" type="text/css" href="../css/icomoon.css">
    <link rel="stylesheet" type="text/css" href="../css/global.css">
    <link rel="stylesheet" type="text/css" href="../css/mobile.css"><!--<!(endif)-->
    <!--(if target dist)><link rel="stylesheet" type="text/css" href="css/{{pkgName}}.{{pkgVersion}}.min.css"><!(endif)-->

    <link href='http://fonts.googleapis.com/css?family=Cabin:400,700' rel='stylesheet' type='text/css'>
    <script src="js/infobubble.js"></script>

    <script type="text/javascript">
        var _gaq = _gaq || [];
        _gaq.push(['_setAccount', 'UA-35575781-1']);
        _gaq.push(['_trackPageview']);
        (function() {
            var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
            ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
        })();
    </script>
</head>

<body>


    
    <div id="content" class="absolute">
<!-- //////////////////////////////////////////////////////
                    HEADER
    /////////////////////////////////////////////////////////--> 
        <header>
            <div class="icon-filter" id="ol"><span class="filter-count hidden"></span></div>
            <h1><a href="/" class="icon-pc-logo">PARK COMPASS</a></h1>
            <div class="icon-reorder" id="or"></div>
            <!--
<nav>
                <a href="about">about</a>
                <a href="#"><img src="img/info_icon.png" width="17" height="17" alt="info"></a>
            </nav>
-->
        </header>
        <div class="relative tall">
            <section id="location_search_area"><!-- locate/search -->
                <div id="geolocate_btn" class="icon-location"></div>
                
                <div id="location_search_bar" >
                    <input type="text" id="location_search_input" placeholder="Enter a location" >
                    <!-- <div onclick="searchLocations()" id="location_search_submit" class="icon-search"/></div> -->
                    <div id="location_search_submit" class="icon-search"/></div>
                </div>
                
            </section>
            
            
            <!-- //////////////////////////////////////////////////////
                    MAP
    /////////////////////////////////////////////////////////-->
        
            <div id="map_frame"  data-snap-ignore>
                <div id="map_canvas"></div>
            </div>
        </div>
        
        <!--
<div id="bottom-slide">
            <div id="bottom-slide-close" class="icon-cancel"></div>
            <div id="bottom-slide-content"></div>
        </div>
-->
    </div>
    
<!-- //////////////////////////////////////////////////////
                    SIDEBAR
    /////////////////////////////////////////////////////////--> 

            
        
    
        
        <!--
<div id="content" class="absolute">
            <div class="relative tall">
                <div id="toolbar" class="absolute"></div>
                
            </div>
        </div>
-->
    
    
    <section id="sidebar" class="right-drawer absolute scrollable">

        
        
           
        <section id="search_results_wrap"><!-- Search Results -->
            <p id="showing_results_for">Showing results near: <span id="showing_results_for_span"></span></p>
            <ul id="search_results_list" style="width:100%;visibility:hidden"></ul>
        </section>
        
    </section>
    
    <div id="sidebar_backing"></div>
    
    </div>
    
    <div class="drawers absolute">
        <div id="left-drawer" class="left-drawer absolute">
            <h2 class="drawer-title">Facility Filters</h2>
        <a href="#" id="remove_all_filters_btn">remove all filters</a>
         <section id="facilities_flyout"><!-- facility tags -->
            <?php
                $query = 'SELECT FacilityID, FacilityType FROM Facilities';
                
                $result = mysql_query($query);
                if (!$result) {
                  die('Invalid query: ' . mysql_error());
                }
                
                while ($row = @mysql_fetch_assoc($result)){
                    if ($row['FacilityID'] == 0) {
                        echo '';
                    } else {
                        echo '<a href="#" id="filter'.$row['FacilityID'].'">'.$row['FacilityType'].'</a>';
                    }
                }
            ?>
        </section>

            
    </div>
    

    
    


<!-- //////////////////////////////////////////////////////
                    HELP MODAL
    /////////////////////////////////////////////////////////-->
    
    <!--
<div id="help_modal_screen_cover" class="help_modal"></div>
    <div id="help_modal" class="help_modal">
        <a href="#" id="help_modal_close">Get Started!</a>
    </div>
-->
        
    <!-- //////////////////////////////////////////////////////
                    JAVASCRIPT
    /////////////////////////////////////////////////////////--> 
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
    <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true&libraries=places"></script>
    <!--(if target dev)><!-->
    <script src="../js/map.js"></script>
    <script src="../js/markercluster.js"></script>
    <script src="../js/jquery.cookie.js"></script>
    <!--<!(endif)-->
    <!--(if target dist)>
    <script src="js/{{pkgName}}-libs.{{pkgVersion}}.min.js"></script>
    <script src="js/{{pkgName}}-app.{{pkgVersion}}.min.js"></script>
    <!(endif)-->
    <script src="../js/snap.js"></script>
        <script type="text/javascript">
        var updateLog = function(sn){

            var log =  window.parent.document.getElementById('log') ;
            if(log){
                var state = sn.state(),
                    theState = 'State: '+state.state+"\n";
                
                theState += 'Opening: '+state.info.opening+"\n";
                theState += 'Towards: '+state.info.towards+"\n";
                theState += 'HyperExtending: '+state.info.hyperExtending+"\n";
                theState += 'HalfWay: '+state.info.halfway+"\n";
                theState += 'Flickable: '+state.info.flick+"\n";
                theState += 'Translation.absolute: '+state.info.translation.absolute+"\n";
                theState += 'Translation.relative: '+state.info.translation.relative+"\n";
                theState += 'Translation.sinceDirectionChange: '+state.info.translation.sinceDirectionChange+"\n";
                
                log.value=theState;
            }
        }

            var snapper = new Snap({
                element: document.getElementById('content'),
                minDragDistance: 999999
            });
            
            UpdateDrawers = function(){
    			var state = snapper.state(),
    				towards = state.info.towards,
    				opening = state.info.opening;
    			if(opening=='right' && towards=='left'){
    				document.getElementById('sidebar').classList.add('active-drawer');
    				document.getElementById('left-drawer').classList.remove('active-drawer');
    			} else if(opening=='left' && towards=='right') {
    				document.getElementById('sidebar').classList.remove('active-drawer');
    				document.getElementById('left-drawer').classList.add('active-drawer');
    			}
    		};
    		
    		snapper.on('drag', UpdateDrawers);
    		snapper.on('animating', UpdateDrawers);
    		snapper.on('animated', UpdateDrawers);

            snapper.on('drag', function(){
                updateLog(snapper);
            }).on('animated', function(){
                updateLog(snapper);
            });

			document.getElementById('ol').addEventListener('click', function(){
                    if( snapper.state().state=="left" ){
                        snapper.close();
                    } else {
                        snapper.open('left');
                    }
				
			});
			
			document.getElementById('or').addEventListener('click', function(){
                    if( snapper.state().state=="right" ){
                        snapper.close();
                    } else {
                        snapper.open('right');
                    }
			});

        </script>
    
    <script type="text/javascript">



        
        // adds autocomplete function to the main search bar
        function location_autocomplete() {
            var defaultBounds = new google.maps.LatLngBounds(
            new google.maps.LatLng(47.475123, - 135.28857),
            new google.maps.LatLng(60.862735, - 54.725824));
            var input = document.getElementById('location_search_input');
            var options = {
                bounds: defaultBounds,
            };
            autocomplete = new google.maps.places.Autocomplete(input, options);

            google.maps.event.addListener(autocomplete, 'place_changed', function () {
                searchLocations();
        });
        }
        
       
 function first_visit_cookie() {
            var firstvisit = true;
            if ($.cookie('parkcompass_first_visit')) {
                firstvisit = false;
            } else {
                $.cookie('parkcompass_first_visit', true, { expires: 7 });
                $('.help_modal').show();
            }
        }

        
        function twitter_button(direct_link) {	
            $('#twitter_share_btn').attr('href', 'https://twitter.com/intent/tweet?text=Check%20out%20this%20link:%20&url=' + direct_link + '&hashtags=sshareit');
            
            $('#twitter_share_btn').click(function() {
            newwindow=window.open($(this).attr('href'),'','height=400px, width=500px');
            if (window.focus) {newwindow.focus()}
            return false;
            });
        
        }
        
        function email_button(direct_link) {
        $('#email_share_btn').attr('href', "mailto:<?php echo $youremailaddress ?>?subject=Park%20Compass%20Link:%20" + direct_link + '&body=' + direct_link);
        }
    
        $(document).ready(function() {
        
            $('#bottom-slide-close').click(function(e) {
                e.preventDefault();
                $('#bottom-slide').removeClass('active');
            });
        			
            var lat = 49.25;
            var lng = -123.133333;
            <?php
            if (isset($_GET['slug'])) {
                $slug = $_GET['slug'];
                $sql = "SELECT ParkID, ParkLat, ParkLng FROM Parks WHERE slug='$slug'";
                $result = mysql_query($sql);
                if (mysql_num_rows($result)!=0) {
                    while ($row = @mysql_fetch_assoc($result)){ 
                            $lat=$row['ParkLat'];
                            $lng=$row['ParkLng']; }; ?>
            var lat = <?php echo $lat ?>;
            var lng = <?php echo $lng ?>;
            $(window).bind("load",function() {
            map.setZoom(15);
            $('#0 h2').click(); 
            });
            <?php }} ?>
            
            load(lat, lng);
        
            location_autocomplete();
             first_visit_cookie(); 
            
            
            
            // ============ SEARCH TOOLS =======================================
            
            $('#geolocate_btn').click(function() {
                geolocation();
            });
            
            $('#location_search_input').keypress(function(e) {
                if(e.which == 10 || e.which == 13) {
                    searchLocations();
                }
            });
            
            $('#location_search_submit').click(function(e) {
                e.preventDefault();
                if ($('#location_search_area').hasClass('active')) {
                    // $('#location_search_area').removeClass('active');
                    searchLocations();
                } else {
                    $('#location_search_area').addClass('active');
                }
                
            });

            
           /*
 $('#help_modal_close').click(function() {
                 $('.help_modal').addClass('help_modal_minimize');
                 $('.help_modal').delay(500).queue(function() {
                    $(this).hide();
                    $(this).dequeue();
                });
            });
*/
            /*
$('#0').click(function() {
                alert('hey');
                //google.maps.event.trigger(markers[0], 'click');
                });
            $('#0').delay(3000).trigger('click');
*/
            
        });
        <?php if (isset($_GET['park'])) {  ?>
        
        <?php } ?>
    </script>
</body>
</html>
