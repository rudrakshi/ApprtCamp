<?php

session_start();
try{
$pdfData = $_SESSION['pdf'];

}catch(Exception $e){
    header("location: error.php");
}
$filename='Tweets.php';
if ($pdfData) {
    $data = json_decode($pdfData);
    
    
            getPDF($data,$filename);
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


?>
