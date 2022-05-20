<?php
//error_reporting(E_ALL ^ E_NOTICE);
error_reporting(E_ALL ^ E_NOTICE);

class User extends User_Data
{
    public function __construct()
    {
        parent::__construct();
    }

//行事历
    public function calendar($year = null, $month = null)
    {
        $year = isset($year) ? $year : date("Y");
        $month = isset($month) ? $month : date("m");

        $data = $this->general_data;
        $data['teacher'] = $this->all_model->general_get("bm_user", array("id" => $data['cookie_teacher_id']));
        $data['title'] = '行事历';

        $data['personal_events'] = $this->all_model->general_list("bm_events", array("teacher_id" => $data['cookie_teacher_id'], "date_format(date,'%Y-%m')" => date("Y-m", strtotime($year . "-" . $month))));

        $data['name'] = $data['teacher_item']['name'];

        $this->load->library('calendar');
        $calc = new Calendar(array("year" => $year, "month" => $month, "au_event_edit" => 0));
        $data['calendar'] = $calc->showCalendar($data['personal_events']);
        $this->form_validation->set_rules('submit', 'submit', 'required');
        if ($this->form_validation->run() === TRUE) {
            $post = $this->input->post();
            $update_arr = array();
            $except_arr = array("submit");
            foreach ($post as $x_key => $x_value):
                if (!in_array($x_key, $except_arr)) {
                    $update_arr[$x_key] = $x_value;
                }
            endforeach;
            $this->all_model->general_update("bm_user", $update_arr, array("id" => $data['cookie_teacher_id']));
            redirect('user/home/');
        }

        $this->load->view('templates/header', $data);
        $this->load->view('teachers/calendar', $data);
        $this->load->view('templates/footer');
    }


//在登录后首页删除个人记事
    public function delete_personal_event($id, $year = null, $month = null)
    {
        $this->all_model->general_delete("bm_events", array("id" => $id));
        redirect('user/home/' . $year . '/' . $month);
    }

//新建个人记事
    public function add_personal_event($year = null, $month = null)
    {
        $data = $this->general_data;

        $this->form_validation->set_rules('date', '日期', 'required');
        $this->form_validation->set_rules('content', '内容', 'required');

        if ($this->form_validation->run() === FALSE) {
            redirect('user/home/' . $year . '/' . $month);
        } else {
            $update_arr = array();
            $update_arr['date'] = $this->input->post('date');
            $update_arr['content'] = $this->input->post('content');
            $update_arr['teacher_id'] = $data['cookie_teacher_id'];
            $this->all_model->general_insert("bm_events", $update_arr);
            redirect('user/home/' . date("Y", strtotime($update_arr['date'])) . '/' . date("m", strtotime($update_arr['date'])));
        }
    }

//实时记事本显示
    public function rt_note()
    {
        $data = $this->general_data;
        $data['title'] = '实时记事本';
        $this->load->view('templates/header', $data);
        $this->load->view('teachers/rt_note', $data);
        $this->load->view('templates/footer');
    }

//实时记事本保存
    public function rt_note_save()
    {
        $data = $this->general_data;
        $update_arr = array(
            'rt_note' => $this->input->post('content')
        );
        $this->all_model->general_update("bm_user", $update_arr, array("id" => $data['cookie_teacher_id']));
        echo "saved";
    }


//个人首页
    public function home()
    {
        $data = $this->general_data;
        echo $data['cookie_level'];

        $data['title'] = '我的收藏夹';

        if ($data['cookie_level'] == "work") {
            if ($this->all_model->general_get_amount('bm_folder', array("teacher_id" => $data['cookie_teacher_id'], "father_id" => -1, "folder_name" => "工作")) == 0) {
                $update_arr["teacher_id"] = $data['cookie_teacher_id'];
                $update_arr["folder_name"] = "工作";
                $update_arr["father_id"] = -1;
                $this->all_model->general_insert('bm_folder', $update_arr);
            }
        }

        $data['folder'] = $this->all_model->general_list("bm_folder", array("teacher_id" => $data['cookie_teacher_id'], "father_id" => -1), array("convert(folder_name using gbk)" => "asc"));
        if ($data['cookie_level'] != "all") {
            $data['folder'] = delByValue($data['folder'], 'folder_name', '个人');
        }
        if ($data['cookie_level'] == "work") {
            $data['folder'] = keepByValue($data['folder'], 'folder_name', '工作');
        }
        $folder_id_str = '';

        if ($data['cookie_level'] != "work") {
            $data['root_bookmarks'][] = $this->all_model->general_list("bm_bookmark", array("teacher_id" => $data['cookie_teacher_id'], 'folder_id' => -1), array("tag" => "desc", "convert(title using gbk)" => "asc"));
        }

        for ($i = 0; $i < count($data['folder']); $i++) {
            $data['folder'][$i]['bookmarks'] = $this->all_model->general_list("bm_bookmark", array("teacher_id" => $data['cookie_teacher_id'], 'folder_id' => $data['folder'][$i]['id']), array("tag" => "desc", "convert(title using gbk)" => "asc"));
            $data['folder'][$i]["subfolder"] = $this->get_folder($data['cookie_teacher_id'], $data['folder'][$i]['id'], 1);
            if ($i == 0) {
                if ($data['cookie_level'] == "work") {
                    $folder_id_str = $this->get_subfolder_id($data['folder'][$i]);
                } else {
                    $folder_id_str = "0," . $this->get_subfolder_id($data['folder'][$i]);
                }
            } else {
                $folder_id_str = $folder_id_str . "," . $this->get_subfolder_id($data['folder'][$i]);
            }
        }
        $folder_id_arr = explode(",", $folder_id_str);

        $data['tag'] = $this->all_model->general_select("bm_bookmark", "tag", array("teacher_id" => $data['cookie_teacher_id'], "tag!=" => ""), array("folder_id" => $folder_id_arr), null, "tag");


        $this->load->view('templates/header', $data);
        $this->load->view('teachers/home', $data);
        $this->load->view('templates/footer');
    }


//管理书签
    public function manage_bookmark()
    {
        $data = $this->general_data;
        $data['title'] = $data['teacher_item']['name'] . '的书签';
        $data['bookmark'] = $this->all_model->general_list("bm_bookmark", array("teacher_id" => $data['cookie_teacher_id']), array("folder_id" => "desc", "tag" => "desc", "convert(title using gbk)" => "asc"));
        $data['tag'] = $this->all_model->general_select("bm_bookmark", "tag", array("teacher_id" => $data['cookie_teacher_id'], "tag!=" => ""), null, null, "tag");
        $data['folder'] = $this->all_model->general_list("bm_folder", array("teacher_id" => $data['cookie_teacher_id'], "father_id" => -1), array("convert(folder_name using gbk)" => "asc"));
        for ($i = 0; $i < count($data['folder']); $i++) {
            $data['folder'][$i]["subfolder"] = $this->all_model->general_list("bm_folder", array("teacher_id" => $data['cookie_teacher_id'], "father_id" => $data['folder'][$i]['id']), array("convert(folder_name using gbk)" => "asc"));
        }
        $data['folder'][] = array("id" => "0", "folder_name" => "根目录");   //不能直接放到第一位
        array_unshift($data['folder'], array_pop($data['folder']));         //把最后一个元素移到第一位

        $this->form_validation->set_rules('title', '标题', 'required');
        $this->form_validation->set_rules('url', '网址', 'required');
        if ($this->form_validation->run() === FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('teachers/manage_bookmark', $data);
            $this->load->view('templates/footer');
        } else {
            $update_arr = array(
                'title' => $this->input->post('title'),
                'url' => $this->input->post('url'),
                'tag' => $this->input->post('tag'),
                'folder_id' => $this->input->post('folder_id'),
                'is_private' => $this->input->post('is_private') ? 1 : 0,    //当is_private为null时使用0
            );
            if ($this->input->post('submit') == "addnew") {
                $update_arr["teacher_id"] = $data['cookie_teacher_id'];
                $update_arr["createtime"] = date("Y-m-d H:i:s");
                $update_arr['safe_code'] = rand_str(8);
                $paresed_url = parse_url($this->input->post('url'));
                if (check_remote_file_exists($paresed_url['scheme'] . '://' . $paresed_url['host'] . '/favicon.ico')) {
                    $update_arr["icon_uri"] = $paresed_url['scheme'] . '://' . $paresed_url['host'] . '/favicon.ico';
                }
                $this->all_model->general_insert("bm_bookmark", $update_arr);
            } elseif ($this->input->post('submit') == "addnew_home") {
                $update_arr["teacher_id"] = $data['cookie_teacher_id'];
                $update_arr["createtime"] = date("Y-m-d H:i:s");
                $update_arr['safe_code'] = rand_str(8);
                $paresed_url = parse_url($this->input->post('url'));
                if (check_remote_file_exists($paresed_url['scheme'] . '://' . $paresed_url['host'] . '/favicon.ico')) {
                    $update_arr["icon_uri"] = $paresed_url['scheme'] . '://' . $paresed_url['host'] . '/favicon.ico';
                }
                $this->all_model->general_insert("bm_bookmark", $update_arr);
                redirect('user/home/');
            } elseif ($this->input->post('submit') == "update") {
                $this->all_model->general_update("bm_bookmark", $update_arr, array("id" => $this->input->post('id'), "teacher_id" => $data['cookie_teacher_id']));
            } elseif ($this->input->post('submit') == "delete") {
                $this->all_model->general_delete("bm_bookmark", array("id" => $this->input->post('id'), "teacher_id" => $data['cookie_teacher_id']));
            }
            redirect('user/manage_bookmark/#' . $this->input->post('id'));
        }
    }

//修改书签
    public function edit_bookmark($id, $safe_code)
    {
        $data = $this->general_data;
        $data['title'] = '编辑书签';
        $data['bookmark'] = $this->all_model->general_get("bm_bookmark", array("teacher_id" => $data['cookie_teacher_id'], "id" => $id, "safe_code" => $safe_code));
        $data['tag'] = $this->all_model->general_select("bm_bookmark", "tag", array("teacher_id" => $data['cookie_teacher_id'], "tag!=" => ""), null, null, "tag");

        $data['folder'] = $this->all_model->general_list("bm_folder", array("teacher_id" => $data['cookie_teacher_id'], "father_id" => -1), array("convert(folder_name using gbk)" => "asc"));
        if ($data['cookie_level'] != "all") {
            $data['folder'] = delByValue($data['folder'], 'folder_name', '个人');
        }
        if ($data['cookie_level'] == "work") {
            $data['folder'] = keepByValue($data['folder'], 'folder_name', '工作');
        }
        for ($i = 0; $i < count($data['folder']); $i++) {
            $data['folder'][$i]["subfolder"] = $this->get_folder($data['cookie_teacher_id'], $data['folder'][$i]['id'], 1);
        }


        $this->form_validation->set_rules('title', '标题', 'required');
        $this->form_validation->set_rules('url', '网址', 'required');
        if ($this->form_validation->run() === FALSE) {
            $this->load->view('teachers/edit_bookmark', $data);
        } else {
            $update_arr = array(
                'title' => $this->input->post('title'),
                'url' => $this->input->post('url'),
                'tag' => $this->input->post('tag'),
                'folder_id' => $this->input->post('folder_id'),
                'is_private' => $this->input->post('is_private') ? 1 : 0,    //当is_private为null时使用0
            );
            $this->all_model->general_update("bm_bookmark", $update_arr, array("id" => $id, "safe_code" => $safe_code, "teacher_id" => $data['cookie_teacher_id']));
            $_SESSION['err_msg'] = err_msg("修改成功");
            redirect('user/edit_bookmark/' . $id . '/' . $safe_code);
        }
    }


//删除书签
    public function delete_bookmark($id, $safe_code)
    {
        $data = $this->general_data;
        $this->all_model->general_delete("bm_bookmark", array("id" => $id, "safe_code" => $safe_code, "teacher_id" => $data['cookie_teacher_id']));
        redirect('user/home/');
    }

//获取网页标题
    public function url_title()
    {
        $data = $this->general_data;
        $url = $this->input->post('url');
        $this->load->library('curl');
        $html = Curl::get($url);
        $reTag = "/<title>([\s\S]*?)<\/title>/i";
        preg_match($reTag, $html, $match);
        $title = $match[1];
        if ($title == "" || $title == null) {
            echo "！未获取到网站标题";
        } else {
            echo $title;
        }
        //$arr = Curl::post('http://localhost:9090/test.php', array('a'=>1,'b'=>2));
    }

//管理目录和个人首页要用的函数
    private function get_folder($teacher_id, $folder_id, $withbookmark = 0)
    {
        $subfolder = $this->all_model->general_list("bm_folder", array("teacher_id" => $teacher_id, "father_id" => $folder_id), array("convert(folder_name using gbk)" => "asc"));
        for ($i = 0; $i < count($subfolder); $i++) {
            $subfolder[$i]['subfolder'] = $this->get_folder($teacher_id, $subfolder[$i]['id'], $withbookmark);
            if ($withbookmark == 1) {
                $subfolder[$i]['bookmarks'] = $this->all_model->general_list("bm_bookmark", array("teacher_id" => $teacher_id, 'folder_id' => $subfolder[$i]['id']), array("tag" => "desc", "convert(title using gbk)" => "asc"));
            }
        }
        return $subfolder;
    }

//管理目录要用的函数
    private function get_subfolder_id($sub_item)
    {
        $out = $sub_item['id'];
        if ($sub_item['subfolder'] != null) {
            foreach ($sub_item['subfolder'] as $item) {
                $out = $out . ',' . $this->get_subfolder_id($item);
            }
        }
        return $out;
    }

//管理目录
    public function manage_folder()
    {
        $data = $this->general_data;
        echo $data['cookie_level'];
        $data['folder'] = $this->all_model->general_list("bm_folder", array("teacher_id" => $data['cookie_teacher_id'], "father_id" => -1), array("convert(folder_name using gbk)" => "asc"));
        if ($data['cookie_level'] != "all") {
            $data['folder'] = delByValue($data['folder'], 'folder_name', '个人');
        }
        if ($data['cookie_level'] == "work") {
            $data['folder'] = keepByValue($data['folder'], 'folder_name', '工作');
        }
        for ($i = 0; $i < count($data['folder']); $i++) {
            $data['folder'][$i]["subfolder"] = $this->get_folder($data['cookie_teacher_id'], $data['folder'][$i]['id']);
        }

        $data['title'] = '管理目录';
        $this->form_validation->set_rules('submit', 'submit', 'required');

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('teachers/manage_folder', $data);
            $this->load->view('templates/footer');
        } else {
            if ($this->input->post('folder_name') == '' && $this->input->post('submit') != "empty_root") {
                $_SESSION['err_msg'] = err_msg("不能为空！");
            } else {
                $post = $this->input->post();
                $update_arr = array();
                $except_arr = array("id", "submit");
                foreach ($post as $x_key => $x_value):
                    if (!in_array($x_key, $except_arr)) {
                        $update_arr[$x_key] = $x_value;
                    }
                endforeach;
                $update_arr["teacher_id"] = $data['cookie_teacher_id'];
                if ($this->input->post('submit') == "add_folder") {     //添加二级目录
                    if ($this->all_model->general_get_amount('bm_folder', array("teacher_id" => $data['cookie_teacher_id'], "father_id" => $this->input->post('father_id'), "folder_name" => $this->input->post('folder_name'))) > 0) {
                        $_SESSION['err_msg'] = err_msg("已存在同名目录！");
                    } else {
                        $this->all_model->general_insert('bm_folder', $update_arr);
                    }
                } elseif ($this->input->post('submit') == "empty_root") {     //清空根目录
                    $this->all_model->general_delete('bm_bookmark', array("folder_id" => -1, "teacher_id" => $data['cookie_teacher_id']));
                } elseif ($this->input->post('submit') == "empty_folder") {     //清空目录
                    $the_folder = $this->all_model->general_get("bm_folder", array("teacher_id" => $data['cookie_teacher_id'], "id" => $this->input->post('id')));
                    $the_folder["subfolder"] = $this->get_folder($data['cookie_teacher_id'], $the_folder['id']);
                    $id_arr = explode(",", $this->get_subfolder_id($the_folder));
                    $this->all_model->general_delete('bm_bookmark', array("teacher_id" => $data['cookie_teacher_id']), array("folder_id" => $id_arr));
                } elseif ($this->input->post('submit') == "delete_folder") {     //删除目录
                    $the_folder = $this->all_model->general_get("bm_folder", array("teacher_id" => $data['cookie_teacher_id'], "id" => $this->input->post('id')));
                    $the_folder["subfolder"] = $this->get_folder($data['cookie_teacher_id'], $the_folder['id']);
                    $id_arr = explode(",", $this->get_subfolder_id($the_folder));
                    $this->all_model->general_delete('bm_bookmark', array("teacher_id" => $data['cookie_teacher_id']), array("folder_id" => $id_arr));
                    $this->all_model->general_delete('bm_folder', array("teacher_id" => $data['cookie_teacher_id']), array("id" => $id_arr));
                } elseif ($this->input->post('submit') == "update_folder") {              //修改目录
                    if ($this->all_model->general_get_amount('bm_folder', array("id!=" => $this->input->post('id'), "teacher_id" => $data['cookie_teacher_id'], "father_id" => $this->input->post('father_id'), "folder_name" => $this->input->post('folder_name'))) > 0) {
                        $_SESSION['err_msg'] = err_msg("已存在同名目录00！");
                    } else {
                        $this->all_model->general_update("bm_folder", $update_arr, array("id" => $this->input->post('id'), "teacher_id" => $data['cookie_teacher_id']));
                    }
                }
            }
            redirect('user/manage_folder/');
        }
    }

