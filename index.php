<?php
@header('Content-type: text/html;charset=UTF-8');
session_start();
session_destroy();      //将session去掉，以每次都能取新的session值;

//是否打开调试开关
$debug = 0;

//预定义一些空变量
$catout = '';
$father = '';
$fatherhref = '';
$titleout = '';
$metatitle = '';
$metakeywords = '';
$metadescription = '';
$contentout = '';
$postcategoryid = '';
$origincatid = '';
$sitename = '';
$tplpath = '';
$browserlang = '';
$siteurl = '';
$homelangurl = '';
$homelang_id = '';
$themename = '';
$langidsarr = array();
$thumbnail = '';
$businessemail = '';
$version = '';
$siteid = '';
$reload = '';
$tpages = '';
$langid = '';
$showshare = '';
$categoryidbysorting = '';
$matchtype = '';
$binding = false;
$bindingdomain = '';
$bindinglangid = '';
$langurl = '';
$colors = array('default', 'primary', 'success', 'info', 'warning', 'danger');
$days = 0;
$newchargedate = '';
$charge = 0;
$balance = 0;
//导入引用文件
require 'include/config.php';

require 'include/conn.php';
require 'include/function.php';
require 'include/langsarr.php';

function get($url)
{//curl
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, false);
    $file_contents = curl_exec($ch);
    curl_close($ch);
    return $file_contents;
}

//部分重复使用的正则
//$re_images = "/src=[\'|\"]?([http\:\/|https\:\/]?\/([\s\S]*?)(\.jpg|\.gif|\.jpeg|\.png|\.bmp))[\'|\"]?/i";   //全部匹配的为[0]，第一个圆括号里为[1]，内层的依次是[2]等。http这部分会出错
$re_images = "/src=[\'|\"]?(([\s\S]*?)(\.jpg|\.gif|\.jpeg|\.png|\.bmp))[\'|\"]?/i";

//判断用户的系统语言对应的api语种
if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
    $getbrowserlang = strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']);
    if (stripos($getbrowserlang, ',') !== false) {
        $getbrowserlang = substr($getbrowserlang, 0, stripos($getbrowserlang, ','));
    }
} else {
    $getbrowserlang = 'en';
}

switch ($getbrowserlang) {
    case 'zh-hk':
        $browserlang = 'zh-cht';
        break;
    case 'zh-mo':
        $browserlang = 'zh-cht';
        break;
    case 'zh-cn':
        //$browserlang = 'zh-chs';
        $browserlang = 'en';     //zh-chs莫名故障
        break;
    case 'zh-sg':
        //$browserlang = 'zh-chs';
        $browserlang = 'zh-cht';     //zh-chs莫名故障
        break;
    case 'zh-tw':
        $browserlang = 'zh-cht';
        break;
    default:
        for ($i = 0; $i < count($toarr); $i++) {
            if (strrpos($getbrowserlang, strtolower($toarr[$i])) !== false) {
                $browserlang = $toarr[$i];
            } else {
                $browserlang = 'en';
            }
        }
}
$host = $_SERVER['HTTP_HOST'];
$uri = $_SERVER["REQUEST_URI"];
if (stripos($_SERVER['SERVER_SOFTWARE'], "iis") != false) {
    $uri = iconv("gb2312", "UTF-8", $uri);                       //中文IIS需要对网址转码一下
}

$thisurl = 'http://' . $host . $uri;

if ($host != $basehost) {
    $sql1 = "select site_id,lang_id,domain from x_domain where domain='$host' and actived=1";
    $result1 = mysqli_query($con, $sql1);
    if ($result1->num_rows == 0) {
        if ($debug == 1) {
            myecho($host . $basehost . "line101");
            die;
        }
        Header("Location: http://" . $browserlang . '.' . $brandurl);
    } else {
        $binding = true;
        $row1 = mysqli_fetch_array($result1);
        $bindinglangid = $row1[1];
        $bindingdomain = $row1[2];
        $sql = "select * from x_site where id='$row1[0]'";
    }
} else {
    $sitename = checkinput('sitename', str_replace($prefix, "", str_replace("/", "", @$_GET["sitename"])), 3, 50);
    if ($sitename == '' || $sitename == null) {
        if ($debug == 1) {
            myecho($_GET["sitename"]);
            myecho($host);
            myecho("line116");
            die;
        }
        Header("Location: http://" . $browserlang . '.' . $brandurl);
    } else {
        $sql = "select * from x_site where sitename='$sitename'";
    }
}
if ($sql == '') {
    myecho("line133");
    die;
}
$result = mysqli_query($con, $sql);


if ($result->num_rows == 0) {
    if ($debug == 1) {
        myecho("line140");
        die;
    }
    Header("Location: http://" . $browserlang . '.' . $brandurl);
} else {
    $row = mysqli_fetch_array($result);
    $sitename = $row['sitename'];
    $version = $row['version'];
    $enddate = $row['enddate'];

    switch ($version) {
        case 0:
            $translationlimit = $translationlimit_free;
            $disklimit = $disklimit_free;
            $articlelimit = $articlelimit_free;
            $categorylimit = $categorylimit_free;
            $shopswitch = $shopswitch_free;
            $customthemeswitch = $customthemeswitch_free;
            break;
        case 1:
            $translationlimit = $translationlimit_basic;
            $disklimit = $disklimit_basic;
            $articlelimit = $articlelimit_basic;
            $categorylimit = $categorylimit_basic;
            $shopswitch = $shopswitch_basic;
            $customthemeswitch = $customthemeswitch_basic;
            break;
        case 2:
            $translationlimit = $translationlimit_pro;
            $disklimit = $disklimit_pro;
            $articlelimit = $articlelimit_pro;
            $categorylimit = $categorylimit_pro;
            $shopswitch = $shopswitch_pro;
            $customthemeswitch = $customthemeswitch_pro;
            break;
        default:
            die;
            break;
    }

    $balance = $row['balance'];
    $homelangurl = $row['homelangurl'];
    $homelang_id = $row['homelang_id'];
    if ($bindinglangid == 0) {
        $bindinglangid = $homelang_id;
    }
    $siteid = $row['id'];
    $themename = $row['themename'];
    $businessemail = $row['businessemail'];
    $showshare = $row['showshare'];
    $hidehomelang = $row['hidehomelang'];
    $pp_user = $row['paypal_user'];
    $pp_pwd = $row['paypal_pwd'];
    $pp_signature = $row['paypal_sign'];
    $currency = $row['currency'];
    $showgg = $row['showgg'];
    $ggclient = $row['gg_client'];
    $ggslot = $row['gg_slot'];
    $grouparr = '';
    $countrystr = '';
    $countryweight = array();
    $tempweight = "";
    for ($i = 1; $i < 5; $i++) {
        if ($row['countrygroup' . $i] != '' && $row['countrygroup' . $i] != null) {
            $tempweight = $row['firstweight' . $i] . "|" . $row['addweight' . $i];
            unset($tmparr);
            $tmparr = explode("|", $row['countrygroup' . $i]);
            for ($j = 0; $j < count($tmparr); $j++) {
                $countrystr = $countrystr . "|" . $tmparr[$j];
                $groupstr = $groupstr . "|" . $i;
                $countryweight[$tmparr[$j]] = $tempweight;
            }
        }
    }
    $countrystr = substr($countrystr, 1);
    $groupstr = substr($groupstr, 1);
    $shippingcountryarr = explode("|", $countrystr);
    sort($shippingcountryarr);
    if ($row['langs_id'] != null && $row['langs_id'] != '') {
        if ($hidehomelang != 1) {
            $langids = $homelang_id . ',' . $row['langs_id'];
        } else {
            $langids = $row['langs_id'];
        }
        if (strstr($langids, ',') != false) {
            $langidsarr = explode(",", $langids);        //所有有效语种数组
        } else {
            $langidsarr = (array)$langids;        //所有有效语种数组
        }
    } else {
        $langids = $homelang_id;
        $langidsarr = (array)$langids;       //所有有效语种数组
    }
}
if (count($langidsarr) == 1) {
    $onesite = 1;
}

if ($sitename == '' || $sitename == null) {
    if ($debug == 1) {
        myecho("line187");
        die;
    }
    Header("Location: http://" . $browserlang . '.' . $brandurl);
}

//获取json文件
$langjson = json_decode(get($base . "include/langs.json"));
//替换站点网址，后面要用$siteurl，所以要放的靠前一点
$lang = checkinput('lang', str_replace("/", "", @$_GET["lang"]), 0, 50);        //语种需要把里面的斜杠替换一下
$sitemap = checkinput('str', @$_GET["sitemap"], 0, 50);

//输入参数

$categoryoriginid = checkinput('int', $_GET["categoryoriginid"], 0, 11);
$page = checkinput('int', str_replace("_", "", @$_GET["page"]), 0, 11);
if ($page == '') {
    $page = 1;
}
$postid = checkinput('int', @$_GET["postid"], 0, 11);
$search = checkinput('strutfeasy', @$_GET["search"], 0, 50);
$tag = checkinput('strutf', @$_GET["tag"], 0, 50);
$firstchar = mb_substr($sitename, 0, 1, 'utf-8');
$lastchar = mb_substr($sitename, -1, 1, 'utf-8');
$path = 'user/' . $firstchar . '/' . $lastchar . '/' . $sitename;
if ($debug == 1) {
    myecho(urldecode($_GET["categoryname"]));
    myecho("line231");
    //die;
}
if (!is_dir($path)) {
    if ($debug == 1) {
        myecho("line209");
        die;
    }
    Header("Location: http://" . $browserlang . '.' . $brandurl);
}

if ($lang === '' || $lang === null) {
    if ($binding == true) {
        $lang = $langarr[$bindinglangid];
        $siteurl2 = 'http://' . $bindingdomain . '/' . $prefix . $sitename . '/';                //不能删，需要靠这个知道用户目录的路径
        $siteurl = 'http://' . $bindingdomain . '/';
        $langurl = '';
        if ($sitemap != '') {
            if (file_exists($path . '/sitemap/' . $bindingdomain . '.' . $sitemap)) {
                $myfile = fopen($path . '/sitemap/' . $bindingdomain . '.' . $sitemap, "r") or die("Unable to open file!");
                echo fread($myfile, filesize($path . '/sitemap/' . $bindingdomain . '.' . $sitemap));
                fclose($myfile);
            } else {
                $myfile = fopen($path . '/sitemap/sitemap.' . $sitemap, "r") or die("Unable to open file!");
                echo fread($myfile, filesize($path . '/sitemap/sitemap.' . $sitemap));
                fclose($myfile);
            }
            die;
        }
    } else {
        if ($homelangurl == '') {
            if ($hidehomelang != 1) {
                $langid = $homelang_id;       //当前语种ID，不要移动位置，下面要用，如果使用域名
                if ($langid == '') {
                    die;
                }
            } else {
                if ($langids == $homelang_id) {
                    $langid = $langidsarr[0];
                    //die;   在只有主语种的情况下隐藏主语种不生效
                } else {
                    $langid = $langidsarr[0];
                }
            }
            $lang = $langjson->{'lang2'}->{$langid}[0]->{'lang'};
            $langurl = $lang . '/';
        } else {
            if ($debug == 1) {
                myecho("line248");
                die;
            }
            Header("Location: " . $homelangurl);
        }
    }
} else {
    if ($binding === true) {
        //$lang = $langarr[$bindinglangid];
        $siteurl2 = 'http://' . $bindingdomain . '/' . $prefix . $sitename . '/';                //不能删，需要靠这个知道用户目录的路径
        $siteurl = 'http://' . $bindingdomain . '/';
        $langurl = $lang . '/';
    } else {
        $siteurl = $base . $prefix . $sitename . '/';
        $siteurl2 = $base . $prefix . $sitename . '/';
        if ($bindinglangid != $homelang_id || $langid != $homelang_id) {
            $langurl = $lang . '/';
        } else {
            $langurl = '';
        }
    }
}


if ($sitemap != '') {
    if (file_exists($path . '/sitemap/' . $bindingdomain . '.' . $sitemap)) {
        $myfile = fopen($path . '/sitemap/' . $bindingdomain . '.' . $sitemap, "r") or die("Unable to open file!");
        echo fread($myfile, filesize($path . '/sitemap/' . $bindingdomain . '.' . $sitemap));
        fclose($myfile);
    } else {
        $myfile = fopen($path . '/sitemap/sitemap.' . $sitemap, "r") or die("Unable to open file!");
        echo fread($myfile, filesize($path . '/sitemap/sitemap.' . $sitemap));
        fclose($myfile);
    }
    die;
}

if (!in_array($lang, $toarr)) {
    die;
}

$langid = $langjson->{'lang1'}->$lang;       //当前语种ID，不要移动位置，下面要用，同时包括了使用域名和不使用域名的情况
if ($langid == NULL) {
    $langid = $langjson->{'lang1'}->{'en'};
}
if (is_string($langidsarr) && $debug == 1) {
    $debugstr = '';
    $debugstr = $debugstr . '地址：' . $thisurl . ';sitename:' . $sitename . ';langidsarr:' . $langidsarr . '\n';
    $filename = "debug.txt";
    $handle = fopen($filename, "a+");
    $str = fwrite($handle, $debugstr);
    fclose($handle);
}
if ($debug == 1) {
    myecho("line380");
    var_dump($langid);
    var_dump($langidsarr);
    var_dump($homelang_id);
    //die;
}
if (!in_array($langid, $langidsarr) && $langid != $homelang_id) {
    die;
}

$arr = json_decode(get($base . $langpath . $lang . ".json"), true);
if ($debug == 1) {
    myecho("line388");
    //die;
}
if ($path == 'user///') {
    die;
}

if (get($base . $path . '/' . $langid . '.json')) {
    $json = json_decode(get($base . $path . '/' . $langid . '.json'), true);
} else {
    if ($debug == 1) {
        myecho("line294");
        myecho($base . $path . '/' . $langid . '.json');
        var_dump(get($base . $path . '/' . $langid . '.json'));
        die;
    }
    Header("Location: http://" . $browserlang . '.' . $brandurl);
}
if ($json == null || $json == '') {
    //Header("Location: " . $siteurl);   //此句在管理员修改过母语且正好没有母语json时会导致重复定向
    if ($debug == 1) {
        myecho($path . '/' . $langid . '.json');
        myecho("line302");
        die;
    }
    Header("Location: http://" . $browserlang . '.' . $brandurl);
}

if ($row['close_site'] == 1) {
    Header("Location: " . $siteurl . $langurl . "503.php");
    exit;
}

//分类相关变量
$catarr0 = $json['category0'];   //没有子类的分类名-分类ID数组
$catarr1 = $json['category1'];   //有父目录的分类ID数组
$catarr2 = $json['category2'];            //全部分类名数组
$originidarr = $json['categoryoriginid'];            //全部原始分类ID数组
$catarrori = $json['categoryorigin'];   //全部分类名-分类ID数组
$catarrall = $json['categoryall'];            //所有语种分类ID_语种-分类名数组
$subcatarr = $json['subcategory'];            //有父目录的分类的父分类ID_分类ID-分类名数组
$subcatarr2 = $json['subcategory2'];            //有父目录的分类的父分类ID_分类ID-分类名数组，针对非母语的语言
$cataliasarr = $json['catalias'];            //分类别名-名称数组
$cataliasidarr = $json['cataliasid'];            //分类别名-ID数组
$cattemplate = $json['categorytemplate'];            //分类别名-模板数组
$catthumbnails = $json['catthumbnails'];            //分类缩略图数组
$catbanners = $json['catbanners'];            //分类banner数组

//自定义域名相关
$domains = $json['domains'];            //语种-域名数组
//单词相关变量
$wordarr = $json['words'];
//TAG数组
$tagarrpre = $json['tags'];
$tagarr = array();
$mintagsize = 99999999;
$maxtagsize = 0;
foreach ($tagarrpre as $key => $value) {
    array_push($tagarr, $key);
    if ($value > $maxtagsize) {
        $maxtagsize = $value;
    }
    if ($value < $mintagsize) {
        $mintagsize = $value;
    }
}
///////////////////////////////////////////////
if (array_key_exists('{' . $homelang_id . '}', $domains)) {
    $mainurl = 'http://' . $domains['{' . $homelang_id . '}'] . '/';
} else {
    if (count($domains) > 0) {
        $mainurl = 'http://' . reset($domains) . '/';
    } else {
        $mainurl = $base . $prefix . $sitename . '/';
    }
}
//$siteurl=$mainurl;
//单页数组
$singles = $json['singles'];          //单页ID-标题数组
$singleids = $json['singleids'];      //单页ID-原始ID数组，可以删掉
$singlealias = $json['singlealias'];          //单页ID-标题数组
$singlealiasid = $json['singlealiasid'];      //单页ID-原始ID数组
//推荐页数组
$recommendidsarr = $json['recommendids'];            //推荐页面ID数组
$recommendtitlesarr = $json['recommendtitles'];            //推荐页面标题数组
$recommendthumbnailsarr = $json['recommendthumbnails'];            //推荐页面img数组
$recommendbriefsarr = $json['recommendbriefs'];            //推荐页面brief数组
$recommendpricesarr = $json['recommendprices'];            //推荐页面价格数组
$recommendtimesarr = $json['recommendtimes'];            //推荐页面价格数组
//链接数组
$linksarr = $json['links'];      //单页ID-原始ID数组
$logosarr = $json['logos'];      //单页ID-原始ID数组
/////////////公共标签
$tagoutarr = array();
for ($i = 0; $i < count($tagarr); $i++) {
    array_push($tagoutarr, "<a href='" . $siteurl . $langurl . "tag/" . myurlencode($tagarr[$i]) . "/'>" . $tagarr[$i] . '</a>');
}

//判断页面种类
$categoryname = checkinput('strutfeasy', $_GET["categoryname"], 0, 100);
if ($categoryname == '') {
    $categoryname = $catarrall[$categoryoriginid . '_' . $langid];
}
$pagetype = 'index';
if ($postid != '' && $categoryname == '' && $sitename != '' && $lang != '') {
    $pagetype = 'page';
}
if ($postid == '' && $categoryname != '' && $sitename != '' && $lang != '') {
    $pagetype = 'category';
}
if ($postid == '' && $categoryname == '' && $sitename != '' && $search == '' && $tag == '') {
    $pagetype = 'index';
}
if ($postid == '' && $categoryname == '' && $sitename != '' && $search != '' && $lang != '') {
    $pagetype = 'search';
}
if ($postid == '' && $categoryname == '' && $sitename != '' && $tag != '' && $lang != '') {
    $pagetype = 'tag';
}
if ($postid == '' && $categoryname == 'gallery' && $sitename != '' && $lang != '') {
    $pagetype = 'gallery';
}
if ($postid == '' && $categoryname == 'download' && $sitename != '' && $lang != '') {
    $pagetype = 'download';
}
if ($postid == '' && $categoryname == 'cart' && $sitename != '' && $lang != '') {
    $pagetype = 'cart';
}
if ($postid == '' && $categoryname == 'checkout' && $sitename != '' && $lang != '') {
    $pagetype = 'checkout';
}
if ($postid == '' && $categoryname == 'client' && $sitename != '' && $lang != '') {
    $pagetype = 'client';
}
if ($postid == '' && $categoryname == 'complete' && $sitename != '' && $lang != '') {
    $pagetype = 'complete';
}
//读取模板
if ($themename == '') {
    $themename = '*default-green';
}
if (substr($themename, 0, 1) == '*') {
    $tplpath = 'themes/' . substr($themename, 1);
} else {
    $tplpath = $path . '/userthemes/' . $themename;
}
if (!is_dir($tplpath)) {
    $tplpath = 'themes/default-green';
}
//读取tpl中的requires.json
$themejson = json_decode(get($base . $tplpath . "/requires.json"));
//读取模板文件
if ($pagetype == 'index') {
    $tpl = get($base . $tplpath . '/index.html');
}
if ($pagetype == 'search') {
    $tpl = get($base . $tplpath . '/search.html');
}
if ($pagetype == 'tag') {
    $tpl = get($base . $tplpath . '/tag.html');
}
if ($pagetype == 'gallery') {
    $tpl = get($base . $tplpath . '/gallery.html');
}
if ($pagetype == 'download') {
    $tpl = get($base . $tplpath . '/download.html');
}
if ($pagetype == 'cart') {
    $tpl = get($base . $tplpath . '/cart.html');
}
if ($pagetype == 'checkout') {
    $tpl = get($base . 'html/checkout.html');
}
if ($pagetype == 'client') {
    $tpl = get($base . 'html/client.html');
}
if ($pagetype == 'complete') {
    $tpl = get($base . 'html/complete.html');
}
if ($pagetype == 'category') {
    if (!is_file($tplpath . '/' . $cattemplate['{' . $categoryoriginid . '}'])) {
        $tpl = get($base . $tplpath . '/category.html');
    } else {
        $tpl = get($base . $tplpath . '/' . $cattemplate['{' . $categoryoriginid . '}']);
    }
}


