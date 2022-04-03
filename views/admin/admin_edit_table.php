<div class="container">
    <h2><?php echo $title; ?></h2>
    <ul class="nav nav-pills">
        <li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                <?php echo $table_chinese; ?><span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
                <?php
                for ($i = 0; $i < count($tables); $i++) {
                    ?>
                    <li>
                        <a href="<?php echo site_url('admin/admin_edit_table/' . $i); ?>"><?php echo $tables[$i]['table_chinese']; ?></a>
                    </li>
                    <?php
                }
                ?>
            </ul>
        </li>
    </ul>

    <table class="table table-bordered table-striped">
        <tr>
            <?php
            foreach ($columns as $column_item):
                echo "<th>".$column_item['Field']."</th>";
            endforeach;
            ?>
            <th>操作</th>
        </tr>
        <?php echo form_open('admin/admin_edit_table/' . $table_id, array('class' => 'form-horizontal', 'role' => 'form')); ?>

        <tr>
            <td>
                新增
            </td>
            <?php
            foreach ($columns as $column_item):
                if ($column_item['Field']!="id"){
                    echo '<td><input type="text" class="form-control" name="'.$column_item['Field'].'" value="'.$table_item[$column_item['Field']].'"></td>';
                }
            endforeach;
            ?>
            <td>
                <button type="submit" name="submit" value="addnew" class="btn btn-success">提交</button>
            </td>
        </tr>
        </form>
        <?php
        foreach ($table as $table_item): ?>

            <?php echo form_open('admin/admin_edit_table/' . $table_id, array('class' => 'form-horizontal', 'role' => 'form')); ?>

            <tr>
                <td>
                    <input type="hidden" class="form-control" name="id" value="<?php echo $table_item['id']; ?>">
                    <?php echo $table_item['id']; ?>
                </td>

                <?php
                foreach ($columns as $column_item):
                    if ($column_item['Field']!="id"){
                        echo '<td><input type="text" class="form-control" name="'.$column_item['Field'].'" value="'.$table_item[$column_item['Field']].'"></td>';
                    }
                endforeach;
                ?>
                <td>
                    <button type="submit" name="submit" value="update" class="btn btn-success">修改</button>
                    <a href="<?php echo site_url('admin/admin_edit_table/'. $table_id.'/' . $table_item['id']); ?>" onclick="javascript:return del();">删除</a>
                </td>
            </tr>
            </form>
        <?php endforeach; ?>

    </table>
</div>
