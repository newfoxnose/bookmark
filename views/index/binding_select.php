<div class="container">
    <h3><?php echo $title; ?>
    </h3>
    <h3>已绑定身份</h3>
    <table class="table">
        <?php
        if ($teacher != null) {
            ?>

            <tr>
                <td>
                    <?php
                    echo $teacher['name'];
                    ?>
                </td>
                <td>
                    教职工
                </td>
                <td>
                    <?php
                    foreach ($departments as $departments_item):
                        if ($teacher['department_id'] == $departments_item['id']) {
                            echo $departments_item['name'];
                            break;
                        }
                    endforeach;
                    ?>
                </td>
                <td>
                    <a href="<?php echo site_url('index/wx_enter/1/' . $teacher['id']); ?>">选择进入</a>
                </td>
            </tr>

            <?php
        }
        ?>
        <?php
        if ($student != null) {
            ?>
                <tr>
                    <td>
                        <?php
                        echo $student['name'];
                        ?>
                    </td>
                    <td>
                        学生家长
                    </td>
                    <td>
                        <?php
                        echo name_from_class($student['grade'], $student['class']);
                        ?>
                    </td>
                    <td>
                        <a href="<?php echo site_url('index/wx_enter/0/' . $student['id']); ?>">选择进入</a>
                    </td>
                </tr>
            <?php
        }
        ?>
    </table>
    <h4>
        <a href="<?php echo site_url('index/wx_binding/') ?>">绑定新身份</a>
    </h4>
</div>
