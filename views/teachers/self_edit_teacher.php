<style>
    th {
        text-align: center;
    }
</style>
<div class="container">
    <h2><?php echo $title; ?></h2>


    <?php echo validation_errors(); ?>
    <?php echo form_open('user/self_edit_teacher/' . $teacher['id'], array('class' => 'form-horizontal', 'role' => 'form')); ?>
    <input type="hidden" name="teacher_id" value="<?php echo $teacher['id'] ?>">
    <table class="table table-bordered table-hover table-striped text-center">
        <tr>
            <th>昵称</th>
            <td>
                <input type="text" name="name" class="form-control" value="<?php echo $teacher['name'] ?>">
            </td>
        </tr>
        <tr>
            <th>电子邮件</th>
            <td>
                <input type="text" name="email" class="form-control" value="<?php echo $teacher['email'] ?>">
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <button type="submit" name="submit" value="submit" class="btn btn-success">修改</button>
            </td>
        </tr>
    </table>
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
