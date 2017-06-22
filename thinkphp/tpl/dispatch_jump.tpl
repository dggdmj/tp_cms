{__NOLAYOUT__}<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
    <!--<![endif]-->
    <!-- BEGIN HEAD -->
    <head>
    <meta charset="utf-8" />
    <title>跳转提示</title>
    <meta name="renderer" content="webkit|ie-comp|ie-stand" />
    <meta http-equiv="X-UA-Compatible" content="IE=12,chrome=1">
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <meta name="author" content="YS">
    <style type="text/css">
    .note.note-info {
        background-color: #f5f8fd;
        border-color: #8bb4e7;
        color: #010407;
    }
    .note {
        margin: 20px;
        padding: 15px 30px 15px 15px;
        border-left: 5px solid #eee;
        border-radius: 0 4px 4px 0;
    }
    .note, .tabs-right.nav-tabs>li>a:focus, .tabs-right.nav-tabs>li>a:hover {
        -webkit-border-radius: 0 4px 4px 0;
        -moz-border-radius: 0 4px 4px 0;
        -ms-border-radius: 0 4px 4px 0;
        -o-border-radius: 0 4px 4px 0;
    }
    body {
        color: #34495e;
        padding: 0!important;
        margin: 0!important;
        direction: "ltr";
        background-color:#F7FFF8;
    }
    body, h1, h2, h3, h4, h5, h6 {
        font-family: "Open Sans",sans-serif;
    }
    body {
        font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
        line-height: 1.42857;
        color: #34495e;
    }
    Inherited from html
    html {
        -webkit-tap-highlight-color: transparent;
    }
    html {
        font-family: sans-serif;
        -ms-text-size-adjust: 100%;
        -webkit-text-size-adjust: 100%;
    }
    Pseudo ::before element
    *, :after, :before {
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
    }
    Pseudo ::after element
    *, :after, :before {
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
    }
    .container {
        height:151px;
    margin: auto;
    position: absolute;
    top:-150px;bottom:0;left:0;right:0;
  }
  .container:before,
  .container:after {
    content: " ";
    display: table; }
  .container:after {
    clear: both; }
  @media (min-width: 768px) {
    .container {
      width: 750px; } }
      .note.note-info{-webkit-box-shadow: 0 8px 8px rgba(0,0,0,0.3); -moz-box-shadow: 0 8px 8px rgba(0,0,0,0.3); -ms-box-shadow: 0 8px 8px rgba(0,0,0,0.3); -o-box-shadow: 0 8px 8px rgba(0,0,0,0.3); box-shadow: 0 8px 8px rgba(0,0,0,0.3);-webkit-border-radius:6px;-moz-border-radius:6px;-ms-border-radius:6px;-o-border-radius:6px;border-radius:6px; border-top: #dfdfdf solid 1px;border-right: #dfdfdf solid 1px;}
    </style>
</head>
<body>
<div class="container">
    <div class="note note-info">
        <?php switch ($code) {?>
            <?php case 1:?>
            <h2 class="block"><p class="success"><?php echo(strip_tags($msg));?></p></h2>
            <?php break;?>
            <?php case 0:?>
            <h2 class="block"><p class="error"><?php echo(strip_tags($msg));?></p></h2>
            <?php break;?>
        <?php } ?>
        <p class="detail"></p>
        <p class="jump">
            页面自动 <a id="href" href="<?php echo($url);?>">跳转</a> 等待时间： <b id="wait"><?php echo($wait);?></b>
        </p>
    </div>
</div>
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
