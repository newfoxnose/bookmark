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
</style>
<div class="container">
    <h1>
        <?php echo $title; ?>
    </h1>
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
        </div>
        <div class="col-xs-6 col-sm-1">
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="is_private" class="checkbox" value="1"><i class="fa fa-lock fa-2x"></i>
                </label>
            </div>
        </div>
        <div class="col-xs-6 col-sm-1">
            <button type="submit" name="submit" value="addnew" class="btn btn-success">添加</button>
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
                echo '<a class="tooltip-toggle href" href="' . $bookmark[$i]['url'] . '" target="_blank" data-toggle="tooltip" data-placement="top" title="' . $bookmark[$i]['title'] . '<br>' . $bookmark[$i]['url'] . '">';
                if ($bookmark[$i]['icon'] != '' && $bookmark[$i]['icon'] != null) {
                    $icon = '<img src="' . $bookmark[$i]['icon'] . '" style="width:16px;height:16px;">&nbsp;';
                } elseif ($bookmark[$i]['icon_uri'] != '' && $bookmark[$i]['icon_uri'] != null) {
                    $icon = '<img src="' . $bookmark[$i]['icon_uri'] . '" style="width:16px;height:16px;">&nbsp;';
                } else {
                    $icon = "";
                }
                echo $icon;
                echo mb_substr($bookmark[$i]['title'], 0, 10);
                echo '</a></div>';
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

        $("#fetch-title").click(function(){
            $.get($("#url").val(),function(data,status){
                alert("数据: " + data + "\n状态: " + status);
            });
        });
    });


    function getData(){
        console.log("abc");
        $.ajax({
            //url: "https://office.sleda.com:8097/web/index.html",
            url: "http://office.sleda.com:8096/web/index.html",
            timeout: 5000
        }).done(function (data,status) {
            // 请求成功
            if (status == "success") {
                console.log(data);
            }
            else{
                console.log("关机");
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            // net::ERR_CONNECTION_REFUSED 发生时，也能进入
            console.info("网络出错");
            $(".server_status").html("关机");
        });
        /*
        $.get("https://xzsoftware.sleda.com/", function(data,status){
            if (status == "success") {
                $(".hawkhost_status").html("开机");
            }
            else{
                $(".hawkhost_status").html("关机");
            }
        });
        */
    };


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