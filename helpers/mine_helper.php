<?php
function get_sex_from_id($idcard, $type = 0)      //如果type为1，返回汉字男女，否则返回0或1；
{
    if ($idcard == null || $idcard == '') {
        if ($type == 1) {
            return "未知";
        } else {
            return -1;
        }
    }
    if (substr($idcard, 16, 1) % 2 == 0) {
        if ($type == 1) {
            return "女";
        } else {
            return 0;
        }
    } else {
        if ($type == 1) {
            return "男";
        } else {
            return 1;
        }
    }
}

function get_birthday_from_id($idcard)            //从身份证号码返回出生日期
{
    $birth = strlen($idcard) == 15 ? ('19' . substr($idcard, 6, 6)) : substr($idcard, 6, 8);
    return $birth;
}

function my_rand($len)        //不好用。生成一个随机字符串
{
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
    $string = time();
    for (; $len >= 1; $len--) {
        $position = rand() % strlen($chars);
        $position2 = rand() % strlen($string);
        $string = substr_replace($string, substr($chars, $position, 1), $position2, 0);
    }
    return $string;
}

function random_str($length)
{
    //生成一个包含 大写英文字母, 小写英文字母, 数字 的数组
    $arr = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));
    $str = '';
    $arr_len = count($arr);
    for ($i = 0; $i < $length; $i++) {
        $rand = mt_rand(0, $arr_len - 1);
        $str .= $arr[$rand];
    }
    return $str;
}

/**
 * 检查文件是否存在
 * @param $file_http_path       文件完整路径 带http或者https
 * @return bool
 */
function check_exists($file_http_path)
{
    // 远程文件
    if (strtolower(substr($file_http_path, 0, 4)) == 'http') {

        $header = get_headers($file_http_path, true);

        return isset($header[0]) && (strpos($header[0], '200') || strpos($header[0], '304'));
        // 本地文件
    } else {
        return file_exists('.' . $file_http_path);
    }
}

function format_phone($phone)           //格式化电话号码（3-4-4）
{
    $phone = preg_replace("/[^0-9]/", "", $phone);

    if (strlen($phone) == 7)
        return preg_replace("/([0-9]{3})([0-9]{4})/", "$1 $2", $phone);
    elseif (strlen($phone) == 10)
        return preg_replace("/([0-9]{4})([0-9]{3})([0-9]{3})/", "$1 $2 $3", $phone);
    elseif (strlen($phone) == 11)
        return preg_replace("/([0-9]{3})([0-9]{4})([0-9]{4})/", "$1 $2 $3", $phone);
    else
        return $phone;
}

function format_name($name)           //格式化姓名（双字姓名中间加空格）
{
    if (mb_strlen($name) == 2)
        return mb_substr($name, 0, 1) . "&nbsp;&nbsp;&nbsp;&nbsp;" . mb_substr($name, 1, 1);
    else
        return $name;
}

function vertical_name($name)           //把水平文字转为竖直
{
    $out = '';
    for ($i = 0; $i < (mb_strlen($name) - 1); $i++) {
        $out = $out . mb_substr($name, $i, 1) . "";
    }
    $out = $out . mb_substr($name, (mb_strlen($name) - 1), 1);
    return $out;
}

function id2name_from_array($id, $array)
{
    foreach ($array as $item) {
        if ($item["id"] == $id) {
            return $item["name"];
        }
    }
    return "";
}

function my_mb_str_split($string, $len = 1)   //按长度分割字符串为数组
{
    $start = 0;
    $strlen = mb_strlen($string);
    while ($strlen) {
        $array[] = mb_substr($string, $start, $len, "utf8");
        $string = mb_substr($string, $len, $strlen, "utf8");
        $strlen = mb_strlen($string);
    }
    return $array;
}

function trimall($str)       //删除所有空格
{
    $oldchar = array(" ", "　", "\t", "\n", "\r");
    $newchar = array("", "", "", "", "");
    return str_replace($oldchar, $newchar, $str);
}


