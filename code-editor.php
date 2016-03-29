<?php
include_once dirname(__FILE__)."/functions.php";
include_once dirname(__FILE__)."/auth.php";
include dirname(__FILE__)."/conf.php";
if($cfg->authentification_needed && !$userlogin)
{
	include_once dirname(__FILE__)."/tool-login-form.php";
	exit();
}
if(@$_GET['option'] == 'ajax-load')
{
	$cnt = "";
	$path = kh_filter_input(INPUT_GET, 'filepath');
	$filepath = path_decode($path, $cfg->rootdir);
	if(file_exists($filepath))
	{
		$cnt = file_get_contents($filepath);
		echo $cnt;
	}
}
else
{
	$cnt = "";
	$path = kh_filter_input(INPUT_GET, 'filepath');
	$filepath = path_decode($path, $cfg->rootdir);
	if(file_exists($filepath))
	{
		$cnt = file_get_contents($filepath);
	}
?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Planetbiru Code Editor</title>
<link rel="stylesheet" href="style/file-type.css">
<link rel="stylesheet" href="cm/lib/codemirror.css">
<script src="cm/lib/codemirror.js"></script>
<script src="cm/addon/mode/loadmode.js"></script>
<script src="cm/mode/meta.js"></script>
<style type="text/css">
body, html{
	height:100%;
	margin:0;
	padding:0;
	background-color:#FAFAFA;
}
.CodeMirror {
	border-top: 1px solid #DDDDDD; 
	border-bottom: 1px solid #DDDDDD;
}
#filename{
	width:100%;
	-webkit-box-sizing:border-box;
	-moz-box-sizing:border-box;
	box-sizing:border-box;
	margin:0;
	padding:3px 3px;
	border:1px solid #DDDDDD;
	background-color:#FFFFFF;
	color:#555555;
	padding-left:24px;
	background-repeat:no-repeat;
	background-position:4px center;
}
#open, #save{
	width:100%;
	-webkit-box-sizing:border-box;
	-moz-box-sizing:border-box;
	box-sizing:border-box;
	margin:0;
	padding:2px 10px;
	border:1px solid #DDDDDD;
	background-color:#FFFFFF;
	color:#555555;
}
.file{
	padding:10px;
}
</style>
</head>
<body>
<div>
<article>
<form method="post" enctype="multipart/form-data" action="" onsubmit="return false;">
<div class="file">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><input type="text" name="filename" id="filename" value="<?php echo path_encode($filepath, $cfg->rootdir);?>" autocomplete="off" placeholder="File" required></td>
    <td width="60" style="padding-left:4px;"><input type="button" name="open" id="open" value="Open"></td>
    <td width="60" style="padding-left:4px;"><input type="button" name="save" id="save" value="Save"></td>
  </tr>
</table>
</div>
<div class="code">
<textarea id="code" name="code"><?php echo htmlspecialchars($cnt);?></textarea>
</div>
</form>
<script>
var modified = true;
var editor = null;   
function format(){
    var totalLines = editor.lineCount();  
    editor.autoFormatRange({line:0, ch:0}, {line:totalLines});
}
var modeInput = null;
window.onload = function()
{
	document.addEventListener('keydown', function(e) {
	  if(e.ctrlKey && (e.which == 83)) {
		e.preventDefault();
		saveFile();
		return false;
	  }
	});
	modeInput = document.getElementById('filename');
	CodeMirror.modeURL = "cm/mode/%N/%N.js";
	editor = CodeMirror.fromTextArea(document.getElementById("code"), 
	{
		lineNumbers: true,
		lineWrapping: true,
		matchBrackets: true,
		indentUnit: 4,
		indentWithTabs: true
	});
	change();

	window.addEventListener('resize', function(e){
		var w = window.innerWidth - 0;
		var h = window.innerHeight - 70;
		editor.setSize(w, h);
	});
	document.getElementById('open').addEventListener('click', function(e){
		var c1 = document.getElementById('code').value;
		var c2 = editor.getValue();
		e.preventDefault();
		if(c1 != c2)
		{
			if(confirm('This file has been changed but you have not saved. Are you going to open a new file without saving this file?'))
			{
				openFile();
			}
		}
		else
		{
			openFile();
		}
	});
	var w = window.innerWidth - 0;
	var h = window.innerHeight - 60;
	editor.setSize(w, h);
	
	CodeMirror.on(modeInput, "keypress", function(e) {
	  if (e.keyCode == 13){
		  openFile();
	  }
	});
	modeInput.addEventListener('change', function(){
		change();
	});
	document.getElementById('save').addEventListener('click', function(){
		saveFile();
	});
}
function openFile()
{
	var filepath = modeInput.value;
	ajax.get('code-editor.php', {'option':'ajax-load', 'filepath':filepath}, function(answer){
		editor.setValue(answer);
		document.getElementById('code').value = answer;
		change();
	});


}
function onSaveFile()
{
	saveFile();
	return false;
}
var ajax = {};
ajax.x = function () {
    if (typeof XMLHttpRequest !== 'undefined') {
        return new XMLHttpRequest();
    }
    var versions = [
        "MSXML2.XmlHttp.6.0",
        "MSXML2.XmlHttp.5.0",
        "MSXML2.XmlHttp.4.0",
        "MSXML2.XmlHttp.3.0",
        "MSXML2.XmlHttp.2.0",
        "Microsoft.XmlHttp"
    ];

    var xhr;
    for (var i = 0; i < versions.length; i++) {
        try {
            xhr = new ActiveXObject(versions[i]);
            break;
        } catch (e) {
        }
    }
    return xhr;
};

