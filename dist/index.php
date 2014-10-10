<?php
    require_once("db_config.php");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Park Compass | A Vancouver Park Finder</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="description" content="The easiest way to discover Vancouver's parks. Park Compass helps you find parks in Vancouver using geolocation, search, and filtering by park facility."/>
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1.0"> -->
    <!--[if lt IE 9]>
    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <link rel="shortcut icon" type="image/x-icon" href="img/favicon.ico">

    
    <link rel="stylesheet" type="text/css" href="css/html5-reset.1.0.0.min.css">
      <link rel="stylesheet" type="text/css" href="css/styles.1.0.0.min.css">

    <!-- <link rel="stylesheet/less" type="text/css" href="css/styles.less"> -->
    <link href='http://fonts.googleapis.com/css?family=Cabin:400,700' rel='stylesheet' type='text/css'>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
    <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true&libraries=places"></script>
    <!-- <script src="js/less-1.3.0.min.js"></script> -->

    

    
    <script src="js/parkcompass-libs.1.0.0.min.js"></script>
    <script src="js/parkcompass-app.1.0.0.min.js"></script>
    
    
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
<!-- //////////////////////////////////////////////////////
                    HEADER
    /////////////////////////////////////////////////////////--> 
    
    <header>
        <h1><a href="/">PARK COMPASS</a><span></span></h1>
        <nav>
            <a href="about">about</a>
            <a href="#"><img src="img/info_icon.png" width="17" height="17" alt="info"></a>
        </nav>
    </header>
    
<!-- //////////////////////////////////////////////////////
                    SIDEBAR
    /////////////////////////////////////////////////////////--> 
    
    <section id="sidebar">

        <section id="location_search_area"><!-- locate/search -->
            <a href="#" id="geolocate_btn"><img src="img/geolocate_icon.png" width="28" height="28" alt="geolocate icon">Find Parks Near You</a>
            
            <div id="location_search_bar">
                <input type="text" id="location_search_input" placeholder="Enter an address or place">
                <input type="button" onclick="searchLocations()" value="Search" id="location_search_submit"/>
            </div>
            
            <a href="#" id="filter_facilities_btn">Filter by Facility<img src="img/faciltiy_triangle.png" width="6" height="11" id="facility_triangle"></a><!-- TODO: Correct spelling of FACILITY in the src! -->
            <a href="#" id="remove_all_filters_btn">remove all filters</a> <!-- hidden until a facility tag is selected -->
        </section>
        
        <section id="facilities_flyout"><!-- facility tags -->
            <?php
                $connection = mysql_connect ($hostname, $username, $password);
                if (!$connection) {  die('Not connected : ' . mysql_error());} 
                
                $db_selected = mysql_select_db($database, $connection);
                if (!$db_selected) {
                  die ('Can\'t use db : ' . mysql_error());
                }
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
    
        <section id="search_results_wrap"><!-- Search Results -->
            <p id="showing_results_for">Showing results near: <span id="showing_results_for_span"></span></p>
            <ul id="search_results_list" style="width:100%;visibility:hidden"></ul>
        </section>
        
    </section>
    
    <div id="sidebar_backing"></div>
    
    
<!-- //////////////////////////////////////////////////////
                    MAP
    /////////////////////////////////////////////////////////-->
    
    <div id="map_frame">
        <div id="map_canvas"></div>
    </div>

    
<!-- //////////////////////////////////////////////////////
                    HELP MODAL
    /////////////////////////////////////////////////////////-->
    
    <div id="help_modal_screen_cover" class="help_modal"></div>
    <div id="help_modal" class="help_modal">
        <a href="#" id="help_modal_close">Get Started!</a>
    </div>
        
    <!-- //////////////////////////////////////////////////////
                    JAVASCRIPT
    /////////////////////////////////////////////////////////--> 
    
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
        $('#email_share_btn').attr('href', "mailto:<?php echo $youremailaddress?>?subject=Park%20Compass%20Link:%20" + direct_link + '&body=' + direct_link);
        }
    
        $(document).ready(function() {
        
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
            // ============ FACILITIES BTN =======================================
            
            $('#filter_facilities_btn').click(function() {
               $('#facilities_flyout').slideToggle(100, function() {
                   if ($('#facilities_flyout').is(':visible')) {
                       $('#facility_triangle').addClass('triangle_down');
                   } else {
                       $('#facility_triangle').removeClass('triangle_down');
                   }
               });
            });
            
            // ============ HELP MODAL =======================================
            $('nav img').click(function() {
                $('.help_modal').show().removeClass('help_modal_minimize');
            });
            
            $('#help_modal_close').click(function() {
                 $('.help_modal').addClass('help_modal_minimize');
                 $('.help_modal').delay(500).queue(function() {
                    $(this).hide();
                    $(this).dequeue();
                });
            });
            /*
$('#0').click(function() {
                alert('hey');
                //google.maps.event.trigger(markers[0], 'click');
                console.log('clicked');
                });
            $('#0').delay(3000).trigger('click');
*/
            
        });
        <?php if (isset($_GET['park'])) {  ?>
        
        <?php } ?>
    </script>
</body>
</html>
