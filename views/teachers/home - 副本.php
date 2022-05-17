<style>
    .href {
        font-size: 0.9em;
        color: #000;
    }

    .sub_folder {
        margin-left: 20px;
    }

    .href-div {
        padding-top: 10px;
        padding-bottom: 10px;
    }

    .edit_bookmark{
        cursor: url(<?php echo site_url('resource/images/pen.cur');?>), pointer;
    }
    a{
        text-decoration: none !important;
    }
</style>
<div class="container">
    <h1>
        <?php echo $title; ?>
    </h1>
    <?php

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
        $select_folder='';
    }
    foreach ($folder as $item):
        $select_folder = $select_folder . '<option value="' . $item['id'] . '" lv="0">' . $item['folder_name'] . '</option>';
        if ($item['subfolder'] != null) {
            foreach ($item['subfolder'] as $sub_item):
                $select_folder = $select_folder . get_select_folder($sub_item, $select_folder);
            endforeach;
        }
    endforeach;
    ?>
    <?php echo form_open('user/manage_bookmark/', array('class' => 'form-horizontal', 'role' => 'form')); ?>
    <div class="form-group">
        <div class="col-xs-12 col-sm-3">
            <div class="input-group">
                <input type="text" class="form-control" name="url" id="url" placeholder="网址">
                <span class="input-group-btn">
                        <button class="btn btn-default" type="button"><i class="fa fa-search" onclick="getData()"></i></button>
                    </span>
            </div>
        </div>
        <div class="col-xs-12 col-sm-3">
            <input type="text" class="form-control" name="title" id="title" placeholder="标题">
        </div>
        <div class="col-xs-12 col-sm-2">
            <input list="select_code" name="tag" placeholder="输入或选择标签" value="" class="form-control"/>
            <datalist id="select_code">
                <?php
                foreach ($tag as $item):
                    echo '<option value="' . $item['tag'] . '">' . $item['tag'] . '</option>';
                endforeach;
                ?>
            </datalist>
        </div>
        <div class="col-xs-12 col-sm-2">
            <select class="form-control" name="folder_id">
                <?php
                echo $select_folder;
                ?>
            </select>
        </div>
        <div class="col-xs-6 col-sm-1">
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="is_private" class="checkbox" value="1"><i class="fa fa-lock fa-2x"></i>
                </label>
            </div>
        </div>
        <div class="col-xs-6 col-sm-1">
            <button type="submit" name="submit" value="addnew_home" class="btn btn-success">添加</button>
        </div>
    </div>
    </form>
    <?php

    //$json = json_encode($bookmark, JSON_UNESCAPED_UNICODE);
    //echo $json;

    foreach ($bookmark as $key0 => $value0) {
        if (is_string($key0)) {
            echo "<div class='sub_folder'>";
            echo "<h2 data-toggle='collapse' data-target='#" . $key0 . "'>" . $key0 . "</h2>";
            echo "<div id='" . $key0 . "' class='collapse'>";
            foreach ($value0 as $key1 => $value1) {
                if (is_string($key1)) {
                    echo "<div class='sub_folder'>";
                    echo "<h3 data-toggle='collapse' data-target='#" . $key0 . $key1 . "'>" . $key1 . "</h3>";
                    echo "<div id='" . $key0 . $key1 . "' class='collapse out'>";
                    foreach ($value1 as $key2 => $value2) {
                        if (is_string($key2)) {
                            echo "<h4>" . $key2 . "</h4>";   //这里其实不会显示
                        }
                        bookmark_output($value2);
                    }
                    echo "</div>";
                    echo "</div>";
                }
                bookmark_output($value1);
            }
            echo "</div>";
            echo "</div>";
        }
        bookmark_output($value0);
        /*
        foreach ($value0 as $item0) {
            echo $item0['title'];
        }
        */
    }


    function bookmark_output($bookmark)
    {
        $arr = array("primary", "success", "info", "warning", "danger");
        $j = 0;
        $tmp_tag = "null";
        for ($i = 0; $i < count($bookmark); $i++) {
            if ($bookmark[$i]['url'] != '') {
                if ($bookmark[$i]["tag"] == '') {
                    $tag = "无标签";
                } else {
                    $tag = $bookmark[$i]["tag"];
                }
                if ($tmp_tag != $tag) {
                    if ($i != 0) {
                        $j++;
                        if ($j == 5) {
                            $j = 0;
                        }
                        echo '</div></div></div>';
                    }
                    echo '<div class="panel panel-' . $arr[$j] . '"><div class="panel-heading">';
                    echo $tag;
                    echo '</div><div class="panel-body"><div class="row">';
                }
                echo '<div class="col-xs-6 col-sm-3 col-md-2 href-div">';
                if ($bookmark[$i]['icon'] != '' && $bookmark[$i]['icon'] != null) {
                    $icon = '<img src="' . $bookmark[$i]['icon'] . '" style="width:16px;height:16px;">&nbsp;';
                } elseif ($bookmark[$i]['icon_uri'] != '' && $bookmark[$i]['icon_uri'] != null) {
                    $icon = '<img src="' . $bookmark[$i]['icon_uri'] . '" style="width:16px;height:16px;">&nbsp;';
                } else {
                    $icon =  '<img src="' . site_url('resource/images/default.ico'). '" style="width:16px;height:16px;">&nbsp;';
                }
                echo '<a class="edit_bookmark" onclick="edit_bookmark(' . $bookmark[$i]['id'].',\'' . $bookmark[$i]['safe_code'] . '\')">';
                echo $icon;
                echo '</span>';
                echo '<a class="tooltip-toggle href" href="' . $bookmark[$i]['url'] . '" target="_blank" data-toggle="tooltip" data-placement="top" title="' . $bookmark[$i]['title'] . '<br>' . $bookmark[$i]['url'] . '">';
                echo mb_substr($bookmark[$i]['title'], 0, 10);
                echo '</a>';
                if ($bookmark[$i]['is_private']==1){
                    echo '<i class="fa fa-lock red"></i>';
                }
                echo '</div>';
                echo "\n";
                if ($bookmark[$i]["tag"] == '') {
                    $tmp_tag = "无标签";
                } else {
                    $tmp_tag = $bookmark[$i]["tag"];
                }
                if ($i == count($bookmark) - 1) {
                    echo '</div></div></div>';
                }
            }
        }
    }

    ?>


</div>


<script>
    $(function () {
        $('.tooltip-toggle').tooltip(
            {html: true}
        );
    });

    function edit_bookmark(id,code){
        layer.open({
            type: 2,
            title: '编辑书签',
            shadeClose: true,
            shade: 0.8,
            area: ['600px', '380px'],
            content: '<?php echo site_url('user/edit_bookmark/'); ?>'+id+'/' +code//iframe的url
        });
    }

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

    //自动折叠功能，目前不用
    /*
    $(function () {
        $("a[data-toggle='collapse']").hover(function(){
            console.log($(this).attr('href'));
            $($(this).attr('href')).collapse('show');
        },function(){
            $($(this).attr('href')).collapse('hide');
        });
    });
    */
</script>