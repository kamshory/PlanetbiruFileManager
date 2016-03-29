<?php
include dirname(__FILE__)."/session.php";
include_once dirname(__FILE__)."/functions.php";
unset($_SESSION['userid']);
unset($_SESSION['username']);
unset($_SESSION['password']);
header("Location: ./");

?>