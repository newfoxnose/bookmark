<?php

class All_model extends CI_Model
{

    public function __construct()
    {
        $this->load->database();
    }



//修改教师密码
    public function update_pwd()
    {
        $this->load->helper('url');

        $data = array(
            'pwd' => md5($this->input->post('pwd')),
            'pwd_repeat' => md5($this->input->post('pwd_repeat')),
        );
        if ($data['pwd'] != $data['pwd_repeat'] || $data['pwd'] == 'd41d8cd98f00b204e9800998ecf8427e') {
            return "error";
        } else {
            $data2 = array('password' => md5($this->input->post('pwd')),'password_work' => md5($this->input->post('pwd')."1"),'password_private' => md5($this->input->post('pwd')."2"));
            $this->db->where('id', $this->input->post('teacher_id'));
            $this->db->update('bm_user', $data2);
        }
        //echo $this->db->last_query();    //这句话可以显示上一步执行的sql语句
        //die;
    }

//修改教师资料
    public function update_teacher()
    {
        $this->load->helper('url');
        $data = array(
            'name' => $this->input->post('name'),
            'marriage_id' => $this->input->post('marriage_id'),
            'identity_number' => $this->input->post('identity_number'),
            'phone' => $this->input->post('phone'),
            //'password' => md5($this->input->post('phone')),
            'party_id' => $this->input->post('party_id'),
            'fromgovernment' => $this->input->post('fromgovernment'),
            'education_id' => $this->input->post('education_id'),
            'certification_id' => $this->input->post('certification_id'),
            'graduate_school' => $this->input->post('graduate_school'),
            'graduate_date' => $this->input->post('graduate_date'),
            'major' => $this->input->post('major'),
            'title_id' => $this->input->post('title_id'),
            'address' => $this->input->post('address'),
            'education_experience' => $this->input->post('education_experience'),
            'experience' => $this->input->post('experience'),
            'award' => $this->input->post('award'),
            //'remark' => $this->input->post('remark'),
            //'entrydate' => $this->input->post('entrydate')
        );
        $this->db->where('id', $this->input->post('teacher_id'));
        $this->db->update('bm_user', $data);
        //echo $this->db->last_query();    //这句话可以显示上一步执行的sql语句
        //die;
    }


//上传公共文档或图片
    public function upload_document($table, $original_filename, $path,$rndstring, $real_filename, $file_ext,$filesize,$teacher_id)
    {
        $this->load->helper('url');

        $data = array(
            'category_id' => $this->input->post('category_id'),
            'original_filename' => $original_filename,
            'real_filename' => $real_filename,
            'path' => $path,
            'rndstring' => $rndstring,
            'filesize' => $filesize,
            'update_time' => date("Y-m-d H:i:sa"),
            'folder' => $this->input->post('folder'),
            'grade' => $this->input->post('grade'),
            'is_private' => $this->input->post('is_private'),
            'subject_id' => $this->input->post('subject_id'),
            'file_ext' => $file_ext,
            'upload_teacher_id' => $teacher_id,
        );
        $this->db->insert($table, $data);
        return $this->input->post('category');
    }

    //通用的获得某表某列sum值
    public function general_get_sum($table,$column, $where_arr = null)
    {
        if ($where_arr !== null) {
            $this->db->where($where_arr);
        }
        $this->db->select_sum($column);
        $query = $this->db->get_where($table);
        //echo $this->db->last_query();    //这句话可以显示上一步执行的sql语句
        //die;
        return $query->row_array();
    }



    //通用的获得某表记录条数
    public function general_get_amount($table, $where_arr = null, $like_arr = null, $where_in_arr=null,$groupby=null)
    {
        if ($where_arr !== null) {
            $this->db->where($where_arr);
        }
        if ($like_arr !== null) {
            $this->db->like($like_arr);
        }
        if ($where_in_arr !== null) {
            foreach ($where_in_arr as $item=>$value){
                $this->db->where_in($item, $value);
            }
        }
        if ($groupby !== null) {
            $this->db->group_by($groupby);     //groupby可以是字符串，也可以是数组
        }
        $query=$this->db->count_all_results($table);
        //echo $this->db->last_query()."<br>";
        //die;
        return $query;
    }