function name_from_grade($grade)       //根据年级数字返回中文名
{
    switch ($grade) {
        case "-1":
            $output = "未指定";
            break;
        case "0":
            $output = "学前班";
            break;
        case "1":
            $output = "一年级";
            break;
        case "2":
            $output = "二年级";
            break;
        case "3":
            $output = "三年级";
            break;
        case "4":
            $output = "四年级";
            break;
        case "5":
            $output = "五年级";
            break;
        case "6":
            $output = "六年级";
            break;
        case "7":
            $output = "七年级";
            break;
        case "8":
            $output = "八年级";
            break;
        case "9":
            $output = "九年级";
            break;
        default:
            $output = "";
            break;
    }
    return $output;
}


function name_from_class($grade, $class)       //根据年级和班级返回中文名
{
    if ($grade == "-1") {
        if ($class == "0") {
            $output = "未指定年级与班级";
        }
        if ($class != "0") {
            $output = "未指定年级," . $class . "班";
        }
    } elseif ($grade == "-2") {    //只返回班名
        switch ($class) {
            case 0:
                $output = "未指定";
                break;
            default:
                $output = $class;
                break;
        }
        $output = $output . "班";
    } else {
        if ($class == "0") {
            switch ($grade) {
                case 0:
                    $output = "学前班";
                    break;
                case 1:
                    $output = "一年级";
                    break;
                case 2:
                    $output = "二年级";
                    break;
                case 3:
                    $output = "三年级";
                    break;
                case 4:
                    $output = "四年级";
                    break;
                case 5:
                    $output = "五年级";
                    break;
                case 6:
                    $output = "六年级";
                    break;
                case 7:
                    $output = "七年级";
                    break;
                case 8:
                    $output = "八年级";
                    break;
                case 9:
                    $output = "九年级";
                    break;
            }
            $output = $output . ",未指定班级";
        } else {
            switch ($grade) {
                case 0:
                    $output = "学";
                    break;
                case 1:
                    $output = "一";
                    break;
                case 2:
                    $output = "二";
                    break;
                case 3:
                    $output = "三";
                    break;
                case 4:
                    $output = "四";
                    break;
                case 5:
                    $output = "五";
                    break;
                case 6:
                    $output = "六";
                    break;
                case 7:
                    $output = "七";
                    break;
                case 8:
                    $output = "八";
                    break;
                case 9:
                    $output = "九";
                    break;
            }
            $output = $output . "（" . $class . "）班";
        }

    }
    return $output;
}

function enrolled_name($enrolled)
{
    switch ($enrolled) {
        case 0:
            $output = "已报名未录取";
            break;
        case 1:
            $output = "已毕业";
            break;
        case 2:
            $output = "已录取未缴费";
            break;
        case 3:
            $output = "退学";
            break;
        case 4:
            $output = "在校";
            break;
        case 5:
            $output = "已缴费未入学";
            break;
    }
    return $output;
}


function class_from_name($str, $type)
{      //根据中文班级（格式“一（1）班”）返回年级和班级数字
    $arr1 = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9);
    $arr2 = array("学", "一", "二", "三", "四", "五", "六", "七", "八", "九");
    $grade = mb_substr($str, 0, 1);
    $class = mb_substr($str, 2, 1);
    if ($type == "grade") {
        return $arr1[array_search($grade, $arr2)];
    } else {
        return $class;
    }
}


function room_from_exam_number($exam_number)
{     //根据考号返回考场号
    $arr1 = array('11', '12', '13', '14', '21', '22', '31', '32', '33', '41', '42', '43', '51', '52', '53', '61', '62', '63', '71', '72', '73', '81', '82');
    $arr2 = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23');
    return $arr2[array_search(substr($exam_number, 0, 2), $arr1)];
}

/*
* 根据身份证号码获取年龄
* inupt   $code = 完整的身份证号
* return  $age : 年龄
*/
function ageVerification($code)
{
    $age_time = strtotime(substr($code, 6, 8));
    if ($age_time === false) {
        return "未知";
    }
    list($y1, $m1, $d1) = explode("-", date("Y-m-d", $age_time));

    $now_time = strtotime("now");

    list($y2, $m2, $d2) = explode("-", date("Y-m-d", $now_time));
    $age = $y2 - $y1;
    if ((int)($m2 . $d2) < (int)($m1 . $d1)) {
        $age -= 1;
    }
    return $age;
}

