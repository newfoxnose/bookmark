<style>
    th {
        text-align: center;
    }
</style>
<div class="container">
    <h2><?php echo $title; ?></h2>


    <?php echo validation_errors(); ?>
    <?php echo form_open('user/self_edit_teacher', array('class' => 'form-horizontal', 'role' => 'form')); ?>
    <input type="hidden" name="teacher_id" value="<?php echo $teacher['id'] ?>">
    <div class="form-group">
        <label for="name" class="col-sm-2 control-label">昵称</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" name="name" id="name" value="<?php echo $teacher['name'] ?>">
        </div>
    </div>
    <div class="form-group">
        <label for="email" class="col-sm-2 control-label">邮箱</label>
        <div class="col-sm-10">
            <input type="email" class="form-control" name="email" id="email" value="<?php echo $teacher['email'] ?>">
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" name="submit" value="submit" class="btn btn-default">修改</button>
        </div>
    </div>
    </form>

</div>
<style>
    .my_checkbox {
        width: 20px;
        height: 20px;
        position: relative;
        left: -20px;
        top: -50px;
        z-index: 10;
    }
</style>
