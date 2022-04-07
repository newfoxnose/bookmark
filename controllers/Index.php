<?php
//error_reporting(E_ALL ^ E_NOTICE);
error_reporting(E_ALL ^ E_NOTICE);

class Index extends Index_Data
{
    public function __construct()
    {
        parent::__construct();
    }


//管理员登入
    public function admin_login()
    {
        $data['title'] = '登陆';
        $this->form_validation->set_rules('password', '密码', 'required');
        if ($this->form_validation->run() === FALSE) {
            $this->load->view('index/admin_login', $data);
            $this->load->view('templates/footer');
        } else {
            if (md5($this->input->post('password')) == "455d7bbea248fedac82497dfe2a7e62b") {    //Zhangnan123
                $_SESSION['admin_login'] = "yes";
                redirect('admin/admin_home');
            } else {
                $this->load->view('index/admin_login', $data);
                $this->load->view('templates/footer');
            }
        }
    }

//管理员登出
    public function admin_logout()
    {
        $data['title'] = '已登出';
        unset($_SESSION['admin_login']);
        $this->load->view('admin/admin_login', $data);
        $this->load->view('templates/footer');
    }


//注册
    public function reg()
    {
        $data['title'] = '注册新账号';

        $this->form_validation->set_rules('email', '邮箱', 'required|valid_email');
        $this->form_validation->set_rules('password', '密码', 'required');
        $this->form_validation->set_rules('password2', '重复密码', 'required|matches[password]');
        $this->form_validation->set_message('required', '{field}不能为空');
        $this->form_validation->set_message('valid_email', '{field}格式不正确');
        $this->form_validation->set_message('matches', '两次密码不一致');
        if ($this->form_validation->run() === FALSE) {
            $this->load->view('templates/public_header', $data);
            $this->load->view('index/reg', $data);
            $this->load->view('templates/footer');
        } else {
            $email = checkinput('email', $this->input->post('email'), 5, 100);
            $password = checkinput('pwd', $this->input->post('password'), 6, 30);
            $password2 = checkinput('pwd', $this->input->post('password2'), 6, 30);
            if ($email == '') {
                $_SESSION['err_msg'] = err_msg("邮箱格式不正确");
                redirect('index/reg');
            }
            elseif($password == ''){
                $_SESSION['err_msg'] = err_msg("密码输入不符合要求");
                redirect('index/reg');
            }
            elseif($password!=$password2){
                $_SESSION['err_msg'] = err_msg("两次密码不一致");
                redirect('index/reg');
            }
            else {
                if ($this->all_model->general_get_amount('xz_teachers', array("email" => $email)) > 0) {
                    $_SESSION['err_msg'] = err_msg("该邮箱已被注册");
                    redirect('index/reg');
                } else {
                    $update_arr = array();
                    $update_arr['name']=rand_name().rand_str(3).mt_rand(100,999);
                    $update_arr['email']=$email;
                    $update_arr['password']=md5($password);
                    $update_arr['employed']=0;
                    $update_arr['createtime']=date("Y-m-d H:i:s");
                    $this->all_model->general_insert("xz_teachers", $update_arr);
                    $_SESSION['err_msg'] = err_msg("注册成功，请登录");
                    redirect('index/login');
                }
            }
        }
    }

//老师和学生登入
    public function login()
    {
        $data['title'] = '登陆';

        $this->form_validation->set_rules('email', '邮箱', 'required');
        $this->form_validation->set_rules('password', '密码', 'required');

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('templates/public_header', $data);
            $this->load->view('index/login', $data);
            $this->load->view('templates/footer');
        } else {
            $email = $this->input->post('email');
            $password = md5($this->input->post('password'));
            $data['teacher_item'] = $this->all_model->general_get("xz_teachers", array("email" => $email, "password" => $password, "employed" => 0));
            if ($data['teacher_item'] != NULL) {
                if ($data['teacher_item']['employed'] == 0) {
                    $_SESSION['login'] = "yes";
                    $_SESSION['teacher_id'] = $data['teacher_item']['id'];
                    redirect('user/home');
                } else {
                    $this->load->view('templates/public_header', $data);
                    $this->load->view('index/login', $data);
                    $this->load->view('templates/footer');
                }
            } else {
                $this->load->view('templates/public_header', $data);
                $this->load->view('index/login', $data);
                $this->load->view('templates/footer');
            }
        }
    }


