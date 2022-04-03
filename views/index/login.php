<html>
<head>
    <title>行知国际学校学生管理系统</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0,user-scalable=no,maximum-scale=1.0">
    <link href="<?php echo site_url('resource/css/bootstrap.min.css'); ?>" rel="stylesheet">
    <script src="<?php echo site_url('resource/js/jquery.min.js'); ?>"></script>
    <script src="<?php echo site_url('resource/js/bootstrap.min.js'); ?>"></script>
    <link href="<?php echo site_url('resource/css/font-awesome.min.css'); ?>" rel="stylesheet">
</head>
<body>

<br><BR>
<div class="container">
    <div class="jumbotron">

        <?php echo validation_errors(); ?>

        <?php echo form_open('index/login/', array('class' => 'form-horizontal', 'role' => 'form')); ?>
        <div class="form-group">
            <label for="name" class="col-sm-2 control-label">昵称</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="name" name="name" placeholder="请输入昵称">
            </div>
        </div>
        <div class="form-group">
            <label for="password" class="col-sm-2 control-label">密码</label>
            <div class="col-sm-10">
                <input type="password" class="form-control" id="password" name="password" placeholder="请输入密码">
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" name="submit" class="btn btn-success">登录</button>
            </div>
        </div>
        <div class="form-group hide">
            <div class="col-sm-offset-2 col-sm-10">
                <a href="<?php echo site_url('index/wx_login'); ?>"><i class="fa fa-wechat"></i> 微信扫码登陆</a>
            </div>
        </div>
        </form>
    </div>

</div>

<Style>
    .nologin *{
        font-size:16px;
    }
    .nologin a{
        padding:10px;
    }
</Style>
