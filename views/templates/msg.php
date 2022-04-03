<html>
<head>
    <title><?php echo $title; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0,user-scalable=no,maximum-scale=1.0">
    <link href="<?php echo site_url('resource/css/bootstrap.min.css'); ?>" rel="stylesheet">
    <script src="<?php echo site_url('resource/js/jquery.min.js'); ?>"></script>
    <script src="<?php echo site_url('resource/js/bootstrap.min.js'); ?>"></script>
    <style>
        .container{
            margin-top:70px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h2 class="panel-title">
                <?php echo $title; ?>
            </h2>
        </div>
        <div class="panel-body">
            <?php echo $msg; ?>
        </div>
    </div>
</div>