//教师登出
    public function logout()
    {
        $data['title'] = '已登出';
        unset($_SESSION['login']);
        unset($_SESSION['teacher_id']);
        $this->load->view('templates/public_header', $data);
        $this->load->view('index/login', $data);
        $this->load->view('templates/footer');
    }

//公开书签
    public function bookmark($email = null)
    {
        $data['teachers'] = $this->all_model->general_select("xz_teachers", "id,name", array("employed" => 0));
        if ($email == null) {
            $data['title'] = '公开书签';
            $sql = "select * from xz_bookmark where is_private=0 order by tag desc";
        } else {
            $teacher = $this->all_model->general_get("xz_teachers", array("email" => $email));
            if ($teacher == null) {
                $data['title'] = '公开书签';
                $sql = "select * from xz_bookmark where is_private=0 order by tag desc";
                $_SESSION['err_msg'] = err_msg("不存在该用户！将显示所有公开书签");
            } else {
                $data['title'] = $teacher['name'] . '的公开书签';
                $teacher_id = $teacher['id'];
                $sql = "select * from xz_bookmark where is_private=0 and teacher_id='$teacher_id' order by tag desc";
            }

        }
        $query = $this->db->query($sql);
        $data['bookmark'] = $query->result_array();
        $this->load->view('templates/public_header', $data);
        $this->load->view('index/bookmark', $data);
        $this->load->view('templates/footer');
    }


    //微信登录，管理网址https://open.weixin.qq.com/，用户名：wenshao@mp.sleda.com，密码左长
    public function wx_login()
    {
        //--微信登录-----生成唯一随机串防CSRF攻击
        $state = md5(uniqid(rand(), TRUE));
        $_SESSION["wx_state"] = $state; //存到SESSION
        $callback = urlencode(OPEN_CALLBACKURL);
        $wxurl = "https://open.weixin.qq.com/connect/qrconnect?appid=" . OPEN_APPID . "&redirect_uri=" . $callback . "&response_type=code&scope=snsapi_login&state=" . $state . "#wechat_redirect";
        header("Location: $wxurl");
    }

    public function wxBack()
    {
        if ($_GET['state'] != $_SESSION["wx_state"]) {
            echo 'sorry,网络请求失败...';
            exit("5001");
        }
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . OPEN_APPID . '&secret=' . OPEN_APPSECRET . '&code=' . $_GET['code'] . '&grant_type=authorization_code';
        $arr = json_decode(get($url), true);
        //得到 access_token 与 openid
        $url = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $arr['access_token'] . '&openid=' . $arr['openid'] . '&lang=zh_CN';
        $user_info = json_decode(get($url), true);;
        //$this->dealWithWxLogin($user_info);    //这样可以直接调用本controller下的其他函数,但是无法显示其view

        if ($user_info['errcode'] != null) {
            $data['title'] = '出错了';
            $data['general_msg'] = '出错了，请后退重试';
            $this->load->view('templates/public_header', $data);
            $this->load->view('templates/general_msg', $data);
        } else {
            $_SESSION['openid'] = $user_info['openid'];
            $user = $this->all_model->general_get("xz_wx_user", array('openid' => $user_info['openid']));
            if ($user == null) {
                $user_info['createtime'] = date("Y-m-d H:i:s");
                unset($user_info['privilege']);
                $this->all_model->general_insert("xz_wx_user", $user_info);
                $data['title'] = '绑定用户';
                $this->load->view('templates/public_header', $data);
                $this->load->view('index/binding', $data);
            } else {
                if ($user['teacher_id'] == 0 && $user['student_id'] == 0) {
                    $data['title'] = '绑定用户';
                    $this->load->view('templates/public_header', $data);
                    $this->load->view('index/binding', $data);
                } else {
                    $data['title'] = '选择绑定';
                    if ($user['teacher_id'] != 0) {
                        $data['teacher'] = $this->all_model->general_get("xz_teachers", array('id' => $user['teacher_id']));
                    }
                    if ($user['student_id'] != 0) {
                        $data['student'] = $this->all_model->general_get("xz_students", array('id' => $user['student_id']));
                    }
                    $data['departments'] = $this->all_model->general_load("xz_departments", "sort", "desc");
                    $this->load->view('templates/public_header', $data);
                    $this->load->view('index/binding_select', $data);
                }
            }
        }
    }

    //微信扫码绑定
    public function wx_binding()
    {
        $data['title'] = '微信扫码绑定';

        $this->form_validation->set_rules('name', '姓名', 'required');
        $this->form_validation->set_rules('identity_number', '身份证号码', 'required');
        if ($this->form_validation->run() === FALSE) {
            $data['title'] = '绑定用户';
            $this->load->view('templates/public_header', $data);
            $this->load->view('index/binding', $data);
        } else {
            $openid = $_SESSION['openid'];
            $name = $this->input->post('name');
            $phone = $this->input->post('phone');
            $identity_number = $this->input->post('identity_number');
            if ($this->input->post('type_select') == 0) {          //type为0表示学生
                $student_id = $this->all_model->general_get("xz_students", array('name' => $name, 'parent_phone' => $phone, 'identity_number' => $identity_number), "id");
                $this->all_model->general_update("xz_wx_user", array('student_id' => $student_id['id']), array('openid' => $openid));
            } else if ($this->input->post('type_select') == 1) {
                $teacher_id = $this->all_model->general_get("xz_teachers", array('name' => $name, 'phone' => $phone, 'identity_number' => $identity_number), "id");
                $this->all_model->general_update("xz_wx_user", array('teacher_id' => $teacher_id['id']), array('openid' => $openid));
            } else {
                redirect('index/access_forbidden');
            }
            $user = $this->all_model->general_get("xz_wx_user", array('openid' => $openid));
            if ($user['teacher_id'] != 0) {
                $data['teacher'] = $this->all_model->general_get("xz_teachers", array('id' => $user['teacher_id']));
            }
            if ($user['student_id'] != 0) {
                $data['student'] = $this->all_model->general_get("xz_students", array('id' => $user['student_id']));
            }
            $data['departments'] = $this->all_model->general_load("xz_departments", "sort", "desc");
            $this->load->view('templates/public_header', $data);
            $this->load->view('index/binding_select', $data);
        }
    }


    //选择绑定身份
    public function binding_select()
    {
        $data['title'] = '选择绑定身份';
        $openid = $_SESSION['openid'];
        $name = $this->input->post('name');
        $phone = $this->input->post('phone');
        $identity_number = $this->input->post('identity_number');
        if ($this->input->post('type_select') == 0) {          //type为0表示学生
            $student_id = $this->all_model->general_get("xz_students", array('name' => $name, 'parent_phone' => $phone, 'identity_number' => $identity_number), "id");
            $this->all_model->general_update("xz_wx_user", array('student_id' => $student_id['id']), array('openid' => $openid));
            $this->load->view('templates/public_header', $data);
            $this->load->view('index/binding_select', $data);
        } else {
            $teacher_id = $this->all_model->general_get("xz_teachers", array('name' => $name, 'phone' => $phone, 'identity_number' => $identity_number), "id");
            $this->all_model->general_update("xz_wx_user", array('teacher_id' => $teacher_id['id']), array('openid' => $openid));
            $this->load->view('templates/public_header', $data);
            $this->load->view('index/binding_select', $data);
        }
    }


