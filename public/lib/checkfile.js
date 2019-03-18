function checkfile(fileId,maxsize=512){

    var maxsize = maxsize*1024;//512K
    var errMsg = "上传的附件文件不能超过"+maxsize/1024+"K！！！";
    var tipMsg = "您的浏览器暂不支持计算上传文件的大小，确保上传文件不要超过"+maxsize+"K，建议使用IE、FireFox、Chrome浏览器。";
    var browserCfg = {};
    var ua = window.navigator.userAgent;

    if (ua.indexOf("MSIE")>=1){
        browserCfg.ie = true;
    }else if(ua.indexOf("Firefox")>=1){
        browserCfg.firefox = true;
    }else if(ua.indexOf("Chrome")>=1){
        browserCfg.chrome = true;
    }

    if(typeof FileReader  != 'undefined'){
        var file = document.getElementById(fileId).files[0];
        if((file.type).indexOf("image/")==-1){
            alert('请上传图片（格式BMP、JPG、JPEG、PNG、GIF等）!!!!');
            return false;
        }
    }else{
        var fileName=document.getElementById(fileId).value;
        var suffixIndex=fileName.lastIndexOf(".");
        var suffix=fileName.substring(suffixIndex+1).toUpperCase();
        if(suffix!="BMP"&&suffix!="JPG"&&suffix!="JPEG"&&suffix!="PNG"&&suffix!="GIF"){
            alert("请上传图片（格式BMP、JPG、JPEG、PNG、GIF等）!");
            return false;
        }
    }

    try{
        var obj_file = document.getElementById(fileId);
        if(obj_file.value==""){
            alert("请先选择上传文件");
            return false;
        }
        var filesize = 0;
        if(browserCfg.firefox || browserCfg.chrome ){
            filesize = obj_file.files[0].size;
        }else{
            alert(tipMsg);
            return false;
        }

        if(filesize==-1){
            alert(tipMsg);
            return false;
        }else if(filesize>maxsize){
            alert(errMsg);
            return false;
        }else{
            return true;
        }
    }catch(e){
        alert(e);
        return false;
    }
    return false;
}