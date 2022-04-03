<?php

/**
 * PHP万年历
 * @author Fly 2012/10/16
 */
class Calendar
{
    protected $_table;//table表格
    protected $_currentDate;//当前日期
    protected $_year;    //年
    protected $_month;    //月
    protected $_days;    //给定的月份应有的天数
    protected $_dayofweek;//给定月份的 1号 是星期几
    protected $CI;     //为了在自定义类中使用ci的类库
    /**
     * 构造函数
     */
    public function __construct($params)
    {
        $this->CI =& get_instance();    //为了在自定义类中使用ci的类库
        $this->_year = $params['year'];
        $this->_month = $params['month'];
        $this->_au_edit_event = $params['au_edit_event'];
        $this->_table = "";
        //$this->_year = isset($_GET["y"]) ? $_GET["y"] : date("Y");
        //$this->_month = isset($_GET["m"]) ? $_GET["m"] : date("m");
        if ($this->_month > 12) {//处理出现月份大于12的情况
            $this->_month = 1;
            $this->_year++;
        }
        if ($this->_month < 1) {//处理出现月份小于1的情况
            $this->_month = 12;
            $this->_year--;
        }
        $this->_currentDate = $this->_year . '年' . $this->_month . '月';//当前得到的日期信息
        $this->_days = date("t", mktime(0, 0, 0, $this->_month, 1, $this->_year));//得到给定的月份应有的天数
        $this->_dayofweek = date("w", mktime(0, 0, 0, $this->_month, 1, $this->_year));//得到给定的月份的 1号 是星期几
    }

    /**
     * 输出标题和表头信息
     */
    protected function _showTitle()
    {
        $this->_table = "<table class='table table-bordered table-striped'><thead><tr>";
        $this->_table .= "<th colspan='1' class='text-center'><a href='/user/home/" . ($this->_year - 1) . "/" . ($this->_month) . "'>上一年</a></th>";
        $this->_table .= "<th colspan='1' class='text-center'><a href='/user/home/" . ($this->_year) . "/" . ($this->_month - 1) . "'>上一月</a></th>";
        $this->_table .= "<th colspan='3' class='text-center'>" . $this->_currentDate . "<small>&nbsp;&nbsp;&nbsp;&nbsp;<a href='/user/home/'>本月</a></small></th>";
        $this->_table .= "<th colspan='1' class='text-center'><a href='/user/home/" . ($this->_year) . "/" . ($this->_month + 1) . "'>下一月</a></th>";
        $this->_table .= "<th colspan='1' class='text-center'><a href='/user/home/" . ($this->_year + 1) . "/" . ($this->_month) . "'>下一年</a></th>";
        $this->_table .= "</tr></thead><tbody><tr>";
        $this->_table .= "<td class='text-center' width='14%'>星期一</td>";
        $this->_table .= "<td class='text-center' width='14%'>星期二</td>";
        $this->_table .= "<td class='text-center' width='14%'>星期三</td>";
        $this->_table .= "<td class='text-center' width='14%'>星期四</td>";
        $this->_table .= "<td class='text-center' width='14%'>星期五</td>";
        $this->_table .= "<td class='text-center' style='color:red' width='14%'>星期六</td>";
        $this->_table .= "<td class='text-center' style='color:red' width='14%'>星期日</td>";
        $this->_table .= "</tr>";
    }

    /**
     * 输出日期信息
     * 根据当前日期输出日期信息
     */
    protected function _showDate($personal_events)
    {
        $this->CI->load->helper('mine');  //调用ci类库里的自定义函数
        $nums = $this->_dayofweek;
        if ($nums == 0) {
            for ($i = 1; $i < 7; $i++) {//输出1号之前的空白日期
                $this->_table .= "<td>&nbsp</td>";
            }
        } else {
            for ($i = 1; $i < $this->_dayofweek; $i++) {//输出1号之前的空白日期
                $this->_table .= "<td>&nbsp</td>";
            }
        }
        for ($i = 1; $i <= $this->_days; $i++) {//输出天数信息
            if ($this->_year == date("Y") && $this->_month == date("m") && $i == date("d")) {
                $today_class = ' success';
            } else {
                $today_class = '';
            }
            $event='';
            foreach ($personal_events as $personal_events_item):
                if ($personal_events_item['date'] == date("Y-m-d", strtotime($this->_year . "-" . $this->_month . "-" . $i))) {
                    $event = $event."<br><small>" . str_replace("\r\n","<br>",deal_long_content($personal_events_item['content']))."&nbsp;&nbsp;<a href='".site_url('user/delete_personal_event/'.$personal_events_item["id"]).'/'.$this->_year.'/'.$this->_month."' onclick='javascript:return del();'><span class=\"glyphicon glyphicon-trash\"></span></a></small>";
                }
            endforeach;
            if ($nums % 7 == 0) {//换行处理：7个一行
                $this->_table .= '<td class="text-center ' . $today_class .'" height="70px;">'.$i.$event.'</td></tr><tr>';
            } else {
                $this->_table .= '<td class="text-center ' . $today_class .'" height="70px;">'.$i.$event.'</td>';
            }
            $nums++;
        }
        $this->_table .= "</tbody></table>";
    }

    /**
     * 输出日历
     */
    public function showCalendar($personal_events)
    {
        $this->_showTitle();
        $this->_showDate($personal_events);
        return $this->_table;
    }
}