function subject_name($str, $type)
{     //如果type为english，根据中文返回英文；如果type为chinese，根据英文返回中文
    $arr1 = array("chinese", "math", "english", "physics", "science", "moral", "politics", "history", "geography", "biology", "politics_history");
    $arr2 = array("语文", "数学", "英语", "物理", "科学", "品社", "政治", "历史", "地理", "生物", "政治历史");
    if ($type == "english") {
        return $arr1[array_search($str, $arr2)];
    } else {
        return $arr2[array_search($str, $arr1)];
    }
}


/**
 * @desc加密
 * @param string $str 待加密字符串
 * @param string $key 密钥
 * @return string
 */
function encrypt($str, $key)
{
    $mixStr = md5(date('Y-m-d H:i:s') . rand(0, 1000));
    $tmp = '';
    $strLen = strlen($str);
    for ($i = 0, $j = 0; $i < $strLen; $i++, $j++) {
        $j = $j == 32 ? 0 : $j;
        $tmp .= $mixStr[$j] . ($str[$i] ^ $mixStr[$j]);
    }
    return substr(base64_encode(bind_key($tmp, $key)), 0, 11);
}

/**
 * @desc解密
 * @param string $str 待解密字符串
 * @param string $key 密钥
 * @return string
 */
function decrypt($str, $key)
{
    $str = bind_key(base64_decode($str), $key);
    $strLen = strlen($str);
    $tmp = '';
    for ($i = 0; $i < $strLen; $i++) {
        $tmp .= $str[$i] ^ $str[++$i];
    }
    return $tmp;
}

/**
 * @desc辅助方法 用密钥对随机化操作后的字符串进行处理
 * @param $str
 * @param $key
 * @return string
 */
function bind_key($str, $key)
{
    $encrypt_key = md5($key);

    $tmp = '';
    $strLen = strlen($str);
    for ($i = 0, $j = 0; $i < $strLen; $i++, $j++) {
        $j = $j == 32 ? 0 : $j;
        $tmp .= $str[$i] ^ $encrypt_key[$j];
    }
    return $tmp;
}

function student_upload_path($student_id, $dir1, $dir2, $switch)
{              //获取学生上传目录，如果switch为true时新建dir2，否则不新建
    $firstchar = mb_substr($student_id, 0, 1, 'utf-8');
    $lastchar = mb_substr($student_id, -1, 1, 'utf-8');
    if (!is_dir('./students_uploads')) {
        mkdir('./students_uploads', 0755);
    }
    if (!is_dir('./students_uploads/' . $firstchar)) {
        mkdir('./students_uploads/' . $firstchar, 0755);
    }
    if (!is_dir('./students_uploads/' . $firstchar . '/' . $lastchar)) {
        mkdir('./students_uploads/' . $firstchar . '/' . $lastchar, 0755);
    }
    if (!is_dir('./students_uploads/' . $firstchar . '/' . $lastchar . '/' . $student_id)) {
        mkdir('./students_uploads/' . $firstchar . '/' . $lastchar . '/' . $student_id, 0755);
    }
    if (!is_dir('./students_uploads/' . $firstchar . '/' . $lastchar . '/' . $student_id . '/' . $dir1)) {
        mkdir('./students_uploads/' . $firstchar . '/' . $lastchar . '/' . $student_id . '/' . $dir1, 0755);
    }
    if ($switch == true) {
        if (!is_dir('./students_uploads/' . $firstchar . '/' . $lastchar . '/' . $student_id . '/' . $dir1 . '/' . $dir2)) {
            mkdir('./students_uploads/' . $firstchar . '/' . $lastchar . '/' . $student_id . '/' . $dir1 . '/' . $dir2, 0755);
        }
    }
    $path = './students_uploads/' . $firstchar . '/' . $lastchar . '/' . $student_id . '/' . $dir1 . '/' . $dir2 . '/';
    return $path;
}

