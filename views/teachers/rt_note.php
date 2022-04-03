<div class="container">

    <h2><?php echo $title; ?></h2>
    <?php echo form_open('user/rt_note_save/', array('class' => 'form-horizontal', 'role' => 'form', 'enctype' => 'multipart/form-data')); ?>
    <div id="editor"><?php echo $teacher_item['rt_note']; ?></div>
    <textarea name="content" id="content" style="display:none"></textarea>
    <style>
        .w-e-text-container{
            height: 200px !important;/*!important是重点，因为原div是行内样式设置的高度300px*/
        }
    </style>
    </form>
</div>

<script src="<?php echo site_url('../resource/wangeditor/wangEditor.min.js'); ?>"></script>
<script>

    $(document).ready(function () {
        var E = window.wangEditor
        var editor = new E('#editor')
        var $text1 = $('#content')
        editor.customConfig.onchange = function (html) {
            // 监控变化，同步更新到 textarea
            $text1.val(html);
            $.post("<?php echo site_url('user/rt_note_save/'); ?>", {content: html}, function (result) {
                console.log("html="+html);
                console.log("结果："+result);
            });
        }
        editor.customConfig.menus = [
            'undo',  // 撤销
            'redo',  // 重复
            'bold',
            'underline',
            'foreColor',  // 文字颜色
            'backColor',  // 背景颜色
            'link',  // 插入链接
            'table'
        ]
        editor.customConfig.zIndex = 0;
        editor.create()
        $text1.val(editor.txt.html())
    });


</script>