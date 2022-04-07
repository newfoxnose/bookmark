<?php
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

function replace4json($str)
{
    //$str = preg_replace("'([\r\n])[\s]+'", "", $str);     //replace return by regex pattern
    $str = str_replace(array("\r\n", "\r", "\n"), "", $str);
    $str = preg_replace("/	/", "", $str);    //删掉tab
    //$str =preg_replace("/\s|　/","",$str);    //会把正常空格删掉
    $str = preg_replace("/　/", "", $str);
    $str = str_replace('\\', '\\\\', $str);
    $str = str_replace('"', '\"', $str);
    //$str = str_replace(':', '\:', $str);
    $str = str_replace('\'', '\'', $str);
    //$str=str_replace(',', '\,', $str);
    return $str;
}

function deldir($dir)
{
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

function get_zip_originalsize($filename, $path)
{
    //先判断待解压的文件是否存在
    if (!file_exists($filename)) {
        die("文件 $filename 不存在！");
    }
    $starttime = explode(' ', microtime()); //解压开始的时间
    //将文件名和路径转成windows系统默认的gb2312编码，否则将会读取不到
    $filename = iconv("utf-8", "gb2312", $filename);
    $path = iconv("utf-8", "gb2312", $path);
    //打开压缩包
    $resource = zip_open($filename);
    $i = 1;
    //遍历读取压缩包里面的一个个文件
    while ($dir_resource = zip_read($resource)) {
        //如果能打开则继续
        if (zip_entry_open($resource, $dir_resource)) {
            //获取当前项目的名称,即压缩包里面当前对应的文件名
            $file_name = $path . zip_entry_name($dir_resource);
            //以最后一个“/”分割,再用字符串截取出路径部分
            $file_path = substr($file_name, 0, strrpos($file_name, "/"));
            //如果路径不存在，则创建一个目录，true表示可以创建多级目录
            if (!is_dir($file_path)) {
                mkdir($file_path, 0777, true);
            }
            //如果不是目录，则写入文件
            if (!is_dir($file_name)) {
                //读取这个文件
                $file_size = zip_entry_filesize($dir_resource);
                //最大读取6M，如果文件过大，跳过解压，继续下一个
                if ($file_size < (1024 * 1024 * 6)) {
                    $file_content = zip_entry_read($dir_resource, $file_size);
                    file_put_contents($file_name, $file_content);
                } else {
                    echo "<p> " . $i++ . " 此文件已被跳过，原因：文件过大， -> " . iconv("gb2312",
                            "utf-8", $file_name) . " </p>";
                }
            }
            //关闭当前
            zip_entry_close($dir_resource);
        }
    }
    //关闭压缩包
    zip_close($resource);
    $endtime = explode(' ', microtime()); //解压结束的时间
    $thistime = $endtime[0] + $endtime[1] - ($starttime[0] + $starttime[1]);
    $thistime = round($thistime, 3); //保留3为小数
    //echo "<p>解压完毕！，本次解压花费：$thistime 秒。</p>";
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

function getpath($sitename)
{
    if ($sitename != '') {
        $firstchar = mb_substr($sitename, 0, 1, 'utf-8');
        $lastchar = mb_substr($sitename, -1, 1, 'utf-8');
        if (!is_dir('user/' . $firstchar)) {
            mkdir('user/' . $firstchar, 0755);
        }
        if (!is_dir('user/' . $firstchar . '/' . $lastchar)) {
            mkdir('user/' . $firstchar . '/' . $lastchar, 0755);
        }
        if (!is_dir('user/' . $firstchar . '/' . $lastchar . '/' . $sitename)) {
            mkdir('user/' . $firstchar . '/' . $lastchar . '/' . $sitename, 0755);
        }
        if (!is_dir('user/' . $firstchar . '/' . $lastchar . '/' . $sitename . '/userthemes')) {
            mkdir('user/' . $firstchar . '/' . $lastchar . '/' . $sitename . '/userthemes',
                0755);
        }
        if (!is_dir('user/' . $firstchar . '/' . $lastchar . '/' . $sitename . '/upload')) {
            mkdir('user/' . $firstchar . '/' . $lastchar . '/' . $sitename . '/upload',
                0755);
        }
        if (!is_dir('user/' . $firstchar . '/' . $lastchar . '/' . $sitename . '/gallery')) {
            mkdir('user/' . $firstchar . '/' . $lastchar . '/' . $sitename . '/gallery',
                0755);
        }
        if (!is_dir('user/' . $firstchar . '/' . $lastchar . '/' . $sitename . '/gallery/thumbnail')) {
            mkdir('user/' . $firstchar . '/' . $lastchar . '/' . $sitename . '/gallery/thumbnail',
                0755);
        }
        if (!is_dir('user/' . $firstchar . '/' . $lastchar . '/' . $sitename . '/download')) {
            mkdir('user/' . $firstchar . '/' . $lastchar . '/' . $sitename . '/download',
                0755);
        }
        if (!is_dir('user/' . $firstchar . '/' . $lastchar . '/' . $sitename . '/productthumbnail')) {
            mkdir('user/' . $firstchar . '/' . $lastchar . '/' . $sitename . '/productthumbnail',
                0755);
        }
        if (!is_dir('user/' . $firstchar . '/' . $lastchar . '/' . $sitename . '/sitemap')) {
            mkdir('user/' . $firstchar . '/' . $lastchar . '/' . $sitename . '/sitemap',
                0755);
        }
        $path = 'user/' . $firstchar . '/' . $lastchar . '/' . $sitename;
        return $path;
    } else {
        return '';
    }
}

function paginate_one($reload, $page, $tpages, $adjacents)    //管理员后台用
{
    $firstlabel = "&laquo;&nbsp;";
    $prevlabel = "&lsaquo;&nbsp;";
    $nextlabel = "&nbsp;&rsaquo;";
    $lastlabel = "&nbsp;&raquo;";
    $out = "<ul class=\"pagination\">\n";
    // first
    if ($page > 1) {
        $out .= "<li><a href=\"" . $reload . "\">" . $firstlabel . "</a></li>\n";
    } else {
        $out .= "<li><a href=\"###\">" . $firstlabel . "</a></li>\n";
    }
    // previous
    if ($page == 1) {
        $out .= "<li><a href=\"###\">" . $prevlabel . "</a></li>\n";
    } elseif ($page == 2) {
        $out .= "<li><a href=\"" . $reload . "\">" . $prevlabel . "</a></li>\n";
    } else {
        $out .= "<li><a href=\"" . $reload . "&page=" . ($page - 1) . "\">" . $prevlabel . "</a></li>\n";
    }
    // 1 2 3 4 etc
    $pmin = ($page > $adjacents) ? ($page - $adjacents) : 1;
    $pmax = ($page < ($tpages - $adjacents)) ? ($page + $adjacents) : $tpages;
    for ($i = $pmin; $i <= $pmax; $i++) {
        if ($i == $page) {
            $out .= "<li class=\"active\"><a href=\"###\">" . $i . "</a></li>\n";
        } elseif ($i == 1) {
            $out .= "<li><a href=\"" . $reload . "\">" . $i . "</a></li>\n";
        } else {
            $out .= "<li><a href=\"" . $reload . "&page=" . $i . "\">" . $i . "</a></li>\n";
        }
    }
    // next
    if ($page < $tpages) {
        $out .= "<li><a href=\"" . $reload . "&page=" . ($page + 1) . "\">" . $nextlabel . "</a></li>\n";
    } else {
        $out .= "<li><a href=\"###\">" . $nextlabel . "</a></li>\n";
    }
    // last
    if ($page < $tpages) {
        $out .= "<li><a href=\"" . $reload . "&page=" . $tpages . "\">" . $lastlabel . "</a></li>\n";
    } else {
        $out .= "<li><a href=\"###\">" . $lastlabel . "</a></li>\n";
    }
    $out .= "</ul>";
    return $out;
}

function paginate_two($reload, $page, $tpages, $adjacents,$tail)
{
    $firstlabel = "&laquo;&nbsp;";
    $prevlabel = "&lsaquo;&nbsp;";
    $nextlabel = "&nbsp;&rsaquo;";
    $lastlabel = "&nbsp;&raquo;";
    $out = "<ul class=\"pagination\">\n";
    // first
    if ($page > 1) {
        $out .= "<li><a href=\"" . $reload.'1'.$tail . "\">" . $firstlabel . "</a></li>\n";
    } else {
        $out .= "<li><a href=\"###\">" . $firstlabel . "</a></li>\n";
    }
    // previous
    if ($page == 1) {
        $out .= "<li><a href=\"###\">" . $prevlabel . "</a></li>\n";
    }
    else {
        $out .= "<li><a href=\"" . $reload . ($page - 1).$tail . "/\">" . $prevlabel . "</a></li>\n";
    }
    // 1 2 3 4 etc
    $pmin = ($page > $adjacents) ? ($page - $adjacents) : 1;
    $pmax = ($page < ($tpages - $adjacents)) ? ($page + $adjacents) : $tpages;
    for ($i = $pmin; $i <= $pmax; $i++) {
        if ($i == $page) {
            $out .= "<li class=\"active\"><a href=\"###\">" . $i . "</a></li>\n";
        } elseif ($i == 1) {
            $out .= "<li><a href=\"" . $reload.'1'.$tail . "\">" . $i . "</a></li>\n";
        } else {
            //    $out.= "<li><a href=\"" . $reload . "&amp;page=" . $i . "\">" . $i . "</a></li>\n";
            $out .= "<li><a href=\"" . $reload . $i .$tail. "/\">" . $i . "</a></li>\n";
        }
    }
    // next
    if ($page < $tpages) {
        // $out.= "<li><a href=\"" . $reload . "&amp;page=" . ($page + 1) . "\">" . $nextlabel . "</a></li>\n";
        $out .= "<li><a href=\"" . $reload . ($page + 1).$tail . "/\">" . $nextlabel . "</a></li>\n";
    } else {
        $out .= "<li><a href=\"###\">" . $nextlabel . "</a></li>\n";
    }
    // last
    if ($page < $tpages) {
        // $out.= "<li><a href=\"" . $reload . "&amp;page=" . $tpages . "\">" . $lastlabel . "</a></li>\n";
        $out .= "<li><a href=\"" . $reload . $tpages.$tail . "/\">" . $lastlabel . "</a></li>\n";
    } else {
        $out .= "<li><a href=\"###\">" . $lastlabel . "</a></li>\n";
    }
    $out .= "</ul>";
    return $out;
}

function copydir($strSrcDir, $strDstDir)
{
    $dir = opendir($strSrcDir);
    if (!$dir) {
        return false;
    }
    if (!is_dir($strDstDir)) {
        if (!mkdir($strDstDir)) {
            return false;
        }
    }
    while (false !== ($file = readdir($dir))) {
        if (($file != '.') && ($file != '..')) {
            if (is_dir($strSrcDir . '/' . $file)) {
                if (!copydir($strSrcDir . '/' . $file, $strDstDir . '/' . $file)) {
                    return false;
                }
            } else {
                if (!copy($strSrcDir . '/' . $file, $strDstDir . '/' . $file)) {
                    return false;
                }
            }
        }
    }
    closedir($dir);
    return true;
}

// 获取文件夹大小  
function getDirSize($dir)
{
    if ($dir != '' && $dir != null) {
        $dir = new RecursiveDirectoryIterator($dir);
        $totalSize = 0;
        foreach (new RecursiveIteratorIterator($dir) as $file) {
            $totalSize += $file->getSize();
        }
        return $totalSize;
    } else {
        return 0;
    }
}

// 单位自动转换函数  
function getRealSize($size)
{
    $kb = 1024;         // Kilobyte  
    $mb = 1024 * $kb;   // Megabyte  
    //$gb = 1024 * $mb;   // Gigabyte  
    //$tb = 1024 * $gb;   // Terabyte  

    if ($size < $kb) {
        return $size . " B";
    } else if ($size < $mb) {
        return round($size / $kb, 2) . " KB";
    } else {
        return round($size / $mb, 2) . " MB";
    }
    /*else if ($size < $gb) {
        return round($size / $mb, 2) . " MB";
    } 
    else if ($size < $tb) {
        return round($size / $gb, 2) . " GB";
    } else {
        return round($size / $tb, 2) . " TB";
    }
     * 
     */
}

function img_create_small($big_img, $width, $height, $small_img)
{//原始大图地址，缩略图宽度，高度，缩略图地址
    $imgage = getimagesize($big_img); //得到原始大图片
    switch ($imgage[2]) { // 图像类型判断
        case 1:
            $im = imagecreatefromgif($big_img);
            break;
        case 2:
            $im = imagecreatefromjpeg($big_img);
            break;
        case 3:
            $im = imagecreatefrompng($big_img);
            break;
    }
    $src_W = $imgage[0]; //获取大图片宽度
    $src_H = $imgage[1]; //获取大图片高度
    $src_ratio = $src_W / $src_H;
    $out_ratio = $width / $height;
    if ($src_ratio > $out_ratio) {
        $height = $width / $src_ratio;
    }
    if ($src_ratio < $out_ratio) {
        $width = $height * $src_ratio;
    }
    //echo $src_W . '.' . $src_H;
    $tn = imagecreatetruecolor($width, $height); //创建缩略图
    imagecopyresampled($tn, $im, 0, 0, 0, 0, $width, $height, $src_W, $src_H); //复制图像并改变大小
    imagejpeg($tn, $small_img, 100); //输出图像
}

//获取文件夹或文件数量，第一个参数是路径，第二个参数为1返回文件数量，为0返回文件夹数量
function getdircount($dir, $type)
{
    $filenum = 0;
    $dirnum = 0;
    $open = scandir($dir);
    for ($i = 2; $i < count($open); $i++) {
        if (is_file($dir . '/' . $open[$i])) {
            $filenum++;
        } else {
            $dirnum++;
        }
    }
    if ($type == 'file') {
        return $filenum;
    }
    if ($type == 'folder') {
        return $dirnum;
    }
}

function verificationCode($leng)
{
    $arr = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));
    shuffle($arr);
    $str = implode('', array_slice($arr, 0, $leng));
    return $str;
}