if ($pagetype == 'page') {
    //读取文章内容
    $sql = "select * from x_post where status=1 and id='$postid' and site_id='$siteid'";
    $result = mysqli_query($con, $sql);
    if ($result->num_rows > 0) {
        $row = mysqli_fetch_array($result);
        $originid = $row['origin_id'];
        $titleout = $row['title'];
        $year = date('Y', strtotime($row["time"]));
        $month = date('m', strtotime($row["time"]));
        $day = date('d', strtotime($row["time"]));
        $views = $row['views'];
        $metatitle = $row['metatitle'];
        $postcategoryid = $row['category_id'];
        $categoryname = $catarrall[$postcategoryid . '_' . $langid];
        $metakeywords = $row['metakeywords'];
        $metadescription = $row['metadescription'];
        preg_match($re_images, $row["thumbnail"], $thumbnail);
        preg_match_all($re_images, $row["productpics"], $productpics);

        $price = $row['price'];
        $originalprice = $row['originalprice'];
        $unit = $row['unit'];
        $onsale = $row['onsale'];
        $postweight = $row['weight'];
        $sku = $row['sku'];
        $moq = $row['moq'];
        $stock = $row['stock'];
        $shopping = $row['shopping'];
        $section = $row['section'];
        $section2 = $row['section2'];
        $section3 = $row['section3'];
        $section4 = $row['section4'];
        $remark = $row['remark'];

        $tpl = str_replace("{[posthref]}", $siteurl . $lang . '/' . $postid . '/' . myurlencode($titleout) . '/', $tpl);
        $customer_field1 = $row['customer_field1'];
        $customer_field2 = $row['customer_field2'];
        $customer_field3 = $row['customer_field3'];
        $customer_field4 = $row['customer_field4'];
        $customer_field5 = $row['customer_field5'];
        $customer_name1 = $row['customer_name1'];
        $customer_name2 = $row['customer_name2'];
        $customer_name3 = $row['customer_name3'];
        $customer_name4 = $row['customer_name4'];
        $customer_name5 = $row['customer_name5'];
        $contentout = $row['content'];
        $contentout2 = $row['content2'];
        $contentout3 = $row['content3'];
        $contentout4 = $row['content4'];
        if (!is_file($tplpath . '/' . $row['tplname'])) {
            $tpl = get($base . $tplpath . '/page.html');
        } else {
            $tpl = get($base . $tplpath . '/' . $row['tplname']);
        }
        $sql = "update x_post set views=views+1 where status=1 and id='$postid' and site_id='$siteid'";
        mysqli_query($con, $sql);
    }
}


//替换模板文件中的包含文件
$reTag = "/{\#(([a-z0-9]+)\.(html|htm){1})\#}/i";
preg_match_all($reTag, $tpl, $matches);
for ($i = 0; $i < count($matches[0]); $i++) {
    $x_value = $matches[1][$i];
    if (file_exists($tplpath . '/' . $x_value)) {
        $tpl = str_replace($matches[0][$i], get($base . $tplpath . '/' . $x_value), $tpl);
    }
}
//替换js的文件内容
$tpl = str_replace("{[include]}", get($base . "html/include.html"), $tpl);

//替换引用的js和css
$reTag = "/{%(\w+)%}/i";
preg_match_all($reTag, $tpl, $matches);
for ($i = 0; $i < count($matches[0]); $i++) {
    if (array_key_exists($matches[1][$i], $include)) {
        $tpl = str_replace($matches[0][$i], $include[$matches[1][$i]], $tpl);
    } else {
        $tpl = str_replace($matches[0][$i], '', $tpl);
    }
}


$tpl = $tpl . get($base . "html/js.html");
if ($pagetype == 'cart') {
    $tpl = $tpl . get($base . "html/cartjs.html");
}
if ($pagetype == 'gallery') {
    $tpl = $tpl . get($base . "html/galleryjs.html");
}

//对于非高级付费用户，替换模板文件中的添加到购物车部分
if ($shopswitch == false || $themejson->{"shopping"} == false) {
    if ($pagetype == 'cart' || $pagetype == 'client' || $pagetype == 'checkout' || $pagetype == 'complete') {
        Header("Location: " . $siteurl . $langurl);
    }
    $reTag = "/{shop}([\s\S]*?){\/shop}/i";
    preg_match_all($reTag, $tpl, $matches);
    for ($i = 0; $i < count($matches[0]); $i++) {
        $tpl = str_replace($matches[0][$i], "", $tpl);
    }
} else {
    $tpl = str_replace("{shop}", "", $tpl);
    $tpl = str_replace("{/shop}", "", $tpl);
}
//对于单语种站点，替换模板文件中的商城部分
if ($onesite == 1) {
    $reTag = "/{onesite}([\s\S]*?){\/onesite}/i";
    preg_match_all($reTag, $tpl, $matches);
    for ($i = 0; $i < count($matches[0]); $i++) {
        $tpl = str_replace($matches[0][$i], "", $tpl);
    }
} else {
    $tpl = str_replace("{onesite}", "", $tpl);
    $tpl = str_replace("{/onesite}", "", $tpl);
}
//文章相关
if ($pagetype == 'page') {
    //显示文章标签
    $pagetags = array();
    for ($i = 1; $i <= 4; $i++) {
        if ($row['tag' . $i] != '' && $row['tag' . $i] != null) {
            array_push($pagetags, $row['tag' . $i]);
        }
    }
    //显示文章的各种相关内容
    $tpl = str_replace("{[postid]}", $postid, $tpl);
    $tpl = str_replace("{[title]}", $titleout, $tpl);
    $tpl = str_replace("{[year]}", $year, $tpl);
    $tpl = str_replace("{[month]}", $month, $tpl);
    $tpl = str_replace("{[day]}", $day, $tpl);
    $tpl = str_replace("{[views]}", $views, $tpl);
    $tpl = str_replace("{[metatitle]}", $metatitle, $tpl);
    $tpl = str_replace("{[metakeywords]}", $metakeywords, $tpl);
    $tpl = str_replace("{[metadescription]}", $metadescription, $tpl);
    $tpl = str_replace("{[section]}", $section, $tpl);
    $tpl = str_replace("{[section2]}", $section2, $tpl);
    $tpl = str_replace("{[section3]}", $section3, $tpl);
    $tpl = str_replace("{[section4]}", $section4, $tpl);
    $tpl = str_replace("{[content]}", $contentout, $tpl);
    $tpl = str_replace("{[content2]}", $contentout2, $tpl);
    $tpl = str_replace("{[content3]}", $contentout3, $tpl);
    $tpl = str_replace("{[content4]}", $contentout4, $tpl);
    $tpl = str_replace("{[thumbnail]}", $thumbnail[1], $tpl);
    $tpl = str_replace("{[price]}", $price, $tpl);
    $tpl = str_replace("{[originalprice]}", $originalprice, $tpl);
    $tpl = str_replace("{[unit]}", $unit, $tpl);
    $tpl = str_replace("{[moq]}", $moq, $tpl);
    $tpl = str_replace("{[stock]}", $stock, $tpl);
    $tpl = str_replace("{[onsale]}", $onsale, $tpl);
    $tpl = str_replace("{[weight]}", $postweight, $tpl);
    $tpl = str_replace("{[sku]}", $sku, $tpl);
    $tpl = str_replace("{[posthref]}", $siteurl . $lang . '/' . $postid . '/' . myurlencode($titleout) . '/', $tpl);
    $tpl = str_replace("{[remark]}", $remark, $tpl);
    //显示下一篇文章
    $sql = "select * from x_post where status=1 and id>'$postid' and category_id='$postcategoryid' and lang_id='$langid' and site_id='$siteid' order by id limit 1";
    $result = mysqli_query($con, $sql);
    if ($result->num_rows == 0) {
        $tpl = str_replace('{[next]}', '<a href="###">' . $arr["none"] . '</a>', $tpl);
        $tpl = str_replace('{[nextpure]}', '<a href="###">' . $arr["none"] . '</a>', $tpl);
    } else {
        $row = mysqli_fetch_array($result);
        $tpl = str_replace('{[next]}', '<a href="' . $siteurl . $langurl . $row["id"] . "/" . myurlencode($row["title"]) . '/" >' . $row['title'] . ' <i class="fa fa-long-arrow-right" aria-hidden="true"></i></a>', $tpl);
        $tpl = str_replace('{[nextpure]}', '<a href="' . $siteurl . $langurl . $row["id"] . "/" . myurlencode($row["title"]) . '/" >' . $row['title'] . '</a>', $tpl);
    }
//显示上一篇文章
    $sql = "select * from x_post where status=1 and id<'$postid' and category_id='$postcategoryid' and lang_id='$langid' and site_id='$siteid' order by id desc limit 1";
    $result = mysqli_query($con, $sql);
    if ($result->num_rows == 0) {
        $tpl = str_replace('{[previous]}', '<a href="###">' . $arr["none"] . '</a>', $tpl);
        $tpl = str_replace('{[previouspure]}', '<a href="###">' . $arr["none"] . '</a>', $tpl);
    } else {
        $row = mysqli_fetch_array($result);
        $tpl = str_replace('{[previous]}', '<a href="' . $siteurl . $langurl . $row["id"] . "/" . myurlencode($row["title"]) . '/" ><i class="fa fa-long-arrow-left" aria-hidden="true"></i> ' . $row['title'] . '</a>', $tpl);
        $tpl = str_replace('{[previouspure]}', '<a href="' . $siteurl . $langurl . $row["id"] . "/" . myurlencode($row["title"]) . '/" >' . $row['title'] . '</a>', $tpl);
    }
    //显示产品图片
    $reTag = "/{pt\:productpics_([0-9]+)}([\s\S]*?){\/pt}/i";
    preg_match_all($reTag, $tpl, $matches);
    for ($l = 0; $l < count($matches[0]); $l++) {
        if (isset($matches[0][$l])) {
            unset($arrdata);
            $limit = $matches[1][$l];
            if ($limit > count($productpics[1])) {
                $limit = count($productpics[1]);
            }
            for ($i = 0; $i < $limit; $i++) {
                $arrdata[$i]['img'] = $productpics[1][$i];
            }
            $tpl = str_replace($matches[0][$l], looponce($matches[0][$l], $reTag, $arrdata), $tpl);
        }
    }
    //显示神州购买按钮
    $reTag = "/{pt\:customerfields_([0-9]+)}([\s\S]*?){\/pt}/i";
    preg_match_all($reTag, $tpl, $matches);
    for ($l = 0; $l < count($matches[0]); $l++) {
        if (isset($matches[0][$l])) {
            unset($arrdata);
            if ($customer_field1 != '' && $customer_name1 != '') {
                $arrdata[] = array("title" => $customer_name1, "href" => $customer_field1);
            }
            if ($customer_field2 != '' && $customer_name2 != '') {
                $arrdata[] = array("title" => $customer_name2, "href" => $customer_field2);
            }
            if ($customer_field3 != '' && $customer_name3 != '') {
                $arrdata[] = array("title" => $customer_name3, "href" => $customer_field3);
            }
            if ($customer_field4 != '' && $customer_name4 != '') {
                $arrdata[] = array("title" => $customer_name4, "href" => $customer_field4);
            }
            if ($customer_field5 != '' && $customer_name5 != '') {
                $arrdata[] = array("title" => $customer_name5, "href" => $customer_field5);
            }
            $tpl = str_replace($matches[0][$l], looponce($matches[0][$l], $reTag, $arrdata), $tpl);
        }
    }
    //////////////////////////
} else {
    $tpl = str_replace("{[title]}", "", $tpl);
}
//处理商城相关
$countryoption = '';
for ($i = 0; $i < count($shippingcountryarr); $i++) {
    $countryoption = $countryoption . '<option value="' . $countryweight[$shippingcountryarr[$i]] . '">' . $shippingcountryarr[$i] . '</a>';
}
$tpl = str_replace("{[deliverycountry]}", $countryoption, $tpl);
////////////////
if (substr($themename, 0, 1) == '*') {
    //$tpl = str_replace('{[themepath]}', $siteurl . $tplpath . '/', $tpl);
    $tpl = str_replace('{[themepath]}', $base . $tplpath . '/', $tpl);
} else {
    $tpl = str_replace('{[themepath]}', $base . $prefix . $sitename . '/userthemes/' . $themename . '/', $tpl);
}


//和分类相关的
if (array_key_exists($categoryname, $catarrori)) {
    $origincatid = $catarrori[$categoryname];
}
//读取分类的具体内容
if ($pagetype == 'category') {
    $sql = "select * from x_category where origin_id='$origincatid' and lang_id='$langid' and site_id='$siteid'";
    $result = mysqli_query($con, $sql);
    if ($result->num_rows > 0) {
        $row = mysqli_fetch_array($result);
        preg_match($re_images, $row["thumbnail"], $thumbnail);
        if ($thumbnail[1] == null) {
            $thumbnail2 = '';
        } else {
            $thumbnail2 = $thumbnail[1];
        }
        $tpl = str_replace('{[category_thumbnail]}', $thumbnail2, $tpl);
        preg_match($re_images, $row["banner"], $banner);
        if ($banner[1] == null) {
            $banner2 = '';
        } else {
            $banner2 = $banner[1];
        }
        $tpl = str_replace('{[category_banner]}', $banner2, $tpl);
        $tpl = str_replace('{[category_content]}', $row['content'], $tpl);
    }
}

//替换分类菜单
$catout_left = '';
$catout_div = '';
$catout_collapse = '';
$catout_3levels = '';
foreach ($catarr0 as $x => $x_value) {
    $subcats = '';
    $subcats2 = '';
    $subcats3 = '';
    $k = 0;
    for ($i = 0; $i < count($catarr1); $i++) {
        if ($subcatarr[$catarrori[$x] . '_' . $catarr1[$i]] != NULL) {
            unset($tmparr);
            foreach ($subcatarr2 as $y => $y_value) {
                if ($subcatarr[$catarrori[$x] . '_' . $catarr1[$i]] == $y_value) {
                    $tmparr = explode("_", $y);
                }
            }
            $k++;
            $subsubcats = '';
            if (strpos($tpl, "{[categories_3levels]}")) {
                $sql = "select * from x_post where status=1 and lang_id='$langid' and site_id='$siteid' and (category_id='$tmparr[1]' or category_id2='$tmparr[1]' )   order by sorting desc,id desc limit 20";
                $result = mysqli_query($con, $sql);
                if ($result->num_rows > 0) {
                    while ($row = mysqli_fetch_array($result)) {
                        $subsubcats = $subsubcats . "<li><a href='" . $siteurl . $langurl . $row["id"] . "/" . myurlencode($row["title"]) . "/'>" . $row["title"] . "</a></li>";
                    }
                }
            }
            if ($subsubcats == '') {
                $subcats3 = $subcats3 . "<li><a href='" . $siteurl . $langurl . 'category_' . $tmparr[1] . '/' . myurlencode($subcatarr[$catarrori[$x] . '_' . $catarr1[$i]]) . "/'>" . $subcatarr[$catarrori[$x] . '_' . $catarr1[$i]] . "</a></li>";
            } else {
                $subcats3 = $subcats3 . "<li class=dropdown><a href='" . $siteurl . $langurl . 'category_' . $tmparr[1] . '/' . myurlencode($subcatarr[$catarrori[$x] . '_' . $catarr1[$i]]) . "/' class=dropdown-toggle data-toggle=dropdown>" . $subcatarr[$catarrori[$x] . '_' . $catarr1[$i]] . "</a><ul class=dropdown-menu animated>" . $subsubcats . "</ul></li>";
            }
            $subcats = $subcats . "<li><a href='" . $siteurl . $langurl . 'category_' . $tmparr[1] . '/' . myurlencode($subcatarr[$catarrori[$x] . '_' . $catarr1[$i]]) . "/'>" . $subcatarr[$catarrori[$x] . '_' . $catarr1[$i]] . "</a></li>";
            $subcats2 = $subcats2 . "<div class='mlb_languages_div'><a href='" . $siteurl . $langurl . 'category_' . $tmparr[1] . '/' . myurlencode($subcatarr[$catarrori[$x] . '_' . $catarr1[$i]]) . "/'>" . $subcatarr[$catarrori[$x] . '_' . $catarr1[$i]] . "</a></div>";
        }
    }
    if ($k % 4 != 0) {
        for ($j = 0; $j < (4 - $k % 4); $j++) {
            $subcats2 = $subcats2 . '<div class="mlb_languages_div">&nbsp;</div>';
        }
    }
    if ($subcats == '') {
        $catout = $catout . "<li><a href='" . $siteurl . $langurl . 'category_' . $catarrori[$x] . '/' . myurlencode($x) . "/'>" . $x . "</a></li>";
        $catout_left = $catout_left . "<li><a href='" . $siteurl . $langurl . 'category_' . $catarrori[$x] . '/' . myurlencode($x) . "/'>" . $x . "</a></li>";
        $catout_div = $catout_div . "<li><a href='" . $siteurl . $langurl . 'category_' . $catarrori[$x] . '/' . myurlencode($x) . "/'>" . $x . "</a></li>";
        $catou_3levels = $catou_3levels . "<li><a href='" . $siteurl . $langurl . 'category_' . $catarrori[$x] . '/' . myurlencode($x) . "/'>" . $x . "</a></li>";
    } else {
        $catout = $catout . "<li class=dropdown><a href='#' class=dropdown-toggle data-toggle=dropdown>" . $x . "<b class='caret'></b></a><ul class=dropdown-menu>" . $subcats . '</ul></li>';
        $catout_left = $catout_left . "<li><a href='#mlb_collapse" . $x_value . "' data-toggle=collapse>" . $x . "</a></li><ul id='mlb_collapse" . $x_value . "' class='nav nav-list panel-collapse collapse'>" . $subcats . '</ul>';
        $catout_div = $catout_div . '<li><a data-toggle="collapse" href="#category_' . $x_value . '">' . $x . '<b class="caret"></b></a></li>';
        $catout_collapse = $catout_collapse . '<div class="panel-collapse collapse" id="category_' . $x_value . '">
                                   <div class="panel panel-default">
                        <div class="panel-body text-center">
                           ' . $subcats2 . '
                        </div>
                                 </div>
            </div>';
        $catou_3levels = $catou_3levels . "<li class=dropdown><a href='" . $siteurl . $langurl . 'category_' . $catarrori[$x] . '/' . myurlencode($x) . "/' class=dropdown-toggle data-toggle=dropdown>" . $x . "</a><ul class=dropdown-menu>" . $subcats3 . '</ul></li>';
    }
}