ajax.send = function (url, callback, method, data, async) {
    if (async === undefined) {
        async = true;
    }
    var x = ajax.x();
    x.open(method, url, async);
    x.onreadystatechange = function () {
        if (x.readyState == 4) {
            callback(x.responseText)
        }
    };
    if (method == 'POST') {
        x.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    }
    x.send(data)
};

ajax.get = function (url, data, callback, async) {
    var query = [];
    for (var key in data) {
        query.push(encodeURIComponent(key) + '=' + encodeURIComponent(data[key]));
    }
    ajax.send(url + (query.length ? '?' + query.join('&') : ''), callback, 'GET', null, async)
};

ajax.post = function (url, data, callback, async) {
    var query = [];
    for (var key in data) {
        query.push(encodeURIComponent(key) + '=' + encodeURIComponent(data[key]));
    }
    ajax.send(url, callback, 'POST', query.join('&'), async)
};

function saveFile()
{
	var filepath = document.getElementById('filename').value;
	var filecontent = editor.getValue();
	ajax.post('tool-edit-file.php?option=savefile', {filepath:filepath, filecontent:filecontent}, function(answer){
	if(answer=='READONLY')
	{
		alert('The operation was disabled on read-only mode.');
	}
	else if(answer=='READONLYFILE')
	{
		alert('Saving was aborted because this file is read-only. You should to change permission of this file first.');
	}
	else if(answer=='ISDIR')
	{
		alert('Saving was aborted because this file name is similiar to a directory name. You should to change file name first.');
	}
	else if(answer=='FORBIDDENEXT')
	{
		alert('Saving was aborted because this file name extension is forbidden. Please use another file name extension to save it.');
	}
	else if(answer=='NOTMODIFIED')
	{
		alert('Content is not modified.');
	}
	else if(answer=='SAVED')
	{
		document.getElementById('code').value = filecontent;
		alert('File saved.');
	}
	});
}
function getfileextension(filename){
return (/[.]/.exec(filename))?/[^.]+$/.exec(filename):'';
}

function change() {
	var modeInput = document.getElementById('filename');
	var val = modeInput.value, m, mode, spec;
	var ext = getfileextension(val);
	document.getElementById('filename').setAttribute('class', 'fileicon-'+ext+' filepath');
	if (m = /.+\.([^.]+)$/.exec(val)) 
	{
		var info = CodeMirror.findModeByExtension(m[1]);
		if (info)
		{
			mode = info.mode;
			spec = info.mime;
		}
	}
	else if (/\//.test(val))
	{
		var info = CodeMirror.findModeByMIME(val);
		if (info) 
		{
			mode = info.mode;
			spec = val;
		}
	} 
	else 
	{
		mode = spec = val;
	}
	if (mode) 
	{
		editor.setOption("mode", spec);
		CodeMirror.autoLoadMode(editor, mode);
	} 
}

</script>
</article>
</div>
</body>
</html>
<?php
}
?>