function br_mb_substr($content, $num)
{
    //$content = str_replace('</p>', '卐', $content);
    $content = str_replace('<br>', '卐', $content);
    $content = strip_tags($content);
    $content = mb_substr($content, 0, $num, 'utf-8') . ' ...';
    $content = str_replace('卐', '<br>', $content);
    return $content;
}

function istopdomain($domain)
{
    $regex = "/^[a-z0-9-]+\.[a-z]+$/i";
    return (preg_match($regex, $domain));
}

function iswwwdomain($domain)
{
    $regex = "/^www\.[a-z0-9-]+\.[a-z]+$/i";
    return (preg_match($regex, $domain));
}


function looponce($source, $re1, $arrdata)
{
    $outfull = '';
    preg_match_all($re1, $source, $matches1);
    for ($i = 0; $i < count($matches1[0]); $i++) {
        if (isset($matches1[2][$i])) {
            $circle = $matches1[1][$i];
            if ($circle > count($arrdata) || $circle == 0) {
                $circle = count($arrdata);
            }
            $html1 = $matches1[2][$i];         //与looponce的区别
            //echo $html1;
            $re2 = "/{\:loopa}((.|\n)*?){\/loopa}/i";
            preg_match($re2, $html1, $matches2);
            //var_dump($matches2);
            if (isset($matches2[1])) {
                $html2 = $matches2[1];
                //echo $circle;
                for ($j = 0; $j < $circle; $j++) {
                    $out = str_replace('[a:title]', $arrdata[$j]['title'],$html2);   //放在第一位
                    $out = str_replace('[a:no]', $j,$out);
                    $out = str_replace('[a:href]', $arrdata[$j]['href'], $out);
                    $out = str_replace('[a:short]', $arrdata[$j]['short'], $out);        //短的lang
                    $out = str_replace('[a:img]', $arrdata[$j]['img'], $out);
                    $out = str_replace('[a:banner]', $arrdata[$j]['banner'], $out);
                    $out = str_replace('[a:brief]', $arrdata[$j]['brief'], $out);
                    $out = str_replace('[a:size]', $arrdata[$j]['size'], $out);
                    $out = str_replace('[a:year]', $arrdata[$j]['year'], $out);
                    $out = str_replace('[a:month]', $arrdata[$j]['month'], $out);
                    $out = str_replace('[a:day]', $arrdata[$j]['day'], $out);
                    $out = str_replace('[a:views]', $arrdata[$j]['views'], $out);
                    $out = str_replace('[a:price]', $arrdata[$j]['price'], $out);
                    $out = str_replace('[a:id]', $arrdata[$j]['id'], $out);
                    $out = str_replace('[a:status]', $arrdata[$j]['status'], $out);
                    $outfull = $outfull . $out;
                }
            }
        }
    }
    return $outfull;
}

