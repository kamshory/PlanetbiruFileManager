(function(){
var input=document.getElementById("images"),
formdata=false;
function showUploadedItem(source){
var list=document.getElementById("image-list"),
li=document.createElement("li"),
img=document.createElement("img");
img.src=source;
li.appendChild(img);
list.appendChild(li);
}   
if(window.FormData){
formdata=new FormData();
}
else{
$('.upload-button').css('display','inline');
}
input.addEventListener('change',function(evt){
if(window.FormData){
$('#response').html('Uploading . . .');
}
var i=0,len=this.files.length,img,reader,file;
for(;i<len;i++){
file=this.files[i];
if(!!file.type.match(/image.*/)){
if(window.FileReader){
reader=new FileReader();
reader.onloadend=function(e){
showUploadedItem(e.target.result,file.fileName);
};
reader.readAsDataURL(file);
}
if(formdata){
formdata.append("images[]",file);
}
}
else{
formdata.append("images[]",file);
}
}
if(formdata){
var dl=$('#address').val();
$.ajax({
url:'tool-upload-file.php?targetdir='+encodeURIComponent(dl),
type:'POST',
data:formdata,
processData:false,
contentType:false,
success:function(answer){
if(answer=='SUCCESS'){
$('#response').html('File has been uploaded.'); 
openDir();
}
else if(answer=='READONLY'){
openDir();
$('#response').html('&nbsp;');
jqAlert('This operation is disabled on read only mode.', 'Readonly');
formdata=new FormData();
}
else if(answer=='DENIED'){
openDir();
$('#response').html('&nbsp;');
jqAlert('Uploading file is forbidden.', 'Forbidden');
formdata=new FormData();
}
else if(answer=='FORBIDDEN'){
openDir();
$('#response').html('&nbsp;');
jqAlert('Uploading file is forbidden.', 'Forbidden');
formdata=new FormData();
}
}
});
}
},false);
}());