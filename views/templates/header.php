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
    <link href="<?php echo site_url('resource/css/font-awesome.min.css?rnd=3'); ?>" rel="stylesheet">
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
                <span class="sr-only">????????????</span>
                ??????
            </button>
            <a class="navbar-brand" href="<?php echo site_url('user/home/'); ?>">
                ????????????
            </a>
        </div>
        <div class="collapse navbar-collapse" id="example-navbar-collapse">
            <ul class="nav navbar-nav">
                <li><a href="<?php echo site_url('user/rt_note/'); ?>">?????????</a></li>
                <li><a href="<?php echo site_url('user/calendar/'); ?>">?????????</a></li>
                <li class="hide"><a href="<?php echo site_url('user/list_documents/-/-/-'); ?>">????????????</a></li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        ??????
                        <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="<?php echo site_url('user/manage_folder/'); ?>">?????????</a></li>
                        <li><a href="<?php echo site_url('user/import/'); ?>">????????????</a></li>
                        <li><a href="<?php echo site_url('user/export/'); ?>">????????????</a></li>
                        <li><a href="<?php echo site_url('user/self_edit_teacher'); ?>">????????????</a></li>
                        <li><a href="<?php echo site_url('user/pwd'); ?>">????????????</a></li>
                    </ul>
                </li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="<?php echo site_url('index/logout'); ?>"><span
                                class="glyphicon glyphicon-log-in"></span>&nbsp;&nbsp;??????</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<?php
if ($todos != null) {
    ?>
    <div class="alert alert-info">
        ???????????????
        <?php
        $i = 0;
        foreach ($todos as $todos_item):
            $i++;
            echo $i;
            ?>
            ???
            <a href="<?php echo site_url('user/exec_todo/' . $todos_item['id']); ?>"><?php echo $todos_item['title']; ?></a>???
        <?php endforeach; ?>
    </div>
    <?php
}
?>
<?php
echo $_SESSION['err_msg'];
unset($_SESSION['err_msg']);
?>