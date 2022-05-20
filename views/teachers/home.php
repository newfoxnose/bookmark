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

    .edit_bookmark {
        cursor: url(<?php echo site_url('resource/images/pen.cur');?>), pointer;
    }

    a {
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
        $spaces = '';
        for ($i = 0; $i < $level; $i++) {
            $spaces = $spaces . "&nbsp;&nbsp;&nbsp;&nbsp;";
        }
        $out = '<option value="' . $sub_item['id'] . '" lv="' . $level . '">' . $spaces . $sub_item['folder_name'] . '</option>';
        if ($sub_item['subfolder'] != null) {
            foreach ($sub_item['subfolder'] as $item) {
                $out = $out . get_select_folder($item, $select_folder, $level + 1);
            }
        }
        return $out;
    }

    if ($cookie_level != 'work') {
        $select_folder = '<option value="-1" lv="-1">根目录</option>';
    } else {
        $select_folder = '';
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
                        <button class="btn btn-default" type="button"><i class="fa fa-search"
                                                                         onclick="getData()"></i></button>
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

    if ($root_bookmarks != null) {
        foreach ($root_bookmarks as $item) {
            echo bookmark_output($item);
        }
    }

    if ($folder != null) {
        foreach ($folder as $item) {
            echo folder_bookmark_output($item);
        }
    }


    ///*
    function folder_bookmark_output($folder_item)
    {
        $out = '';
        if ($folder_item['subfolder'] != null || $folder_item['bookmarks'] != null) {
            $out = $out . "<div><h2 class='folder_title' data-toggle='collapse' data-target='#" . $folder_item['id'] . "'><i class='fa fa-folder-open-o' aria-hidden='true'></i>  " . $folder_item['folder_name'] . "</h2>\n";
            $out = $out . "<div class='sub_folder collapse in' id='" . $folder_item['id'] . "'>\n";
            if ($folder_item['bookmarks'] != null) {
                $out = $out . bookmark_output($folder_item['bookmarks']);
            }
            if ($folder_item['subfolder'] != null) {

                foreach ($folder_item['subfolder'] as $item) {
                    $out = $out . folder_bookmark_output($item);
                }
            }
            $out = $out . "</div></div>\n";
        } else {
            $out = $out . "<h2>" . $folder_item['folder_name'] . "</h2>\n";
        }
        return $out;
    }

    //*/
    /*
    function folder_bookmark_output($folder_item)
    {
        if ($folder_item['subfolder'] != null || $folder_item['bookmarks'] != null) {
            $out =  "<h2 class='folder_title' data-toggle='collapse' data-target='#" . $folder_item['id'] . "'><i class='fa fa-folder-open-o' aria-hidden='true'></i>  " . $folder_item['folder_name'] . "</h2>\n";
            $out =$out . "<div class='sub_folder collapse in' id='" . $folder_item['id'] . "'>\n";
        }
        if ($folder_item['bookmarks'] != null) {
            //$out = $out . "<div id='" . $folder_item['id'] . "' class='collapse in'>\n";
            $out = $out . bookmark_output($folder_item['bookmarks']);
            //$out = $out . "</div>\n";
        }
        if ($folder_item['subfolder'] != null || $folder_item['bookmarks'] != null) {
            $out = $out . "</div>\n";
        }
        if ($folder_item['subfolder'] != null) {
            foreach ($folder_item['subfolder'] as $item) {
                $out = $out . folder_bookmark_output($item);
            }
        }
        return $out;
    }
   */


    function bookmark_output2($bookmark)
    {
        $out = '';
        for ($i = 0; $i < count($bookmark); $i++) {
            if ($bookmark[$i]['url'] != '') {
                $out = $out . mb_substr($bookmark[$i]['title'], 0, 10);
            }
        }
        return $out;
    }

    function bookmark_output($bookmark)
    {
        $out = '';
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
                        $out = $out . "</div></div></div>\n";
                    }
                    $out = $out . '<div class="panel panel-' . $arr[$j] . '"><div class="panel-heading">' . "\n";
                    $out = $out . $tag;
                    $out = $out . '</div><div class="panel-body"><div class="row">' . "\n";
                }
                $out = $out . '<div class="col-xs-6 col-sm-3 col-md-2 href-div">';
                if ($bookmark[$i]['icon'] != '' && $bookmark[$i]['icon'] != null) {
                    $icon = '<img src="' . $bookmark[$i]['icon'] . '" style="width:16px;height:16px;">&nbsp;';
                } elseif ($bookmark[$i]['icon_uri'] != '' && $bookmark[$i]['icon_uri'] != null) {
                    $icon = '<img src="' . $bookmark[$i]['icon_uri'] . '" style="width:16px;height:16px;">&nbsp;';
                } else {
                    $icon = '<img src="' . site_url('resource/images/default.ico') . '" style="width:16px;height:16px;">&nbsp;';
                }
                $out = $out . '<a class="edit_bookmark" onclick="edit_bookmark(' . $bookmark[$i]['id'] . ',\'' . $bookmark[$i]['safe_code'] . '\')">';
                $out = $out . $icon;
                $out = $out . "</span>\n";
                $out = $out . '<a class="tooltip-toggle href" href="' . $bookmark[$i]['url'] . '" target="_blank" data-toggle="tooltip" data-placement="top" title="' . $bookmark[$i]['title'] . '<br>' . $bookmark[$i]['url'] . '">' . "\n";
                $out = $out . mb_substr($bookmark[$i]['title'], 0, 10);
                $out = $out . "</a>\n";
                if ($bookmark[$i]['is_private'] == 1) {
                    $out = $out . '<i class="fa fa-lock red"></i>';
                }
                $out = $out . "</div>\n";
                $out = $out . "\n";
                if ($bookmark[$i]["tag"] == '') {
                    $tmp_tag = "无标签";
                } else {
                    $tmp_tag = $bookmark[$i]["tag"];
                }
                if ($i == count($bookmark) - 1) {
                    $out = $out . "</div></div></div>\n";
                }
            }
        }
        return $out;
    }

    ?>
</div>
<script>
    $(function () {
        $('.tooltip-toggle').tooltip(
            {html: true}
        );
    });

    function edit_bookmark(id, code) {
        layer.open({
            type: 2,
            title: '编辑书签',
            shadeClose: true,
            shade: 0.8,
            offset: '100px',
            area: ['600px', '380px'],
            content: '<?php echo site_url('user/edit_bookmark/'); ?>' + id + '/' + code//iframe的url
        });
    }

    function getData() {
        var url = $("#url").val();
        if (IsURL(url)) {
            $.ajax({
                url: "/user/url_title/",
                type: "POST",
                data: {"url": url},
                timeout: 5000
            }).done(function (data, status) {
                console.log(data);
                // 请求成功
                if (status == "success") {
                    $("#title").val(data);
                }
                else {
                    alert("未成功获取标题");
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                // net::ERR_CONNECTION_REFUSED 发生时，也能进入
                alert("未成功获取标题");
            });
        }
        else {
            alert("网址无效！");
        }
    };

    function IsURL(strUrl) {
        if (strUrl == '' || strUrl == null) {
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


    $(function () {
        $(".folder_title").each(function () {
            $(this).click(function () {
                if ($(this).hasClass("collapsed")) {
                    $(this).parent().find(".fa:first").removeClass("fa-folder-o");
                    $(this).parent().find(".fa:first").addClass("fa-folder-open-o");
                }
                else {
                    $(this).parent().find(".fa:first").removeClass("fa-folder-open-o");
                    $(this).parent().find(".fa:first").addClass("fa-folder-o");
                }
            });
        });
    });
</script>