$tpl = str_replace("{[categories]}", $catout, $tpl);
$tpl = str_replace("{[categories_left]}", $catout_left, $tpl);
$tpl = str_replace("{[categories_div]}", $catout_div, $tpl);
$tpl = str_replace("{[categories_collapse]}", $catout_collapse, $tpl);
$tpl = str_replace("{[categories_3levels]}", $catou_3levels, $tpl);
//直接显示分类及页面菜单，不区分父目录或子目录
$reTag = "/{pt\:categories_([0-9]+)_([0-9]+)}([\s\S]*?){\/pt}/i";     //显示几个分类，每个分类显示几个页面，这个和其他的有点不一样
preg_match_all($reTag, $tpl, $matches);
for ($l = 0; $l < count($matches[0]); $l++) {
    if (isset($matches[0][$l])) {
        unset($arrdata);
        $pagenum = $matches[2][$l];
        $catnum = $matches[1][$l];
        $k = 0;
        foreach ($catarrori as $x => $x_value) {
            $arrdata[$k]['title'] = $x;
            $arrdata[$k]['href'] = $siteurl . $langurl . 'category' . $categoryoriginid . '/' . myurlencode($x) . '/';
            $catids = $x_value;
            $sql = "select * from x_post where status=1 and lang_id='$langid' and site_id='$siteid' and (category_id in ($catids) or category_id2 in ($catids)  )  order by sorting desc,id desc limit $pagenum";

            $result = mysqli_query($con, $sql);
            if ($result->num_rows > 0) {
                $j = 0;
                while ($row = mysqli_fetch_array($result)) {
                    preg_match($re_images, $row["thumbnail"], $thumbnail);
                    if ($thumbnail[1] == null) {
                        $thumbnail2 = '/img/default.png';
                    } else {
                        $thumbnail2 = $thumbnail[1];
                    }
                    $arrdata[$k]['content'][$j]['title'] = $row["title"];
                    $arrdata[$k]['content'][$j]["href"] = $siteurl . $langurl . $row["id"] . "/" . myurlencode($row["title"]) . "/";
                    $arrdata[$k]['content'][$j]["img"] = $thumbnail2;
                    if ($row["brief"] != null && $row["brief"] != '') {
                        $arrdata[$k]['content'][$j]['brief'] = $row["brief"];
                    } else {
                        $arrdata[$k]['content'][$j]['brief'] = mb_substr(strip_tags($row["content"]), 0, 80, 'utf-8');
                    }
                    $j++;
                }
            }
            $k++;
            if ($k > $catnum) {
                break;
            }
        }
        $tpl = str_replace($matches[0][$l], looptwice($matches[0][$l], $reTag, $arrdata), $tpl);
    }
}
//只显示直接目录
$reTag = "/{pt\:categories_([0-9]+)}([\s\S]*?){\/pt}/i";
preg_match_all($reTag, $tpl, $matches);
for ($l = 0; $l < count($matches[0]); $l++) {
    if (isset($matches[0][$l])) {
        unset($arrdata);
        $limit = $matches[1][$l];
        $i = 0;
        foreach ($catarr0 as $x => $x_value) {
            $arrdata[$i]['title'] = $x;
            $arrdata[$i]['href'] = $siteurl . $langurl . 'category_' . $catarrori[$x] . '/' . myurlencode($x) . '/';
            $i++;
            if ($i > $limit) {
                break;
            }
        }
        $tpl = str_replace($matches[0][$l], looponce($matches[0][$l], $reTag, $arrdata), $tpl);
    }
}
//展开当前分类所属父目录的所有子分类，显示子目录带链接，不显示页面，也不显示孙级目录
if ($pagetype == 'category' || $pagetype == 'page') {
    $out = '';
    $fathercatid = '';
    $x_value = $categoryname;
    //echo $x_value;
    //echo $catarrori[$x_value];
    foreach ($subcatarr2 as $z => $z_value) {
        unset($tmparr2);
        $tmparr2 = explode("_", $z);
        if ($tmparr2[1] == $catarrori[$x_value]) {
            $fathercatid = $tmparr2[0];
        }
    }
    //echo $fathercatid;
    foreach ($subcatarr2 as $z => $z_value) {
        unset($tmparr2);
        $tmparr2 = explode("_", $z);
        if ($tmparr2[0] == $fathercatid) {
            if ($x_value == $z_value) {
                $out = $out . '<li class=active><a href="' . $siteurl . $langurl . 'category_' . $catarrori[$z_value] . '/' . myurlencode($z_value) . '/">' . $z_value . "</a></li>";
            } else {
                $out = $out . '<li><a href="' . $siteurl . $langurl . 'category_' . $catarrori[$z_value] . '/' . myurlencode($z_value) . '/">' . $z_value . "</a></li>";
            }
        }
    }
    //die;
    $tpl = str_replace("{[category_expandsub]}", $out, $tpl);
}

//显示当前目录的子目录
if ($pagetype == 'category' || $pagetype == 'page') {
    $reTag = "/{pt\:subcategories_([0-9]+)}([\s\S]*?){\/pt}/i";
    preg_match_all($reTag, $tpl, $matches);
    for ($l = 0; $l < count($matches[0]); $l++) {
        if (isset($matches[0][$l])) {
            unset($arrdata);
            $limit = $matches[1][$l];
            $i = 0;
            foreach ($subcatarr as $y => $y_value) {
                unset($tmparr);
                $tmparr = explode("_", $y);
                if ($tmparr[0] == $categoryoriginid) {
                    $arrdata[$i]['title'] = $catarrall[$tmparr[1] . '_' . $langid];
                    $arrdata[$i]['href'] = $siteurl . $langurl . 'category_' . $catarrori[$y_value] . '/' . myurlencode($catarrall[$tmparr[1] . '_' . $langid]) . '/';
                    $i++;
                    if ($i > $limit) {
                        break;
                    }
                }
            }
            $tpl = str_replace($matches[0][$l], looponce($matches[0][$l], $reTag, $arrdata), $tpl);
        }
    }
}
//显示某一目录的子目录
$reTag = "/{pt\:subcategories_([a-z]+)_([0-9]+)}([\s\S]*?){\/pt}/i";
preg_match_all($reTag, $tpl, $matches);
for ($l = 0; $l < count($matches[0]); $l++) {
    if (isset($matches[0][$l])) {
        unset($arrdata);
        $limit = $matches[2][$l];
        $x_value = $matches[1][$l];
        $i = 0;
        if (array_key_exists($x_value, $cataliasidarr)) {
            $x = $cataliasidarr[$x_value];
            foreach ($subcatarr as $y => $y_value) {
                unset($tmparr);
                $tmparr = explode("_", $y);
                if ($tmparr[0] == $x) {
                    $arrdata[$i]['title'] = $catarrall[$tmparr[1] . '_' . $langid];
                    $arrdata[$i]['href'] = $siteurl . $langurl . 'category_' . $catarrori[$y_value] . '/' . myurlencode($catarrall[$tmparr[1] . '_' . $langid]) . '/';
                    $i++;
                    if ($i > $limit) {
                        break;
                    }
                }
            }
        }
        $tpl = str_replace($matches[0][$l], looponce2($matches[0][$l], $reTag, $arrdata), $tpl);
    }
}
//展开某一指定分类，子目录做导航，页面做下拉
$reTag = "/{\[category_expandsubpost_([a-z]+)\]}/i";
preg_match_all($reTag, $tpl, $matches);
for ($i = 0; $i < count($matches[0]); $i++) {
    $out = '';
    $x_value = $matches[1][$i];
    if (array_key_exists($x_value, $cataliasidarr)) {
        $x = $cataliasidarr[$x_value];
        foreach ($subcatarr as $y => $y_value) {
            unset($tmparr);
            $tmparr = explode("_", $y);
            if ($tmparr[0] == $x) {
                $subcats = '';
                $tmpid = $originidarr["{" . $tmparr[1] . "}"];
                $sql = "select * from x_post where status=1 and (category_id='$tmpid' or category_id2='$tmpid') and lang_id='$langid' and site_id='$siteid'  order by sorting desc";
                $result = mysqli_query($con, $sql);
                while ($row = mysqli_fetch_array($result)) {
                    $subcats = $subcats . "<li><a href='" . $siteurl . $langurl . $row["id"] . "/" . myurlencode($row["title"]) . "/'>" . $row["title"] . "</a></li>";
                }
                if ($subcats == '') {
                    $out = $out . '<li><a href="' . $siteurl . $langurl . 'category_' . $catarrori[$y_value] . '/' . myurlencode($catarrall[$tmparr[1] . '_' . $langid]) . '/">' . $catarrall[$tmparr[1] . '_' . $langid] . "</a></li>";
                } else {
                    $out = $out . "<li class=dropdown><a href='#' class=dropdown-toggle data-toggle=dropdown>" . $catarrall[$tmparr[1] . '_' . $langid] . "</a><ul class=dropdown-menu>" . $subcats . '</ul></li>';
                }
            }
        }
        $tpl = str_replace($matches[0][$i], $out, $tpl);
    }
}

//展开某一指定分类，子目录做导航，无格式，需要用jq在页面里设置
$reTag = "/{\[category_expandsubpostpure_([a-z]+)\]}/i";
preg_match_all($reTag, $tpl, $matches);
for ($i = 0; $i < count($matches[0]); $i++) {
    $out = '';
    $x_value = $matches[1][$i];
    if (array_key_exists($x_value, $cataliasidarr)) {
        $x = $cataliasidarr[$x_value];
        foreach ($subcatarr as $y => $y_value) {
            unset($tmparr);
            $tmparr = explode("_", $y);
            if ($tmparr[0] == $x) {
                $subcats = '';
                $tmpid = $originidarr["{" . $tmparr[1] . "}"];
                $sql = "select * from x_post where status=1 and (category_id='$tmpid' or category_id2='$tmpid') and lang_id='$langid' and site_id='$siteid'  order by sorting desc";
                $result = mysqli_query($con, $sql);
                while ($row = mysqli_fetch_array($result)) {
                    $subcats = $subcats . "<li><a href='" . $siteurl . $langurl . $row["id"] . "/" . myurlencode($row["title"]) . "/'>" . $row["title"] . "</a></li>";
                }
                if ($subcats == '') {
                    $out = $out . '<li><a href="' . $siteurl . $langurl . 'category_' . $catarrori[$y_value] . '/' . myurlencode($catarrall[$tmparr[1] . '_' . $langid]) . '/">' . $catarrall[$tmparr[1] . '_' . $langid] . "</a></li>";
                } else {
                    $out = $out . "<li><a href='#'>" . $catarrall[$tmparr[1] . '_' . $langid] . "</a><ul>" . $subcats . '</ul></li>';
                }
            }
        }
        $tpl = str_replace($matches[0][$i], $out, $tpl);
    }
}

//展开某一指定分类，页面做导航，不考虑子目录
$reTag = "/{\[category_post_([a-z]+)\]}/i";
preg_match_all($reTag, $tpl, $matches);
for ($i = 0; $i < count($matches[0]); $i++) {
    $out = '';
    $x_value = $matches[1][$i];
    if (array_key_exists($x_value, $cataliasidarr)) {
        $x = $cataliasidarr[$x_value];

        $tmpid = $originidarr["{" . $x . "}"];
        $sql = "select * from x_post where status=1 and (category_id='$tmpid' or category_id2='$tmpid') and lang_id='$langid' and site_id='$siteid'  order by sorting desc";
        $result = mysqli_query($con, $sql);
        while ($row = mysqli_fetch_array($result)) {
            $subcats = $subcats . "<li><a href='" . $siteurl . $langurl . $row["id"] . "/" . myurlencode($row["title"]) . "/'>" . $row["title"] . "</a></li>";
        }
        $out = $subcats;
        //$out = $out . '<li><a href="' . $siteurl . $langurl . 'category_' . $catarrori[$y_value] . '/' . myurlencode($catarrall[$tmparr[1] . '_' . $langid]) . '/">' . $catarrall[$tmparr[1] . '_' . $langid] . "</a></li>";


        $tpl = str_replace($matches[0][$i], $out, $tpl);
    }
}

//展开全部分类,折叠菜单，不显示页面链接，留作备用
$out = '';
foreach ($catarr0 as $x => $x_value) {
    $out1 = '';
    //x是祖级
    foreach ($subcatarr2 as $y => $y_value) {
        unset($tmparr);
        $tmparr = explode("_", $y);
        //y是父级
        if ($tmparr[0] == $x_value) {
            $out2 = '';
            //z是子级
            foreach ($subcatarr2 as $z => $z_value) {
                unset($tmparr2);
                $tmparr2 = explode("_", $z);
                if ($tmparr2[0] == $tmparr[1]) {
                    $out2 = $out2 . ' <li><a href="' . $siteurl . $langurl . 'category_' . $catarrori[$z_value] . '/' . myurlencode($z_value) . '/">' . $z_value . '</a></li>';
                }
            }
            if ($out2 != '') {
                $out1 = $out1 . '<li data-toggle="collapse"  data-target="#coll' . $y . '"><a href="###">' . $y_value . '</a><ul id="coll' . $y . '" class="collapse">' . $out2 . '</ul></li>';
            } else {
                $out1 = $out1 . '<li><a href="' . $siteurl . $langurl . 'category_' . $catarrori[$y_value] . '/' . myurlencode($y_value) . '/">' . $y_value . '</a></li>';
            }
        }
    }
    if ($out1 != '') {
        $out = $out . '<li data-toggle="collapse"  data-target="#coll' . $x_value . '"><a href="###">' . $x . '</a><ul id="coll' . $x_value . '" class="collapse">' . $out1 . '</ul></li>';
    } else {
        $out = $out . '<li><a href="' . $siteurl . $langurl . 'category_' . $catarrori[$x] . '/' . myurlencode($x) . '/">' . $x . '</a></li>';
    }
}
$tpl = str_replace('{[categories_collleft]}', $out, $tpl);
//替换本页分类名称
$tpl = str_replace('{[category]}', $categoryname, $tpl);
//替换分类的超链接
if ($categoryname != '') {
    $tpl = str_replace('{[categoryhref]}', $siteurl . $langurl . 'category_' . $catarrori[$categoryname] . '/' . myurlencode($categoryname) . '/', $tpl);
} else {
    $tpl = str_replace('{[categoryhref]}', '', $tpl);
}
//替换父目录的名称和链接
foreach ($subcatarr as $x => $x_value) {
    if ($x_value == $categoryname) {
        unset($tmparr);
        $tmparr = explode("_", $x);
        $father = $catarrall[$tmparr[0] . '_' . $langid];              //不要把上面的数组和这里连起来写，sudu不支持
        $fatherhref = $siteurl . $langurl . 'category_' . $catarrori[$father] . '/' . myurlencode($father) . '/';
        $fatherli = '<li><a href="' . $siteurl . $langurl . 'category_' . $catarrori[$father] . '/' . myurlencode($father) . '/">' . $father . '</a></li>';
    }
}
$tpl = str_replace("{[father]}", $father, $tpl);
$tpl = str_replace("{[fatherhref]}", $fatherhref, $tpl);
$tpl = str_replace("{[fatherli]}", $fatherli, $tpl);

//按序号替换分类名称
$reTag = "/{\[category_(\d+)\]}/i";
preg_match_all($reTag, $tpl, $matches);
for ($i = 0; $i < count($matches[0]); $i++) {
    $start = $matches[1][$i];
    $out = $catarr2[$start];
    $tpl = str_replace($matches[0][$i], $out, $tpl);
}
//按别名替换分类名称
$reTag = "/{\[category_([a-z]+)\]}/i";
preg_match_all($reTag, $tpl, $matches);
for ($i = 0; $i < count($matches[0]); $i++) {
    $start = $matches[1][$i];
    if (array_key_exists($start, $cataliasarr)) {
        $out = $cataliasarr[$start];
        $tpl = str_replace($matches[0][$i], $out, $tpl);
    }
}
//按序号替换分类地址
$reTag = "/{\[categoryhref_(\d+)\]}/i";
preg_match_all($reTag, $tpl, $matches);
for ($i = 0; $i < count($matches[0]); $i++) {
    $start = $matches[1][$i];
    $out = $siteurl . $langurl . 'category_' . $catarrori[$catarr2[$start]] . '/' . myurlencode($catarr2[$start]) . "/";
    $tpl = str_replace($matches[0][$i], $out, $tpl);
}
//按别名替换分类地址
$reTag = "/{\[categoryhref_([a-z]+)\]}/i";
preg_match_all($reTag, $tpl, $matches);
for ($i = 0; $i < count($matches[0]); $i++) {
    $start = $matches[1][$i];
    $out = $siteurl . $langurl . 'category_' . $catarrori[$cataliasarr[$start]] . '/' . myurlencode($cataliasarr[$start]) . "/";
    $tpl = str_replace($matches[0][$i], $out, $tpl);
}

//按序号替换分类缩略图
$reTag = "/{\[categorythumbnail_(\d+)\]}/i";
preg_match_all($reTag, $tpl, $matches);
for ($i = 0; $i < count($matches[0]); $i++) {
    $start = $matches[1][$i];
    $out = $catthumbnails[$start]['thumbnail'];
    $tpl = str_replace($matches[0][$i], $out, $tpl);
}

//按别名替换分类缩略图
$reTag = "/{\[categorythumbnail_([a-z]+)\]}/i";
preg_match_all($reTag, $tpl, $matches);
for ($i = 0; $i < count($matches[0]); $i++) {
    $start = $matches[1][$i];
    foreach ($catthumbnails as $item) {
        if ($item['alias'] == $start) {
            $out = $item['thumbnail'];
            $tpl = str_replace($matches[0][$i], $out, $tpl);
        }
    }
}
//按序号替换分类banner
$reTag = "/{\[categorybanner_(\d+)\]}/i";
preg_match_all($reTag, $tpl, $matches);
for ($i = 0; $i < count($matches[0]); $i++) {
    $start = $matches[1][$i];
    $out = $catbanners[$start]['banner'];
    $tpl = str_replace($matches[0][$i], $out, $tpl);
}
//按别名替换分类banner
$reTag = "/{\[categorybanner_([a-z]+)\]}/i";
preg_match_all($reTag, $tpl, $matches);
for ($i = 0; $i < count($matches[0]); $i++) {
    $start = $matches[1][$i];
    foreach ($catbanners as $item) {
        if ($item['alias'] == $start) {
            $out = $item['banner'];
            $tpl = str_replace($matches[0][$i], $out, $tpl);
        }
    }
}
//提取某分类指定长度文本
$reTag = "/{\[category_([a-z]+)_(\d+)\]}/i";
preg_match_all($reTag, $tpl, $matches);
for ($i = 0; $i < count($matches[0]); $i++) {
    $out = '';
    $start = $matches[1][$i];
    $num = $matches[2][$i];
    if ($cataliasarr[$start] != null) {
        $tmpid = $catarrori[$cataliasarr[$start]];
        $sql = "select content from x_category where origin_id='$tmpid' and lang_id='$langid' and site_id='$siteid'";

        $result = mysqli_query($con, $sql);
        $row = mysqli_fetch_array($result);
        if ($num == 0) {
            $out = $row["content"];
        } else {
            $out = br_mb_substr($row["content"], $num);
        }
        $tpl = str_replace($matches[0][$i], $out, $tpl);
    }
}
//展开当前页面爷分类，子目录做导航，无格式，需要用jq在页面里设置
if ($pagetype == 'page') {
    $reTag = "/{\[category_expandsubpostpure\]}/i";
    preg_match_all($reTag, $tpl, $matches);
    for ($i = 0; $i < count($matches[0]); $i++) {
        $out = '';
        $x = $catarrori[$father];
        foreach ($subcatarr as $y => $y_value) {
            unset($tmparr);
            $tmparr = explode("_", $y);
            if ($tmparr[0] == $x) {
                $subcats = '';
                $tmpid = $originidarr["{" . $tmparr[1] . "}"];
                $sql = "select * from x_post where status=1 and (category_id='$tmpid' or category_id2='$tmpid') and lang_id='$langid' and site_id='$siteid'  order by sorting desc";
                $result = mysqli_query($con, $sql);
                while ($row = mysqli_fetch_array($result)) {
                    $subcats = $subcats . "<li><a href='" . $siteurl . $langurl . $row["id"] . "/" . myurlencode($row["title"]) . "/'>" . $row["title"] . "</a></li>";
                }
                if ($subcats == '') {
                    $out = $out . '<li><a href="' . $siteurl . $langurl . 'category_' . $catarrori[$y_value] . '/' . myurlencode($catarrall[$tmparr[1] . '_' . $langid]) . '/">' . $catarrall[$tmparr[1] . '_' . $langid] . "</a></li>";
                } else {
                    $out = $out . "<li><a href='#'>" . $catarrall[$tmparr[1] . '_' . $langid] . "</a><ul>" . $subcats . '</ul></li>';
                }
            }
        }
        $tpl = str_replace($matches[0][$i], $out, $tpl);
    }
}
//按别名替换页面名称，显示为纯文字
$reTag = "/{\[page_([a-z]+)\]}/i";
preg_match_all($reTag, $tpl, $matches);
for ($i = 0; $i < count($matches[0]); $i++) {
    $start = $matches[1][$i];
    $out = $singlealias['{' . $start . '}'];
    $tpl = str_replace($matches[0][$i], $out, $tpl);
}
//按别名替换页面名称链接，显示为链接
$reTag = "/{\[pagehref_([a-z]+)\]}/i";
preg_match_all($reTag, $tpl, $matches);
for ($i = 0; $i < count($matches[0]); $i++) {
    $start = $matches[1][$i];
    if (array_key_exists('{' . $start . '}', $singlealiasid)) {
        $out = $siteurl . $langurl . $singlealiasid['{' . $start . '}'] . "/" . myurlencode($singlealias['{' . $start . '}']) . "/";
        $tpl = str_replace($matches[0][$i], $out, $tpl);
    }
}
//提取某页面指定长度文本
$reTag = "/{\[page_([a-z]+)_(\d+)\]}/i";
preg_match_all($reTag, $tpl, $matches);
for ($i = 0; $i < count($matches[0]); $i++) {
    $out = '';
    $start = $matches[1][$i];
    $num = $matches[2][$i];
    if (array_key_exists('{' . $start . '}', $singlealiasid)) {
        $realid = $singlealiasid['{' . $start . '}'];
        $sql = "select content from x_post where status=1 and id='$realid' and lang_id='$langid' and site_id='$siteid'" ;
        $result = mysqli_query($con, $sql);
        $row = mysqli_fetch_array($result);
        if ($num == 0) {
            $out = $row["content"];
        } else {
            $out = br_mb_substr($row["content"], $num);
        }
        $tpl = str_replace($matches[0][$i], $out, $tpl);
    }
}
//提取某页面图片地址
$reTag = "/{\[page_([a-z]+)_img\]}/i";
preg_match_all($reTag, $tpl, $matches);
for ($i = 0; $i < count($matches[0]); $i++) {
    $out = '';
    $start = $matches[1][$i];
    $num = $matches[2][$i];
    $realid = $singlealiasid['{' . $start . '}'];
    $sql = "select thumbnail,productpics,content from x_post where status=1 and id='$realid' and lang_id='$langid' and site_id='$siteid' " ;
    $result = mysqli_query($con, $sql);
    $row = mysqli_fetch_array($result);
    preg_match($re_images, $row["thumbnail"], $thumbnail);
    if ($thumbnail[1] == null) {
        $thumbnail2 = '/img/default.png';
    } else {
        $thumbnail2 = $thumbnail[1];
    }
    $tpl = str_replace($matches[0][$i], $thumbnail2, $tpl);
}
//注意如果某页面想要能够单独获得，必须指定一个别名
//替换当前语种
$tpl = str_replace("{[currentlang]}", $toarr3[$langid], $tpl);
$tpl = str_replace("{[currentflag]}", $base . "img/flags/" . $langarr[$langid] . ".png", $tpl);

