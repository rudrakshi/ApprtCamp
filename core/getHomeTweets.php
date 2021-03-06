<?php
session_start();
try {
    ini_set('max_execution_time', 300); //300 seconds = 5 minutes

    require '../lib/twitter/tmhOAuth.php';
    require_once '../config/TwitterConfig.php';

    $tmhOAuth = new tmhOAuth($twitter);

    if (isset($_SESSION['access_token'])) {
        $GLOBALS['tmhOAuth']->config['user_token'] = $_SESSION['access_token']['oauth_token'];
        $GLOBALS['tmhOAuth']->config['user_secret'] = $_SESSION['access_token']['oauth_token_secret'];
    } else {
        header("location: login.php");
    }

    $pdfData = array();
	
	if (isset($_SESSION['user'])) {
        $param = array(
            'user_id' => $_SESSION['user']->id,
            'count' => 10,
        );
        $code = $GLOBALS['tmhOAuth']->request('GET', $GLOBALS['tmhOAuth']->url('1.1/statuses/user_timeline'), $param);
        if ($code == 200) {
            $Tweets = json_decode($GLOBALS['tmhOAuth']->response['response']);
            if (!empty($Tweets)) {
                foreach ($Tweets as $tweet) {
                    if (!isset($tweet->retweeted_status)) {
                        $original = $tweet->text;
                        $tweet->text = getLink($tweet->text, $tweet->entities);
                       
                        $GLOBALS['pdfData'][] = array(
                            'userImg' => $tweet->user->profile_image_url_https,
                            'userName' => $tweet->user->name,
                            'tweetData' => $original,
                            'dataWithLink' => $tweet->text
                        );
                    } else {
                        $original = $tweet->text;
                        $tweet->retweeted_status->text = getLink($tweet->retweeted_status->text, $tweet->retweeted_status->entities);
                        
                        $GLOBALS['pdfData'][] = array(
                            'retweet' => true,
                            'userImg' => $tweet->retweeted_status->user->profile_image_url_https,
                            'userName' => $tweet->retweeted_status->user->name,
                            'tweetData' => $original,
                            'dataWithLink' => $tweet->retweeted_status->text
                        );
                    }
                }
            }
        }
        $_SESSION['pdf'] = json_encode($GLOBALS['pdfData']);
    } else {
        header("location: login.php");
    }
    ?>
    <h4><div><center>Home Timeline</center></div></h4>
    <div id="myCarousel" class="carousel slide">
        <div class="carousel-inner">
            <?php
            $status = 0;

            $param = array(
                'exclude_replies' => true,
                'count' => 10
            );

            $code = $GLOBALS['tmhOAuth']->request('GET', $GLOBALS['tmhOAuth']->url('1.1/statuses/home_timeline'), $param);

            if ($code == 200) {
                $Tweets = json_decode($GLOBALS['tmhOAuth']->response['response']);
                if (empty($Tweets)) {
                    ?>
                    <div class="item active">
                        <div class="container-fluid">
                            <div class="tweet container-fluid">
                                <strong>Sorry there are no Tweets!!!</strong>
                            </div>
                        </div>
                    </div>
                    <?php
                    $status = 0;
                } else {
                    $first = 0;
                    foreach ($Tweets as $tweet) {
                        if (!isset($tweet->retweeted_status)) {
                            
                            $tweet->text = getLink($tweet->text, $tweet->entities);
                            if ($first == 0) {
                                $first++;
                                ?><div class="item active">
                                <?php
                            } else {
                                ?>     
                                    <div class="item">
                                    <?php } ?>
                                    <div class="container-fluid">
                                        <div class="tweet container-fluid">
                                            <div class="row-fluid">
                                                <h4> <div class="span2">
                                                        <img src='<?php echo $tweet->user->profile_image_url_https ?>' >
                                                    </div>
                                                    <div class="span9">
                                                        <strong><?php echo $tweet->user->name ?></strong>
                                                        <br><a href='http://twitter.com/<?php echo $tweet->user->screen_name ?>' target='_blank'>@<?php echo $tweet->user->screen_name ?></a><br>
                                                    </div></h4>
                                            </div>

                                            <p><br> <?php echo $tweet->text ?><br><br></p>
                                        </div>
                                    </div>
                                </div>

                                <?php
                            } else {
                                
                                $tweet->retweeted_status->text = getLink($tweet->retweeted_status->text, $tweet->retweeted_status->entities);
                                if ($first == 0) {
                                    $first++;
                                    ?><div class="item active">
                                    <?php
                                } else {
                                    ?>     
                                        <div class="item">
                                <?php } ?>
                                        <div class="container-fluid">
                                            <div class="tweet container-fluid">
                                                <div ><h4><strong>Retweet</strong></h4></div><br>
                                                <div class="row-fluid">
                                                    <h4><div class="span2">
                                                            <img src='<?php echo $tweet->retweeted_status->user->profile_image_url_https ?>' >
                                                        </div>
                                                        <div class="span9">
                                                            <strong><?php echo $tweet->retweeted_status->user->name ?></strong>
                                                            <br><a href='http://twitter.com/<?php echo $tweet->retweeted_status->user->screen_name ?>' target='_blank'>@<?php echo $tweet->retweeted_status->user->screen_name ?></a><br>
                                                        </div></h4>
                                                </div>
                                                <p> <br>  <?php echo $tweet->retweeted_status->text ?><br><br></p>

                                            </div>
                                        </div>  
                                    </div>

                    <?php
                }
                             
            }
            
            $status = 1;
        }
    } else {
        outputError($GLOBALS['tmhOAuth']);
    }
    ?>
                </div>
                <a class="left carousel-control" href="#myCarousel" data-slide="prev">&lsaquo;</a>
                <a class="right carousel-control" href="#myCarousel" data-slide="next">&rsaquo;</a>
            </div>
            <div style="
    <?php
    if ($status == 0) {
        ?>display: none;
                <?php
            }
            ?>
                 ">
                <center>
                    <button id="show"class="btn btn-primary" onclick="showOptns();">
                        Download Tweets
                    </button>
                </center>
                <div id="download" style="display: none">
                    <div align="center" class="container-fluid">
                        <form action="core/getFile.php">
                            <div class="row-fluid">
                                Choose a Type to get the Tweets:
                                    <select name="typeDwnld">
                                        <option value="pdf">PDF</option>
                                        <option value="xls">Excel</option>
                                        <option value="csv">CSV</option>
                                        <option value="json">JSON</option>
                                        <option value="xml">XML</option>
                                        <option value="google">Google Drive</option>
                                    </select>
                               
                            </div>
                            <div> 
                                <button class="btn btn-success" onclick="alert('Please wait your request will be processed shortly.');">Get Tweets</button>&nbsp;
                                <button type="button" class="btn btn-danger " onclick="hideOptns()">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
    <?php
} catch (Exception $e) {
    outputError($e);
}

function getLink($text, $ent) {
    foreach ($ent->urls as $url) {
        $from = $url->url;
        $to = "<a href=" . $url->expanded_url . " target=blank>" . $url->display_url . "</a>";
        $text = str_replace($from, $to, $text);
    }

    if (isset($ent->media)) {
        foreach ($ent->media as $media) {
            $from = $media->url;
            $to = "<a href=" . $media->url . " target=blank>" . $media->display_url . "</a>";
            $text = str_replace($from, $to, $text);
        }
    }

    if ($text) {
        $pattern = '/\@([a-zA-Z0-9_]+)/';
        $replace = '<a href=http://twitter.com/\1 target=blank>@\1</a>';
        $text = preg_replace($pattern, $replace, $text);

        $pattern = '/\#([a-zA-Z0-9_]+)/';
        $replace = '<a href=http://twitter.com/search?q=\1 target=blank>#\1</a>';
        $text = preg_replace($pattern, $replace, $text);
    }

    return $text;
}

function outputError($tmhOAuth) {
    //echo 'Error: ' . $tmhOAuth->response['response'] . PHP_EOL;
    header("location: error.php");
}
?>
