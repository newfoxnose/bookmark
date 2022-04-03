<style>
    .href {
        margin: 5px;
    }

    .href:hover {
        color: #fff;
        text-decoration: none;
    }

    .href:visited {
        color: #fff;
        text-decoration: none;
    }

    .href:link {
        color: #fff;
        text-decoration: none;
    }
</style>
<div class="container">
    <h2><?php echo $title; ?>
    </h2>

    <?php
    $arr = array("primary", "success", "info", "warning", "danger");
    $j = 0;
    $tmp_tag = null;
    for ($i = 0;
    $i < count($bookmark);
    $i++) {
    foreach ($teachers as $teachers_item):
        if ($bookmark[$i]['teacher_id'] == $teachers_item['id']) {
            $teacher_name = $teachers_item['name'];
        }
    endforeach;
    if ($tmp_tag != $bookmark[$i]["tag"]) {
    if ($i != 0) {
    $j++;
    if ($j == 5) {
        $j = 0;
    }
    ?>
</div>
</div>
<?php
}
?>
<div class="panel panel-<?php echo $arr[$j]; ?>">
    <div class="panel-heading">
        <?php
        if ($bookmark[$i]["tag"] == "") {
            echo "未分类";
        } else {
            echo $bookmark[$i]["tag"];
        }
        ?>
    </div>
    <div class="panel-body">
        <?php
        }
        echo '<span class="label label-' . $arr[$j] . '"><a class="tooltip-toggle href" href="' . $bookmark[$i]['url'] . '" target="_blank" data-toggle="tooltip" data-placement="top" title="' . $bookmark[$i]['url'] . '&nbsp;&nbsp;&nbsp;提供人：' . $teacher_name. '">' . $bookmark[$i]['title'] . '</a></span>';
        echo "\n";
        $tmp_tag = $bookmark[$i]["tag"];
        if ($i == count($bookmark)) {
        ?>
    </div>
</div>
<?php
}
}
?>
</div>


<script>
    $(function () {
        $('.tooltip-toggle').tooltip();
    });
</script>