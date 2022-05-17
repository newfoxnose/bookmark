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
//判断远程文件
function check_remote_file_exists($url)
{
    $curl = curl_init($url);
// 不取回数据
    curl_setopt($curl, CURLOPT_NOBODY, true);
// 不验证https
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
// 发送请求
    $result = curl_exec($curl);
    $found = false;
// 如果请求没有发送失败
    if ($result !== false) {
// 再检查http响应码是否为200
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($statusCode == 200) {
            $found = true;
        }
    }
    curl_close($curl);
    return $found;
}

function checkinput($type, $input, $min, $max)
{
    switch ($type) {
        case '01':
            $regex = "/^[01]?$/";
            break;
        case '012':
            $regex = "/^[012]?$/";
            break;
        case 'int':
            $regex = "/^[0-9\-]+$/";
            break;
        case '09':
            $regex = "/^[0-9]+$/";
            break;
        case 'float':
            $regex = "/^[0-9\.]+$/";
            break;
        case 'date':
            $regex = "/^[0-9]{4}[\-\/][0-9]{1,2}[\-\/][0-9]{1,2}$/";
            break;
        case 'phone':
            $regex = "/^[0-9\+\(\)\-\.\s]+$/";
            break;
        case 'az':
            $regex = "/^[a-z]+$/";
            break;
        case 'str':
            $regex = "/^[a-zA-Z0-9\s]+$/";
            break;
        case 'strext':
            $regex = "/^[a-zA-Z0-9_\-\s\.]+$/";
            break;
        case 'lang':
            $regex = "/^[a-zA-Z0-9\-]+$/";
            break;
        case 'pwd':
            $regex = "/^[a-zA-Z0-9`~!@#\$\^\*\(\)_\-\+=\{\[\}\]:;\|,\.\?\/]+$/";
            break;
        case 'strutf':
            $regex = "/^[^<,\|\/>\"\\\\\'%]+$/";       //匹配\要使用4个\ ,不允许出现/   ,去掉了&，最后面加i表示不区分大小写字母
            break;
        case 'strutfeasy':
            $regex = "/^[^<>\\\\]+$/";       //匹配\要使用4个\ ,不允许出现/   ,去掉了&，最后面加i表示不区分大小写字母
            break;
        case 'clientsecret':
            $regex = "/^[a-zA-Z0-9\/\+\=]+$/";
            break;
        case 'url':
            $regex = "/^http[s]?:\/\/[^<>\"\']+$/i";
            break;
        case 'url2':
            $regex = "/^[^<>\"\']+$/i";
            break;
        case 'email':
            //$regex = "/^(\w)+(\.\w+)*@(\w)+((\.\w+)+)$/i";
            $regex = "/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,5}$/i";
            break;
        case 'sitename':
            $regex = "/^[a-z0-9]+$/";
            break;
        case 'safefile':
            $regex = "/^[A-Za-z0-9\/_\-\(\)\.]+\.(xml|json|css|html|js|htm|jpg|jpeg|png|gif|ttf|otf|woff|woff2|eot|svg){1}$/i";
            break;
        case 'txtfile':
            $regex = "/^[A-Za-z0-9_\-\(\)\.]+\.(xml|json|css|html|js|htm){1}$/i";
            break;
        case 'htmfile':
            $regex = "/^[A-Za-z0-9_\-\(\)\.]+\.(htm|html){1}$/i";
            break;
        case 'fontfile':
            $regex = "/^[A-Za-z0-9_\-\(\)\.]+\.(ttf|otf|woff|woff2|eot|svg){1}$/i";
            break;
        case 'imgfile':
            $regex = "/^[A-Za-z0-9_\-\(\)\.]+\.(ico|jpg|jpeg|png|gif){1}$/i";
            break;
        case 'mp4file':
            $regex = "/^[A-Za-z0-9_\-\(\)\.]+\.(mp4|webm){1}$/i";
            break;
        case 'zipfile':
            $regex = "/^[A-Za-z0-9_\-\(\)\.]+\.(zip|rar){1}$/i";
            break;
        case 'pdffile':
            $regex = "/^[A-Za-z0-9_\-\(\)\.]+\.pdf$/i";
            break;
        case 'folder':
            $regex = "/^[a-zA-Z0-9_\-\/\.\(\)]+$/";          //必须带点，因为有要编辑的文件名
            break;
        case 'filename':
            $regex = "/^[a-zA-Z0-9\-_]+\.[a-zA-Z0-9]+$/";          //仅用于检测单独的文件名
            break;
    }
    if ($input == null || $input == '') {
        return '';
    } else {
        if (!preg_match($regex, $input)) {
            return '';
        } else {
            if (mb_strlen($input) >= $min && mb_strlen($input) <= $max) {
                return $input;
            } else {
                return '';
            }
        }
    }
}