function local_upload_path($dir1, $dir2, $dir3 = null, $dir4 = null)
{     //获取上传目录，没有就创建目录
    if (!is_dir('./' . $dir1)) {
        mkdir('./' . $dir1, 0755);
    }
    if (!is_dir('./' . $dir1 . '/' . $dir2)) {
        mkdir('./' . $dir1 . '/' . $dir2, 0755);
    }
    if ($dir3 == null && $dir4 == null) {
        $path = './' . $dir1 . '/' . $dir2 . '/';
    } elseif ($dir3 != null && $dir4 == null) {
        if (!is_dir('./' . $dir1 . '/' . $dir2 . '/' . $dir3)) {
            mkdir('./' . $dir1 . '/' . $dir2 . '/' . $dir3, 0755);
        }
        $path = './' . $dir1 . '/' . $dir2 . '/' . $dir3 . '/';
    } else {
        if (!is_dir('./' . $dir1 . '/' . $dir2 . '/' . $dir3 . '/' . $dir4)) {
            mkdir('./' . $dir1 . '/' . $dir2 . '/' . $dir3 . '/' . $dir4, 0755);
        }
        $path = './' . $dir1 . '/' . $dir2 . '/' . $dir3 . '/' . $dir4 . '/';
    }
    return $path;
}

function qiniu_upload_path($dir1, $dir2, $dir3 = null, $dir4 = null)
{     //返回七牛上传目录路径
    if ($dir3 != null && $dir4 == null) {
        $path = $dir1 . '/' . $dir2 . '/' . $dir3 . '/';
    } elseif ($dir3 == null && $dir4 == null) {
        $path = $dir1 . '/' . $dir2 . '/';
    } else {
        $path = $dir1 . '/' . $dir2 . '/' . $dir3 . '/' . $dir4 . '/';
    }
    return $path;
}

function get_extension($file)
{   //返回文件扩展名
    return pathinfo($file, PATHINFO_EXTENSION);
}


function find_in_arr($arr, $in_key, $in_value, $out_key)
{            //需要4个参数，关联数组，关联数组的查询键，关联数组的查询值，关联数组的返回键
    $out_value = null;
    for ($i = 0; $i < count($arr); $i++) {
        if ($arr[$i][$in_key] == $in_value) {
            $out_value = $arr[$i][$out_key];
        }
    }
    return $out_value;
}


function arr_from_query($query_arr, $column)
{
    foreach ($query_arr as $row) {
        $result[] = $row[$column];
    }
    return $result;
}


function deal_long_content($content)
{    //长字符串截取部分并显示为链接
    if (mb_strlen($content, "utf-8") > 8) {
        return "<a href=### onclick='javascript:layer.alert(\"" . $content . "\");'>" . mb_substr($content, 0, 8, "utf-8") . "...</a>";
    } else {
        return $content;
    }
}

function myScanDir($dir)
{         //获取目录的文件，不包含子目录
    $new_arr = array();
    if (is_dir($dir)) {
        $file_arr = scandir($dir);
        foreach ($file_arr as $item) {
            if ($item != ".." && $item != ".") {
                if (is_dir($dir . "/" . $item)) {
                    //$new_arr[$item] = myScanDir($dir."/".$item);
                } else {
                    $new_arr[] = $item;
                }
            }
        }
    }
    return $new_arr;
}

function myScanDir2($dir)
{         //获取目录的文件，包含子目录
    $new_arr = array();
    if (is_dir($dir)) {
        $file_arr = scandir($dir);
        foreach ($file_arr as $item) {
            if ($item != ".." && $item != ".") {
                if (is_dir($dir . "/" . $item)) {
                    $new_arr[$item] = myScanDir($dir . "/" . $item);
                } else {
                    $new_arr[] = $item;
                }
            }
        }
    }
    return $new_arr;
}