//微信扫码后选择身份进入 ，type为0表示学生
    public function wx_enter($type, $id)
    {
        $openid = $_SESSION['openid'];
        if ($type == 0) {
            $user = $this->all_model->general_get("xz_wx_user", array('student_id' => $id, 'openid' => $openid), "student_id");
            if ($user != null) {
                $_SESSION['student_login'] = "yes";
                $_SESSION['student_id'] = $user['student_id'];
                redirect('student/index');
            } else {
                redirect('index/access_forbidden');
            }
        } else {
            $user = $this->all_model->general_get("xz_wx_user", array('teacher_id' => $id, 'openid' => $openid), "teacher_id");
            if ($user != null) {
                $_SESSION['login'] = "yes";
                $_SESSION['teacher_id'] = $user['teacher_id'];
                redirect('user/home');
            } else {
                redirect('index/access_forbidden');
            }
        }
    }

    /**
     * 根据微信授权用户的信息 进行下一步的梳理
     * @param $user_info
     */
    public function dealWithWxLogin($user_info)
    {
        //TODO 数据处理
        var_dump($user_info);
        die;
    }


//换取openid，蕲春行知小程序，如果换了其他小程序，需要修改这里的appid和appsecret
    public function xcx_get_openid($code)
    {
        $AppID = "wxc4b98e66f79b4131";
        $AppSecret = "d06593f260586bdd2d02057856b510ac";

        if ($code != "") {

            //$pre_row = $this->all_model->general_get("xz_teachers", array("the_code" => $code), "identity_number,name,phone,wx_openid");
            //if ($pre_row == null) {
            $url = "https://api.weixin.qq.com/sns/jscode2session?appid=" . $AppID . "&secret=" . $AppSecret . "&js_code=" . $code . "&grant_type=authorization_code";

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_TIMEOUT, 500);
// 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_URL, $url);

            $result = curl_exec($curl);
            curl_close($curl);
            $arr = json_decode($result, true);
            //$binding=$this->all_model->general_get_amount("xz_teachers", array("wx_openid"=>$arr['openid']), null, null);
            //$arr['binding']=$binding;
            $row = $this->all_model->general_get("xz_teachers", array("wx_openid" => $arr['openid']));
            $arr['wx_openid'] = $row['wx_openid'];
            $arr['id'] = $row['identity_number'];
            $arr['name'] = $row['name'];
            $arr['phone'] = $row['phone'];
            /*
            } else {
                $arr = array();
                $arr['wx_openid'] = $pre_row['wx_openid'];
                $arr['id'] = $pre_row['identity_number'];
                $arr['name'] = $pre_row['name'];
                $arr['phone'] = $pre_row['phone'];
            }
            */
            $json = json_encode($arr);
            echo $json;
        } else {
            echo "error";
        }
    }

