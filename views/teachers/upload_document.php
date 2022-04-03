
<div class="container">
    <h2><?php echo $title; ?>

        <span class="small pull-right" style="margin:10px">
            <a href="<?php echo site_url('user/list_documents/'); ?>">公共文档</a>
                |
                <a href="<?php echo site_url('user/my_documents/'.$grade.'/'.$subject_id.'/'. $category_id); ?>">我的文档</a>
            </span>
    </h2>

    <?php echo validation_errors();?>

    <?php echo form_open('user/upload_document_qiniu/', array('class' => 'form-horizontal', 'role' => 'form', 'enctype' => 'multipart/form-data')); ?>


    <div class="form-group">
        <label for="attachment" class="col-sm-2 control-label">选择文件</label>
        <div class="col-sm-10" id="attachment">
                <input class="form-control" type="file" name="userfile"/>
        </div>
    </div>
    <p>注：格式为doc、docx、xls、xlsx、ppt、pptx、pdf、jpg、gif、bmp、png、webp、mp3、mp4、txt、zip、rar、7z，如有其他格式文件需要上传，请先打包为压缩文件。体积8M以内。</p>
    <div class="form-group">
        <label for="grade" class="col-sm-2 control-label">年级</label>
        <div class="col-sm-10">
            <select class="form-control" name="grade">
                <?php
                for ($i = 0; $i < count(GRADE_ARR); $i++) {
                    ?>
                    <option value="<?php echo GRADE_ARR[$i]; ?>"
                        <?php
                        if ($grade==GRADE_ARR[$i])
                        {echo " selected";}
                        ?>
                    >
                        <?php echo CNGRADE_ARR[$i]; ?>
                    </option>
                    <?php
                }?>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label for="subject_id" class="col-sm-2 control-label">科目</label>
        <div class="col-sm-10">
            <select class="form-control" name="subject_id">
                <?php
                foreach ($subjects as $subjects_item): ?>
                    <option value="<?php echo $subjects_item['id']; ?>"
                        <?php
                        if ($subject_id==$subjects_item['id'])
                        {echo " selected";}
                        ?>
                    ><?php echo $subjects_item['name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label for="category_id" class="col-sm-2 control-label">分类</label>
        <div class="col-sm-10">
            <select class="form-control" name="category_id">
                <?php
                foreach ($document_categories as $document_categories_item): ?>
                    <option value="<?php echo $document_categories_item['id']; ?>"
                        <?php
                        if ($category_id==$document_categories_item['id'])
                        {echo " selected";}
                        ?>
                    >
                        <?php echo $document_categories_item['name']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label for="folder" class="col-sm-2 control-label">文件夹名（相同的会归为一组显示）</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" name="folder" id="folder" value="<?php echo $folder;?>">
        </div>
    </div>
    <div class="form-group">
        <label for="is_private" class="col-sm-2 control-label">是否公开</label>
        <div class="col-sm-10">
            <input type="radio" name="is_private" value="0" checked>公开
            <input type="radio" name="is_private" value="1">私密
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" name="submit" value="提交" class="btn btn-success">提交</button>
        </div>
    </div>
    </form>
</div>
