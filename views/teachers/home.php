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
    .sub_folder{
        margin-left:20px;
    }
</style>
<div class="container">
    <h1><?php echo $title; ?>
    </h1>

    <?php
    //$json = json_encode($bookmark, JSON_UNESCAPED_UNICODE);
    //echo $json;


    // var_dump($bookmark);

    foreach ($bookmark as $key0 => $value0) {
        if (is_string($key0)) {
            echo "<div class='sub_folder'>";
            echo "<h2>" . $key0 . "</h2>";
            foreach ($value0 as $key1 => $value1) {
                if (is_string($key1)) {
                    echo "<div class='sub_folder'>";
                    echo "<h3>" . $key1 . "</h3>";
                    foreach ($value1 as $key2 => $value2) {
                        if (is_string($key2)) {
                            echo "<h4>" . $key2 . "</h4>";   //这里其实不会显示
                        }
                        bookmark_output($value2);
                    }
                    echo "</div>";
                }
                bookmark_output($value1);
            }
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
        for ($i = 0;$i < count($bookmark);$i++) {
            if ( $bookmark[$i]['url']!='') {
                if ($bookmark[$i]["tag"]==''){
                    $tag="无标签";
                }
                else{
                    $tag=$bookmark[$i]["tag"];
                }
                if ($tmp_tag != $tag) {
                    if ($i != 0) {
                        $j++;
                        if ($j == 5) {
                            $j = 0;
                        }
                        echo '</div></div>';
                    }
                    echo '<div class="panel panel-' . $arr[$j] . '"><div class="panel-heading">';
                    echo $tag;
                    echo '</div><div class="panel-body">';
                }
                echo '<span class="label label-' . $arr[$j] . '"><a class="tooltip-toggle href" href="' . $bookmark[$i]['url'] . '" target="_blank" data-toggle="tooltip" data-placement="top" title="' . $bookmark[$i]['url'] . '">' . mb_substr($bookmark[$i]['title'],0,10) . '</a></span>';
                echo "\n";
                if ($bookmark[$i]["tag"]==''){
                    $tmp_tag="无标签";
                }
                else{
                    $tmp_tag=$bookmark[$i]["tag"];
                }
                if ($i == count($bookmark)-1) {
                    echo '</div></div>';
                }
            }
        }
    }

    ?>


</div>


<script>
    $(function () {
        $('.tooltip-toggle').tooltip();
    });
</script>