<div class="container">
    <?php
    echo $calendar;
    ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">
                添加行事历
            </h3>
        </div>
        <div class="panel-body">
            <?php echo form_open('user/add_personal_event/', array('class' => 'form-horizontal', 'role' => 'form')); ?>
            <table class="table table-striped">
                <tr>
                    <th>日期</th>
                    <td><input type="date" name="date" class="form-control"></td>
                </tr>
                <tr>
                    <th>内容</th>
                    <td><textarea class="form-control" name="content"></textarea></td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <button type="submit" name="submit" value="addnew" class="btn btn-success">提交</button>
                    </td>
                </tr>
            </table>
            </form>
        </div>
    </div>
</div>
<script>
    function popup(str){
        layer.open({
            type: 1,
            title: false,
            closeBtn: 0,
            shadeClose: true,
            skin: '',
            content: str
        });
    }

</script>