<?php

    session_start();
    
    $follower = $_SESSION['followers'];
    $q=$_POST['q'];
    
    if($follower)
    {
        $follower = json_decode($follower);
        $result=array();
        
        foreach($follower as $f)
        {
            if(stristr($f->name, $q)==TRUE)
            {
                $result[]=$f->name;
            }
            if(stristr($f->screen_name, $q)==TRUE)
            {
                $result[]="@".$f->screen_name;
            }
        }
        sort($result);
        
        
        echo json_encode($result);
    }
    else
        die('Follower list is empty');
?>
