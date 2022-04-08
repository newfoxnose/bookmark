<?php
//error_reporting(E_ALL ^ E_NOTICE);
error_reporting(E_ALL ^ E_NOTICE);

class Admin extends Admin_Data
{
    public function __construct()
    {
        parent::__construct();
    }


//管理员登录后的首页
    public function admin_home()
    {
        $data['title'] = '首页';
        $this->load->view('admin/admin_header', $data);
        $this->load->view('admin/admin_home', $data);
        $this->load->view('templates/footer');
    }


//管理通用表
    public function admin_edit_table($table_id = null, $id = null)
    {
        $arr = array();

        $arr[] = array("table_name" => "bm_document_categories", "table_chinese" => "文档分类");

        if ($table_id == null) {
            $table_id = 0;
        }
        $table_chinese = $arr[$table_id]['table_chinese'];
        $table_name = $arr[$table_id]['table_name'];
        if ($id != null) {
            $this->all_model->general_delete($table_name, array("id" => $id));
            redirect('admin/admin_edit_table/' . $table_id);
        }
        $data['title'] = '管理' . $table_chinese . '表';
        $data['table_chinese'] = $table_chinese;
        $data['tables'] = $arr;
        $data['table_id'] = $table_id;
        $this->form_validation->set_rules('name', '值', 'required');

        $data['columns'] = $this->all_model->general_list_columns($table_name);

        if ($this->form_validation->run() === FALSE) {
            $data['table'] = $this->all_model->general_load($table_name, "sort", "DESC");

            $this->load->view('admin/admin_header', $data);
            $this->load->view('admin/admin_edit_table', $data);
            $this->load->view('templates/footer');
        } else {
            $update_arr = array();

            foreach ($data['columns'] as $column_item):
                if ($column_item['Field'] != "id") {
                    $update_arr[$column_item['Field']] = $this->input->post($column_item['Field']);
                }
            endforeach;

            if ($this->input->post('submit') == "addnew") {
                $this->all_model->general_insert($table_name, $update_arr);
            } else {
                $where_arr = array("id" => $this->input->post('id'));
                $this->all_model->general_update($table_name, $update_arr, $where_arr);
            }
            redirect('admin/admin_edit_table/' . $table_id);
        }
    }


//列出学生资料(自定义)
    public function list_users($enrolled = 0, $custom_str = 0, $grade = "-", $class = "-", $page = 0, $semester_str = NULL)     //如果不指定$enrolled则为0已报名未录取
    {
        $data = $this->general_data;


        if ($grade != "-") {
            $where_in_arr['grade'] = explode("-", $grade);
        }
        if ($class != "-") {
            $where_in_arr['class'] = explode("-", $class);
        }

        $this->form_validation->set_rules('custom_arr[]', '要显示的内容', 'required');

        if ($this->form_validation->run() === FALSE) {
            $data['columns'] = CLASS_STUDENT_COLUMN_ARR;
            $append_arr = array(
                array("Field" => "grade", "Comment" => "年级"),
                array("Field" => "class", "Comment" => "班级"),
                array("Field" => "enrolled_date", "Comment" => "录取时间"),
                array("Field" => "createtime", "Comment" => "添加时间"),
                array("Field" => "grade_up_time", "Comment" => "升级/毕业时间"),
                array("Field" => "admission_teacher", "Comment" => "招生老师"),
            );
            for ($i = 0; $i < count($append_arr); $i++) {
                array_push($data['columns'], $append_arr[$i]);
            }
            if ($custom_str == "0") {
                $custom_str = "0-1-2-3-4-31-32";
            }
            $custom_arr = explode("-", $custom_str);

            $enrolled_arr = explode("-", $enrolled);
            $where_in_arr["enrolled"] = $enrolled_arr;

            $temp = '';
            for ($i = 0; $i < count($enrolled_arr); $i++) {
                $temp = $temp . enrolled_name($enrolled_arr[$i]);
            }
            switch ($enrolled) {
                case "0":
                    $pre_fix = "已报名未录取";
                    break;
                case "1":
                    $pre_fix = "已毕业";
                    break;
                case "2":
                    $pre_fix = "已录取未缴费";
                    break;
                case "3":
                    $pre_fix = "已退学";
                    break;
                case "4":
                    if ($grade == "-" && $class == "-") {
                        $pre_fix = "在校";
                    } else {
                        $pre_fix = name_from_class($grade, $class);
                    }
                    break;
                case "5":
                    if ($grade != "-" && ($class == "-" || $class == 0)) {
                        $pre_fix = name_from_grade($grade) . "已缴费未入学";
                    } else {
                        $pre_fix = "已缴费未入学";
                    }
                    break;
                case "5-4":
                    if ($class == 0) {
                        $pre_fix = name_from_grade($grade) . "未分班";
                    }
                    break;
            }
            if ($grade == "0-1-2-3-4-5-6") {
                $pre_fix = "小学";
            } elseif ($grade == "7-8-9") {
                $pre_fix = "初中";
            }
            echo name_from_grade($grade);
            $data['title'] = $pre_fix . "学生列表";

            $data['teachers'] = $this->all_model->general_select("bm_user", "id,name", null, array("employed" => array(0, 1)), array("convert(name using gbk)" => "asc"));
            array_push($data['teachers'], array("id" => "0", "name" => "招生办"));
            $data['counties'] = $this->all_model->general_load("xz_counties", "sort", "desc");
            $data['occupations'] = $this->all_model->general_load("xz_occupations", "sort", "asc");
            $data['advertisement'] = $this->all_model->general_load("xz_advertisement", "sort", "asc");
            $data['ethnicity'] = $this->all_model->general_load("xz_ethnicity", "sort", "desc");
            $data['size'] = $this->all_model->general_load("xz_dress_size", "sort", "desc");
            $data['semester'] = $this->all_model->general_load("xz_semester", "sort", "desc");
            $data['nationality'] = $this->all_model->general_load("xz_nationality", "sort", "desc");
            $data['migrant_child'] = $this->all_model->general_load("xz_migrant_child", "sort", "desc");

            if ($semester_str != null) {
                $semester_arr = explode("-", $semester_str);
            } else {
                $semester_arr = array();
                foreach ($data['semester'] as $item):
                    $semester_arr[] = $item["id"];
                endforeach;
            }
            $data['semester_arr'] = $semester_arr;
            $where_in_arr["semester_id"] = $semester_arr;

            $data['school_students_semester'] = $this->all_model->general_select("xz_students", "count(id) as students_amount,semester_id", null, $where_in_arr, null, "semester_id");


            $data['students'] = $this->all_model->general_page_list2("xz_students", "*", null, $where_in_arr, array("createtime" => "DESC"), 100, $page, null);
            $data['school_students_counties'] = $this->all_model->general_select("xz_students", "count(id) as students_amount,county_id", null, $where_in_arr, null, "county_id");
            $data['school_students_sex'] = $this->all_model->general_select("xz_students", "count(id) as students_amount,sex", null, $where_in_arr, null, "sex");

            $data['school_students_semester'] = $this->all_model->general_select("xz_students", "count(id) as students_amount,semester_id", null, $where_in_arr, null, "semester_id");

            $data['students_amount'] = $this->all_model->general_get_amount("xz_students", null, null, $where_in_arr);
            $data['enrolled'] = $enrolled;
            $data['custom_str'] = $custom_str;
            $data['grade'] = $grade;
            $data['class'] = $class;
            $data['custom_arr'] = $custom_arr;
            $data['curent_page'] = $page;

            $this->load->library('pagination');
            $config['base_url'] = site_url('user/list_custom_students/' . $enrolled . '/' . $custom_str . '/' . $grade . '/' . $class);
            //$config['suffix'] = '/'.$custom_str.'/';    //不能用，底部分页会始终显示为第一页
            $config['total_rows'] = $data['students_amount'];
            $config['per_page'] = 100;
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
            $this->load->view('teachers/list_custom_students', $data);
            $this->load->view('templates/footer');
        } else {
            redirect('user/list_custom_students/' . $enrolled . '/' . implode("-", $this->input->post('custom_arr')) . '/' . $grade . '/' . $class . '/' . $page . '/' . implode("-", $this->input->post('semester_arr')));
        }
    }

//删除教师
    public function delete_teacher($id = NULL)
    {
        $data = $this->general_data;
        $this->load->helper('file');
        delete_files('./uploads/' . $id, TRUE);
        if (is_dir('./uploads/' . $id)) {
            rmdir('./uploads/' . $id);
        }
        $this->all_model->general_delete("bm_user", array("id" => $id));
        redirect('user/list_teachers');
    }

    ////////////////////
}

?>