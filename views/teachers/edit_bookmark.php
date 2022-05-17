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
<div class="container" style="padding-top:20px;">
    <?php
    echo $_SESSION['err_msg'];
    unset($_SESSION['err_msg']);


    function get_select_folder($sub_item, $select_folder, $level = 1, $folder_id = 0)
    {
        $spaces = '';
        for ($i = 0; $i < $level; $i++) {
            $spaces = $spaces . "&nbsp;&nbsp;&nbsp;&nbsp;";
        }
        $out = '<option value="' . $sub_item['id'] . '" lv="' . $level . '">' . $spaces . $sub_item['folder_name'] . '</option>';
        if ($sub_item['subfolder'] != null) {
            foreach ($sub_item['subfolder'] as $item) {
                $out = $out . get_select_folder($item, $select_folder, $level + 1);
            }
        }
        return $out;
    }

    if ($cookie_level != 'work') {
        $select_folder = '<option value="-1" lv="-1">根目录</option>';
    } else {
        $select_folder = '';
    }
    foreach ($folder as $item):
        $select_folder = $select_folder . '<option value="' . $item['id'] . '" lv="0">' . $item['folder_name'] . '</option>';
        if ($item['subfolder'] != null) {
            foreach ($item['subfolder'] as $sub_item):
                $select_folder = $select_folder . get_select_folder($sub_item, $select_folder);
            endforeach;
        }
    endforeach;


    ?>
    <?php echo form_open('user/edit_bookmark/' . $bookmark['id'] . '/' . $bookmark['safe_code'], array('class' => 'form-horizontal', 'role' => 'form')); ?>
    <div class="form-group">
        <label for="url" class="col-xs-2 control-label">网址</label>
        <div class="col-xs-10">
            <input type="text" class="form-control" name="url" id="url" value="<?php echo $bookmark['url']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label for="title" class="col-xs-2 control-label">标题</label>
        <div class="col-xs-10">
            <input type="text" class="form-control" name="title" id="title" value="<?php echo $bookmark['title']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label for="title" class="col-xs-2 control-label">tag</label>
        <div class="col-xs-10">
            <input type="text" class="form-control" name="tag" id="tag" value="<?php echo $bookmark['tag']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label for="title" class="col-xs-2 control-label">目录</label>
        <div class="col-xs-10">
            <select class="form-control" name="folder_id" id="folder_id" self_id="<?php echo $bookmark['folder_id'];?>">
                <?php
                echo $select_folder;
                ?>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label for="title" class="col-xs-2 control-label">私有</label>
        <div class="col-xs-10">
            <input type="checkbox" name="is_private" class="checkbox" value="1" <?php
            if ($bookmark['is_private'] == 1) {
                echo " checked";
            }
            ?>>
        </div>
    </div>
    <div class="form-group">
        <div class="col-xs-offset-2 col-xs-10">
            <button type="submit" name="submit" value="update" class="btn btn-success">修改</button>
            <a type="button" class="btn btn-success"
               href="<?php echo site_url('user/delete_bookmark/' . $bookmark['id'] . '/' . $bookmark['safe_code']); ?>"
               onclick="javascript:return del();" target="_top">删除</a>
        </div>
    </div>
    </form>
</div>
<style>
    .my_checkbox {
        margin-left: 10px !important;
        width: 15px;
        height: 15px;
    }

    * {
        word-wrap: break-word;
        word-break: break-all;
    }

    .alert {
        width: 100%;
        z-index: 10;
    }

    .swiper-container {
        width: 100%;
        height: 100%;
    }

    .swiper-slide {

        /* Center slide text vertically */
        display: -webkit-box;
        display: -ms-flexbox;
        display: -webkit-flex;
        display: flex;
        -webkit-box-pack: center;
        -ms-flex-pack: center;
        -webkit-justify-content: center;
        justify-content: center;
        -webkit-box-align: center;
        -ms-flex-align: center;
        -webkit-align-items: center;
        align-items: center;
    }

    .swiper-pagination-bullet {
        width: 20px;
        height: 20px;
        text-align: center;
        line-height: 20px;
        font-size: 12px;
        color: #000;
        opacity: 1;
        background: rgba(0, 0, 0, 0.2);
    }

    .swiper-pagination-bullet-active {
        color: #fff;
        background: #007aff;
    }

    .footer {
        text-align: center;
    }
</style>
<script>
    function del() {
        if (window.confirm('确定这个操作吗？')) {
            return true;
        } else {
            return false;
        }
    }

    $(document).ready(function () {
        var onResize = function () {
            // apply dynamic padding at the top of the body according to the fixed navbar height
            //$("body").css("padding-left", $(".side-navbar").width());
            $("body").css("padding-bottom", $(".navbar-fixed-bottom").height());
        };

        // attach the function to the window resize event
        $(window).resize(onResize);
        onResize();
        $("#folder_id").val($("#folder_id").attr("self_id"));
    })
</script>
</body>
</html>