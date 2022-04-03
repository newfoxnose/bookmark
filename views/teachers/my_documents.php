<style>
    .documents {
        float: left;
        height: 220px;
        width: 220px;
        font-size: 9pt;
        padding: 15px;
        margin-right: 5px;
        margin-bottom: 8px;
        box-shadow: 1px 1px 1px 2px #ddd;
        text-align: center;
        padding-top: 10px;
        padding-bottom: 2px;
        padding-left: 2px;
        padding-right: 2px;
    }

    .panel {
        margin: 4px;
    }

    .panel-body {
        padding: 4px;
    }

    .img {
        max-width: 140px;
        max-height: 80px;
        margin-bottom: 5px;
        border: 1px solid #000;
        -moz-box-shadow: 3px 3px 4px #000;
        -webkit-box-shadow: 3px 3px 4px #000;
        box-shadow: 3px 3px 4px #000;
        background: #fff;
        filter: progid:DXImageTransform.Microsoft.Shadow(Strength=4, Direction=135, Color='#000000');
    }

    .filetype {
        width: 50px;
    }

    .zoom {
        transform: scale(1.1, 1.1);
        -ms-transform: scale(1.1, 1.1); /* IE 9 */
        -webkit-transform: scale(1.1, 1.1); /* Safari and Chrome */
        background: #eee
    }

    .filename{
        font-weight:bold;
        color:#000;
        font-size:14px;
        padding-top:10px;
        padding-bottom:10px;
    }
