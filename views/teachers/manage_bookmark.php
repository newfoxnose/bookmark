<div class="container">
    <h2><?php echo $title; ?>
        <span class="pull-right small">
<a href="<?php echo site_url('user/import/'); ?>">导入</a>|<a href="<?php echo site_url('user/manage_folder/'); ?>">目录</a>
        </span>
    </h2>
    <table class="table table-bordered table-striped">
        <tr>
            <th>序号</th>
            <th>标题</th>
            <th>网址</th>
            <th>标签</th>
            <th>目录</th>
            <th>私有</th>
            <th>操作</th>
        </tr>
        <?php echo form_open('user/manage_bookmark/', array('class' => 'form-horizontal', 'role' => 'form')); ?>
        <tr>
            <td>
                新增
            </td>
            <td>
                <input type="text" class="form-control" name="title" id="title" placeholder="标题">
            </td>
            <td>
                <input type="text" class="form-control" name="url" id="url" placeholder="网址">
            </td>
            <td><input list="select_code" name="tag" placeholder="输入或选择标签" value="" class="form-control"/>
                <datalist id="select_code">
                    <?php
                    foreach ($tag as $item):
                        echo '<option value="' . $item['tag'] . '">' . $item['tag'] . '</option>';
                    endforeach;
                    ?>
                </datalist>
            </td>
            <td>
                <select class="form-control" name="folder_id">
                    <?php
                    foreach ($folder as $item): ?>
                        <option value="<?php echo $item['id']; ?>"><?php echo $item['folder_name']; ?></option>
                        <?php
                        foreach ($item['subfolder'] as $sub_item): ?>
                            <option value="<?php echo $sub_item['id']; ?>">
                                &nbsp;&nbsp;&nbsp;&nbsp;┗<?php echo $sub_item['folder_name']; ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </select>
            </td>
            <td>
                <input type="checkbox" name="is_private" class="checkbox" value="1">
            </td>
            <td>
                <button type="submit" name="submit" value="addnew" class="btn btn-success">提交</button>
            </td>
        </tr>
        </form>
        <?php
        $i=1;
        foreach ($bookmark as $item):
            ?>
            <?php echo form_open('user/manage_bookmark/', array('class' => 'form-horizontal', 'role' => 'form')); ?>
            <tr>
                <td><?php echo $i; ?><input type="hidden" name="id" class="form-control" value="<?php echo $item['id']; ?>"></td>
                <td>
                    <input type="text" name="title" class="form-control" value="<?php echo $item['title']; ?>"></td>
                <td>
                    <input type="text" name="url" class="form-control" value="<?php echo $item['url']; ?>"></td>
                <td>
                    <input type="text" name="tag" class="form-control" value="<?php echo $item['tag']; ?>">
                </td>
                <td>
                    <select class="form-control" name="folder_id">
                        <?php
                        foreach ($folder as $folder_item): ?>
                            <option value="<?php echo $folder_item['id']; ?>" <?php
                            if ($item['folder_id'] == $folder_item['id']) {
                                echo " selected";
                            }
                            ?>><?php echo $folder_item['folder_name']; ?></option>
                            <?php
                            foreach ($folder_item['subfolder'] as $sub_item): ?>
                                <option value="<?php echo $sub_item['id']; ?>" <?php
                                if ($item['folder_id'] == $sub_item['id']) {
                                    echo " selected";
                                }
                                ?>>
                                    &nbsp;&nbsp;&nbsp;&nbsp;┗<?php echo $sub_item['folder_name']; ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td>
                    <input type="checkbox" name="is_private" class="checkbox" value="1" <?php
                    if ($item['is_private'] == 1) {
                        echo " checked";
                    }
                    ?>>
                </td>
                <td>
                    <button type="submit" name="submit" value="update" class="btn btn-success">修改</button>
                    <button type="submit" name="submit" value="delete" class="btn btn-success" onclick="javascript:return del();">删除</button>
                </td>
            </tr>
            </form>
        <?php
            $i++;
        endforeach;
        ?>
    </table>
</div>