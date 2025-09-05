<!-- File: logout.php -->
<?php
session_start();
session_destroy();
header("Location: Login_Admin.php");
exit();
?>