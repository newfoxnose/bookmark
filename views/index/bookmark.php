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
    <h2><?php echo $title; ?>
        <span class="small pull-right">
            <?php
            if ($_SESSION['login'] == "yes"){
                echo '<a href="'.site_url('user/home/').'">我的首页</a>';
                echo ' | ';
                echo '<a href="'.site_url('index/logout/').'">退出</a>';
            }
            else{
                echo '<a href="'.site_url('index/login/').'">登入</a>';
                echo ' | ';
                echo '<a href="'.site_url('index/reg/').'">注册</a>';
            }
            ?>
        </span>
    </h2>

    <?php
    $arr = array("primary", "success", "info", "warning", "danger");
    $j = 0;
    $tmp_tag = null;
    for ($i = 0; $i < count($bookmark); $i++) {
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
        echo '</a>';
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
    ?>
</div>


<script>
    $(function () {
        $('.tooltip-toggle').tooltip({html: true});
    });
</script>