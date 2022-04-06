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
        $data['teacher'] = $this->all_model->general_get("xz_teachers", array("id" => $_SESSION['teacher_id']));
        $data['title'] = '行事历';

        $data['personal_events'] = $this->all_model->general_list("xz_personal_events", array("teacher_id" => $_SESSION['teacher_id'], "date_format(date,'%Y-%m')" => date("Y-m", strtotime($year . "-" . $month))));

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
            $this->all_model->general_update("xz_teachers", $update_arr, array("id" => $_SESSION['teacher_id']));
            redirect('user/home/');
        }

        $this->load->view('templates/header', $data);
        $this->load->view('teachers/calendar', $data);
        $this->load->view('templates/footer');
    }


//在登录后首页删除个人记事
    public function delete_personal_event($id, $year = null, $month = null)
    {
        $this->all_model->general_delete("xz_personal_events", array("id" => $id));
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
            $update_arr['teacher_id'] = $_SESSION['teacher_id'];
            $this->all_model->general_insert("xz_personal_events", $update_arr);
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
        $this->all_model->general_update("xz_teachers", $update_arr, array("id" => $_SESSION['teacher_id']));
        echo "saved";
    }


//个人首页
    public function home()
    {
        $data = $this->general_data;
        $data['title'] = '我的收藏夹';
        $data['folder'] = $this->all_model->general_list("xz_folder", array("teacher_id" => $_SESSION['teacher_id'], "father_id" => -1), array("convert(folder_name using gbk)" => "asc"));
        for ($i = 0; $i < count($data['folder']); $i++) {
            $data['folder'][$i]["subfolder"] = $this->all_model->general_list("xz_folder", array("teacher_id" => $_SESSION['teacher_id'], "father_id" => $data['folder'][$i]['id']), array("convert(folder_name using gbk)" => "asc"));
        }
        $data['folder'][] = array("id" => "0", "folder_name" => "根目录");   //不能直接放到第一位
        array_unshift($data['folder'], array_pop($data['folder']));         //把最后一个元素移到第一位

        $data['bookmark']=array();
        $data['bookmark'][]=$this->all_model->general_list("xz_bookmark", array("teacher_id" => $_SESSION['teacher_id'],'folder_id'=>0), array("tag" => "desc", "convert(title using gbk)" => "asc"));
        $folder0=$this->all_model->general_list("xz_folder", array("teacher_id" => $_SESSION['teacher_id'], "father_id" => -1), array("convert(folder_name using gbk)" => "asc"));
        foreach($folder0 as $item0){
            $data['bookmark'][$item0['folder_name']]=array();
            $data['bookmark'][$item0['folder_name']][]=$this->all_model->general_list("xz_bookmark", array("teacher_id" => $_SESSION['teacher_id'],'folder_id'=>$item0['id']), array("tag" => "desc", "convert(title using gbk)" => "asc"));
            $father_id1=$item0['id'];
            $folder1=$this->all_model->general_list("xz_folder", array("teacher_id" => $_SESSION['teacher_id'], "father_id" => $father_id1), array("convert(folder_name using gbk)" => "asc"));
            foreach ($folder1 as $item1){
                $data['bookmark'][$item0['folder_name']][$item1['folder_name']]=array();
                $data['bookmark'][$item0['folder_name']][$item1['folder_name']][]=$this->all_model->general_list("xz_bookmark", array("teacher_id" => $_SESSION['teacher_id'],'folder_id'=>$item1['id']), array("tag" => "desc", "convert(title using gbk)" => "asc"));
            }
        }


/*
        $temp_id = $_SESSION["teacher_id"];
        $sql = "select * from xz_bookmark where is_private=0 or (is_private=1 and teacher_id='$temp_id') order by tag desc";
        $query = $this->db->query($sql);
        $data['bookmark'] = $query->result_array();
*/

        $this->load->view('templates/header', $data);
        $this->load->view('teachers/home', $data);
        $this->load->view('templates/footer');
    }