//替换语言种类
$out_btn_flag_nav = '';
$langarrdata = array();
$langarrdata_nomain = array();
if (count($langidsarr) > 1) {
    if ($pagetype != 'page') {
        for ($i = 0; $i < count($langidsarr); $i++) {
            $templangid = $langidsarr[$i];
            $thelang2 = $langjson->{'lang2'}->$templangid;
            $langarrdata[$i]['title'] = $thelang2[0]->{'locallang'};
            $langarrdata[$i]['short'] = $thelang2[0]->{'lang'};
            $langarrdata[$i]['img'] = $base . "img/flags/" . $langarr[$langidsarr[$i]] . ".png";
            if ($pagetype == 'index') {
                if ($homelangurl != '' && $homelang_id == $langidsarr[$i]) {
                    $langarrdata[$i]['href'] = $homelangurl;
                } else {
                    if (array_key_exists('{' . $langidsarr[$i] . '}', $domains)) {
                        $langarrdata[$i]['href'] = "http://" . $domains['{' . $langidsarr[$i] . '}'] . "/";
                    } else {
                        //$langarrdata[$i]['href'] = $mainurl . $thelang2[0]->{'lang'} . "/";
                        $langarrdata[$i]['href'] = $siteurl . $thelang2[0]->{'lang'} . "/";
                    }
                }
            }
            if ($pagetype == 'search') {
                if ($homelangurl != '' && $homelang_id == $langidsarr[$i]) {
                    $langarrdata[$i]['href'] = $homelangurl;
                } else {
                    if (array_key_exists('{' . $langidsarr[$i] . '}', $domains)) {
                        $langarrdata[$i]['href'] = "http://" . $domains['{' . $langidsarr[$i] . '}'] . "/search/" . $search . "/";
                    } else {
                        //$langarrdata[$i]['href'] = $mainurl . $thelang2[0]->{'lang'} . "/search/" . $search . "/";
                        $langarrdata[$i]['href'] = $siteurl . $thelang2[0]->{'lang'} . "/search/" . $search . "/";
                    }
                }
            }
            if ($pagetype == 'tag') {
                if ($homelangurl != '' && $homelang_id == $langidsarr[$i]) {
                    $langarrdata[$i]['href'] = $homelangurl;
                } else {
                    if (array_key_exists('{' . $langidsarr[$i] . '}', $domains)) {
                        $langarrdata[$i]['href'] = "http://" . $domains['{' . $langidsarr[$i] . '}'] . "/tag/" . $tag . "/";
                    } else {
                        //$langarrdata[$i]['href'] = $mainurl . $thelang2[0]->{'lang'} . "/tag/" . $tag . "/";
                        $langarrdata[$i]['href'] = $siteurl . $thelang2[0]->{'lang'} . "/tag/" . $tag . "/";
                    }
                }
            }
            if ($pagetype == 'category') {
                $page2 = '';
                if ($page != '') {
                    $page2 = $page . '/';
                } else {
                    $page2 = $page;
                }
                if ($homelangurl != '' && $homelang_id == $langidsarr[$i]) {
                    $langarrdata[$i]['href'] = $homelangurl;
                } else {
                    if (array_key_exists('{' . $langidsarr[$i] . '}', $domains)) {
                        $langarrdata[$i]['href'] = "http://" . $domains['{' . $langidsarr[$i] . '}'] . "/category_" . $categoryoriginid . "/" . $catarrall[$origincatid . '_' . $langidsarr[$i]] . "_" . $page . "/";
                    } else {
                        //$langarrdata[$i]['href'] = $mainurl . $thelang2[0]->{'lang'} . "/category_" . $categoryoriginid . "/" . $catarrall[$origincatid . '_' . $langidsarr[$i]] . "_" . $page . "/";
                        $langarrdata[$i]['href'] = $siteurl . $thelang2[0]->{'lang'} . "/category_" . $categoryoriginid . "/" . $catarrall[$origincatid . '_' . $langidsarr[$i]] . "_" . $page . "/";
                    }
                }
            }
            if ($pagetype == 'download' || $pagetype == 'gallery' || $pagetype == 'cart' || $pagetype == 'checkout' || $pagetype == 'complete' || $pagetype == 'client') {
                if ($homelangurl != '' && $homelang_id == $langidsarr[$i]) {
                    $langarrdata[$i]['href'] = $homelangurl;
                } else {
                    if (array_key_exists('{' . $langidsarr[$i] . '}', $domains)) {
                        $langarrdata[$i]['href'] = "http://" . $domains['{' . $langidsarr[$i] . '}'] . "/" . $pagetype . "/";
                    } else {
                        //$langarrdata[$i]['href'] = $mainurl . $thelang2[0]->{'lang'} . "/" . $pagetype . "/";
                        $langarrdata[$i]['href'] = $siteurl . $thelang2[0]->{'lang'} . "/" . $pagetype . "/";
                    }
                }
            }
            if ($langidsarr[$i] != $langid) {
                $langarrdata_nomain[$i]['title'] = $langarrdata[$i]['title'];
                $langarrdata_nomain[$i]['short'] = $langarrdata[$i]['short'];
                $langarrdata_nomain[$i]['img'] = $langarrdata[$i]['img'];
                $langarrdata_nomain[$i]['href'] = $langarrdata[$i]['href'];
            }
        }
    }
    if ($pagetype == 'page') {
        $titles = array();
        $postids = array();
        $sql = "select id,title,lang_id from x_post where status=1 and origin_id='$originid' and site_id='$siteid'" ;
        $result = mysqli_query($con, $sql);
        while ($row = mysqli_fetch_array($result)) {
            $titles[$row['lang_id']] = $row['title'];
            $postids[$row['lang_id']] = $row['id'];
        }
        for ($i = 0; $i < count($langidsarr); $i++) {
            $templangid = $langidsarr[$i];
            $thelang2 = $langjson->{'lang2'}->$templangid;
            $langarrdata[$i]['title'] = $thelang2[0]->{'locallang'};
            $langarrdata[$i]['short'] = $thelang2[0]->{'lang'};
            $langarrdata[$i]['img'] = $base . "img/flags/" . $langarr[$langidsarr[$i]] . ".png";
            if ($postids[$langidsarr[$i]] !== null) {
                if ($homelangurl != '' && $homelang_id == $langidsarr[$i]) {
                    $langarrdata[$i]['href'] = $homelangurl;
                } else {
                    if (array_key_exists('{' . $langidsarr[$i] . '}', $domains)) {
                        $langarrdata[$i]['href'] = "http://" . $domains['{' . $langidsarr[$i] . '}'] . "/" . $postids[$langidsarr[$i]] . '/' . ($titles[$langidsarr[$i]]) . "/";
                    } else {
                        //$langarrdata[$i]['href'] = $mainurl . $thelang2[0]->{'lang'} . "/" . $postids[$langidsarr[$i]] . '/' . ($titles[$langidsarr[$i]]) . "/";
                        $langarrdata[$i]['href'] = $siteurl . $thelang2[0]->{'lang'} . "/" . $postids[$langidsarr[$i]] . '/' . ($titles[$langidsarr[$i]]) . "/";
                    }
                }
            }
            if ($langidsarr[$i] != $langid) {
                $langarrdata_nomain[$i]['title'] = $langarrdata[$i]['title'];
                $langarrdata_nomain[$i]['short'] = $langarrdata[$i]['short'];
                $langarrdata_nomain[$i]['img'] = $langarrdata[$i]['img'];
                $langarrdata_nomain[$i]['href'] = $langarrdata[$i]['href'];
            }
        }
    }
}
//替换语种，不含当前语种
$reTag = "/{pt\:languages_nomain_([0-9]+)}([\s\S]*?){\/pt}/i";
preg_match_all($reTag, $tpl, $matches);

//var_dump($langarrdata);
//var_dump($langarrdata_nomain);

$langarrdata_nomain = array_values($langarrdata_nomain);   //需要对其重新排序
for ($l = 0; $l < count($matches[0]); $l++) {
    if (isset($matches[0][$l])) {
        $tpl = str_replace($matches[0][$l], looponce($matches[0][$l], $reTag, $langarrdata_nomain), $tpl);
    }
}
//替换语种
$reTag = "/{pt\:languages_([0-9]+)}([\s\S]*?){\/pt}/i";
preg_match_all($reTag, $tpl, $matches);

for ($l = 0; $l < count($matches[0]); $l++) {
    if (isset($matches[0][$l])) {
        $tpl = str_replace($matches[0][$l], looponce($matches[0][$l], $reTag, $langarrdata), $tpl);
    }
}

//替换用户单词
$reTag = "/{#(\w+)#}/i";
preg_match_all($reTag, $tpl, $matches);
for ($i = 0; $i < count($matches[0]); $i++) {
    if (array_key_exists($matches[0][$i], $wordarr)) {
        if (filter_var($wordarr[$matches[0][$i]], FILTER_VALIDATE_EMAIL)) {
            //$tpl = str_replace($matches[0][$i], '<img src=' . $base . 'include/textpng.php?text=' . (encode($wordarr[$matches[0][$i]])) . '>', $tpl);    以图片显示邮件地址
            $tpl = str_replace($matches[0][$i], $wordarr[$matches[0][$i]], $tpl);
        } else {
            $tpl = str_replace($matches[0][$i], $wordarr[$matches[0][$i]], $tpl);
        }
    } else {
        //$tpl = str_replace($matches[0][$i], '', $tpl);
    }
}
//替换公共单词
$reTag = "/{@(\w+)@}/i";
preg_match_all($reTag, $tpl, $matches);
for ($i = 0; $i < count($matches[0]); $i++) {
    if (array_key_exists($matches[1][$i], $arr)) {
        $tpl = str_replace($matches[0][$i], $arr[$matches[1][$i]], $tpl);
    } else {
        $tpl = str_replace($matches[0][$i], '', $tpl);
    }
}
//GOOGLE ADSENSE
$reTag = "/{adsense}([\s\S]*?){\/adsense}/i";
preg_match_all($reTag, $tpl, $matches);
for ($l = 0; $l < count($matches[0]); $l++) {
    if ($showgg == 1) {
        $ggout = $matches[1][$l];
        $ggout = str_replace("{[ggclient]}", $ggclient, $ggout);
        $ggout = str_replace("{[ggslot]}", $ggslot, $ggout);
        $tpl = str_replace($matches[0][$l], $ggout, $tpl);
    } else {
        $tpl = str_replace($matches[0][$l], '', $tpl);
    }
}
//替换主页、页面标题、正文、TAG等
$tpl = str_replace("{[siteid]}", $siteid, $tpl);
$tpl = str_replace("{[siteurl]}", $siteurl, $tpl);
$tpl = str_replace("{[sitename]}", $sitename, $tpl);
$tpl = str_replace("{[thisurl]}", $thisurl, $tpl);
$tpl = str_replace("{[langurl]}", $siteurl . $langurl, $tpl);
$tpl = str_replace("{[lang]}", $lang, $tpl);
$tpl = str_replace("{[currency]}", $currency, $tpl);
$tpl = str_replace("{[currencysymbol]}", currency_symbol($currency), $tpl);
$tpl = str_replace("{[businessemail]}", $businessemail, $tpl);                 //替换商务邮件地址
if ($siteurl == $base . $prefix . $sitename . "/") {
    $cookiepath = "/" . $prefix . $sitename . "/";
} else {
    $cookiepath = "/";
}
$tpl = str_replace("{[cookiepath]}", $cookiepath, $tpl);
$tpl = str_replace("{[analyticshost]}", $analyticshost, $tpl);     //替换统计代码的域名，在config里设置
//替换自定义的分享代码
$reTag = "/{share}([\s\S]*?){\/share}/i";
preg_match_all($reTag, $tpl, $matches);
for ($l = 0; $l < count($matches[0]); $l++) {
    if (isset($matches[0][$l])) {
        if ($showshare == 1) {
            $tpl = str_replace($matches[0][$l], $matches[1][$l], $tpl);
        } else {
            $tpl = str_replace($matches[0][$l], '', $tpl);
        }
    }
}
//替换addthis的share代码
if ($showshare == 1) {
    $tpl = str_replace("{[share_side_left]}", '<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-58f182d9e1677435"></script>', $tpl);
    $tpl = str_replace("{[share_side_right]}", '<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-58f1a8e73610fedf"></script>', $tpl);
    $tpl = str_replace("{[share_expand_left]}", '<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-58f1a871893a91d1"></script>', $tpl);
    $tpl = str_replace("{[share_expand_right]}", '<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-58f1a992ca2dc423"></script>', $tpl);
    $tpl = str_replace("{[share_inline]}", '<div class="addthis_inline_share_toolbox"></div><script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-58f1a9b6c404cb08"></script>', $tpl);
} else {
    $tpl = str_replace("{[share_side_left]}", '', $tpl);
    $tpl = str_replace("{[share_side_right]}", '', $tpl);
    $tpl = str_replace("{[share_expand_left]}", '', $tpl);
    $tpl = str_replace("{[share_expand_right]}", '', $tpl);
    $tpl = str_replace("{[share_inline]}", '', $tpl);
}
//替换base地址
$tpl = str_replace("{[base]}", $base, $tpl);
$tpl = str_replace("{[userpath]}", $base . $prefix . $sitename . '/', $tpl);
//$tpl = str_replace("{[userpath]}", "/" . $prefix . $sitename . '/', $tpl);

//替换slide
$out1 = '';
$out2 = '';
for ($i = 1; $i < 6; $i++) {
    if (is_file($path . '/slide' . $i . '.png')) {
        $out1 = $out1 . '<li data-target="#mlb-carousel" data-slide-to="' . ($i - 1) . '"></li>';
        $out2 = $out2 . '<div class="item"><img src="' . $base . $prefix . $sitename . '/slide' . $i . '.png" width="100%"></div>';
    }
}
$tpl = str_replace("{[slide]}", '<div id="mlb-carousel" class="carousel slide" data-ride="carousel"><ol class="carousel-indicators">' . $out1 . '</ol><div class="carousel-inner">' . $out2 . '</div><a class="carousel-control left" href="#mlb-carousel" data-slide="prev">&lsaquo;</a><a class="carousel-control right" href="#mlb-carousel" data-slide="next">&rsaquo;</a></div>', $tpl);
//替换slide，无控制
$out1 = '';
$out2 = '';
for ($i = 1; $i < 6; $i++) {
    if (is_file($path . '/slide' . $i . '.png')) {
        $out1 = $out1 . '<li data-target="#mlb-carousel" data-slide-to="' . ($i - 1) . '"></li>';
        $out2 = $out2 . '<div class="item"><img src="' . $base . $prefix . $sitename . '/slide' . $i . '.png" width="100%"></div>';
    }
}
$tpl = str_replace("{[slide-nocontrol]}", '<div id="mlb-carousel" class="carousel slide" data-ride="carousel"><ol class="carousel-indicators">' . $out1 . '</ol><div class="carousel-inner">' . $out2 . '</div></div>', $tpl);

//替换本分类内容列表，显示子分类，每页显示几个子分类，每个分类显示几个页面
//子分类循环嵌套
if ($pagetype == 'category' || $pagetype == 'page') {
    $reTag = "/{pt\:categorypic_([0-9]+)_([0-9]+)}([\s\S]*?){\/pt}/i";
    preg_match_all($reTag, $tpl, $matches);
    for ($l = 0; $l < count($matches[0]); $l++) {
        if (isset($matches[0][$l])) {
            unset($arrdata);
            $num = $matches[2][$l];
            $limit = $matches[1][$l];
            if ($page <= 0 || $page == '') {
                $page = 1;
            }
            $k = 0;
            $catids = '';
            foreach ($subcatarr as $x => $x_value) {
                unset($tmparr);
                $tmparr = explode("_", $x);
                if ($tmparr[0] == $origincatid) {
                    $catids = $catids . ',' . $catarrori[$x_value];
                }
            }
            if ($catids != '') {
                $catids = $origincatid . $catids;
            } else {
                $catids = $origincatid;
            }
            $sql = "SELECT count(*) FROM x_post where status=1 and lang_id='$langid' and site_id='$siteid' and (category_id in ($catids) or category_id2 in ($catids)  )  " ;
            $tpagesresult = mysqli_query($con, $sql);
            $limit2 = $limit * $num;
            if ($tpagesresult->num_rows > 0) {
                $tpagesrow = mysqli_fetch_array($tpagesresult);
                $tpages = ceil($tpagesrow[0] / $limit2);
                if ($page <= 0 || $page == '') {
                    $page = 1;
                }
                if ($tpages <= 0 || $tpages == '') {
                    $tpages = 1;
                }
                $reload = $siteurl . $langurl . 'category_' . $catarrori[$categoryname] . '_';
                $startfrom = ($page - 1) * $limit2;
                $sql = "select * from x_post where status=1 and lang_id='$langid' and site_id='$siteid' and (category_id in ($catids) or category_id2 in ($catids)  )  order by sorting desc,id desc limit $startfrom,$limit2";
                $result = mysqli_query($con, $sql);
                if ($result->num_rows > 0) {
                    $i = 0;
                    $j = 0;
                    while ($row = mysqli_fetch_array($result)) {
                        preg_match($re_images, $row["thumbnail"], $thumbnail);
                        preg_match_all($re_images, $row["productpics"], $productpics);
                        if ($thumbnail[1] == null) {
                            $thumbnail2 = '/img/default.png';
                        } else {
                            $thumbnail2 = $thumbnail[1];
                        }
                        $arrdata[$i]['content'][$j]['id'] = $row["id"];
                        $arrdata[$i]['content'][$j]['title'] = $row["title"];
                        $arrdata[$i]['content'][$j]["href"] = $siteurl . $langurl . $row["id"] . "/" . myurlencode($row["title"]) . "/";
                        $arrdata[$i]['content'][$j]["img"] = $thumbnail2;
                        $arrdata[$i]['content'][$j]["pics"] = $productpics[1];
                        if ($row["brief"] != null && $row["brief"] != '') {
                            $arrdata[$i]['content'][$j]['brief'] = $row["brief"];
                        } else {
                            $arrdata[$i]['content'][$j]['brief'] = mb_substr(strip_tags($row["content"]), 0, 80, 'utf-8');
                        }
                        $arrdata[$i]['content'][$j]['price'] = $row["price"];
                        $arrdata[$i]['content'][$j]['attachment'] = $row["attachment"];
                        $arrdata[$i]['content'][$j]['unit'] = $row["unit"];
                        $arrdata[$i]['content'][$j]['onsale'] = $row["onsale"];
                        $arrdata[$i]['content'][$j]['originalprice'] = $row["originalprice"];
                        $arrdata[$i]['content'][$j]['weight'] = $row["weight"];
                        if ($row["shopping"] == 1) {
                            $arrdata[$i]['content'][$j]['shopping'] = "shopping";
                        } else {
                            $arrdata[$i]['content'][$j]['shopping'] = "noshopping";
                        }
                        $arrdata[$i]['content'][$j]['year'] = date('Y', strtotime($row["time"]));
                        $arrdata[$i]['content'][$j]['month'] = date('m', strtotime($row["month"]));
                        $arrdata[$i]['content'][$j]['day'] = date('d', strtotime($row["day"]));
                        $j++;
                        if ($j >= $num) {
                            $j = 0;
                            $i++;
                        }
                    }
                }
            }
            //$tpl = str_replace($matches[0][$l], looptwice($matches[0][$l], $reTag, $arrdata), $tpl);
            $tpl = str_replace($matches[0][$l], loopthrice($matches[0][$l], $reTag, $arrdata), $tpl);
            //替换分页
            $tpl = str_replace('{[categorypages]}', paginate_two($reload, $page, $tpages, 4, "/" . myurlencode($categoryname)), $tpl);
        }
    }
}
//子分类循环嵌套
if ($pagetype == 'category') {
    $reTag = "/{pt\:categorypicsub_([0-9]+)_([0-9]+)}([\s\S]*?){\/pt}/i";
    preg_match_all($reTag, $tpl, $matches);
    for ($l = 0; $l < count($matches[0]); $l++) {
        if (isset($matches[0][$l])) {
            unset($arrdata);
            $num = $matches[2][$l];
            $limit = $matches[1][$l];
            if ($page <= 0 || $page == '') {
                $page = 1;
            }
            $i = 0;
            $k = 0;
            foreach ($subcatarr as $x => $x_value) {
                unset($tmparr);
                $tmparr = explode("_", $x);
                if ($tmparr[0] == $origincatid) {
                    $catids = $catids . ',' . $catarrori[$x_value];
                    //echo '<h3>' . $i . '>='.$limit.'*('.$page.'-1)</h3>';
                    if ($k >= $limit * ($page - 1) && $k < $page * $limit) {
                        $arrdata[$i]['href'] = $siteurl . $langurl . "category_'.$catarrori[$x_value].'/" . myurlencode($x_value) . '/';
                        $arrdata[$i]['title'] = $x_value;

                        $sql = "select * from x_post where status=1 and lang_id='$langid' and site_id='$siteid' and (category_id='$catarrori[$x_value]' or category_id2='$catarrori[$x_value]' )  order by sorting desc,id desc limit 0,$num";
                        //echo $sql;
                        $result = mysqli_query($con, $sql);
                        if ($result->num_rows > 0) {
                            $j = 0;
                            while ($row = mysqli_fetch_array($result)) {
                                preg_match($re_images, $row["thumbnail"], $thumbnail);
                                if ($thumbnail[1] == null) {
                                    $thumbnail2 = '/img/default.png';
                                } else {
                                    $thumbnail2 = $thumbnail[1];
                                }
                                $arrdata[$i]['content'][$j]['title'] = $row["title"];
                                $arrdata[$i]['content'][$j]["href"] = $siteurl . $langurl . $row["id"] . "/" . myurlencode($row["title"]) . "/";
                                $arrdata[$i]['content'][$j]["img"] = $thumbnail2;
                                if ($row["brief"] != null && $row["brief"] != '') {
                                    $arrdata[$i]['content'][$j]['brief'] = $row["brief"];
                                } else {
                                    $arrdata[$i]['content'][$j]['brief'] = mb_substr(strip_tags($row["content"]), 0, 80, 'utf-8');
                                }
                                $j++;
                            }
                        }
                        $i++;
                    }
                    $k++;
                }
            }
            //var_dump($arrdata);
            $tpl = str_replace($matches[0][$l], looptwice($matches[0][$l], $reTag, $arrdata), $tpl);
            if ($catids != '') {
                $catids = $origincatid . $catids;
                //echo count(   explode(',', $catids));
                $tpages = ceil((count(explode(',', $catids)) - 1) / $limit);
            } else {
                $catids = $origincatid;
                $tpages = 1;
            }
            if ($tpages <= 0 || $tpages == '') {
                $tpages = 1;
            }
            $reload = $siteurl . $langurl . 'category_' . $catarrori[$categoryname] . '_';
            //替换分页
            $tpl = str_replace('{[categorypagessub]}', paginate_two($reload, $page, $tpages, 4, '/' . myurlencode($categoryname)), $tpl);
        }
    }
}