function deldir($dir)
{        //完全删除一个目录
    //先删除目录下的文件：
    $dh = opendir($dir);
    while ($file = readdir($dh)) {
        if ($file != "." && $file != "..") {
            $fullpath = $dir . "/" . $file;
            if (!is_dir($fullpath)) {
                unlink($fullpath);
            } else {
                deldir($fullpath);
            }
        }
    }
    closedir($dh);
    //删除当前文件夹：
    if (rmdir($dir)) {
        return true;
    } else {
        return false;
    }
}

function accounter_number($number)
{
    $a2 = array('零', '壹', '贰', '叁', '肆', '伍', '陆', '柒', '捌', '玖');
    $number = str_replace(".", "", $number);
    $out = "<span>⊗</span>";
    for ($i = 0; $i < strlen($number); $i++) {
        $out = $out . "<span>" . $a2[intval(substr($number, $i, 1))] . "</span>";
    }
    return $out;
}

function float_amount($amount)
{
    return number_format($amount, 2, ".", "");
}

/**
 * 将数值金额转换为中文大写金额
 * @param $amount float 金额(支持到分)
 * @param $type   int   补整类型,0:到角补整;1:到元补整
 * @return mixed 中文大写金额
 */
function convertAmountToCn($amount, $type = 1)
{
    // 判断输出的金额是否为数字或数字字符串
    if (!is_numeric($amount)) {
        return "要转换的金额只能为数字!";
    }
    // 金额为0,则直接输出"零元整"
    if ($amount == 0) {
        return "零元整";
    }
    // 金额不能为负数
    if ($amount < 0) {
        return "要转换的金额不能为负数!";
    }
    // 金额不能超过万亿,即12位
    if (strlen($amount) > 12) {
        return "要转换的金额不能为万亿及更高金额!";
    }
    // 预定义中文转换的数组
    $digital = array('零', '壹', '贰', '叁', '肆', '伍', '陆', '柒', '捌', '玖');
    // 预定义单位转换的数组
    $position = array('仟', '佰', '拾', '亿', '仟', '佰', '拾', '万', '仟', '佰', '拾', '元');
    // 将金额的数值字符串拆分成数组
    $amountArr = explode('.', $amount);
    // 将整数位的数值字符串拆分成数组
    $integerArr = str_split($amountArr[0], 1);
    // 将整数部分替换成大写汉字
    $result = '';//前缀
    $integerArrLength = count($integerArr);     // 整数位数组的长度
    $positionLength = count($position);         // 单位数组的长度
    for ($i = 0; $i < $integerArrLength; $i++) {
        // 如果数值不为0,则正常转换
        if ($integerArr[$i] != 0) {
            $result = $result . $digital[$integerArr[$i]] . $position[$positionLength - $integerArrLength + $i];
        } else {
            // 如果数值为0, 且单位是亿,万,元这三个的时候,则直接显示单位
            if (($positionLength - $integerArrLength + $i + 1) % 4 == 0) {
                $result = $result . $position[$positionLength - $integerArrLength + $i];
            }
        }
    }
    // 如果小数位也要转换
    if ($type == 0) {
        // 将小数位的数值字符串拆分成数组
        $decimalArr = str_split($amountArr[1], 1);
        // 将角替换成大写汉字. 如果为0,则不替换
        if ($decimalArr[0] != 0) {
            $result = $result . $digital[$decimalArr[0]] . '角';
        }
        // 将分替换成大写汉字. 如果为0,则不替换
        if ($decimalArr[1] != 0) {
            $result = $result . $digital[$decimalArr[1]] . '分';
        }
    } else {
        $result = $result . '整';
    }
    return $result;
}

function array_in_array($needle, $arr)
{
    $return = false;
    for ($i = 0; $i < count($needle); $i++) {
        if (in_array($needle[$i], $arr)) {
            $return = true;
            break;
        }
    }
    return $return;
}


function err_msg($str)
{
    return '<div class="alert alert-danger navbar-fixed-top alert-dismissable"><button type="button" class="close" data-dismiss="alert"
                    aria-hidden="true">
                &times;
            </button>' . $str . '</div>';
}

function match_chinese($chars, $encoding = 'utf8')
{
    $pattern = ($encoding == 'utf8') ? '/[\x{4e00}-\x{9fa5}a-zA-Z0-9]/u' : '/[\x80-\xFF]/';
    preg_match_all($pattern, $chars, $result);
    $temp = join('', $result[0]);
    return $temp;
}