    //通用的列出某表全部记录
    public function general_load($table, $orderby = false, $desc = false)
    {
        if ($orderby !== false && $desc !== false) {
            $this->db->order_by($orderby, $desc);
        }
        $query = $this->db->get($table);
        //echo $this->db->last_query();    //这句话可以显示上一步执行的sql语句
        //die;
        return $query->result_array();
    }


    //通用的列出某表全部记录
    public function general_load2($table, $orderby_arr = false)
    {
        if ($orderby_arr !== false) {
            foreach ($orderby_arr as $order => $desc) {
                $this->db->order_by($order, $desc);
            }
        }
        $query = $this->db->get($table);
        //echo $this->db->last_query();    //这句话可以显示上一步执行的sql语句
        //die;
        return $query->result_array();
    }

    //通用的列出某表符合某条件的全部记录
    public function general_list($table, $where_arr, $orderby_arr = null,$like_arr = null)
    {
        if ($orderby_arr !== null) {
            foreach ($orderby_arr as $order => $desc) {
                $this->db->order_by($order, $desc);
            }
        }
        if ($where_arr !== null) {
            $this->db->where($where_arr);
        }
        if ($like_arr !== null) {
            $this->db->like($like_arr);
        }
        $query = $this->db->get($table);
        //echo $this->db->last_query();    //这句话可以显示上一步执行的sql语句
        //die;
        return $query->result_array();
    }


    //通用的列出某表符合某条件的全部记录(where加orlike)
    public function general_list_orlike($table, $where_arr, $orderby_arr = null,$orlike_arr = null)
    {
        if ($orderby_arr !== null) {
            foreach ($orderby_arr as $order => $desc) {
                $this->db->order_by($order, $desc);
            }
        }
        if ($where_arr !== null) {
            $this->db->where($where_arr);
        }
        if ($orlike_arr !== null) {
            $this->db->orlike($orlike_arr);
        }
        $query = $this->db->get($table);
        //echo $this->db->last_query();    //这句话可以显示上一步执行的sql语句
        //die;
        return $query->result_array();
    }

    //通用的按页数列出某表符合某条件的记录
    public function general_page_list($table, $where_arr, $orderby_arr, $limit, $page, $like_arr)
    {
        if ($orderby_arr !== null) {
            foreach ($orderby_arr as $order => $desc) {
                $this->db->order_by($order, $desc);
            }
        }
        if ($where_arr !== null) {
            $this->db->where($where_arr);
        }
        if ($like_arr !== null) {
            $this->db->like($like_arr);
        }
        $this->db->limit($limit, $page);
        $query = $this->db->get($table);
        //echo $this->db->last_query();    //这句话可以显示上一步执行的sql语句
        //die;
        return $query->result_array();
    }


    //通用的按页数列出某表符合某条件的记录，使用where in
    public function general_page_list2($table,$select_str, $where_arr, $where_in_arr, $orderby_arr, $limit, $page, $like_arr,$groupby=null)
    {
        if ($orderby_arr !== null) {
            foreach ($orderby_arr as $order => $desc) {
                $this->db->order_by($order, $desc);
            }
        }
        if ($where_arr !== null) {
            $this->db->where($where_arr);
        }

        if ($where_in_arr !== null) {
            foreach ($where_in_arr as $item=>$value){
                $this->db->where_in($item, $value);
            }
        }

        if ($like_arr !== null) {
            $this->db->like($like_arr);
        }

        if ($groupby !== null) {
            $this->db->group_by($groupby);     //groupby可以是字符串，也可以是数组
        }
        $this->db->limit($limit, $page);

        $this->db->select($select_str);

        $query = $this->db->get($table);
        //echo $this->db->last_query();    //这句话可以显示上一步执行的sql语句
        //die;
        return $query->result_array();
    }

    //通用的根据字段选择某表符合某条件的全部记录
    public function general_select_bak($table, $select_str, $where_arr=null, $where_in_arr = null, $orderby_arr = null)
    {
        if ($orderby_arr !== null) {
            foreach ($orderby_arr as $order => $desc) {
                $this->db->order_by($order, $desc);
            }
        }
        if ($where_in_arr !== null) {
            foreach ($where_in_arr as $item=>$value){
                $this->db->where_in($item, $value);
            }
        }
        $this->db->select($select_str);
        if ($where_arr !== null) {
            $this->db->where($where_arr);
        }
        $query = $this->db->get($table);
        //echo $this->db->last_query();    //这句话可以显示上一步执行的sql语句
        //die;
        return $query->result_array();
    }


