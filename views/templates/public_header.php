<!DOCTYPE html>
<html>
<head>
    <title><?php echo $title; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0,user-scalable=no,maximum-scale=1.0">


    <link href="<?php echo site_url('resource/css/bootstrap.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo site_url('resource/css/jquery-ui.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo site_url('resource/css/colors.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo site_url('resource/css/mine.css'); ?>" rel="stylesheet">
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
<?php
echo $_SESSION['err_msg'];
unset($_SESSION['err_msg']);
?>