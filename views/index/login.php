<br><BR>
<div class="container">
    <h1><?php echo $title?></h1>
    <div class="jumbotron">

        <?php echo validation_errors(); ?>

        <?php echo form_open('index/login/', array('class' => 'form-horizontal', 'role' => 'form')); ?>

        <div class="form-group">
            <label for="email" class="col-sm-2 control-label">邮箱</label>
            <div class="col-sm-10">
                <input type="email" class="form-control" id="email" name="email" placeholder="请输入注册邮箱">
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