function rand_name()
{
    $tou = array('快乐', '冷静', '醉熏', '潇洒', '糊涂', '积极', '冷酷', '深情', '粗暴', '温柔', '可爱', '愉快', '义气', '认真', '威武', '帅气', '传统', '潇洒', '漂亮', '自然', '专一', '听话', '昏睡', '狂野', '等待', '搞怪', '幽默', '魁梧', '活泼', '开心', '高兴', '超帅', '留胡子', '坦率', '直率', '轻松', '痴情', '完美', '精明', '无聊', '有魅力', '丰富', '繁荣', '饱满', '炙热', '暴躁', '碧蓝', '俊逸', '英勇', '健忘', '故意', '无心', '土豪', '朴实', '兴奋', '幸福', '淡定', '不安', '阔达', '孤独', '独特', '疯狂', '时尚', '落后', '风趣', '忧伤', '大胆', '爱笑', '矮小', '健康', '合适', '玩命', '沉默', '斯文', '香蕉', '苹果', '鲤鱼', '鳗鱼', '任性', '细心', '粗心', '大意', '甜甜', '酷酷', '健壮', '英俊', '霸气', '阳光', '默默', '大力', '孝顺', '忧虑', '着急', '紧张', '善良', '凶狠', '害怕', '重要', '危机', '欢喜', '欣慰', '满意', '跳跃', '诚心', '称心', '如意', '怡然', '娇气', '无奈', '无语', '激动', '愤怒', '美好', '感动', '激情', '激昂', '震动', '虚拟', '超级', '寒冷', '精明', '明理', '犹豫', '忧郁', '寂寞', '奋斗', '勤奋', '现代', '过时', '稳重', '热情', '含蓄', '开放', '无辜', '多情', '纯真', '拉长', '热心', '从容', '体贴', '风中', '曾经', '追寻', '儒雅', '优雅', '开朗', '外向', '内向', '清爽', '文艺', '长情', '平常', '单身', '伶俐', '高大', '懦弱', '柔弱', '爱笑', '乐观', '耍酷', '酷炫', '神勇', '年轻', '唠叨', '瘦瘦', '无情', '包容', '顺心', '畅快', '舒适', '靓丽', '负责', '背后', '简单', '谦让', '彩色', '缥缈', '欢呼', '生动', '复杂', '慈祥', '仁爱', '魔幻', '虚幻', '淡然', '受伤', '雪白', '高高', '糟糕', '顺利', '闪闪', '羞涩', '缓慢', '迅速', '优秀', '聪明', '含糊', '俏皮', '淡淡', '坚强', '平淡', '欣喜', '能干', '灵巧', '友好', '机智', '机灵', '正直', '谨慎', '俭朴', '殷勤', '虚心', '辛勤', '自觉', '无私', '无限', '踏实', '老实', '现实', '可靠', '务实', '拼搏', '个性', '粗犷', '活力', '成就', '勤劳', '单纯', '落寞', '朴素', '悲凉', '忧心', '洁净', '清秀', '自由', '小巧', '单薄', '贪玩', '刻苦', '干净', '壮观', '和谐', '文静', '调皮', '害羞', '安详', '自信', '端庄', '坚定', '美满', '舒心', '温暖', '专注', '勤恳', '美丽', '腼腆', '优美', '甜美', '甜蜜', '整齐', '动人', '典雅', '尊敬', '舒服', '妩媚', '秀丽', '喜悦', '甜美', '彪壮', '强健', '大方', '俊秀', '聪慧', '迷人', '陶醉', '悦耳', '动听', '明亮', '结实', '魁梧', '标致', '清脆', '敏感', '光亮', '大气', '老迟到', '知性', '冷傲', '呆萌', '野性', '隐形', '笑点低', '微笑', '笨笨', '难过', '沉静', '火星上', '失眠', '安静', '纯情', '要减肥', '迷路', '烂漫', '哭泣', '贤惠', '苗条', '温婉', '发嗲', '会撒娇', '贪玩', '执着', '眯眯眼', '花痴', '想人陪', '眼睛大', '高贵', '傲娇', '心灵美', '爱撒娇', '细腻', '天真', '怕黑', '感性', '飘逸', '怕孤独', '忐忑', '高挑', '傻傻', '冷艳', '爱听歌', '还单身', '怕孤单', '懵懂');
    $do = array("的", "爱", "", "与", "给", "扯", "和", "用", "方", "打", "就", "迎", "向", "踢", "笑", "闻", "有", "等于", "保卫", "演变");
    $wei = array('嚓茶', '凉面', '便当', '毛豆', '花生', '可乐', '灯泡', '哈密瓜', '野狼', '背包', '眼神', '缘分', '雪碧', '人生', '牛排', '蚂蚁', '飞鸟', '灰狼', '斑马', '汉堡', '悟空', '巨人', '绿茶', '自行车', '保温杯', '大碗', '墨镜', '魔镜', '煎饼', '月饼', '月亮', '星星', '芝麻', '啤酒', '玫瑰', '大叔', '小伙', '哈密瓜，数据线', '太阳', '树叶', '芹菜', '黄蜂', '蜜粉', '蜜蜂', '信封', '西装', '外套', '裙子', '大象', '猫咪', '母鸡', '路灯', '蓝天', '白云', '星月', '彩虹', '微笑', '摩托', '板栗', '高山', '大地', '大树', '电灯胆', '砖头', '楼房', '水池', '鸡翅', '蜻蜓', '红牛', '咖啡', '机器猫', '枕头', '大船', '诺言', '钢笔', '刺猬', '天空', '飞机', '大炮', '冬天', '洋葱', '春天', '夏天', '秋天', '冬日', '航空', '毛衣', '豌豆', '黑米', '玉米', '眼睛', '老鼠', '白羊', '帅哥', '美女', '季节', '鲜花', '服饰', '裙子', '白开水', '秀发', '大山', '火车', '汽车', '歌曲', '舞蹈', '老师', '导师', '方盒', '大米', '麦片', '水杯', '水壶', '手套', '鞋子', '自行车', '鼠标', '手机', '电脑', '书本', '奇迹', '身影', '香烟', '夕阳', '台灯', '宝贝', '未来', '皮带', '钥匙', '心锁', '故事', '花瓣', '滑板', '画笔', '画板', '学姐', '店员', '电源', '饼干', '宝马', '过客', '大白', '时光', '石头', '钻石', '河马', '犀牛', '西牛', '绿草', '抽屉', '柜子', '往事', '寒风', '路人', '橘子', '耳机', '鸵鸟', '朋友', '苗条', '铅笔', '钢笔', '硬币', '热狗', '大侠', '御姐', '萝莉', '毛巾', '期待', '盼望', '白昼', '黑夜', '大门', '黑裤', '钢铁侠', '哑铃', '板凳', '枫叶', '荷花', '乌龟', '仙人掌', '衬衫', '大神', '草丛', '早晨', '心情', '茉莉', '流沙', '蜗牛', '战斗机', '冥王星', '猎豹', '棒球', '篮球', '乐曲', '电话', '网络', '世界', '中心', '鱼', '鸡', '狗', '老虎', '鸭子', '雨', '羽毛', '翅膀', '外套', '火', '丝袜', '书包', '钢笔', '冷风', '八宝粥', '烤鸡', '大雁', '音响', '招牌', '胡萝卜', '冰棍', '帽子', '菠萝', '蛋挞', '香水', '泥猴桃', '吐司', '溪流', '黄豆', '樱桃', '小鸽子', '小蝴蝶', '爆米花', '花卷', '小鸭子', '小海豚', '日记本', '小熊猫', '小懒猪', '小懒虫', '荔枝', '镜子', '曲奇', '金针菇', '小松鼠', '小虾米', '酒窝', '紫菜', '金鱼', '柚子', '果汁', '百褶裙', '项链', '帆布鞋', '火龙果', '奇异果', '煎蛋', '唇彩', '小土豆', '高跟鞋', '戒指', '雪糕', '睫毛', '铃铛', '手链', '香氛', '红酒', '月光', '酸奶', '银耳汤', '咖啡豆', '小蜜蜂', '小蚂蚁', '蜡烛', '棉花糖', '向日葵', '水蜜桃', '小蝴蝶', '小刺猬', '小丸子', '指甲油', '康乃馨', '糖豆', '薯片', '口红', '超短裙', '乌冬面', '冰淇淋', '棒棒糖', '长颈鹿', '豆芽', '发箍', '发卡', '发夹', '发带', '铃铛', '小馒头', '小笼包', '小甜瓜', '冬瓜', '香菇', '小兔子', '含羞草', '短靴', '睫毛膏', '小蘑菇', '跳跳糖', '小白菜', '草莓', '柠檬', '月饼', '百合', '纸鹤', '小天鹅', '云朵', '芒果', '面包', '海燕', '小猫咪', '龙猫', '唇膏', '鞋垫', '羊', '黑猫', '白猫', '万宝路', '金毛', '山水', '音响', '尊云', '西安');
    $tou_num = rand(0, 331);
    $do_num = rand(0, 19);
    $wei_num = rand(0, 327);
    $type = rand(0, 1);
    if ($type == 0) {
        $username = $tou[$tou_num] . $do[$do_num] . $wei[$wei_num];
    } else {
        $username = $wei[$wei_num] . $tou[$tou_num];
    }
    return $username; //输出生成昵称
}