//管理书签
    public function manage_bookmark()
    {
        $data = $this->general_data;
        $data['title'] = $data['teacher_item']['name'] . '的书签';
        $data['bookmark'] = $this->all_model->general_list("xz_bookmark", array("teacher_id" => $_SESSION['teacher_id']), array("folder_id" => "desc", "tag" => "desc", "convert(title using gbk)" => "asc"));
        $data['tag'] = $this->all_model->general_select("xz_bookmark", "tag", array("teacher_id" => $_SESSION['teacher_id'], "tag!=" => ""), null, null, "tag");
        $data['folder'] = $this->all_model->general_list("xz_folder", array("teacher_id" => $_SESSION['teacher_id'], "father_id" => -1), array("convert(folder_name using gbk)" => "asc"));
        for ($i = 0; $i < count($data['folder']); $i++) {
            $data['folder'][$i]["subfolder"] = $this->all_model->general_list("xz_folder", array("teacher_id" => $_SESSION['teacher_id'], "father_id" => $data['folder'][$i]['id']), array("convert(folder_name using gbk)" => "asc"));
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
                $update_arr["teacher_id"] = $_SESSION['teacher_id'];
                $update_arr["createtime"] = date("Y-m-d H:i:s");
                $paresed_url=parse_url($this->input->post('url'));
                if (check_remote_file_exists($paresed_url['scheme'].'://'.$paresed_url['host'].'/favicon.ico')){
                    $update_arr["icon_uri"] = $paresed_url['scheme'].'://'.$paresed_url['host'].'/favicon.ico';
                }
                $this->all_model->general_insert("xz_bookmark", $update_arr);
            } elseif ($this->input->post('submit') == "update") {
                $this->all_model->general_update("xz_bookmark", $update_arr, array("id" => $this->input->post('id'), "teacher_id" => $_SESSION['teacher_id']));
            } elseif ($this->input->post('submit') == "delete") {
                $this->all_model->general_delete("xz_bookmark", array("id" => $this->input->post('id'), "teacher_id" => $_SESSION['teacher_id']));
            }
            redirect('user/manage_bookmark/#'.$this->input->post('id'));
        }
    }


    //列出公共文档
    public function list_documents($grade = "-", $subject_id = "-", $category_id = "-", $page = 0)
    {
        $data = $this->general_data;
        $data['subjects'] = $this->all_model->general_list("xz_subjects", array("id!=" => "-1"), array("sort" => "desc"));
        array_unshift($data['subjects'], array("id" => "-", "name" => "全部"));
        $data['document_categories'] = $this->all_model->general_load2('xz_document_categories', array("sort" => "DESC"));
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

        $data['documents'] = $this->all_model->general_page_list("xz_documents", $where_arr, array("folder" => "desc"), $limit, $page, null);

        $data['category_id'] = $category_id;
        $data['grade'] = $grade;
        $data['subject_id'] = $subject_id;

        $data['teachers'] = $this->all_model->general_select("xz_teachers", "id,name", array("employed" => 0));
        $data['teacher_arr'] = array();
        foreach ($data['teachers'] as $item):
            $data['teacher_arr'][$item['id']] = $item['name'];
        endforeach;


        $data['title'] = '公共文档';

        $data['curent_page'] = $page;
        $this->load->library('pagination');
        $config['base_url'] = site_url('user/list_documents/' . $grade . '/' . $subject_id . '/' . $category_id);
        $config['total_rows'] = $this->all_model->general_get_amount('xz_documents', array('is_private' => 0), null);
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
        $data['subjects'] = $this->all_model->general_list("xz_subjects", array("id!=" => "-1"), array("sort" => "desc"));
        array_unshift($data['subjects'], array("id" => "-", "name" => "全部"));
        $data['document_categories'] = $this->all_model->general_load2('xz_document_categories', array("sort" => "DESC"));
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
        $where_arr["upload_teacher_id"] = $_SESSION['teacher_id'];

        $limit = 50;

        $data['documents'] = $this->all_model->general_page_list("xz_documents", $where_arr, array("folder" => "desc"), $limit, $page, null);

        $data['category_id'] = $category_id;
        $data['grade'] = $grade;
        $data['subject_id'] = $subject_id;
        $data['title'] = '我的文档';

        $data['curent_page'] = $page;
        $this->load->library('pagination');
        $config['base_url'] = site_url('user/my_documents/' . $grade . '/' . $subject_id . '/' . $category_id);
        $config['total_rows'] = $this->all_model->general_get_amount('xz_documents', array('upload_teacher_id' => $_SESSION['teacher_id']), null);
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

//上传一个文档（本地）
    public function upload_document($grade = "-", $subject_id = "-", $category_id = "-")
    {
        $data = $this->general_data;
        $data['title'] = "上传公共文档";
        $data['document_categories'] = $this->all_model->general_load2('xz_document_categories', array("sort" => "DESC"));
        $data['subjects'] = $this->all_model->general_list("xz_subjects", array("id!=" => "-1"), array("sort" => "desc"));
        $this->form_validation->set_rules('userfile', '文档', 'required');

        if ($this->input->post('submit') == "提交") {

            $this->load->helper('path');
            $path = "uploads/documents/" . date("Y/m/d") . "/";
            //$path = "uploads/documents/" . find_in_arr($data['document_categories'], "name", $this->input->post('category'), "id") . "/";
            if (!is_dir($path)) {
                mkdir($path, 0755, true);
            }
            $config['upload_path'] = $path;
            $config['allowed_types'] = 'pdf|xls|xlsx|doc|docx|ppt|pptx|zip|rar|txt|7z|7zip|jpg|png|gif|jpeg|bmp|webp|mp4|mp3';
            $config['file_ext_tolower'] = true;
            $config['overwrite'] = true;
            $config['max_size'] = 20480;
            $config['encrypt_name'] = true;

            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('userfile')) {
                $error = array('error' => $this->upload->display_errors());
                $error = $error . "<br>请确认文件扩展名和文件真实类型一致，而不是自己修改的扩展名。";
                $this->load->view('templates/error', $error);
                $uploaded = $this->upload->data();
                var_dump($uploaded['file_type']);
                die;
            } else {
                $uploaded = $this->upload->data();
                //var_dump($uploaded['file_type']);
                //die;
                //var_dump($uploaded['file_ext']);
                if (thumb_img($uploaded['file_name'])) {
                    $config_manip = array(
                        'image_library' => 'gd2',
                        'source_image' => $path . "{$uploaded['file_name']}",
                        'new_image' => $path . "thumb_{$uploaded['file_name']}",
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
                        return array('errors' => $this->image_lib->display_errors());
                        $this->load->view('templates/error', $error);
                    }
                }
                $category = $this->all_model->upload_document('xz_documents', $uploaded['orig_name'], $path, $uploaded['file_name'], $uploaded['file_ext']);
                $category_id = $this->input->post('category_id');
                $grade = $this->input->post('grade');
                $subject_id = $this->input->post('subject_id');
                redirect('user/my_documents/' . $grade . '/' . $subject_id . '/' . $category_id);
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


//上传一个文档（七牛）
    public function upload_document_qiniu($grade = "-", $subject_id = "-", $category_id = "-")
    {
        $data = $this->general_data;

        $this->load->library('Qiniu');
        $auth = new Qiniu\Auth(QINIU_ACCESSKEY, QINIU_SECRETKEY);

        $data['title'] = "上传公共文档";
        $data['document_categories'] = $this->all_model->general_load2('xz_document_categories', array("sort" => "DESC"));
        $data['subjects'] = $this->all_model->general_list("xz_subjects", array("id!=" => "-1"), array("sort" => "desc"));
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
                    $category = $this->all_model->upload_document('xz_documents', $_FILES["userfile"]["name"], $thumb_path, $rndString, $name, get_extension($_FILES["userfile"]["name"]), $filesize);
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
        $document = $this->all_model->general_get('xz_documents', array("id" => $id));
        if (($document['is_private'] == 1 && $document['upload_teacher_id'] == $_SESSION['teacher_id']) || $document['is_private'] == 0) {
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
            $data['result'] = $this->all_model->general_get('xz_documents', $array);
            if (is_file($data['result']['path'] . "thumb_" . $data['result']['real_filename'])) {
                unlink($data['result']['path'] . "thumb_" . $data['result']['real_filename']);
            }
            if (is_file($data['result']['path'] . $data['result']['real_filename'])) {
                unlink($data['result']['path'] . $data['result']['real_filename']);
            }
            $this->all_model->general_delete("xz_documents", $array);
            redirect('user/list_documents/' . $category_id);
        } else {
            redirect('index/access_forbidden');
        }
    }

//上传者删除自己的文档（全部本地）
    public function self_delete_document($grade, $subject_id, $category_id, $id)
    {
        $data = $this->general_data;
        $array = array('id' => $id, "upload_teacher_id" => $_SESSION['teacher_id']);
        $data['result'] = $this->all_model->general_get('xz_documents', $array);
        if (is_file($data['result']['path'] . "thumb_" . $data['result']['real_filename'])) {
            unlink($data['result']['path'] . "thumb_" . $data['result']['real_filename']);
        }
        if (is_file($data['result']['path'] . $data['result']['real_filename'])) {
            unlink($data['result']['path'] . $data['result']['real_filename']);
        }
        $this->all_model->general_delete("xz_documents", $array);
        redirect('user/my_documents/' . $grade . '/' . $subject_id . '/' . $category_id);
    }

//管理员删除一个文档（本地缩略图加七牛大图）
    public function delete_document_qiniu($grade, $subject_id, $category_id, $id)
    {
        $data = $this->general_data;
        if (in_array('au_document_delete', $data['authority'])) {
            $array = array('id' => $id);
            $data['result'] = $this->all_model->general_get('xz_documents', $array);
            if (is_file($data['result']['path'] . "/thumb_" . $data['result']['rndstring'] . ".jpg")) {
                unlink($data['result']['path'] . "/thumb_" . $data['result']['rndstring'] . ".jpg");
            }
            $this->load->library('Qiniu');
            $auth = new Qiniu\Auth(QINIU_ACCESSKEY, QINIU_SECRETKEY);
            $bucketManager = new Qiniu\Storage\BucketManager($auth);
            $keys = array($data['result']['path'] . "/" . $data['result']['rndstring'] . "/" . $data['result']['real_filename']);
            $ops = $bucketManager->buildBatchDelete(QINIU_BUCKET, $keys);
            list($ret, $err) = $bucketManager->batch($ops);

            $this->all_model->general_delete("xz_documents", $array);
            redirect('user/list_documents/' . $grade . '/' . $subject_id . '/' . $category_id);
        } else {
            redirect('index/access_forbidden');
        }
    }


//上传者删除自己的文档（本地缩略图加七牛大图）
    public function self_delete_document_qiniu($grade, $subject_id, $category_id, $id)
    {
        $data = $this->general_data;
        $array = array('id' => $id, "upload_teacher_id" => $_SESSION['teacher_id']);
        $data['result'] = $this->all_model->general_get('xz_documents', $array);
        if (is_file($data['result']['path'] . "/thumb_" . $data['result']['rndstring'] . ".jpg")) {
            unlink($data['result']['path'] . "/thumb_" . $data['result']['rndstring'] . ".jpg");
        }
        $this->load->library('Qiniu');
        $auth = new Qiniu\Auth(QINIU_ACCESSKEY, QINIU_SECRETKEY);
        $bucketManager = new Qiniu\Storage\BucketManager($auth);
        $keys = array($data['result']['path'] . "/" . $data['result']['rndstring'] . "/" . $data['result']['real_filename']);
        $ops = $bucketManager->buildBatchDelete(QINIU_BUCKET, $keys);
        list($ret, $err) = $bucketManager->batch($ops);

        $this->all_model->general_delete("xz_documents", $array);
        redirect('user/my_documents/' . $grade . '/' . $subject_id . '/' . $category_id);
    }

//上传者修改自己文档的分类和是否公开
    public function self_edit_document($grade, $subject_id, $category_id, $id)
    {
        $data = $this->general_data;
        $where_arr = array('id' => $id, "upload_teacher_id" => $_SESSION['teacher_id']);
        $data['document'] = $this->all_model->general_get('xz_documents', $where_arr);
        $data['document_categories'] = $this->all_model->general_load2('xz_document_categories', array("sort" => "DESC"));
        $data['subjects'] = $this->all_model->general_list("xz_subjects", array("id!=" => "-1"), array("sort" => "desc"));
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
            $this->all_model->general_update("xz_documents", $update_arr, $where_arr);
            redirect('user/my_documents/' . $grade . '/' . $subject_id . '/' . $category_id);
        }
    }



//一般通用导出数据到excel文件
//不能使用php7.3版本，会出错，只能用7.2版本
    public function export_excel()
    {
        $this->form_validation->set_rules('filename', '文件名', 'required');
        $this->form_validation->set_rules('data', '数据', 'required');
        if ($this->form_validation->run() !== FALSE) {
            $this->load->library("phpexcel");
            $obj = new \PHPExcel();

// 文件名和文件类型
            $fileName = $this->input->post('filename');
            $fileType = "xlsx";     //xls会出错

            $data_json = $this->input->post('data');

            $data = json_decode($data_json, true);


            // 以下内容是excel文件的信息描述信息
            $obj->getProperties()->setCreator(''); //设置创建者
            $obj->getProperties()->setLastModifiedBy(''); //设置修改者
            $obj->getProperties()->setTitle(''); //设置标题
            $obj->getProperties()->setSubject(''); //设置主题
            $obj->getProperties()->setDescription(''); //设置描述
            $obj->getProperties()->setKeywords('');//设置关键词
            $obj->getProperties()->setCategory('');//设置类型

            // 设置当前sheet
            $obj->setActiveSheetIndex(0);

            // 设置当前sheet的名称
            $obj->getActiveSheet()->setTitle('Sheet1');

            // 列标
            $list = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ', 'BA', 'BB', 'BC', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BK', 'BL', 'BM', 'BN', 'BO', 'BP', 'BQ', 'BR', 'BS', 'BT', 'BU', 'BV', 'BW', 'BX', 'BY', 'BZ');

            //填充标题（如果设置了的话）
            if ($data['title'] != null) {
                $start_row = 2;
                $obj->getActiveSheet()->mergeCells('A1:' . $list[count($data['header'])] . '1');
                $obj->setActiveSheetIndex(0)->setCellValue('A1', $data['title']);   //注意这种方法是设置第一个表为活动表，然后设置单元格；而不是获取当前活动表然后再设置单元格。注意区别，两种方法都可以。
                $styleArray = array(
                    'font' => array(
                        'bold' => false,
                        'color' => array('rgb' => 'FF0000'),
                        'size' => 9,
                        'name' => 'Verdana'
                    ));
                $obj->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);     //利用数组设置单元格样式
            } else {
                $start_row = 1;
            }
            // 填充列名数据

            for ($i = 0; $i < count($data['header']); $i++) {
                $obj->getActiveSheet()
                    ->setCellValue($list[$i] . $start_row, $data['header'][$i]);
            }

            // 填充第n(n>=2, n∈N*)行数据
            $length = count($data['body']);
            for ($i = 0; $i < count($data['body']); $i++) {
                for ($j = 0; $j < count($data['body'][$i]); $j++) {
                    $obj->getActiveSheet()->setCellValue($list[$j] . ($i + $start_row + 1), $data['body'][$i][$j], PHPExcel_Cell_DataType::TYPE_STRING);//将其设置为文本格式

                    $obj->getActiveSheet()->getCell($list[$j] . ($i + $start_row + 1))->setDataType('inlineStr');//设置单元格为文本格式
                    $obj->getActiveSheet()->getStyle($list[$j] . ($i + $start_row + 1))->getNumberFormat()->setFormatCode('0');
                    $obj->getActiveSheet()->setCellValue($list[$j] . ($i + $start_row + 1), $data['body'][$i][$j]);
                    //$obj->getActiveSheet()->setCellValue($list[$j] . ($i + 2), $data['body'][$i][$j],PHPExcel_Cell_DataType::TYPE_STRING);//将其设置为文本格式  //这种设置方式无效
                }
            }

            // 设置加粗和左对齐
            foreach ($list as $col) {
                // 设置标题行加粗
                $obj->getActiveSheet()->getStyle($col . $start_row)->getFont()->setBold(true);
                // 设置内容行左对齐
                for ($i = 1; $i <= $length + $start_row + 1; $i++) {
                    $obj->getActiveSheet()->getStyle($col . $i)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                }
            }

            // 设置列宽
            //$obj->getActiveSheet()->getColumnDimension('A')->setWidth(20);
            //$obj->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            //$obj->getActiveSheet()->getColumnDimension('C')->setWidth(15);

            // 导出
            ob_clean();
            if ($fileType == 'xls') {
                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename="' . $fileName . '.xls');
                header('Cache-Control: max-age=1');
                $objWriter = new \PHPExcel_Writer_Excel5($obj);
                $objWriter->save('php://output');
                exit;
            } elseif ($fileType == 'xlsx') {
                //header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('content-type:application/octet-stream');
                header('Content-Disposition: attachment;filename=' . $fileName . '.xlsx');
                header('Cache-Control: max-age=1');
                $objWriter = \PHPExcel_IOFactory::createWriter($obj, 'Excel2007');
                $objWriter->save('php://output');
                exit;
            }
        }
    }