function require_authority($array, $authority, $self_id = null)
{
    if ($array != null && $authority != null) {
        for ($i = 0; $i < count($array); $i++) {
            if (in_array($array[$i], $authority)) {
                return true;
            }
        }
    }
    if ($self_id != null) {
        if ($self_id == $_SESSION['teacher_id']) {
            return true;
        }
    }
    show_error("错误：没有权限", "", $heading = 'An Error Was Encountered');
}

function require_self($id)
{
    if ($id == $_SESSION['teacher_id']) {
        return true;
    }
    show_error("错误：没有权限", "", $heading = 'An Error Was Encountered');
}

function my_in_array($needle, $arr)
{
    for ($i = 0; $i < count($arr); $i++) {
        if ((string)$needle == (string)$arr[$i]) {
            return true;
        }
    }
    return false;
}

function thumb_img($filename)      //不包括webp和gif，因为无法生成缩略图
{       //判断是否图片文件
    $arr = array("jpg", "png", "jpeg", "bmp");
    if (in_array(strtolower(get_extension($filename)), $arr)) {
        return true;
    }
    return false;
}


function is_img($filename)
{       //判断是否图片文件
    $arr = array("jpg", "png", "gif", "jpeg", "bmp", "webp");
    if (in_array(strtolower(get_extension($filename)), $arr)) {
        return true;
    }
    return false;
}

function is_office($filename)
{       //判断是否office系列文件
    $arr = array("doc", "docx", "xls", "xlsx", "ppt", "pptx");
    if (in_array(strtolower(get_extension($filename)), $arr)) {
        return true;
    }
    return false;
}


function show_fileicon($filename)
{
    $arr = explode('.', $filename);
    $ext = end($arr);
    switch ($ext) {
        case "pdf":
            return '<img class="filetype" src="' . site_url('resource/iconfont/pdf.png') . '">';
            break;
        case "xls":
        case "xlsx":
            return '<img class="filetype" src="' . site_url('resource/iconfont/xls.png') . '">';
            break;
        case "doc":
        case "docx":
            return '<img class="filetype" src="' . site_url('resource/iconfont/doc.png') . '">';
            break;
        case "ppt":
        case "pptx":
            return '<img class="filetype" src="' . site_url('resource/iconfont/ppt.png') . '">';
            break;
        case "7z":
            return '<img class="filetype" src="' . site_url('resource/iconfont/7z.png') . '">';
            break;
        case "rar":
            return '<img class="filetype" src="' . site_url('resource/iconfont/rar.png') . '">';
            break;
        case "zip":
            return '<img class="filetype" src="' . site_url('resource/iconfont/zip.png') . '">';
            break;
        case "txt":
            return '<img class="filetype" src="' . site_url('resource/iconfont/txt.png') . '">';
            break;
        case "mp4":
            return '<img class="filetype" src="' . site_url('resource/iconfont/mp4.png') . '">';
            break;
        case "mp3":
            return '<img class="filetype" src="' . site_url('resource/iconfont/mp3.png') . '">';
            break;
        default:
            return "";
            break;
    }
}

//生成8位不重复字符串
function randString()
{
    $code = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $rand = $code[rand(0, 25)]
        . strtoupper(dechex(date('m')))
        . date('d') . substr(time(), -5)
        . substr(microtime(), 2, 5)
        . sprintf('%02d', rand(0, 99));
    for (
        $a = md5($rand, true),
        $s = '0123456789ABCDEFGHIJKLMNOPQRSTUV',
        $d = '',
        $f = 0;
        $f < 8;
        $g = ord($a[$f]),
        $d .= $s[($g ^ ord($a[$f + 8])) - $g & 0x1F],
        $f++
    ) ;
    return $d;
}


function post($url, $post_data = '', $timeout = 5)
{//curl
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    if ($post_data != '') {
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_HEADER, false);
    $file_contents = curl_exec($ch);
    curl_close($ch);
    return $file_contents;
}

