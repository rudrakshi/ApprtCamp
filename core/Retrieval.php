<?php

session_start();

ini_set('max_execution_time', 300); //300 seconds = 5 minutes

require 'lib/twitter/tmhOAuth.php';
require 'lib/twitter/tmhUtilities.php';
require_once 'config/TwitterConfig.php';

$tmhOAuth = new tmhOAuth($twitter);

if (isset($_SESSION['access_token'])) {
    $GLOBALS['tmhOAuth']->config['user_token'] = $_SESSION['access_token']['oauth_token'];
    $GLOBALS['tmhOAuth']->config['user_secret'] = $_SESSION['access_token']['oauth_token_secret'];
} else {
    header("location: login.php");
}

$here = tmhUtilities::php_self();

function outputError($tmhOAuth) {
    //echo 'Error: ' . $tmhOAuth->response['response'] . PHP_EOL;
    //include_once 'error.php';
    header("location: error.php");
}

$followerList = array();
$user;

function getUserInfo() {
    $code = $GLOBALS['tmhOAuth']->request('GET', $GLOBALS['tmhOAuth']->url('1.1/account/verify_credentials'));
    if ($code == 200) {
        $user = json_decode($GLOBALS['tmhOAuth']->response['response']);
        $GLOBALS['user'] = $user;
        return $user;
    } else {
        outputError($GLOBALS['tmhOAuth']);
    }
}

function getFollowers() {
    $param = array(
        'user_id' => $GLOBALS['user']->id_str,
        // 'count' =>10,
        'stringify_ids' => true
    );
    $code = $GLOBALS['tmhOAuth']->request('GET', $GLOBALS['tmhOAuth']->url('1.1/followers/list'), $param);
    if ($code == 200) {
        $followers = json_decode($GLOBALS['tmhOAuth']->response['response']);
        // print_r($resp);
        foreach ($followers->users as $follower) {

            $GLOBALS['followerList'][] = array(
                'id' => $follower->id_str,
                'name' => $follower->name,
                'screen_name' => $follower->screen_name
            );
        }
        $_SESSION['followers'] = json_encode($GLOBALS['followerList']);
        return $followers;
    } else {
        outputError($GLOBALS['tmhOAuth']);
    }
}

?>