function looponce2($source, $re1, $arrdata)
{
    $outfull = '';
    preg_match_all($re1, $source, $matches1);
    for ($i = 0; $i < count($matches1[0]); $i++) {
        if (isset($matches1[3][$i])) {
            $circle = $matches1[2][$i];    //记录的条数
            if ($circle > count($arrdata) || $circle == 0) {
                $circle = count($arrdata);
            }
            $html1 = $matches1[3][$i];                        //与looponce的区别
            $re2 = "/{\:loopa}((.|\n)*?){\/loopa}/i";
            preg_match($re2, $html1, $matches2);
            //var_dump($matches2);
            if (isset($matches2[1])) {
                $html2 = $matches2[1];
                for ($j = 0; $j < $circle; $j++) {
                    $out = str_replace('[a:title]', $arrdata[$j]['title'],
                        $html2);
                    $out = str_replace('[a:no]', $j,$out);
                    $out = str_replace('[a:href]', $arrdata[$j]['href'], $out);
                    $out = str_replace('[a:short]', $arrdata[$j]['short'], $out);      //短的lang
                    $out = str_replace('[a:img]', $arrdata[$j]['img'], $out);
                    $out = str_replace('[a:banner]', $arrdata[$j]['banner'], $out);
                    $out = str_replace('[a:brief]', $arrdata[$j]['brief'], $out);
                    $out = str_replace('[a:size]', $arrdata[$j]['size'], $out);
                    $out = str_replace('[a:year]', $arrdata[$j]['year'], $out);
                    $out = str_replace('[a:month]', $arrdata[$j]['month'], $out);
                    $out = str_replace('[a:day]', $arrdata[$j]['day'], $out);
                    $out = str_replace('[a:views]', $arrdata[$j]['views'], $out);
                    $out = str_replace('[a:price]', $arrdata[$j]['price'], $out);
                    $out = str_replace('[a:categoryhref]', $arrdata[$j]['categoryhref'], $out);
                    $outfull = $outfull . $out;
                }
            }
        }
    }
    return $outfull;
}