//导入浏览器收藏夹要用的函数
    private function import_folder($teacher_id, $item, $father_id = -1)
    {
        $out = '';
        if (is_bookmark_folder($item)) {
            $root_name1 = array_key_first($item);
            $tmp1 = $this->all_model->general_select("bm_folder", "id", array("folder_name" => $root_name1, "teacher_id" => $teacher_id, "father_id" => $father_id));
            if ($tmp1 == null) {
                $father_id = $this->all_model->general_insert('bm_folder', array("folder_name" => $root_name1, "teacher_id" => $teacher_id, "father_id" => $father_id));
                $out = $out . "目录[" . $root_name1 . "]插入成功<br>";
            } else {
                $father_id = $tmp1[0]['id'];
                $out = $out . "目录[" . $root_name1 . "]已存在<br>";
            }
            for ($j = 0; $j < count($item[$root_name1]); $j++) {
                $out = $out . $this->import_folder($teacher_id, $item[$root_name1][$j], $father_id);
            }
        } else {
            if ($this->all_model->general_get_amount("bm_bookmark", array("url" => $item["href"], "teacher_id" => $teacher_id)) == 0) {
                $insert_arr = array();
                $insert_arr['teacher_id'] = $teacher_id;
                $insert_arr["createtime"] = date("Y-m-d H:i:s");
                $insert_arr["title"] = $item["name"];
                $insert_arr["url"] = $item["href"];
                $insert_arr["icon"] = $item["icon"];
                $insert_arr["icon_uri"] = $item["icon"];
                $insert_arr["folder_id"] = $father_id;
                $insert_arr['safe_code'] = rand_str(8);
                $this->all_model->general_insert('bm_bookmark', $insert_arr);
                $out = $out . $item["name"] . "导入成功<br>";
            } else {
                $out = $out . $item["name"] . "跳过<br>";
            }
        }
        return $out;
    }


