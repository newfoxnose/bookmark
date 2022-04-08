<?php
/**
 * Class MY_Controller
 * 自定义控制器
 */
class MY_Controller extends CI_Controller
{
    function  __construct()
    {
        parent::__construct();
        $this->load->model('all_model');
        $this->load->helper('url_helper');
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->load->library('session');
        $this->load->helper('mine');
        $this->load->helper('directory');
        if (file_exists(APPPATH."views/in_maintenance.php")) {
            //show_404();
            show_error("暂时无法访问。", 200, $heading = '系统维护中');
        }
    }
    //这里的函数在各个控制器里可以直接使用
    //生成二维码
    public function show_qr($controller,$function,$str)
    {
        $this->load->helper('phpqrcode');
        QRcode::png(site_url($controller.'/'.$function.'/' . $str));
    }
    //无权限提示
    public function access_forbidden()
    {
        $this->load->view('templates/access_forbidden');
    }

    //定义所需要的权限，没有则跳转到accesse_forbidden，其中之一为真即可
    public function require_permission($arr){
        $result=false;
        for ($i=0;$i<count($arr);$i++){
            if ($arr[$i]==true){
                $result=true;
                break;
            }
        }
        if ($result==true){
            return true;
        }
        else{
            redirect('index/access_forbidden' );
        }
    }

}
/**
 * Class General_Data
 * 共用数据控制器
 */
class User_Data extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($_SESSION['login'] != "yes") {
            redirect('index/login/');
        }
        $data['teacher_item'] = $this->all_model->general_get("bm_user",array("id"=>$_SESSION['teacher_id']));
        if (empty($data['teacher_item'])) {
            show_404();
        }
        $this->general_data=$data;
    }

}
/**
 * Class General_Data
 * 共用数据控制器
 */
class Admin_Data extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($_SESSION['admin_login'] != "yes") {
            redirect('index/admin_login/');
        }
    }
}
/**
 * Class General_Data
 * 共用数据控制器
 */
class Index_Data extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }
}
?>