function looponce3($source, $re1, $arrdata)
    //对应{pt\:postlist_([0-9]+|[a-z]+)_([0-9]+)_([0-9]+)}([\s\S]*?){\/pt}这种形式
{
    $outfull = '';
    preg_match_all($re1, $source, $matches1);
    for ($i = 0; $i < count($matches1[0]); $i++) {
        if (isset($matches1[4][$i])) {
            $circle = $matches1[3][$i];
            if ($circle > count($arrdata) || $circle == 0) {
                $circle = count($arrdata);
            }
            $html1 = $matches1[4][$i];                        //与looponce的区别
            $re2 = "/{\:loopa}((.|\n)*?){\/loopa}/i";
            preg_match($re2, $html1, $matches2);
            //var_dump($matches2);
            if (isset($matches2[1])) {
                $html2 = $matches2[1];
                //echo $circle;
                for ($j = 0; $j < $circle; $j++) {
                    $out = str_replace('[a:title]', $arrdata[$j]['title'],
                        $html2);
                    $out = str_replace('[a:no]', $j,$out);
                    $out = str_replace('[a:href]', $arrdata[$j]['href'], $out);
                    $out = str_replace('[a:short]', $arrdata[$j]['short'], $out);      //短的lang
                    $out = str_replace('[a:img]', $arrdata[$j]['img'], $out);
                    $out = str_replace('[a:banner]', $arrdata[$j]['banner'], $out);
                    $out = str_replace('[a:brief]', $arrdata[$j]['brief'], $out);
                    $out = str_replace('[a:size]', $arrdata[$j]['size'], $out);
                    $out = str_replace('[a:year]', $arrdata[$j]['year'], $out);
                    $out = str_replace('[a:month]', $arrdata[$j]['month'], $out);
                    $out = str_replace('[a:day]', $arrdata[$j]['day'], $out);
                    $out = str_replace('[a:views]', $arrdata[$j]['views'], $out);
                    $out = str_replace('[a:price]', $arrdata[$j]['price'], $out);
                    $outfull = $outfull . $out;
                }
            }
        }
    }
    return $outfull;
}

