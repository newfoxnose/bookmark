<style>
    .filetype {
        width: 50px;
    }
</style>
<div class="container">
    <h2><?php echo $title; ?>

        <span class="small pull-right" style="margin:10px">
            <a href="<?php echo site_url('user/list_documents/'); ?>">公共文档</a>
                |
                <a href="<?php echo site_url('user/my_documents/'.$grade.'/'.$subject_id.'/'. $category_id); ?>">我的文档</a>
            </span>
    </h2>

    <?php echo validation_errors();?>

    <?php echo form_open('user/self_edit_document/' . $grade . '/' . $subject_id . '/' . $category_id.'/'.$document["id"], array('class' => 'form-horizontal', 'role' => 'form', 'enctype' => 'multipart/form-data')); ?>
    <div class="form-group">
        <label for="folder" class="col-sm-2 control-label">文件名</label>
        <div class="col-sm-10">
            <?php
            echo show_fileicon($document["original_filename"]) . "<br>";
            echo $document['original_filename'];
            ?>
        </div>
    </div>
    <div class="form-group">
        <label for="grade" class="col-sm-2 control-label">年级</label>
        <div class="col-sm-10">
            <select class="form-control" name="grade">
                <?php
                for ($i = 0; $i < count(GRADE_ARR); $i++) {
                    ?>
                    <option value="<?php echo GRADE_ARR[$i]; ?>"
                        <?php
                        if ($document['grade']==GRADE_ARR[$i])
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
                        if ($document['subject_id']==$subjects_item['id'])
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
                        if ($document['category_id']==$document_categories_item['id'])
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
            <input type="text" class="form-control" name="folder" id="folder" value="<?php echo $document['folder'];?>">
        </div>
    </div>
    <div class="form-group">
        <label for="is_private" class="col-sm-2 control-label">是否公开</label>
        <div class="col-sm-10">
            <input type="radio" name="is_private" value="0">公开
            <input type="radio" name="is_private" value="1"
                <?php
                if ($document['is_private']==1){
                    echo " checked";
                }
                ?>
            >私密
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" name="submit" value="提交" class="btn btn-success">提交</button>
        </div>
    </div>
    </form>
</div>