//不限分类列表，单循环
if ($pagetype == 'index') {
    $reTag = "/{pt\:categorylistall_([0-9]+)}([\s\S]*?){\/pt}/i";
    preg_match_all($reTag, $tpl, $matches);
    for ($l = 0; $l < count($matches[0]); $l++) {
        if (isset($matches[0][$l])) {
            unset($arrdata);
            $limit = $matches[1][$l];
            $sql = "SELECT count(*) FROM x_post where status=1 and lang_id='$langid' and site_id='$siteid' " ;
            $tpagesresult = mysqli_query($con, $sql);
            if ($tpagesresult->num_rows > 0) {
                $tpagesrow = mysqli_fetch_array($tpagesresult);
                $tpages = ceil($tpagesrow[0] / $limit);
                if ($page <= 0 || $page == '') {
                    $page = 1;
                }
                if ($tpages <= 0 || $tpages == '') {
                    $tpages = 1;
                }
                $reload = $siteurl . $langurl;
                $startfrom = ($page - 1) * $limit;
                $sql = "select * from x_post where status=1 and lang_id='$langid' and site_id='$siteid'  order by sorting desc,id desc limit $startfrom,$limit";
                $result = mysqli_query($con, $sql);
                if ($result->num_rows > 0) {
                    $i = 0;
                    while ($row = mysqli_fetch_array($result)) {
                        preg_match($re_images, $row["thumbnail"], $thumbnail);
                        if ($thumbnail[1] == null) {
                            $thumbnail2 = '/img/default.png';
                        } else {
                            $thumbnail2 = $thumbnail[1];
                        }
                        $arrdata[$i]['title'] = $row["title"];
                        $arrdata[$i]["href"] = $siteurl . $langurl . $row["id"] . "/" . myurlencode($row["title"]) . "/";
                        $arrdata[$i]["img"] = $thumbnail2;
                        $arrdata[$i]["year"] = date('Y', strtotime($row["time"]));
                        $arrdata[$i]["month"] = date('m', strtotime($row["time"]));
                        $arrdata[$i]["day"] = date('d', strtotime($row["time"]));
                        $arrdata[$i]["views"] = $row["views"];
                        if ($row["brief"] != null && $row["brief"] != '') {
                            $arrdata[$i]['brief'] = $row["brief"];
                        } else {
                            $arrdata[$i]['brief'] = mb_substr(strip_tags($row["content"]), 0, 80, 'utf-8');
                        }
                        $i++;
                    }
                }
            }
            $tpl = str_replace($matches[0][$l], looponce($matches[0][$l], $reTag, $arrdata), $tpl);
            //替换分页
            $tpl = str_replace('{[allpages]}', paginate_two($reload, $page, $tpages, 4, ''), $tpl);
        }
    }
}
//分类列表，按序号或按别名指定，单循环
$reTag = "/{pt\:categorylist_([0-9]+|[a-z]+)_([0-9]+)}([\s\S]*?){\/pt}/i";
preg_match_all($reTag, $tpl, $matches);
for ($l = 0; $l < count($matches[0]); $l++) {
    if (isset($matches[0][$l])) {
        unset($arrdata);
        $start = $matches[1][$l];
        $limit = $matches[2][$l];
        $catids = '';
        if (checkinput('az', $start, 1, 255) != '') {
            foreach ($subcatarr as $x => $x_value) {
                unset($tmparr);
                $tmparr = explode("_", $x);
                if ($tmparr[0] == $cataliasidarr[$start]) {
                    $catids = $catids . ',' . $catarrori[$x_value];
                }
            }
            if (array_key_exists($start, $cataliasidarr)) {
                if ($catids != '') {
                    $catids = $cataliasidarr[$start] . $catids;
                } else {
                    $catids = $cataliasidarr[$start];
                }
            }
        }
        if (checkinput('int', $start, 1, 5) != '') {
            $sql = "select origin_id from x_category where categoryname='$catarr2[$start]' and  lang_id='$langid' and site_id='$siteid' limit 1";
            $result = mysqli_query($con, $sql);
            $row = mysqli_fetch_array($result);
            $categoryidbysorting = $row[0];
            foreach ($subcatarr as $x => $x_value) {
                unset($tmparr);
                $tmparr = explode("_", $x);
                if ($tmparr[0] == $categoryidbysorting) {
                    $catids = $catids . ',' . $catarrori[$x_value];
                }
            }
            if ($catids != '') {
                $catids = $categoryidbysorting . $catids;
            } else {
                $catids = $categoryidbysorting;
            }
        }
        //$sql = "select id from x_category where lang_id='$homelang_id' and site_id='$siteid' order by sorting desc limit $num,1";
        $sql = "select * from x_post where status=1 and lang_id='$langid' and site_id='$siteid' and (category_id in ($catids) or category_id2 in ($catids))  order by sorting desc,id desc limit 0,$limit";

        $result = mysqli_query($con, $sql);
        if ($result->num_rows > 0) {
            $i = 0;
            while ($row = mysqli_fetch_array($result)) {
                preg_match($re_images, $row["thumbnail"], $thumbnail);
                if ($thumbnail[1] == null) {
                    $thumbnail2 = $thumbnail[1];
                } else {
                    $thumbnail2 = $thumbnail[1];
                }
                $arrdata[$i]['title'] = $row["title"];
                $arrdata[$i]["href"] = $siteurl . $langurl . $row["id"] . "/" . myurlencode($row["title"]) . "/";
                $arrdata[$i]["img"] = $thumbnail2;
                if ($row["brief"] != null && $row["brief"] != '') {
                    $arrdata[$i]['brief'] = $row["brief"];
                } else {
                    $arrdata[$i]['brief'] = mb_substr(strip_tags($row["content"]), 0, 80, 'utf-8');
                }
                $arrdata[$i]["year"] = date('Y', strtotime($row["time"]));
                $arrdata[$i]["month"] = date('m', strtotime($row["time"]));
                $arrdata[$i]["day"] = date('d', strtotime($row["time"]));
                $arrdata[$i]["views"] = $row["views"];
                $i++;
            }
        }
        $tpl = str_replace($matches[0][$l], looponce2($matches[0][$l], $reTag, $arrdata), $tpl);
    }
}
//分类列表，按序号或按别名指定，单循环，从指定位置开始替换多少条记录
$reTag = "/{pt\:postlist_([0-9]+|[a-z]+)_([0-9]+)_([0-9]+)}([\s\S]*?){\/pt}/i";
preg_match_all($reTag, $tpl, $matches);
for ($l = 0; $l < count($matches[0]); $l++) {
    if (isset($matches[0][$l])) {
        unset($arrdata);
        $start = $matches[1][$l];
        $num = $matches[2][$l];
        $limit = $matches[3][$l];
        $catids = '';
        if (checkinput('az', $start, 1, 255) != '') {
            foreach ($subcatarr as $x => $x_value) {
                unset($tmparr);
                $tmparr = explode("_", $x);
                if ($tmparr[0] == $cataliasidarr[$start]) {
                    $catids = $catids . ',' . $catarrori[$x_value];
                }
            }
            if (array_key_exists($start, $cataliasidarr)) {
                if ($catids != '') {
                    $catids = $cataliasidarr[$start] . $catids;
                } else {
                    $catids = $cataliasidarr[$start];
                }
            }
        }
        if (checkinput('int', $start, 1, 5) != '') {
            $sql = "select origin_id from x_category where categoryname='$catarr2[$start]' and  lang_id='$langid' and site_id='$siteid' limit 1";
            $result = mysqli_query($con, $sql);
            $row = mysqli_fetch_array($result);
            $categoryidbysorting = $row[0];
            foreach ($subcatarr as $x => $x_value) {
                unset($tmparr);
                $tmparr = explode("_", $x);
                if ($tmparr[0] == $categoryidbysorting) {
                    $catids = $catids . ',' . $catarrori[$x_value];
                }
            }
            if ($catids != '') {
                $catids = $categoryidbysorting . $catids;
            } else {
                $catids = $categoryidbysorting;
            }
        }
        //$sql = "select id from x_category where lang_id='$homelang_id' and site_id='$siteid' order by sorting desc limit $num,1";
        $sql = "select * from x_post where status=1 and lang_id='$langid' and site_id='$siteid' and (category_id in ($catids) or category_id2 in ($catids))  order by sorting desc,id desc limit $num,$limit";
        $result = mysqli_query($con, $sql);
        if ($result->num_rows > 0) {
            $i = 0;
            while ($row = mysqli_fetch_array($result)) {
                preg_match($re_images, $row["thumbnail"], $thumbnail);
                if ($thumbnail[1] == null) {
                    $thumbnail2 = '/img/default.png';
                } else {
                    $thumbnail2 = $thumbnail[1];
                }
                $arrdata[$i]['title'] = $row["title"];
                $arrdata[$i]["href"] = $siteurl . $langurl . $row["id"] . "/" . myurlencode($row["title"]) . "/";
                $arrdata[$i]["img"] = $thumbnail2;
                if ($row["brief"] != null && $row["brief"] != '') {
                    $arrdata[$i]['brief'] = $row["brief"];
                } else {
                    $arrdata[$i]['brief'] = mb_substr(strip_tags($row["content"]), 0, 80, 'utf-8');
                }
                $arrdata[$i]["year"] = date('Y', strtotime($row["time"]));
                $arrdata[$i]["month"] = date('m', strtotime($row["time"]));
                $arrdata[$i]["day"] = date('d', strtotime($row["time"]));
                $arrdata[$i]["views"] = $row["views"];
                $i++;
            }
        }
        $tpl = str_replace($matches[0][$l], looponce3($matches[0][$l], $reTag, $arrdata), $tpl);
    }
}

//所有产品列表，双循环
$reTag = "/{pt\:all_([0-9]+)_([0-9]+)}([\s\S]*?){\/pt}/i";
preg_match_all($reTag, $tpl, $matches);
for ($l = 0; $l < count($matches[0]); $l++) {
    if (isset($matches[0][$l])) {
        unset($arrdata);
        $limit = $matches[1][$l];
        $num = $matches[2][$l];
        $sql = "SELECT count(*) FROM x_post where status=1 and lang_id='$langid' and site_id='$siteid'  ";
        $tpagesresult = mysqli_query($con, $sql);
        $limit2 = $limit * $num;
        if ($tpagesresult->num_rows > 0) {
            $tpagesrow = mysqli_fetch_array($tpagesresult);
            $tpages = ceil($tpagesrow[0] / $limit2);
            if ($page <= 0 || $page == '') {
                $page = 1;
            }
            if ($tpages <= 0 || $tpages == '') {
                $tpages = 1;
            }
            $reload = $siteurl . $langurl . "search/" . myurlencode($search) . "/";
            $startfrom = ($page - 1) * $limit2;
            $sql = "select * from x_post where status=1 and title like '%$search%' and lang_id='$langid' and site_id='$siteid'  order by sorting desc,id desc limit $startfrom,$limit2";
            $result = mysqli_query($con, $sql);
            if ($result->num_rows > 0) {
                $i = 0;
                $j = 0;
                while ($row = mysqli_fetch_array($result)) {
                    preg_match($re_images, $row["thumbnail"], $thumbnail);
                    if ($thumbnail[1] == null) {
                        preg_match_all("/<img ([^>]+)>/i", $row["content"], $thumbnails);
                        preg_match_all("/ src=(\"|\')?(.*?)(\"|\'| ){1}/i", $thumbnails[0][0], $matches2);
                        $thumbnail2 = $matches2[2][0];
                        //$thumbnail2 = str_replace('"', '', $thumbnail2);
                        //$thumbnail2 = str_replace('\'', '', $thumbnail2);
                    } else {
                        $thumbnail2 = $thumbnail[1];
                    }
                    if (substr($thumbnail2, 0, 4) != 'http') {
                        $thumbnail2 = $base . $thumbnail2;
                    }
                    if ($thumbnail2 == $base) {
                        $thumbnail2 = $base . 'img/default.png';
                    }
                    $arrdata[$i]['content'][$j]['title'] = $row["title"];
                    $arrdata[$i]['content'][$j]["href"] = $siteurl . $langurl . $row["id"] . "/" . myurlencode($row["title"]) . "/";
                    $arrdata[$i]['content'][$j]["img"] = $thumbnail2;
                    $arrdata[$i]['content'][$j]["year"] = date('Y', strtotime($row["time"]));
                    $arrdata[$i]['content'][$j]["month"] = date('m', strtotime($row["time"]));
                    $arrdata[$i]['content'][$j]["day"] = date('d', strtotime($row["time"]));
                    $arrdata[$i]['content'][$j]['views'] = $row["views"];
                    if ($row["brief"] != null && $row["brief"] != '') {
                        $arrdata[$i]['content'][$j]['brief'] = $row["brief"];
                    } else {
                        $arrdata[$i]['content'][$j]['brief'] = mb_substr(strip_tags($row["content"]), 0, 80, 'utf-8');
                    }
                    $arrdata[$i]['content'][$j]['id'] = $row["id"];
                    $arrdata[$i]['content'][$j]['price'] = $row["price"];
                    $arrdata[$i]['content'][$j]['onsale'] = $row["onsale"];
                    $arrdata[$i]['content'][$j]['originalprice'] = $row["originalprice"];
                    $arrdata[$i]['content'][$j]['weight'] = $row["weight"];
                    $j++;
                    if ($j >= $num) {
                        $j = 0;
                        $i++;
                    }
                }
            }
        }
        $tpl = str_replace($matches[0][$l], looptwice($matches[0][$l], $reTag, $arrdata), $tpl);
        //替换分页
        $tpl = str_replace('{[allpages]}', paginate_two($reload, $page, $tpages, 4, ''), $tpl);
    }
}
//分类列表，按序号或按别名指定，双循环
$reTag = "/{pt\:categorylist_([0-9]+|[a-z]+)_([0-9]+)_([0-9]+)}([\s\S]*?){\/pt}/i";
preg_match_all($reTag, $tpl, $matches);
for ($l = 0; $l < count($matches[0]); $l++) {
    if (isset($matches[0][$l])) {
        unset($arrdata);
        $start = $matches[1][$l];
        $limit = $matches[2][$l] * $matches[3][$l];
        $catids = '';
        if (checkinput('az', $start, 1, 255) != '') {
            foreach ($subcatarr as $x => $x_value) {
                unset($tmparr);
                $tmparr = explode("_", $x);
                if ($tmparr[0] == $cataliasidarr[$start]) {
                    $catids = $catids . ',' . $catarrori[$x_value];
                }
            }
            if (array_key_exists($start, $cataliasidarr)) {
                if ($catids != '') {
                    $catids = $cataliasidarr[$start] . $catids;
                } else {
                    $catids = $cataliasidarr[$start];
                }
            }
        }
        if (checkinput('int', $start, 1, 5) != '') {
            $sql = "select origin_id from x_category where categoryname='$catarr2[$start]' and  lang_id='$langid' and site_id='$siteid' limit 1";
            $result = mysqli_query($con, $sql);
            $row = mysqli_fetch_array($result);
            $categoryidbysorting = $row[0];
            foreach ($subcatarr as $x => $x_value) {
                unset($tmparr);
                $tmparr = explode("_", $x);
                if ($tmparr[0] == $categoryidbysorting) {
                    $catids = $catids . ',' . $catarrori[$x_value];
                }
            }
            if ($catids != '') {
                $catids = $categoryidbysorting . $catids;
            } else {
                $catids = $categoryidbysorting;
            }
        }
        $sql = "select * from x_post where status=1 and lang_id='$langid' and site_id='$siteid' and (category_id in ($catids) or category_id2 in ($catids))  order by sorting desc,id desc limit 0,$limit";
        $result = mysqli_query($con, $sql);
        if ($result->num_rows > 0) {
            $i = 0;
            $j = 0;
            while ($row = mysqli_fetch_array($result)) {
                preg_match($re_images, $row["thumbnail"], $thumbnail);
                if ($thumbnail[1] == null) {
                    $thumbnail2 = '/img/default.png';
                } else {
                    $thumbnail2 = $thumbnail[1];
                }
                $arrdata[$i]['content'][$j]['title'] = $row["title"];
                $arrdata[$i]['content'][$j]["href"] = $siteurl . $langurl . $row["id"] . "/" . myurlencode($row["title"]) . "/";
                $arrdata[$i]['content'][$j]["img"] = $thumbnail2;
                if ($row["brief"] != null && $row["brief"] != '') {
                    $arrdata[$i]['content'][$j]['brief'] = $row["brief"];
                } else {
                    $arrdata[$i]['content'][$j]['brief'] = mb_substr(strip_tags($row["content"]), 0, 80, 'utf-8');
                }
                $j++;
                if ($j >= $matches[3][$l]) {
                    $i++;
                    $j = 0;
                }
            }
        }
        $tpl = str_replace($matches[0][$l], looptwice2($matches[0][$l], $reTag, $arrdata), $tpl);
        //替换分页
        $tpl = str_replace('{[categorypages]}', paginate_two($reload, $page, $tpages, 4, "/" . myurlencode($categoryname)), $tpl);
    }
}

