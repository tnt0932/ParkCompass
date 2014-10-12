<!DOCTYPE html>
<html>
<head>
    <title>About | Park Compass</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="description" content="The easiest way to discover Vancouver's parks. Park Compass helps you find parks in Vancouver using geolocation, search, and filtering by park facility."/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--[if lt IE 9]>
    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <link rel="shortcut icon" type="image/x-icon" href="img/favicon.ico">
    <link href='http://fonts.googleapis.com/css?family=Cabin:400,700' rel='stylesheet' type='text/css'>
        <!--(if target dev)><!--><link rel="stylesheet" type="text/css" href="css/html5-reset.css">
    <link rel="stylesheet" type="text/css" href="css/styles.css"><!--<!(endif)-->
    <!--(if target dist)><link rel="stylesheet" type="text/css" href="css/{{pkgName}}.{{pkgVersion}}.min.css"><!(endif)-->

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

<body id="about_body">

    
<!-- //////////////////////////////////////////////////////
                    HEADER
    /////////////////////////////////////////////////////////-->
    
    <header>
        <h1><a href="/">PARK COMPASS</a><span></span></h1>
        <nav>
            <a href="/">map</a>
            <a href="#"><img src="img/info_icon.png" width="17" height="17" alt="info"></a>
        </nav>
    </header>

    
<!-- //////////////////////////////////////////////////////
                    CONTAINER
    /////////////////////////////////////////////////////////-->
    
    <div id="container">
        
        <!-- ============ BODY CONTENT ========================-->
        
        <section class="section group">
            <section class="col span_8_of_12">

                <h2>Data Sources</h2>
                <p>Source: <a href="http://vancouver.ca/your-government/open-data-catalogue.aspx" target="_blank">City of Vancouver Open Data Catalogue</a></p>
                <p>Source: <a href="http://www.geoweb.dnv.org/" target="_blank">The District of North Vancouver GIS Department</a></p>

            </section>
            
            <!-- ============ CONTACT========================-->
            
         
        </section>
        
        <!-- ============ FOOTER ========================-->
        
        <footer class="section group">
            <p class="col span_4_of_12">&copy; 2012 Park Compass</p>
            <p class="col span_8_of_12">Made in <span></span> by <a href="http://troy.is">Troy Tucker</a></p>
        </footer>
       
    </div><!-- /container -->
    
    <!-- ============ HELP MODAL ========================-->
    <div id="help_modal_screen_cover" class="help_modal"></div>
    <div id="help_modal" class="help_modal">
        <a href="#" id="help_modal_close">Get Started!</a>
    </div>
    
<!-- //////////////////////////////////////////////////////
                    JAVASCRIPT
    /////////////////////////////////////////////////////////-->
    
    <script type="text/javascript">
        
    $(document).ready(function() {
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
    });
    </script>

</body>
</head>