//预览txt文本
    public function txt_preview($id)
    {
        $data['title'] = '预览';
        $document = $this->all_model->general_get('xz_documents', array("id" => $id));
        $file_url = QINIU_DOMAIN . $document["path"] . "/" . $document["rndstring"] . "/" . $document["real_filename"];
        $text = file_get_contents($file_url);
        $code = chkCode($text);
        if ($code != null) {
            if ($code != "UTF-8") {
                $text = iconv($code, "UTF-8", $text);
            } else {
                $text = $text;
            }
        }
        echo nl2br($text);
    }


//通讯录json，小程序用
    public function addressbook_json($openid)
    {
        $data['title'] = '通讯录';
        $data['teachers'] = $this->all_model->general_list("xz_teachers", array("employed" => 0), array("convert(name using gbk)" => "asc"));
        for ($i = 0; $i < count($data['teachers']); $i++) {
            $data['teachers'][$i]['show'] = true;
            $data['teachers'][$i]['search'] = $data['teachers'][$i]['name'] . $data['teachers'][$i]['phone'];
        }
        echo json_encode($data['teachers']);
    }

//绑定微信openid
    public function binding()
    {
        $this->all_model->general_update("xz_teachers", array("wx_openid" => ""), array("wx_openid" => $this->input->post('wx_openid')));

        $check_binding_result = $this->all_model->general_get_amount("xz_teachers", array("employee_number" => $this->input->post('employee_number'), "name" => $this->input->post('name')), null, null);
        if ($check_binding_result > 1) {
            echo "2";
        } else {
            $result = $this->all_model->general_update("xz_teachers", array("wx_openid" => $this->input->post('wx_openid'), "avatar_url" => $this->input->post('avatar_url')), array("employee_number" => $this->input->post('employee_number'), "name" => $this->input->post('name')));
        }
        echo $result;
    }

//解绑微信openid
    public function undobinding()
    {
        $result = $this->all_model->general_update("xz_teachers", array("wx_openid" => ""), array("wx_openid" => $this->input->post('wx_openid')));
        echo $result;
    }


    ////////////////////
}