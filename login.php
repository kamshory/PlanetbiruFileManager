<?php
include_once dirname(__FILE__)."/session.php";
include_once dirname(__FILE__)."/conf.php";
include_once dirname(__FILE__)."/functions.php";
require_once __DIR__."/classes/HTPasswd.php";
require_once __DIR__."/classes/ListFile.php";

if(isset($_POST['username']) && isset($_POST['password']))
{
	$uid = trim($_POST['username']);
	$pas = trim($_POST['password']);
	$userid = "";
	if(strlen($cfg->users))
	{
		if(HTPasswd::auth($uid, $pas, $cfg->users))
		{
			$userid = $uid;
		}
		if($userid)
		{
			
			$_SESSION['userid'] = $userid;
			if(strlen(@$_POST['ref']))
			{
				$ref = @$_POST['ref'];
				unset($_SESSION['login_error']);
				header("Location: $ref");
				exit();
			}
			else
			{
				header("Location: ./");
				exit();
			}
		}
		else
		{
			if(strlen(@$_POST['ref']))
			{
				$ref = $_POST['ref'];
				$_SESSION['login_error'] = "Username atau password salah.";
				header("Location: $ref");
				exit();
			}
		}
	}
}

if(!isset($_SESSION['userid']))
{
	include_once dirname(__FILE__)."/tool-login-form.php";
}
else
{
	header("Location: ./");
}