//导入浏览器收藏夹
    public function import()
    {
        $data = $this->general_data;
        $data['title'] = '导入浏览器收藏夹';
        $data['folder'] = $this->all_model->general_list("bm_folder", array("teacher_id" => $data['cookie_teacher_id'], "father_id" => -1));
        $this->form_validation->set_rules('json_string', '文件', 'required');
        $data['output'] = array();
        if ($_POST["submit"] != "submit") {
            $this->load->view('templates/header', $data);
            $this->load->view('teachers/import', $data);
            $this->load->view('templates/footer');
        } else {
            $arr = json_decode($this->input->post('json_string'), 1);
            //var_dump($this->input->post('onlywork'));
            $root_name = array_key_first($arr);
            $data['output'][] = "根名：" . $root_name;
            if ($data['cookie_level'] == "work") {
                if($this->input->post('onlywork')=="onlywork"){
                    $father_id = -1;
                }
                else {
                    $work_folder = $this->all_model->general_get("bm_folder", array("teacher_id" => $data['cookie_teacher_id'], "folder_name" => "工作", "father_id" => -1));
                    $father_id = $work_folder['id'];
                }
            } else {
                $father_id = -1;
            }
            for ($i = 0; $i < count($arr[$root_name]); $i++) {
                echo $this->import_folder($data['cookie_teacher_id'], $arr[$root_name][$i], $father_id);
            }
            $this->load->view('templates/header', $data);
            $this->load->view('teachers/import', $data);
            $this->load->view('templates/footer');
        }
    }

    //导出html要用到的函数
    private function export_folder($teacher_id, $folder, $father_id = -1)
    {
        $out = '';
        foreach ($folder as $folder_item) {
            $out = $out . '<DT><H3 ADD_DATE="' . $folder_item['timestamp'] . '">' . $folder_item['folder_name'] . '</H3>';
            $out = $out . "\n";
            $out = $out . '<DL><p>';
            $out = $out . "\n";
            $sub_folder = $this->all_model->general_list("bm_folder", array("teacher_id" => $teacher_id, "father_id" => $folder_item['id']), array("convert(folder_name using gbk)" => "asc"));
            $out = $out . $this->export_folder($teacher_id, $sub_folder, $folder_item['id']);
            $bookmark = $this->all_model->general_list("bm_bookmark", array("teacher_id" => $teacher_id, 'folder_id' => $folder_item['id']), array("tag" => "desc", "convert(title using gbk)" => "asc"));
            foreach ($bookmark as $bookmark_item) {
                $out = $out . '<DT><A HREF="' . $bookmark_item['url'] . '" ADD_DATE="' . $bookmark_item['timestamp'] . '" ICON="' . $bookmark_item['icon'] . '" ICON_URI="' . $bookmark_item['icon_uri'] . '">' . $bookmark_item['title'] . '</A>';
                $out = $out . "\n";
            }
            $out = $out . '</DL>';
            $out = $out . "\n";
        }
        return $out;
    }


    //导出html
    public function export()
    {
        $data = $this->general_data;
        $out = '<DL><p>';
        $out = $out . "\n";

        if ($data['cookie_level'] == "work") {
            $lv1_folder = $this->all_model->general_list("bm_folder", array("teacher_id" => $data['cookie_teacher_id'], "folder_name" => "工作", "father_id" => -1), array("convert(folder_name using gbk)" => "asc"));
            $onlywork="<META name='onlywork'>\n";
            $out_filename="gm_ws_bookmarks_work_" . date("Y_m_d") . ".html";
        } else {
            $root_bookmark = $this->all_model->general_list("bm_bookmark", array("teacher_id" => $data['cookie_teacher_id'], 'folder_id' => -1), array("tag" => "desc", "convert(title using gbk)" => "asc"));
            foreach ($root_bookmark as $item) {
                $out = $out . '<DT><A HREF="' . $item['url'] . '" ADD_DATE="' . $item['timestamp'] . '" ICON="' . $item['icon'] . '" ICON_URI="' . $item['icon_uri'] . '">' . $item['title'] . '</A></DT>';
                $out = $out . "\n";
            }
            $lv1_folder = $this->all_model->general_list("bm_folder", array("teacher_id" => $data['cookie_teacher_id'], "father_id" => -1), array("convert(folder_name using gbk)" => "asc"));
            $onlywork="";
            $out_filename="gm_ws_bookmarks_" . date("Y_m_d") . ".html";
        }
        $out = $out . $this->export_folder($data['cookie_teacher_id'], $lv1_folder);

        $out = $out . '</DL>';
        $out = $out . "\n";

        $header = "<!DOCTYPE NETSCAPE-Bookmark-file-1>\n";
        $header = $header."<!-- This is an automatically generated file.\n" ;
        $header = $header."It will be read and overwritten.\n";
        $header = $header. "DO NOT EDIT! -->\n" ;
        $header = $header. '<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8">' . "\n" ;
        $header = $header. $onlywork ;
        $out = $header."<TITLE>收藏夹</TITLE>\n<H1>收藏夹</H1>\n" . $out;
        header('Accept-Ranges: bytes');
        //header('Accept-Length: ' . filesize($filename));
        header('Content-Transfer-Encoding: binary');
        header('Content-type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . $out_filename);
        header('Content-Type: application/octet-stream; name=' . $out_filename);
        echo $out;
    }

    //列出公共文档
    public function list_documents($grade = "-", $subject_id = "-", $category_id = "-", $page = 0)
    {
        $data = $this->general_data;
        $data['subjects'] = $this->all_model->general_list("bm_subjects", array("id!=" => "-1"), array("sort" => "desc"));
        array_unshift($data['subjects'], array("id" => "-", "name" => "全部"));
        $data['document_categories'] = $this->all_model->general_load2('bm_document_categories', array("sort" => "DESC"));
        array_unshift($data['document_categories'], array("id" => "-", "name" => "全部"));
        $data['grades'] = GRADE_ARR;
        array_unshift($data['grades'], "-");
        $data['cngrades'] = CNGRADE_ARR;
        array_unshift($data['cngrades'], "全部");
        $where_arr = array();
        if ($grade != "-") {
            $where_arr["grade"] = $grade;
        }
        if ($subject_id != "-") {
            $where_arr["subject_id"] = $subject_id;
        }
        if ($category_id != "-") {
            $where_arr["category_id"] = $category_id;
        }
        $where_arr["is_private"] = 0;
        $limit = 50;

        $data['documents'] = $this->all_model->general_page_list("bm_documents", $where_arr, array("folder" => "desc"), $limit, $page, null);

        $data['category_id'] = $category_id;
        $data['grade'] = $grade;
        $data['subject_id'] = $subject_id;

        $data['teachers'] = $this->all_model->general_select("bm_user", "id,name", null);
        $data['teacher_arr'] = array();
        foreach ($data['teachers'] as $item):
            $data['teacher_arr'][$item['id']] = $item['name'];
        endforeach;


        $data['title'] = '公共文档';

        $data['curent_page'] = $page;
        $this->load->library('pagination');
        $config['base_url'] = site_url('user/list_documents/' . $grade . '/' . $subject_id . '/' . $category_id);
        $config['total_rows'] = $this->all_model->general_get_amount('bm_documents', array('is_private' => 0), null);
        $config['per_page'] = $limit;
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="###">';
        $config['cur_tag_close'] = '</a></li>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $this->pagination->initialize($config);
        $data['page'] = $this->pagination->create_links();

        $this->load->view('templates/header', $data);
        $this->load->view('teachers/list_documents', $data);
        $this->load->view('templates/footer');
    }


    //列出自己的文档
    public function my_documents($grade = "-", $subject_id = "-", $category_id = "-", $page = 0)
    {
        $data = $this->general_data;
        $data['subjects'] = $this->all_model->general_list("bm_subjects", array("id!=" => "-1"), array("sort" => "desc"));
        array_unshift($data['subjects'], array("id" => "-", "name" => "全部"));
        $data['document_categories'] = $this->all_model->general_load2('bm_document_categories', array("sort" => "DESC"));
        array_unshift($data['document_categories'], array("id" => "-", "name" => "全部"));
        $data['grades'] = GRADE_ARR;
        array_unshift($data['grades'], "-");
        $data['cngrades'] = CNGRADE_ARR;
        array_unshift($data['cngrades'], "全部");
        $where_arr = array();
        if ($grade != "-") {
            $where_arr["grade"] = $grade;
        }
        if ($subject_id != "-") {
            $where_arr["subject_id"] = $subject_id;
        }
        if ($category_id != "-") {
            $where_arr["category_id"] = $category_id;
        }
        $where_arr["upload_teacher_id"] = $data['cookie_teacher_id'];

        $limit = 50;

        $data['documents'] = $this->all_model->general_page_list("bm_documents", $where_arr, array("folder" => "desc"), $limit, $page, null);

        $data['category_id'] = $category_id;
        $data['grade'] = $grade;
        $data['subject_id'] = $subject_id;
        $data['title'] = '我的文档';

        $data['curent_page'] = $page;
        $this->load->library('pagination');
        $config['base_url'] = site_url('user/my_documents/' . $grade . '/' . $subject_id . '/' . $category_id);
        $config['total_rows'] = $this->all_model->general_get_amount('bm_documents', array('upload_teacher_id' => $data['cookie_teacher_id']), null);
        $config['per_page'] = $limit;
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="###">';
        $config['cur_tag_close'] = '</a></li>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $this->pagination->initialize($config);
        $data['page'] = $this->pagination->create_links();

        $this->load->view('templates/header', $data);
        $this->load->view('teachers/my_documents', $data);
        $this->load->view('templates/footer');
    }


//上传一个文档（七牛）
    public function upload_document_qiniu($grade = "-", $subject_id = "-", $category_id = "-")
    {
        $data = $this->general_data;

        $this->load->library('Qiniu');
        $auth = new Qiniu\Auth(QINIU_ACCESSKEY, QINIU_SECRETKEY);

        $data['title'] = "上传公共文档";
        $data['document_categories'] = $this->all_model->general_load2('bm_document_categories', array("sort" => "DESC"));
        $data['subjects'] = $this->all_model->general_list("bm_subjects", array("id!=" => "-1"), array("sort" => "desc"));
        $this->form_validation->set_rules('userfile', '文档', 'required');


        $config['allowed_types'] = 'pdf|xls|xlsx|doc|docx|ppt|pptx|zip|rar|txt|7z|7zip|jpg|png|gif|jpeg|bmp|webp|mp4|mp3';
        $config['file_ext_tolower'] = true;
        $config['overwrite'] = true;
        $config['max_size'] = 20480;
        $config['encrypt_name'] = true;


        if ($this->input->post('submit') == "提交") {

            $this->load->helper('path');
            $rndString = randString();
            $thumb_path = "uploads/documents/" . date("Y/m/d");
            if (!is_dir($thumb_path)) {
                mkdir($thumb_path, 0755, true);     //对于缩略图，需要生成目录
            }
            if ($_FILES["userfile"]["tmp_name"] != "") {
                //$name = session_create_id() . "." . get_extension($_FILES["userfile"]["name"]);           //要求PHP7.1以上
                $name = $_FILES["userfile"]["name"];
                $filePath = $_FILES["userfile"]["tmp_name"];
                $filesize = $_FILES["userfile"]["size"];
// 上传到七牛后保存的文件名
                $key = $thumb_path . "/" . $rndString . "/" . $name;
                // 生成上传 Token
                $token = $auth->uploadToken(QINIU_BUCKET, $key);
                // 初始化 UploadManager 对象并进行文件的上传。
                $uploadMgr = new Qiniu\Storage\UploadManager();
// 调用 UploadManager 的 putFile 方法进行文件的上传。
                list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
                if ($err !== null) {
                    var_dump($err);
                } else {
                    //echo $ret['key'];   //上传成功后的文件名
                    if (thumb_img($_FILES["userfile"]["name"])) {
                        $config_manip = array(
                            'image_library' => 'gd2',
                            'source_image' => $_FILES["userfile"]["tmp_name"],
                            'new_image' => $thumb_path . "/" . "thumb_" . $rndString . ".jpg",
                            'create_thumb' => true,
                            'thumb_marker' => '',
                            'maintain_ratio' => true,
                            'width' => 140,
                            'height' => 140
                        );
                        // Create thumbnail
                        $this->load->library('image_lib');
                        $this->image_lib->resize();
                        $this->image_lib->clear();
                        $this->image_lib->initialize($config_manip);
                        if (!$this->image_lib->resize()) {
                            echo $this->image_lib->display_errors();
                        }
                    }
                    $category = $this->all_model->upload_document('bm_documents', $_FILES["userfile"]["name"], $thumb_path, $rndString, $name, get_extension($_FILES["userfile"]["name"]), $filesize);
                    $category_id = $this->input->post('category_id');
                    $grade = $this->input->post('grade');
                    $subject_id = $this->input->post('subject_id');
                    redirect('user/my_documents/' . $grade . '/' . $subject_id . '/' . $category_id);
                }
            }
        } else {
            $data['category_id'] = $category_id;
            $data['grade'] = $grade;
            $data['subject_id'] = $subject_id;

            $this->load->view('templates/header', $data);
            $this->load->view('teachers/upload_document', $data);
            $this->load->view('templates/footer');
        }
    }


//下载文档
    public function download_document($id)
    {
        $data = $this->general_data;
        $document = $this->all_model->general_get('bm_documents', array("id" => $id));
        if (($document['is_private'] == 1 && $document['upload_teacher_id'] == $data['cookie_teacher_id']) || $document['is_private'] == 0) {
            $filename = $document['path'] . $document['real_filename'];
            $out_filename = $document['original_filename'];
            if (!file_exists($filename)) {
                echo('Not Found' . $filename);
                exit;
            } else {
                header('Accept-Ranges: bytes');
                header('Accept-Length: ' . filesize($filename));
                header('Content-Transfer-Encoding: binary');
                header('Content-type: application/octet-stream');
                header('Content-Disposition: attachment; filename=' . $out_filename);
                header('Content-Type: application/octet-stream; name=' . $out_filename);
                if (is_file($filename) && is_readable($filename)) {
                    $file = fopen($filename, "r");
                    echo(fread($file, filesize($filename)));
                    fclose($file);
                }
                exit;
            }
        } else {
            redirect('index/access_forbidden');
        }
    }

//管理员删除一个公共文档（本地）
    public function delete_document($category_id, $id)
    {
        $data = $this->general_data;
        if (in_array('au_document_delete', $data['authority'])) {
            $array = array('id' => $id);
            $data['result'] = $this->all_model->general_get('bm_documents', $array);
            if (is_file($data['result']['path'] . "thumb_" . $data['result']['real_filename'])) {
                unlink($data['result']['path'] . "thumb_" . $data['result']['real_filename']);
            }
            if (is_file($data['result']['path'] . $data['result']['real_filename'])) {
                unlink($data['result']['path'] . $data['result']['real_filename']);
            }
            $this->all_model->general_delete("bm_documents", $array);
            redirect('user/list_documents/' . $category_id);
        } else {
            redirect('index/access_forbidden');
        }
    }

//上传者删除自己的文档（全部本地）
    public function self_delete_document($grade, $subject_id, $category_id, $id)
    {
        $data = $this->general_data;
        $array = array('id' => $id, "upload_teacher_id" => $data['cookie_teacher_id']);
        $data['result'] = $this->all_model->general_get('bm_documents', $array);
        if (is_file($data['result']['path'] . "thumb_" . $data['result']['real_filename'])) {
            unlink($data['result']['path'] . "thumb_" . $data['result']['real_filename']);
        }
        if (is_file($data['result']['path'] . $data['result']['real_filename'])) {
            unlink($data['result']['path'] . $data['result']['real_filename']);
        }
        $this->all_model->general_delete("bm_documents", $array);
        redirect('user/my_documents/' . $grade . '/' . $subject_id . '/' . $category_id);
    }

//管理员删除一个文档（本地缩略图加七牛大图）
    public function delete_document_qiniu($grade, $subject_id, $category_id, $id)
    {
        $data = $this->general_data;
        if (in_array('au_document_delete', $data['authority'])) {
            $array = array('id' => $id);
            $data['result'] = $this->all_model->general_get('bm_documents', $array);
            if (is_file($data['result']['path'] . "/thumb_" . $data['result']['rndstring'] . ".jpg")) {
                unlink($data['result']['path'] . "/thumb_" . $data['result']['rndstring'] . ".jpg");
            }
            $this->load->library('Qiniu');
            $auth = new Qiniu\Auth(QINIU_ACCESSKEY, QINIU_SECRETKEY);
            $bucketManager = new Qiniu\Storage\BucketManager($auth);
            $keys = array($data['result']['path'] . "/" . $data['result']['rndstring'] . "/" . $data['result']['real_filename']);
            $ops = $bucketManager->buildBatchDelete(QINIU_BUCKET, $keys);
            list($ret, $err) = $bucketManager->batch($ops);

            $this->all_model->general_delete("bm_documents", $array);
            redirect('user/list_documents/' . $grade . '/' . $subject_id . '/' . $category_id);
        } else {
            redirect('index/access_forbidden');
        }
    }


//上传者删除自己的文档（本地缩略图加七牛大图）
    public function self_delete_document_qiniu($grade, $subject_id, $category_id, $id)
    {
        $data = $this->general_data;
        $array = array('id' => $id, "upload_teacher_id" => $data['cookie_teacher_id']);
        $data['result'] = $this->all_model->general_get('bm_documents', $array);
        if (is_file($data['result']['path'] . "/thumb_" . $data['result']['rndstring'] . ".jpg")) {
            unlink($data['result']['path'] . "/thumb_" . $data['result']['rndstring'] . ".jpg");
        }
        $this->load->library('Qiniu');
        $auth = new Qiniu\Auth(QINIU_ACCESSKEY, QINIU_SECRETKEY);
        $bucketManager = new Qiniu\Storage\BucketManager($auth);
        $keys = array($data['result']['path'] . "/" . $data['result']['rndstring'] . "/" . $data['result']['real_filename']);
        $ops = $bucketManager->buildBatchDelete(QINIU_BUCKET, $keys);
        list($ret, $err) = $bucketManager->batch($ops);

        $this->all_model->general_delete("bm_documents", $array);
        redirect('user/my_documents/' . $grade . '/' . $subject_id . '/' . $category_id);
    }

//上传者修改自己文档的分类和是否公开
    public function self_edit_document($grade, $subject_id, $category_id, $id)
    {
        $data = $this->general_data;
        $where_arr = array('id' => $id, "upload_teacher_id" => $data['cookie_teacher_id']);
        $data['document'] = $this->all_model->general_get('bm_documents', $where_arr);
        $data['document_categories'] = $this->all_model->general_load2('bm_document_categories', array("sort" => "DESC"));
        $data['subjects'] = $this->all_model->general_list("bm_subjects", array("id!=" => "-1"), array("sort" => "desc"));
        $data['title'] = '编辑文档属性';
        $data['category_id'] = $category_id;
        $data['grade'] = $grade;
        $data['subject_id'] = $subject_id;
        $this->form_validation->set_rules('submit', '值', 'required');

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('teachers/self_edit_document', $data);
            $this->load->view('templates/footer');
        } else {
            $post = $this->input->post();
            $update_arr = array();
            $except_arr = array("submit");
            foreach ($post as $x_key => $x_value):
                if (!in_array($x_key, $except_arr)) {
                    $update_arr[$x_key] = $x_value;
                }
            endforeach;
            $this->all_model->general_update("bm_documents", $update_arr, $where_arr);
            redirect('user/my_documents/' . $grade . '/' . $subject_id . '/' . $category_id);
        }
    }


