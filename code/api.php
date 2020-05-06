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
        case 5:
            showExcel();
            break;
        case 6:
            showTask();
            break;
        case 7:
            showPosition();
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
    $date_count = $_POST['date_count'];
    $start_postion = $_POST['start_postion'];
    if(isset($_POST['excel_id'])){
        $excel_id = $_POST['excel_id'];
        $sql=JXMySQL_Execute("update `excel` set excel_name=?,date_count=?,start_postion=?,start_time=?,end_time=? where id =?",[$excel_name,$date_count,$start_postion,$start_time,$end_time,$excel_id]);
        $list = JXMySQL_Affect($sql);
    }else{
        $sql=JXMySQL_Execute("insert into `excel` (excel_name,date_count,start_postion,start_time,end_time) values(?,?,?,?,?)",[$excel_name,$date_count,$start_postion,$start_time,$end_time]);
        $list = JXMySQL_Insert($sql);
    }

    if ($list) {
        JXReturn_Json(0,0,[],'成功');
    }else{
        JXReturn_Json(1,0,[],'失败');
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
    $sort = $_POST['sort'];
    if(isset($_POST['task_id'])){
        $task_id = $_POST['task_id'];
        $sql=JXMySQL_Execute("update `task` set task_name=?,sort=? where id =?",[$task_name,$sort,$task_id]);
        $list = JXMySQL_Affect($sql);
    }else{
        $excel_id = $_POST['excel_id'];
        $sql=JXMySQL_Execute("insert into `task` (task_name,excel_id,sort) values(?,?,?)",[$task_name,$excel_id,$sort]);
        $list = JXMySQL_Insert($sql);
    }

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
    $sort = $_POST['sort'];
    if(isset($_POST['position_id'])){
        $position_id = $_POST['position_id'];
        $sql=JXMySQL_Execute("update `position` set position_name=?,sort=? where id=?",[$position_name,$sort,$position_id]);
        $list = JXMySQL_Affect($sql);
    }else{
        $excel_id = $_POST['excel_id'];
        $sql=JXMySQL_Execute("insert into `position` (position_name,excel_id,sort) values(?,?,?)",[$position_name,$excel_id,$sort]);
        $list = JXMySQL_Insert($sql);
    }

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
    $start_time = $result[0]['start_time'];
    $date_count = $result[0]['date_count'];
    $start_postion = $result[0]['start_postion'];
    JXMySQL_Execute('select * from `task` where excel_id =? order by sort',[$excel_id]);
    $tasks = JXMySQL_Result();
    JXMySQL_Execute('select * from `position` where excel_id =? order by sort',[$excel_id]);
    $positions = JXMySQL_Result();
    $x = $y = [];
    //    foreach ($tasks as $task){
    //        $x[] = $task['task_name'];
    //    }
    foreach ($tasks as $task){
        $x[] = ['task_name'=>$task['task_name'],'sort'=>$task['sort']];
    }

    foreach ($positions as $position){
        $y[] = $position['position_name'];
    }
    //    var_dump($x);
    //    var_dump($y);die();
    //$datas= calculationDate($start_time,$x,$y);
    $datas= calculationNewDate($start_time,$x,$y,$date_count,$start_postion);
//    var_dump($datas);die();

    $firstchange = [];
    foreach ($datas as $data){
        $firstchange[$data['position']]['任务'] = $data['position'];
        $firstchange[$data['position']][$data['task']] = $data['date'];
    }
    return ['dateData'=>array_values($firstchange),'excel'=>$result,'timeList'=>timeTask($datas)];

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

function calculationNewDate($start_time,$x,$y,$date_count,$start_postion){
    $last_date_count = $date_count-1;
    $new =[];
    $final=[];
    foreach ($x as $k =>$v){

        foreach ($y as $a =>$b){
            if($k == 0 && $a == 0){
                $new[$k][$a] = ['date'=>$start_time,'position'=>$b,'task'=>$v['task_name'],'task_sort'=>$v['sort'],'position_sort'=>$a];
                $final[]= ['date'=>$start_time,'position'=>$b,'task'=>$v['task_name'],'task_sort'=>$v['sort'],'position_sort'=>$a];
            }else{
                $lastk = $k -1;
                if(isset($x[$lastk]) && $x[$k]['sort'] == $x[$lastk]['sort']){
                    if($k != 0 && $a == 0){
                        $start_time = $new[$k-1][getrRandom()]['date'];
                        $start_time = date("Y-m-d", strtotime("+".$last_date_count." day", strtotime($start_time)));
                        $new[$k][$a] = ['date'=>$start_time,'position'=>$b,'task'=>$v['task_name'],'task_sort'=>$v['sort'],'position_sort'=>$a];
                        $final[]= ['date'=>$start_time,'position'=>$b,'task'=>$v['task_name'],'task_sort'=>$v['sort'],'position_sort'=>$a];
                    }else{
                        $start_time =date("Y-m-d", strtotime("+".$date_count." day", strtotime($start_time)));
                        $new[$k][$a] = ['date'=>$start_time,'position'=>$b,'task'=>$v['task_name'],'task_sort'=>$v['sort'],'position_sort'=>$a];
                        $final[]= ['date'=>$start_time,'position'=>$b,'task'=>$v['task_name'],'task_sort'=>$v['sort'],'position_sort'=>$a];
                    }
                }else{
                    if($k != 0 && $a == 0){
                        if(isset($lastk)){
                            $start_time = $new[$lastk][getNewStart($y,$start_postion)]['date'];
                            $start_time = date("Y-m-d", strtotime("+".$last_date_count." day", strtotime($start_time)));
                            $new[$k][$a] = ['date'=>$start_time,'position'=>$b,'task'=>$v['task_name'],'task_sort'=>$v['sort'],'position_sort'=>$a];
                            $final[]= ['date'=>$start_time,'position'=>$b,'task'=>$v['task_name'],'task_sort'=>$v['sort'],'position_sort'=>$a];
                        }
                    }else{
                        $start_time =date("Y-m-d", strtotime("+".$date_count." day", strtotime($start_time)));
                        $new[$k][$a] = ['date'=>$start_time,'position'=>$b,'task'=>$v['task_name'],'task_sort'=>$v['sort'],'position_sort'=>$a];
                        $final[]= ['date'=>$start_time,'position'=>$b,'task'=>$v['task_name'],'task_sort'=>$v['sort'],'position_sort'=>$a];
                    }
                }

            }
        }

    }

    return $final;
}
function getNewStart($y,$start_postion){
    $middle = count($y)/$start_postion;
    //    $middle = count($y)/3;
    if(ceil($middle)==$middle){
        $range = [$middle -2,$middle-1 ,$middle];
    }else{
        $range = [floor($middle)-1,ceil($middle)-1];
    }
    $newStart = $range[array_rand($range)];
    return $newStart;
}

function getrRandom(){
    $range = [0,1];
    $newStart = $range[array_rand($range)];
    return $newStart;
}


function showExcel(){
    $excel_id = $_GET['excel_id'];
    JXMySQL_Execute('select * from `excel` where id =?',[$excel_id]);
    $result = JXMySQL_Result();
    return $result;
}

function showTask(){
    $task_id = $_GET['task_id'];
    JXMySQL_Execute('select * from `task` where id =?',[$task_id]);
    $result = JXMySQL_Result();
    return $result;
}

function showPosition(){
    $position_id = $_GET['position_id'];
    JXMySQL_Execute('select * from `position` where id =?',[$position_id]);
    $result = JXMySQL_Result();
    return $result;
}


function timeTask($arr){
//    $arr = [
//        ['date'=>'2020-05-02','task'=>'一','position'=>1],
//        ['date'=>'2020-05-01','task'=>'二','position'=>2],
//        ['date'=>'2020-05-03','task'=>'三','position'=>3],
//        ['date'=>'2020-05-04','task'=>'四','position'=>4],
//        ['date'=>'2020-05-05','task'=>'五','position'=>5],
//        ['date'=>'2020-05-06','task'=>'六','position'=>6],
//        ['date'=>'2020-05-04','task'=>'七','position'=>7],
//        ['date'=>'2020-05-05','task'=>'八','position'=>8],
//    ];
    //var_dump($arr);die();
    $new =[];
    foreach ($arr as $k =>$v){
        $new[strtotime($v['date'])][] = $v;
    }
    ksort($new);
    $finalData = [];
    //var_dump($new);die();

    foreach ($new as $a =>$b){
        foreach ($b as  $d){
            $task_position[] = $d['task'].'--'.$d['position'];
        }
        $finalData[] = ['date'=>$b[0]['date'],'str'=>implode(";",$task_position)];
        unset($task_position);
    }
    return $finalData;
}
?>