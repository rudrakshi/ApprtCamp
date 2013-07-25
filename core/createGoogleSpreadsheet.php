<?php
session_start();

require_once '../config/config.php';
require_once '../lib/google/Google_Client.php';
require_once '../lib/google/Google_DriveService.php';

$client = new Google_Client();
$service = new Google_DriveService($client);


// Exchange authorization code for access token
if (isset($_GET['code'])) {
$accessToken = $client->authenticate();
$client->setAccessToken($accessToken);
$_SESSION['google_token']=$accessToken;
}

//Insert a file
if(isset ($_SESSION['google_token'])){
    $client->setAccessToken($_SESSION['google_token']);
    
$file = new Google_DriveFile();
$file->setTitle('Tweets');
$file->setDescription('A spreadsheet containing your tweets');
$file->setMimeType('text/csv');

$data = file_get_contents("Tweets.csv");
$createdFile = $service->files->insert($file, array(
      'data' => $data,
      'mimeType' => 'text/csv',
      'convert' => true,
    ));
$_SESSION['GDSLink']=$createdFile['alternateLink'];
unlink("Tweets.csv");
header("Location: ../index.php");
//print_r($createdFile);
}else{
    $authUrl = $client->createAuthUrl();
//Request authorization
header("location: ".$authUrl);
}
?>
