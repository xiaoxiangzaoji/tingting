<?php
require_once('Mysql.php');

$method = $_SERVER['REQUEST_METHOD'] == 'GET' ? true : false;
if($method){
    $action = $_GET['action'];
    switch($action){
        case 1;
            getExcelList();
            break;
        case 2;
            getTaskList();
            break;
        case 3;
            getPositionList();
            break;
        case 4:
            getDateList();
            break;
    }
}else{
    $action = $_POST['action'];
    switch($action){
        case 1;
            addExcel();
            break;
        case 2:
            addTask();
            break;
        case 3:
            addPosition();
            break;
    }
}

function getExcelList(){
    JXMySQL_Execute('select count(id) from `excel`');
    $countData = JXMySQL_Result()?:0;
    JXMySQL_Execute("select * from `excel`");
    $result = JXMySQL_Result()?:[];
    JXReturn_Json(0,$countData,$result,'success');
}
function addExcel(){
    $excel_name = $_POST['excel_name'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $sql=JXMySQL_Execute("insert into `excel` (excel_name,start_time,end_time) values(?,?,?)",[$excel_name,$start_time,$end_time]);
    $list = JXMySQL_Insert($sql);
    if ($list) {
        JXReturn_Json(0,0,[],'success');
    }else{
        JXReturn_Json(1,0,[],'fail');
    }
}


function getTaskList(){
    $excel_id = $_GET['excel_id'];
    JXMySQL_Execute('select count(id) from `task` where excel_id =?',[$excel_id]);
    $countData = JXMySQL_Result()?:0;
    JXMySQL_Execute("select * from `task` where excel_id=? order by sort",[$excel_id]);
    $result = JXMySQL_Result()?:[];
    JXReturn_Json(0,$countData,$result,'success');
}

function addTask(){
    $task_name = $_POST['task_name'];
    $excel_id = $_POST['excel_id'];
    $sort = $_POST['sort'];
    $sql=JXMySQL_Execute("insert into `task` (task_name,excel_id,sort) values(?,?,?)",[$task_name,$excel_id,$sort]);
    $list = JXMySQL_Insert($sql);
    if ($list) {
        JXReturn_Json(0,0,[],'success');
    }else{
        JXReturn_Json(1,0,[],'fail');
    }
}

function getPositionList(){
    $excel_id = $_GET['excel_id'];
    JXMySQL_Execute('select count(id) from `position` where excel_id =?',[$excel_id]);
    $countData = JXMySQL_Result()?:0;
    JXMySQL_Execute("select * from `position` where excel_id=?",[$excel_id]);
    $result = JXMySQL_Result()?:[];
    JXReturn_Json(0,$countData,$result,'success');
}

function addPosition(){
    $position_name = $_POST['position_name'];
    $excel_id = $_POST['excel_id'];
    $sort = $_POST['sort'];
    $sql=JXMySQL_Execute("insert into `position` (position_name,excel_id,sort) values(?,?,?)",[$position_name,$excel_id,$sort]);
    $list = JXMySQL_Insert($sql);
    if ($list) {
        JXReturn_Json(0,0,[],'success');
    }else{
        JXReturn_Json(1,0,[],'fail');
    }
}

function getDateList(){
    $excel_id = $_GET['excel_id'];
    JXMySQL_Execute('select * from `excel` where id =?',[$excel_id]);
    $result = JXMySQL_Result();
    $excel_name = $result[0]['excel_name'];
    $start_time = $result[0]['start_time'];
    $end_time = $result[0]['end_time'];

    JXMySQL_Execute('select * from `task` where excel_id =? order by sort',[$excel_id]);
    $tasks = JXMySQL_Result();
    JXMySQL_Execute('select * from `position` where excel_id =? order by sort',[$excel_id]);
    $positions = JXMySQL_Result();
    $x = $y = [];
    foreach ($tasks as $task){
        $x[] = $task['task_name'];
    }

    foreach ($positions as $position){
        $y[] = $position['position_name'];
    }
    $datas= calculationDate($start_time,$x,$y);

    //    var_dump($datas);die();

    $firstchange = [];
    foreach ($datas as $data){
        $firstchange[$data['position']]['任务'] = $data['position'];
        $firstchange[$data['position']][$data['task']] = $data['date'];
    }
    return ['dateData'=>array_values($firstchange),'excel'=>$result];

}

function getExcelTask(){
    $excel_id = $_GET['excel_id'];
    JXMySQL_Execute('select * from `task` where excel_id =? order by sort',[$excel_id]);
    $tasks = JXMySQL_Result();
    return $tasks;
}
function calculationDate($start_time,$x,$y){
    $new =[];
    $final=[];
    foreach ($x as $k =>$v){

        foreach ($y as $a =>$b){
            if($k == 0 && $a == 0){
                $new[$k][$a] = ['date'=>$start_time,'position'=>$b,'task'=>$v,'task_sort'=>$k,'position_sort'=>$a];
                $final[]= ['date'=>$start_time,'position'=>$b,'task'=>$v,'task_sort'=>$k,'position_sort'=>$a];
            }else{
                if($k != 0 && $a == 0){
                    $lastk = $k -1;
                    if(isset($lastk)){
                        $start_time = $new[$lastk][getNewStart($y)]['date'];
                        $new[$k][$a] = ['date'=>$start_time,'position'=>$b,'task'=>$v,'task_sort'=>$k,'position_sort'=>$a];
                        $final[]= ['date'=>$start_time,'position'=>$b,'task'=>$v,'task_sort'=>$k,'position_sort'=>$a];
                    }
                }else{
                    $start_time =date("Y-m-d", strtotime("+1 day", strtotime($start_time)));
                    $new[$k][$a] = ['date'=>$start_time,'position'=>$b,'task'=>$v,'task_sort'=>$k,'position_sort'=>$a];
                    $final[]= ['date'=>$start_time,'position'=>$b,'task'=>$v,'task_sort'=>$k,'position_sort'=>$a];
                }

            }
        }
    }

    return $final;
}

function getNewStart($y){
    $middle = count($y)/2;
    if(ceil($middle)==$middle){
        $range = [$middle -2,$middle-1 ,$middle];
    }else{
        $range = [floor($middle)-1,ceil($middle)-1];
    }
    $newStart = $range[array_rand($range)];
    return $newStart;
}
?>

