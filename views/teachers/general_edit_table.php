<div class="container">
    <h2><?php echo $title; ?></h2>
    <table class="table table-bordered table-striped">
        <tr>
            <th>
                ID
            </th>
            <?php
            for($i=0;$i<count($column['column_chinese']);$i++){
                echo "<th>".$column['column_chinese'][$i]."</th>";
            }
            ?>
            <th>操作</th>
        </tr>
        <?php echo form_open('user/general_edit_table/' . $table_id, array('class' => 'form-horizontal', 'role' => 'form')); ?>
        <tr>
            <td>
                新增
            </td>
            <?php
            for($i=0;$i<count($column['column_name']);$i++){
                    echo '<td><input type="text" class="form-control" name="'.$column['column_name'][$i].'"></td>';
            }
            ?>
            <td>
                <button type="submit" name="submit" value="addnew" class="btn btn-success">提交</button>
            </td>
        </tr>
        </form>
        <?php
        foreach ($table as $table_item): ?>
            <?php echo form_open('user/general_edit_table/' . $table_id.'/'. $page, array('class' => 'form-horizontal', 'role' => 'form')); ?>
            <tr>
                <td>
                    <input type="hidden" class="form-control" name="id" value="<?php echo $table_item['id']; ?>">
                    <?php echo $table_item['id']; ?>
                </td>
                <?php
                for($i=0;$i<count($column['column_name']);$i++){
                        echo '<td><input type="text" class="form-control" name="'.$column['column_name'][$i].'" value="'.$table_item[$column['column_name'][$i]].'"></td>';
                }
                ?>
                <td>
                    <button type="submit" name="submit" value="update" class="btn btn-success">修改</button>
                    <a href="<?php echo site_url('user/general_edit_table/'. $table_id.'/'. $page.'/' . $table_item['id']); ?>" onclick="javascript:return del();">删除</a>
                </td>
            </tr>
            </form>
        <?php endforeach; ?>
    </table>
</div>