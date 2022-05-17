<div class="container">
    <h2><?php echo $title; ?>
    </h2>
    <?php echo validation_errors(); ?>
    <table class="table">
        <tr>
            <td colspan="2">ID</td>
            <td>分类名称</td>
            <td>所属分类</td>
            <td>操作</td>
        </tr>

        <?php
        if ($cookie_level!='work') {
            echo form_open('user/manage_folder/', array('class' => 'form-horizontal', 'role' => 'form')); ?>
            <tr>
                <td colspan="2">根目录</td>
                <td></td>
                <td>
                </td>
                <td>
                    <button type="submit" name="submit" value="empty_root" class="btn btn-success">清空</button>
                </td>
            </tr>
            </form>
            <?php
            echo form_open('user/manage_folder/', array('class' => 'form-horizontal', 'role' => 'form')); ?>
            <tr>
                <td colspan="2">添加一级目录</td>
                <td><input type="text" name="folder_name" class="form-control"></td>
                <td>根目录
                </td>
                <td>
                    <button type="submit" name="submit" value="add_folder" class="btn btn-success">提交</button>
                </td>
            </tr>
            </form>
            <?php
        }
        if ($select_folder!=null) {
            echo form_open('user/manage_folder/', array('class' => 'form-horizontal', 'role' => 'form')); ?>
            <tr>
                <td colspan="2">添加二级目录</td>
                <td><input type="text" name="folder_name" class="form-control"></td>
                <td>
                    <select class="form-control" name="father_id">
                        <?php
                        foreach ($select_folder as $select_folder_item): ?>
                            <option value="<?php echo $select_folder_item['id']; ?>"><?php echo $select_folder_item['folder_name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td>
                    <button type="submit" name="submit" value="add_subfolder" class="btn btn-success">提交</button>
                </td>
            </tr>
            </form>
            <?php
        }
        foreach ($folder as $item): ?>
            <?php echo form_open('user/manage_folder/', array('class' => 'form-horizontal', 'role' => 'form')); ?>
            <tr>
                <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                <td colspan="2"><?php echo $item['id']; ?></td>
                <td>
                    <input type="text" name="folder_name" value="<?php echo $item['folder_name']; ?>"
                           class="form-control">
                </td>
                <td></td>
                <td>
                    <button type="submit" name="submit" value="empty_folder" class="btn btn-success">清空</button>
                    <?php
                    if ($cookie_level!='work') {
                        ?>
                        <button type="submit" name="submit" value="submit" class="btn btn-success">修改</button>
                        <button type="submit" name="submit" value="delete_folder" class="btn btn-success"
                                onclick="javascript:return del();">删除
                        </button>
                        <?php
                    }
                    ?>
                </td>
            </tr>
            </form>
        <?php foreach ($item['subfolder'] as $sub_item): ?>
            <?php echo form_open('user/manage_folder/', array('class' => 'form-horizontal', 'role' => 'form')); ?>
            <tr>
                <input type="hidden" name="id" value="<?php echo $sub_item['id']; ?>">
                <td align="right">
                    <img src="<?php echo site_url('resource/images/expansion.png'); ?>" width="15">
                </td>
                <td align="left"><?php echo $sub_item['id']; ?></td>
                <td>
                    <input type="text" name="folder_name" value="<?php echo $sub_item['folder_name']; ?>" class="form-control">
                </td>
                <td>
                    <select class="form-control" name="father_id">
                        <?php
                        foreach ($select_folder as $select_folder_item): ?>
                            <option value="<?php echo $select_folder_item['id']; ?>" <?php
                            if ($sub_item['father_id'] == $select_folder_item['id']) {
                                echo " selected";
                            }
                            ?>><?php echo $select_folder_item['folder_name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td>
                    <button type="submit" name="submit" value="empty_folder" class="btn btn-success">清空</button>
                    <button type="submit" name="submit" value="update_subfolder" class="btn btn-success">修改</button>
                    <button type="submit" name="submit" value="delete_subfolder" class="btn btn-success" onclick="javascript:return del();">删除</button>
                </td>
            </tr>
            </form>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </table>
</div>