function looptwice($source, $re1, $arrdata)
{
    $findarr = array("id", "title", "href", "img", "brief", "price", "onsale", "originalprice", "weight","unit","shopping","noshopping","year","month","day","pics");
    $out2full = '';
//$re1 = "/{pt\:([a-z]+)}((.|\n)*?){\/pt}/i";
    preg_match_all($re1, $source, $matches1);
    //var_dump($matches1[0]);
    for ($i = 0; $i < count($matches1[0]); $i++) {
        if (isset($matches1[3][$i])) {
            $circle2 = $matches1[1][$i];
            if ($circle2 > count($arrdata) || $circle2 == 0) {
                $circle2 = count($arrdata);
            }
                        $html1 = $matches1[3][$i];
            $re2 = "/{\:loopa}((.|\n)*?){\/loopa}/i";
            preg_match($re2, $html1, $matches2);
            //var_dump($matches2[2]);
            if (isset($matches2[1])) {
                $html2 = $matches2[1];
                $re3 = "/{\:loopb}((.|\n)*?){\/loopb}/i";
                preg_match($re3, $html2, $matches3);
                if (isset($matches3[1])) {
                    $html3 = $matches3[1];
                    $html3full = $matches3[0];
                }
                //echo $html3full;
                //die;
                for ($j = 0; $j < $circle2; $j++) {
                    if (isset($html3)) {
                        $out3full = '';
                        $circle3 = $matches1[2][$i];                //位置从前面移到了这里，不然会被覆盖
                        if ($circle3 > count($arrdata[$j]['content']) || $circle3 == 0) {
                            $circle3 = count($arrdata[$j]['content']);
                        }
                        for ($k = 0; $k < $circle3; $k++) {
                            $out3 = $html3;
                            for ($l = 0; $l < count($findarr); $l++) {
                                $out3 = str_replace("[b:" . $findarr[$l] . "]", $arrdata[$j]['content'][$k][$findarr[$l]], $out3);
                            }
                            $out3full = $out3full . $out3;
                        }
                        $out2 = str_replace($html3full, $out3full, $html2);
                    }
                    $out2 = str_replace('[a:title]', $arrdata[$j]['title'],$out2);
                    $out2 = str_replace('[a:href]', $arrdata[$j]['href'], $out2);
                    $out2 = str_replace('[a:year]', $arrdata[$j]['year'], $out2);
                    $out2 = str_replace('[a:month]', $arrdata[$j]['month'], $out2);
                    $out2 = str_replace('[a:day]', $arrdata[$j]['day'], $out2);
                    $out2full = $out2full . $out2;
                }
            }
        }
    }
    return $out2full;
}

function looptwice2($source, $re1, $arrdata)
{
    $out2full = '';
    preg_match_all($re1, $source, $matches1);
    for ($i = 0; $i < count($matches1[0]); $i++) {
        if (isset($matches1[4][$i])) {
            $circle2 = $matches1[2][$i];
            if ($circle2 > count($arrdata) || $circle2 == 0) {
                $circle2 = count($arrdata);
            }
                        $html1 = $matches1[4][$i];
            $re2 = "/{\:loopa}((.|\n)*?){\/loopa}/i";
            preg_match($re2, $html1, $matches2);
            //var_dump($matches2[2]);
            if (isset($matches2[1])) {
                $html2 = $matches2[1];
                $re3 = "/{\:loopb}((.|\n)*?){\/loopb}/i";
                preg_match($re3, $html2, $matches3);
                if (isset($matches3[1])) {
                    $html3 = $matches3[1];
                    $html3full = $matches3[0];
                }
                for ($j = 0; $j < $circle2; $j++) {
                    if (isset($html3)) {
                        $out3full = '';
                        $circle3 = $matches1[3][$i];
                        if ($circle3 > count($arrdata[$j]['content']) || $circle3 == 0) {
                            $circle3 = count($arrdata[$j]['content']);
                        }
                        for ($k = 0; $k < $circle3; $k++) {
                            $out3 = str_replace('[b:title]',$arrdata[$j]['content'][$k]['title'], $html3);
                            $out3 = str_replace('[b:href]',$arrdata[$j]['content'][$k]['href'], $out3);
                            $out3 = str_replace('[b:img]',$arrdata[$j]['content'][$k]['img'], $out3);
                            $out3 = str_replace('[b:banner]',$arrdata[$j]['content'][$k]['banner'], $out3);
                            $out3 = str_replace('[b:brief]',$arrdata[$j]['content'][$k]['brief'], $out3);
                            $out3 = str_replace('[b:year]',$arrdata[$j]['content'][$k]['year'], $out3);
                            $out3 = str_replace('[b:month]',$arrdata[$j]['content'][$k]['month'], $out3);
                            $out3 = str_replace('[b:day]',$arrdata[$j]['content'][$k]['day'], $out3);
                            $out3full = $out3full . $out3;
                        }
                        $out2 = str_replace($html3full, $out3full, $html2);
                    }
                    $out2 = str_replace('[a:title]', $arrdata[$j]['title'],$out2);
                    $out2 = str_replace('[a:href]', $arrdata[$j]['href'], $out2);
                    $out2full = $out2full . $out2;
                }
            }
        }
    }
    return $out2full;
}

