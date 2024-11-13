<?php
include dirname(__FILE__)."/session.php"; //NOSONAR

include dirname(__FILE__)."/conf.php"; //NOSONAR
$userlogin = null;
if(isset($_SESSION['userid']))
{
	$userlogin = 1;
}
if($userlogin)
{
	$authblogid = 1;
}
