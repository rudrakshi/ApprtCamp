<?php

session_start();
try{
$pdfData = $_SESSION['pdf'];
$type=$_GET['typeDwnld'];
}catch(Exception $e){
    header("location: error.php");
}
$filename='Tweets.'.$type;
if ($pdfData) {
    $data = json_decode($pdfData);
    
    switch ($type) {
        case 'pdf':
            getPDF($data,$filename);
            exit();
            break;
        case 'xml':
            getXML($data,$filename);

            break;
        case 'json':
            getJSON($pdfData,$filename);

            break;
        case 'csv':
            getCSV($data,$filename);

            break;
        case 'xls':
            getXLS($data,$filename);

            break;
        case 'google':
            getCSV($data,"Tweets.csv");
            header("location: createGoogleSpreadsheet.php");
            break;

        default:
            getPDF($data,$filename);
            exit();
            break;
    }
    header("Content-Type: application/".$type);
    header("Content-Disposition: attachment;Filename=" . $filename);
    readfile($filename);
    unlink($filename);
    //header("location: ../index.php");
    exit();
}

function getPDF($pdfData,$filename) {

    require('../lib/pdf/extraPDFFunc.php');

    
    $pdf = new PDF();
    //echo "PDF created<br>";
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->SetTitle('Tweets');
    $pdf->Cell(180, 10, 'Tweets', 0, 1, 'C');
    if (is_array($pdfData)) {
        foreach ($pdfData as $tweet) {
            //echo $tweet;
            if (isset($tweet->retweet)) {
                 $pdf->SetFont('Arial', 'B', 16);
                $pdf->Cell(20, 10, 'Retweet', 0, 1, 'C');
            }
            //$tweet->userImg=preg_replace("/^https:/", "http:", $tweet->userImg);
            $pdf->Image($tweet->userImg);
             $pdf->SetFont('Arial', 'B', 16);
            $pdf->Cell(40, 10, $tweet->userName);
             $pdf->SetFont('Arial', '', 16);
            $pdf->Ln();
            $pdf->MultiCell(180,8,$tweet->tweetData);
            $pdf->Ln();
        }
    }
    //echo "About to download<br>";
   
    $pdf->Output($filename, 'D');
    return;
}
function getJSON($pdfData,$filename)
{
        $file = fopen($filename, "w");
        fputs($file, $pdfData);
        fclose($file);
            
}
function getXML($XMLData,$filename) {
    $xml = new SimpleXMLElement('<xml/>');
    if (is_array($XMLData)) {
        foreach ($XMLData as $tweet) {
            $tweetData = $xml->addChild('tweet');
            if (isset($tweet->retweet)) {
                $tweetData->addChild('retweet', $tweet->retweet);
            }
            $tweetData->addChild('Image', $tweet->userImg);
            $tweetData->addChild('UserName', $tweet->userName);
            $tweetData->addChild('Status', $tweet->tweetData);
        }

        $file = fopen($filename, "w");
        fputs($file, $xml->asXML());
        fclose($file);
        
    }
}
function getXLS($xlsData,$filename)
{
    $file = fopen($filename, "w");
    fputs($file, "Name\tStatus\tIsRetweet\n");
    if (is_array($xlsData)) {
        foreach ($xlsData as $tweet) {
            fputs($file, $tweet->userName . "\t");
            fputs($file, "\"" . $tweet->tweetData . "\"\t");
            
            if (isset($tweet->retweet)) {
                fputs($file, "Yes\n");
            } else {
                fputs($file, "No\n");
            }
            
        }
    }
    fclose($file);

}
function getCSV($csvData,$filename) {
    
    $file = fopen($filename, "w");
    fputs($file, "Name,Status,IsRetweet,\n");
    if (is_array($csvData)) {
        foreach ($csvData as $tweet) {
            fputs($file, $tweet->userName . ",");
            $tweet->tweetData=str_replace("\"","'", $tweet->tweetData);
            fputs($file, "\"" . $tweet->tweetData . "\"" . ",");
            if (isset($tweet->retweet)) {
                fputs($file, "Yes\n");
            } else {
                fputs($file, "No\n");
            }
        }
    }
    fclose($file);

}

?>
