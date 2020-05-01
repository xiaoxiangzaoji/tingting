<?php
$start_time = '2020-05-01';
$x = ['楼梯','窗子', '箱子'];
$y = ['1楼','2楼','3楼','4楼','5楼','6楼','7楼'];
$middle = count($y)/2;
if(ceil($middle)==$middle){
    $range = [$middle -2,$middle-1 ,$middle];
}else{
    $range = [floor($middle)-1,ceil($middle)-1];
}
$new =[];
//var_dump($range);
$newStart = $range[array_rand($range)];
//var_dump($newStartTime);die();
//var_dump(date("Y-m-d", strtotime("+1 day", strtotime($start_time))));die();
foreach ($x as $k =>$v){

    foreach ($y as $a =>$b){
        if($k == 0 && $a == 0){
            $new[$k][$a] = ['date'=>$start_time,'position'=>$b,'task'=>$v];
        }else{
            if($k != 0 && $a == 0){
                $lastk = $k -1;
                if(isset($lastk)){
                    $task = $x[$lastk];
                    $start_time = $new[$lastk][$newStart]['date'];
                    $new[$k][$a] = ['date'=>$start_time,'position'=>$b,'task'=>$v];
                }
            }else{
                $start_time =date("Y-m-d", strtotime("+1 day", strtotime($start_time)));
                $new[$k][$a] = ['date'=>$start_time,'position'=>$b,'task'=>$v];
            }

        }
    }
//    var_dump($new);die();
}
var_dump($new);die();

?>