//编辑教师
    public function edit_teacher($id)
    {
        $data = $this->general_data;
        require_authority(array("au_teacher_manage"), $data['authority']);
        $data['teacher'] = $this->all_model->general_get("xz_teachers", array("id" => $id));
        $data['counties'] = $this->all_model->general_load("xz_counties", "sort", "desc");
        $data['parties'] = $this->all_model->general_load("xz_parties", "sort", "desc");
        $data['education'] = $this->all_model->general_load("xz_education", "sort", "desc");
        $data['marriage'] = $this->all_model->general_load("xz_marriage", "sort", "desc");
        $data['certification'] = $this->all_model->general_load("xz_certification", "sort", "desc");
        $data['titles'] = $this->all_model->general_load("xz_titles", "sort", "desc");
        $data['ethnicity'] = $this->all_model->general_load("xz_ethnicity", "sort", "desc");
        $data['subjects'] = $this->all_model->general_list("xz_subjects", array("id!=" => "-1"), array("sort" => "desc"));
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
            $this->all_model->general_update("xz_teachers", $update_arr, array("id" => $this->input->post('teacher_id')));
            $_SESSION['err_msg'] = err_msg("修改成功！");
            redirect('user/edit_teacher/' . $id);
            //redirect('user/list_teachers/');
        }
    }


