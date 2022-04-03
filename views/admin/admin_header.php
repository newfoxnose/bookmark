<!DOCTYPE html>
<html>
<head>
    <title>管理员</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0,user-scalable=no,maximum-scale=1.0">


    <link href="<?php echo site_url('resource/css/bootstrap.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo site_url('resource/css/jquery-ui.min.css'); ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo site_url('resource/css/colors.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo site_url('resource/css/mine.css'); ?>">
    <link href="<?php echo site_url('resource/css/jquery.raty.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo site_url('resource/css/font-awesome.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo site_url('resource/css/swiper.min.css'); ?>" rel="stylesheet">


    <script src="<?php echo site_url('resource/js/jquery.min.js'); ?>"></script>
    <script src="<?php echo site_url('resource/js/jquery-ui.min.js'); ?>"></script>
    <script src="<?php echo site_url('resource/layer/layer.js'); ?>"></script>
    <script src="<?php echo site_url('resource/js/bootstrap.min.js'); ?>"></script>
    <script src="<?php echo site_url('resource/js/moment.min.js'); ?>"></script>

    <script src="<?php echo site_url('resource/js/jquery.raty.min.js'); ?>"></script>
    <script src="<?php echo site_url('resource/js/swiper.min.js'); ?>"></script>


    <style>
        @font-face {
            font-family: 'Glyphicons Halflings';
            src: url("<?php echo site_url('resource/fonts/glyphicons-halflings-regular.eot');?>");
            src: url("<?php echo site_url('resource/fonts/glyphicons-halflings-regular.eot?#iefix');?>") format('embedded-opentype'),
            url("<?php echo site_url('resource/fonts/glyphicons-halflings-regular.woff');?>") format('woff'),
            url("<?php echo site_url('resource/fonts/glyphicons-halflings-regular.ttf');?>") format('truetype'),
            url("<?php echo site_url('resource/fonts/glyphicons-halflings-regular.svg?#glyphicons-halflingsregular');?>") format('svg');

    </style>
</head>
<body>

<nav class="navbar navbar-default navbar-fixed-bottom" role="navigation">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse"
                    data-target="#example-navbar-collapse">
                <span class="sr-only">切换导航</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="<?php echo site_url('index'); ?>">首页</a>
        </div>
        <div class="collapse navbar-collapse" id="example-navbar-collapse">
            <ul class="nav navbar-nav">
                <li><a href="<?php echo site_url('admin/list_users'); ?>">用户管理</a></li>
                <li><a href="<?php echo site_url('admin/admin_edit_table/0'); ?>">通用表管理</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="<?php echo site_url('index/admin_logout'); ?>"><span
                                class="glyphicon glyphicon-log-in"></span> 退出</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<?php
echo $_SESSION['err_msg'];
unset($_SESSION['err_msg']);
?>