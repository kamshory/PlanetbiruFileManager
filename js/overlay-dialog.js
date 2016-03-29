function overlayDialog(content, width, height){
var objall=$(window);
var allwidth=parseInt(objall.width());
var allheight=parseInt(objall.height());
var winner=parseInt(width);
var hinner=parseInt(height);
var wouter=winner+22;
var houter=hinner+22;
var louter=parseInt((allwidth-wouter)/2);
var linner=louter-0;
var touter=parseInt((allheight-houter)/2);
var tinner=touter-0;

$('#overlay-container').remove();
$('body').append('<div id="overlay-container" style="display:none"></div>');

var html='<div class="ui-overlay"><div class="ui-widget-overlay"></div><div id="overlay-outer" class="ui-widget-shadow ui-corner-all" style="position:absolute;z-index:10;"></div></div><div id="overlay-inner" style="position:absolute;padding:10px;z-index:20;" class="ui-widget ui-widget-content ui-corner-all"><div class="ui-dialog-content ui-widget-content" style="background:none;border:0;"><div id="overlay-container-inner">'+content+'</div></div></div>';
$('#overlay-container').html(html);
$('#overlay-outer').css('width',wouter+'px');
$('#overlay-outer').css('height',houter+'px');
$('#overlay-outer').css('left',louter+'px');
$('#overlay-outer').css('top',touter+'px');
$('#overlay-inner').css('width',winner);
$('#overlay-inner').css('height',hinner);
$('#overlay-inner').css('left',linner+'px');
$('#overlay-inner').css('top',tinner+'px');

objall=$(document);
allwidth=parseInt(objall.width());
allheight=parseInt(objall.height());

$('#overlay-container').css('position','fixed');
$('#overlay-container').css('left','0px');
$('#overlay-container').css('top','0px');
$('#overlay-container').css('width',allwidth+'px');
$('#overlay-container').css('height',allheight+'px');

$('#overlay-container').css('display','block');
//$('#overlay-container').fadeIn(200);

$('.ui-widget-overlay').click(function(){closeOverlayDialog();});
$(document).keydown(function(event){if(event.keyCode=='13'||(event.keyCode&&event.keyCode===$.ui.keyCode.ESCAPE)){closeOverlayDialog();}});
}
function fixOverlayDialogToInner(objref){
var objall=$(window);
var allwidth=parseInt(objall.width());
var allheight=parseInt(objall.height());

var winner=parseInt($(objref).width());
var hinner=parseInt($(objref).height());
var wouter=winner+22;
var houter=hinner+22;
var louter=parseInt((allwidth-wouter)/2);
var linner=louter-0;
var touter=parseInt((allheight-houter)/2);
var tinner=touter-0;
	
$('#overlay-outer').css('width',wouter+'px');
$('#overlay-outer').css('height',houter+'px');
$('#overlay-outer').css('left',louter+'px');
$('#overlay-outer').css('top',touter+'px');
$('#overlay-inner').css('width',winner);
$('#overlay-inner').css('height',hinner);
$('#overlay-inner').css('left',linner+'px');
$('#overlay-inner').css('top',tinner+'px');
}
function closeOverlayDialog(){
	$('#overlay-container').fadeOut(200, function(){
	$('#overlay-container').html('');
	$('#overlay-container').css('display','none');
	});
}