function rand_str($len)
{
    $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    $string='';
    for(;$len>=1;$len--)
    {
        $string=$string.substr($chars,rand(0,strlen($chars)),1);
    }
    return $string;
}


/**
 * 简单对称加密算法之加密
 * @param String $string 需要加密的字串
 * @param String $skey 加密EKY
 * @author Anyon Zou <zoujingli@qq.com>
 * @date 2013-08-13 19:30
 * @update 2014-10-10 10:10
 * @return String
 */
function encode($string = '', $skey = 'i83jeu')
{
    $strArr = str_split(base64_encode($string));
    $strCount = count($strArr);
    foreach (str_split($skey) as $key => $value)
        $key < $strCount && $strArr[$key] .= $value;
    return str_replace(array('=', '+', '/'), array('O0O0O', 'o000o', 'oo00o'),
        join('', $strArr));
}

/**
 * 简单对称加密算法之解密
 * @param String $string 需要解密的字串
 * @param String $skey 解密KEY
 * @author Anyon Zou <zoujingli@qq.com>
 * @date 2013-08-13 19:30
 * @update 2014-10-10 10:10
 * @return String
 */
function decode($string = '', $skey = 'i83jeu')
{
    $strArr = str_split(str_replace(array('O0O0O', 'o000o', 'oo00o'),
        array('=', '+', '/'), $string), 2);
    $strCount = count($strArr);
    foreach (str_split($skey) as $key => $value)
        $key <= $strCount && isset($strArr[$key]) && $strArr[$key][1] === $value
        && $strArr[$key] = $strArr[$key][0];
    return base64_decode(join('', $strArr));
}

//根据指定键名的值删除数组元素
function delByValue($arr,$key, $value){
    if(!is_array($arr)){
        return $arr;
    }
    foreach($arr as $k=>$v){
        if($v[$key] == $value){
            unset($arr[$k]);
        }
    }
    return array_values($arr);
}

//只保留指定键名的值
function keepByValue($arr,$key, $value){
    if(!is_array($arr)){
        return $arr;
    }
    foreach($arr as $k=>$v){
        if($v[$key] != $value){
            unset($arr[$k]);
        }
    }
    return array_values($arr);
}
?>