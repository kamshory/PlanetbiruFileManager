<?php
include dirname(__FILE__)."/session.php";
include_once dirname(__FILE__)."/conf.php";

include_once dirname(__FILE__)."/functions.php";


if($_POST['username'] && $_POST['password'])
{
	$uid = addslashes($_POST['username']);
	$pas = addslashes($_POST['password']);
	$userid = "";
	if(is_array($cfg->users))
	{
		foreach($cfg->users as $user)
		{
			if(matchUser($user, $uid, $pas))
			{
				$userid = $user[0];
				break;
			}
		}
		if($userid)
		{
			$_SESSION['userid'] = $userid;
			if($_POST['ref'])
			header("Location: $res");
			else
			header("Location: ./");
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
?>