//自己编辑资料
    public function self_edit_teacher()
    {
        $data = $this->general_data;
        $data['teacher'] = $this->all_model->general_get("xz_teachers", array("id" => $_SESSION['teacher_id']));
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
            if ($this->all_model->general_get_amount('xz_teachers', array("id!="=>$_SESSION['teacher_id'],"name"=>$this->input->post('name')))>0){
                $_SESSION['err_msg'] = err_msg("已存在相同昵称！");
            }
            elseif($this->all_model->general_get_amount('xz_teachers', array("id!="=>$_SESSION['teacher_id'],"email"=>$this->input->post('email')))>0){
                $_SESSION['err_msg'] = err_msg("已存在相同电子邮件！");
            }
            else{
                $this->all_model->general_update("xz_teachers", $update_arr, array("id" => $_SESSION['teacher_id']));
            }
            redirect('user/self_edit_teacher/');
        }
    }

//新建教职工
    public function create_teacher()
    {
        $data = $this->general_data;
        require_authority(array("au_teacher_manage"), $data['authority']);
        $data['title'] = '新增教职工';
        $data['marriage'] = $this->all_model->general_load("xz_marriage", "sort", "desc");
        $data['education'] = $this->all_model->general_load("xz_education", "sort", "desc");
        $data['parties'] = $this->all_model->general_load("xz_parties", "sort", "desc");
        $data['stafftype'] = $this->all_model->general_load("xz_stafftype", "sort", "desc");
        $data['ethnicity'] = $this->all_model->general_load("xz_ethnicity", "sort", "desc");
        $data['position'] = $this->all_model->general_load("xz_position", "sort", "desc");
        $data['departments'] = $this->all_model->general_list("xz_departments", array("parent_id" => -1), array("sort" => "desc"));
        for ($i = 0; $i < count($data['departments']); $i++) {
            $data['departments'][$i]["subdepartments"] = $this->all_model->general_list("xz_departments", array("parent_id" => $data['departments'][$i]['id']), array("sort" => "desc"));
        }
        $data['departments'][] = array("id" => "0", "name" => "未分类");

        $this->form_validation->set_rules('name', '姓名', 'required');

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('teachers/create_teacher', $data);
            $this->load->view('templates/footer');
        } else {
            $post = $this->input->post();
            $update_arr = array();
            $except_arr = array("submit", "identity_number");
            foreach ($post as $x_key => $x_value):
                if (!in_array($x_key, $except_arr)) {
                    $update_arr[$x_key] = $x_value;
                }
            endforeach;
            $update_arr["identity_number"] = str_replace("x", "X", $this->input->post('identity_number'));
            $update_arr["createtime"] = date("Y-m-d H:i:s");
            $this->all_model->general_insert("xz_teachers", $update_arr);
            redirect('user/list_custom_teachers/0');
        }
    }