//分类列表,替换本分类内容列表或page页内容列表，单循环
if ($pagetype == 'page' || $pagetype == 'category') {
    $reTag = "/{pt\:categorylist_([0-9]+)}([\s\S]*?){\/pt}/i";
    preg_match_all($reTag, $tpl, $matches);
    for ($l = 0; $l < count($matches[0]); $l++) {
        if (isset($matches[0][$l])) {
            unset($arrdata);
            $limit = $matches[1][$l];
            $catids = '';
            foreach ($subcatarr as $x => $x_value) {
                unset($tmparr);
                $tmparr = explode("_", $x);
                if ($tmparr[0] == $origincatid) {
                    $catids = $catids . ',' . $catarrori[$x_value];
                }
            }
            if ($catids != '') {
                $catids = $origincatid . $catids;
            } else {
                $catids = $origincatid;
            }
            $sql = "SELECT count(*) FROM x_post where status=1 and lang_id='$langid' and site_id='$siteid' and (category_id in ($catids) or category_id2 in ($catids)  ) " ;
            $tpagesresult = mysqli_query($con, $sql);
            if ($tpagesresult->num_rows > 0) {
                $tpagesrow = mysqli_fetch_array($tpagesresult);
                $tpages = ceil($tpagesrow[0] / $limit);
                if ($page <= 0 || $page == '') {
                    $page = 1;
                }
                if ($tpages <= 0 || $tpages == '') {
                    $tpages = 1;
                }
                $reload = $siteurl . $langurl . 'category_' . $catarrori[$categoryname] . '_';
                $startfrom = ($page - 1) * $limit;
                $sql = "select * from x_post where status=1 and lang_id='$langid' and site_id='$siteid' and (category_id in ($catids) or category_id2 in ($catids)  )  order by sorting desc,id desc limit $startfrom,$limit";
                $result = mysqli_query($con, $sql);
                if ($result->num_rows > 0) {
                    $i = 0;
                    while ($row = mysqli_fetch_array($result)) {
                        preg_match($re_images, $row["thumbnail"], $thumbnail);
                        if ($thumbnail[1] == null) {
                            $thumbnail2 = '/img/default.png';
                        } else {
                            $thumbnail2 = $thumbnail[1];
                        }
                        $arrdata[$i]['title'] = $row["title"];
                        $arrdata[$i]["href"] = $siteurl . $langurl . $row["id"] . "/" . myurlencode($row["title"]) . "/";
                        $arrdata[$i]["img"] = $thumbnail2;
                        $arrdata[$i]["year"] = date('Y', strtotime($row["time"]));
                        $arrdata[$i]["month"] = date('m', strtotime($row["time"]));
                        $arrdata[$i]["day"] = date('d', strtotime($row["time"]));
                        $arrdata[$i]['views'] = $row["views"];
                        if ($row["brief"] != null && $row["brief"] != '') {
                            $arrdata[$i]['brief'] = $row["brief"];
                        } else {
                            $arrdata[$i]['brief'] = mb_substr(strip_tags($row["content"]), 0, 80, 'utf-8');
                        }
                        $i++;
                    }
                }
            }
            $tpl = str_replace($matches[0][$l], looponce($matches[0][$l], $reTag, $arrdata), $tpl);
            //替换分页
            $tpl = str_replace('{[categorypages]}', paginate_two($reload, $page, $tpages, 4, '/' . myurlencode($categoryname)), $tpl);
        }
    }
}


//当前分类的页数、分页，用{[categorypages_([0-9]+)]}指定每页数量
if ($pagetype == 'category') {
    $reTag = "/{\[categorypages_([0-9]+)\]}/i";
    preg_match($reTag, $tpl, $match);
    if (isset($match[0])) {
        $limitperpage = $match[1];
        $catids = '';
        foreach ($subcatarr as $x => $x_value) {
            unset($tmparr);
            $tmparr = explode("_", $x);
            if ($tmparr[0] == $origincatid) {
                $catids = $catids . ',' . $catarrori[$x_value];
            }
        }
        if ($catids != '') {
            $catids = $origincatid . $catids;
        } else {
            $catids = $origincatid;
        }
        $sql = "SELECT count(*) FROM x_post where status=1 and lang_id='$langid' and site_id='$siteid' and (category_id in ($catids) or category_id2 in ($catids)  ) " ;
        $tpagesresult = mysqli_query($con, $sql);
        if ($tpagesresult->num_rows > 0) {
            $tpagesrow = mysqli_fetch_array($tpagesresult);
            $tpages = ceil($tpagesrow[0] / $limitperpage);
            if ($page <= 0 || $page == '') {
                $page = 1;
            }
            if ($tpages <= 0 || $tpages == '') {
                $tpages = 1;
            }
            $reload = $siteurl . $langurl . 'category_' . $catarrori[$categoryname] . '_';
        }
        $tpl = str_replace($match[0], paginate_two($reload, $page, $tpages, 4, '/' . myurlencode($categoryname)), $tpl);
        //只有在指定了每页记录条数后才能正确显示列表
//分类列表,替换本分类内容列表或page页内容列表，指定起始序号，单循环，注意和页数的关系
        $reTag = "/{pt\:postlist_([0-9]+)_([0-9]+)}([\s\S]*?){\/pt}/i";
        preg_match_all($reTag, $tpl, $matches);
        for ($l = 0; $l < count($matches[0]); $l++) {
            if (isset($matches[0][$l])) {
                unset($arrdata);
                $start = $matches[1][$l];
                $limit = $matches[2][$l];
                $startfrom = ($page - 1) * $limitperpage + $start;
                $sql = "select * from x_post where status=1 and lang_id='$langid' and site_id='$siteid' and (category_id in ($catids) or category_id2 in ($catids)  )  order by sorting desc,id desc limit $startfrom,$limit";
                $result = mysqli_query($con, $sql);
                if ($result->num_rows > 0) {
                    $i = 0;
                    while ($row = mysqli_fetch_array($result)) {
                        preg_match($re_images, $row["thumbnail"], $thumbnail);
                        if ($thumbnail[1] == null) {
                            $thumbnail2 = '/img/default.png';
                        } else {
                            $thumbnail2 = $thumbnail[1];
                        }
                        $arrdata[$i]['title'] = $row["title"];
                        $arrdata[$i]["href"] = $siteurl . $langurl . $row["id"] . "/" . myurlencode($row["title"]) . "/";
                        $arrdata[$i]["img"] = $thumbnail2;
                        $arrdata[$i]["year"] = date('Y', strtotime($row["time"]));
                        $arrdata[$i]["month"] = date('m', strtotime($row["time"]));
                        $arrdata[$i]["day"] = date('d', strtotime($row["time"]));
                        $arrdata[$i]['views'] = $row["views"];
                        if ($row["brief"] != null && $row["brief"] != '') {
                            $arrdata[$i]['brief'] = $row["brief"];
                        } else {
                            $arrdata[$i]['brief'] = mb_substr(strip_tags($row["content"]), 0, 80, 'utf-8');
                        }
                        $i++;
                    }
                }
                $tpl = str_replace($matches[0][$l], looponce2($matches[0][$l], $reTag, $arrdata), $tpl);
            }
        }
//列表结束
    }
}
//随机显示本分类内容列表或page页内容列表，单循环
if ($pagetype == 'page' || $pagetype == 'category') {
    $reTag = "/{pt\:randomlist_([0-9]+)}([\s\S]*?){\/pt}/i";
    preg_match_all($reTag, $tpl, $matches);
    for ($l = 0; $l < count($matches[0]); $l++) {
        if (isset($matches[0][$l])) {
            unset($arrdata);
            $limit = $matches[1][$l];
            $catids = '';
            foreach ($subcatarr as $x => $x_value) {
                unset($tmparr);
                $tmparr = explode("_", $x);
                if ($tmparr[0] == $origincatid) {
                    $catids = $catids . ',' . $catarrori[$x_value];
                }
            }
            if ($catids != '') {
                $catids = $origincatid . $catids;
            } else {
                $catids = $origincatid;
            }
            $sql = "SELECT count(*) FROM x_post where status=1 and lang_id='$langid' and site_id='$siteid' and (category_id in ($catids) or category_id2 in ($catids)  ) " ;
            $tpagesresult = mysqli_query($con, $sql);
            $tpagesrow = mysqli_fetch_array($tpagesresult);
            if ($tpagesresult->num_rows > 0) {
                if ($tpagesrow[0] < $limit) {
                    $limit = $tpagesrow[0];
                }
                $sql = "select * from x_post where status=1 and lang_id='$langid' and site_id='$siteid' and (category_id in ($catids) or category_id2 in ($catids)  )  order by rand() limit $limit";
                $result = mysqli_query($con, $sql);
                if ($result->num_rows > 0) {
                    $i = 0;
                    while ($row = mysqli_fetch_array($result)) {
                        preg_match($re_images, $row["thumbnail"], $thumbnail);
                        if ($thumbnail[1] == null) {
                            $thumbnail2 = '/img/default.png';
                        } else {
                            $thumbnail2 = $thumbnail[1];
                        }
                        $arrdata[$i]['title'] = $row["title"];
                        $arrdata[$i]["href"] = $siteurl . $langurl . $row["id"] . "/" . myurlencode($row["title"]) . "/";
                        $arrdata[$i]["img"] = $thumbnail2;
                        $arrdata[$i]["year"] = date('Y', strtotime($row["time"]));
                        $arrdata[$i]["month"] = date('m', strtotime($row["time"]));
                        $arrdata[$i]["day"] = date('d', strtotime($row["time"]));
                        $arrdata[$i]['views'] = $row["views"];
                        if ($row["brief"] != null && $row["brief"] != '') {
                            $arrdata[$i]['brief'] = $row["brief"];
                        } else {
                            $arrdata[$i]['brief'] = mb_substr(strip_tags($row["content"]), 0, 80, 'utf-8');
                        }
                        $i++;
                    }
                }
            }
            $tpl = str_replace($matches[0][$l], looponce($matches[0][$l], $reTag, $arrdata), $tpl);
        }
    }
}
//公共标签
$reTag = "/{pt\:pubtags_([0-9]+)}([\s\S]*?){\/pt}/i";
preg_match_all($reTag, $tpl, $matches);
for ($l = 0; $l < count($matches[0]); $l++) {
    if (isset($matches[0][$l])) {
        unset($arrdata);
        $limit = $matches[1][$l];
        /////////////////////////////公共标签
        if ($limit > count($tagarr)) {
            $limit = count($tagarr);
        }
        $i = 0;
        foreach ($tagarrpre as $key => $value) {
            $arrdata[$i]['title'] = $key;
            $arrdata[$i]['href'] = $siteurl . $langurl . "tag/" . myurlencode($key) . "/";
            if ($maxtagsize - $mintagsize != 0) {
                $arrdata[$i]['size'] = 1 + 1.5 * $value / ($maxtagsize - $mintagsize);
            } else {
                $arrdata[$i]['size'] = 1;
            }
            $i++;
            if ($i >= $limit) {
                break;
            }
        }
        $tpl = str_replace($matches[0][$l], looponce($matches[0][$l], $reTag, $arrdata), $tpl);
    }
}
//页面标签
if ($pagetype == 'page') {
    $reTag = "/{pt\:pagetags_([0-9]+)}([\s\S]*?){\/pt}/i";
    preg_match_all($reTag, $tpl, $matches);
    for ($l = 0; $l < count($matches[0]); $l++) {
        if (isset($matches[0][$l])) {
            unset($arrdata);
            $limit = $matches[1][$l];
            if ($limit > count($pagetags)) {
                $limit = count($pagetags);
            }
            for ($i = 0; $i < $limit; $i++) {
                $arrdata[$i]['title'] = $pagetags[$i];
                $arrdata[$i]['href'] = $siteurl . $langurl . "tag/" . myurlencode($pagetags[$i]) . "/";
            }
            $tpl = str_replace($matches[0][$l], looponce($matches[0][$l], $reTag, $arrdata), $tpl);
        }
    }
}
//带别名的页面
$reTag = "/{pt\:pageall_([0-9]+)}([\s\S]*?){\/pt}/i";
preg_match_all($reTag, $tpl, $matches);
for ($l = 0; $l < count($matches[0]); $l++) {
    if (isset($matches[0][$l])) {
        unset($arrdata);
        $limit = $matches[1][$l];
        if ($limit > count($singles)) {
            $limit = count($singles);
        }
        $i = 0;
        foreach ($singles as $x => $x_value) {
            $arrdata[$i]['title'] = $x_value;
            $arrdata[$i]['href'] = $siteurl . $langurl . str_replace('}', '', str_replace('{', '', $x)) . "/" . myurlencode($x_value) . "/";
            $i++;
            if ($i > $limit) {
                break;
            }
        }
        $tpl = str_replace($matches[0][$l], looponce($matches[0][$l], $reTag, $arrdata), $tpl);
    }
}
//合作伙伴logo，可以删除，留作备用
$reTag = "/{pt\:partnerlogo_([0-9]+)}([\s\S]*?){\/pt}/i";
preg_match_all($reTag, $tpl, $matches);
for ($l = 0; $l < count($matches[0]); $l++) {
    if (isset($matches[0][$l])) {
        unset($arrdata);
        $j = 0;
        for ($i = 0; $i < 10; $i++) {
            if (is_file($path . '/partnerlogo' . ($i + 1) . '.png')) {
                $arrdata[$j]['img'] = $base . $prefix . $sitename . '/partner' . ($i + 1) . '.png';
                $j = $j + 1;
            }
        }
        $tpl = str_replace($matches[0][$l], looponce($matches[0][$l], $reTag, $arrdata), $tpl);
    }
}
//友情链接
$reTag = "/{pt\:links_([0-9]+)}([\s\S]*?){\/pt}/i";
preg_match_all($reTag, $tpl, $matches);
for ($l = 0; $l < count($matches[0]); $l++) {
    if (isset($matches[0][$l])) {
        unset($arrdata);
        $limit = $matches[1][$l];
        ////////////////////友情链接
        if ($limit > count($linksarr)) {
            $limit = count($linksarr);
        }
        $i = 0;
        foreach ($linksarr as $x => $x_value) {
            $arrdata[$i]['title'] = $x;
            $arrdata[$i]['href'] = $x_value;
            $arrdata[$i]['img'] = $logosarr[$x];
            $i++;
            if ($i > $limit) {
                break;
            }
        }
        //var_dump($arrdata);
        $tpl = str_replace($matches[0][$l], looponce($matches[0][$l], $reTag, $arrdata), $tpl);
        ////////////////////
    }
}
//替换最近更新的若干条记录，不限分类
$reTag = "/{pt\:recent_(views|id|sorting){1}_([0-9]+)}([\s\S]*?){\/pt}/i";
preg_match_all($reTag, $tpl, $matches);
for ($l = 0; $l < count($matches[0]); $l++) {
    if (isset($matches[0][$l])) {
        $limit = $matches[2][$l];
        $orderby = $matches[1][$l];
        $sql = "select * from x_post where status=1 and lang_id='$langid' and site_id='$siteid'  order by $orderby desc limit $limit";
        $result = mysqli_query($con, $sql);
        if ($result->num_rows > 0) {
            $i = 0;
            while ($row = mysqli_fetch_array($result)) {
                preg_match($re_images, $row["thumbnail"], $thumbnail);
                if ($thumbnail[1] == null) {
                    $thumbnail2 = '/img/default.png';
                } else {
                    $thumbnail2 = $thumbnail[1];
                }
                $arrdata[$i]['title'] = $row["title"];
                $arrdata[$i]["href"] = $siteurl . $langurl . $row["id"] . "/" . myurlencode($row["title"]) . "/";
                $arrdata[$i]["img"] = $thumbnail2;
                $arrdata[$i]['views'] = $row["views"];
                if ($row["brief"] != null && $row["brief"] != '') {
                    $arrdata[$i]['brief'] = $row["brief"];
                } else {
                    $arrdata[$i]['brief'] = mb_substr(strip_tags($row["content"]), 0, 80, 'utf-8');
                }
                $arrdata[$i]['year'] = date('Y', $row["time"]);
                $arrdata[$i]['month'] = date('m', $row["time"]);
                $arrdata[$i]['day'] = date('d', $row["time"]);
                $i++;
            }
        }
        $tpl = str_replace($matches[0][$l], looponce2($matches[0][$l], $reTag, $arrdata), $tpl);
    }
}
//替换最近第几条记录的标题、链接、图片、正文片段，指定分类
$reTag = "/{\[(latest|top){1}_([a-z0-9]+)_([a-z]+)_(\d+)\]}/i";
preg_match_all($reTag, $tpl, $matches);
for ($i = 0; $i < count($matches[0]); $i++) {
    $out = '';
    if ($matches[1][$i] == 'latest') {
        $orderby = 'id';
    } else {
        $orderby = 'sorting';
    }
    $matchtype = $matches[2][$i];
    $start = $matches[3][$i];
    $no = $matches[4][$i];
    $catids = '';
    foreach ($subcatarr as $x => $x_value) {
        unset($tmparr);
        $tmparr = explode("_", $x);
        if ($tmparr[0] == $cataliasidarr[$start]) {
            $catids = $catids . ',' . $catarrori[$x_value];
        }
    }
    if (array_key_exists($start, $cataliasidarr)) {
        if ($catids != '') {
            $catids = $cataliasidarr[$start] . $catids;
        } else {
            $catids = $cataliasidarr[$start];
        }
    }
    $sql = "select * from x_post where status=1 and lang_id='$langid' and site_id='$siteid' and (category_id in ($catids) or category_id2 in ($catids))  order by $orderby desc limit $no,1";
    $result = mysqli_query($con, $sql);
    if ($result->num_rows > 0) {
        $row = mysqli_fetch_array($result);
        if ($matchtype == 'title') {
            $tpl = str_replace($matches[0][$i], $row["title"], $tpl);
        }
        if ($matchtype == 'href') {
            $out = $siteurl . $langurl . $row["id"] . "/" . myurlencode($row["title"]) . "/";
            $tpl = str_replace($matches[0][$i], $out, $tpl);
        }
        if ($matchtype == 'time') {
            $tpl = str_replace($matches[0][$i], $row["time"], $tpl);
        }
        if ($matchtype == 'date') {
            $tpl = str_replace($matches[0][$i], date('m.d.Y', strtotime($row["time"])), $tpl);
        }
        if ($matchtype == 'brief') {
            if ($row["brief"] != null && $row["brief"] != '') {
                $tpl = str_replace($matches[0][$i], $row["brief"], $tpl);
            } else {
                $tpl = str_replace($matches[0][$i], mb_substr(strip_tags($row["content"]), 0, 80, 'utf-8'), $tpl);
            }
        }
        if ($matchtype == 'day') {
            $tpl = str_replace($matches[0][$i], date('d', strtotime($row["time"])), $tpl);
        }
        if ($matchtype == 'month') {
            $tpl = str_replace($matches[0][$i], date('m', strtotime($row["time"])), $tpl);
        }
        if ($matchtype == 'year') {
            $tpl = str_replace($matches[0][$i], date('Y', strtotime($row["time"])), $tpl);
        }
        if ($matchtype == 'titlehref') {
            $out = "<a href='" . $siteurl . $langurl . $row["id"] . "/" . myurlencode($row["title"]) . "/'>" . $row["title"] . "</a>";
            $tpl = str_replace($matches[0][$i], $out, $tpl);
        }
        if ($matchtype == 'img') {
            preg_match($re_images, $row["thumbnail"], $thumbnail);
            if ($thumbnail[1] == null) {
                $thumbnail2 = '/img/default.png';
            } else {
                $thumbnail2 = $thumbnail[1];
            }
            $tpl = str_replace($matches[0][$i], $thumbnail2, $tpl);
        }
        if (checkinput('int', $matchtype, 1, 5) != '') {
            $out = br_mb_substr($row["content"], $matchtype);
            $tpl = str_replace($matches[0][$i], $out, $tpl);
        }
    }
}
//替换推荐内容，从第几条开始，共几条
$reTag = "/{pt\:recommend_([0-9]+)_([0-9]+)}([\s\S]*?){\/pt}/i";
preg_match_all($reTag, $tpl, $matches);
for ($l = 0; $l < count($matches[0]); $l++) {
    if (isset($matches[0][$l])) {
        unset($arrdata);
        $start = $matches[1][$l];
        $limit = $matches[2][$l];
        $sql = "select * from x_post where status=1 and recommend=1 and lang_id='$langid' and site_id='$siteid'  order by sorting desc,id desc limit $start,$limit";
        $result = mysqli_query($con, $sql);
        if ($result->num_rows > 0) {
            $i = 0;
            while ($row = mysqli_fetch_array($result)) {
                preg_match($re_images, $row["thumbnail"], $thumbnail);
                if ($thumbnail[1] == null) {
                    $thumbnail2 = '/img/default.png';
                } else {
                    $thumbnail2 = $thumbnail[1];
                }
                preg_match($re_images, $row["banner"], $productbanner);
                $arrdata[$i]['title'] = $row["title"];
                $arrdata[$i]["href"] = $siteurl . $langurl . $row["id"] . "/" . myurlencode($row["title"]) . "/";
                $arrdata[$i]["img"] = $thumbnail2;
                $arrdata[$i]["banner"] = $productbanner[1];
                $arrdata[$i]["year"] = date('Y', strtotime($row["time"]));
                $arrdata[$i]["month"] = date('m', strtotime($row["time"]));
                $arrdata[$i]["day"] = date('d', strtotime($row["time"]));
                $arrdata[$i]['views'] = $row["views"];
                $arrdata[$i]['categoryhref'] = $siteurl . $langurl . 'category_' . $row["category_id"] . '/' . myurlencode($catarrall[$row["category_id"] . '_' . $langid]) . '/';
                if ($row["brief"] != null && $row["brief"] != '') {
                    $arrdata[$i]['brief'] = $row["brief"];
                } else {
                    $arrdata[$i]['brief'] = mb_substr(strip_tags($row["content"]), 0, 80, 'utf-8');
                }
                $i++;
            }
        }
        //var_dump($arrdata);
        $tpl = str_replace($matches[0][$l], looponce2($matches[0][$l], $reTag, $arrdata), $tpl);
        ////////////////////
    }
}
//替换推荐内容，从头开始
$reTag = "/{pt\:recommend_([0-9]+)}([\s\S]*?){\/pt}/i";
preg_match_all($reTag, $tpl, $matches);
for ($l = 0; $l < count($matches[0]); $l++) {
    if (isset($matches[0][$l])) {
        unset($arrdata);
        $limit = $matches[1][$l];
        if ($limit > count($recommendidsarr)) {
            $limit = count($recommendidsarr);
        }
        for ($i = 0; $i < $limit; $i++) {
            $arrdata[$i]['title'] = $recommendtitlesarr[$i];
            $arrdata[$i]['href'] = $siteurl . $langurl . $recommendidsarr[$i] . "/" . myurlencode($recommendtitlesarr[$i]) . "/";
            $arrdata[$i]['img'] = $recommendthumbnailsarr[$i];
            $arrdata[$i]['brief'] = $recommendbriefsarr[$i];
            $arrdata[$i]['price'] = $recommendpricesarr[$i];
            $arrdata[$j]['year'] = date('Y', strtotime($recommendtimearr[$i]));
            $arrdata[$j]['month'] = date('m', strtotime($recommendtimearr[$i]));
            $arrdata[$j]['day'] = date('d', strtotime($recommendtimearr[$i]));
        }
        //var_dump($arrdata);
        $tpl = str_replace($matches[0][$l], looponce($matches[0][$l], $reTag, $arrdata), $tpl);
        ////////////////////
    }
}

