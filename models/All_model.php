<?php

class All_model extends CI_Model
{

    public function __construct()
    {
        $this->load->database();
    }



    //老师登录
    public function login()
    {
        $name = $this->input->post('name');
        $password = md5($this->input->post('password'));
        if ($name === FALSE || $this->input->post('password') === FALSE) {
            redirect('login/');
        }
        $sql = "select * from xz_teachers where employed=0 and `name`='$name' and `password`='$password'";
        $query = $this->db->query($sql);
        //echo $this->db->last_query();    //这句话可以显示上一步执行的sql语句
        //die;
        return $query->row_array();
    }


//修改教师密码
    public function update_teacher_pwd()
    {
        $this->load->helper('url');

        $data = array(
            'pwd' => md5($this->input->post('pwd')),
            'pwd_repeat' => md5($this->input->post('pwd_repeat')),
        );
        if ($data['pwd'] != $data['pwd_repeat'] || $data['pwd'] == 'd41d8cd98f00b204e9800998ecf8427e') {
            return "error";
        } else {
            $data2 = array('password' => md5($this->input->post('pwd')));
            $this->db->where('id', $this->input->post('teacher_id'));
            $this->db->update('xz_teachers', $data2);
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
        $this->db->update('xz_teachers', $data);
        //echo $this->db->last_query();    //这句话可以显示上一步执行的sql语句
        //die;
    }


//上传公共文档或图片
    public function upload_document($table, $original_filename, $path,$rndstring, $real_filename, $file_ext,$filesize)
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
            'upload_teacher_id' => $_SESSION['teacher_id'],
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

    //在校学生累计缴费情况
    public function student_payment_sum($grade,$class)
    {
        $sql="SELECT a.name, a.identity_number,a.grade,a.class, b.total_amount FROM xz_students a  LEFT JOIN (SELECT sum(amount) as total_amount,student_id FROM xz_payments group by student_id) b ON a.id = b.student_id where a.grade='$grade' and a.class='$class' and a.enrolled=4 order by convert(name using gbk)";

        $query = $this->db->query($sql);
        //echo $this->db->last_query();    //这句话可以显示上一步执行的sql语句
        //die;
        return $query->result_array();
    }



    //在校学生累计缴费情况，分页
    public function student_payment_sum_page($grade,$class, $page)
    {
        $sql="SELECT sum(a.amount) as total_amount,b.id,b.name,b.identity_number,b.parent_phone FROM xz_payments a, xz_students b WHERE a.student_id = b.id and a.paydate>='2020-6-1' and a.paydate<='2021-6-30' and b.enrolled='4' and b.grade='$grade' and b.class='$class' group by a.student_id limit 100*$page,100";
        $query = $this->db->query($sql);
        //echo $this->db->last_query();    //这句话可以显示上一步执行的sql语句
        //die;
        return $query->result_array();
    }


    //按学生升年级时间分组
    public function get_grade_up_time($grade)
    {
        $sql="select grade_up_time from xz_students where grade=$grade and enrolled in (4) group by grade_up_time";
        $query = $this->db->query($sql);
        //echo $this->db->last_query();    //这句话可以显示上一步执行的sql语句
        //die;
        //return $query->row_array();
        return $query->result_array();
    }

    //学生升年级，科任老师升年级
    public function grade_up()
    {
        $grade_up_time=date("Y-m-d H:i:s");
        $sql="update xz_students set enrolled=1,grade_up_time='$grade_up_time' where grade in (9) and enrolled in (4)";
        $this->db->query($sql);
        $sql="update xz_students set grade=grade+1,grade_up_time='$grade_up_time' where grade in (0,1,2,3,4,5,6,7,8) and enrolled in (4)";
        $this->db->query($sql);
        //$sql="update xz_class_teachers set grade=666, grade_up_time='$grade_up_time' where grade=6";
        //$this->db->query($sql);
        $sql="update xz_class_teachers set grade=999, grade_up_time='$grade_up_time' where grade=9";
        $this->db->query($sql);
        $sql="update xz_class_teachers set grade=grade+1,grade_up_time='$grade_up_time' where grade in (1,2,3,4,5,6,7,8)";
        $this->db->query($sql);
        //$sql="update xz_class_teachers set grade=1, grade_up_time='$grade_up_time' where grade=666";
        //$this->db->query($sql);
        $sql="update xz_class_teachers set grade=7, grade_up_time='$grade_up_time' where grade=999";
        $this->db->query($sql);
    }

    //在校学生或某班所属乡镇
    public function school_students_counties_bak($grade=NULL,$class=NULL)
    {
        //如果不提供年级和班级就返回全校学生数据
        if ($grade==NULL&&$class==NULL){
            $query = $this->db->query("select count(id) as students_amount,county_id from xz_students where enrolled in (4) group by county_id");
        }
        elseif($grade!=NULL&&$class==NULL){
            $query = $this->db->query("select count(id) as students_amount,county_id from xz_students where enrolled in (4) and grade='$grade' group by county_id");
        }
        else{
            $query = $this->db->query("select count(id) as students_amount,county_id from xz_students where enrolled in (4) and grade='$grade' and class='$class' group by county_id");
        }
        return $query->result_array();
    }




    //在校学生或某班所属乡镇
    public function school_students_counties($grade="-",$class="-")
    {
        //如果不提供年级和班级就返回全校学生数据
        if ($grade=="-"&&$class=="-"){
            $query = $this->db->query("select count(id) as students_amount,county_id from xz_students where enrolled in (4) group by county_id");
        }
        elseif($grade!="-"&&$class=="-"){
            $grade= str_replace("-",",",$grade);
            $query = $this->db->query("select count(id) as students_amount,county_id from xz_students where enrolled in (4) and grade in ($grade) group by county_id");
        }
        else{
            $grade= str_replace("-",",",$grade);
            $class= str_replace("-",",",$class);
            $query = $this->db->query("select count(id) as students_amount,county_id from xz_students where enrolled in (4) and grade in ($grade) and class in ($class) group by county_id");
        }
        return $query->result_array();
    }


    //在校学生所属年级
    public function school_students_grades($grade_str="-")
    {
        if ($grade_str=="-") {
            $query = $this->db->query("select count(id) as students_amount,grade from xz_students where enrolled in (4) group by grade");
        }
        else{
            $query = $this->db->query("select count(id) as students_amount,grade from xz_students where enrolled in (4) and grade in ($grade_str) group by grade");
        }
        return $query->result_array();
    }

    //在校学生性别分布
    public function school_students_sex($grade="-",$class="-")
    {
        //如果不提供年级和班级就返回全校学生数据
        if ($grade=="-"&&$class=="-"){
            $query = $this->db->query("select count(id) as students_amount,sex from xz_students where enrolled in (4) group by sex");
        }
        elseif($grade!="-"&&$class=="-"){
            $grade_arr= str_replace("-",",",$grade);
            $query = $this->db->query("select count(id) as students_amount,sex from xz_students where enrolled in (4) and grade in ($grade_arr)  group by sex");
        }
        else{
            $grade_arr= str_replace("-",",",$grade);
            $class_arr= str_replace("-",",",$class);
            $query = $this->db->query("select count(id) as students_amount,sex from xz_students where enrolled in (4) and grade in ($grade_arr) and class in ($class_arr)  group by sex");
        }
        return $query->result_array();
    }

//某时段内的缴费具体情况
    public function payment_detail($start_date,$end_date)
    {
        $this->db->where('paydate>=', $start_date);
        $this->db->where('paydate<=', $end_date);
        $this->db->from('xz_payments a');
        $this->db->join('xz_students b', 'a.student_id = b.id');
        $this->db->select("*,a.remark as remark1,b.remark as remark2");
        $query = $this->db->get();
        //echo $this->db->last_query();    //这句话可以显示上一步执行的sql语句
        //die;
        return $query->result_array();
    }

//某同学缴费总额
    public function get_payments_sum($id = FALSE)
    {
        if ($id === FALSE) {
            return NULL;
        }
        $this->db->select_sum("amount", "payments_sum");
        $query = $this->db->get_where('xz_payments', array('student_id' => $id));
        return $query->row_array('payments_sum');
    }


//今日付款总金额
    public function today_payments_sum($date = FALSE)
    {
        $this->db->select_sum("amount", "today_payments_sum");
        $query = $this->db->get_where('xz_payments', array('paydate' => $date));
        return $query->row_array('today_payments_sum');
    }


//某时段内付款总金额
    public function period_payments_sum($start_date,$end_date,$enrolled=5)      //$enrolled为缴费时的学生状态，2新录取未缴费,5已缴费未录取,4在校生
    {
        $this->db->select_sum("amount", "amount");
        $query = $this->db->get_where('xz_payments', array('paydate>=' => $start_date,'paydate<=' => $end_date,'enrolled'=>$enrolled));
        return $query->row_array('today_payments_sum');
        //echo $this->db->last_query();    //这句话可以显示上一步执行的sql语句
        //die;
    }

//某时段内付款总人数
    public function period_payer_sum($start_date,$end_date,$enrolled=5)    //$enrolled为缴费时的学生状态，2新录取未缴费,5已缴费未录取,4在校生
    {
        $query = $this->db->query("select count(*) from xz_payments where paydate>='$start_date' and paydate<='$end_date' and enrolled='$enrolled' group by student_id");
        return $query->num_rows();
    }

//某时段付款学生所属年级
    public function period_students_grades($start_date,$end_date,$enrolled=5)    //$enrolled为缴费时的学生状态，2新录取未缴费,5已缴费未入学,4在校生
    {
        $query = $this->db->query("select count(c.student_id) as students_amount,c.grade from (SELECT a.student_id, b.grade FROM xz_payments a, xz_students b WHERE a.student_id = b.id and a.paydate>='$start_date' and a.paydate<='$end_date' and a.enrolled='$enrolled' group by a.student_id) c group by c.grade");
        return $query->result_array();
    }
//某时段付款学生所属乡镇
    public function period_students_counties($start_date,$end_date,$enrolled=5)   //$enrolled为缴费时的学生状态，2新录取未缴费,5已缴费未入学,4在校生
    {
        $query = $this->db->query("select count(c.student_id) as students_amount,c.county_id from (SELECT a.student_id, b.county_id FROM xz_payments a, xz_students b WHERE a.student_id = b.id and a.paydate>='$start_date' and a.paydate<='$end_date' and a.enrolled='$enrolled' group by a.student_id) c group by c.county_id");
        return $query->result_array();
    }
//某时段付款学生性别
    public function period_students_sex($start_date,$end_date,$enrolled=5)     //$enrolled为缴费时的学生状态，2新录取未缴费,5已缴费未入学,4在校生
    {
        $sql = "select count(c.student_id) as students_amount,c.sex from (SELECT a.student_id, b.sex FROM xz_payments a, xz_students b WHERE a.student_id = b.id and a.paydate>='$start_date' and a.paydate<='$end_date' and a.enrolled='$enrolled' group by a.student_id) c group by c.sex";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

//某时段付款学生招生老师
    public function period_students_teachers($start_date,$end_date,$enrolled=5)    //$enrolled为缴费时的学生状态，2新录取未缴费,5已缴费未入学,4在校生
    {
        $sql = "select count(c.student_id) as students_amount,c.admission_teacher from (SELECT a.student_id, b.admission_teacher FROM xz_payments a, xz_students b WHERE a.student_id = b.id and a.paydate>='$start_date' and a.paydate<='$end_date' and a.enrolled='$enrolled' group by a.student_id) c group by c.admission_teacher";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

//某时段付款学生所属年级(按天分组)
    public function period_students_grades_daily($start_date,$end_date,$enrolled=5)
    {
        $query = $this->db->query("select count(c.student_id) as students_amount,c.grade,c.paydate from (SELECT a.student_id, b.grade,a.paydate FROM xz_payments a, xz_students b WHERE a.student_id = b.id and a.paydate>='$start_date' and a.paydate<='$end_date' and a.enrolled='$enrolled' ) c group by c.paydate,c.grade order by c.grade");
        return $query->result_array();
    }

//某时段付款学生性别（按天分组)
    public function period_students_sex_daily($start_date,$end_date,$enrolled=5)
    {
        $query = $this->db->query("select count(c.student_id) as students_amount,c.sex,c.paydate from (SELECT a.student_id, b.sex,a.paydate FROM xz_payments a, xz_students b WHERE a.student_id = b.id and a.paydate>='$start_date' and a.paydate<='$end_date' and a.enrolled='$enrolled') c group by c.paydate,c.sex");
        return $query->result_array();
    }


//某时段内每日付款金额（按天分组)
    public function period_payments_sum_daily($start_date,$end_date,$enrolled=5)
    {
        $query = $this->db->query("select sum(amount) as sum_amount,paydate from xz_payments WHERE paydate>='$start_date' and paydate<='$end_date' and enrolled='$enrolled' group by paydate");
        return $query->result_array();
    }


//某时段内每日缴费人数（按天分组)
    public function period_students_sum_daily($start_date,$end_date,$enrolled=5)
    {
        $query = $this->db->query("select count(student_id) as students_amount,paydate from xz_payments WHERE paydate>='$start_date' and paydate<='$end_date' and enrolled='$enrolled' group by paydate");
        return $query->result_array();
    }

//今日付款总人数
    public function today_payer_sum($date = FALSE)
    {
        $query = $this->db->query("select count(*) from xz_payments where paydate='$date' group by student_id");
        return $query->num_rows();
    }

//某年付款总金额
    public function total_payments_sum($date = FALSE)
    {
        $year = date('Y', strtotime($date));
        $query = $this->db->query("select sum(amount) as total_payments_sum from xz_payments where year(paydate)='$year'");
        return $query->row_array('total_payments_sum');
    }

//某年付款总人数
    public function total_payer_sum($date = FALSE)
    {
        $year = date('Y', strtotime($date));
        $query = $this->db->query("select count(*) from xz_payments where year(paydate)='$year' group by student_id");
        return $query->num_rows();
    }

//付款学生所属乡镇
    public function students_counties($date = FALSE)
    {
        $year = date('Y', strtotime($date));
        $query = $this->db->query("select count(c.student_id) as students_amount,c.county_id from (SELECT a.student_id, b.county_id FROM xz_payments a, xz_students b WHERE a.student_id = b.id and year(a.paydate)='$year' group by a.student_id) c group by c.county_id");
        return $query->result_array();
    }

//付款学生性别
    public function students_sex($date = FALSE)
    {
        $year = date('Y', strtotime($date));
        $sql = "select count(c.student_id) as students_amount,c.sex from (SELECT a.student_id, b.sex FROM xz_payments a, xz_students b WHERE a.student_id = b.id and year(a.paydate)='$year' group by a.student_id) c group by c.sex";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

//付款学生招生老师
    public function students_teachers($date = FALSE)
    {
        $year = date('Y', strtotime($date));
        $sql = "select count(c.student_id) as students_amount,c.teacher_id from (SELECT a.student_id, b.teacher_id FROM xz_payments a, xz_students b WHERE a.student_id = b.id and year(a.paydate)='$year' group by a.student_id) c group by c.teacher_id";
        $query = $this->db->query($sql);
        return $query->result_array();
    }


//查询学生信息后列出班级
    public function list_classes($grade_arr, $class_arr)
    {
        $arr = array();
        for ($i = 0; $i < count($grade_arr); $i++) {
            for ($j = 0; $j < count($class_arr); $j++) {
                $grade = $grade_arr[$i];
                $class = $class_arr[$j];
                $this->db->where(array("grade" => $grade, "class" => $class));
                $this->db->where_in("enrolled", array(5,4));
                if ($this->db->count_all_results("xz_students") > 0) {
                    $this->db->where(array("grade" => $grade, "class" => $class));
                    $headteacher_id=$this->all_model->general_select("xz_class_teachers", "teacher_id", array("grade" => $grade, "class" => $class, "subject_id" => -1));
                    array_push($arr, array("grade"=>$grade, "class"=>$class,"headteacher_id"=>$headteacher_id));
                }
                //echo $this->db->last_query()."<br>";    //这句话可以显示上一步执行的sql语句
            }
        }
        //die;
        return $arr;
    }

//返回每班的在校学生人数
    public function class_students_amount($grade_arr, $class_arr)
    {
        $arr = array();
        for ($i = 0; $i < count($grade_arr); $i++) {
            $tmp_arr = array();
            $grade = $grade_arr[$i];
            $amount_sum=0;
            $female_sum=0;
            for ($j = 0; $j < count($class_arr); $j++) {
                $class = $class_arr[$j];
                $this->db->where(array("grade" => $grade, "class" => $class));
                $this->db->where_in("enrolled", array(4));  //$where_in_arr[0]是字符串，$where_in_arr[1]是数组
                $amount=$this->db->count_all_results("xz_students");

                $this->db->where(array("grade" => $grade, "class" => $class, "sex" => 0));
                $this->db->where_in("enrolled", array(4));  //$where_in_arr[0]是字符串，$where_in_arr[1]是数组
                $female=$this->db->count_all_results("xz_students");
                if ( $amount> 0) {
                    array_push($tmp_arr, array("class"=>$class,"amount"=>$amount,"female"=>$female));
                    $amount_sum=$amount_sum+$amount;
                    $female_sum=$female_sum+$female;
                }
                else{
                    array_push($tmp_arr, array("class"=>$class,"amount"=>"","female"=>""));
                }
                //echo $this->db->last_query()."<br>";    //这句话可以显示上一步执行的sql语句
            }
            array_push($arr, array("grade"=>$grade, "class_arr"=>$tmp_arr,"amount_sum"=>$amount_sum,"female_sum"=>$female_sum));
            unset($tmp_arr);
        }
        //die;
        return $arr;
    }



//导入教师
    public function import_teacher($name, $idnumber, $phone)
    {
        $this->load->library("phpexcel");
        $this->load->helper('url');

        $data = array(
            'name' => $name,
            'identity_number ' => $idnumber,
            'password ' => md5($idnumber),
            'phone ' => $phone,
            'createtime' => date("Y-m-d H:i:sa")
        );
        return $this->db->insert('xz_teachers', $data);
    }

//导入学生
    public function import_student($name, $county_id, $identity_number, $sex, $student_code, $address, $parent_name, $parent_occupation_id, $parent_phone, $is_teacher_child, $is_teacher_relative, $is_left_behind, $previous_school, $branch, $grade, $class, $teacher_id, $is_temporary)
    {
        $this->load->helper('url');

        $data = array(
            'name' => $name,
            'county_id' => $county_id,
            'identity_number' => $identity_number,
            'sex' => $sex,
            'student_code' => $student_code,
            'address' => $address,
            'parent_name' => $parent_name,
            'parent_occupation_id' => $parent_occupation_id,
            'parent_phone' => $parent_phone,
            'is_teacher_child' => $is_teacher_child,
            'is_teacher_relative' => $is_teacher_relative,
            'is_left_behind' => $is_left_behind,
            'previous_school' => $previous_school,
            'branch' => $branch,
            'grade' => $grade,
            'class' => $class,
            'teacher_id' => $teacher_id,
            'is_temporary' => $is_temporary,
            'advertisement_id' => 0,
            'remark' => "",
            'createtime' => date("Y-m-d H:i:sa")
        );
        return $this->db->insert('xz_students', $data);
    }

//导入付款记录
    public function import_payments($amount, $paydate, $remark, $student_id, $note, $billnumber, $method, $receiver_id)
    {
        if ($remark == NULL) {
            $remark = "";
        }
        if ($method == NULL) {
            $method = "";
        }
        $this->load->library("phpexcel");
        $this->load->helper('url');

        $data = array(
            'amount' => $amount,
            'paydate' => $paydate,
            'remark' => $remark,
            'student_id' => $student_id,
            'note' => $note,
            'billnumber' => $billnumber,
            'method' => $method,
            'receiver_id' => $receiver_id,
            'createtime' => date("Y-m-d H:i:sa")
        );
        return $this->db->insert('xz_payments', $data);
    }

//导入190225考试成绩
    public function import_exam190225($name, $number, $grade, $class, $chinese, $math, $english, $physics, $total)
    {
        $this->load->helper('url');

        $data = array(
            'name' => $name,
            'number ' => $number,
            'grade ' => $grade,
            'class ' => $class,
            'chinese' => $chinese,
            'math' => $math,
            'english' => $english,
            'physics' => $physics,
            'total' => $total
        );
        return $this->db->insert('exam190225', $data);
    }

//导入考试成绩
    public function import_exam($name, $room, $number, $grade, $chinese, $math, $english, $physics, $total, $enroll1, $rank, $enroll2)
    {
        $this->load->helper('url');

        $data = array(
            'name' => $name,
            'room ' => $room,
            'number ' => $number,
            'grade ' => $grade,
            'chinese' => $chinese,
            'math' => $math,
            'english' => $english,
            'physics' => $physics,
            'total' => $total,
            'enroll1' => $enroll1,
            'rank' => $rank,
            'enroll2' => $enroll2
        );
        return $this->db->insert('exam190127', $data);
    }


//导入某日考试成绩
    public function import_exam_date($exam_date, $exam_number, $subject, $score, $missed)
    {
        $this->load->helper('url');

        $this->db->select('grade,class,name');
        $this->db->where('exam_number', $exam_number);
        $query = $this->db->get('xz_students');
        $result = $query->row_array();

        if ($result == true) {
            $this->db->select('id');
            $this->db->where('exam_number', $exam_number);
            $this->db->where('exam_date', $exam_date);
            $query = $this->db->get('xz_exam');
            $exist_id = $query->row_array();
            $subject_missed = $subject . '_missed';
            if ($exist_id == false) {
                $data = array(
                    'exam_number' => $exam_number,
                    'name' => $result['name'],
                    'grade' => $result['grade'],
                    'class' => $result['class'],
                    'exam_date' => $exam_date,
                    $subject => $score,
                    $subject_missed => $missed
                );
                if ($this->db->insert('xz_exam', $data)) {
                    return $exam_number . $result['name'] . $subject . "插入成功";
                } else {
                    return $exam_number . $result['name'] . $subject . "插入失败";
                }
            } else {
                $data = array(
                    $subject => $score,
                    $subject_missed => $missed
                );
                $this->db->where('exam_number', $exam_number);
                $this->db->where('exam_date', $exam_date);
                if ($this->db->update('xz_exam', $data)) {
                    //return $this->db->last_query();    //这句话可以显示上一步执行的sql语句
                    return $exam_number . $subject . "更新成功";
                } else {
                    return $exam_number . $subject . "更新失败";
                }
            }
        } else {
            return '该记录无效';
        }
    }

//将2月考试成绩更新到新表里
    public function update_exam_from190225()
    {
        $this->load->helper('url');


        $query = $this->db->get('exam190225');
        $result = $query->result_array();
        for ($i = 0; $i < count($result); $i++) {
            $name = $result[$i]['name'];
            $grade = $result[$i]['grade'];
            $chinese = $result[$i]['chinese'];
            $math = $result[$i]['math'];
            $english = $result[$i]['english'];
            $physics = $result[$i]['physics'];
            $total = $result[$i]['total'];
            $sql = "select * from xz_exam where exam_date='2019-02-25' and name='$name' and grade='$grade'";
            $query = $this->db->query($sql);
            $exist_id = $query->row_array();
            if ($exist_id == true) {
                $data = array(
                    'chinese' => $chinese,
                    'math' => $math,
                    'english' => $english,
                    'physics' => $physics,
                    'total_score' => $total,
                );
                $this->db->where('exam_date', '2019-02-25');
                $this->db->where('name', $name);
                $this->db->where('grade', $grade);
                if ($this->db->update('xz_exam', $data)) {
                    echo $name . '修改成功<br>';
                }
                //echo $this->db->last_query();    //这句话可以显示上一步执行的sql语句
                //die;
            }
        }
    }

//查分0119
    public function get_score()
    {
        $name = $this->input->post('name');
        $grade = $this->input->post('grade');
        $number = (int)($this->input->post('number'));
        if ($number == 9999) {
            $this->db->like('name', $name);
            $this->db->where('grade', $grade);
            $query = $this->db->get_where('exam190127');
        } else {
            $query = $this->db->get_where('exam190127', array('name' => $name, 'grade' => $grade, 'number' => $number));
        }
        //return $query->row_array();
        return $query->result_array();
    }


    ///////////导入考勤记录
    public function import_attendance($teacher_id, $name, $date, $coming, $leaving)
    {
        $this->load->helper('url');
        $this->db->select('id');
        $this->db->where('teacher_id', $teacher_id);
        $this->db->where('date', $date);
        $query = $this->db->get('xz_attendance');
        $exist_id = $query->row_array();

        if ($exist_id == false && 1 == 2) {     //如果不存在考勤计划就跳过
            $data = array(
                'teacher_id' => $teacher_id,
                'name' => $name,
                'date' => $date,
                'attendance1 ' => $coming,
                'attendance4 ' => $leaving,
                'createtime' => date("Y-m-d H:i:sa")
            );
            if ($this->db->insert('xz_attendance', $data)) {
                return $name . "插入成功";
            } else {
                return $name . "插入失败";
            }
        }
        if ($exist_id == true) {
            $id = $exist_id['id'];
            $data = array(
                'attendance1 ' => $coming,
                'attendance4 ' => $leaving
            );
            $this->db->where('id', $id);
            if ($this->db->update('xz_attendance', $data)) {
                return $name . "更新成功";
            } else {
                return $name . "更新失败";
            }
        } else {
            return $name . "考勤记录不存在";
        }
    }


    //新建某天考勤计划
    public function create_attendance()
    {
        $this->load->helper('url');

        $date = $this->input->post('date');
        $teacher_id_arr = $this->input->post('teacher_id');
        $name_arr = $this->input->post('name');
        $time1_arr = $this->input->post('time1');
        $time4_arr = $this->input->post('time4');
        $exception1_arr = $this->input->post('exception1');
        $exception4_arr = $this->input->post('exception4');
        $remark_arr = $this->input->post('remark');
        $excluded_arr = $this->input->post('excluded');
        $data = array();
        for ($i = 0; $i < count($teacher_id_arr); $i++) {
            if ($time1_arr[$i] == '') {
                $time1_arr[$i] = null;
            }
            if ($time4_arr[$i] == '') {
                $time4_arr[$i] = null;
            }
            if ($exception1_arr[$i] == '') {
                $exception1_arr[$i] = 0;
            }
            if ($exception4_arr[$i] == '') {
                $exception4_arr[$i] = 0;
            }
            reset($data);
            $this->db->select('id');
            $this->db->where('teacher_id', $teacher_id_arr[$i]);
            $this->db->where('date', $date);
            $query = $this->db->get('xz_attendance');
            $exist_id = $query->row_array();
            if ($exist_id == false) {
                $data = array(
                    'date' => $date,
                    'teacher_id' => $teacher_id_arr[$i],
                    'name' => $name_arr[$i],
                    'time1' => $time1_arr[$i],
                    'time4' => $time4_arr[$i],
                    'exception1' => $exception1_arr[$i],
                    'exception4' => $exception4_arr[$i],
                    'remark' => $remark_arr[$i],
                    'createtime' => date("Y-m-d H:i:s"),
                    'excluded' => $excluded_arr[$i],
                );
                $this->db->insert('xz_attendance', $data);
            } else {
                $id = $exist_id['id'];
                $data = array(
                    'time1' => $time1_arr[$i],
                    'time4' => $time4_arr[$i],
                    'exception1' => $exception1_arr[$i],
                    'exception4' => $exception4_arr[$i],
                    'remark' => $remark_arr[$i],
                    'excluded' => $excluded_arr[$i],
                );
                $this->db->where('id', $id);
                $this->db->update('xz_attendance', $data);
            }
            //echo $this->db->last_query();    //这句话可以显示上一步执行的sql语句
            //die;
        }
    }

    //查看2个月内存在的考勤记录
    public function existing_attendance($teachers_id_arr, $date)    //需要提供教师id数组和日期字符串
    {
        $date = date('Y-m-d', strtotime($date . ' -2 month'));
        $this->load->helper('url');

        $this->db->select('date');
        $this->db->where_in('teacher_id', $teachers_id_arr);
        $this->db->where('date>=', $date);
        $this->db->group_by('date');
        $query = $this->db->get('xz_attendance');
        //echo $this->db->last_query();    //这句话可以显示上一步执行的sql语句
        //die;
        return $query->result_array();
    }

    //查看某天考勤结果
    public function attendance_result($teachers_id_arr, $date)    //需要提供教师id数组和日期字符串
    {
        $this->load->helper('url');

        $this->db->where_in('teacher_id', $teachers_id_arr);
        $this->db->where('date', $date);
        $query = $this->db->get('xz_attendance');
        return $query->result_array();
    }

    //查看某月某人考勤结果汇总
    public function teacher_attendance_result($teacher_id, $month)    //需要提供教师id和月份，月份格式为2019-03-01
    {
        $year = date('Y', strtotime($month));
        $month = date('m', strtotime($month));
        $this->load->helper('url');

        $this->db->where('teacher_id', $teacher_id);
        //$this->db->where('date >=', $date);
        $where = "year(date)='$year' and month(date)='$month'";
        $this->db->where($where);
        $this->db->order_by("date", "DESC");
        $query = $this->db->get('xz_attendance');
        //echo $this->db->last_query();    //这句话可以显示上一步执行的sql语句
        //die;
        return $query->result_array();
    }


    //查看某人考勤记录
    public function teacher_attendance($teacher_id, $type)    //需要提供教师id和月份，month为0表示当月,month为1表示上月,month为日期表示具体月
    {
        if ($type == 0) {
            $date = date("Y-m-01", time());
        } elseif ($type == 1) {
            $date = date('Y-m-d', strtotime(date('Y-m-01') . ' -1 month'));
        } else {
            $date = $type;
        }
        $year = date('Y', strtotime($date));
        $month = date('m', strtotime($date));
        $this->load->helper('url');

        $this->db->where('teacher_id', $teacher_id);
        //$this->db->where('date >=', $date);
        $where = "year(date)='$year' and month(date)='$month'";
        $this->db->where($where);
        $this->db->order_by("date", "DESC");
        $query = $this->db->get('xz_attendance');
        //echo $this->db->last_query();    //这句话可以显示上一步执行的sql语句
        //die;
        return $query->result_array();
    }

    //查看某人是否还能申请考勤例外
    public function self_exception_ok($type)    //
    {
        if ($type == "thismonth") {   //本月
            $date = date("Y-m-01", time());
        } else {   //上月
            $date = date('Y-m-01', strtotime(date('Y-m-01') . ' -1 month'));
        }
        $year = date('Y', strtotime($date));
        $month = date('m', strtotime($date));

        $this->load->helper('url');

        $this->db->select('count(id) as count');
        $this->db->where('teacher_id', $_SESSION['teacher_id']);
        //$this->db->where('self_exception1', 1);
        //$this->db->or_where("self_exception4", 1);
        $where = "(self_exception1=1 or self_exception4=1) and year(date)='$year' and month(date)='$month'";
        $this->db->where($where);
        $query = $this->db->get('xz_attendance');
        //echo $this->db->last_query();    //这句话可以显示上一步执行的sql语句
        //die;
        return $query->row_array();
    }

    //个人提交签到签退例外请求
    public function request_exception()
    {
        $this->load->helper('url');

        $date = $this->input->post('date');
        $teacher_id = $_SESSION['teacher_id'];
        $submit = $this->input->post('submit');
        if ($submit == "request_exception1") {
            $data = array(
                'exception1' => "1",
                'self_exception1' => "1",
                'exception1_note' => $this->input->post('exception1_note')
            );
        }
        if ($submit == "request_exception4") {
            $data = array(
                'exception4' => "1",
                'self_exception4' => "1",
                'exception4_note' => $this->input->post('exception4_note')
            );
        }
        $this->db->where('date', $date);
        $this->db->where('teacher_id', $teacher_id);
        $this->db->update('xz_attendance', $data);
        //echo $this->db->last_query();    //这句话可以显示上一步执行的sql语句
    }

    //年级平均分，不考虑缺考学生
    public function grade_avg_v1($date, $grade, $class = false)
    {
        if ($class == true) {
            $class = " and class=" . $class;
        }
        $query = $this->db->query("select avg(total_score) as total_score_avg,avg(chinese) as chinese_avg,avg(math) as math_avg,avg(english) as english_avg,avg(physics) as physics_avg,avg(science) as science_avg,avg(english) as english_avg,avg(geography) as geography_avg,avg(moral) as moral_avg,avg(biology) as biology_avg,avg(politics) as politics_avg,avg(history) as history_avg,avg(politics_history) as politics_history_avg from xz_exam where exam_date='$date' and grade='$grade' $class");
        return $query->row_array();
    }

    //班级或年级的高分率，不考虑缺考学生科目
    public function grade_ratio_v1($type, $date, $grade, $subject, $full_mark, $class = false)
    {
        if ($class == true) {
            $class = " and class=" . $class;
        } else {
            $class = '';
        }
        $query = $this->db->query("select count(id) as total_num from xz_exam where exam_date='$date' and grade='$grade' $class");
        $row = $query->row_array();
        $total_num = $row['total_num'];
        if ($type == 'high') {
            $the_score = $full_mark * 0.8;
            $query = $this->db->query("select count(id) as the_num from xz_exam where $subject>=$the_score and exam_date='$date' and grade='$grade' $class");
        } elseif ($type == 'pass') {
            $the_score = $full_mark * 0.6;
            $query = $this->db->query("select count(id) as the_num from xz_exam where $subject>=$the_score and exam_date='$date' and grade='$grade' $class");
        } elseif ($type == 'low') {
            $the_score = $full_mark * 0.4;
            $query = $this->db->query("select count(id) as the_num from xz_exam where $subject<$the_score and exam_date='$date' and grade='$grade' $class");
        }
        $row = $query->row_array();
        $the_num = $row['the_num'];
        return 100 * round($the_num / $total_num, 4);
    }

    //年级平均分，缺考学生不计算
    public function grade_avg($date, $grade, $class = false)
    {
        $arr = array();
        if ($class == true) {
            $class = " and class=" . $class;
        }

        /*
        $sql="select avg(chinese) as chinese_avg from xz_exam where chinese_missed=0 and exam_date='$date' and grade='$grade' $class";
        $query = $this->db->query($sql);
        $result=$query->row_array();
        $arr["chinese_avg"]=$result["chinese_avg"];
        $sql="select avg(math) as math_avg from xz_exam where math_missed=0 and exam_date='$date' and grade='$grade' $class";
        $query = $this->db->query($sql);
        $result=$query->row_array();
        $arr["math_avg"]=$result["math_avg"];
        $sql="select avg(english) as english_avg from xz_exam where english_missed=0 and exam_date='$date' and grade='$grade' $class";
        $query = $this->db->query($sql);
        $result=$query->row_array();
        $arr["english_avg"]=$result["english_avg"];
        $sql="select avg(physics) as physics_avg from xz_exam where physics_missed=0 and exam_date='$date' and grade='$grade' $class";
        $query = $this->db->query($sql);
        $result=$query->row_array();
        $arr["physics_avg"]=$result["physics_avg"];
        $sql="select avg(science) as science_avg from xz_exam where science_missed=0 and exam_date='$date' and grade='$grade' $class";
        $query = $this->db->query($sql);
        $result=$query->row_array();
        $arr["science_avg"]=$result["science_avg"];
        $sql="select avg(geography) as geography_avg from xz_exam where geography_missed=0 and exam_date='$date' and grade='$grade' $class";
        $query = $this->db->query($sql);
        $result=$query->row_array();
        $arr["geography_avg"]=$result["geography_avg"];
        $sql="select avg(moral) as moral_avg from xz_exam where moral_missed=0 and exam_date='$date' and grade='$grade' $class";
        $query = $this->db->query($sql);
        $result=$query->row_array();
        $arr["moral_avg"]=$result["moral_avg"];
        $sql="select avg(biology) as biology_avg from xz_exam where biology_missed=0 and exam_date='$date' and grade='$grade' $class";
        $query = $this->db->query($sql);
        $result=$query->row_array();
        $arr["biology_avg"]=$result["biology_avg"];
        $sql="select avg(politics) as politics_avg from xz_exam where politics_missed=0 and exam_date='$date' and grade='$grade' $class";
        $query = $this->db->query($sql);
        $result=$query->row_array();
        $arr["politics_avg"]=$result["politics_avg"];
        $sql="select avg(history) as history_avg from xz_exam where history_missed=0 and exam_date='$date' and grade='$grade' $class";
        $query = $this->db->query($sql);
        $result=$query->row_array();
        $arr["history_avg"]=$result["history_avg"];
        $sql="select avg(politics_history) as politics_history_avg from xz_exam where politics_history_missed=0 and exam_date='$date' and grade='$grade' $class";
        $query = $this->db->query($sql);
        $result=$query->row_array();
        $arr["politics_history_avg"]=$result["politics_history_avg"];
        */


        $sql = "select avg(chinese) as chinese_avg from xz_exam where chinese is not null and exam_date='$date' and grade='$grade' $class";
        $query = $this->db->query($sql);
        $result = $query->row_array();
        $arr["chinese_avg"] = $result["chinese_avg"];
        $sql = "select avg(math) as math_avg from xz_exam where math is not null and exam_date='$date' and grade='$grade' $class";
        $query = $this->db->query($sql);
        $result = $query->row_array();
        $arr["math_avg"] = $result["math_avg"];
        $sql = "select avg(english) as english_avg from xz_exam where english is not null and exam_date='$date' and grade='$grade' $class";
        $query = $this->db->query($sql);
        $result = $query->row_array();
        $arr["english_avg"] = $result["english_avg"];
        $sql = "select avg(physics) as physics_avg from xz_exam where physics is not null and exam_date='$date' and grade='$grade' $class";
        $query = $this->db->query($sql);
        $result = $query->row_array();
        $arr["physics_avg"] = $result["physics_avg"];
        $sql = "select avg(science) as science_avg from xz_exam where science is not null and exam_date='$date' and grade='$grade' $class";
        $query = $this->db->query($sql);
        $result = $query->row_array();
        $arr["science_avg"] = $result["science_avg"];
        $sql = "select avg(geography) as geography_avg from xz_exam where geography is not null and exam_date='$date' and grade='$grade' $class";
        $query = $this->db->query($sql);
        $result = $query->row_array();
        $arr["geography_avg"] = $result["geography_avg"];
        $sql = "select avg(moral) as moral_avg from xz_exam where moral is not null and exam_date='$date' and grade='$grade' $class";
        $query = $this->db->query($sql);
        $result = $query->row_array();
        $arr["moral_avg"] = $result["moral_avg"];
        $sql = "select avg(biology) as biology_avg from xz_exam where biology is not null and exam_date='$date' and grade='$grade' $class";
        $query = $this->db->query($sql);
        $result = $query->row_array();
        $arr["biology_avg"] = $result["biology_avg"];
        $sql = "select avg(politics) as politics_avg from xz_exam where politics is not null and exam_date='$date' and grade='$grade' $class";
        $query = $this->db->query($sql);
        $result = $query->row_array();
        $arr["politics_avg"] = $result["politics_avg"];
        $sql = "select avg(history) as history_avg from xz_exam where history is not null and exam_date='$date' and grade='$grade' $class";
        $query = $this->db->query($sql);
        $result = $query->row_array();
        $arr["history_avg"] = $result["history_avg"];
        $sql = "select avg(politics_history) as politics_history_avg from xz_exam where politics_history is not null and exam_date='$date' and grade='$grade' $class";
        $query = $this->db->query($sql);
        $result = $query->row_array();
        $arr["politics_history_avg"] = $result["politics_history_avg"];

        $total_score_avg = 0;
        foreach ($arr as $key => $value) {
            $total_score_avg = $total_score_avg + $value;
        }
        $arr["total_score_avg"] = $total_score_avg;
        return $arr;
    }

    //班级或年级的高分率，缺考学生科目不参与计算
    public function grade_ratio($type, $date, $grade, $subject, $full_mark, $class = false)
    {
        if ($class == true) {
            $class = " and class=" . $class;
        } else {
            $class = '';
        }
        if ($subject == "total_score") {
            $subject_missed_sql = '';
        } else {
            $subject_missed_sql = " and " . $subject . "_missed=0";
        }
        $subject_missed_sql = " and " . $subject . " IS NOT NULL ";
        $query = $this->db->query("select count(id) as total_num from xz_exam where exam_date='$date' $subject_missed_sql and grade='$grade' $class");
        $row = $query->row_array();
        $total_num = $row['total_num'];
        if ($type == 'high') {
            $the_score = $full_mark * 0.8;
            $query = $this->db->query("select count(id) as the_num from xz_exam where $subject>=$the_score $subject_missed_sql and exam_date='$date' and grade='$grade' $class");
        } elseif ($type == 'pass') {
            $the_score = $full_mark * 0.6;
            $query = $this->db->query("select count(id) as the_num from xz_exam where $subject>=$the_score $subject_missed_sql and exam_date='$date' and grade='$grade' $class");
        } elseif ($type == 'low') {
            $the_score = $full_mark * 0.4;
            $query = $this->db->query("select count(id) as the_num from xz_exam where $subject<$the_score $subject_missed_sql and exam_date='$date' and grade='$grade' $class");
        }
        $row = $query->row_array();
        $the_num = $row['the_num'];
        if ($total_num == 0) {
            return 0;
        } else {
            return 100 * round($the_num / $total_num, 4);
        }
    }

    //年级前或后若干名，没考虑分数相同的情况
    public function grade_top_v1($date, $grade, $subject, $desc, $limit)     //subject必须是total_score、chinese、math、english、physics中的一个
    {
        if ($subject == "total_score") {
            $subject_missed_sql = '';
        } else {
            $subject_missed_sql = " and " . $subject . "_missed=0";
        }
        $subject_missed_sql = " and " . $subject . " IS NOT NULL ";
        $query = $this->db->query("select class from xz_exam where exam_date='$date' and grade='$grade' $subject_missed_sql order by $subject $desc limit $limit");
        return $query->result_array();
    }

    //年级前或后若干名
    public function grade_top($date, $grade, $subject, $desc, $limit)     //subject必须是total_score、chinese、math、english、physics中的一个
    {
        $limit = $limit - 1;
        if ($subject == "total_score") {
            $subject_missed_sql = '';
        } else {
            $subject_missed_sql = " and " . $subject . "_missed=0";
        }
        $subject_missed_sql = " and " . $subject . " IS NOT NULL ";
        if ($desc == "desc") {
            $query = $this->db->query("select $subject from xz_exam where exam_date='$date' and grade='$grade' $subject_missed_sql order by $subject desc limit $limit,1");
            $value = $query->row_array();
            $top_score = $value[$subject];
            $query = $this->db->query("select class from xz_exam where exam_date='$date' and grade='$grade' and $subject>='$top_score' $subject_missed_sql order by $subject desc");
        } else {
            $query = $this->db->query("select $subject from xz_exam where exam_date='$date' and grade='$grade' $subject_missed_sql order by $subject asc limit $limit,1");
            $value = $query->row_array();
            $top_score = $value[$subject];
            $query = $this->db->query("select class from xz_exam where exam_date='$date' and grade='$grade' and $subject<='$top_score' $subject_missed_sql order by $subject asc");
        }
        return $query->result_array();
    }

    //年级平均分
    public function grade_avg_3($grade, $class = false)
    {
        if ($class == true) {
            $class = " and class=" . $class;
        }
        $query = $this->db->query("select avg(total_score) as total_score_avg,avg(chinese) as chinese_avg,avg(math) as math_avg,avg(english) as english_avg,avg(physics) as physics_avg,avg(science) as science_avg,avg(english) as english_avg,avg(geography) as geography_avg,avg(moral) as moral_avg,avg(biology) as biology_avg,avg(politics) as politics_avg,avg(history) as history_avg,avg(politics_history) as politics_history_avg from xz_exam where exam_date='2019-03-26' and grade='$grade' $class");
        return $query->row_array();
    }

    //根据时间计算年级或班级平均分
    public function grade_avgs($grade, $date, $class = FALSE)
    {
        if ($class == true) {
            $class = " and class=" . $class;
        }
        $query = $this->db->query("select avg(total_score) as total_score_avg,avg(chinese) as chinese_avg,avg(math) as math_avg,avg(english) as english_avg,avg(physics) as physics_avg,avg(science) as science_avg,avg(english) as english_avg,avg(geography) as geography_avg,avg(moral) as moral_avg,avg(biology) as biology_avg,avg(politics) as politics_avg,avg(history) as history_avg,avg(politics_history) as politics_history_avg from xz_exam where exam_date='$date' and grade='$grade' $class");
        return $query->row_array();
    }

    //根据时间计算年级或班级最高分
    public function grade_maxs($grade, $date, $class = FALSE)
    {
        if ($class == true) {
            $class = " and class=" . $class;
        }
        $query = $this->db->query("select max(total_score) as total_score_max,max(chinese) as chinese_max,max(math) as math_max,max(english) as english_max,max(physics) as physics_max,max(science) as science_max,max(english) as english_max,max(geography) as geography_max,max(moral) as moral_max,max(biology) as biology_max,max(politics) as politics_max,max(history) as history_max,max(politics_history) as politics_history_max from xz_exam where exam_date='$date' and grade='$grade' $class");
        return $query->row_array();
    }

    //根据时间计算个人在年级或班级中的排名
    public function student_rank($grade, $date, $subject, $score, $class = FALSE)
    {
        if ($class == true) {
            $class = " and class='" . $class . "'";
        }
        $query = $this->db->query("select count(id) as count from xz_exam where $subject>$score and exam_date='$date' and grade='$grade' $class");
        $result = $query->row_array();
        return $result['count'];
        //echo $this->db->last_query();    //这句话可以显示上一步执行的sql语句
        //die;
    }


    //年级前或后若干名
    public function grade_top_3($grade, $subject, $desc, $limit)     //subject必须是total、chinese、math、english、physics中的一个
    {
        $query = $this->db->query("select class from xz_exam where exam_date='2019-03-26' and grade='$grade' order by $subject $desc limit $limit");
        return $query->result_array();
    }

    //更新某次考试总分
    public function update_exam_total_score($grade, $subject_arr, $date)
    {
        $pre_sql = implode('+', $subject_arr);
        $this->db->query("update xz_exam set total_score=$pre_sql where grade='$grade' and exam_date='$date'");
        //echo $this->db->last_query();    //这句话可以显示上一步执行的sql语句
        //die;
    }

    //年级前十
    public function grade_top_10($grade, $date)
    {
        //$query = $this->db->query("select name,class,total_score from xz_exam where exam_date='$date' and grade='$grade' order by total_score desc limit 10");
        $query = $this->db->query("select total_score from xz_exam where exam_date='$date' and grade='$grade' order by total_score desc limit 9,1");
        $value = $query->row_array();
        $top10_score = $value['total_score'];
        $query = $this->db->query("select name,class,total_score from xz_exam where exam_date='$date' and grade='$grade' and total_score>='$top10_score' order by total_score desc");
        return $query->result_array();
    }

    //年级单科第一
    public function grade_top_1($grade, $subject, $date)
    {
        $query = $this->db->query("select $subject from xz_exam where exam_date='$date' and grade='$grade' and `$subject` IS NOT NULL order by $subject desc limit 1");
        $value = $query->row_array();
        $top_score = $value[$subject];
        $query = $this->db->query("select name,class,$subject from xz_exam where exam_date='$date' and grade='$grade' and `$subject`='$top_score'");
        return $query->result_array();
    }



//设置考号
    public function set_exam_number()      //此功能需要修改
    {
        $arr = array(25, 33);         //grade
        $arr_0 = array(25, 33);         //class
        $arr_1 = array(38, 37);          //考场人数
        $arr_2 = array(8101, 8201);  //考场编号起点
        for ($i = 0; $i < count($arr_1); $i++) {
            $exam_number = $arr_2[$i];
            $j = 0;
            $k = 0;
            while ($j < $arr_1[$i]) {
                $class = $arr_0[$k];
                $this->db->query("update xz_students set exam_number='$exam_number' where exam_number=0 and class='$class' order by id limit 1");
                //echo $this->db->last_query();    //这句话可以显示上一步执行的sql语句
                //die;
                if ($this->db->affected_rows() > 0) {
                    $exam_number = $exam_number + 1;
                    $j = $j + 1;
                }
                $k = $k + 1;
                if ($k == count($arr_0)) {
                    $k = 0;
                }
            }
        }
    }


    public function teacher_class_subject($teacher_id, $grade, $class)   //不包括班主任
    {
        $this->db->where('teacher_id=', $teacher_id);
        $this->db->where('grade=', $grade);
        $this->db->where('class=', $class);
        $this->db->where('subject_id!=', "-1");
        $this->db->from('xz_class_teachers');
        $this->db->join('xz_subjects', 'xz_class_teachers.subject_id = xz_subjects.id');
        $query = $this->db->get();
        return $query->row_array();
    }

//批量修改学生密码
    public function update_student_pwd_batch()
    {
        $query = $this->db->query('SELECT * FROM xz_students');

        foreach ($query->result_array() as $row) {
            if (md5($row['identity_number']) == $row['password']) {
                $newpwd = md5(get_birthday_from_id($row['identity_number']));
                $id = $row['id'];
                $this->db->query("update xz_students set password='$newpwd' where id='$id'");
            }
        }
    }


//新增肺炎调查
    public function feiyan()
    {
        $this->load->helper('url');

        $data = array(
            'name' => $this->input->post('name'),
            'identity_number_4' => $this->input->post('identity_number_4'),
            'location' => $this->input->post('location'),
            'family_number' => $this->input->post('family_number'),
            'wuhan_return' => $this->input->post('wuhan_return'),
            'huangzhou_return' => $this->input->post('huangzhou_return'),
            'fever' => $this->input->post('fever'),
            'close_contact' => $this->input->post('close_contact'),
            //'remark' => $this->input->post('remark'),
            'submit_time' => date("Y-m-d H:i:sa"),
            'submit_date' => date("Y-m-d"),
            'ip' => $_SERVER['REMOTE_ADDR']
        );
        return $this->db->insert('xz_feiyan', $data);
    }

//备用的mysql语句

    //update `xz_teachers` set `contract_start` = SUBSTRING_INDEX(`contract_period`, '-', 1),`contract_end` = SUBSTRING_INDEX(`contract_period`, '-', -1)          //在mysql中拆分字符串
    /////////////////////
}

?>