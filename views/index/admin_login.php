<html>
<head>
    <title>行知国际学校学生管理系统</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0,user-scalable=no,maximum-scale=1.0">
    <link href="<?php echo site_url('resource/css/bootstrap.min.css'); ?>" rel="stylesheet">
    <script src="<?php echo site_url('resource/js/jquery.min.js'); ?>"></script>
    <script src="<?php echo site_url('resource/js/bootstrap.min.js'); ?>"></script>
</head>
<body>

<br><BR>
<div class="container">
    <div class="jumbotron">

        <?php echo validation_errors(); ?>

        <?php echo form_open('index/admin_login/', array('class' => 'form-horizontal', 'role' => 'form')); ?>
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
        </form>

    </div>
</div>

