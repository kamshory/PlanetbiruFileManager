<?php
include dirname(__FILE__)."/session.php";

include dirname(__FILE__)."/conf.php";
$userlogin = null;
if(isset($_SESSION['userid']))
{
	$userlogin = 1;
}
if($userlogin)
{
	$authblogid = 1;
}

?>