</style>
<div class="container">
    <h2><?php echo $title; ?>
        <span class="small pull-right" style="margin:10px">
            <a href="<?php echo site_url('user/list_documents/'); ?>">公共文档</a>
                |
                <a href="<?php echo site_url('user/upload_document/' . $grade . '/' . $subject_id . '/' . $category_id); ?>">上传</a>
            </span>
    </h2>
    <h4>
        选择年级：
        <?php
        for ($i = 0; $i < count($grades); $i++) {
            if ($grades[$i] == $grade) {
                echo $cngrades[$i];
            } else {
                ?>
                <a href="<?php echo site_url('user/my_documents/' . $grades[$i] . '/' . $subject_id . '/' . $category_id); ?>">
                    <?php echo $cngrades[$i]; ?>
                </a>
                <?php
            }
        }
        ?>
    </h4>
    <h4>
        选择科目：
        <?php
        foreach ($subjects as $subjects_item):
            if ($subjects_item['id'] == $subject_id) {
                echo $subjects_item['name'];
            } else {
                ?>
                <a href="<?php echo site_url('user/my_documents/' . $grade . '/' . $subjects_item['id'] . '/' . $category_id); ?>"><?php echo $subjects_item['name']; ?></a>
                <?php
            }
        endforeach; ?>
    </h4>
    <h4>
        选择分类：
        <?php
        foreach ($document_categories as $item):
            if ($item['id'] == $category_id) {
                echo $item['name'];
            } else {
                ?>
                <a href="<?php echo site_url('user/my_documents/' . $grade . '/' . $subject_id . '/' . $item['id']); ?>"><?php echo $item['name']; ?></a>
                <?php
            }
        endforeach; ?>
    </h4>
    <h4>
        <input type="text" class="form-control" id="search" placeholder="输入关键词在当前页筛选，用空格分隔多个关键词">
    </h4>
    <?php
    for ($i = 0; $i < count($documents); $i++) {
        if ($tmp != $images[$i]["folder"]) {
            echo '<div style="clear:both">';
        }
        ?>
        <div class="documents mytree">
            <?php
            $qiniu_file=QINIU_DOMAIN.$documents[$i]["path"]."/".$documents[$i]["rndstring"] ."/". $documents[$i]["real_filename"];
            if (is_img($documents[$i]['real_filename'])) {
                if (thumb_img($documents[$i]['real_filename'])) {
                       $thumb_src=site_url($documents[$i]["path"]."/thumb_".$documents[$i]["rndstring"] .".jpg");
                } else {
                    $thumb_src =$qiniu_file;
                }
                ?>
                <div class="layer-photos">
                    <img class="img" layer-src="<?php echo $qiniu_file; ?>"
                         src="<?php echo $thumb_src ?>">
                </div>
                <span class="filename"><?php echo $documents[$i]["original_filename"] ?></span>
                <?php
            }
            else {
                if ($documents[$i]["file_ext"] == 'mp4'){
                    ?>
                    <a onclick="popup('<?php echo $qiniu_file; ?>')"
                       style="cursor:pointer;">
                        <?php
                        echo show_fileicon($documents[$i]["original_filename"]) . "<br>";
                        ?>
                        <span class="filename"><?php echo $documents[$i]["original_filename"] ?></span>
                    </a>
                    <?php
                }
           elseif ($documents[$i]["file_ext"] == 'txt'){
                    ?>
                <a onclick="popup('<?php echo site_url('index/txt_preview/'  . $documents[$i]["id"]); ?>')"
                   style="cursor:pointer;">
                    <?php
                    echo show_fileicon($documents[$i]["original_filename"]) . "<br>";
                    ?>
                    <span class="filename"><?php echo $documents[$i]["original_filename"] ?></span>
                </a>
                <?php
                }
                elseif ($documents[$i]["file_ext"] == 'mp3') {
                    ?>
                    <audio controls style="max-width:170px !important;">
                        <source src="<?php echo $qiniu_file; ?>"
                                type="audio/mpeg">
                        您的浏览器不支持 audio 元素。
                    </audio>
                    <br>
                    <span class="filename"><?php echo $documents[$i]["original_filename"] ?></span>
                    <?php
                }
                elseif (is_office($documents[$i]["real_filename"])) {
                    ?>
                    <a onclick="popup('https://view.officeapps.live.com/op/view.aspx?src=<?php echo $qiniu_file; ?>')"
                       style="cursor:pointer;">
                        <?php
                        echo show_fileicon($documents[$i]["original_filename"]) . "<br>";
                        ?>
                        <span class="filename"><?php echo $documents[$i]["original_filename"] ?></span>
                    </a>
                    <?php
                }elseif ($documents[$i]["file_ext"]=="pdf") {
                    ?>
                    <a onclick="popup('<?php echo $qiniu_file; ?>')"
                       style="cursor:pointer;">
                        <?php
                        echo show_fileicon($documents[$i]["original_filename"]) . "<br>";
                        ?>
                        <span class="filename"><?php echo $documents[$i]["original_filename"] ?></span>
                    </a>
                    <?php
                }
                else{
                    ?>
                    <?php
                    echo show_fileicon($documents[$i]["original_filename"]) . "<br>";
                    ?>
                    <span class="filename"><?php echo $documents[$i]["original_filename"] ?></span>
                    <?php
                }
            }
            ?>
                    <div style="text-align:left;padding-left:10px;">
                        <?php
                        if ($documents[$i]["folder"] != '') {
                            echo '文件夹：' . $documents[$i]["folder"].'<br>';
                        }
                        ?>
                        文件大小：<?php echo format_size($documents[$i]["filesize"]) ?>
                    <Br>更新日期：<?php echo $documents[$i]["update_time"] ?>
                    <Br>状态：<?php
                    if ($documents[$i]["is_private"] == 1) {
                        echo "<i class='red fa fa-lock'> 私密</i>";
                    } else {
                        echo "公开";
                    }
                    ?>
                    <Br><a href="<?php echo $qiniu_file; ?>" target="_blank">下载（右键另存为）</a>
                    <br>
                        <a href="<?php echo site_url('user/self_edit_document/' . $grade . '/' . $subject_id . '/' . $category_id . '/' . $documents[$i]["id"]); ?>">修改</a>
                        |
                        <a href="<?php echo site_url('user/self_delete_document_qiniu/' . $grade . '/' . $subject_id . '/' . $category_id . '/' . $documents[$i]["id"]); ?>"
                           onclick="javascript:return del();">删除</a>
                    </div>
        </div>
        <?php
    }
    ?>
</div>
<div class="container">
    <?php
    echo $page;
    ?>
    <br><br><br><br>
</div>
<script>
    //iframe 层
    function popup(url) {
        layer.open({
            type: 2,
            title: ' ',
            shadeClose: true,
            shade: false,
            maxmin: true, //开启最大化最小化按钮
            area: ['640px', '480px'],
            content: url
        });
    }


    $(document).ready(function () {
        $(".documents").hover(
            function () {
                $(this).addClass("zoom");
            }, function () {
                $(this).removeClass("zoom");
            });
    });

    $('#search').bind('input propertychange', function () {
        var arr = $(this).val().split(' ');//手动输入的字符串；
        $(".mytree").each(function () {
            //var paraStr = $(this).children().children().html();
            var paraStr = $(this).text();
            var included = false;
            for (var i = 0; i < arr.length; i++) {
                if (paraStr.indexOf(arr[i]) < 0) {
                    included = false;
                    break;
                }
                else {
                    included = true;
                }
            }
            if (included==false) {//不包含
                $(this).hide();
            } else {//包含
                $(this).show();
            }
        })
    });


    layer.photos({
        photos: '.layer-photos'
        ,anim: 5 //0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）
    });
</script>