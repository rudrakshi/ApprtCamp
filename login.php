<?php
session_start();
ini_set('max_execution_time', 300); //300 seconds = 5 minutes

require 'lib/twitter/tmhOAuth.php';
require_once 'config/TwitterConfig.php';

require 'lib/twitter/tmhUtilities.php';

$tmhOAuth = new tmhOAuth($twitter);
$here = tmhUtilities::php_self();

function outputError($tmhOAuth) {
    //echo 'Error: ' . $tmhOAuth->response['response'] . PHP_EOL;
    header('Location: error.php');
}
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>ApprtCamp</title>

        <script src="js/bootstrap.min.js" charset="utf-8"></script>
        <link rel="stylesheet" href="css/bootstrap.css"></link>
    </head>
    <body>
        <div class="container-fluid">
            <div class="navbar navbar-inverse navbar-fixed-top">
                <div class="navbar-inner">
                    <div class="container">
                        <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a class="brand" href="<?php echo $here;?>">ApprtCamp</a>
                    </div>
                </div>
            </div>
            <?php
            //call twitter to authorize or login
            if (isset($_REQUEST['authenticate']) || isset($_REQUEST['authorize'])) {
                $params = array(
                    'oauth_callback' => $here
                );

                $code = $tmhOAuth->request('POST', $tmhOAuth->url('oauth/request_token', ''), $params);

                if ($code == 200) {
                    print_r($tmhOAuth->response['response']);
                    $_SESSION['oauth'] = $tmhOAuth->extract_params($tmhOAuth->response['response']);
                    $method = isset($_REQUEST['authenticate']) ? 'authenticate' : 'authorize';
                    $authurl = $tmhOAuth->url("oauth/{$method}", '') . "?oauth_token={$_SESSION['oauth']['oauth_token']}";
                    header('Location: ' . $authurl);
                } else {
                    outputError($tmhOAuth);
                }
            }


//callback from twitter n request for access token 
            elseif (isset($_REQUEST['oauth_verifier'])) {
                $tmhOAuth->config['user_token'] = $_SESSION['oauth']['oauth_token'];
                $tmhOAuth->config['user_secret'] = $_SESSION['oauth']['oauth_token_secret'];

                $code = $tmhOAuth->request('POST', $tmhOAuth->url('oauth/access_token', ''), array(
                    'oauth_verifier' => $_REQUEST['oauth_verifier']
                        ));

                if ($code == 200) {
                    $_SESSION['access_token'] = $tmhOAuth->extract_params($tmhOAuth->response['response']);
                    unset($_SESSION['oauth']);

                    header("Location: index.php");
                } else {
                    outputError($tmhOAuth);
                }
            } else {
                ?>


                <div class="hero-unit" >
                    <br><center><a href="?authorize=1.1" class="btn btn-primary" >Sign in with Twitter</a></center>
                </div>
                <?php
            }
            ?>
        </div>
    </body>
</html>