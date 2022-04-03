
<div class="container">
    <h2><?php echo $title;?></h2>

    <?php echo validation_errors(); ?>
    <?php echo form_open_multipart('user/import',array('class' => 'form-horizontal', 'role' => 'form')); ?>
    <div class="form-group">
        <label for="file" class="col-sm-2 control-label">选择文件</label>
        <div class="col-sm-10">
            <input type="file" name="file" size="20" class="form-control" id="fileInput"/>
            <textarea name="json_string" id="json_string" class="hide"></textarea>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" name="submit" value="submit" class="btn btn-default">提交</button>
        </div>
    </div>
    </form>
    <div class="panel-body">
        <?php
        foreach ($output as $key=>$value){
            echo $value."<bR>";
        }
        ?>
    </div>
</div>
<div id="bm" class="hide"></div>
<script>
    $(document).ready(function(){

        document.getElementById("fileInput")
            .addEventListener("change",function selectedFileChanged(){
                if (this.files.length===0){
                    alert("请选择文件！");
                    return;
                }
                var reader=new FileReader();
                reader.onload=function fileReadCompleted(){
                    $("#bm").html(reader.result)
                    var dl=$("#bm dl").first();
                    console.log("dt长度为："+dl.children("dt").length);
                    //var dt = dl.children("dt").eq(0);
                    //var obj = foo(dt);
                    var arr1 = [];
                    var obj1 = {};
                    for (var i = 0; i < dl.children("dt").length; i++) {
                        // 遍历下一级dt标签
                        var tmp1 = foo(dl.children("dt").eq(i));
                        // 将返回的对象push至子文件数组
                        arr1.push(tmp1);
                    }
                    // 创建文件夹与子文件数组的键值对
                    obj1["根"] = arr1;
                    // 将对象转化为json字符串，添加额外参数使json格式更易阅读
                    var s = JSON.stringify(obj1, null, 4);
                    $("#json_string").val(s);
                    console.log(s);
                    // 将json字符串写入json文件
                    //fs.writeFileSync('output.json', s);
                    function foo(dt) {
                        // h3标签为文件夹名称
                        var h3 = dt.children("h3");
                        if (h3.length == 0) {
                            // a标签为网址
                            var a = dt.children("a");
                            // 返回该书签的名称和网址组成的对象
                            return a.length > 0 ? {"name": a.text(), "href": a.attr('href'), "icon_uri": a.attr('icon_uri'), "icon": a.attr('icon')} : null;
                        }
                        var h3_text = h3.text();
                        var arr = [];
                        var obj = {};
                        // 获取下一级dt标签集合
                        var dl = dt.children("dl");
                        var dtArr = dl.children("dt");
                        for (var i = 0; i < dtArr.length; i++) {
                            // 遍历下一级dt标签
                            var tmp = foo(dtArr.eq(i));
                            // 将返回的对象push至子文件数组
                            arr.push(tmp);
                        }
                        // 创建文件夹与子文件数组的键值对
                        obj[h3_text] = arr;
                        // 返回该对象
                        return obj;
                    }
                };
                reader.readAsText(this.files[0]);      //这句必须有
            });
    });
</script>