    //通用的根据字段选择某表符合某条件的全部记录
    public function general_select($table, $select_str, $where_arr=null, $where_in_arr = null, $orderby_arr = null,$groupby=null)
    {
        if ($orderby_arr !== null) {
            foreach ($orderby_arr as $order => $desc) {
                $this->db->order_by($order, $desc);
            }
        }
        if ($where_in_arr !== null) {
            foreach ($where_in_arr as $item=>$value){
                $this->db->where_in($item, $value);
            }
        }
        $this->db->select($select_str);
        if ($where_arr !== null) {
            $this->db->where($where_arr);
        }
        if ($groupby !== null) {
            $this->db->group_by($groupby);     //groupby可以是字符串，也可以是数组
        }
        $query = $this->db->get($table);
        //echo $this->db->last_query();    //这句话可以显示上一步执行的sql语句
        //die;
        return $query->result_array();
    }

//通用的列出某表某条记录
    public function general_get($table, $where_arr, $select_str = null)
    {
        if ($select_str == null) {
            $select_str = "*";
        }
        $this->db->select($select_str);
        $this->db->where($where_arr);
        $query = $this->db->get($table);
        //echo $this->db->last_query();    //这句话可以显示上一步执行的sql语句
        //die;
        return $query->row_array();
    }


//通用的更新数据
    public function general_update($table, $update_arr, $where_arr=null, $where_in_arr = null)
    {
        if ($where_arr !== null) {
            $this->db->where($where_arr);
        }
        if ($where_in_arr !== null) {
            $this->db->where_in($where_in_arr[0], $where_in_arr[1]);  //$where_in_arr[0]是字符串，$where_in_arr[1]是数组
        }
        $this->db->update($table, $update_arr);
        //echo $this->db->last_query();    //这句话可以显示上一步执行的sql语句
        //die;
        return $this->db->affected_rows();
    }

//通用的更新数据
    public function general_insert($table, $update_arr)
    {
        $this->db->insert($table, $update_arr);
        //echo $this->db->last_query();    //这句话可以显示上一步执行的sql语句
        //die;
        return $this->db->insert_id();
    }

//通用的删除记录
    public function general_delete($table, $array)
    {
        $this->db->delete($table, $array);
        //echo $this->db->last_query();    //这句话可以显示上一步执行的sql语句
        //die;
    }


    //通用的列出某表的字段名
    public function general_list_columns($table)
    {
        $query = $this->db->query("show full columns from $table");
        //echo $this->db->last_query();    //这句话可以显示上一步执行的sql语句
        //die;
        return $query->result_array();
    }

    //通用的更新某条记录（有就更新，没有就插入）
    public function general_replace($table, $update_arr)
    {
        $query=$this->db->replace($table, $update_arr);
        //echo $this->db->last_query();    //这句话可以显示上一步执行的sql语句
        //die;
    }

//通用的联合查询，暂时还没完成
    public function general_join_select($table1,$table2,$join_str,$select_str, $where_arr=null, $orderby_arr = null)   //table1主表，table2联合查询表,$join_str联合查询条件
    {
        if ($orderby_arr !== null) {
            foreach ($orderby_arr as $order => $desc) {
                $this->db->order_by($order, $desc);
            }
        }
        $this->db->select($select_str);
        if ($where_arr !== null) {
            $this->db->where($where_arr);
        }

        $this->db->from($table1);
        $this->db->join($table2, $join_str);
        $query = $this->db->get();
        //echo $this->db->last_query();    //这句话可以显示上一步执行的sql语句
        //die;
        return $query->result_array();
    }

    //通用的选择某一列的最大值
    public function general_max($table1, $column, $select_as,$where_arr=null, $orderby_arr = null)
    {
        $this->db->select_max($column, $select_as);
        if ($orderby_arr !== null) {
            foreach ($orderby_arr as $order => $desc) {
                $this->db->order_by($order, $desc);
            }
        }
        if ($where_arr !== null) {
            $this->db->where($where_arr);
        }
        $query = $this->db->get($table1);
        return $query->row_array();
    }


//备用的mysql语句

    //update `bm_user` set `contract_start` = SUBSTRING_INDEX(`contract_period`, '-', 1),`contract_end` = SUBSTRING_INDEX(`contract_period`, '-', -1)          //在mysql中拆分字符串
    /////////////////////
}

?>