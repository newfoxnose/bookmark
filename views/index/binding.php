<div class="container">
    <h3><?php echo $title; ?>
    </h3>
    <?php echo validation_errors(); ?>

    <?php echo form_open('index/wx_binding', array('class' => 'form-horizontal', 'role' => 'form')); ?>
    <div class="form-group">
        <label class="col-sm-2 control-label">绑定类型</label>
        <div class="col-sm-10">
            <select class="form-control" id="type_select" name="type_select">
                <option>请选择</option>
                <option value="0">学生家长</option>
                <option value="1">本校教职工</option>
            </select>
        </div>
    </div>


    <div class="form-group type-1">
        <label for="name" class="col-sm-2 control-label"><span class="type-0">学生</span>姓名</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" name="name" placeholder="请输入姓名">
        </div>
    </div>


    <div class="form-group type-1">
        <label for="phone" class="col-sm-2 control-label">预留手机号码</label>
        <div class="col-sm-10">
            <input type="text" name="phone" class="form-control">
        </div>
    </div>

    <div class="form-group type-1">
        <label for="identity_number" class="col-sm-2 control-label">身份证号码</label>
        <div class="col-sm-10">
            <input type="text" name="identity_number" class="form-control" id="identity_number" required="required">
        </div>
    </div>


    <div class="form-group type-1">
        <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" name="submit" value="submit" class="btn btn-success">提交</button>
        </div>
    </div>
    </form>
</div>

<script>
    $(document).ready(function () {
        $(".type-0").hide();
        $(".type-1").hide();
        $("#type_select").change(function () {
            var select = $(this).val();
            if (select == 1) {
                $(".type-0").hide();
                $(".type-1").show();
            }
            else if (select == 0) {
                $(".type-0").show();
                $(".type-1").show();
            }
            else {
                $(".type-0").hide();
                $(".type-1").hide();
            }
        });
    });


    function verify() {
        var id_json=IdCodeValid($("#identity_number").val());
        if (id_json.pass==true){
            if (parseInt($("#identity_number").val().substr(16, 1)) % 2 == $('input:radio[name=sex]:checked').val()){
                return true;
            }
            else{
                alert('身份证号码与性别选择不符');
                return false;
            }
        }else{
            alert(id_json.msg);
            return false;
        }
    }

    function IdCodeValid(code){
        //身份证号合法性验证
        //支持15位和18位身份证号
        //支持地址编码、出生日期、校验位验证
        var city={11:"北京",12:"天津",13:"河北",14:"山西",15:"内蒙古",21:"辽宁",22:"吉林",23:"黑龙江 ",31:"上海",32:"江苏",33:"浙江",34:"安徽",35:"福建",36:"江西",37:"山东",41:"河南",42:"湖北 ",43:"湖南",44:"广东",45:"广西",46:"海南",50:"重庆",51:"四川",52:"贵州",53:"云南",54:"西藏 ",61:"陕西",62:"甘肃",63:"青海",64:"宁夏",65:"新疆",71:"台湾",81:"香港",82:"澳门",91:"国外 "};
        var row={
            'pass':true,
            'msg':'验证成功'
        };
        if(!code || !/^\d{6}(18|19|20)?\d{2}(0[1-9]|1[012])(0[1-9]|[12]\d|3[01])\d{3}(\d|[xX])$/.test(code)){
            row={
                'pass':false,
                'msg':'身份证号格式错误'
            };
        }else if(!city[code.substr(0,2)]){
            row={
                'pass':false,
                'msg':'身份证号地址编码错误'
            };
        }else{
            //18位身份证需要验证最后一位校验位
            if(code.length == 18){
                code = code.split('');
                //∑(ai×Wi)(mod 11)
                //加权因子
                var factor = [ 7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2 ];
                //校验位
                var parity = [ 1, 0, 'X', 9, 8, 7, 6, 5, 4, 3, 2 ];
                var sum = 0;
                var ai = 0;
                var wi = 0;
                for (var i = 0; i < 17; i++)
                {
                    ai = code[i];
                    wi = factor[i];
                    sum += ai * wi;
                }
                if(parity[sum % 11] != code[17].toUpperCase()){
                    row={
                        'pass':false,
                        'msg':'身份证号校验位错误'
                    };
                }
            }
        }
        return row;
    }
</script>
