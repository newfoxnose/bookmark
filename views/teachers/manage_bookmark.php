<div class="container">
    <h2><?php echo $title; ?>
        <span class="pull-right small">
            <a href="<?php echo site_url('user/manage_folder/'); ?>">目录</a>
            |
            <a href="<?php echo site_url('user/import/'); ?>">导入</a>
            |
            <a href="<?php echo site_url('user/export/'); ?>" target="_blank">导出</a>
        </span>
    </h2>
    <table class="table table-bordered table-striped">
        <tr>
            <th>序号</th>
            <th>网址</th>
            <th>标题</th>
            <th>标签</th>
            <th>目录</th>
            <th>日期</th>
            <th>私有</th>
            <th>操作</th>
        </tr>
        <?php echo form_open('user/manage_bookmark/', array('class' => 'form-horizontal', 'role' => 'form')); ?>
        <tr>
            <td>
                新增
            </td>
            <td>
                <div class="input-group">
                    <input type="text" class="form-control" name="url" id="url" placeholder="网址">
                    <span class="input-group-btn">
                        <button class="btn btn-default" type="button"><i class="fa fa-search" onclick="getData()"></i></button>
                    </span>
                </div>
            </td>
            <td>
                <input type="text" class="form-control" name="title" id="title" placeholder="标题">
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
                        <option value="<?php echo $item['id']; ?>">
                            <?php
                            if ($item['id']!=0){
                                echo "&nbsp;&nbsp;┗";
                            }
                            echo $item['folder_name']; ?>
                        </option>
                        <?php
                    if ($item['subfolder']!=null) {
                        foreach ($item['subfolder'] as $sub_item): ?>
                            <option value="<?php echo $sub_item['id']; ?>">
                                &nbsp;&nbsp;&nbsp;&nbsp;┗<?php echo $sub_item['folder_name']; ?>
                            </option>
                        <?php endforeach;
                    }?>
                    <?php endforeach; ?>
                </select>
            </td>
            <td>
            </td>
            <td>
                <input type="checkbox" name="is_private" class="checkbox" value="1">
            </td>
            <td>
                <button type="submit" name="submit" value="addnew" class="btn btn-success">添加</button>
            </td>
        </tr>
        </form>
        <?php
        $i = 1;
        foreach ($bookmark as $item):
            ?>
            <?php echo form_open('user/manage_bookmark/', array('class' => 'form-horizontal', 'role' => 'form')); ?>
            <tr>
                <td><a name="<?php echo $item['id']; ?>"></a><?php echo $i; ?><input type="hidden" name="id"
                                                                                     class="form-control"
                                                                                     value="<?php echo $item['id']; ?>">
                </td>
                <td>
                    <input type="text" name="url" class="form-control" value="<?php echo $item['url']; ?>"></td>
                <td>
                    <input type="text" name="title" class="form-control" value="<?php echo $item['title']; ?>"></td>
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
                            ?>><?php
                                if ($folder_item['id']!=0){
                                    echo "&nbsp;&nbsp;┗";
                                }
                                echo $folder_item['folder_name']; ?></option>
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
                    <?php echo date("y/m/d H:i",strtotime($item['timestamp'])); ?>
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
                    <button type="submit" name="submit" value="delete" class="btn btn-success"
                            onclick="javascript:return del();">删除
                    </button>
                </td>
            </tr>
            </form>
            <?php
            $i++;
        endforeach;
        ?>
    </table>
</div>

<script>
    function getData(){
        var url= $("#url").val();
        if (IsURL(url)){
            $.ajax({
                url:"/user/url_title/",
                type:"POST",
                data:{"url":url},
                timeout: 5000
            }).done(function (data,status) {
                console.log(data);
                // 请求成功
                if (status == "success") {
                    $("#title").val(data);
                }
                else{
                    alert("未成功获取标题");
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                // net::ERR_CONNECTION_REFUSED 发生时，也能进入
                alert("未成功获取标题");
            });
        }
        else{
            alert("网址无效！");
        }
    };

    function IsURL(strUrl) {
        if (strUrl==''||strUrl==null){
            return false;
        }
        var regular = /^\b(((https?|http?|ftp):\/\/)?[-a-z0-9]+(\.[-a-z0-9]+)*\.(?:com|edu|gov|int|mil|net|org|biz|info|name|museum|asia|coop|aero|[a-z][a-z]|((25[0-5])|(2[0-4]\d)|(1\d\d)|([1-9]\d)|\d))\b(\/[-a-z0-9_:\@&?=+,.!\/~%\$]*)?)$/i
        if (regular.test(strUrl)) {
            return true;
        }
        else {
            return false;
        }
    }
</script>