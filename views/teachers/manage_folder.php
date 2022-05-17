<div class="container">
    <h2><?php echo $title; ?>
    </h2>
    <?php echo validation_errors(); ?>
    <?php

    define("TD_WIDTH",array("20%","30%","30%","20%"));

    function get_select_folder($sub_item, $select_folder, $level = 1, $folder_id = 0)
    {
        $spaces='';
        for ($i=0;$i<$level;$i++){
            $spaces=$spaces."&nbsp;&nbsp;&nbsp;&nbsp;";
        }
        $out = '<option value="' . $sub_item['id'] . '" lv="' . $level . '">' . $spaces.$sub_item['folder_name'] . '</option>';
        if ($sub_item['subfolder'] != null) {
            foreach ($sub_item['subfolder'] as $item) {
                $out = $out . get_select_folder($item, $select_folder, $level + 1);
            }
        }
        return $out;
    }
    if ($cookie_level != 'work') {
        $select_folder = '<option value="-1" lv="-1">根目录</option>';
    }
    else{
        $select_folder='<option value="-1" lv="-1" disabled>根目录</option>';
    }
    foreach ($folder as $item):
        $select_folder = $select_folder . '<option value="' . $item['id'] . '" lv="0">' . $item['folder_name'] . '</option>';
        if ($item['subfolder'] != null) {
            foreach ($item['subfolder'] as $sub_item):
                $select_folder = $select_folder . get_select_folder($sub_item, $select_folder);
            endforeach;
        }
    endforeach;


    function get_subfolder($sub_item, $select_folder, $level = 1)
    {
        ?>
        <?php echo form_open('user/manage_folder/', array('class' => 'form-horizontal subfolder_form', 'role' => 'form')); ?>
        <table class="table">
            <tr>
                <input type="hidden" name="id" value="<?php echo $sub_item['id']; ?>">
                <td width="<?php echo TD_WIDTH[0];?>" align="left" style="padding-left:<?php echo $level * 30; ?>px;">
                    <img src="<?php echo site_url('resource/images/expansion.png'); ?>" width="15">
                </td>
                <td width="<?php echo TD_WIDTH[1];?>">
                    <input type="text" name="folder_name" value="<?php echo $sub_item['folder_name']; ?>"
                           class="form-control">
                </td>
                <td width="<?php echo TD_WIDTH[2];?>">
                    <input type="hidden" class="father_id" value="<?php echo $sub_item['father_id']; ?>">
                    <select class="form-control" name="father_id" lv="<?php echo $level?>" self_id="<?php echo $sub_item['id']; ?>">
                        <?php
                        echo $select_folder;
                        ?>
                    </select>
                </td>
                <td width="<?php echo TD_WIDTH[3];?>">
                    <button type="submit" name="submit" value="empty_folder" class="btn btn-success">清空</button>
                    <button type="submit" name="submit" value="update_folder" class="btn btn-success">修改</button>
                    <button type="submit" name="submit" value="delete_folder" class="btn btn-success"
                            onclick="javascript:return del();">删除
                    </button>
                </td>
            </tr>
        </table>
        </form>
        <?php
        if ($sub_item['subfolder'] != null) {
            foreach ($sub_item['subfolder'] as $item) {
                get_subfolder($item, $select_folder, $level + 1);
            }
        }
    }



    ?>
    <table class="table">
        <tr>
            <td width="<?php echo TD_WIDTH[0];?>"></td>
            <td width="<?php echo TD_WIDTH[1];?>">分类名称</td>
            <td width="<?php echo TD_WIDTH[2];?>">所属父目录</td>
            <td width="<?php echo TD_WIDTH[3];?>">操作</td>
        </tr>
        <?php

        if ($cookie_level != 'work') {
            echo form_open('user/manage_folder/', array('class' => 'form-horizontal', 'role' => 'form')); ?>
            <tr>
                <td>根目录</td>
                <td></td>
                <td>
                </td>
                <td>
                    <button type="submit" name="submit" value="empty_root" class="btn btn-success">清空</button>
                </td>
            </tr>
            </form>
            <?php
        }
            echo form_open('user/manage_folder/', array('class' => 'form-horizontal', 'role' => 'form')); ?>
            <tr>
                <td>添加目录</td>
                <td><input type="text" name="folder_name" class="form-control"></td>
                <td>
                    <select class="form-control" name="father_id">
                        <?php
                        echo $select_folder;
                        ?>
                    </select>
                </td>
                <td>
                    <button type="submit" name="submit" value="add_folder" class="btn btn-success">提交</button>
                </td>
            </tr>
            </form>
    </table>
        <?php
        foreach ($folder as $item): ?>
            <?php echo form_open('user/manage_folder/', array('class' => 'form-horizontal subfolder_form', 'role' => 'form')); ?>
            <table class="table">
            <tr>
                <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                <td width="<?php echo TD_WIDTH[0];?>"><i class="fa fa-folder-open-o" aria-hidden="true"></i></td>
                <td width="<?php echo TD_WIDTH[1];?>">
                    <input type="text" name="folder_name" value="<?php echo $item['folder_name']; ?>"
                           class="form-control">
                </td>
                <td width="<?php echo TD_WIDTH[2];?>">
                    <input type="hidden" class="father_id" value="-1">
                    <select class="form-control" name="father_id" lv="0" self_id="<?php echo $item['id']; ?>">
                        <?php
                        echo $select_folder;
                        ?>
                    </select>
                </td>
                <td width="<?php echo TD_WIDTH[3];?>">
                    <button type="submit" name="submit" value="empty_folder" class="btn btn-success">清空</button>
                    <?php
                    if ($cookie_level != 'work') {
                        ?>
                        <button type="submit" name="submit" value="update_folder" class="btn btn-success">修改</button>
                        <button type="submit" name="submit" value="delete_folder" class="btn btn-success"
                                onclick="javascript:return del();">删除
                        </button>
                        <?php
                    }
                    ?>
                </td>
            </tr>
            </table>
            </form>
            <?php
            foreach ($item['subfolder'] as $sub_item):
                get_subfolder($sub_item, $select_folder);
            endforeach; ?>
        <?php endforeach; ?>
</div>
<script>
    $(document).ready(function () {
        $(".subfolder_form").each(function () {
            $(this).find("select").val($(this).find(".father_id").val());
            $(this).find("select").children("option").each(function(){
                if($(this).attr("lv")>$(this).parent().attr("lv")||$(this).attr("value")==$(this).parent().attr("self_id")){
                    $(this).attr("disabled","disabled")
                }
            })
        });
    })
</script>
<style>
    option:disabled{
        border: 1px solid #DDD;
        background-color: #F5F5F5;
        color:#ACA899;
    }
</style>