//编辑教师
    public function edit_teacher($id)
    {
        $data = $this->general_data;
        $data['teacher'] = $this->all_model->general_get("bm_user", array("id" => $id));
        $data['counties'] = $this->all_model->general_load("xz_counties", "sort", "desc");
        $data['parties'] = $this->all_model->general_load("xz_parties", "sort", "desc");
        $data['education'] = $this->all_model->general_load("xz_education", "sort", "desc");
        $data['marriage'] = $this->all_model->general_load("xz_marriage", "sort", "desc");
        $data['certification'] = $this->all_model->general_load("xz_certification", "sort", "desc");
        $data['titles'] = $this->all_model->general_load("xz_titles", "sort", "desc");
        $data['ethnicity'] = $this->all_model->general_load("xz_ethnicity", "sort", "desc");
        $data['subjects'] = $this->all_model->general_list("bm_subjects", array("id!=" => "-1"), array("sort" => "desc"));
        $data['phases'] = $this->all_model->general_load("xz_phases", "sort", "desc");
        $data['mandarin_level'] = $this->all_model->general_load("xz_mandarin_level", "sort", "desc");
        $data['pc_level'] = $this->all_model->general_load("xz_pc_level", "sort", "desc");
        $data['core_teacher'] = $this->all_model->general_load("xz_core_teacher", "sort", "desc");
        $data['famous_teacher'] = $this->all_model->general_load("xz_famous_teacher", "sort", "desc");
        $data['stafftype'] = $this->all_model->general_load("xz_stafftype", "sort", "desc");
        $data['position'] = $this->all_model->general_load("xz_position", "sort", "desc");
        $data['departments'] = $this->all_model->general_list("xz_departments", array("parent_id" => -1), array("sort" => "desc"));
        for ($i = 0; $i < count($data['departments']); $i++) {
            $data['departments'][$i]["subdepartments"] = $this->all_model->general_list("xz_departments", array("parent_id" => $data['departments'][$i]['id']), array("sort" => "desc"));
        }
        $data['departments'][] = array("id" => "0", "name" => "未分类");

        $data['title'] = $data['teacher']['name'] . "-详细资料";
        $this->form_validation->set_rules('teacher_id', 'id', 'required');

        //var_dump($this->form_validation->run());
        if ($this->form_validation->run() === FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('teachers/edit_teacher', $data);
            $this->load->view('templates/footer');
        } else {
            $post = $this->input->post();
            $update_arr = array();
            $except_arr = array("teacher_id", "submit");
            foreach ($post as $x_key => $x_value):
                if (!in_array($x_key, $except_arr)) {
                    $update_arr[$x_key] = $x_value;
                }
            endforeach;
            $this->all_model->general_update("bm_user", $update_arr, array("id" => $this->input->post('teacher_id')));
            $_SESSION['err_msg'] = err_msg("修改成功！");
            redirect('user/edit_teacher/' . $id);
            //redirect('user/list_teachers/');
        }
    }


