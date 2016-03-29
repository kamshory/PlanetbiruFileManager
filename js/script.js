/*
** Copyright Kamshory Developer 2010-2013 
** All rights reserved
** Join Planet Biru http://www.planetbiru.com
*/
var angle = 0;
var fliph = 0;
var flipv = 0;
var resize = 0;
var imgwidth = 1;
var imgheight = 1;
var crop = 0;
var filepath = '';
var fileurl = '';
var arrthumbnail = new Array();
var arrthumbnailURL = new Array();
var startX, startY, endX, endY, centerX, centerY, width, height, show = false;
if (window.XMLHttpRequest) 
{
	var xhr = new XMLHttpRequest();
} 
else 
{
	var xhr;
	var versions = [
		"MSXML2.XmlHttp.5.0", 
		"MSXML2.XmlHttp.4.0",
		"MSXML2.XmlHttp.3.0", 
		"MSXML2.XmlHttp.2.0",
		"Microsoft.XmlHttp"
	];
	for(var i = 0, len = versions.length; i < len; i++) 
	{
		try {
			xhr = new ActiveXObject(versions[i]);
			break;
		}
		catch(e){}
	} 
}		

function setInputSelection(input, startPos, endPos)
{
	input.focus();
	if(typeof input.selectionStart != "undefined")
	{
		input.selectionStart = startPos;
		input.selectionEnd = endPos;
	} 
	else if(document.selection && document.selection.createRange)
	{
		input.select();
		var range = document.selection.createRange();
		range.collapse(true);
		range.moveEnd("character", endPos);
		range.moveStart("character", startPos);
		range.select();
	}
}
function initPreviewImageUpload()
{
	$(document).on('click', '#image-list li img', function(){
        var src = $(this).attr('src');
		show = true;
		width = parseFloat($(this).width());
        height = parseFloat($(this).height());
		var width2 = 6*width;
		var height2 = 6*height;				
		startX = parseFloat($(this).offset().left);
		startY = parseFloat($(this).offset().top);
		centerX = parseFloat($(window).width())/2;
		centerY = parseFloat($(window).height())/2;
		endX = centerX - (width2/2) - 5;
		endY = centerY - (height2/2) - 5;
		$('.preview-image').remove();
		show = true;
		$('<img/>').attr('src', src).addClass('preview-image').css({'width':width+'px','height':height+'px','position':'absolute','z-index':'5000','left':startX+'px','top':startY+'px','border':'1px solid #CCCCCC','border-radius':'1px','padding':'4px','background-color':'#FFFFFF','box-shadow':'0 0 2px #CCCCCC'}).appendTo('body');
		$('.preview-image').animate({width:width2, height:height2, left:endX, top:endY}, 200);
		$('.preview-image, .ui-dialog, .ui-widget-overlay').click(function(){
			show = false;
			$('.preview-image').animate({width:width, height:height, left:startX, top:startY}, 200, function(){
				if(!show)
				{
					$('.preview-image').remove();
				}
			});
		});		
    });
}
function updateToolbarStatus()
{
	if(($('.fileid:checked').length == $('.fileid').length))
	{
		$('.toolbar-inner li a img.check').parent().parent().addClass('inactive');
	}
	else
	{
		$('.toolbar-inner li a img.check').parent().parent().removeClass('inactive');
	}
	
	if(clipboardfile.operation == '')
	{
		$('.toolbar-inner li a img.paste').parent().parent().addClass('inactive');
	}
	else
	{
		$('.toolbar-inner li a img.paste').parent().parent().removeClass('inactive');
	}
	
	if($('.fileid:checked').length == 0)
	{
		$('.toolbar-inner li a img.uncheck').parent().parent().addClass('inactive');
		$('.toolbar-inner li a img.copy').parent().parent().addClass('inactive');
		$('.toolbar-inner li a img.cut').parent().parent().addClass('inactive');
		$('.toolbar-inner li a img.move').parent().parent().addClass('inactive');
		$('.toolbar-inner li a img.delete').parent().parent().addClass('inactive');
		$('.toolbar-inner li a img.rename').parent().parent().addClass('inactive');
		$('.toolbar-inner li a img.compress').parent().parent().addClass('inactive');
		$('.toolbar-inner li a img.permission').parent().parent().addClass('inactive');
	}
	else
	{
		$('.toolbar-inner li a img.uncheck').parent().parent().removeClass('inactive');
		$('.toolbar-inner li a img.copy').parent().parent().removeClass('inactive');
		$('.toolbar-inner li a img.cut').parent().parent().removeClass('inactive');
		$('.toolbar-inner li a img.move').parent().parent().removeClass('inactive');
		$('.toolbar-inner li a img.delete').parent().parent().removeClass('inactive');
		$('.toolbar-inner li a img.rename').parent().parent().removeClass('inactive');
		$('.toolbar-inner li a img.compress').parent().parent().removeClass('inactive');
		$('.toolbar-inner li a img.permission').parent().parent().removeClass('inactive');
	}
	if($('.row-data-file[data-file-type="application/zip"]').find('input[type=checkbox]:checked').length)
	{
		$('.toolbar-inner li a img.extract').parent().parent().removeClass('inactive');
	}
	else
	{
		$('.toolbar-inner li a img.extract').parent().parent().addClass('inactive');
	}
	$(document).on('click', '.toolbar-inner li.inactive a', function(){
		return false;
	});
}

