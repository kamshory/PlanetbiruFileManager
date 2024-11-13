<?php
include __DIR__."/session.php";
include_once __DIR__."/functions.php";
unset($_SESSION['userid']);
unset($_SESSION['username']);
unset($_SESSION['password']);
header("Location: ./");