function loopthrice($source, $re1, $arrdata)
{
    $findarr = array("id", "title", "href", "img", "brief", "price", "onsale", "originalprice", "weight","unit","shopping","noshopping","year","month","day","attachment");
    $out2full = '';
//$re1 = "/{pt\:([a-z]+)}((.|\n)*?){\/pt}/i";
    preg_match_all($re1, $source, $matches1);     //匹配最外层PT
    //var_dump($matches1[0]);
    for ($i = 0; $i < count($matches1[0]); $i++) {
        if (isset($matches1[3][$i])) {           //如果存在PT
            $circle2 = $matches1[1][$i];          //A循环次数
            if ($circle2 > count($arrdata) || $circle2 == 0) {
                $circle2 = count($arrdata);
            }
            $html1 = $matches1[3][$i];
            $re2 = "/{\:loopa}((.|\n)*?){\/loopa}/i";       //匹配A循环
            preg_match($re2, $html1, $matches2);
            //var_dump($matches2[2]);
            if (isset($matches2[1])) {
                $html2 = $matches2[1];
                $re3 = "/{\:loopb}((.|\n)*?){\/loopb}/i";          //匹配B循环
                preg_match($re3, $html2, $matches3);
                if (isset($matches3[1])) {
                    $html3 = $matches3[1];        //B循环里的内容
                    $html3full = $matches3[0];
                }
                //echo $html3full;
                //die;
                for ($j = 0; $j < $circle2; $j++) {
                    if (isset($html3)) {
                        $out3full = '';
                        $circle3 = $matches1[2][$i];                //位置从前面移到了这里，不然会被覆盖，B循环次数
                        if ($circle3 > count($arrdata[$j]['content']) || $circle3 == 0) {
                            $circle3 = count($arrdata[$j]['content']);
                        }
                        for ($k = 0; $k < $circle3; $k++) {
                            $out3 = $html3;       //B循环里的内容
                            $re4 = "/{\:loopc}((.|\n)*?){\/loopc}/i";          //匹配C循环
                            preg_match($re4, $html3, $matches4);                  //matchesc是C循环的匹配结果
                            if (isset($matches4[1])) {
                                $html4 = $matches4[1];        //C循环里的内容
                                $html4full = $matches4[0];
                                //var_dump($matches4[1]);
                                $pics=$arrdata[$j]['content'][$k]['pics'];
                                //var_dump($pics);
                                $out4='';
                                for ($m = 0; $m < count($pics); $m++) {
                                    $out4 = $out4.str_replace("[c:pic]", $pics[$m], $html4);
                                }
                                $out3 = str_replace($html4full,$out4, $out3);
                            }
                            for ($l = 0; $l < count($findarr); $l++) {
                                $out3 = str_replace("[b:" . $findarr[$l] . "]", $arrdata[$j]['content'][$k][$findarr[$l]], $out3);
                            }
                            $out3full = $out3full . $out3;
                        }
                        $out2 = str_replace($html3full, $out3full, $html2);
                    }
                    $out2 = str_replace('[a:title]', $arrdata[$j]['title'],$out2);
                    $out2 = str_replace('[a:href]', $arrdata[$j]['href'], $out2);
                    $out2 = str_replace('[a:year]', $arrdata[$j]['year'], $out2);
                    $out2 = str_replace('[a:month]', $arrdata[$j]['month'], $out2);
                    $out2 = str_replace('[a:day]', $arrdata[$j]['day'], $out2);
                    $out2full = $out2full . $out2;
                }
            }
        }
    }
    return $out2full;
}

function codeconv($str, $direction)
{
    /*
    if ($direction == 1) {
        $str = str_replace("&nbsp;", "#@#", $str);
        $str = str_replace("<code>", "<code#", $str);
        $str = str_replace("</code>", "!code>", $str);
        $str = str_replace("<pre>", "<pre#", $str);
        $str = str_replace("</pre>", "!pre>", $str);
    }
    if ($direction == -1) {
        $str = str_replace("<code#", "<code>", $str);
        $str = str_replace("!code>", "</code>", $str);
        $str = str_replace("<pre#", "<pre>", $str);
        $str = str_replace("!pre>", "</pre>", $str);
        $str = str_replace("#@#", "&nbsp;", $str);
    }
    */
    return $str;
}


function myecho($str)
{
    echo "<p>" . $str . "</p>";
}

function getfilenamefromurl($url)
{
    preg_match("/\/([0-9]+[\.jpg|\.gif|\.jpeg|\.png|\.bmp]+)/i", $url, $filename);
    return $filename[1];
}


