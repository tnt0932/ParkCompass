<?php
require_once('project_vars.php')
function sanitize( $str ) {
   return( str_ireplace(array( "\r", "\n", "%0a", "%0d", "Content-Type:", "bcc:","to:","cc:" ), "", $str ) );
}

function remove_period($str) {
    $str = str_replace("\r\n.", "\r\n..", $str);
    $str = (substr($str, 0, 1) == '.' ? '.'.$str : $str);
    return $str;
}

function show_alert($valid) {
    if (!$valid) {
        echo '<strong class="alert"> !! </strong>';
    }
}

$all_valid = $name_valid = $email_valid = $message_valid = true;


$email_response = '';

if (isset($_POST['submit'])) {  

    if ($_POST['name'] == '') {
        $all_valid = $name_valid = false;
    }
    if ($_POST['message'] == '') {
        $all_valid = $message_valid = false;
    }
    
    /*  The next two lines include the Google Code email validator file, and
        store a new instance of EmailAddressValidator in a variable.
    */

    require_once 'EmailAddressValidator.php';
    $validator = new EmailAddressValidator;

    if (!$validator->check_email_address($_POST['email'])) {
        $all_valid = $email_valid = false;
    }
        
    if ($all_valid) {
        //  ####    NO PROBLEMS FOUND - PROCESS THE FORM DATA HERE
        $message = trim($_POST['message']);
        $to = $youremailaddress;
        $subject = 'Email from parkcompass.com';
        $name = sanitize(trim($_POST['name']));
        $email = sanitize(trim($_POST['email']));
        
        $message = htmlspecialchars(trim($message));
        $message = remove_period($message);
        
        $body = "The following message has been sent by ". $name . " (".$email.")\r\n\r\n";
        $body .= "Message:\r\n" . $message;
        $body = wordwrap($body,72);//good for accomodating older email clients
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/plain; charset=ISO-8859-1\r\n";
        $headers .= "From: " . $name . " <".$email.">\r\n";
        $headers .= "X-Priority: 1\r\n";
        $headers .= "X-MSMail-Priority: High\r\n";
        
        $m = mail($to, $subject, $body, $headers);
        
        $email_response = '<h4 style="color:green">We\'ve received your email and will get back to you soon - thanks!</h4>';
    }

}
?>
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
    <link rel="stylesheet" href="css/html5reset-1.6.1.css">
    <link rel="stylesheet" type="text/css" href="css/styles.css">
    <!-- <link rel="stylesheet/less" type="text/css" href="css/styles.less"> -->
    <link href='http://fonts.googleapis.com/css?family=Cabin:400,700' rel='stylesheet' type='text/css'>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
    <!-- <script src="js/less-1.3.0.min.js"></script> -->
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
        <h1><a href="index">PARK COMPASS</a><span></span></h1>
        <nav>
            <a href="index">map</a>
            <!-- <a href="#">contact</a> -->
            <a href="#"><img src="img/info_icon.png" width="17" height="17" alt="info"></a>
        </nav>
    </header>

    
<!-- //////////////////////////////////////////////////////
                    CONTAINER
    /////////////////////////////////////////////////////////-->
    
    <div id="container">
    
    <!-- ============ BANNER ================================-->
    
        <section id="banner">
            <h2>Welcome to</h2>
            <h1>Park Compass</h1>
            <h3>The easiest way to discover Vancouver's parks</h3>
        </section>
    
        <!-- ============ FEATURES ===========================-->
    
        <section id="features" class="section group">
            <div class="col span_4_of_12">
                <img src="img/geolocate.png" width="168" height="133" alt="geolocate">
                <h4>Geolocate</h4>
            </div>
            <div class="col span_4_of_12">
                <img src="img/search.png" width="168" height="138" alt="search">
                <h4>Search</h4>
            </div>
            <div class="col span_4_of_12">
                <img src="img/filter.png" width="168" height="150" alt="filter">
                <h4>Filter</h4>
            </div>
        </section>
        
        <!-- ============ BODY CONTENT ========================-->
        
        <section class="section group">
            <section class="col span_8_of_12">
                <section id="about" >
                    <h3>About</h3>
                    <p>Park Compass is all about helping people to discover and explore their city parks. We think parks are amazing. They are part of the fabric of almost every community - a place where neighbours, friends, and families can connect and have fun. When people are more connected to their community, neighbourhood, and city, they are more likely to fight to protect and improve it. We hope Park Compass helps you to get out and experience your parks!</p>
                </section>
            
                <section id="help">
                    <h3>Help</h3>
                    
                    <article class="help_item">
                        <h4>I found a mistake! / You missed a park!</h4>
                        <p>Let us know about it! Send us a message and let us know what isn't working. Right now we're only working with Vancouver parks, but if you can give us details about other parks we will try to include them in future versions of the site.</p>
                    </article>
                    
                    <article class="help_item">
                        <h4>Geolocation (The "Find Parks Near You" button) isn't working!</h4>
                        <p>There are a couple of things you need to know about geolocation:</p>
                        <ul>
                            <li>We need your permission to find your location. Internet browsers (Firefox, Chrome, etc.) enforce this to make sure that you're not giving out your location information without knowing about it. Your browser should ask you for your permission when Park Compass first loads.</li>
                            <li>Geolocation isn't perfect. The accuracy will depend on who provides your internet. Some companies will only show us the location of their closest tower, so your user marker will be way off of your actual location. Sorry!</li>
                        </ul>
                        <p>If geolocation just won't work - don't worry. You can always use the search bar, or drag your user marker to the desired location on the map!
                    </article>
                    
                    <article class="help_item">
                        <h4>How come you're only showing Vancouver parks?</h4>
                        <p>Three reasons:</p>
                        <ul>
                            <li>You've got to start somewhere</li>
                            <li>Vancouver Parks data was freely available from the <a href="http://vancouver.ca/your-government/open-data-catalogue.aspx">City of Vancouver Open Data catalogue</a>.</li>
                            <li>We love Vancouver and want to do our part to help people experience it!</li>
                        </ul>
                    </article>
                </section>
            </section>
            
            <!-- ============ CONTACT========================-->
            
            <section id="contact" class="col span_4_of_12">
                <h3>Contact</h3>
                <h3 id="email"><a href="mailto:<?php echo $youremailaddress?>"><?php echo $youremailaddress?></a></h3>
                <a href="http://twitter.com/parkcompass" target="_blank" id="twitter">@parkcompass</a>
                
                <!--
<h4>Send us a message!</h4>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                    <label for="name">Name</label><br>
                    <input type="text" name="name"><br>
                    <?php if (!$name_valid) echo '<p style="color: red;">Please enter your name</p>'; ?>
                    <label for="email">Email</label><br>
                    <input type="email" name="email"><br>
                    <?php if (!$email_valid) echo '<p style="color: red;">Please enter a valid email address</p>'; ?>
                    <label for="message">Message</label><br>
                    <textarea name="message" rows="10"></textarea><br>
                    <?php if (!$message_valid) echo '<p style="color: red;">Please write us a message</p>'; ?>
                    <?php echo $email_response; ?>
                    <input type="submit" name="submit" value="submit">
                </form>
-->
            </section>
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