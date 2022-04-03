<div class="container">
    <h2><?php echo $teacher_item['name']; ?>-修改密码</h2>


    <?php echo validation_errors(); ?>

    <?php
    if ($result=='success'){
        ?>
        <div class="alert alert-success">
            <a href="#" class="close" data-dismiss="alert">
                &times;
            </a>
            <strong>密码修改成功！</strong>
        </div>
        <?php
    }
    ?>
    <?php
    if ($result=='fail'){
        ?>
        <div class="alert alert-warning">
            <a href="#" class="close" data-dismiss="alert">
                &times;
            </a>
            <strong>密码修改失败！</strong>请确认两次输入的密码相同且不为空。
        </div>
        <?php
    }
    ?>

    <?php echo form_open('teacher_pwd/', array('class' => 'form-horizontal', 'role' => 'form')); ?>
    <input type="hidden" name="teacher_id" value="<?php echo $teacher_item['id'] ?>">
    <div class="form-group">
        <label for="pwd" class="col-sm-2 control-label">新密码</label>
        <div class="col-sm-10">
            <input type="password" class="form-control" name="pwd" id="pwd">
        </div>
    </div>
    <div class="form-group">
        <label for="pwd_repeat" class="col-sm-2 control-label">重复新密码</label>
        <div class="col-sm-10">
            <input type="password" class="form-control" name="pwd_repeat" id="pwd_repeat">
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" name="submit" value="submit" class="btn btn-default">修改</button>
        </div>
    </div>
    </form>
</div>