function get($url)
{//curl
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_HEADER, false);
    $file_contents = curl_exec($ch);
    curl_close($ch);
    return $file_contents;
}


/**
 * 格式化文件大小显示
 *
 * @param int $size
 * @return string
 */
function format_size($size)
{
    $prec = 3;
    $size = round(abs($size));
    $units = array(
        0 => " B ",
        1 => " KB",
        2 => " MB",
        3 => " GB",
        4 => " TB"
    );
    if ($size == 0) {
        return str_repeat(" ", $prec) . "0$units[0]";
    }
    $unit = min(4, floor(log($size) / log(2) / 10));
    $size = $size * pow(2, -10 * $unit);
    $digi = $prec - 1 - floor(log($size) / log(10));
    $size = round($size * pow(10, $digi)) * pow(10, -$digi);
    return $size . $units[$unit];
}


function chkCode($string)
{
    $code = array(
        'ASCII',
        'GBK',
        'GB2312',
        'UTF-8'
    );
    foreach ($code as $c) {
        if ($string === iconv('UTF-8', $c, iconv($c, 'UTF-8', $string))) {
            return $c;
        }
    }
    return null;
}

function format_class($grade, $class)
{
    $out = $grade . $class;
    if (strlen($out) == 2) {
        $out = $grade . "0" . $class;
    }
    return $out;
}

function real_root_folder()
{
    return dirname(dirname(dirname(__FILE__)));
}


function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct)
{       //为图片添加保留透明背景的alpha值得水印
    // creating a cut resource
    $cut = imagecreatetruecolor($src_w, $src_h);

    // copying relevant section from background to the cut resource
    imagecopy($cut, $dst_im, 0, 0, $dst_x, $dst_y, $src_w, $src_h);

    // copying relevant section from watermark to the cut resource
    imagecopy($cut, $src_im, 0, 0, $src_x, $src_y, $src_w, $src_h);

    // insert cut resource to destination image
    imagecopymerge($dst_im, $cut, $dst_x, $dst_y, 0, 0, $src_w, $src_h, $pct);
}


function closetags($html)
{
    // strip fraction of open or close tag from end (e.g. if we take first x characters, we might cut off a tag at the end!)
    $html = preg_replace('/<[^>]*$/', '', $html); // ending with fraction of open tag
    // put open tags into an array
    preg_match_all('#<([a-z]+)(?: .*)?(?<![/|/ ])>#iU', $html, $result);
    $opentags = $result[1];
    // put all closed tags into an array
    preg_match_all('#</([a-z]+)>#iU', $html, $result);
    $closetags = $result[1];
    $len_opened = count($opentags);
    // if all tags are closed, we can return
    if (count($closetags) == $len_opened) {
        return $html;
    }
    // close tags in reverse order that they were opened
    $opentags = array_reverse($opentags);
    // self closing tags
    $sc = array('br', 'input', 'img', 'hr', 'meta', 'link');
    // ,'frame','iframe','param','area','base','basefont','col'
    // should not skip tags that can have content inside!
    for ($i = 0; $i < $len_opened; $i++) {
        $ot = strtolower($opentags[$i]);
        if (!in_array($opentags[$i], $closetags) && !in_array($ot, $sc)) {
            $html .= '</' . $opentags[$i] . '>';
        } else {
            unset($closetags[array_search($opentags[$i], $closetags)]);
        }
    }
    return $html;
}

function is_assoc($arr)
{    //判断是否关联数组
    if (!is_array($arr)) {
        return false;
    } else {
        return array_keys($arr) !== range(0, count($arr) - 1);
    }
}

function is_bookmark_folder($arr)
{
    $result = false;
    if (is_array($arr)) {
        foreach ($arr as $key1 => $value1) {
            if (is_array($value1)) {
                foreach ($value1 as $key2 => $value2) {
                    if (is_array($value2)) {
                        $result = true;
                        break 2;
                    }
                }
            }
        }
    }
    return $result;
}

/*
 各浏览器的根级目录名
 edge-收藏夹栏
chrome-书签栏
firefox-书签工具栏
ie-links

 */
?>