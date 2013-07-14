<?php
session_start();

if (isset($_REQUEST['wipe'])) {
            session_destroy();
            header("Location: login.php");
        }
?>