//修改教师密码
    public function teacher_pwd($result = NULL)
    {
        $data = $this->general_data;
        $data['name'] = $data['teacher_item']['name'];
        $data['result'] = $result;

        $this->form_validation->set_rules('teacher_id', 'id', 'required');

        //var_dump($this->form_validation->run());
        if ($this->form_validation->run() === FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('teachers/teacher_pwd', $data);
            $this->load->view('templates/footer');
        } else {
            if ($this->all_model->update_teacher_pwd() == "error") {
                redirect('user/teacher_pwd/fail');
            } else {
                redirect('user/teacher_pwd/success');
            }
        }
    }
//获取网页标题
    public function url_title()
    {
        $data = $this->general_data;
        $url=$this->input->post('url');
        $this->load->library('curl');
        $html=Curl::get($url);
        $reTag = "/<title>([\s\S]*?)<\/title>/i";
        preg_match($reTag, $html, $match);
        $title=$match[1];
        echo $title;
        //$arr = Curl::post('http://localhost:9090/test.php', array('a'=>1,'b'=>2));
    }

//管理目录
    public function manage_folder()
    {
        $data = $this->general_data;
        $data['select_folder'] = $this->all_model->general_select("xz_folder", "id,folder_name", array("teacher_id" => $_SESSION['teacher_id'], "father_id" => -1));
        $data['folder'] = $this->all_model->general_list("xz_folder", array("teacher_id" => $_SESSION['teacher_id'], "father_id" => -1), array("convert(folder_name using gbk)" => "asc"));
        for ($i = 0; $i < count($data['folder']); $i++) {
            $data['folder'][$i]["subfolder"] = $this->all_model->general_list("xz_folder", array("teacher_id" => $_SESSION['teacher_id'], "father_id" => $data['folder'][$i]['id']), array("convert(folder_name using gbk)" => "asc"));
        }
        $data['title'] = '管理目录';
        $this->form_validation->set_rules('submit', 'submit', 'required');

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('teachers/manage_folder', $data);
            $this->load->view('templates/footer');
        } else {
            $post = $this->input->post();
            $update_arr = array();
            $except_arr = array("id", "submit");
            foreach ($post as $x_key => $x_value):
                if (!in_array($x_key, $except_arr)) {
                    $update_arr[$x_key] = $x_value;
                }
            endforeach;
            $update_arr["teacher_id"] = $_SESSION['teacher_id'];
            if ($this->input->post('submit') == "add_folder") {   //添加一级目录
                if ($this->all_model->general_get_amount('xz_folder', array("teacher_id"=>$_SESSION['teacher_id'],"folder_name"=>$this->input->post('folder_name')))>0){
                    $_SESSION['err_msg'] = err_msg("已存在同名目录！");
                }
                else{
                    $update_arr["father_id"] = -1;
                    $this->all_model->general_insert('xz_folder', $update_arr);
                }
            } elseif ($this->input->post('submit') == "add_subfolder") {     //添加二级目录
                if ($this->all_model->general_get_amount('xz_folder', array("teacher_id"=>$_SESSION['teacher_id'],"father_id"=>$this->input->post('father_id'),"folder_name"=>$this->input->post('folder_name')))>0){
                    $_SESSION['err_msg'] = err_msg("已存在同名目录！");
                }
                else{
                    $this->all_model->general_insert('xz_folder', $update_arr);
                }
            } elseif ($this->input->post('submit') == "delete_subfolder") {     //删除二级目录
                $this->all_model->general_delete('xz_bookmark', array("folder_id" => $this->input->post('id'),"teacher_id"=>$_SESSION['teacher_id']));
                $this->all_model->general_delete('xz_folder', array("id" => $this->input->post('id'),"teacher_id"=>$_SESSION['teacher_id']));
            } elseif ($this->input->post('submit') == "delete_folder") {       //删除一级目录
                //先要遍历子目录的id再删
                $sub_folder=$this->all_model->general_list("xz_folder", array("teacher_id" => $_SESSION['teacher_id'], "father_id" => $this->input->post('id')));
                foreach($sub_folder as $item){
                    $this->all_model->general_delete('xz_bookmark', array("folder_id" => $item['id'],"teacher_id"=>$_SESSION['teacher_id']));
                    $this->all_model->general_delete('xz_folder', array("id" => $item['id'],"teacher_id"=>$_SESSION['teacher_id']));
                }
                $this->all_model->general_delete('xz_folder', array("id" => $this->input->post('id'),"teacher_id"=>$_SESSION['teacher_id']));
            } elseif ($this->input->post('submit') == "update_subfolder") {              //修改二级目录
                if ($this->all_model->general_get_amount('xz_folder', array("teacher_id"=>$_SESSION['teacher_id'],"father_id"=>$this->input->post('father_id'),"folder_name"=>$this->input->post('folder_name')))>0){
                    $_SESSION['err_msg'] = err_msg("已存在同名目录！");
                }
                else {
                    $this->all_model->general_update("xz_folder", $update_arr, array("id" => $this->input->post('id'), "teacher_id" => $_SESSION['teacher_id']));
                }
            } else {     //修改一级目录
                if ($this->all_model->general_get_amount('xz_folder', array("teacher_id"=>$_SESSION['teacher_id'],"father_id"=>-1,"folder_name"=>$this->input->post('folder_name')))>0){
                    $_SESSION['err_msg'] = err_msg("已存在同名目录！");
                }
                else {
                    $this->all_model->general_update("xz_folder", $update_arr, array("id" => $this->input->post('id'), "teacher_id" => $_SESSION['teacher_id']));
                }
            }
            redirect('user/manage_folder/');
        }
    }