//function paypal($order, $currency, $price, $shipping,$itemarr,$cancelurl,$returnurl,$notifyurl){
function paypal($pp_hostname, $user, $pwd, $signature, $order, $currency, $price, $shipping, $itemarr, $cancelurl, $returnurl, $notifyurl)
{
    //$pp_hostname = "sandbox.paypal.com";

    $API_Endpoint = "https://api-3t.$pp_hostname/nvp";
    /*
    $user = urlencode("117223504_api1.qq.com");
    $pwd = urlencode("H6T24MG8WTRE8KBU");
    $signature = urlencode("A-Ate8Vp8kszfWBNdI3AEE8ZSmXVA1j0cFsHl0NZ6l2Mg.HGWidyVBN.");
*/
    $user = urlencode($user);
    $pwd = urlencode($pwd);
    $signature = urlencode($signature);


    $method = urlencode("BMCreateButton");
    $buttontype = urlencode("BUYNOW");
    $buttoncode = urlencode("ENCRYPTED");
    $version = urlencode("204");
    $subreq = '';
    $L_BUTTONVAR = array();
    array_push($L_BUTTONVAR, urlencode("currency_code=" . $currency));
    array_push($L_BUTTONVAR, urlencode("item_name=" . $order));
    array_push($L_BUTTONVAR, urlencode("amount=" . $price));
    array_push($L_BUTTONVAR, urlencode("shipping=" . $shipping));
    array_push($L_BUTTONVAR, urlencode("cancel_return=" . $cancelurl));
    array_push($L_BUTTONVAR, urlencode("return=" . $returnurl));      //这个链接是支付完成后出现的允许点击的链接，无法带结果参数，有pdt的话这个不起作用，但pdt是在paypal网站里针对整个账户设置的，无法单独指定
    array_push($L_BUTTONVAR, urlencode("notify_url=" . $notifyurl));

    /*
    //等以后要包括订单管理系统的时候再用，必须包括全部国家、城市、姓名等内容
    array_push($L_BUTTONVAR, urlencode("no_shipping=2"));
    array_push($L_BUTTONVAR, urlencode("address_override=1"));
    array_push($L_BUTTONVAR, urlencode("country=".$countrycode));
    array_push($L_BUTTONVAR, urlencode("state=abc"));
    array_push($L_BUTTONVAR, urlencode("city=Beijing"));
    array_push($L_BUTTONVAR, urlencode("address2=jfiej iejwojsje"));
    array_push($L_BUTTONVAR, urlencode("address1=no342"));
    array_push($L_BUTTONVAR, urlencode("email=jiejo@ajeiw.com"));
    array_push($L_BUTTONVAR, urlencode("first_name=Nan"));
    array_push($L_BUTTONVAR, urlencode("last_name=Zhang"));
    array_push($L_BUTTONVAR, urlencode("zip=y3y4y3"));
    array_push($L_BUTTONVAR, urlencode("night_phone_b=170885554"));
    */
    for ($i = 0; $i < count($L_BUTTONVAR); $i++) {
        $subreq = $subreq . "&L_BUTTONVAR" . ($i) . "=" . $L_BUTTONVAR[$i];
    }

    for ($i = 0; $i < count($itemarr); $i++) {
        if ($i % 2 == 0) {
            $subreq = $subreq . "&L_BUTTONVAR" . ($i + count($L_BUTTONVAR)) . "=" . urlencode("on" . $i . "=" . $itemarr[$i]);
        } else {
            $subreq = $subreq . "&L_BUTTONVAR" . ($i + count($L_BUTTONVAR)) . "=" . urlencode("os" . ($i - 1) . "=" . $itemarr[$i]);
        }
    }

    $req = "USER=" . $user . "&PWD=" . $pwd . "&SIGNATURE=" . $signature . "&METHOD=" . $method . "&BUTTONTYPE=" . $buttontype . "&BUTTONCODE=" . $buttoncode . "&VERSION=" . $version . $subreq;

    //return $req;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $API_Endpoint);
    curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
    $res = curl_exec($ch);
    //return $res;
    if (strpos($res, "ACK=Success") != 0) {
        $lines = explode("&", $res);
        $keyarray = array();
        for ($i = 0; $i < count($lines); $i++) {
            list($key, $val) = explode("=", $lines[$i]);
            $keyarray[urldecode($key)] = urldecode($val);
            //echo urldecode($key) . ":" . urldecode($val) . "<br>";
        }
        $reTag = "/(-----BEGIN PKCS7)(.|\n)*?(END PKCS7-----)/i";
        preg_match($reTag, $keyarray['WEBSITECODE'], $match);
        $pp_btn = $match[0];
        //$pp_btn = $keyarray['WEBSITECODE'];
    } else {
        $pp_btn = "";
    }
    return $pp_btn;
}


function currency_symbol($currency)
{
    switch ($currency) {
        case "USD":
            return "$";
            break;
        case "EUR":
            return "€";
            break;
        case "RMB":
            return "￥";
            break;
        case "GBP":
            return "£";
            break;
        case "JPY":
            return "￥";
            break;
        case "THB":
            return "฿";
            break;
        default:
            return $currency;
    }
}

