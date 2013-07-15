<?php
require 'core/Retrieval.php';
ini_set('max_execution_time', 300); //300 seconds = 5 minutes

if (!isset($_SESSION['access_token'])) {
    header("location: login.php");
}// if connected get data from twitter
else {
//get user's information
    $user = getUserInfo();

//get followers            
    $followers = getFollowers();

    //set theme n bkgnd
    $background = $user->profile_background_image_url_https;
    $fillColor = $user->profile_sidebar_fill_color;
    $profileBackgnd = $user->profile_background_color;
    $fontColor=$user->profile_text_color;
    ?>
    <!DOCTYPE html>
    <html>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title></title>

            <link rel="stylesheet" href="css/bootstrap.css"></link>
            <link rel="stylesheet" href="css/bootstrap-responsive.css"></link>
            <link rel="stylesheet" href="css/ApprtCamp.css"></link>

            <script src="http://code.jquery.com/jquery-1.10.0.min.js"></script>
            <script src="js/bootstrap.min.js" charset="utf-8"></script>
            <script src="js/ApprtCamp.js" charset="utf-8"></script>

        </head>
        <body style="background:url('<?php echo $background; ?>')  no-repeat ;color: #<?php echo $fontColor;?>;background-color:#<?php echo $profileBackgnd; ?>; ">
            <div id="container">
                <?php
                if (!empty($user)) {
                    // print_r($user);
                    ?>
               
                    <div class="navbar navbar-inverse navbar-fixed-top">
                        <div class="navbar-inner">
                            <div class="container">
                                <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                                    <span class="icon-bar"></span>
                                    <span class="icon-bar"></span>
                                    <span class="icon-bar"></span>
                                </button>
                                <a class="brand" href="<?php echo $here ?>">ApprtCamp</a>
                                <div class="nav-collapse collapse">
                                    <ul class="nav"> 
                                        <li >
                                            <div id="welcome">
                                                Welcome <?php echo $user->name ?>!!
                                            </div>
                                        </li>

                                    </ul><ul class="nav pull-right">
                                        <li><a href="https://github.com/rudrakshi/ApprtCamp" target="blank">Fork Me</a></li>
                                        <li><a href="logout.php?wipe=1">Sign out</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="container-fluid" id="twitter-data">
                        <div class="row-fluid">  
                            <div class="box span3" style="background-color: #<?php echo $fillColor; ?>">
                                <div align="center" class="row-fluid">
                                    <div class="span3">

                                        <a href='#' class="home"><img class="img-polaroid" src='<?php echo $user->profile_image_url; ?> ' /></a>
                                    </div>
                                    <div class="span9">
                                        <h4><strong><?php echo $user->name ?></strong><br/>
                                            <a href="http://www.twitter.com" target="blank">@<?php echo $user->screen_name ?></a>
                                        </h4>
                                    </div>
                                </div>
                                <hr>
                                <div align="center" class="row-fluid span12">                                    
                                    <div class="span4" >
                                        <b><?php echo $user->statuses_count ?></b><br /> Tweets 
                                    </div>
                                    <div class="span4" >
                                        <b><?php echo $user->friends_count ?></b><br /> Following 
                                    </div>
                                    <div class="span4" >
                                        <b><?php echo $user->followers_count ?></b><br /> Follwers 
                                    </div>
                                </div>  
                            </div>
                            <div id="slideshow" class="slideshow-box span6" style="background-color: #<?php echo $fillColor; ?>;"></div>

                            <div class="span3" id="followers" style="background-color: #<?php echo $fillColor; ?>;">
                                <div class="span12" align="center">
                                    <h5>Someone Missing?</h5>
                                    <div class="input-prepend">
                                        <span class="add-on">@</span>
                                        <input id="follower" class="input-prepend" name="follower" type="text" placeholder="Username" data-provide="typeahead" />
                                    </div>
                                </div> 
                                <div>
                                    <center> <h4>Followers</h4></center>
                                    <?php
                                    $j = 0; //counter to print 10 followers

                                    if (!empty($followers)) {
                                        foreach ($followers->users as $follower) {
                                            if ($j < 10) {
                                                ?>
                                                <div align="center" class="row-fluid">
                                                    <div class="span4">
                                                        <img class="img-polaroid" src='<?php echo $follower->profile_image_url_https ?>' />
                                                    </div>
                                                    <div class="span8">
                                                        <i><?php echo $follower->name ?></i><br>
                                                        <a class='ajax_class' id ='ajax_a<?php echo $j ?>' href=''>@<?php echo $follower->screen_name ?></a>
                                                    </div>
                                                </div>
                                                <div id="flwr-space"></div>
                                                <?php
                                                $j++;
                                            }
                                        }
                                    } else {
                                        ?>
                                        Your Followers' List is empty!!!
                                        <?php
                                    }
                                    ?>
                                </div> 
                            </div>
                        </div>
                    </div>
                    <?php
                } else {
                    ?>
                    <div>
                        <?php header('location: error.php'); ?>
                    </div>

                    <?php
                }
            }
            ?>
        </div>
    </body>
</html>