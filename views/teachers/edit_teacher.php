<div class="container">
    <h2><?php echo $title; ?></h2>
    <?php echo validation_errors(); ?>
    <?php echo form_open_multipart('user/teacher_upload_picture/edit_teacher', array('class' => 'form-horizontal', 'role' => 'form')); ?>
    <div class="form-group">
        <label for="userfile" class="col-sm-2 control-label">照片</label>
        <div class="col-sm-5">
            <?php
            if (check_exists(base_url() . "uploads/" . $teacher['id'] . "/photo.jpg")) {
                ?>
                <a href="<?php echo base_url() . "uploads/" . $teacher['id'] . "/" ?>photo.jpg" target="_blank">
                    <img style="max-width:150px;" src="<?php echo base_url() . "uploads/" . $teacher['id'] . "/" ?>photo.jpg?<?php echo rand() ?>">
                </a>
                <input type="submit" name="submit" value="删除"/>
                <?php
            } else {
                echo '<img src="' . base_url() . 'images/nophoto.jpg">';
            }
            ?>
        </div>
        <div class="col-sm-5">
            <input type="hidden" name="teacher_id" value="<?php echo $teacher['id'] ?>">
            <input type="hidden" name="upload_type" value="photo">
            <input type="file" name="userfile" size="20"/>（必须是jpg格式，体积小于1.5M）
            <input type="submit" name="submit" value="上传"/>
        </div>
    </div>
    </form>
    <?php echo form_open('user/edit_teacher/' . $teacher['id'], array('class' => 'form-horizontal', 'role' => 'form')); ?>
    <input type="hidden" name="teacher_id" value="<?php echo $teacher['id'] ?>">
    <div class="form-group">
        <label for="name" class="col-sm-2 control-label">姓名</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" name="name" id="name" value="<?php echo $teacher['name'] ?>">
        </div>
    </div>
    <div class="form-group">
        <label for="employee_number" class="col-sm-2 control-label">工号</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" name="employee_number"
                   value="<?php echo $teacher['employee_number'] ?>">
        </div>
    </div>

    <div class="form-group">
        <label for="party_id" class="col-sm-2 control-label">政治面貌</label>
        <div class="col-sm-10">
            <select class="form-control" name="party_id">
                <?php
                foreach ($parties as $parties_item): ?>
                    <option value="<?php echo $parties_item['id']; ?>" <?php
                    if ($teacher['party_id'] == $parties_item['id']) {
                        echo " selected";
                    }
                    ?>><?php echo $parties_item['name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label for="marriage_id" class="col-sm-2 control-label">婚姻状态</label>
        <div class="col-sm-10">

            <select class="form-control" name="marriage_id">
                <?php
                foreach ($marriage as $marriage_item): ?>
                    <option value="<?php echo $marriage_item['id']; ?>" <?php
                    if ($teacher['marriage_id'] == $marriage_item['id']) {
                        echo " selected";
                    }
                    ?>><?php echo $marriage_item['name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label for="emergency_contact" class="col-sm-2 control-label">紧急联系人</label>
        <div class="col-sm-10">
            <input type="text" name="emergency_contact" class="form-control"
                   value="<?php echo $teacher['emergency_contact'] ?>">
        </div>
    </div>

    <div class="form-group">
        <label for="address" class="col-sm-2 control-label">现居住地址</label>
        <div class="col-sm-10">
            <input type="text" name="address" class="form-control" value="<?php echo $teacher['address'] ?>">
        </div>
    </div>
    <div class="form-group">
        <label for="phone" class="col-sm-2 control-label">手机号码</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" name="phone" id="phone"
                   value="<?php echo $teacher['phone'] ?>">
        </div>
    </div>

    <div class="form-group">
        <label for="email" class="col-sm-2 control-label">电子邮件</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" name="email" value="<?php echo $teacher['email'] ?>">
        </div>
    </div>
    <hr>
    <div class="form-group">
        <label for="identity_number" class="col-sm-2 control-label">身份证号码</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" name="identity_number" id="identity_number"
                   value="<?php echo $teacher['identity_number'] ?>" required="required">
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">性别</label>
        <div class="col-sm-10">
            <?php echo get_sex_from_id($teacher['identity_number'], 1) ?>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">出生日期</label>
        <div class="col-sm-10">
            <?php echo get_birthday_from_id($teacher['identity_number']) ?>
        </div>
    </div>

    <div class="form-group">
        <label for="education_id" class="col-sm-2 control-label">民族</label>
        <div class="col-sm-10">
            <select class="form-control" name="ethnicity_id">
                <?php
                foreach ($ethnicity as $ethnicity_item): ?>
                    <option value="<?php echo $ethnicity_item['id']; ?>" <?php
                    if ($teacher['ethnicity_id'] == $ethnicity_item['id']) {
                        echo " selected";
                    }
                    ?>><?php echo $ethnicity_item['name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label for="registration_address" class="col-sm-2 control-label">户籍所属地区（以身份证为准）</label>
        <div class="col-sm-10">
            <select class="form-control" name="county_id">
                <?php
                foreach ($counties as $county_item): ?>
                    <option value="<?php echo $county_item['id']; ?>" <?php
                    if ($teacher['county_id'] == $county_item['id']) {
                        echo " selected";
                    }
                    ?>><?php echo $county_item['name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label for="registration_address" class="col-sm-2 control-label">户籍地址（以身份证为准）</label>
        <div class="col-sm-10">
            <input type="text" name="registration_address" class="form-control"
                   value="<?php echo $teacher['registration_address'] ?>">
        </div>
    </div>

    <hr>

    <div class="form-group">
        <label for="start_work_date" class="col-sm-2 control-label">参加工作日期</label>
        <div class="col-sm-10">
            <input type="text" name="start_work_date" value="<?php echo $teacher['start_work_date'] ?>" class="form-control datepicker">
        </div>
    </div>

    <div class="form-group">
        <label for="first_graduate_date" class="col-sm-2 control-label">第一学历毕业日期</label>
        <div class="col-sm-10">
            <input type="text" name="first_graduate_date" value="<?php echo $teacher['first_graduate_date'] ?>"
                   class="form-control datepicker">
        </div>
    </div>
    <div class="form-group">
        <label for="first_graduate_school" class="col-sm-2 control-label">毕业学校</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" name="first_graduate_school"
                   value="<?php echo $teacher['graduate_school'] ?>">
        </div>
    </div>
    <div class="form-group">
        <label for="first_major" class="col-sm-2 control-label">所学专业</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" name="first_major" value="<?php echo $teacher['major'] ?>">
        </div>
    </div>


    <hr>
    <div class="form-group">
        <label for="education_id" class="col-sm-2 control-label">最高学历</label>
        <div class="col-sm-10">
            <select class="form-control" name="education_id">
                <?php
                foreach ($education as $education_item): ?>
                    <option value="<?php echo $education_item['id']; ?>" <?php
                    if ($teacher['education_id'] == $education_item['id']) {
                        echo " selected";
                    }
                    ?>><?php echo $education_item['name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label for="graduate_date" class="col-sm-2 control-label">毕业日期</label>
        <div class="col-sm-10">
            <input type="text" name="graduate_date" value="<?php echo $teacher['graduate_date'] ?>" class="form-control datepicker">
        </div>
    </div>
    <div class="form-group">
        <label for="graduate_school" class="col-sm-2 control-label">毕业学校</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" name="graduate_school" id="graduate_school"
                   value="<?php echo $teacher['graduate_school'] ?>">
        </div>
    </div>
    <div class="form-group">
        <label for="major" class="col-sm-2 control-label">所学专业</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" name="major" id="major"
                   value="<?php echo $teacher['major'] ?>">
        </div>
    </div>
    <div class="form-group">
        <label for="education_code" class="col-sm-2 control-label">毕业证编号</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" name="education_code" id="education_code"
                   value="<?php echo $teacher['education_code'] ?>">必须是毕业证，不要学位证。
        </div>
    </div>
    <hr>


    <div class="form-group">
        <label for="certification_id" class="col-sm-2 control-label">教师资格证学段</label>
        <div class="col-sm-10">
            <select class="form-control" name="certification_id">
                <?php
                foreach ($certification as $certification_item): ?>
                    <option value="<?php echo $certification_item['id']; ?>" <?php
                    if ($teacher['certification_id'] == $certification_item['id']) {
                        echo " selected";
                    }
                    ?>><?php echo $certification_item['name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label for="certificate_code" class="col-sm-2 control-label">编号</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" name="certificate_code"
                   value="<?php echo $teacher['certificate_code'] ?>">
        </div>
    </div>
    <div class="form-group">
        <label for="certificate_subject_id" class="col-sm-2 control-label">科目</label>
        <div class="col-sm-10">
            <select class="form-control" name="certificate_subject_id">
                <?php
                foreach ($subjects as $subjects_item): ?>
                    <option value="<?php echo $subjects_item['id']; ?>" <?php
                    if ($teacher['certificate_subject_id'] == $subjects_item['id']) {
                        echo " selected";
                    }
                    ?>><?php echo $subjects_item['name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label for="certificate_date" class="col-sm-2 control-label">取得日期</label>
        <div class="col-sm-10">
            <input type="text" name="certificate_date" value="<?php echo $teacher['certificate_date'] ?>"
                   class="form-control datepicker">
        </div>
    </div>
    <hr>
    <div class="form-group">
        <label for="title_id" class="col-sm-2 control-label">职称</label>
        <div class="col-sm-10">
            <select class="form-control" name="title_id">
                <?php
                foreach ($titles as $titles_item): ?>
                    <option value="<?php echo $titles_item['id']; ?>" <?php
                    if ($teacher['title_id'] == $titles_item['id']) {
                        echo " selected";
                    }
                    ?>><?php echo $titles_item['name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label for="title_code" class="col-sm-2 control-label">职称编号</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" name="title_code" value="<?php echo $teacher['title_code'] ?>">
        </div>
    </div>
    <div class="form-group">
        <label for="title_date" class="col-sm-2 control-label">取得职称日期</label>
        <div class="col-sm-10">
            <input type="text" name="title_date" value="<?php echo $teacher['title_date'] ?>" class="form-control datepicker">
        </div>
    </div>
    <hr>
    <div class="form-group">
        <label for="mandarin_level_id" class="col-sm-2 control-label">普通话等级</label>
        <div class="col-sm-10">
            <select class="form-control" name="mandarin_level_id">
                <?php
                foreach ($mandarin_level as $mandarin_level_item): ?>
                    <option value="<?php echo $mandarin_level_item['id']; ?>" <?php
                    if ($teacher['mandarin_level_id'] == $mandarin_level_item['id']) {
                        echo " selected";
                    }
                    ?>><?php echo $mandarin_level_item['name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label for="mandarin_level_code" class="col-sm-2 control-label">普通话证编号</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" name="mandarin_level_code"
                   value="<?php echo $teacher['mandarin_level_code'] ?>">
        </div>
    </div>
    <hr>
    <div class="form-group">
        <label for="pc_level_id" class="col-sm-2 control-label">计算机等级证</label>
        <div class="col-sm-10">
            <select class="form-control" name="pc_level_id">
                <?php
                foreach ($pc_level as $item): ?>
                    <option value="<?php echo $item['id']; ?>" <?php
                    if ($teacher['pc_level_id'] == $item['id']) {
                        echo " selected";
                    }
                    ?>><?php echo $item['name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label for="pc_level_code" class="col-sm-2 control-label">计算机证编号</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" name="pc_level_code"
                   value="<?php echo $teacher['pc_level_code'] ?>">
        </div>
    </div>
    <hr>
    <div class="form-group">
        <label for="famous_id" class="col-sm-2 control-label">名师</label>
        <div class="col-sm-10">
            <select class="form-control" name="famous_id">
                <?php
                foreach ($famous_teacher as $item): ?>
                    <option value="<?php echo $item['id']; ?>" <?php
                    if ($teacher['famous_id'] == $item['id']) {
                        echo " selected";
                    }
                    ?>><?php echo $item['name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label for="core_id" class="col-sm-2 control-label">骨干</label>
        <div class="col-sm-10">
            <select class="form-control" name="core_id">
                <?php
                foreach ($core_teacher as $item): ?>
                    <option value="<?php echo $item['id']; ?>" <?php
                    if ($teacher['core_id'] == $item['id']) {
                        echo " selected";
                    }
                    ?>><?php echo $item['name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label for="fromgovernment" class="col-sm-2 control-label">是否在编</label>
        <div class="col-sm-10">
            <div class="radio">
                <label>
                    <input type="radio" name="fromgovernment" id="optionsRadios1" value="0" <?php
                    if ($teacher['fromgovernment'] == 0) {
                        echo " checked";
                    }
                    ?>>否
                </label>
            </div>
            <div class="radio">
                <label>
                    <input type="radio" name="fromgovernment" id="optionsRadios2" value="1" <?php
                    if ($teacher['fromgovernment'] == 1) {
                        echo " checked";
                    }
                    ?>>是
                </label>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label for="organization" class="col-sm-2 control-label">人事关系</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" name="organization" value="<?php echo $teacher['organization'] ?>">
        </div>
    </div>

<hr>
    <div class="form-group">
        <label for="grade" class="col-sm-2 control-label">人员分类
        <i class="fa fa-question-circle-o" id="stafftype_tips"></i>
        </label>
        <div class="col-sm-10">
            <select class="form-control" name="stafftype_id">
                <?php
                foreach ($stafftype as $item): ?>
                    <option value="<?php echo $item['id']; ?>" <?php
                    if ($teacher['stafftype_id'] == $item['id']) {
                        echo " selected";
                    }
                    ?>><?php echo $item['name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>


    <div class="form-group">
        <label for="grade" class="col-sm-2 control-label">部门</label>
        <div class="col-sm-10">
            <select class="form-control" name="department_id">
                <?php
                foreach ($departments as $item): ?>
                    <option value="<?php echo $item['id']; ?>" <?php
                    if ($teacher['department_id'] == $item['id']) {
                        echo " selected";
                    }
                    ?>><?php echo $item['name']; ?></option>
                    <?php
                    foreach ($item['subdepartments'] as $subdepartment_item): ?>
                        <option value="<?php echo $subdepartment_item['id']; ?>" <?php
                        if ($teacher['department_id'] == $subdepartment_item['id']) {
                            echo " selected";
                        }
                        ?>>
                            &nbsp;&nbsp;&nbsp;&nbsp;┗<?php echo $subdepartment_item['name']; ?>
                        </option>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label for="position" class="col-sm-2 control-label">岗位</label>
        <div class="col-sm-10">
            <select class="form-control" name="position_id">
                <?php
                foreach ($position as $position_item): ?>
                    <option value="<?php echo $position_item['id']; ?>" <?php
                    if ($teacher['position_id'] == $position_item['id']) {
                        echo " selected";
                    }
                    ?>><?php echo $position_item['name']; ?></option>
                <?php endforeach; ?>
            </select>
            <!--
            <input type="text" name="position" class="form-control" value="<?php echo $teacher['position'] ?>">
            -->
        </div>
    </div>
    <div class="form-group">
        <label for="grade" class="col-sm-2 control-label">任教年级</label>
        <div class="col-sm-10">
            <select class="form-control" name="grade">
                <?php
                for ($i = 0; $i < count(GRADE_ARR); $i++) {
                    ?>
                    <option value="<?php echo GRADE_ARR[$i]; ?>" <?php
                    if ($teacher['grade'] == GRADE_ARR[$i]) {
                        echo " selected";
                    }
                    ?>>
                        <?php echo CNGRADE_ARR[$i]; ?>
                    </option>
                    <?php
                }
                ?>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label for="subject_id" class="col-sm-2 control-label">任教科目</label>
        <div class="col-sm-10">
            <select class="form-control" name="subject_id">
                <?php
                foreach ($subjects as $subjects_item): ?>
                    <option value="<?php echo $subjects_item['id']; ?>" <?php
                    if ($teacher['subject_id'] == $subjects_item['id']) {
                        echo " selected";
                    }
                    ?>><?php echo $subjects_item['name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label for="phase_id" class="col-sm-2 control-label">学段</label>
        <div class="col-sm-10">
            <select class="form-control" name="phase_id">
                <?php
                foreach ($phases as $phases_item): ?>
                    <option value="<?php echo $phases_item['id']; ?>" <?php
                    if ($teacher['phase_id'] == $phases_item['id']) {
                        echo " selected";
                    }
                    ?>><?php echo $phases_item['name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
<!--
    <div class="form-group">
        <label class="col-sm-2 control-label">是否考勤</label>
        <div class="col-sm-10">
            <label>
                <input type="radio" name="need_attend" value="1" <?php
                if ($teacher['need_attend'] == 1) {
                    echo " checked";
                }
                ?>>是
            </label>
            <label>
                <input type="radio" name="need_attend" value="0" <?php
                if ($teacher['need_attend'] == 0) {
                    echo " checked";
                }
                ?>>否
            </label>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">是否住校</label>
        <div class="col-sm-10">
            <label>
                <input type="radio" name="in_school" value="1" <?php
                if ($teacher['in_school'] == 1) {
                    echo " checked";
                }
                ?>>是
            </label>
            <label>
                <input type="radio" name="in_school" value="0" <?php
                if ($teacher['in_school'] == 0) {
                    echo " checked";
                }
                ?>>否
            </label>
        </div>
    </div>
    -->
    <hr>
    <div class="form-group">
        <label for="education_experience" class="col-sm-2 control-label">教育及培训经历</label>
        <div class="col-sm-10">
            <textarea name="education_experience"
                      class="form-control"><?php echo $teacher['education_experience'] ?></textarea>
        </div>
    </div>
    <div class="form-group">
        <label for="experience" class="col-sm-2 control-label">工作经历</label>
        <div class="col-sm-10">
            <textarea name="experience" class="form-control"><?php echo $teacher['experience'] ?></textarea>
        </div>
    </div>

    <div class="form-group">
        <label for="award" class="col-sm-2 control-label">表彰情况</label>
        <div class="col-sm-10">
            <textarea name="award" class="form-control"><?php echo $teacher['award'] ?></textarea>
        </div>
    </div>
    <div class="form-group">
        <label for="remark" class="col-sm-2 control-label">备注</label>
        <div class="col-sm-10">
            <input type="text" name="remark" class="form-control" value="<?php echo $teacher['remark'] ?>">
        </div>
    </div>
    <div class="form-group">
        <label for="entrydate" class="col-sm-2 control-label">入职日期</label>
        <div class="col-sm-10">
            <input type="text" name="entrydate" class="form-control datepicker"
                   value="<?php echo $teacher['entrydate'] ?>">
        </div>
    </div>
    <div class="form-group">
        <label for="dimissiondate" class="col-sm-2 control-label">离职日期</label>
        <div class="col-sm-10">
            <input type="text" name="dimissiondate" class="form-control datepicker"
                   value="<?php echo $teacher['dimissiondate'] ?>">
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" name="submit" value="submit" class="btn btn-success"
                    onclick="javascript:return verify();">修改
            </button>
        </div>
    </div>
    </form>

</div>
<script>
    $(function () {

        $("#stafftype_tips").hover(function () {
            layer.tips('专任教师：只要上课就算（不含外聘）；<br>行政人员：不上课的管理人员；<br>教辅人员：文印员、考核办、巡课员、校医、会计、图书管理员、实验室管理员；<br>工勤人员：餐厅、生活老师、保安、保洁、水电工等；<br>其他：外聘、外教。', '#stafftype_tips', {
                tips: [1, '#3595CC'],
                time: 4000
            });
        });


        $(".datepicker").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: "yy-mm-dd"
        });

    });


    function verify() {
        var id_json = IdCodeValid($("#identity_number").val());
        if (id_json.pass == true) {
            return true;
        } else {
            layer.alert(id_json.msg);
            return false;
        }
    }

    function IdCodeValid(code) {
        //身份证号合法性验证
        //支持15位和18位身份证号
        //支持地址编码、出生日期、校验位验证
        var city = {
            11: "北京",
            12: "天津",
            13: "河北",
            14: "山西",
            15: "内蒙古",
            21: "辽宁",
            22: "吉林",
            23: "黑龙江 ",
            31: "上海",
            32: "江苏",
            33: "浙江",
            34: "安徽",
            35: "福建",
            36: "江西",
            37: "山东",
            41: "河南",
            42: "湖北 ",
            43: "湖南",
            44: "广东",
            45: "广西",
            46: "海南",
            50: "重庆",
            51: "四川",
            52: "贵州",
            53: "云南",
            54: "西藏 ",
            61: "陕西",
            62: "甘肃",
            63: "青海",
            64: "宁夏",
            65: "新疆",
            71: "台湾",
            81: "香港",
            82: "澳门",
            91: "国外 "
        };
        var row = {
            'pass': true,
            'msg': '验证成功'
        };
        if (!code || !/^\d{6}(18|19|20)?\d{2}(0[1-9]|1[012])(0[1-9]|[12]\d|3[01])\d{3}(\d|[xX])$/.test(code)) {
            row = {
                'pass': false,
                'msg': '身份证号格式错误'
            };
        } else if (!city[code.substr(0, 2)]) {
            row = {
                'pass': false,
                'msg': '身份证号地址编码错误'
            };
        } else {
            //18位身份证需要验证最后一位校验位
            if (code.length == 18) {
                code = code.split('');
                //∑(ai×Wi)(mod 11)
                //加权因子
                var factor = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2];
                //校验位
                var parity = [1, 0, 'X', 9, 8, 7, 6, 5, 4, 3, 2];
                var sum = 0;
                var ai = 0;
                var wi = 0;
                for (var i = 0; i < 17; i++) {
                    ai = code[i];
                    wi = factor[i];
                    sum += ai * wi;
                }
                if (parity[sum % 11] != code[17].toUpperCase()) {
                    row = {
                        'pass': false,
                        'msg': '身份证号校验位错误'
                    };
                }
            }
        }
        return row;
    }
</script>
<style>
    .my_checkbox {
        width: 20px;
        height: 20px;
        position: relative;
        left: -20px;
        top: -50px;
        z-index: 10;
    }
</style>