function check_file_type($filetype, $type)
{
    $img_array = array("image/gif", "image/jpeg", "image/pjpeg", "image/png", "image/x-icon");
    $font_array = array("application/octet-stream", "application/vnd.ms-fontobject", "application/font-woff", "application/x-font-woff", "image/svg+xml");
    $zip_array = array("application/x-zip-compressed", "application/octet-stream", "application/zip");
    $rar_array = array("application/x-rar-compressed", "application/octet-stream", "application/rar");
    $txt_array = array("text/css", "text/html", "application/x-javascript", "text/plain", "text/xml", "application/json");
    $mp4_array = array("video/mp4", "video/webm");
    $pdf_array = array("application/pdf");
    switch ($type) {
        case "img":
            if (in_array($filetype, $img_array)) {
                return true;
            } else {
                return false;
            }
            break;
        case "font":
            if (in_array($filetype, $font_array)) {
                return true;
            } else {
                return false;
            }
            break;
        case "zip":
            if (in_array($filetype, $zip_array)) {
                return true;
            } else {
                return false;
            }
            break;
        case "rar":
            if (in_array($filetype, $rar_array)) {
                return true;
            } else {
                return false;
            }
            break;
        case "txt":
            if (in_array($filetype, $txt_array)) {
                return true;
            } else {
                return false;
            }
            break;
        case "mp4":
            if (in_array($filetype, $mp4_array)) {
                return true;
            } else {
                return false;
            }
            break;
        case "pdf":
            if (in_array($filetype, $pdf_array)) {
                return true;
            } else {
                return false;
            }
            break;
        default:
            return false;
    }
}

function myurlencode($str){
    //return urlencode($str);
    return str_replace("%2F","/",rawurlencode($str));
    //return htmlspecialchars($str);
}

function get_file_type($filepath){
    $finfo    = finfo_open(FILEINFO_MIME);
    $mimetype = finfo_file($finfo, $filepath);
    finfo_close($finfo);
    return str_replace("; charset=binary","",$mimetype);
}



function send_mail($to,$fromname,$replyto,$subject,$mailbody) {
    $url = 'http://api.sendcloud.net/apiv2/mail/send';
    $API_USER = 'dfefwsf94832';
    $API_KEY = 'FFeScoScZ35gn0Ez';

    $param = array(
        'apiUser' => $API_USER, # 使用api_user和api_key进行验证
        'apiKey' => $API_KEY,
        'from' => 'sender@mailer.sleda.com', # 发信人，用正确邮件地址替代
        'fromName' => $fromname,
        'replyTo'=>$replyto,
        'to' => $to,# 收件人地址, 用正确邮件地址替代, 多个地址用';'分隔
        'subject' => $subject,
        'html' => $mailbody,
        'respEmailId' => 'true'
    );


    $data = http_build_query($param);

    $options = array(
        'http' => array(
            'method' => 'POST',
            'header' => 'Content-Type: application/x-www-form-urlencoded',
            'content' => $data
        ));
    $context  = stream_context_create($options);
    $result = file_get_contents($url, FILE_TEXT, $context);

    $json=json_decode($result,true);
    return $json["statusCode"];

    /*
     $result的返回值：{"result":true,"statusCode":200,"message":"请求成功","info":{"emailIdList":["1512789698721_91615_16738_6088.sc-10_9_58_242-inbound0$bel81nan@163.com"]}}
     */
}

function translate_v3($from_lang,$to_lang,$inputStrArr){
    $transArr = array();
    for ($i = 0; $i < count($inputStrArr); $i++) {
        array_push($transArr, '{"Text":"'.addslashes($inputStrArr[$i]).'"}');
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.cognitive.microsofttranslator.com/translate?api-version=3.0&from='.$from_lang.'&to='.$to_lang);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS,  '['.implode(',',$transArr).']');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $headers = array();
    $headers[] = 'Ocp-Apim-Subscription-Key: 8afaf700418a4029b03fcb2b94b80296';           //bel81nan@hotmail.com
    $headers[] = 'Content-Type: application/json';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    curl_close ($ch);
    $json=json_decode($result,1);
    //$output=$json[0]['translations'][0]['text'];
    return $json;
}

function detect_v3($str){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.cognitive.microsofttranslator.com/detect?api-version=3.0');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "[{'Text':'".$str."'}]");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $headers = array();
    $headers[] = 'Ocp-Apim-Subscription-Key: 8afaf700418a4029b03fcb2b94b80296';           //bel81nan@hotmail.com
    $headers[] = 'Content-Type: application/json';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    curl_close ($ch);
    $json=json_decode($result,1);
    $output=$json[0]['language'];
    return (string)$output;
}

/**
 * 删除指定标签
 *
 * @param array $tags     删除的标签  数组形式
 * @param string $str     html字符串
 * @param bool $content   true保留标签的内容text
 * @return mixed
 */
function stripHtmlTags($tags, $str, $content = true)
{
    $html = array();
    // 是否保留标签内的text字符
    if($content){
        foreach ($tags as $tag) {
            array_push($html, '/(<' . $tag . '.*?>(.|\n)*?<\/' . $tag . '>)/is');
        }
    }else{
        foreach ($tags as $tag) {
            array_push($html,"/(<(?:\/" . $tag . "|" . $tag . ")[^>]*>)/is");
        }
    }
    $data = preg_replace($html, '', $str);
    return $data;
}
//输出<div><p>这里是p标签</p><img src="" alt="这里是img标签"><br></div>;

?>