//替换推荐的第几条记录的标题、链接、图片、正文片段，不指定分类
$reTag = "/{\[recommend_([a-z]+)_(\d+)\]}/i";
preg_match_all($reTag, $tpl, $matches);
for ($i = 0; $i < count($matches[0]); $i++) {
    $out = '';
    $matchtype = $matches[1][$i];
    $no = $matches[2][$i];
    if ($recommendtitlesarr[$no] != null) {

        switch ($matchtype) {
            case "title":
                $tpl = str_replace($matches[0][$i], $recommendtitlesarr[$no], $tpl);
            case "href":
                $out = $siteurl . $langurl . $recommendidsarr[$no] . "/" . myurlencode($recommendtitlesarr[$no]) . "/";
                $tpl = str_replace($matches[0][$i], $out, $tpl);
            case "img":
                $tpl = str_replace($matches[0][$i], $recommendthumbnailsarr[$no], $tpl);
            case "brief":
                $tpl = str_replace($matches[0][$i], $recommendbriefsarr[$no], $tpl);
            case "price":
                $tpl = str_replace($matches[0][$i], $recommendpricesarr[$no], $tpl);
            case "year":
                $tpl = str_replace($matches[0][$i], date('Y', strtotime($recommendtimearr[$no])), $tpl);
            case "month":
                $tpl = str_replace($matches[0][$i], date('m', strtotime($recommendtimearr[$no])), $tpl);
            case "day":
                $tpl = str_replace($matches[0][$i], date('d', strtotime($recommendtimearr[$no])), $tpl);
        }

        /*
                if ($matchtype == 'title') {
                    $tpl = str_replace($matches[0][$i], $recommendtitlesarr[$no], $tpl);
                }
                if ($matchtype == 'href') {
                    $out = $siteurl . $langurl . $recommendidsarr[$no] . "/" . ($recommendtitlesarr[$no]) . "/";
                    $tpl = str_replace($matches[0][$i], $out, $tpl);
                }
                if ($matchtype == 'img') {
                    $tpl = str_replace($matches[0][$i], $recommendthumbnailsarr[$no], $tpl);
                }
                if ($matchtype == 'brief') {
                    $tpl = str_replace($matches[0][$i], $recommendbriefsarr[$no], $tpl);
                }
                if ($matchtype == 'price') {
                    $tpl = str_replace($matches[0][$i], $recommendpricesarr[$no], $tpl);
                }
        */
    }
}
//查找结果
if ($pagetype == 'search') {
    $tpl = str_replace("{[search]}", $search, $tpl);
    //单循环
    $reTag = "/{pt\:search_([0-9]+)}([\s\S]*?){\/pt}/i";
    preg_match_all($reTag, $tpl, $matches);
    for ($l = 0; $l < count($matches[0]); $l++) {
        if (isset($matches[0][$l])) {
            unset($arrdata);
            $limit = $matches[1][$l];
            $sql = "SELECT count(*) FROM x_post where status=1 and title like '%$search%' and lang_id='$langid' and site_id='$siteid'  " ;
            $tpagesresult = mysqli_query($con, $sql);
            if ($tpagesresult->num_rows > 0) {
                $tpagesrow = mysqli_fetch_array($tpagesresult);
                $tpages = ceil($tpagesrow[0] / $limit);
                if ($page <= 0 || $page == '') {
                    $page = 1;
                }
                if ($tpages <= 0 || $tpages == '') {
                    $tpages = 1;
                }
                $reload = $siteurl . $langurl . "search/" . myurlencode($search) . "/";
                $startfrom = ($page - 1) * $limit;
                $sql = "select * from x_post where status=1 and title like '%$search%' and lang_id='$langid' and site_id='$siteid'  order by sorting desc,id desc limit $startfrom,$limit";
                $result = mysqli_query($con, $sql);
                if ($result->num_rows > 0) {
                    $i = 0;
                    while ($row = mysqli_fetch_array($result)) {
                        preg_match($re_images, $row["thumbnail"], $thumbnail);
                        if ($thumbnail[1] == null) {
                            $thumbnail2 = '/img/default.png';
                        } else {
                            $thumbnail2 = $thumbnail[1];
                        }
                        $arrdata[$i]['title'] = $row["title"];
                        $arrdata[$i]["href"] = $siteurl . $langurl . $row["id"] . "/" . myurlencode($row["title"]) . "/";
                        $arrdata[$i]["img"] = $thumbnail2;
                        $arrdata[$i]["year"] = date('Y', strtotime($row["time"]));
                        $arrdata[$i]["month"] = date('m', strtotime($row["time"]));
                        $arrdata[$i]["day"] = date('d', strtotime($row["time"]));
                        $arrdata[$i]['views'] = $row["views"];
                        if ($row["brief"] != null && $row["brief"] != '') {
                            $arrdata[$i]['brief'] = $row["brief"];
                        } else {
                            $arrdata[$i]['brief'] = mb_substr(strip_tags($row["content"]), 0, 80, 'utf-8');
                        }
                        $i++;
                    }
                }
            }
            $tpl = str_replace($matches[0][$l], looponce($matches[0][$l], $reTag, $arrdata), $tpl);
            //替换分页
            $tpl = str_replace('{[searchpages]}', paginate_two($reload, $page, $tpages, 4, ''), $tpl);
        }
    }
    //双循环
    $reTag = "/{pt\:search_([0-9]+)_([0-9]+)}([\s\S]*?){\/pt}/i";
    preg_match_all($reTag, $tpl, $matches);
    for ($l = 0; $l < count($matches[0]); $l++) {
        if (isset($matches[0][$l])) {
            unset($arrdata);
            $limit = $matches[1][$l];
            $num = $matches[2][$l];
            $sql = "SELECT count(*) FROM x_post where status=1 and title like '%$search%' and lang_id='$langid' and site_id='$siteid' " ;
            $tpagesresult = mysqli_query($con, $sql);
            $limit2 = $limit * $num;
            if ($tpagesresult->num_rows > 0) {
                $tpagesrow = mysqli_fetch_array($tpagesresult);
                $tpages = ceil($tpagesrow[0] / $limit2);
                if ($page <= 0 || $page == '') {
                    $page = 1;
                }
                if ($tpages <= 0 || $tpages == '') {
                    $tpages = 1;
                }
                $reload = $siteurl . $langurl . "search/" . myurlencode($search) . "/";
                $startfrom = ($page - 1) * $limit2;
                $sql = "select * from x_post where status=1 and title like '%$search%' and lang_id='$langid' and site_id='$siteid'  order by sorting desc,id desc limit $startfrom,$limit2";
                $result = mysqli_query($con, $sql);
                if ($result->num_rows > 0) {
                    $i = 0;
                    $j = 0;
                    while ($row = mysqli_fetch_array($result)) {
                        preg_match($re_images, $row["thumbnail"], $thumbnail);
                        if ($thumbnail[1] == null) {
                            $thumbnail2 = $thumbnail[1];
                        } else {
                            $thumbnail2 = $thumbnail[1];
                        }
                        $arrdata[$i]['content'][$j]['title'] = $row["title"];
                        $arrdata[$i]['content'][$j]["href"] = $siteurl . $langurl . $row["id"] . "/" . myurlencode($row["title"]) . "/";
                        $arrdata[$i]['content'][$j]["img"] = $thumbnail2;
                        $arrdata[$i]['content'][$j]["year"] = date('Y', strtotime($row["time"]));
                        $arrdata[$i]['content'][$j]["month"] = date('m', strtotime($row["time"]));
                        $arrdata[$i]['content'][$j]["day"] = date('d', strtotime($row["time"]));
                        $arrdata[$i]['content'][$j]['views'] = $row["views"];
                        if ($row["brief"] != null && $row["brief"] != '') {
                            $arrdata[$i]['content'][$j]['brief'] = $row["brief"];
                        } else {
                            $arrdata[$i]['content'][$j]['brief'] = mb_substr(strip_tags($row["content"]), 0, 80, 'utf-8');
                        }
                        $arrdata[$i]['content'][$j]['id'] = $row["id"];
                        $arrdata[$i]['content'][$j]['price'] = $row["price"];
                        $arrdata[$i]['content'][$j]['onsale'] = $row["onsale"];
                        $arrdata[$i]['content'][$j]['originalprice'] = $row["originalprice"];
                        $arrdata[$i]['content'][$j]['weight'] = $row["weight"];
                        $j++;
                        if ($j >= $num) {
                            $j = 0;
                            $i++;
                        }
                    }
                }
            }
            $tpl = str_replace($matches[0][$l], looptwice($matches[0][$l], $reTag, $arrdata), $tpl);
            //替换分页
            $tpl = str_replace('{[searchpages]}', paginate_two($reload, $page, $tpages, 4, ''), $tpl);
        }
    }
}

