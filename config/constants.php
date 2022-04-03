<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
defined('FILE_READ_MODE') OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE') OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE') OR define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ') OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE') OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE') OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE') OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE') OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE') OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT') OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT') OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS') OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR') OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG') OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE') OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS') OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT') OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE') OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN') OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX') OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code


//----------------自定义常量----------
defined('QINIU_DOMAIN') OR define('QINIU_DOMAIN', "http://xingzhi-pic.sleda.com/");
defined('QINIU_ACCESSKEY') OR define('QINIU_ACCESSKEY', "JHz7w5uupdxNkpL0qtA8UBIxbAKy7IfzO3eKU9Lv");
defined('QINIU_SECRETKEY') OR define('QINIU_SECRETKEY', "9iqfz3uvzXCjneV0gUJNXkHCB7NkzxvuYhyFjijU");
defined('QINIU_BUCKET') OR define('QINIU_BUCKET', "xingzhi-files");

defined('GRADE_ARR') OR define('GRADE_ARR', array(1, 2, 3, 4, 5, 6, 7, 8, 9));
defined('CNGRADE_ARR') OR define('CNGRADE_ARR', array('一年级', '二年级', '三年级', '四年级', '五年级', '六年级', '七年级', '八年级', '九年级'));
defined('CLASS_ARR') OR define('CLASS_ARR', array(0, 1, 2, 3, 4, 5, 6, 7, 8,9,10,11));
defined('CNCLASS_ARR') OR define('CNCLASS_ARR', array('未指定', '1班', '2班', '3班', '4班', '5班', '6班', '7班', '8班', '9班', '10班', '11班'));

defined('CLASS_STUDENT_COLUMN_ARR')  OR define('CLASS_STUDENT_COLUMN_ARR', array(array("Field" => "name", "Comment" => "姓名"),
    array("Field" => "identity_number", "Comment" => "身份证号码"),
    array("Field" => "sex", "Comment" => "性别"),
    array("Field" => "identity_age", "Comment" => "年龄"),
    array("Field" => "student_number", "Comment" => "学号"),
    array("Field" => "county_id", "Comment" => "乡镇"),
    array("Field" => "address", "Comment" => "家庭住址"),
    //array("Field" => "parent_occupation_id", "Comment" => "家长职业"),
    array("Field" => "previous_school", "Comment" => "原就读学校"),
    //array("Field" => "student_code", "Comment" => "学籍号"),
    array("Field" => "semester_id", "Comment" => "入校学期"),
    array("Field" => "nationality_id", "Comment" => "国籍"),
    array("Field" => "ethnicity_id", "Comment" => "民族"),
    array("Field" => "migrant_child_id", "Comment" => "随迁子女"),
    array("Field" => "is_left_behind", "Comment" => "留守儿童"),
    array("Field" => "dress_size_id", "Comment" => "校服尺码"),
    array("Field" => "parent_name", "Comment" => "监护人1姓名"),
    array("Field" => "guardian_1_relation", "Comment" => "监护人1关系"),
    array("Field" => "parent_phone", "Comment" => "监护人1电话"),
    array("Field" => "guardian_1_id", "Comment" => "监护人1身份证号码"),
    array("Field" => "guardian_2_name", "Comment" => "监护人2姓名"),
    array("Field" => "guardian_2_relation", "Comment" => "监护人2关系"),
    array("Field" => "guardian_2_phone", "Comment" => "监护人2电话"),
    array("Field" => "guardian_2_id", "Comment" => "监护人2身份证号码"),
    array("Field" => "vaccine_1_date", "Comment" => "第1针时间"),
    array("Field" => "vaccine_1_manufacture", "Comment" => "第1针品种"),
    array("Field" => "vaccine_1_hospital", "Comment" => "第1针医院"),
    array("Field" => "vaccine_2_date", "Comment" => "第2针时间"),
    array("Field" => "vaccine_2_manufacture", "Comment" => "第2针品种"),
    array("Field" => "vaccine_2_hospital", "Comment" => "第2针医院"),
    array("Field" => "vaccine_3_date", "Comment" => "第3针时间"),
    array("Field" => "vaccine_3_manufacture", "Comment" => "第3针品种"),
    array("Field" => "vaccine_3_hospital", "Comment" => "第3针医院"),
));


defined('SUBJECT_SCORE_ARR') OR define('SUBJECT_SCORE_ARR', array(10, 20, 30, 40, 50, 60, 80, 100,120,150));
//$column_arr = array(array("Field" => "name", "Comment" => "姓名"), array("Field" => "identity_number", "Comment" => "身份证号码"), array("Field" => "sex", "Comment" => "性别"), array("Field" => "identity_age", "Comment" => "年龄"), array("Field" => "createtime", "Comment" => "创建时间"), array("Field" => "grade", "Comment" => "年级"), array("Field" => "class", "Comment" => "班级"), array("Field" => "county_id", "Comment" => "乡镇"), array("Field" => "address", "Comment" => "家庭住址"), array("Field" => "parent_name", "Comment" => "家长姓名"), array("Field" => "parent_occupation_id", "Comment" => "家长职业"), array("Field" => "parent_phone", "Comment" => "家长电话"), array("Field" => "admission_teacher", "Comment" => "联系老师"), array("Field" => "previous_school", "Comment" => "原就读学校"), array("Field" => "advertisement_id", "Comment" => "了解渠道"), array("Field" => "ethnicity_id", "Comment" => "民族"), array("Field" => "native_place", "Comment" => "籍贯"), array("Field" => "student_code", "Comment" => "学籍号"), array("Field" => "exam_number", "Comment" => "学号"), array("Field" => "is_teacher_child", "Comment" => "教工子女"), array("Field" => "is_teacher_relative", "Comment" => "教工亲戚"), array("Field" => "is_left_behind", "Comment" => "留守儿童"), array("Field" => "is_temporary", "Comment" => "借读"), array("Field" => "enrolled", "Comment" => "类型"), array("Field" => "taking_bus", "Comment" => "是否乘坐校车"), array("Field" => "pick_up_person", "Comment" => "接送人"), array("Field" => "pick_up_phone", "Comment" => "接送人电话"), array("Field" => "pick_up_location", "Comment" => "上下车地点"), array("Field" => "room", "Comment" => "寝室"), array("Field" => "dining_table", "Comment" => "餐桌"), array("Field" => "remark", "Comment" => "备注"), array("Field" => "student_number", "Comment" => "学号"), array("Field" => "updated_by_excel", "Comment" => "更新"));


//微信开放平台的参数，微信登陆网站使用
defined('OPEN_APPID') OR define('OPEN_APPID', "wx6e582e762076aae9");
defined('OPEN_APPSECRET') OR define('OPEN_APPSECRET', "08c3df9f13388510aa98c4b37f65b8d8");
defined('OPEN_CALLBACKURL') OR define('OPEN_CALLBACKURL', "http://xz.sleda.com/index/wxBack");