function addslashes(input){
var searchStr = "\'";
var replaceStr = "\\'";
var re = new RegExp(searchStr , "g");
var output = input.replace(re, replaceStr);
return output;
}
function basename(path){
return path.replace(/\\/g,'/').replace(/.*\//,'');
}
function dirname(path){
return path.replace(/\\/g,'/').replace(/\/[^\/]*$/,'');
}
function getfileextension(filename){
return (/[.]/.exec(filename))?/[^.]+$/.exec(filename):'';
}
function removefileextension(filename){
return filename.replace(/\.[^/.]+$/,'');
}
function setCheckRelation(){
	$(document).on('change', '.checkbox-selector', function(){
	var chk = $(this)[0].checked;
	var selector = $(this).attr('data-target');
	var len = $(selector).length;
	if(chk)
	{
		for(i = 0; i < len; i++)
		{
			 $(selector)[i].checked = true;
		}
	}
	else
	{
		for(i = 0; i < len; i++)
		{
			 $(selector)[i].checked = false;
		}
	}
	updateToolbarStatus();
	});
}
function jqAlert(msg, title, width, height)
{
$('#mb-area').remove();
$('body').append('<div id="mb-area" style="display:none;"><div id="message-box-dialog"><div id="message-box-dialog-inner"></div></div></div>');
if(!title) title = 'Alert';
if(!width) width = 300;
if(!height) height = 165;
try{$('#message-box-dialog').dialog('destroy');} catch(e){}
$('#message-box-dialog-inner').html(msg);
$('#message-box-dialog').dialog({
width:width,
height:height,
modal:true,
title:title,
buttons:{
'Close':function(){
try{$('#message-box-dialog').dialog('destroy');} catch(e){}
}
}
});
}
function contextMenu(selector, menu){
$(selector).on('contextmenu',function(e){
e.preventDefault();
var left = parseInt(e.clientX);
var top  = e.clientY;
var html;
var scrllf = $(document).scrollLeft();
var scrltp = $(document).scrollTop();
left = parseInt(left)+parseInt(scrllf);
top = parseInt(top)+parseInt(scrltp);
var width = parseInt($(window).width());
var height = parseInt($(window).height());
$('.kams-context-menu').remove();
html = '<div class="kams-context-menu"></div>';
$('body').append(html);
html = '<ul>';
for(var i in menu)
{
	var classname = menu[i]['classname'];
	if(!$(selector).hasClass('row-data-file') && !$(selector).hasClass('row-data-dir'))
	{
		if(clipboardfile.operation == '' && (classname == 'empty-clipboard' || classname == 'paste'))
		{
			continue;
		}
		if(classname == 'copy' || classname == 'cut' || classname == 'move' || classname == 'delete' || classname == 'compress' || classname == 'permission' || classname == 'uncheck')
		{
			if($('.fileid:checked').length == 0)
			{
				continue;
			}
		}
		if(($('.fileid:checked').length == $('.fileid').length) && classname == 'check')
		{
			continue;
		}
	}
	html += '<li class="file-function file-function-'+classname+'"><a href="'+menu[i]['linkurl']+'">'+menu[i]['caption']+'</a></li>';
}
html += '</ul>';
$('.kams-context-menu').html(html);
$('.kams-context-menu').css({'display':'none','left':left+'px','top':top+'px'});
var cmwidth = parseInt($('.kams-context-menu').width());
var cmheight = parseInt($('.kams-context-menu').height());
if((cmwidth + left + 16) >= width)
{
	left = left - cmwidth;
	$('.kams-context-menu').css({'left':left+'px','top':top+'px'});
}
if((cmheight + top + 20) >= height)
{
	top = height - parseInt(cmheight) - 20;
	$('.kams-context-menu').css({'left':left+'px','top':top+'px'});
}
$('.kams-context-menu').fadeIn(300);
$(document).on('click',function(){
	$('.kams-context-menu').fadeOut(150, function(){});
});
$(document).on('keydown', function(event){if((event.keyCode&&event.keyCode===$.ui.keyCode.ESCAPE)){
	$('.kams-context-menu').fadeOut(150, function(){});
}});
return false;
});
}

function setSize(){
var wh = parseInt($(window).height());
var ww = parseInt($(window).width());
var sw = parseInt($('.directory-area').outerWidth())+20;
$('.directory-area, .file-area').css('height', (wh-84)+'px');
$('.file-area').css({'width': (ww-sw)+'px', 'margin-left':(sw-14)+'px'});
}

function initContextMenuFileArea(){
var cm = [
{'caption':'Copy Selected File', 'linkurl':'javascript:copySelectedFile()', 'classname':'copy'},
{'caption':'Cut Selected File', 'linkurl':'javascript:cutSelectedFile()', 'classname':'cut'},
{'caption':'Move Selected File', 'linkurl':'javascript:moveSelectedFile()', 'classname':'move'},
{'caption':'Delete Selected File', 'linkurl':'javascript:deleteSelectedFile()', 'classname':'delete'},
{'caption':'Compress Selected File', 'linkurl':'javascript:compressSelectedFile()', 'classname':'compress'},
{'caption':'Set Permission','linkurl':'javascript:changePermission()',	'classname':'permission'},
{'caption':'Paste File', 'linkurl':'javascript:pasteFile()', 'classname':'paste'},
{'caption':'Create New File', 'linkurl':'javascript:createFile()', 'classname':'createfile'},
{'caption':'Create New Directory', 'linkurl':'javascript:createDirectory()', 'classname':'createdir'},
{'caption':'Up Directory', 'linkurl':'javascript:goToUpDir()', 'classname':'up'},
{'caption':'Refresh File List', 'linkurl':'javascript:refreshList()', 'classname':'refresh'},
{'caption':'Change View Type', 'linkurl':'javascript:thumbnail()', 'classname':'view'},
{'caption':'Upload File', 'linkurl':'javascript:uploadFile()', 'classname':'upload'},
{'caption':'Check All', 'linkurl':'javascript:selectAll(1)', 'classname':'check'},
{'caption':'Uncheck All', 'linkurl':'javascript:selectAll(0)', 'classname':'uncheck'},
{'caption':'Empty Clipboard', 'linkurl':'javascript:emptyClipboard()', 'classname':'empty-clipboard'}
];

contextMenu('.file-area', cm);
}

function initContextMenuFile(){
$('.row-data-file').each(function(index){
var filetype = $(this).attr('data-file-type');
var filename = $(this).attr('data-file-name');
var filelocation = $(this).attr('data-file-location');
var filepath = filelocation+'/'+filename;
var fileurl = $(this).attr('data-file-url');
var selfurl = $(this).attr('data-file-url');
if(filetype.indexOf('image')!=-1 || filetype.indexOf('shockwave')!=-1)
{
var width = $(this).attr('data-image-width');
var height = $(this).attr('data-image-height');	
attr = {'width':width, 'height':height};
contextMenu(this, contextMenuListFile(filetype, filepath, fileurl, attr));
}
else
{
contextMenu(this, contextMenuListFile(filetype, filepath, fileurl));
}
});
}

function initContextMenuDir(){
$('.row-data-dir').each(function(index){
var filetype = $(this).attr('data-file-type');
var filename = $(this).attr('data-file-name');
var filelocation = $(this).attr('data-file-location');
var filepath = filelocation+'/'+filename;
contextMenu(this, contextMenuListDir(filepath));
});
}

function contextMenuListFile(filetype, filepath, fileurl, attr){
filepath = addslashes(filepath);
fileurl = addslashes(fileurl);
var width = '0';
var height = '0';
var cm = new Array();
if(filetype.indexOf('image')==0)
{
width = parseInt(attr['width']);
height = parseInt(attr['height']);
cm = [
{'caption':'Select File',			'linkurl':'javascript:selectFile(\''+fileurl+'\')',		'classname':'select'},
{'caption':'Copy File',				'linkurl':'javascript:copyFile(\''+filepath+'\')',		'classname':'copy'},
{'caption':'Cut File',				'linkurl':'javascript:cutFile(\''+filepath+'\')',		'classname':'cut'},
{'caption':'Rename File',			'linkurl':'javascript:renameFile(\''+filepath+'\')',	'classname':'rename'},
{'caption':'Move File',				'linkurl':'javascript:moveFile(\''+filepath+'\')',		'classname':'move'},
{'caption':'Delete File',			'linkurl':'javascript:deleteFile(\''+filepath+'\')',	'classname':'delete'},
{'caption':'Preview Image',			'linkurl':'javascript:previewFile(\''+fileurl+'\', '+width+', '+height+', false, true)',	'classname':'preview'},
{'caption':'Edit Image',			'linkurl':'javascript:editImage(\''+filepath+'\')','classname':'edit-image'},
{'caption':'Compress File',			'linkurl':'javascript:compressFile(\''+filepath+'\')',	'classname':'compress'},
{'caption':'Set Permission','linkurl':'javascript:changePermission(\''+filepath+'\')',	'classname':'permission'},
{'caption':'Download File',			'linkurl':fileurl+'" target="_blank',					'classname':'download'},
{'caption':'Force Download File',	'linkurl':'javascript:forceDownloadFile(\''+filepath+'\')',	'classname':'download'},
{'caption':'Image Properties',		'linkurl':'javascript:propertyImage(\''+filepath+'\')',	'classname':'property'}
];
}
else if(filetype.indexOf('video')==0)
{
cm = [
{'caption':'Select File',			'linkurl':'javascript:selectFile(\''+fileurl+'\')',		'classname':'select'},
{'caption':'Copy File',				'linkurl':'javascript:copyFile(\''+filepath+'\')',		'classname':'copy'},
{'caption':'Cut File',				'linkurl':'javascript:cutFile(\''+filepath+'\')',		'classname':'cut'},
{'caption':'Rename File',			'linkurl':'javascript:renameFile(\''+filepath+'\')',	'classname':'rename'},
{'caption':'Move File',				'linkurl':'javascript:moveFile(\''+filepath+'\')',		'classname':'move'},
{'caption':'Delete File',			'linkurl':'javascript:deleteFile(\''+filepath+'\')',	'classname':'delete'},
{'caption':'Play Video',	'linkurl':'javascript:playVideo(\''+fileurl+'\', \'html5\')',	'classname':'play'},
{'caption':'Compress File',			'linkurl':'javascript:compressFile(\''+filepath+'\')',	'classname':'compress'},
{'caption':'Set Permission','linkurl':'javascript:changePermission(\''+filepath+'\')',	'classname':'permission'},
{'caption':'Download File',			'linkurl':fileurl+'" target="_blank',					'classname':'download'},
{'caption':'Force Download File',	'linkurl':'javascript:forceDownloadFile(\''+filepath+'\')',	'classname':'download'},
{'caption':'Video Properties',		'linkurl':'javascript:propertyVideo(\''+filepath+'\')',	'classname':'property'}
];
}
else if(filetype.indexOf('audio')==0)
{
cm = [
{'caption':'Select File',			'linkurl':'javascript:selectFile(\''+fileurl+'\')',		'classname':'select'},
{'caption':'Copy File',				'linkurl':'javascript:copyFile(\''+filepath+'\')',		'classname':'copy'},
{'caption':'Cut File',				'linkurl':'javascript:cutFile(\''+filepath+'\')',		'classname':'cut'},
{'caption':'Rename File',			'linkurl':'javascript:renameFile(\''+filepath+'\')',	'classname':'rename'},
{'caption':'Move File',				'linkurl':'javascript:moveFile(\''+filepath+'\')',		'classname':'move'},
{'caption':'Delete File',			'linkurl':'javascript:deleteFile(\''+filepath+'\')',	'classname':'delete'},
{'caption':'Play Audio',	'linkurl':'javascript:playAudio(\''+fileurl+'\', \'html5\')',	'classname':'play'},
{'caption':'Compress File',			'linkurl':'javascript:compressFile(\''+filepath+'\')',	'classname':'compress'},
{'caption':'Set Permission','linkurl':'javascript:changePermission(\''+filepath+'\')',	'classname':'permission'},
{'caption':'Download File',			'linkurl':fileurl+'" target="_blank',					'classname':'download'},
{'caption':'Force Download File',	'linkurl':'javascript:forceDownloadFile(\''+filepath+'\')',	'classname':'download'},
{'caption':'File Properties',		'linkurl':'javascript:propertyFile(\''+filepath+'\')',	'classname':'property'}
];
}
else if(filetype.indexOf('pdf')!=-1)
{
cm = [
{'caption':'Select File',			'linkurl':'javascript:selectFile(\''+fileurl+'\')',		'classname':'select'},
{'caption':'Copy File',				'linkurl':'javascript:copyFile(\''+filepath+'\')',		'classname':'copy'},
{'caption':'Cut File',				'linkurl':'javascript:cutFile(\''+filepath+'\')',		'classname':'cut'},
{'caption':'Rename File',			'linkurl':'javascript:renameFile(\''+filepath+'\')',	'classname':'rename'},
{'caption':'Move File',				'linkurl':'javascript:moveFile(\''+filepath+'\')',		'classname':'move'},
{'caption':'Delete File',			'linkurl':'javascript:deleteFile(\''+filepath+'\')',	'classname':'delete'},
{'caption':'Read PDF Document',		'linkurl':'javascript:previewPDF(\''+fileurl+'\')',		'classname':'preview'},
{'caption':'Compress File',			'linkurl':'javascript:compressFile(\''+filepath+'\')',	'classname':'compress'},
{'caption':'Set Permission','linkurl':'javascript:changePermission(\''+filepath+'\')',	'classname':'permission'},
{'caption':'Download File',			'linkurl':fileurl+'" target="_blank',					'classname':'download'},
{'caption':'Force Download File',	'linkurl':'javascript:forceDownloadFile(\''+filepath+'\')',	'classname':'download'},
{'caption':'File Properties',		'linkurl':'javascript:propertyFile(\''+filepath+'\')',	'classname':'property'}
];
}
else if(filetype.indexOf('shockwave')!=-1)
{
width = parseInt(attr['width']);
height = parseInt(attr['height']);
cm = [
{'caption':'Select File',			'linkurl':'javascript:selectFile(\''+fileurl+'\')',		'classname':'select'},
{'caption':'Copy File',				'linkurl':'javascript:copyFile(\''+filepath+'\')',		'classname':'copy'},
{'caption':'Cut File',				'linkurl':'javascript:cutFile(\''+filepath+'\')',		'classname':'cut'},
{'caption':'Rename File',			'linkurl':'javascript:renameFile(\''+filepath+'\')',	'classname':'rename'},
{'caption':'Move File',				'linkurl':'javascript:moveFile(\''+filepath+'\')',		'classname':'move'},
{'caption':'Delete File',			'linkurl':'javascript:deleteFile(\''+filepath+'\')',	'classname':'delete'},
{'caption':'View Shock Wave',		'linkurl':'javascript:previewSWF(\''+fileurl+'\', '+width+', '+height+')','classname':'preview'},
{'caption':'Compress File',			'linkurl':'javascript:compressFile(\''+filepath+'\')',	'classname':'compress'},
{'caption':'Set Permission','linkurl':'javascript:changePermission(\''+filepath+'\')',	'classname':'permission'},
{'caption':'Download File',			'linkurl':fileurl+'" target="_blank',					'classname':'download'},
{'caption':'Force Download File',	'linkurl':'javascript:forceDownloadFile(\''+filepath+'\')',	'classname':'download'},
{'caption':'File Properties',		'linkurl':'javascript:propertyFile(\''+filepath+'\')',	'classname':'property'}
];
}
else if(filetype.indexOf('application/zip')==0)
{
cm = [
{'caption':'Select File',			'linkurl':'javascript:selectFile(\''+fileurl+'\')',		'classname':'select'},
{'caption':'Copy File',				'linkurl':'javascript:copyFile(\''+filepath+'\')',		'classname':'copy'},
{'caption':'Cut File',				'linkurl':'javascript:cutFile(\''+filepath+'\')',		'classname':'cut'},
{'caption':'Rename File',			'linkurl':'javascript:renameFile(\''+filepath+'\')',	'classname':'rename'},
{'caption':'Move File',				'linkurl':'javascript:moveFile(\''+filepath+'\')',		'classname':'move'},
{'caption':'Delete File',			'linkurl':'javascript:deleteFile(\''+filepath+'\')',	'classname':'delete'},
{'caption':'Extract File',			'linkurl':'javascript:extractFile(\''+filepath+'\')',	'classname':'extract'},
{'caption':'Set Permission','linkurl':'javascript:changePermission(\''+filepath+'\')',	'classname':'permission'},
{'caption':'Download File',			'linkurl':fileurl+'" target="_blank',					'classname':'download'},
{'caption':'Force Download File',	'linkurl':'javascript:forceDownloadFile(\''+filepath+'\')',	'classname':'download'},
{'caption':'File Properties',		'linkurl':'javascript:propertyFile(\''+filepath+'\')',	'classname':'property'}
];
}
else if(filetype.indexOf('text')==0 || filetype.indexOf('php')!=-1)
{
cm = [
{'caption':'Select File',			'linkurl':'javascript:selectFile(\''+fileurl+'\')',		'classname':'select'},
{'caption':'Copy File',				'linkurl':'javascript:copyFile(\''+filepath+'\')',		'classname':'copy'},
{'caption':'Cut File',				'linkurl':'javascript:cutFile(\''+filepath+'\')',		'classname':'cut'},
{'caption':'Rename File',			'linkurl':'javascript:renameFile(\''+filepath+'\')',	'classname':'rename'},
{'caption':'Move File',				'linkurl':'javascript:moveFile(\''+filepath+'\')',		'classname':'move'},
{'caption':'Delete File',			'linkurl':'javascript:deleteFile(\''+filepath+'\')',	'classname':'delete'},
{'caption':'Edit as Text',			'linkurl':'javascript:editFile(\''+filepath+'\')',		'classname':'edit'},
{'caption':'Edit Code',				'linkurl':'code-editor.php?filepath='+encodeURIComponent(filepath)+'" target="_blank',		'classname':'edit'},
{'caption':'Compress File',			'linkurl':'javascript:compressFile(\''+filepath+'\')',	'classname':'compress'},
{'caption':'Set Permission','linkurl':'javascript:changePermission(\''+filepath+'\')',	'classname':'permission'},
{'caption':'Download File',			'linkurl':fileurl+'" target="_blank',					'classname':'download'},
{'caption':'Force Download File',	'linkurl':'javascript:forceDownloadFile(\''+filepath+'\')',	'classname':'download'},
{'caption':'File Properties',		'linkurl':'javascript:propertyFile(\''+filepath+'\')',	'classname':'property'}
];
}
else
{
cm = [
{'caption':'Select File',			'linkurl':'javascript:selectFile(\''+fileurl+'\')',		'classname':'select'},
{'caption':'Copy File',				'linkurl':'javascript:copyFile(\''+filepath+'\')',		'classname':'copy'},
{'caption':'Cut File',				'linkurl':'javascript:cutFile(\''+filepath+'\')',		'classname':'cut'},
{'caption':'Rename File',			'linkurl':'javascript:renameFile(\''+filepath+'\')',	'classname':'rename'},
{'caption':'Move File',				'linkurl':'javascript:moveFile(\''+filepath+'\')',		'classname':'move'},
{'caption':'Delete File',			'linkurl':'javascript:deleteFile(\''+filepath+'\')',	'classname':'delete'},
{'caption':'Compress File',			'linkurl':'javascript:compressFile(\''+filepath+'\')',	'classname':'compress'},
{'caption':'Set Permission','linkurl':'javascript:changePermission(\''+filepath+'\')',	'classname':'permission'},
{'caption':'Download File',			'linkurl':fileurl+'" target="_blank',					'classname':'download'},
{'caption':'Force Download File',	'linkurl':'javascript:forceDownloadFile(\''+filepath+'\')',	'classname':'download'},
{'caption':'File Properties',		'linkurl':'javascript:propertyFile(\''+filepath+'\')',	'classname':'property'}
];
}
return cm;
}
function contextMenuListDir(filepath){
filepath = addslashes(filepath);
var cm = new Array();
cm = [
{'caption':'Open Directory',			'linkurl':'javascript:;" onClick="return openDir(\''+filepath+'\')', 'classname':'open'},
{'caption':'Copy Directory',			'linkurl':'javascript:copyFile(\''+filepath+'\')',		'classname':'copy'},
{'caption':'Rename Directory',			'linkurl':'javascript:renameFile(\''+filepath+'\', true)',	'classname':'rename'},
{'caption':'Cut Directory',				'linkurl':'javascript:cutFile(\''+filepath+'\')',		'classname':'cut'},
{'caption':'Move Directory',			'linkurl':'javascript:moveFile(\''+filepath+'\', true)','classname':'move'},
{'caption':'Delete Directory',			'linkurl':'javascript:deleteDirectory(\''+filepath+'\')','classname':'delete'},
{'caption':'Compress Directory',		'linkurl':'javascript:compressFile(\''+filepath+'\')',	'classname':'compress'},
{'caption':'Set Permission',	'linkurl':'javascript:changePermission(\''+filepath+'\')',	'classname':'permission'},
{'caption':'Directory Properties',		'linkurl':'javascript:propertyDir(\''+filepath+'\')',	'classname':'property'}
];
return cm;
}
var skipondrop = false;
// file function
function goToUpDir(){
openDir(dirname($('#address').val()));
}
function forceDownloadFile(filepath)
{
window.open('tool-download-file.php?filepath='+encodeURIComponent(filepath));
}
var thumbnailIndexCur = 0;
var thumbnailIndexNext = 0;
var thumbnailIndexPrev = 0;

function previewFile(url, width, height, fullsize, frommenu)
{
var w = width, h = height, html = '';
var bn = basename(url);
if(arrthumbnail.length)
{
	thumbnailIndexCur = $.inArray(url, arrthumbnailURL);
	if(thumbnailIndexCur == -1)
	{
		thumbnailIndexCur = 0;
		thumbnailIndexNext = 0;
		thumbnailIndexPrev = 0;
	}
	else if(thumbnailIndexCur == 0)
	{
		thumbnailIndexNext = thumbnailIndexCur + 1;
		thumbnailIndexPrev = arrthumbnailURL.length - 1;
	}
	else if(thumbnailIndexCur >= arrthumbnailURL.length)
	{
		thumbnailIndexNext = 0;
		thumbnailIndexPrev = arrthumbnailURL.length - 1;
	}
	else
	{
		thumbnailIndexNext = thumbnailIndexCur + 1;
		thumbnailIndexPrev = thumbnailIndexCur - 1;
	}
	thumbnailIndexNext = thumbnailIndexNext % arrthumbnailURL.length;
	thumbnailIndexPrev = thumbnailIndexPrev % arrthumbnailURL.length;
	if(thumbnailIndexNext < 0) thumbnailIndexNext = 0;
}

if(fullsize){
html = '<img src="'+url+'" width="'+w+'" height="'+h+'" class="image2zoomout" title="'+bn+'" onclick="previewFile(\''+url+'\', \''+width+'\', \''+height+'\', false);" />';
}
else
{
if(width>500)
{
	w = 500;
	h = (height/width)*w;
}
html = '<img src="'+url+'" width="'+w+'" height="'+h+'" class="image2zoomin" title="'+bn+'" onclick="previewFile(\''+url+'\', \''+width+'\', \''+height+'\', true);" />';
}
var prevThumb = new Array();
prevThumb['url'] = arrthumbnail[thumbnailIndexPrev]['url'];
prevThumb['width'] = arrthumbnail[thumbnailIndexPrev]['image_width'];
prevThumb['height'] = arrthumbnail[thumbnailIndexPrev]['image_height'];

var nextThumb = new Array();
nextThumb['url'] = arrthumbnail[thumbnailIndexNext]['url'];
nextThumb['width'] = arrthumbnail[thumbnailIndexNext]['image_width'];
nextThumb['height'] = arrthumbnail[thumbnailIndexNext]['image_height'];

var navtop = Math.round((h/2) - 3);
var navleft = 0;
var navright = w - 8;
var tooltop = navtop + 32;
var toolright = navright - 90;
var prevHTML = '';
var nextHTML = '';
var toolHTML = '';
prevHTML += '<div class="nav-thumb nav-thum-prev" style="top:'+navtop+'px;left:'+navleft+'px;" onclick="previewFile(\''+prevThumb['url']+'\', '+prevThumb['width']+', '+prevThumb['height']+');">&lt;</div>';
nextHTML += '<div class="nav-thumb nav-thum-next" style="top:'+navtop+'px;left:'+navright+'px;" onclick="previewFile(\''+nextThumb['url']+'\', '+nextThumb['width']+', '+nextThumb['height']+');">&gt;</div>';
toolHTML += '<div class="tool-thumb" style="top:'+tooltop+'px;left:'+toolright+'px;">'+
'<a href="javascript:;" onclick="selectFileIndex(\''+url+'\');" title="Select Image"><img src="style/images/trans16.gif" class="select" border="0"></a> '+
'<a href="javascript:;" onclick="window.open(\''+url+'\');" title="Download Image"><img src="style/images/trans16.gif" class="download" border="0"></a> '+
'<a href="javascript:;" onclick="editImage(\''+relative2absolute(url)+'\');closeOverlayDialog();" title="Edit Image"><img src="style/images/trans16.gif" class="edit-image" border="0"></a> '+
'<a href="javascript:;" onclick="propertyImage(\''+relative2absolute(url)+'\');" title="Image Properties"><img src="style/images/trans16.gif" class="property" border="0"></a> '+
'</div>';

if(w>60 && arrthumbnail.length > 1)
{
html += (prevHTML+nextHTML+toolHTML);
}
if(frommenu && togglethumb)
{
	var obj = $('li[data-file-url="'+url+'"] .thumbimage img');
	var lfrom = parseFloat(obj.offset().left);
	var tfrom = parseFloat(obj.offset().top);
	
	var tw = 96;
	if(h>w)
	{
		tw = 96 * w / h;
		lfrom += (96-tw)/2;
	}
	var th = 96;
	if(w>h)
	{
		th = 96 * h / w;
		tfrom += (96-th)/2;
	}
	var lto = (parseFloat($(window).width())-w)/2;
	var tto = (parseFloat($(window).height())-h)/2;
	var bgurl = 'url(tool-thumb-image.php?filepath='+encodeURIComponent(relative2absolute(url))+')';
	$('<img />').addClass('anim-preview').attr('src', url).css({'background-image':bgurl, 'background-size':'100%', 'width':tw+'px', 'height':th+'px', 'left':lfrom+'px', 'top':tfrom+'px', 'position':'absolute', 'z-index':'5000'}).appendTo('body');
	$('.anim-preview').animate({left:lto, top:tto, width:w, height:h}, 200, function(){
		$('.anim-preview').remove();
		overlayDialog(html, w, h);
		$('.image2zoomout, .image2zoomin').on('mouseover',function(){
			var imgsrc = $(this).attr('src');
			$('li[data-file-url="'+imgsrc+'"] .thumbitem').addClass('thumbitem-selected');
		});
		 
		$('.image2zoomout, .image2zoomin').on('mouseout',function(){
			var imgsrc = $(this).attr('src');
			$('li[data-file-url="'+imgsrc+'"] .thumbitem').removeClass('thumbitem-selected');
		});
	});
}
else
{
	overlayDialog(html, w, h);
	$('.image2zoomout, .image2zoomin').on('mouseover',function(){
		var imgsrc = $(this).attr('src');
		$('li[data-file-url="'+imgsrc+'"] .thumbitem').addClass('thumbitem-selected');
	});
	$('.image2zoomout, .image2zoomin').on('mouseout',function(){
		var imgsrc = $(this).attr('src');
		$('li[data-file-url="'+imgsrc+'"] .thumbitem').removeClass('thumbitem-selected');
	});
}
}
function preloadImage()
{
	$(document).on('hover', '.row-data-file[data-file-type*="image"]', function() {
        var url = $(this).attr('data-file-url');
		var w = parseInt($(this).attr('data-image-width'));
		var h = parseInt($(this).attr('data-image-height'));
		var img = new Image(w, h);
		img.src = url;
    });
}
function playVideo(url, type)
{
var html = '';
if(type=='iframe')
html = '<iframe frameborder="0" hspace="0" vspace="0" marginheight="0" marginwidth="0" scrolling="auto" src="'+url+'" width="500" height="375" /></ifame>';
else if(type=='html5')
html = '<video src="'+url+'" poster="style/images/movie.png" controls="true" width="500" height="375"><object width="500" height="375" type="application/x-shockwave-flash" data="moxieplayer.swf"><param name="url" value="'+url+'"><param name="src" value="moxieplayer.swf"><param name="allowfullscreen" value="true"><param name="allowscriptaccess" value="true"><param name="autoplay" value="true"><param name="flashvars" value="url='+url+'&amp;poster=style/images/movie.png&amp;autoplay=true"></object></video>';
else if(type=='moxie')
html = '<object width="500" height="375" type="application/x-shockwave-flash" data="moxieplayer.swf"><param name="url" value="'+url+'"><param name="src" value="moxieplayer.swf"><param name="allowfullscreen" value="true"><param name="allowscriptaccess" value="true"><param name="autoplay" value="true"><param name="flashvars" value="url='+url+'&amp;poster=style/images/movie.png&amp;autoplay=true"></object>';
else
html = '<object width="500" height="375" data="'+url+'" type="application/x-mplayer2"><param name="url" value="'+url+'" /></object>';
overlayDialog(html, 500, 375);
}

function playAudio(url, type)
{
var html = '';
if(type=='iframe')
html = '<iframe frameborder="0" hspace="0" vspace="0" marginheight="0" marginwidth="0" scrolling="auto" src="'+url+'" width="320" height="50" /></ifame>';
else if(type=='html5')
html = '<video src="'+url+'" poster="audio.jpg" controls="true" width="320" height="50"><object width="320" height="50" data="'+url+'" type="application/x-mplayer2"><param name="url" value="'+url+'" /></object></video>';
else if(type=='moxie')
html = '<object width="320" height="50" type="application/x-shockwave-flash" data="moxieplayer.swf"><param name="url" value="'+url+'"><param name="src" value="moxieplayer.swf"><param name="allowfullscreen" value="true"><param name="allowscriptaccess" value="true"><param name="autoplay" value="true"><param name="flashvars" value="url='+url+'&amp;poster=style/images/audio.png&amp;autoplay=true"></object>';
else 
html = '<object width="320" height="50" data="'+url+'" type="application/x-mplayer2"><param name="url" value="'+url+'" /></object>';
overlayDialog(html, 320, 50);
}

function previewPDF(url){
var html = '<embed src="'+url+'#toolbar=0&amp;navpanes=0&amp;scrollbar=0" width="720" height="400">';
overlayDialog(html, 720, 400);
}
function previewSWF(url, width, height)
{
var html = '<object width="'+width+'" height="'+height+'" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0"><param name="src" value="'+url+'"><embed src="'+url+'" width="'+width+'" height="'+height+'"></embed></object>';
overlayDialog(html, width, height);	
}
function propertyFile(filepath)
{
$('#common-dialog-inner').html('');
$('#common-dialog').dialog({
	modal:true,
	title:'File Properties',
	width:400,
	height:270,
	buttons:
	{
		'Close':function(){
			$(this).dialog('destroy');
		}
	}
});
$.get('tool-property-file.php', {'filepath':filepath}, function(answer){
	$('#common-dialog-inner').html(answer);
	var mime = $('.mime-type').attr('data-content');
	var path = $('.mime-type').attr('data-path');
	if(mime == 'application/zip')
	{
		$('.ui-dialog-buttonset').prepend('<button aria-disabled="false" role="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" type="button"><span class="ui-button-text">Show Content</span></button>');
		$('.ui-dialog-buttonset').find('button:first').attr('onclick', 'showZipContent(\''+filepath+'\')');
	}
});
}
function propertyImage(filepath)
{
$('#common-dialog-inner').html('');
$('#common-dialog').dialog({
	modal:true,
	title:'Image Properties',
	width:400,
	height:410,
	buttons:{
		'Close':function(){
			$(this).dialog('destroy');
		}
	}
});
$.get('tool-property-file.php', {'filepath':filepath, 'type':'image'}, function(answer){
	$('#common-dialog-inner').html(answer);
});
}
function propertyVideo(filepath)
{
$('#common-dialog-inner').html('');
$('#common-dialog').dialog({
	modal:true,
	title:'Video Properties',
	width:400,
	height:410,
	buttons:{
		'Close':function(){
			$(this).dialog('destroy');
		}
	}
});
$.get('tool-property-file.php', {'filepath':filepath, 'type':'video'}, function(answer){
	$('#common-dialog-inner').html(answer);
});
}
function propertyDir(filepath)
{
$('#common-dialog-inner').html('');
$('#common-dialog').dialog({
	modal:true,
	title:'Directory Properties',
	width:400,
	height:230,
	buttons:
	{
		'Close':function(){
			$(this).dialog('destroy');
		}
	}
});
$.get('tool-property-file.php', {'filepath':filepath, 'type':'directory'}, function(answer){
	$('#common-dialog-inner').html(answer);
});
}
function showZipContent(filepath)
{
$('#common-dialog-inner').html('');
$('#common-dialog').dialog({
	modal:true,
	title:'Zip Content',
	width:400,
	height:230,
	buttons:
	{
		'Extract File':function(){
			extractFile(filepath);
		},
		'Close':function(){
			$(this).dialog('destroy');
		}
	}
});
$.get('tool-zip-content.php', {'filepath':filepath}, function(answer){
	$('#common-dialog-inner').html(answer);
});
}
function renameFile(filepath, isdir)
{
String.prototype.trim=function () 
{
return this.replace(/^\s+|\s+$/g,'');
}
if(!filepath){
	// assume this is a file
	// get selected file
	var pth;
	$('.fileid:checked').each(function(index){
	if(pth==undefined){
		pth = $(this).attr('value');
		if(pth!=undefined){
			filepath = pth;
			if($(this).attr('data-isdir')=='true'){
				isdir = true;
			}
		}
	}
	});
}
if(filepath)
{
$('#common-dialog-inner').html('');
if(isdir){
var title = 'Rename Directory';
}
else{
var title = 'Rename File';
}
$('#common-dialog').dialog({
	modal:true,
	title:title,
	width:400,
	height:200,
	buttons:
	{
	'OK':function(){
	var dl = $('#fflocation').val();
	var on = $('#ffoldname').val();
	var nn = $('#ffnewname').val();
	try{
	var oe = getfileextension(on.trim());
	}
	catch(e){
	var oe = getfileextension(on);
	}
	try{
	var ne = getfileextension(nn.trim());
	}
	catch(e){
	var ne = getfileextension(nn);
	}
	try{
	nn = nn.trim();
	}
	catch(e){
	nn = nn;
	}
	if(nn == ''){
		jqAlert('Please type new name.', 'Input Needed');
		$('#ffnewname').val(on);
		$('#ffnewname').select();
	}
	else if(on == nn){
		jqAlert('New name and old name must be different.', 'Invalid Name');
	}
	else{
		if(oe.toString() != ne.toString() && !isdir)
		{
			if(!confirm('The new file extension is different with old one.\n'+
					   'If you change a file name extension, the file might become unusable.\n'+
					   'Are you sure you want to change it?'))
			{
				return;
			}
		}
		$.post('tool-file-operation.php?option=renamefile', {'location':dl, 'oldname':on, 'newname':nn}, function(answer){
		if(answer=='SUCCESS'){
			openDir(dl);
			try{$('#common-dialog').dialog('destroy');} catch(e){}				
		}
		else if(answer=='EXIST'){
			jqAlert(nn+' already exists. Please type another name.', 'Invalid Name');
		}
		else if(answer=='READONLY'){
			jqAlert('The operation was disabled on read-only mode.', 'Read-Only');
		}
		else if(answer=='FORBIDDENEXT')
		{
		jqAlert('The operation was aborted because this file name extension is forbidden. Please use another file name extension.', 'Forbidden Extension');
		}
		});
	}
	},
	'Cancel':function(){
		$(this).dialog('destroy');
	}
	}
});
var html = ''+
'<form id="formfilerename" name="form1" method="post" action="">'+
'<table width="100%" border="0" cellpadding="0" cellspacing="0" class="dialog-table">'+
'<tr>'+
'<td width="30%">Location</td>'+
'<td><input type="text" name="fflocation" id="fflocation" class="input-text" autocomplete="off" readonly="readonly" /></td>'+
'</tr>'+
'<tr>'+
'<td>Current Name</td>'+
'<td><input type="text" name="ffoldname" id="ffoldname" class="input-text" autocomplete="off" readonly="readonly" /></td>'+
'</tr>'+
'<tr>'+
'<td>New Name</td>'+
'<td><input type="text" name="ffnewname" id="ffnewname" class="input-text" autocomplete="off" /></td>'+
'</tr>'+
'</table>'+
'</form>';
$('#common-dialog-inner').html(html);
$('#fflocation').val(dirname(filepath));
$('#ffoldname, #ffnewname').val(basename(filepath));
var name = removefileextension($('#ffnewname').val());
startPos = 0;
endPos = name.length;
setInputSelection(document.getElementById('ffnewname'), startPos, endPos);
}
else
{
	jqAlert('No file or directory selected.', 'Invalid Operation');
	return;
}
}

function compressFile(filepath)
{
$('#common-dialog-inner').html('');
$('#common-dialog').dialog({
	modal:true,
	title:'Compress File',
	width:400,
	height:175,
	buttons:
	{
	'OK':function(){
	$('.ui-dialog-buttonpane').append('<div class="wait-status">Plase wait...</div>');
	$('.ui-dialog-buttonset button:first').attr('disabled', 'disabled');
	$('.ui-dialog-buttonset button:first').attr('aria-disabled', 'true');
	var sf = $('#ffsourcepath').val();
	var tf = $('#fftargetpath').val();
	$.post('tool-file-operation.php?option=compressfile', {'sourcepath[]':sf,'targetpath':tf}, function(answer){
	if(answer == 'CONFLICT'){
	jqAlert(filepath+' already exists. Please enter another name.', 'Invalid Name');
	}
	else if(answer == 'SUCCESS'){
	openDir(dirname(tf));
	try{$('#common-dialog').dialog('destroy');} catch(e){}
	}
	else if(answer == 'FAILED'){
	jqAlert('The operation was failed.', 'Unknown Error Occured');
	}
	else if(answer == 'NOTSUPPORTED'){
	jqAlert('The operation was failed.', 'ZipArchive class not exists. Please verify that php_zip extension is available on this server.');
	}
	else if(answer=='READONLY'){
	jqAlert('The operation was disabled on read-only mode.', 'Read-Only');
	}
	$('.ui-dialog-buttonpane').find('.wait-status').remove();
	$('.ui-dialog-buttonset button:first').removeAttr('disabled');
	$('.ui-dialog-buttonset button:first').attr('aria-disabled', 'false');
	});
	},
	'Cancel':function(){
	$(this).dialog('destroy');
	}
	}
});
var html = ''+
'<form id="formfilerename" name="form1" method="post" action="">'+
'<table width="100%" border="0" cellpadding="0" cellspacing="0" class="dialog-table">'+
'<tr>'+
'<td width="30%">Source Name</td>'+
'<td><input type="text" name="ffsourcepath" id="ffsourcepath" class="input-text" autocomplete="off" readonly="readonly" /></td>'+
'</tr>'+
'<tr>'+
'<td>Target Name</td>'+
'<td><input type="text" name="fftargetpath" id="fftargetpath" class="input-text" autocomplete="off" /></td>'+
'</tr>'+
'</table>'+
'</form>';
$('#common-dialog-inner').html(html);
$('#ffsourcepath').val(filepath);
$('#fftargetpath').val(removefileextension(filepath)+'.zip');
var dir = dirname($('#fftargetpath').val());
var startPos = dir.length + 1;
var name = removefileextension(basename($('#fftargetpath').val()));
var endPos = startPos+name.length;
setInputSelection(document.getElementById('fftargetpath'), startPos, endPos);
}
function compressSelectedFile(){
var dl = $('#address').val();
var file2compress = '';
var chk = 0;
var html = '';
$('.fileid:checked').each(function(index){
	file2compress += '<div>'+$(this).val()+'</div>';
	chk++;
});

if(chk){
$('#common-dialog-inner').html('');
$('#common-dialog').dialog({
	modal:true,
	title:'Compress File',
	width:400,
	height:260,
	buttons:
	{
	'OK':function(){
	$('.ui-dialog-buttonpane').append('<div class="wait-status">Plase wait...</div>');
	$('.ui-dialog-buttonset button:first').attr('disabled', 'disabled');
	$('.ui-dialog-buttonset button:first').attr('aria-disabled', 'true');
	var targetpath = $('#fftargetpath').val();
	var args = 'targetpath='+encodeURIComponent(targetpath);
	$('.fileid:checked').each(function(index){
	args+='&sourcepath[]='+encodeURIComponent($(this).val());
	});
	$.post('tool-file-operation.php?option=compressfile', {'postdata':args}, function(answer){
		if(answer=='SUCCESS' || answer=='EXIST'){
			openDir(dirname(targetpath));
			try{$('#common-dialog').dialog('destroy');} catch(e){}
		}
		if(answer == 'CONFLICT'){
			jqAlert('Please enter another name.', 'Invalid Name');
		}
		else if(answer == 'SUCCESS'){
			openDir(dirname(tf));
			try{$('#common-dialog').dialog('destroy');} catch(e){}
		}
		else if(answer == 'FAILED'){
			jqAlert('The operation was failed.', 'Unknown Error Occured');
		}
		else if(answer == 'NOTSUPPORTED'){
		jqAlert('The operation was failed.', 'ZipArchive class not exists. Please verify that php_zip extension is available on this server.');
		}
		else if(answer=='READONLY'){
			jqAlert('The operation was disabled on read-only mode.', 'Read-Only');
		}
		$('.ui-dialog-buttonpane').find('.wait-status').remove();
		$('.ui-dialog-buttonset button:first').removeAttr('disabled');
		$('.ui-dialog-buttonset button:first').attr('aria-disabled', 'false');
	});
	},
	'Cancel':function(){
		$(this).dialog('destroy');
	}
	}
});

html = ''+
'<table width="100%" border="0" cellpadding="0" cellspacing="0" class="dialog-table">'+
'<tr>'+
'<td width="30%">Target Name</td>'+
'<td><input type="text" name="fftargetpath" id="fftargetpath" class="input-text" autocomplete="off" /></td>'+
'</tr>'+
'</table>'+
'<div></div><div>File to be compressed:</div><div class="seleted-file-list">'+file2compress+'</div></div>';
$('#common-dialog-inner').html(html);
var val = 'new-compressed';
var i = 1;
var dir = val+'.zip';
var num1 = 1;
var num2 = 1;
do
{
	if(i>1)
	dir = val+'-'+i+'.zip';
	else
	dir = val+'.zip';
	num1 = $('.file-area .row-data-dir[data-file-name="'+dir+'"]').length;
	num2 = $('.file-area .row-data-file[data-file-name="'+dir+'"]').length;
	i++;
}
while(num1 || num2);
$('#fftargetpath').val(dl+'/'+dir);
var dir = dirname($('#fftargetpath').val());
var startPos = dir.length + 1;
var name = removefileextension(basename($('#fftargetpath').val()));
var endPos = startPos+name.length;
setInputSelection(document.getElementById('fftargetpath'), startPos, endPos);
}
else
{
	jqAlert('No file selected.', 'Invalid Operation');
}
}
function moveSelectedFile(){
var dl = $('#address').val();
var file2move = '';
var chk = 0;
var html = '';
$('.fileid:checked').each(function(index){
	file2move += '<div>'+$(this).val()+'</div>';
	chk++;
});
if(chk){
$('#common-dialog-inner').html('');
$('#common-dialog').dialog({
	modal:true,
	title:'Move File',
	width:400,
	height:260,
	buttons:
	{
	'OK':function(){
	var targetdir = $('#fftargetdir').val();
	if(dl!=targetdir){
	var args = 'targetdir='+encodeURIComponent(targetdir);
	$('.fileid:checked').each(function(index){
	args+='&file[]='+encodeURIComponent($(this).val());
	});
	var q = '?option=copyfile&deletesource=1';
	$.post('tool-file-operation.php'+q, {'postdata':args}, function(answer){
	if(answer=='SUCCESS' || answer=='EXIST'){
	openDir($('#fftargetdir').val());
	try{$('#common-dialog').dialog('destroy');} catch(e){}
	}
	else if(answer=='READONLY'){
	jqAlert('The operation was disabled on read-only mode.', 'Read-Only');
	}
	$('.ui-dialog-buttonpane').find('.wait-status').remove();
	$('.ui-dialog-buttonset button:first').removeAttr('disabled');
	$('.ui-dialog-buttonset button:first').attr('aria-disabled', 'false');
	});
	}
	else
	{
	jqAlert('Please enter another name.', 'Invalid Name');
	}
	},
	'Cancel':function(){
	$(this).dialog('destroy');
	}
	}
});

html = ''+
'<table width="100%" border="0" cellpadding="0" cellspacing="0" class="dialog-table">'+
'<tr>'+
'<td width="30%">New Location</td>'+
'<td><input type="text" name="fftargetdir" id="fftargetdir" class="input-text" autocomplete="off" /></td>'+
'</tr>'+
'</table>'+
'<div></div><div>File to be moved:</div><div class="seleted-file-list">'+file2move+'</div></div>';
$('#common-dialog-inner').html(html);
$('#fftargetdir').focus();
$('#fftargetdir').val(dl);
}
else
{
	jqAlert('No file selected.', 'Invalid Operation');
}
}
function extractFile(filepath)
{
if(!filepath)
{
	var pth = $('.fileid:checked[data-iszip=true]').attr('value');
	if(pth!=undefined){
	filepath = pth;
	}
	else{
	jqAlert('No file selected.', 'Invalid Operation');
	return;
	}
}
$('#common-dialog-inner').html('');
$('#common-dialog').dialog({
	modal:true,
	title:'Extract File',
	width:400,
	height:175,
	buttons:
	{
	'OK':function(){
	$('.ui-dialog-buttonpane').append('<div class="wait-status">Plase wait...</div>');
	$('.ui-dialog-buttonset button:first').attr('disabled', 'disabled');
	$('.ui-dialog-buttonset button:first').attr('aria-disabled', 'true');
	var filepath = $('#ffsourcename').val();
	var targetdir = $('#fftargetdir').val();
	$.post('tool-file-operation.php?option=extractfile', {'filepath':filepath, 'targetdir':targetdir}, function(answer){
	if(answer=='SUCCESS'){
	openDir(targetdir);
	try{$('#common-dialog').dialog('destroy');} catch(e){}
	}
	else if(answer=='FAILED'){
	jqAlert('This is not a Zip file.', 'Invalid Format');
	}
	else if(answer == 'NOTSUPPORTED'){
	jqAlert('The operation was failed.', 'ZipArchive class not exists. Please verify that php_zip extension is available on this server.');
	}
	else if(answer=='READONLY'){
	jqAlert('The operation was disabled on read-only mode.', 'Read-Only');
	}
	});
	},
	'Cancel':function(){
	$(this).dialog('destroy');
	}
	}
});
var html = ''+
'<form id="formfilerename" name="form1" method="post" action="">'+
'<table width="100%" border="0" cellpadding="0" cellspacing="0" class="dialog-table">'+
'<tr>'+
'<td width="30%">Source Name</td>'+
'<td><input type="text" name="ffsourcename" id="ffsourcename" class="input-text" autocomplete="off" readonly="readonly" /></td>'+
'</tr>'+
'<tr>'+
'<td>Target Location</td>'+
'<td><input type="text" name="fftargetdir" id="fftargetdir" class="input-text" autocomplete="off" /></td>'+
'</tr>'+
'</table>'+
'</form>';
$('#common-dialog-inner').html(html);
$('#ffsourcename').val(filepath);
$('#fftargetdir').val(dirname(filepath));
}
function createFile()
{
var dir = $('#address').val();
$('#common-dialog-inner').html('');
$('#common-dialog').dialog({
	modal:true,
	title:'Create New File',
	width:400,
	height:200,
	buttons:{
	'OK':function(){
	var dl = $('#fflocation').val();
	var dn = $('#ffname').val();
	$.post('tool-file-operation.php?option=createfile', {'location':dl, 'name':dn}, function(answer){
		if(answer == 'EXIST')
		{
			jqAlert(dl+'/'+dn+' already exists. Please type another name.');
			$('#ffname').select();
		}
		else if(answer == 'SUCCESS')
		{
			openDir(dl);
			try{$('#common-dialog').dialog('destroy');}catch(e){}
		}
		else if(answer=='FORBIDDENEXT')
		{
			jqAlert('Creating file was aborted because this file name extension is forbidden. Please use another file name extension.', 'Forbidden Extension');
		}
		else if(answer=='READONLY')
		{
			jqAlert('The operation was disabled on read-only mode.', 'Read-Only');
		}
	});
	},
	'Cancel':function(){
	$(this).dialog('destroy');
	}
	}
});
var html = ''+
'<form id="formfilecreate" name="form1" method="post" action="">'+
'<table width="100%" border="0" cellpadding="0" cellspacing="0" class="dialog-table">'+
'<tr>'+
'<td width="30%">Location</td>'+
'<td><input type="text" name="fflocation" id="fflocation" class="input-text" autocomplete="off" readonly="readonly" /></td>'+
'</tr>'+
'<tr>'+
'<td>Directory Name</td>'+
'<td><input type="text" name="ffname" id="ffname" class="input-text" autocomplete="off" /></td>'+
'</tr>'+
'</table>'+
'</form>';
$('#common-dialog-inner').html(html);
$('#fflocation').val(dir);
var val = 'new-file';
var i = 1;
var dir = val+'.txt';
var num1 = 1;
var num2 = 1;
do
{
	if(i>1)
	dir = val+'-'+i+'.txt';
	else
	dir = val+'.txt';
	num1 = $('.file-area .row-data-dir[data-file-name="'+dir+'"]').length;
	num2 = $('.file-area .row-data-file[data-file-name="'+dir+'"]').length;
	i++;
}
while(num1 || num2);
$('#ffname').val(dir);
var name = removefileextension(dir);
var startPos = 0;
var endPos = name.length;
setInputSelection(document.getElementById('ffname'), startPos, endPos);
}

function createDirectory()
{
var dir = $('#address').val();
$('#common-dialog-inner').html('');
$('#common-dialog').dialog({
	modal:true,
	title:'Create Directory',
	width:400,
	height:200,
	buttons:{
	'OK':function(){
	var dl = $('#fflocation').val();
	var dn = $('#ffname').val();
	$.post('tool-file-operation.php?option=createdir', {'location':dl, 'name':dn}, function(answer){
	if(answer == 'EXIST')
	{
		jqAlert(dl+'/'+dn+' already exists. Please type another name.');
		$('#ffname').select();
	}
	else if(answer=='SUCCESS')
	{
		openDir(dl);
		try{$('#common-dialog').dialog('destroy');}catch(e){}
	}
	else if(answer=='READONLY')
	{
		jqAlert('The operation was disabled on read-only mode.', 'Read-Only');
	}
	});
	},
	'Cancel':function(){
		$(this).dialog('destroy');
	}
	}
});
var html = ''+
'<form id="formfilerename" name="form1" method="post" action="">'+
'<table width="100%" border="0" cellpadding="0" cellspacing="0" class="dialog-table">'+
'<tr>'+
'<td width="30%">Location</td>'+
'<td><input type="text" name="fflocation" id="fflocation" class="input-text" autocomplete="off" readonly="readonly" /></td>'+
'</tr>'+
'<tr>'+
'<td>Directory Name</td>'+
'<td><input type="text" name="ffname" id="ffname" class="input-text" autocomplete="off" /></td>'+
'</tr>'+
'</table>'+
'</form>';
$('#common-dialog-inner').html(html);
$('#fflocation').val(dir);
var val = 'new-directory';
var i = 1;
var dir = val;
var num1 = 1;
var num2 = 1;
do
{
	if(i>1)
	dir = val+'-'+i;
	else
	dir = val;
	num1 = $('.file-area .row-data-dir[data-file-name="'+dir+'"]').length;
	num2 = $('.file-area .row-data-file[data-file-name="'+dir+'"]').length;
	i++;
}
while(num1 || num2);
$('#ffname').val(dir);
$('#ffname').select();
}

function moveFile(filepath, isdir)
{
$('#common-dialog-inner').html('');
if(isdir){
var title = 'Move Directory';
}
else{
var title = 'Move File';
}
$('#common-dialog').dialog({
	modal:true,
	title:title,
	width:400,
	height:190,
	buttons:
	{
	'OK':function(){
	$('.ui-dialog-buttonpane').append('<div class="wait-status">Plase wait...</div>');
	$('.ui-dialog-buttonset button:first').attr('disabled', 'disabled');
	$('.ui-dialog-buttonset button:first').attr('aria-disabled', 'true');
	var dl = $('#address').val();
	var targetdir = $('#ffnewlocation').val();
	var curlocation = $('#ffcurrentlocation').val();
	var args = 'targetdir='+encodeURIComponent(targetdir);
	args+='&file[]='+encodeURIComponent(curlocation+'/'+$('#ffpath').val());
	var q = '?option=copyfile&deletesource=1';
	$.post('tool-file-operation.php'+q, {'postdata':args}, function(answer){
	if(answer=='SUCCESS' || answer=='EXIST'){
		openDir(dl);
		try{$('#common-dialog').dialog('destroy');} catch(e){}
	}
	else if(answer=='READONLY'){
		jqAlert('The operation was disabled on read-only mode.', 'Read-Only');
	}
	$('.ui-dialog-buttonpane').find('.wait-status').remove();
	$('.ui-dialog-buttonset button:first').removeAttr('disabled');
	$('.ui-dialog-buttonset button:first').attr('aria-disabled', 'false');
	});
	},
	'Cancel':function(){
		$(this).dialog('destroy');
	}
	}
});
var html = ''+
'<form id="formfilemove" name="form1" method="post" action="">'+
'<table width="100%" border="0" cellpadding="0" cellspacing="0" class="dialog-table">'+
'<tr>'+
'<td width="30%">Base Name</td>'+
'<td><input type="text" name="ffpath" id="ffpath" class="input-text" autocomplete="off" readonly="readonly" /></td>'+
'</tr>'+
'<tr>'+
'<td>Current Location</td>'+
'<td><input type="text" name="ffcurrentlocation" id="ffcurrentlocation" class="input-text" autocomplete="off" readonly="readonly" /></td>'+
'</tr>'+
'<tr>'+
'<td>New location</td>'+
'<td><input type="text" name="ffnewlocation" id="ffnewlocation" class="input-text" autocomplete="off" /></td>'+
'</tr>'+
'</table>'+
'</form>';
$('#common-dialog-inner').html(html);
$('#ffpath').val(basename(filepath));
$('#ffcurrentlocation').val(dirname(filepath));
$('#ffnewlocation').focus();
$('#ffnewlocation').val(dirname(filepath));
}
function uploadFile()
{
$('#common-dialog-inner').html('');
$('#common-dialog').dialog({
	modal:true,
	title:'Upload File',
	width:600,
	height:410,
	buttons:
	{
	'Close':function(){
		$(this).dialog('destroy');
	}
	}
});
var dl = $('#address').val();
var html = ''+
'<div id="imageuploader">'+
'<form method="post" enctype="multipart/form-data" action="tool-upload-file.php?iframe=1" target="formdumper">'+
'<input type="hidden" name="targetdir" id="targetdir" value="">'+
'File <input type="file" name="file" id="images" />'+
'<input type="submit" class="upload-button" value="Upload Files" style="display:none" /> &nbsp; <span id="image-settings-controller"></span>'+
'</form><div id="response"></div><ul id="image-list"></ul></div>'+
'<iframe style="display:none; width:0px; height:0px;" id="formdumper" name="formdumper"></iframe>'+
'</div>';

$('#common-dialog-inner').html(html);
$.get('tool-upload-file-settings.php', {'show-control':'1'}, function(answer){
$('#image-settings-controller').html(answer);
});

$('#targetdir').val(dl);
$.ajax({type: "GET", url: "js/upload.js", dataType: "script"});
}
function saveFile(filepath, filecontent, callback){
if(filepath=='')
{
	jqAlert('Please enter a valid file name.', 'Invalid File Name');
}
else
{
$('.ui-dialog-buttonpane').append('<div class="wait-status">Plase wait...</div>');
$('.ui-dialog-buttonset button:first').attr('disabled', 'disabled');
$('.ui-dialog-buttonset button:first').attr('aria-disabled', 'true');
$.post('tool-edit-file.php?option=savefile', {'filepath':filepath, 'filecontent':filecontent}, function(answer){
if(answer=='READONLY')
{
	jqAlert('The operation was disabled on read-only mode.', 'Read-Only');
}
else if(answer=='READONLYFILE')
{
	jqAlert('Saving was aborted because this file is read-only. You should to change permission of this file first.', 'Read-Only');
}
else if(answer=='ISDIR')
{
	jqAlert('Saving was aborted because this file name is similiar to a directory name. You should to change file name first.', 'Invalid File Name');
}
else if(answer=='FORBIDDENEXT')
{
	jqAlert('Saving was aborted because this file name extension is forbidden. Please use another file name extension to save it.', 'Forbidden Extension');
}
else if(answer=='NOTMODIFIED')
{
	jqAlert('Content is not modified.', 'Not Modified');
}
else
{
	if(typeof callback == 'function')
	{
		callback();
	}
	openDir();
}
$('.ui-dialog-buttonpane').find('.wait-status').remove();
$('.ui-dialog-buttonset button:first').removeAttr('disabled');
$('.ui-dialog-buttonset button:first').attr('aria-disabled', 'false');
});
}
}

function setActiveCompress(val)
{
	var value = val?1:0;
	$.post('tool-upload-file-settings.php', {'change-state':'change-state','state':value}, function(answer){
	});
}

function uploadFileSettings()
{
$('#common-dialog-inner').html('');
$('#common-dialog').dialog({
	modal:true,
	title:'Upload File Settings',
	width:420,
	height:270,
	buttons:
	{
	'Save':function(){
		var args = $('#uploadsetting').serialize();
		$.post('tool-upload-file-settings.php', {'save':'save','data':args}, function(answer){
		});
		$(this).dialog('close');
	},
	'Cancel':function(){
		$(this).dialog('destroy');
	}
	}
});
$.get('tool-upload-file-settings.php', {'show-form':'1'}, function(answer){
$('#common-dialog-inner').html(answer);
}); 

}

var cnt1 = '', cnt2 = '';
function openFile(filepath)
{
	cnt2 = $('#filecontent').val();
	var of = true;
	if(cnt1 != cnt2)
	{
		of = confirm('Are you sure you want to reopen file without save change?');
	}
	if(of)
	{
		editFile(filepath);
	}
}
function editFile(filepath)
{
$('#common-dialog-inner').html('');
var dl = $('#address').val();
$('#common-dialog').dialog({
	modal:true,
	title:'Edit Text File',
	closeOnEscape:false,
	resizable:true,
	width:600,
	height:410,
	buttons:
	{
		'Save':function(){
			saveFile($('#filepath').val(), $('#filecontent').val());
			cnt1 = $('#filecontent').val();
		},
		'Save and Close':function(){
			saveFile($('#filepath').val(), $('#filecontent').val(), function(){$('#common-dialog').dialog('destroy');});
		},
		'Close without Save':function(){
			cnt2 = $('#filecontent').val();
			if(cnt1 != cnt2)
			{
			if(confirm('Are you sure you want to close without save?'))
			{
				$(this).dialog('destroy');
			}
			}
			else
			{
				$(this).dialog('destroy');
			}
		}
	},
	resize: function(event, ui){
		$('#filecontent').css({'height':(ui.size.height - 158)+'px'}); 
	}
});
$('.ui-dialog-titlebar-close').remove();
$.get('tool-edit-file.php', {'option':'openfile','filepath':filepath}, function(answer){
	$('#common-dialog-inner').html(answer);
	cnt1 = $('#filecontent').val();
});
}
function deleteFile(filepath)
{
var dl = dirname(filepath);
$('#common-dialog-inner').html('');
$('#common-dialog').dialog({
	modal:true,
	title:'Delete File',
	width:400,
	height:175,
	buttons:
	{
	'OK':function(){
	var args = '';
	args = 'file[]='+filepath;
	$.post('tool-file-operation.php?option=deletefile', {'postdata':args}, function(answer){
	if(answer=='SUCCESS'){
		openDir(dl);
		try{$('#common-dialog').dialog('destroy');} catch(e){}
	}
	else if(answer=='READONLY'){
		jqAlert('The operation was disabled on read-only mode.', 'Read-Only');
	}
	});
	},
	'Cancel':function(){
		$(this).dialog('destroy');
	}
	}
});
var html = ''+
'<div>Are you sure to delete this file:<br />'+filepath+'</div>';
$('#common-dialog-inner').html(html);
$('.ui-dialog-buttonset button:last').focus();
}
function deleteDirectory(filepath)
{
var dl = $('#address').val();
$('#common-dialog-inner').html('');
$('#common-dialog').dialog({
	modal:true,
	title:'Delete Directory',
	width:400,
	height:175,
	buttons:
	{
	'OK':function(){
	var args = '';
	args = 'file[]='+filepath;
	$.post('tool-file-operation.php?option=deletefile', {'postdata':args}, function(answer){
	if(answer=='SUCCESS'){
	openDir(dl);
	try{$('#common-dialog').dialog('destroy');} catch(e){}
	}
	else if(answer=='READONLY'){
	jqAlert('The operation was disabled on read-only mode.', 'Read-Only');
	}
	});
	},
	'Cancel':function(){
		$(this).dialog('destroy');
	}
	}
});
var html = ''+
'<div>Are you sure to delete this directory including its content:<br />'+filepath+'</div>';
$('#common-dialog-inner').html(html);
$('.ui-dialog-buttonset button:last').focus();
}
function deleteSelectedFile(){
var dl = $('#address').val();
var args = '';
var file2del = '';
var chk = 0;
var html = '';
$('.fileid:checked').each(function(index){
	file2del += '<div>'+$(this).val()+'</div>';
	args += '&file[]='+encodeURIComponent($(this).val());
	chk++;
});
if(chk){
$('#common-dialog-inner').html('');
$('#common-dialog').dialog({
	modal:true,
	title:'Delete File',
	width:400,
	height:250,
	buttons:
	{
	'OK':function(){
	$.post('tool-file-operation.php?option=deletefile', {'postdata':args}, function(answer){
	if(answer=='SUCCESS'){
	openDir(dl);
	try{$('#common-dialog').dialog('destroy');} catch(e){}
	}
	else if(answer=='READONLY'){
	jqAlert('The operation was disabled on read-only mode.', 'Read-Only');
	}
	});
	},
	'Cancel':function(){
	$(this).dialog('destroy');
	}
	}
});	
html = '<div>Are you sure to delete file/s:</div><div class="seleted-file-list">'+file2del+'</div></div>';
$('#common-dialog-inner').html(html);
$('.ui-dialog-buttonset button:last').focus();
}
else{
	jqAlert('No file selected.', 'Invalid Operation');
}
}

function selectAll(sel){
	if(sel){
		var len = $('.fileid').length, i, j;
		for(i=0; i<len; i++)
		{
			$('.fileid')[i].checked = true;
		}
		try{
			$('#control-fileid')[0].checked = true;
		} 
		catch(e){}
	}
	else{
		var len = $('.fileid').length, i, j;
		for(i=0; i<len; i++)
		{
			$('.fileid')[i].checked = false;
		}
		try{
			$('#control-fileid')[0].checked = false;
		} 
		catch(e){}
	}
	updateToolbarStatus();
}
var togglethumb = false;
function thumbnail(){
var curdir = $('#address').val();
togglethumb = !togglethumb;
if(togglethumb)
{
	$('#tb-thumbnail').addClass('tb-selected');
}
else
{
	$('#tb-thumbnail').removeClass('tb-selected');
}
var selectedfile = new Array();
$('.fileid:checked').each(function(index){
	selectedfile[selectedfile.length] = $(this).val();
});
var tgl = (togglethumb)?1:0;
cookieWrite('togglethumb', tgl, 24);
openDir(curdir, selectedfile);
}

function refreshList(){
var curdir = $('#address').val();
var selectedfile = new Array();
$('.fileid:checked').each(function(index){
	selectedfile[selectedfile.length] = $(this).val();
});
openDir(curdir, selectedfile);
}
var clipboardfile = {'operation':'', 'content':[]};

function transferFile()
{
	$('#common-dialog-inner').html('');
	$('#common-dialog').dialog({
		modal:true,
		title:'Transfer File',
		width:400,
		height:200,
		'buttons':{
			'Get File':function(){
				var sourcefile = $('#source').val();
				var targetlocation = $('#target').val();
				var targetname = $('#filename').val();
				$.post('tool-file-operation.php?option=transferfile', {'source':sourcefile, 'location':targetlocation, 'name':targetname}, function(answer){
					if(answer == 'READONLY')
					{
						jqAlert('The operation was disabled on read-only mode.', 'Read-Only');
					}
					else if(answer == 'FAILED')
					{
						jqAlert('Transfer file failed.', 'Failed');
					}
					else
					{
						try{$('#common-dialog').dialog('destroy');} catch(e){}
						openDir(targetlocation);
					}
				});
			},
			'Cancle':function(){
				try{$('#common-dialog').dialog('destroy');} catch(e){}
			}
		}
	});
	
	var html = '<form name="form1" method="post" action="">'+
	'<table width="100%" border="0" cellspacing="0" cellpadding="0" class="dialog-table">'+
	'<tr>'+
	'<td width="30%">URL</td>'+
	'<td><input type="text" name="source" class="input-text input-text-long" id="source" data-needed="true" autocomplete="off" /></td>'+
	'</tr>'+
	'<tr>'+
	'<td>Target Location</td>'+
	'<td><input type="text" name="target" class="input-text input-text-long" id="target" data-needed="true" autocomplete="off" /></td>'+
	'</tr>'+
	'<td>File Name</td>'+
	'<td><input type="text" name="filename" class="input-text input-text-long" id="filename" data-needed="true" autocomplete="off" /></td>'+
	'</tr>'+
	'</table>'+
	'</form>';	
	$('#common-dialog-inner').html(html);
	var targetlocation = $('#address').val();
	$('#target').val(targetlocation);
	$('#source').on('change', function(){
		var val = $(this).val();
		if(val.indexOf('data:') == 0)
		{
			var fn = dataToFileName(val);
		}
		else
		{
			var fn = filterFileName(basename($(this).val()));
		}
		$('#filename').val(fn)
	});
	$('#source').on('keyup', function(){
		var val = $(this).val();
		if(val.indexOf('data:') == 0)
		{
			var fn = dataToFileName(val);
		}
		else
		{
			var fn = filterFileName(basename($(this).val()));
		}
		$('#filename').val(fn)
	});
}
function dataToFileName(data)
{
	var arr0 = data.split(',');
	var arr1 = arr0[0].split(':');
	var arr2 = arr1[1].split(';');
	var arr3 = arr2[0].split('/').join('.');
	return arr3;
}

function filterFileName(name)
{
	return name.replace(/[^A-Za-z\.\-\d_\/]/g,'');
}

function copySelectedFile(){
this.operation = 'copy';
this.content = new Array();
var ff = this;
var chk = 0;
$('.fileid:checked').each(function(index){
	ff.content[ff.content.length] = $(this).val();
	chk++;
});
clipboardfile = ff;
if(chk == 0)
{
	jqAlert('No file selected.', 'Invalid Operation');
}
else
{
	$('#tb-clipboard').removeClass('tb-hide');
	$('#tb-clipboard-empty').removeClass('tb-hide');
}
updateToolbarStatus();
}

function cutSelectedFile(){
this.operation = 'cut';
this.content = new Array();
var ff = this;
var chk = 0;
$('.fileid:checked').each(function(index){
	ff.content[ff.content.length] = $(this).val();
	chk++;
});
clipboardfile = ff;
if(chk == 0)
{
	jqAlert('No file selected.', 'Invalid Operation');
}
else
{
	$('#tb-clipboard').removeClass('tb-hide');
	$('#tb-clipboard-empty').removeClass('tb-hide');
}
updateToolbarStatus();
}
function cutFile(filepath){
if(!filepath)
{
jqAlert('No file selected.', 'Invalid Operation');
}
else
{
this.operation = 'cut';
this.content = new Array();
var ff = this;
ff.content[ff.content.length] = filepath;
clipboardfile = ff;
$('#tb-clipboard').removeClass('tb-hide');
$('#tb-clipboard-empty').removeClass('tb-hide');
updateToolbarStatus();
}
}
function copyFile(filepath){
if(!filepath)
{
jqAlert('No file selected.', 'Invalid Operation');
}
else
{
this.operation = 'copy';
this.content = new Array();
var ff = this;
ff.content[ff.content.length] = filepath;
clipboardfile = ff;
$('#tb-clipboard').removeClass('tb-hide');
$('#tb-clipboard-empty').removeClass('tb-hide');
updateToolbarStatus();
}
}

function pasteFile(){
var i = 0;
var dl = $('#address').val();
var pd = dl;
if(clipboardfile.content.length)
{
var args = 'targetdir='+encodeURIComponent(dl);
for(i in clipboardfile.content){
	args+='&file[]='+encodeURIComponent(clipboardfile.content[i]);
}
var q = '?option=copyfile';
if(clipboardfile.operation=='cut'){
	q += '&deletesource=1';
}
$.post('tool-file-operation.php'+q, {'postdata':args}, function(answer){
	if(answer=='SUCCESS' || answer=='EXIST')
	{
	openDir(dl);
	if(clipboardfile.operation == 'cut'){
	emptyClipboard();
	}
	}
	else if(answer=='READONLY'){
		jqAlert('The operation was disabled on read-only mode.', 'Read-Only');
	}
});
}
else
{
jqAlert('The clipboard is empty.', 'Invalid Operation');
}
updateToolbarStatus();
}

function emptyClipboard(){
clipboardfile.content = new Array();
clipboardfile.operation = '';
$('#tb-clipboard').addClass('tb-hide');
$('#tb-clipboard-empty').addClass('tb-hide');
updateToolbarStatus();
}
function showClipboard(){
$('#common-dialog-inner').html('');
$('#common-dialog').dialog({
	modal:true,
	title:'Clipboard Content',
	width:400,
	height:230,
	buttons:
	{
	'Close':function(){
		$(this).dialog('destroy');
	}
	}
});	
html = '<div>File Operation: &quot;'+clipboardfile.operation+'&quot;; '+
'Number of File: '+clipboardfile.content.length+'</div>'+
'<div class="seleted-file-list">'+clipboardfile.content.join('<br />')+'</div>'+
'';
$('#common-dialog-inner').html(html);
}
function changePermission(filepath){
if(!filepath)
{
	var data = [];
	$('.fileid:checked').each(function(index, element) {
		data.push($(this).val());
	});
}
else
{
	var data = [filepath];
}
if(data.length==1)
{
	filepath = data[0];
}
initPermissionControl();
$('#common-dialog-inner').html('<table width="100%" border="0" cellspacing="1" cellpadding="2" class="row-table file-table permission-table">'
+'<thead>'
+'  <tr>'
+'    <td>User</td>'
+'    <td width="25%">Read</td>'
+'    <td width="25%">Write</td>'
+'    <td width="25%">Execute</td>'
+'  </tr>'
+'</thead>'
+'<tbody>'
+'  <tr>'
+'    <td>Owner</td>'
+'    <td><label><input type="checkbox" class="perm-checkbox" value="1" name="user_read" checked="checked"> Allowed</label></td>'
+'    <td><label><input type="checkbox" class="perm-checkbox" value="1" name="user_write"> Allowed</label></td>'
+'    <td><label><input type="checkbox" class="perm-checkbox" value="1" name="user_execute" checked="checked"> Allowed</label></td>'
+'  </tr>'
+'  <tr>'
+'    <td>Group</td>'
+'    <td><label><input type="checkbox" class="perm-checkbox" value="1" name="group_read" checked="checked"> Allowed</label></td>'
+'    <td><label><input type="checkbox" class="perm-checkbox" value="1" name="group_write"> Allowed</label></td>'
+'    <td><label><input type="checkbox" class="perm-checkbox" value="1" name="group_execute" checked="checked"> Allowed</label></td>'
+'  </tr>'
+'  <tr>'
+'    <td>World</td>'
+'    <td><label><input type="checkbox" class="perm-checkbox" value="1" name="world_read" checked="checked"> Allowed</label></td>'
+'    <td><label><input type="checkbox" class="perm-checkbox" value="1" name="world_write"> Allowed</label></td>'
+'    <td><label><input type="checkbox" class="perm-checkbox" value="1" name="world_execute" checked="checked"> Allowed</label></td>'
+'  </tr>'
+'  <tr>'
+'    <td>Permission</td>'
+'    <td colspan="3"><input type="text" id="file-permission" name="file-permission" class="input-text input-text-medium" value="0555" readonly="readonly"> <span id="recursive-control"><label><input type="checkbox" class="recursive" value="1" name="recursive"> Recursive to contents</label><span></td>'
+'    </tr>'
+'</tbody>'
+'</table>');
$('#common-dialog').dialog({
	modal:true,
	title:'Set Permission',
	width:400,
	height:250,
	buttons:
	{
	'Change':function(){
		if(data.length == 0)
		{
			jqAlert('No file selected.', 'Invalid Operation');
		}
		else
		{
		$('.ui-dialog-buttonpane').append('<div class="wait-status">Plase wait...</div>');
		$('.ui-dialog-buttonset button:first').attr('disabled', 'disabled');
		$('.ui-dialog-buttonset button:first').attr('aria-disabled', 'true');
		var rec = $('#recursive:checked').val();	
		var perms = $('#file-permission').val();
		$.post('tool-file-operation.php?option=change-perms', {'recursive':rec, 'perms':perms, 'data':data}, function(answer){
			if(answer=='SUCCESS'){
			try{$('#common-dialog').dialog('destroy');} catch(e){}
			openDir();
			}
			else if(answer=='READONLY'){
			jqAlert('The operation was disabled on read-only mode.', 'Read-Only');
			}
			$('.ui-dialog-buttonpane').find('.wait-status').remove();
			$('.ui-dialog-buttonset button:first').removeAttr('disabled');
			$('.ui-dialog-buttonset button:first').attr('aria-disabled', 'false');
		});
		}
	},
	'Close':function(){
		$(this).dialog('destroy');
	}
	}
});
if(!filepath)
{
	if($('.fileid[data-isdir=true]:checked').length == 0)
	{
		$('#recursive-control').remove();
	}
}
else
{
$.get('tool-file-operation.php', {'option':'get-perms', 'filepath':filepath}, function(answer){
var obj = eval(answer)[0];
var i;
for(i in obj)
{
	if(i.indexOf('_')!=-1)
	{
		if(obj[i]=='1')
		{
			$('input[name="'+i+'"]').attr('checked', 'checked');
		}
		else
		{
			$('input[name="'+i+'"]').removeAttr('checked');
		}
	}
	else if(i.indexOf('-')!=-1)
	{
		$('input[name="'+i+'"]').val(obj[i]);
	}
	if(obj['filetype']=='file')
	{
		$('#recursive-control').remove();
	}
}
});
}
}
function initPermissionControl()
{
	$(document).on('change', '.perm-checkbox', function(){
		var c11 = ($('.perm-checkbox[name=user_read]:checked').length)?1:0;
		var c12 = ($('.perm-checkbox[name=user_write]:checked').length)?1:0;
		var c13 = ($('.perm-checkbox[name=user_execute]:checked').length)?1:0;
		var c21 = ($('.perm-checkbox[name=group_read]:checked').length)?1:0;
		var c22 = ($('.perm-checkbox[name=group_write]:checked').length)?1:0;
		var c23 = ($('.perm-checkbox[name=group_execute]:checked').length)?1:0;
		var c31 = ($('.perm-checkbox[name=world_read]:checked').length)?1:0;
		var c32 = ($('.perm-checkbox[name=world_write]:checked').length)?1:0;
		var c33 = ($('.perm-checkbox[name=world_execute]:checked').length)?1:0;
		
		var u = c11*4+c12*2+c13;
		var g = c21*4+c22*2+c23;
		var w = c31*4+c32*2+c33;
		var p = '0'+u+''+g+''+w+'';
		$('#file-permission').val(p);
	});
}
function editImage(fp){
// calculate window size
var wwidth = $(window).width();
var wheight = $(window).height();
// create layer
var html = '<div id="image-editor-layer"></div>';
$('#all').append(html);
$('#image-editor-layer').css({'width':0+'px', 'height':0+'px'});
$.get('tool-image-editor-form.php', {'filepath':fp}, function(answer){
$('#image-editor-layer').html(answer);
var eh = wheight-73;
$('.image-editor-sidebar-inner, .image-editor-mainbar-inner').css('height', eh+'px');
var options = { to: { width: wwidth, height: wheight } };
$('#image-editor-layer').show('size',options, 500, callbackShowImageEditor );
initImageEditorForm();
});

}

function initImageEditorForm(){
$('.new-dimension input[type=checkbox]').each(function(index){
$(this).css({'border':'none', 'padding':'0'});
});
setSizeImageEditor();
filepath = $('#curfilepath').val();
fileurl = $('#curfileurl').val();
var curw = parseInt($('#curwidth').val());
var curh = parseInt($('#curheight').val());
imgwidth = curw;
imgheight = curh;
angle = 0;
fliph = 0;
flipv = 0;
$(document).on('change', '#newwidth', function(){
	if($('#aspectratio:checked').val())
	{
	var ratio = 1;
	if(angle%180==0)
	{
		ratio = curh/curw;
	}
	else
	{
		ratio = curw/curh;
	}
	var nw = parseInt($(this).val());
	var h2 = parseInt((ratio)*nw);
	$('#newheight').val(h2);
	}
	$('#image2edit').css({'width':$('#newwidth').val()+'px','height':$('#newheight').val()+'px'});
	imgwidth = $('#newwidth').val();
	imgheight = $('#newheight').val();
	previewImageEdit();
});
$(document).on('change', '#newheight', function(){
	if($('#aspectratio:checked').val())
	{
	var ratio = 1;
	if(angle%180==0)
	{
		ratio = curw/curh;
	}
	else
	{
		ratio = curh/curw;
	}
	var nh = parseInt($(this).val());
	var w2 = parseInt((ratio)*nh);
	$('#newwidth').val(w2);
	}
	$('#image2edit').css({'width':$('#newwidth').val()+'px','height':$('#newheight').val()+'px'});
	imgwidth = $('#newwidth').val();
	imgheight = $('#newheight').val();
	previewImageEdit();
});
$(document).on('click', '#cropimage', function(){
	crop = $(this).attr('checked')?1:0;
	previewImageEdit();
});

$(window).resize(function(){
	setSizeImageEditor();
});
}
function setSizeImageEditor(){
var wh = parseInt($(window).height());
var ww = parseInt($(window).width());
var eh = wh-73;
var ew = ww-200;
$('.image-editor-sidebar-inner, .image-editor-mainbar-inner').css('height', eh+'px');
$('#image-editor-layer').css('height', wh+'px');	
$('#curfilepath').css('width', ew+'px');
}
function callbackShowImageEditor(){
$('#wrapper').css('display', 'none');
$('#image-editor-layer').css('position', 'static');
$('#image-editor-layer').css('width', '100%');
}
function callbackDestroyImageEditor(){
$('#image-editor-layer').remove();
}

function destroyImageEditor(){
$('#wrapper').css('display', 'block');
$('#image-editor-layer').css('top', 0);
$('#image-editor-layer').css('left', 0);
$('#image-editor-layer').css('position', 'absolute');
var options = {to:{width:0, height:0}};
$('#image-editor-layer').hide('size', options, 500, function(){
$('#image-editor-layer').remove();
});
}
function previewImageEdit(){
var rnd = (Math.random()*1000);
var html = '<img src="tool-image-editor-thumbnail.php?filepath='+encodeURIComponent(filepath)+
'&flipv='+flipv+'&fliph='+fliph+'&angle='+angle+'&width='+imgwidth+'&height='+imgheight+'&crop='+crop+'&rand='+rnd+'" >';
$('#image-content').html(html);
}
function saveImage(){
if(confirm('Are you sure to save this state and replace current file?'))
{
filepath = $('#curfilepath').val();
var args = 'option=save2file&filepath='+encodeURIComponent(filepath)+
'&flipv='+flipv+'&fliph='+fliph+'&angle='+angle+'&width='+imgwidth+'&height='+imgheight+'&crop='+crop;
$.post('tool-image-editor-thumbnail.php', {'postdata':args}, function(answer){
if(answer=='SUCCESS')
{
$.get('tool-image-editor-form.php', {'filepath':filepath}, function(answer){
$('#image-editor-layer').html(answer);
initImageEditorForm();
});
}
else if(answer=='READONLY')
{
	jqAlert('The operation was disabled on read-only mode.', 'Read-Only');
}
});
}
}
function rotateCW(){
angle-=90;
while(angle<0) angle+=360;
angle = angle % 360;
imgwidth = $('#newwidth').val();
imgheight = $('#newheight').val();
var tmp = imgwidth;
imgwidth = imgheight;
imgheight = tmp
$('#newwidth').val(imgwidth);
$('#newheight').val(imgheight);
previewImageEdit();
}
function rotateCCW(){
angle+=90;
while(angle>360) angle-=360;
angle = angle % 360;
imgwidth = $('#newwidth').val();
imgheight = $('#newheight').val();
var tmp = imgwidth;
imgwidth = imgheight;
imgheight = tmp
$('#newwidth').val(imgwidth);
$('#newheight').val(imgheight);
previewImageEdit();
}
function flipV(){
flipv++;
flipv = flipv % 2;
previewImageEdit();
}

function flipH(){
fliph++;
fliph = fliph % 2;
previewImageEdit();
}
function resizeImage(){
resize++;
resize = resize % 2;
if(resize){
	$('.image-tool-resize-dimension').slideDown(400);		
}
else
{
	$('.image-tool-resize-dimension').slideUp(400);			
}
}
function about(){
var html = ''+
'<table width="100%" border="0" cellpadding="0" cellspacing="0" class="dialog-table dialog-about">'+
'<tr><td width="40%">Module Name</td><td>Planetbiru File Manager</td></tr>'+
'<tr><td>Version</td><td>2.2</td></tr>'+
'<tr><td>Developer</td><td><a href="http://www.kamshory.com" target="_blank">Kamshory Developer</a></td></tr>'+
'<tr><td>Release Date</td><td>2012-10-20</td></tr>'+
'<tr><td>Price</td><td>30 USD</td></tr>'+
'<tr><td>Sponsored Link</td><td><a href="http://www.planetbiru.com" target="_blank">Planetbiru</a></td></tr>'+
'<tr><td>Information</td><td><a href="javascript:;" onclick="return showInformation();">Click Here</a></td></tr>'+
'</table>';
overlayDialog(html, 320, 140);
$('.dialog-about a').css({'text-decoration':'none'});
fixOverlayDialogToInner('.dialog-about');
}

function preventSelect(url){
skipondrop = true;
}

function initDropable(){
$('.file-list .row-data-dir').draggable({drag: function() {
preventSelect($(this).attr('data-file-url'));
$(this).css({'z-index':400,'opacity':0.8});
}});
$('.file-list .row-data-file').draggable({drag: function() {
preventSelect($(this).attr('data-file-url'));
$(this).css({'z-index':400,'opacity':0.8});
}});
$('.file-list .row-data-dir').droppable(
{
activeClass:"directory-drop-active",
hoverClass:"directory-drop-hover",
drop: function(event, ui) 
{
var curlocation = ui.draggable.attr('data-file-location')+'/'+ui.draggable.attr('data-file-name');
var targetdir = $(this).attr('data-file-location')+'/'+$(this).attr('data-file-name');
var args = 'targetdir='+encodeURIComponent(targetdir);
args+='&file[]='+encodeURIComponent(curlocation);
var q = '?option=copyfile&deletesource=1';
$.post('tool-file-operation.php'+q, {'postdata':args}, function(answer){
	if(answer=='SUCCESS'||answer=='EXIST'){
		openDir();
	}
	else if(answer=='READONLY'){
		jqAlert('The operation was disabled on read-only mode.', 'Read-Only');
	}
});
skipondrop = true;
ui.draggable.hide('scale', {percent:0}, 300, function(){ui.draggable.css('display', 'none');openDir();});
return false;
}
});
}
function loadAnimationStart()
{
$('#anim-loader').addClass('anim-active');
}
function loadAnimationStop()
{
$('#anim-loader').removeClass('anim-active');
}
function openDirSearch(filepath)
{
	openDir(filepath);
	$('#common-dialog').dialog('destroy');
}
function selectFileSeach(filepath)
{
	selectFile(filepath);
	$('#common-dialog').dialog('destroy');
}
function openDirTree()
{
	var filepath = $('#address').val();
	var dirs = filepath.split('/');
	var i;
	var _parentdir = 'base';
	var _curdir = '';
	var _parenli;
	var _buff = [];
	for(i in dirs)
	{
		_buff.push(dirs[i]);
		_curdir = _buff.join('/');
		_parentli = $('a[onclick~="'+_curdir+'"]').parent();
		if(_curdir == 'base')
		{
			continue;
		}
		if(!$('#directory-container').find('a[onclick~="'+_curdir+'"]').length)
		{
			var _child = '<ul><li class="row-data-dir dir-control" data-file-path="'+_curdir+'" data-file-name="'+dirs[i]+'" data-file-location="'+_parentdir+'"><a href="javascript:;" onclick="return openDir(\''+_curdir+'\')">'+dirs[i]+'</a></li></ul>';
			$('#directory-container').find('[data-file-path="'+_parentdir+'"]').append(_child);
		}
		_parentdir = _curdir;
	}
	openDir();
	return false;
}
function openDir(filepath, selfile, sortby, sortorder)
{
if(!skipondrop)
{
loadAnimationStart();
var ret = true;
if(!filepath){
	filepath = $('#address').val();
	ret = false;
}
else{
	try{
	filepath = filepath.trim('/');
	}
	catch(e){
	}
}
$('#address').val(filepath);
var arg = {};
if(togglethumb){
	arg = {'dir':filepath, 'thumbnail':1};
}
else
{
	arg = {'dir':filepath};
}
if(sortby)
{
	arg['sortby'] = sortby;
}
if(sortorder)
{
	arg['sortorder'] = sortorder;
}

$.get('tool-load-file-json.php', arg, function(answer){
arrthumbnail = eval(answer);
arrthumbnailURL = new Array();
if(arrthumbnail.length)
{
	var xx = 0;
	for(xx in arrthumbnail)
	{
		arrthumbnailURL[xx] = arrthumbnail[xx]['url'];
	}
}
});
$.get('tool-load-file.php', arg, function(answer){
$('#file-container').html(answer);
try{
if(selfile.length){
	var fn = '';
	$('.fileid').each(function(index){
	fn = $(this).val();
	if($.inArray(fn, selfile) != -1){
		$(this).attr('checked', 'checked');
	}
	});
}
if(($('.fileid:checked').length == $('.fileid').length) && $('.fileid').length)
{
	try{$('#control-fileid').attr('checked', 'checked');} catch(e){}
}
else
{
	try{$('#control-fileid').removeAttr('checked');} catch(e){}
}

}
catch(e){}
initContextMenuFile();
initContextMenuDir();
setCheckRelation();
initDropable();
loadAnimationStop();
removeCheckboxBorder();
updateToolbarStatus();

$('#directory-container .basedir ul li').removeClass('dir-open').addClass('dir-normal');
$('#directory-container .basedir ul li[data-file-path="'+filepath+'"]').addClass('dir-open');

});	
var pth = '';
$.get('tool-load-dir.php', {'seldir':filepath}, function(answer){
	$('.dir-control').each(function(index){
	pth = $(this).attr('data-file-location')+'/'+$(this).attr('data-file-name');
	if(pth[pth.length]=='/') pth = pth.substr(0, pth.length-1);
	if(pth[0]=='/') pth = pth.substr(1);
	if(filepath==pth)
	{
		$(this).children('ul').remove();
		$(this).append(answer);
	}
	});
});
return ret;
}
skipondrop = false;
$('.row-data-dir').css({'left':'0px','top':'0px','z-index':0,'opacity':1});
$('.row-data-file').css({'left':'0px','top':'0px','z-index':0,'opacity':1});
}

function selectFile(url){
if(!skipondrop){
selectFileIndex(url);
}
skipondrop = false;
$('.row-data-dir').css({'left':'0px','top':'0px','z-index':0,'opacity':1});
$('.row-data-file').css({'left':'0px','top':'0px','z-index':0,'opacity':1});
}
function removeCheckboxBorder(){
// IE need this
$('#file-container input[type=checkbox]').each(function(index){
$(this).css({'border':'none', 'padding':'0'});
});
}
function initSortable(){
$(document).on('click', '.sort-holder', function(){
var curdir = $('#address').val();
var sortby = $(this).attr('data-sortby');
var sortorder = $(this).attr('data-sortorder');
var selectedfile = new Array();
$('.fileid:checked').each(function(index){
selectedfile[selectedfile.length] = $(this).val();
});
openDir(curdir, selectedfile, sortby, sortorder);
});
}

function showPermission(perms)
{
	perms += '';
	if(perms.length > 3)
	{
		perms = perms.substr(perms.length-3);
	}
	var p_user = parseInt(perms.substr(0,1));
	var p_group = parseInt(perms.substr(1,1));
	var p_world = parseInt(perms.substr(2,1));
	var ur = (p_user >> 2) % 2;
	var uw = (p_user >> 1) % 2;
	var ue = (p_user) % 2;
	var gr = (p_group >> 2) % 2;
	var gw = (p_group >> 1) % 2;
	var ge = (p_group) % 2;
	var wr = (p_world >> 2) % 2;
	var ww = (p_world >> 1) % 2;
	var we = (p_world) % 2;
	var ret = {
	'user_execute':ue, 'user_write':uw, 'user_read':ur,
	'group_execute':ge, 'group_write':gw, 'group_read':gr,
	'world_execute':we, 'world_write':ww, 'world_read':wr
	}
	return ret;
}
function initPermission(){
$(document).on('click', '.permission-info', function(){
var perms = $(this).text()+'';
try{$('#perms-dialog').dialog('destroy');} catch(e){}
var perms_obj = showPermission(perms);
var p_key;
for(p_key in perms_obj)
{
$('.'+p_key).text((parseInt(perms_obj[p_key])==1)?'Allowed':'Disallowed');
}
$('#perms-dialog').dialog({
resizable:false,
modal:true,
width:280,
height:165
});
$('.permission-table').fadeIn(200);
});
}
function showInformation()
{
$('#common-dialog-inner').html('');
$('#common-dialog').dialog({
	modal:true,
	title:'Information',
	width:400,
	height:310,
	buttons:
	{
	'Close':function(){
		$(this).dialog('destroy');
	}
	}
});	
$.get('tool-info.php', {}, function(answer){
	$('#common-dialog-inner').html(answer);
});
return false;
}
function searchFile()
{
var dir = $('#address').val();
var html = ''
+'<div class="search-container">'
+'<div class="search-inner">'
+'<div class="search-form">'
+'<form name="searchform">'
+'<div class="search-direactory">'
+'<div class="search-label">'
+'Location'
+'</div>'
+'<div class="search-input">'
+'<input type="text" name="sdir" id="sdir" class="input-search" readonly="readonly" />'
+'</div>'
+'</div>'
+'<div class="search-query">'
+'<div class="search-label">'
+'Filter'
+'</div>'
+'<div class="search-input">'
+'<input type="text" name="sfile" id="sfile" class="input-search" />'
+'</div>'
+'</div>'
+'</form>'
+'</div>'
+'<div class="search-result">'
+'<div class="loading-animation">Loading directory content...</div>'
+'</div>'
+'</div>'
+'</div>';
var width = $('body').width()-14;
var height = $('body').height()-14;
var hr = height - 136;

$('#common-dialog').dialog({
	modal:true,
	title:'Search File',
	width:width,
	height:height
});
$('#common-dialog-inner').html(html);
$('.search-result').css({'height':hr+'px', 'overflow':'auto'});
$(document).on('keyup', '#sfile', function(){
	filterFile($(this).val());
});
$(document).on('change', '#sfile', function(){
	filterFile($(this).val());		
});

$('#sdir').val(dir);
$('#sfile').select();
$.get('tool-search-file.php', {'dir':dir}, function(answer){
	$('.search-result').html(answer);
	normalizeTable();
});

}
function normalizeTable()
{
	$('.file-result-table tbody tr').each(function(index, element) {
		$(this).attr('data-file-name-lower', $(this).attr('data-file-name').toLowerCase());
	});
}
function filterFile(name)
{
	name = name.toLowerCase();
	$('.file-result-table').css({'display':''});
	if(name=="")
	{
		$('.file-result-table tbody tr').css({'display':''});
	}
	else
	{
		$('.file-result-table tbody tr').css({'display':'none'});
		$('.file-result-table tbody tr[data-file-name-lower*="'+name+'"]').css({'display':''});
	}
	if($('.file-result-table tbody tr:visible').length == 0)
	{
		$('.file-result-table').css({'display':'none'});
	}
}

function initEXIF(){
$(document).on('click', '.capture-info', function(){
try{$('#exif-dialog').dialog('destroy');} catch(e){}
$('#exif-dialog').remove();
$('#dialogs').append('<div id="exif-dialog"><div id="exif-dialog-inner"></div></div>');
var jsdata = eval(decodeURIComponent($(this).attr('data-exif')));
var key;
var obj = jsdata[0];
var html = '<table width="100%" border="0" cellspacing="1" cellpadding="2" class="row-table exif-table">';
html += '<tbody>';
for(key in obj)
{
html += '<tr><td width="50%">'+key+'</td><td>'+obj[key]+'</td></tr>';
}
html += '</tbody></table>';
$('#exif-dialog-inner').html(html);
$('#exif-dialog').dialog({
resizable:false,
modal:true,
title:'Capture Information',
width:400,
height:410,
buttons:{
'Close':function(){
$('#exif-dialog').dialog('destroy');
}
}
});
});
}

function relative2absolute(url)
{
	return vabs + url.substr(vrel.length);
}

function absolute2relative(url)
{
	return vrel + url.substr(vabs.length);
}
var ca = false;

var focusOnInput = false;
function toggleFocusInput()
{
	$(document).on('focus', ':input', function(){
		focusOnInput = true;
	});
	$(document).on('blur', ':input', function(){
		focusOnInput = false;
	});
}

function initHotKey()
{
toggleFocusInput();
$(document).on('keypress', function( e ) {
	if(e.ctrlKey && !focusOnInput)
	{
		if(e.which == 65 || e.which == 97) 
		{
			ca = !ca;
			selectAll(ca);
			e.preventDefault();
		}
		if(e.which == 67 || e.which == 99) 
		{
			copySelectedFile();
			e.preventDefault();
		}
		if(e.which == 86 || e.which == 118)
		{
			pasteFile();
			e.preventDefault();
		}
		if(e.which == 88 || e.which == 120) 
		{
			cutSelectedFile();
			e.preventDefault();
		}
		if(e.which == 70 || e.which == 102) 
		{
			searchFile();
			e.preventDefault();
		}
		if(e.which == 68 || e.which == 100) 
		{
			thumbnail();
			e.preventDefault();
		}
	}
});
}


function FileDragHover(e)
{
	e.stopPropagation();
	e.preventDefault();
	if(e.type == "dragover") $('.file-area').addClass('file-area-hover');
	else $('.file-area').removeClass('file-area-hover');
}

function FileSelectHandler(e)
{
	FileDragHover(e);
	var files = e.target.files || e.dataTransfer.files;
	var formData = new FormData();
	var html = '<div class="file-upload-caption">Uploading...</div>';
	html += '<div class="progressbar" style="margin:5px 0px;height:10px; background-color:#EEEEEE;"><div class="progressbar-inner" style="height:10px;background-color:rgb(23, 96, 125);width:0%;"></div></div>';
	html += '<div class="file-upload" style="height:180px;">';
	for (var i = 0; i < files.length; i++)
	{
		formData.append('images[]', files[i]);
		html += '<div class="file-upload-item">'+files[i].name+'</div>';
	}
	html += '</div>';
	overlayDialog(html, 360, 224);
	xhr.open('POST', 'tool-upload-file-multi.php?targetdir='+encodeURIComponent($('#address').val()));
	xhr.onload = function ()
	{
		if(xhr.status === 200)
		{
			refreshList();
			closeOverlayDialog();
		} 
	};
	xhr.upload.addEventListener("progress", progressHandler, false);
	xhr.addEventListener("load", completeHandler, false);
	xhr.addEventListener("error", errorHandler, false);
	xhr.addEventListener("abort", abortHandler, false);
	xhr.send(formData);
}

function progressHandler(event){
	var percent = (event.loaded / event.total) * 100;
	$(".progressbar-inner").css({'width':percent+'%'})
}
function completeHandler(event){
	var response = event.target.responseText;
	var data = $.parseJSON(response);
	$(".progressbar-inner").css({'width':'0%'});
	if(data.success)
	{
		closeOverlayDialog();
	}
	else
	{
	}
}
function errorHandler(event){
	jqAlert('Uploading error.', 'Error');
}
function abortHandler(event){
	jqAlert('Process aborted.', 'Aborted');
}

function initDragDropUpload()
{
	if (window.File && window.FileList && window.FileReader)
	{
		var filedrag = $(".file-area")[0];
		if (xhr.upload)
		{
			filedrag.addEventListener("dragover", FileDragHover, false);
			filedrag.addEventListener("dragleave", FileDragHover, false);
			filedrag.addEventListener("drop", FileSelectHandler, false);
			filedrag.style.display = "block";
		}
	}
}

function cookieRead(name){var cookieValue="";var search=name+"=";if(document.cookie.length>0){offset=document.cookie.indexOf(search);if(offset!=-1){offset+=search.length;end=document.cookie.indexOf(";",offset);if(end==-1)end=document.cookie.length;cookieValue=unescape(document.cookie.substring(offset,end))}}return cookieValue;}
function cookieWrite(name,value,hours){var expire="";if(hours!=null){expire=new Date((new Date()).getTime()+hours*3600000);expire=";expires="+expire.toGMTString();}document.cookie=name+"="+escape(value)+expire;}