//导入浏览器收藏夹
    public function import()
    {
        $this->load->library("phpexcel");

        $data['title'] = '导入浏览器收藏夹';
        $data['folder'] = $this->all_model->general_list("xz_folder", array("teacher_id" => $_SESSION['teacher_id'], "father_id" => -1));
        $this->form_validation->set_rules('json_string', '文件', 'required');
        $data['output']=array();
        if ($_POST["submit"] != "submit") {
            $this->load->view('templates/header', $data);
            $this->load->view('teachers/import', $data);
            $this->load->view('templates/footer');
        } else {
            $arr = json_decode($this->input->post('json_string'), 1);
            //var_dump($arr);
            $root_name = array_key_first($arr);
            $data['output'][]="根名：" . $root_name;
            for ($i = 0; $i < count($arr[$root_name]); $i++) {
                if (is_bookmark_folder($arr[$root_name][$i])) {
                    $root_name1 = array_key_first($arr[$root_name][$i]);
                    //echo $root_name1 . "<br>";
                    $tmp1=$this->all_model->general_select("xz_folder", "id", array("folder_name"=>$root_name1,"teacher_id"=>$_SESSION['teacher_id']));
                    if ($tmp1==null){
                        $folder_id1=$this->all_model->general_insert('xz_folder', array("folder_name"=>$root_name1,"teacher_id"=>$_SESSION['teacher_id']));
                        $data['output'][]="目录[".$root_name1."]插入成功";
                    }
                    else{
                        $folder_id1=$tmp1[0]['id'];
                        $data['output'][]="目录[".$root_name1."]已存在";
                    }
                    for ($j = 0; $j < count($arr[$root_name][$i][$root_name1]); $j++) {
                        if (is_bookmark_folder($arr[$root_name][$i][$root_name1][$j])) {
                            $root_name2 = array_key_first($arr[$root_name][$i][$root_name1][$j]);
                            //echo $root_name2 . "<br>";
                            $tmp2=$this->all_model->general_select("xz_folder", "id", array("folder_name"=>$root_name2,"teacher_id"=>$_SESSION['teacher_id'],"father_id"=>$folder_id1));
                            if ($tmp2==null){
                                $folder_id2=$this->all_model->general_insert('xz_folder', array("folder_name"=>$root_name2,"teacher_id"=>$_SESSION['teacher_id'],"father_id"=>$folder_id1));
                                $data['output'][]="目录[".$root_name2."]插入成功";
                            }
                            else{
                                $folder_id2=$tmp2[0]['id'];
                                $data['output'][]="目录[".$root_name2."]已存在";
                            }
                            for ($k = 0; $k < count($arr[$root_name][$i][$root_name1][$j][$root_name2]); $k++) {
                                if (is_bookmark_folder($arr[$root_name][$i][$root_name1][$j][$root_name2][$k])) {
                                    //以下是三级目录，都放在根目录下，超过三级的放弃导入
                                    $root_name3=array_key_first($arr[$root_name][$i][$root_name1][$j][$root_name2][$k]);
                                    for ($l = 0; $l < count($arr[$root_name][$i][$root_name1][$j][$root_name2][$k][$root_name3]); $l++) {
                                            if ($this->all_model->general_get_amount("xz_bookmark", array("url"=>$arr[$root_name][$i][$root_name1][$j][$root_name2][$k][$root_name3][$l]["href"],"teacher_id"=>$_SESSION['teacher_id']))==0){
                                                $insert_arr = array();
                                                $insert_arr['teacher_id']=$_SESSION['teacher_id'];
                                                $insert_arr["createtime"] = date("Y-m-d H:i:s");
                                                $insert_arr["title"] = $arr[$root_name][$i][$root_name1][$j][$root_name2][$k][$root_name3][$l]["name"] ;
                                                $insert_arr["url"] = $arr[$root_name][$i][$root_name1][$j][$root_name2][$k][$root_name3][$l]["href"];
                                                $insert_arr["icon"] = $arr[$root_name][$i][$root_name1][$j][$root_name2][$k][$root_name3][$l]["icon"];
                                                $insert_arr["icon_uri"] =$arr[$root_name][$i][$root_name1][$j][$root_name2][$k][$root_name3][$l]["icon"];
                                                $insert_arr["folder_id"] =0;
                                                $this->all_model->general_insert('xz_bookmark', $insert_arr);
                                                $data['output'][]= $arr[$root_name][$i][$root_name1][$j][$root_name2][$k][$root_name3][$l]["name"] ."导入成功";
                                            }
                                            else{
                                                $data['output'][]= $arr[$root_name][$i][$root_name1][$j][$root_name2][$k][$root_name3][$l]["name"] ."跳过";
                                            }
                                    }
                                } else {
                                    if ($this->all_model->general_get_amount("xz_bookmark", array("url"=>$arr[$root_name][$i][$root_name1][$j][$root_name2][$k]["href"],"teacher_id"=>$_SESSION['teacher_id']))==0){
                                        $insert_arr = array();
                                        $insert_arr['teacher_id']=$_SESSION['teacher_id'];
                                        $insert_arr["createtime"] = date("Y-m-d H:i:s");
                                        $insert_arr["title"] = $arr[$root_name][$i][$root_name1][$j][$root_name2][$k]["name"] ;
                                        $insert_arr["url"] = $arr[$root_name][$i][$root_name1][$j][$root_name2][$k]["href"];
                                        $insert_arr["icon"] = $arr[$root_name][$i][$root_name1][$j][$root_name2][$k]["icon"];
                                        $insert_arr["icon_uri"] =$arr[$root_name][$i][$root_name1][$j][$root_name2][$k]["icon"];
                                        $insert_arr["folder_id"] =$folder_id2;
                                        $this->all_model->general_insert('xz_bookmark', $insert_arr);
                                        $data['output'][]= $arr[$root_name][$i][$root_name1][$j][$root_name2][$k]["name"] ."导入成功";
                                    }
                                    else{
                                        $data['output'][]= $arr[$root_name][$i][$root_name1][$j][$root_name2][$k]["name"] ."跳过";
                                    }
                                }
                            }
                        } else {
                            if ($this->all_model->general_get_amount("xz_bookmark", array("url"=>$arr[$root_name][$i][$root_name1][$j]["href"],"teacher_id"=>$_SESSION['teacher_id']))==0){
                                $insert_arr = array();
                                $insert_arr['teacher_id']=$_SESSION['teacher_id'];
                                $insert_arr["createtime"] = date("Y-m-d H:i:s");
                                $insert_arr["title"] = $arr[$root_name][$i][$root_name1][$j]["name"] ;
                                $insert_arr["url"] = $arr[$root_name][$i][$root_name1][$j]["href"] ;
                                $insert_arr["icon"] = $arr[$root_name][$i][$root_name1][$j]["icon"];
                                $insert_arr["icon_uri"] =$arr[$root_name][$i][$root_name1][$j]["icon"];
                                $insert_arr["folder_id"] =$folder_id1;
                                $this->all_model->general_insert('xz_bookmark', $insert_arr);
                                $data['output'][]= $arr[$root_name][$i][$root_name1][$j]["name"] ."导入成功";
                            }
                            else{
                                $data['output'][]= $arr[$root_name][$i][$root_name1][$j]["name"] ."跳过";
                            }
                        }
                    }
                } else {
                    if ($this->all_model->general_get_amount("xz_bookmark", array("url"=>$arr[$root_name][$i]["href"],"teacher_id"=>$_SESSION['teacher_id']))==0){
                        $insert_arr = array();
                        $insert_arr['teacher_id']=$_SESSION['teacher_id'];
                        $insert_arr["createtime"] = date("Y-m-d H:i:s");
                        $insert_arr["title"] = $arr[$root_name][$i]["name"] ;
                        $insert_arr["url"] = $arr[$root_name][$i]["href"] ;
                        $insert_arr["icon"] = $arr[$root_name][$i]["icon"] ;
                        $insert_arr["icon_uri"] = $arr[$root_name][$i]["icon_uri"] ;
                        $this->all_model->general_insert('xz_bookmark', $insert_arr);
                        $data['output'][]= $arr[$root_name][$i]["name"]."导入成功";
                    }
                    else{
                        $data['output'][]= $arr[$root_name][$i]["name"]."跳过";
                    }
                }
            }
            $this->load->view('templates/header', $data);
            $this->load->view('teachers/import', $data);
            $this->load->view('templates/footer');
        }
    }
    ////////////////////
}