//TAG结果
if ($pagetype == 'tag') {
    $tpl = str_replace("{[tag]}", $tag, $tpl);
    //单循环
    $reTag = "/{pt\:tag_([0-9]+)}([\s\S]*?){\/pt}/i";
    preg_match_all($reTag, $tpl, $matches);
    for ($l = 0; $l < count($matches[0]); $l++) {
        if (isset($matches[0][$l])) {
            unset($arrdata);
            $limit = $matches[1][$l];
            $sql = "SELECT count(*) FROM x_post where status=1 and (tag1 like '%$tag%' or tag2 like '%$tag%' or tag3 like '%$tag%' or tag4 like '%$tag%') and lang_id='$langid' and site_id='$siteid' " ;
            $tpagesresult = mysqli_query($con, $sql);
            if ($tpagesresult->num_rows > 0) {
                $tpagesrow = mysqli_fetch_array($tpagesresult);
                $tpages = ceil($tpagesrow[0] / $limit);
                if ($page <= 0 || $page == '') {
                    $page = 1;
                }
                if ($tpages <= 0 || $tpages == '') {
                    $tpages = 1;
                }
                $reload = $siteurl . $langurl . "tag/" . myurlencode($tag) . "/";
                $startfrom = ($page - 1) * $limit;
                $sql = "select * from x_post where status=1 and (tag1 like '%$tag%' or tag2 like '%$tag%' or tag3 like '%$tag%' or tag4 like '%$tag%') and lang_id='$langid' and site_id='$siteid'  order by sorting desc,id desc limit $startfrom,$limit";
                $result = mysqli_query($con, $sql);
                if ($result->num_rows > 0) {
                    $i = 0;
                    while ($row = mysqli_fetch_array($result)) {
                        preg_match($re_images, $row["thumbnail"], $thumbnail);
                        if ($thumbnail[1] == null) {
                            $thumbnail2 = '/img/default.png';
                        } else {
                            $thumbnail2 = $thumbnail[1];
                        }
                        $arrdata[$i]['title'] = $row["title"];
                        $arrdata[$i]["href"] = $siteurl . $langurl . $row["id"] . "/" . myurlencode($row["title"]) . "/";
                        $arrdata[$i]["img"] = $thumbnail2;
                        $arrdata[$i]["year"] = date('Y', strtotime($row["time"]));
                        $arrdata[$i]["month"] = date('m', strtotime($row["time"]));
                        $arrdata[$i]["day"] = date('d', strtotime($row["time"]));
                        $arrdata[$i]["views"] = $row["views"];
                        if ($row["brief"] != null && $row["brief"] != '') {
                            $arrdata[$i]['brief'] = $row["brief"];
                        } else {
                            $arrdata[$i]['brief'] = mb_substr(strip_tags($row["content"]), 0, 80, 'utf-8');
                        }
                        $i++;
                    }
                }
            }
            $tpl = str_replace($matches[0][$l], looponce($matches[0][$l], $reTag, $arrdata), $tpl);
            //替换分页
            $tpl = str_replace('{[tagpages]}', paginate_two($reload, $page, $tpages, 4, ''), $tpl);
        }
    }
    //双循环
    $reTag = "/{pt\:tag_([0-9]+)_([0-9]+)}([\s\S]*?){\/pt}/i";
    preg_match_all($reTag, $tpl, $matches);
    for ($l = 0; $l < count($matches[0]); $l++) {
        if (isset($matches[0][$l])) {
            unset($arrdata);
            $limit = $matches[1][$l];
            $num = $matches[2][$l];
            $sql = "SELECT count(*) FROM x_post where status=1 and (tag1 like '%$tag%' or tag2 like '%$tag%' or tag3 like '%$tag%' or tag4 like '%$tag%') and lang_id='$langid' and site_id='$siteid'" ;
            $tpagesresult = mysqli_query($con, $sql);
            $limit2 = $limit * $num;
            if ($tpagesresult->num_rows > 0) {
                $tpagesrow = mysqli_fetch_array($tpagesresult);
                $tpages = ceil($tpagesrow[0] / $limit2);
                if ($page <= 0 || $page == '') {
                    $page = 1;
                }
                if ($tpages <= 0 || $tpages == '') {
                    $tpages = 1;
                }
                $reload = $siteurl . $langurl . "tag/" . myurlencode($tag) . "/";
                $startfrom = ($page - 1) * $limit2;
                $sql = "select * from x_post where status=1 and (tag1 like '%$tag%' or tag2 like '%$tag%' or tag3 like '%$tag%' or tag4 like '%$tag%') and lang_id='$langid' and site_id='$siteid'  order by sorting desc,id desc limit $startfrom,$limit2";
                $result = mysqli_query($con, $sql);
                if ($result->num_rows > 0) {
                    $i = 0;
                    $j = 0;
                    while ($row = mysqli_fetch_array($result)) {
                        preg_match($re_images, $row["thumbnail"], $thumbnail);
                        if ($thumbnail[1] == null) {
                            $thumbnail2 = '/img/default.png';
                        } else {
                            $thumbnail2 = $thumbnail[1];
                        }
                        $arrdata[$i]['content'][$j]['title'] = $row["title"];
                        $arrdata[$i]['content'][$j]["href"] = $siteurl . $langurl . $row["id"] . "/" . myurlencode($row["title"]) . "/";
                        $arrdata[$i]['content'][$j]["img"] = $thumbnail2;
                        $arrdata[$i]['content'][$j]["year"] = date('Y', strtotime($row["time"]));
                        $arrdata[$i]['content'][$j]["month"] = date('m', strtotime($row["time"]));
                        $arrdata[$i]['content'][$j]["day"] = date('d', strtotime($row["time"]));
                        $arrdata[$i]['content'][$j]["views"] = $row["views"];
                        if ($row["brief"] != null && $row["brief"] != '') {
                            $arrdata[$i]['content'][$j]['brief'] = $row["brief"];
                        } else {
                            $arrdata[$i]['content'][$j]['brief'] = mb_substr(strip_tags($row["content"]), 0, 80, 'utf-8');
                        }
                        $arrdata[$i]['content'][$j]['id'] = $row["id"];
                        $arrdata[$i]['content'][$j]['price'] = $row["price"];
                        $arrdata[$i]['content'][$j]['onsale'] = $row["onsale"];
                        $arrdata[$i]['content'][$j]['originalprice'] = $row["originalprice"];
                        $arrdata[$i]['content'][$j]['weight'] = $row["weight"];
                        $j++;
                        if ($j >= $num) {
                            $j = 0;
                            $i++;
                        }
                    }
                }
            }
            $tpl = str_replace($matches[0][$l], looptwice($matches[0][$l], $reTag, $arrdata), $tpl);
            //替换分页
            $tpl = str_replace('{[tagpages]}', paginate_two($reload, $page, $tpages, 4, ''), $tpl);
        }
    }
}
//对于不在线销售的商品，不显示其价格和单位部分
$reTag = "/{noshopping}([\s\S]*?){\/noshopping}/i";
preg_match_all($reTag, $tpl, $matches);
for ($i = 0; $i < count($matches[0]); $i++) {
    $tpl = str_replace($matches[0][$i], "", $tpl);
}
$tpl = str_replace("{shopping}", "", $tpl);
$tpl = str_replace("{/shopping}", "", $tpl);
//替换gallery
$tpl = str_replace("{[gallery]}", $siteurl . $langurl . "gallery/", $tpl);
if ($pagetype == 'gallery') {
    $out = '';
    $gallerypath = $path . '/gallery';
    $listdir = scandir($gallerypath);
    for ($i = 0; $i < count($listdir); $i++) {
        if ($listdir[$i] != '.' && $listdir[$i] != '..') {
            if (checkinput('imgfile', $listdir[$i], 1, 50) != '') {
                $imgalt = '';
                if (is_file($gallerypath . '/' . $listdir[$i] . '.' . $langid . '.txt')) {
                    $imgalt = get($gallerypath . '/' . $listdir[$i] . '.' . $langid . '.txt');
                }
                $out = $out . '<img layer-pid="" layer-src="' . $siteurl2 . 'gallery/' . $listdir[$i] . '" src="' . $siteurl2 . 'gallery/thumbnail/' . $listdir[$i] . '" alt="' . $imgalt . '" title="' . $imgalt . '">';
            }
        }
    }
    $tpl = str_replace("{[gallery_photos]}", $out, $tpl);
}
//替换download
$tpl = str_replace("{[download]}", $siteurl . $langurl . "download/", $tpl);      //这是导航
if ($pagetype == 'download' || 1 == 1) {
    $reTag = "/{pt\:download_([0-9]+)}([\s\S]*?){\/pt}/i";
    preg_match_all($reTag, $tpl, $matches);
    for ($l = 0; $l < count($matches[0]); $l++) {
        if (isset($matches[0][$l])) {
            unset($arrdata);
            unset($downloadarr);
            $limit = $matches[1][$l];
            $downloadpath = $path . '/download';
            $listdir = scandir($downloadpath);
            if ($page <= 0 || $page == '') {
                $page = 1;
            }
            $j = 0;
            $k = 0;
            for ($i = 0; $i < count($listdir); $i++) {
                if ($listdir[$i] != '.' && $listdir[$i] != '..') {
                    if (checkinput('zipfile', $listdir[$i], 1, 50) != '' || checkinput('pdffile', $listdir[$i], 1, 50) != '') {
                        if ($k >= ($page - 1) * $limit && $k < $page * $limit) {
                            if ($j < $limit) {
                                $downloadarr[$j] = $listdir[$i];
                                $j++;
                            }
                        }
                        $k++;
                    }
                }
            }
            $tpages = ceil($k / $limit);
            if ($tpages <= 0 || $tpages == '') {
                $tpages = 1;
            }
            $reload = $siteurl . $langurl . "download/";
            for ($i = 0; $i < count($downloadarr); $i++) {
                if (is_file($downloadpath . '/' . $downloadarr[$i] . '.' . $langid . '.txt')) {
                    $myfile = fopen($downloadpath . '/' . $downloadarr[$i] . '.' . $langid . '.txt', "r") or die("Unable to open file!");
                    $description = fread($myfile, filesize($downloadpath . '/' . $downloadarr[$i] . '.' . $langid . '.txt'));
                    fclose($myfile);
                } else {
                    $description = $downloadarr[$i];
                }
                $arrdata[$i]['title'] = $description;
                $arrdata[$i]['href'] = $siteurl2 . 'download/' . $downloadarr[$i];
                $arrdata[$i]['size'] = getRealSize(filesize($downloadpath . '/' . $downloadarr[$i]));
            }
            $tpl = str_replace($matches[0][$l], looponce($matches[0][$l], $reTag, $arrdata), $tpl);
            $tpl = str_replace('{[downloadpages]}', paginate_two($reload, $page, $tpages, 4, ''), $tpl);
        }
    }
}
//检查客户是否登录，放到checkout和client前面
$clientlogin = 0;
$clientid = '';
if ($pagetype == 'client' || $pagetype == 'checkout') {
    if (!isset($_COOKIE["client1"]) || !isset($_COOKIE["client2"])) {
        $clientlogin = 0;
    }
    $clientid = checkinput('int', $_COOKIE["client1"], 1, 11);
    $clientcookie = checkinput('str', $_COOKIE["client2"], 32, 32);
    $sqlcheck = "SELECT * FROM x_client where id='$clientid' and pwd='$clientcookie'";
    $resultcheck = mysqli_query($con, $sqlcheck);
    if (!$resultcheck) {
        $clientlogin = 0;
    } else {
        if ($resultcheck->num_rows == 0) {
            $clientlogin = 0;
        } else {
            $clientlogin = 1;
        }
    }
}
//替换购物车链接
$tpl = str_replace("{[cart]}", $siteurl . $langurl . "cart/", $tpl);
if ($pagetype == 'cart') {
    $deleverycountry = $_COOKIE["deliverycountry"];
    if (!in_array($deleverycountry, $shippingcountryarr)) {
        setcookie("deliverycountry", $shippingcountryarr[0], time() + 3600 * 24 * 365, $cookiepath);
    }
}
//替换结账链接
$tpl = str_replace("{[checkout]}", $siteurl . $langurl . "checkout/", $tpl);
if ($pagetype == 'checkout') {
    if ($clientlogin == 0) {
        Header("Location: " . $siteurl . $langurl . "client/checkout/");
    }
    $cartout = '';
    $cookie = $_COOKIE["cart"];
    $sql = "SELECT * FROM x_post where id in ($cookie) " ;
    $result = mysqli_query($con, $sql);
    if ($result->num_rows > 0) {
        $subtotal = 0;
        $weight = 0;
        $itemarr = array();
        $itemidarr = array();
        $itemurlarr = array();
        while ($row = mysqli_fetch_array($result)) {
            $quantity = $_COOKIE["quantity" . $row["id"]];
            $quantity = checkinput('int', $quantity, 1, 11);
            if ($quantity != '') {
                $cartout = $cartout . "<tr><td>&nbsp;&nbsp;" . $row["title"] . "</td><td>" . $row["price"] . " X " . $quantity . " = " . $row["price"] * $quantity . "</td></tr>";
                $subtotal = $subtotal + $row["price"] * $quantity;
                $weight = $weight + $row["weight"] * $quantity;
                array_push($itemarr, $row["title"]);
                array_push($itemarr, $quantity);
                array_push($itemidarr, $row["id"]);
                array_push($itemidarr, $quantity);
                array_push($itemurlarr, $siteurl . $langurl . $row["id"] . '/');
                array_push($itemurlarr, $row["title"]);
                array_push($itemurlarr, $quantity);
            }
        }
        if ($cartout != '') {
            $cartout = $cartout . "<tr><td>" . $arr["weight"] . " " . $weight . " kg</td><td><strong>" . $arr["subtotal"] . " " . $subtotal . "</strong></td></tr>";
            $calcweight = ceil($weight / 0.5);
            $deleverycountry = $_COOKIE["deliverycountry"];
            if (!in_array($deleverycountry, $shippingcountryarr) || $deleverycountry == null) {
                $cartout = "<tr><td></td><td>" . $arr["unsupportedcountry"] . '</td></tr>';
            } else {
                $cartout = $cartout . "<tr><td>" . $arr["shippingcountry"] . "</td><td>" . $deleverycountry . '</td></tr>';
                $costarr = explode("|", $countryweight[$deleverycountry]);
                if ($calcweight < 2) {
                    $shippingcost = $costarr[0];
                } else {
                    $shippingcost = $costarr[0] + ($calcweight - 1) * $costarr[1];
                }
                $cartout = $cartout . "<tr><td>" . $arr["shippingcost"] . "</td><td>" . $shippingcost . "</td></tr>";
                $cartout = $cartout . "<tr><td><strong>" . $arr["total"] . "</strong></td><td><strong>" . ($shippingcost + $subtotal) . "</strong></td></tr>";
                $cartdetail = ($weight . "," . $currency . "," . $subtotal . "," . $shippingcost . "," . implode(",", $itemidarr));
                $brief = implode(",", $itemurlarr);
                //$brief = $currency . " " . ($shippingcost + $subtotal) . "," . implode(",", $itemarr);         //和下面一条顺序不能变
                array_unshift($itemarr, "Remark", $siteid . "," . $clientid . "," . $lang . "," . implode(",", $itemidarr));       //和上面一条顺序不能变
                $countrycode = $countrycodearr[array_search($deleverycountry, $countryarr)];
                $sql = "SELECT * FROM x_orders where client_id='$clientid' and cartdetail='$cartdetail' and site_id='$siteid' and payment_status=''";
                $result = mysqli_query($con, $sql);
                if ($result->num_rows == 0) {
                    $createtime = date('y-m-d h:i:s');
                    $sql = "insert into x_orders set client_id='$clientid',site_id='$siteid',cartdetail='$cartdetail',createtime='$createtime',country_code='$countrycode',brief='$brief'";
                    mysqli_query($con, $sql);
                    $result3 = mysqli_query($con, "select @@IDENTITY");
                    $row3 = mysqli_fetch_array($result3);
                    $order_id = $row3[0];
                    $order = date('ymd') . $row3[0];
                    $sql = "update x_orders set order_no='$order' where id='$order_id'";
                    mysqli_query($con, $sql);
                } else {
                    $row = mysqli_fetch_array($result);
                    $order = $row["order_no"];
                }

                /*
                myecho($pp_hostname);
                myecho($pp_user);
                myecho($pp_pwd);
                myecho($pp_signature);
                die;
                */
                $pp_btn = paypal($pp_hostname, $pp_user, $pp_pwd, $pp_signature, $order, $currency, $subtotal, $shippingcost, $itemarr, $siteurl . $langurl . "cart/", $siteurl . $langurl . "complete/", $base . "ipn.php");
            }
            if ($pp_btn != "") {
                $pp_btn = '<form action="https://www.' . $pp_hostname . '/cgi-bin/webscr" method="post" target="_top">
    <input type="hidden" name="cmd" value="_s-xclick">
    <input type="hidden" name="encrypted" value="' . $pp_btn . '">
    <button class="btn btn-primary" name="submit">' . $arr["paynow"] . '</button>
</form>';
            } else {
                $pp_btn = "<h5>" . $arr["cannotpay"] . "</h5>";
            }
            $cartout = "<table class='table table-hover'><thead><tr><th>" . $arr['order'] . ":" . $order . "</th><th>" . $arr['currency'] . ":" . $currency . "</th></tr></thead><tbody>" . $cartout . "</tbody><tfoot><tr><td colspan='2'>" . $pp_btn . "</td></tr></tfoot>";
        } else {
            $cartout = "<p>" . $arr["cartisempty"] . '</p>';
        }
    } else {
        $cartout = "<p>" . $arr["cartisempty"] . '</p>';
    }
    $tpl = str_replace("{[cartout]}", $cartout, $tpl);
}
//替换客户相关内容
$tpl = str_replace("{[client]}", $siteurl . $langurl . "client/", $tpl);
if ($pagetype == 'client') {
    if ($_GET["act"] == "logout") {
        setcookie("client1", "", time() - 3600 * 24 * 365, $cookiepath);
        setcookie("client2", "", time() - 3600 * 24 * 365, $cookiepath);
        Header("Location: " . $siteurl . $langurl . "client/");
    }
    $clientout = '';
    $submit = checkinput('strutf', @$_POST["submit"], 0, 20);
    if ($submit != "") {
        $email = checkinput('email', @$_POST["email"], 5, 100);
        $pwd = checkinput('pwd', @$_POST["pwd"], 6, 50);
        $country = checkinput('strutf', @$_POST["country"], 1, 20);
        $state = checkinput('str', @$_POST["state"], 1, 20);
        $city = checkinput('str', @$_POST["city"], 1, 40);
        $address1 = checkinput('str', @$_POST["address1"], 1, 100);
        $address2 = checkinput('str', @$_POST["address2"], 1, 100);
        $firstname = checkinput('str', @$_POST["firstname"], 1, 32);
        $lastname = checkinput('str', @$_POST["lastname"], 1, 32);
        $zip = checkinput('str', @$_POST["zip"], 1, 32);
        $phone = checkinput('str', @$_POST["phone"], 1, 32);
    }
    if ($submit == "modify") {
        $sql = "update x_client set country='$country',state='$state',city='$city',address1='$address1',address2='$address2',first_name='$firstname',last_name='$lastname',zip='$zip',phone='$phone' where id='$clientid' and pwd='$clientcookie'";
        mysqli_query($con, $sql);
        Header("Location: " . $siteurl . $langurl . "client/");
    }
    if ($clientlogin == 1) {
        $rowcheck = mysqli_fetch_array($resultcheck);
        $reTag = "/{pt\:client_no}([\s\S]*?){\/pt}/i";
        preg_match_all($reTag, $tpl, $matches);
        for ($l = 0; $l < count($matches[0]); $l++) {
            $tpl = str_replace($matches[0][$l], "", $tpl);
        }
        $tpl = str_replace("{[clientemail]}", $rowcheck["email"], $tpl);
        $tpl = str_replace("{[clientstate]}", $rowcheck["state"], $tpl);
        $tpl = str_replace("{[clientcity]}", $rowcheck["city"], $tpl);
        $tpl = str_replace("{[clientaddress1]}", $rowcheck["address1"], $tpl);
        $tpl = str_replace("{[clientaddress2]}", $rowcheck["address2"], $tpl);
        $tpl = str_replace("{[clientfirstname]}", $rowcheck["first_name"], $tpl);
        $tpl = str_replace("{[clientlastname]}", $rowcheck["last_name"], $tpl);
        $tpl = str_replace("{[clientzip]}", $rowcheck["zip"], $tpl);
        $tpl = str_replace("{[clientphone]}", $rowcheck["phone"], $tpl);
        $clientcountryoption = '';
        for ($i = 0; $i < count($countryarr); $i++) {
            if ($rowcheck["country"] == $countryarr[$i]) {
                $clientcountryoption = $clientcountryoption . '<option value="' . $countryarr[$i] . '" selected>' . $countryarr[$i] . '</option>';
            } else {
                $clientcountryoption = $clientcountryoption . '<option value="' . $countryarr[$i] . '" >' . $countryarr[$i] . '</option>';
            }
        }
        $tpl = str_replace("{[clientcountry]}", $clientcountryoption, $tpl);
        //客户的订单
        $reTag = "/{pt\:clientorders_([0-9]+)}([\s\S]*?){\/pt}/i";
        preg_match_all($reTag, $tpl, $matches);
        for ($l = 0; $l < count($matches[0]); $l++) {
            if (isset($matches[0][$l])) {
                unset($arrdata);
                $limit = $matches[1][$l];
                $sql = "SELECT count(*) FROM x_orders where client_id='$clientid' and site_id='$siteid' and payment_status!=''";
                $tpagesresult = mysqli_query($con, $sql);
                if ($tpagesresult->num_rows > 0) {
                    $tpagesrow = mysqli_fetch_array($tpagesresult);
                    $tpages = ceil($tpagesrow[0] / $limit);
                    if ($page <= 0 || $page == '') {
                        $page = 1;
                    }
                    if ($tpages <= 0 || $tpages == '') {
                        $tpages = 1;
                    }
                    $reload = $siteurl . $langurl . "client/";
                    $startfrom = ($page - 1) * $limit;
                    $sql = "select * from x_orders where client_id='$clientid' and site_id='$siteid' order by id desc limit $startfrom,$limit";
                    $result = mysqli_query($con, $sql);
                    if ($result->num_rows > 0) {
                        $i = 0;
                        while ($row = mysqli_fetch_array($result)) {
                            $briefarr = explode(",", $row["brief"]);
                            $tmpbrief = '';
                            for ($j = 0; $j < count($briefarr); $j = $j + 3) {
                                $tmpbrief = $tmpbrief . '<a target=_blank href="' . $briefarr[$j] . $briefarr[$j + 1] . '/">' . $briefarr[$j + 1] . '</a> x' . $briefarr[$j + 2];
                                if ($j % 2 == 0) {
                                    $tmpbrief = $tmpbrief . '<br>';
                                }
                            }
                            $arrdata[$i]['id'] = $row["order_no"];
                            $arrdata[$i]['brief'] = $tmpbrief;
                            $tmparr = explode(",", $row["cartdetail"]);
                            $arrdata[$i]['price'] = $tmparr[2] + $tmparr[3];
                            if ($row["payment_status"] == '') {
                                $arrdata[$i]['status'] = $arr["unpaid"];
                            } else {
                                $arrdata[$i]['status'] = $row["payment_status"];
                            }
                            $i++;
                        }
                    }
                }
                $tpl = str_replace($matches[0][$l], looponce($matches[0][$l], $reTag, $arrdata), $tpl);
                //替换分页
                $tpl = str_replace('{[clientorderspages]}', paginate_two($reload, $page, $tpages, 4, ''), $tpl);
            }
        }
        $reTag = "/{pt\:client_yes}([\s\S]*?){\/pt}/i";
        preg_match_all($reTag, $tpl, $matches);
        for ($l = 0; $l < count($matches[0]); $l++) {
            $tpl = str_replace($matches[0][$l], $matches[1][$l], $tpl);
        }
    } else {

        if ($submit == "login") {

            if ($email != "" && $pwd != "") {
                $pwd = md5($pwd);
                $sql = "select * from x_client where email='$email' and pwd='$pwd'";

                $result = mysqli_query($con, $sql);
                if (!$result) {
                    die('Error: ' . mysqli_error());
                } else {
                    if ($result->num_rows == 0) {
                        $errmsg = '<div class="alert alert-success alert-dismissable">
            <button type="button" class="close" data-dismiss="alert"
                    aria-hidden="true">
                &times;
            </button>' . $arr["wronguser"] . '</div>';
                        $tpl = str_replace("{[errmsg]}", $errmsg, $tpl);
                    } else {
                        $row = mysqli_fetch_array($result);
                        $id = $row["id"];
                        $mydate = date("Y-m-d H:i:s");
                        setcookie("client1", $id, time() + 3600 * 24 * 365, $cookiepath);
                        setcookie("client2", $pwd, time() + 3600 * 24 * 365, $cookiepath);
                        $sql1 = "update x_client set lasttime='$mydate' where id='$id' and pwd='$pwd'";
                        mysqli_query($con, $sql1);
                        Header("Location: " . $siteurl . $langurl . "client/");
                    }
                    if ($_GET["act"] == "checkout") {
                        Header("Location: " . $siteurl . $langurl . "checkout/");
                    }
                }
            } else {
                $errmsg = '<div class="alert alert-success alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' . $arr["failedinput"] . '</div>';
                $tpl = str_replace("{[errmsg]}", $errmsg, $tpl);
            }

        }

        if ($submit == "register") {

            if ($email != "" && $pwd != "") {
                $pwd = md5($pwd);
                $sql = "select * from x_client where email='$email' and pwd='$pwd'";

                $result = mysqli_query($con, $sql);
                if (!$result) {
                    die('Error: ' . mysqli_error());
                } else {
                    if ($result->num_rows > 0) {
                        $row = mysqli_fetch_array($result);
                        $id = $row["id"];
                        $mydate = date("Y-m-d H:i:s");
                        setcookie("client1", $id, time() + 3600 * 24 * 365, $cookiepath);
                        setcookie("client2", $pwd, time() + 3600 * 24 * 365, $cookiepath);
                        $sql1 = "update x_client set lasttime='$mydate' where id='$id' and pwd='$pwd'";
                        mysqli_query($con, $sql1);
                        Header("Location: " . $siteurl . $langurl . "client/");
                    } else {
                        $sql = "select * from x_client where email='$email'";

                        $result = mysqli_query($con, $sql);
                        if ($result->num_rows > 0) {
                            $errmsg = '<div class="alert alert-success alert-dismissable">
            <button type="button" class="close" data-dismiss="alert"
                    aria-hidden="true">
                &times;
            </button>' . $arr["emailexist"] . '</div>';
                            $tpl = str_replace("{[errmsg]}", $errmsg, $tpl);
                        } else {
                            $sql = "insert into x_client set email='$email',pwd='$pwd'";

                            $result = mysqli_query($con, $sql);
                            $errmsg = '<div class="alert alert-success alert-dismissable">
            <button type="button" class="close" data-dismiss="alert"
                    aria-hidden="true">
                &times;
            </button>' . $arr["registersuccess"] . '</div>';
                            $tpl = str_replace("{[errmsg]}", $errmsg, $tpl);
                        }
                    }
                }
            } else {
                $errmsg = '<div class="alert alert-success alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' . $arr["failedinput"] . '</div>';
                $tpl = str_replace("{[errmsg]}", $errmsg, $tpl);
            }

        }
        //下面的替换顺序不能变
        $reTag = "/{pt\:clientorders_([0-9]+)}([\s\S]*?){\/pt}/i";
        preg_match_all($reTag, $tpl, $matches);
        for ($l = 0; $l < count($matches[0]); $l++) {
            $tpl = str_replace($matches[0][$l], "", $tpl);
        }
        $reTag = "/{pt\:client_yes}([\s\S]*?){\/pt}/i";
        preg_match_all($reTag, $tpl, $matches);
        for ($l = 0; $l < count($matches[0]); $l++) {
            $tpl = str_replace($matches[0][$l], "", $tpl);
        }
        $reTag = "/{pt\:client_no}([\s\S]*?){\/pt}/i";
        preg_match_all($reTag, $tpl, $matches);
        for ($l = 0; $l < count($matches[0]); $l++) {
            $tpl = str_replace($matches[0][$l], $matches[1][$l], $tpl);
        }
        $tpl = str_replace("{[clientorderspages]}", "", $tpl);
    }
    $tpl = str_replace("{[errmsg]}", "", $tpl);
}
//替换订单支付完成后的内容
if ($pagetype == 'complete') {
    if (isset($_COOKIE["cart"])) {
        $idarr = explode(",", $_COOKIE["cart"]);
        for ($i = 0; $i < count($idarr); $i++) {
            setcookie("item" . $idarr[$i], "", time() - 3600 * 24 * 365, $cookiepath);    //删除cookies时要指定路径
            setcookie("quantity" . $idarr[$i], "", time() - 3600 * 24 * 365, $cookiepath);
        }
        setcookie("cart", "", time() - 3600 * 24 * 365, $cookiepath);
    }
    $tpl = str_replace("{[completeout]}", $arr["paymentyes"], $tpl);
    $tpl = str_replace("{[completeurl]}", $siteurl . $langurl, $tpl);
}

//过期标志
/*
if ($version != 0 && $balance <= 0) {              //会使全部链接无效
    $tpl = preg_replace('/<a (.*?)href=\"([^\"]+?)\"(.*?)>/i', '<a $1href="###" $3>', $tpl);
    $tpl = preg_replace('/<a (.*?)href=\'([^\"]+?)\'(.*?)>/i', '<a $1href="###" $3>', $tpl);
    $tpl = preg_replace('/<a (.*?)href=([^\"]+?) (.*?)>/i', '<a $1href="###" $3>', $tpl);
}
*/

if ($_SERVER['SERVER_NAME'] == "localhost" || $_SERVER['SERVER_NAME'] == "zh-host.sleda.com" || $_SERVER['SERVER_NAME'] == "us-host.sleda.com" || $_SERVER['SERVER_NAME'] == "hk-host.sleda.com") {
    $tpl = preg_replace('/([0-9a-zA-Z]+)(\.jpg|\.gif|\.jpeg|\.png|\.bmp|\.css|\.js){1}/i', '$1$2?' . rand(1, 99), $tpl);
}
//输出
echo $tpl;
if ($pagetype != "client" && $pagetype != "checkout") {
//免费版和过期标志
    if ($version == 0 || ($charge_mode == "byday" && $balance <= 0) || ($charge_mode == "byyear" && strtotime(date("Y-m-d")) > strtotime($enddate))) {
        $copyright = '<div style="background-color:#000;text-align: center;padding:5px;"><a style="color:#fff" href="' . $brandfullurl . '">' . $arr["createdbysleda"] . ' - ' . $brandname . '</a></div>';
        echo $copyright;
        //var_dump($arr);
    }
}
?>
