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

    if (isset($_POST['follower'])) {
        $id = 0;
        $follower = $_POST['follower'];
        $follower = str_replace('@', '', $follower);

        $flwrList = json_decode($_SESSION['followers']);
        if (!empty($flwrList)) {
            foreach ($flwrList as $f) {
                if (strcasecmp($f->name, $follower) == 0 || strcasecmp($f->screen_name, $follower) == 0) {
                    $id = $f->id;
                }
            }
        } else {
            outputError("followers are empty");
        }
        ?>
        <h4><div><center><?php echo $follower; ?>'s Timeline</center></div></h4>
        <div id="myCarousel" class="carousel slide">
            <div class="carousel-inner">


                <?php
                $param = array(
                    'user_id' => $id,
                    'count' => 10,
                );
                $code = $GLOBALS['tmhOAuth']->request('GET', $GLOBALS['tmhOAuth']->url('1.1/statuses/user_timeline'), $param);
                if ($code == 200) {
                    $Tweets = json_decode($GLOBALS['tmhOAuth']->response['response']);

                    if (empty($Tweets)) {
                        ?>
                        <div class="item active">
                            <div class="container-fluid">
                                <div class="tweet container-fluid">
                                    <strong>Sorry there are no Tweets!!!<strong>
                                            </div>
                                            </div>
                                            </div>
                                            <?php
                                        }
                                        $first = 0;
                                        foreach ($Tweets as $tweet) {
// print_r($tweet);
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
                                                                            <img src='<?php echo $tweet->user->profile_image_url_https ?>'>
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
                                                                    <div ><strong>Retweet</strong></div><br>
                                                                    <div class="row-fluid">
                                                                        <h4><div class="span2">
                                                                                <img src='<?php echo $tweet->retweeted_status->user->profile_image_url_https ?>'>
                                                                            </div>
                                                                            <div class="span9">
                                                                                <strong><?php echo $tweet->retweeted_status->user->name ?></strong>
                                                                                <br><a href='http://twitter.com/<?php echo $tweet->retweeted_status->user->screen_name ?>' target='_blank'>@<?php echo $tweet->retweeted_status->user->screen_name ?></a><br>
                                                                            </div></h4>
                                                                    </div>
                                                                    <p><br>  <?php echo $tweet->retweeted_status->text ?><br><br></p>

                                                                </div>
                                                            </div></div>

                    <?php
                }
            }
        } else {
            die(outputError($GLOBALS['tmhOAuth']));
        }
        ?>
                                        </div>
                                        <a class="left carousel-control" href="#myCarousel" data-slide="prev">&lsaquo;</a>
                                        <a class="right carousel-control" href="#myCarousel" data-slide="next">&rsaquo;</a>
                                    </div>
        <?php
    }
} catch (Exception $e) {
    outputError($e);
}

function getLink($text, $ent) {
    foreach ($ent->urls as $url) {
        $from = $url->url;
        $to = "<a href=" . $url->expanded_url . " target=_blank>" . $url->display_url . "</a>";
        $text = str_replace($from, $to, $text);
    }

    if (isset($ent->media)) {
        foreach ($ent->media as $media) {
            $from = $media->url;
            $to = "<a href=" . $media->url . " target=_blank>" . $media->display_url . "</a>";
            $text = str_replace($from, $to, $text);
        }
    }

    if ($text) {
        $pattern = '/\@([a-zA-Z0-9_]+)/';
        $replace = '<a href=http://twitter.com/\1\ target=_blank>@\1</a>';
        $text = preg_replace($pattern, $replace, $text);

        $pattern = '/\#([a-zA-Z0-9_]+)/';
        $replace = '<a href=http://twitter.com/search?q=#\1&src=hash\ target=_blank>#\1</a>';
        $text = preg_replace($pattern, $replace, $text);
    }

    return $text;
}

function outputError($tmhOAuth) {
    //echo 'Error: ' . $tmhOAuth->response['response'] . PHP_EOL;
    header("location: error.php");
}
?>