//自己编辑资料
    public function self_edit_teacher()
    {
        $data = $this->general_data;
        $data['teacher'] = $this->all_model->general_get("bm_user", array("id" => $data['cookie_teacher_id']));
        $data['title'] = "编辑个人资料";
        $this->form_validation->set_rules('teacher_id', 'id', 'required');

        //var_dump($this->form_validation->run());
        if ($this->form_validation->run() === FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('teachers/self_edit_teacher', $data);
            $this->load->view('templates/footer');
        } else {
            $post = $this->input->post();
            $update_arr = array();
            $except_arr = array("teacher_id", "submit");
            foreach ($post as $x_key => $x_value):
                if (!in_array($x_key, $except_arr)) {
                    $update_arr[$x_key] = $x_value;
                }
            endforeach;
            if ($this->all_model->general_get_amount('bm_user', array("id!=" => $data['cookie_teacher_id'], "name" => $this->input->post('name'))) > 0) {
                $_SESSION['err_msg'] = err_msg("已存在相同昵称！");
            } elseif ($this->all_model->general_get_amount('bm_user', array("id!=" => $data['cookie_teacher_id'], "email" => $this->input->post('email'))) > 0) {
                $_SESSION['err_msg'] = err_msg("已存在相同电子邮件！");
            } else {
                $this->all_model->general_update("bm_user", $update_arr, array("id" => $data['cookie_teacher_id']));
            }
            redirect('user/self_edit_teacher');
        }
    }

//修改密码
    public function pwd($result = NULL)
    {
        $data = $this->general_data;
        $data['name'] = $data['teacher_item']['name'];
        $data['result'] = $result;

        $this->form_validation->set_rules('teacher_id', 'id', 'required');

        //var_dump($this->form_validation->run());
        if ($this->form_validation->run() === FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('teachers/pwd', $data);
            $this->load->view('templates/footer');
        } else {
            if ($this->all_model->update_pwd() == "error") {
                redirect('user/pwd/fail');
            } else {
                redirect('user/pwd/success');
            }
        }
    }

    ////////////////////
}