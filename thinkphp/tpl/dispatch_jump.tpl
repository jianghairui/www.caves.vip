{__NOLAYOUT__}
<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <meta name="renderer" content="webkit|ie-comp|ie-stand">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <link rel="Bookmark" href="/favicon.ico" >
    <link rel="Shortcut Icon" href="/favicon.ico" />
    <link rel="stylesheet" type="text/css" href="/static/h-ui/css/H-ui.min.css" />
    <link rel="stylesheet" type="text/css" href="/static/h-ui.admin/css/H-ui.admin.css" />
    <link rel="stylesheet" type="text/css" href="/lib/Hui-iconfont/1.0.8/iconfont.css" />
    <link rel="stylesheet" type="text/css" href="/static/h-ui.admin/skin/default/skin.css" id="skin" />
    <link rel="stylesheet" type="text/css" href="/static/h-ui.admin/css/style.css" />
    <title></title>
</head>
<body>
<section class="container-fluid page-404 minWP text-c">
    <?php switch ($code) {?>
    <?php case 1:?>

    <p class="error-title">
    <i class="Hui-iconfont va-m" style="font-size:80px;color:#555555">&#xe68e;</i>
    </p>
    <p class="error-description" style="color:#555555"><?php echo(strip_tags($msg));?></p>

    <?php break;?>


    <?php case 0:?>

    <p class="error-title">
    <i class="Hui-iconfont va-m" style="font-size:80px;color:#555555">&#xe688;</i>
    </p>
    <p class="error-description" style="color:#555555"><?php echo(strip_tags($msg));?></p>

    <?php break;?>
    <?php } ?>

    <p class="detail"></p>
    <p class="jump" style="color:#555555">
    您可以：
    <a href="javascript:;" onclick="history.go(-1)" class="c-primary">&lt; 返回上一页</a>
    <span class="ml-20">|</span>
        &nbsp;&nbsp;&nbsp;&nbsp;页面自动 <a id="href" href="<?php echo($url);?>" class="c-primary">跳转</a> 等待时间： <b id="wait"><?php echo($wait);?></b>
    </p>

</section>
<script type="text/javascript">
    (function(){
        var wait = document.getElementById('wait'),
            href = document.getElementById('href').href;
        var interval = setInterval(function(){
            var time = --wait.innerHTML;
            if(time <= 0) {
                location.href = href;
                clearInterval(interval);
            };
        }, 1000);
    })();
</script>
</body>
</html>