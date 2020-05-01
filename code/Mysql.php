<?php
$JXMySQL_Host = "localhost";
$JXMySQL_Port = 3306;
$JXMySQL_Username = "root";
$JXMySQL_Password = "123456";
$JXMySQL_Database = "tingting";
$JXMySQL_Connect = mysqli_connect($JXMySQL_Host, $JXMySQL_Username, $JXMySQL_Password, $JXMySQL_Database, $JXMySQL_Port);
if(!$JXMySQL_Connect) exit('[JXMySQL]Failure mysqli_connect');
$JXMySQL_Statement = false;
$JXMySQL_Fetch = false;
$JXMySQL_Affect = false;
$JXMySQL_Insert = false;
$JXMySQL_Result = false;
function JXMySQL_Affect(){
    global $JXMySQL_Affect;
    if($JXMySQL_Affect){
        $JXMySQL_Affect = false;
        global $JXMySQL_Statement;
        if($JXMySQL_Statement) $Affect = mysqli_stmt_affected_rows($JXMySQL_Statement);
        else{
            global $JXMySQL_Connect;
            $Affect = mysqli_affected_rows($JXMySQL_Connect);
        }
        if($Affect === null || $Affect < 0) return(false);
        return($Affect);
    }
    return(false);
}
function JXMySQL_Begin(){
    global $JXMySQL_Connect;
    if(mysqli_begin_transaction($JXMySQL_Connect)) return(true);
    return(false);
}
function JXMySQL_Commit(){
    global $JXMySQL_Connect;
    if(mysqli_commit($JXMySQL_Connect)) return(true);
    return(false);
}
function JXMySQL_Execute($Query = null, $Param = null){
    global $JXMySQL_Statement;
    global $JXMySQL_Fetch;
    if($JXMySQL_Statement){
        mysqli_stmt_close($JXMySQL_Statement);
        $JXMySQL_Statement = false;
    }else if($JXMySQL_Fetch){
        mysqli_free_result($JXMySQL_Fetch);
        $JXMySQL_Fetch = false;
    }
    global $JXMySQL_Affect;
    global $JXMySQL_Insert;
    global $JXMySQL_Result;
    if(is_string($Query)){
        global $JXMySQL_Connect;
        if(is_array($Param)){
            if($JXMySQL_Statement = mysqli_prepare($JXMySQL_Connect, $Query)){
                if(is_array($Param)){
                    $Array = array($JXMySQL_Statement, str_repeat('s', $Count = count($Param)));
                    for($Index = 0; $Index < $Count; ++ $Index) $Array[$Index + 2] = &$Param[$Index];
                    if(!call_user_func_array('mysqli_stmt_bind_param', $Array)){
                        $JXMySQL_Affect = false;
                        $JXMySQL_Insert = false;
                        $JXMySQL_Result = false;
                        return(false);
                    }
                }
                if(mysqli_stmt_execute($JXMySQL_Statement)){
                    $JXMySQL_Affect = true;
                    $JXMySQL_Insert = true;
                    $JXMySQL_Result = true;
                    return(true);
                }
            }
        }else{
            if($JXMySQL_Fetch = mysqli_query($JXMySQL_Connect, $Query, MYSQLI_USE_RESULT)){
                if($JXMySQL_Fetch === true){
                    $JXMySQL_Fetch = false;
                    $JXMySQL_Affect = true;
                    $JXMySQL_Insert = true;
                    $JXMySQL_Result = false;
                }else{
                    $JXMySQL_Affect = false;
                    $JXMySQL_Insert = false;
                    $JXMySQL_Result = true;
                }
                return(true);
            }
        }
    }
    $JXMySQL_Affect = false;
    $JXMySQL_Insert = false;
    $JXMySQL_Result = false;
    return(false);
}
function JXMySQL_Insert(){
    global $JXMySQL_Insert;
    if($JXMySQL_Insert){
        $JXMySQL_Insert = false;
        global $JXMySQL_Statement;
        if($JXMySQL_Statement) $Insert = mysqli_stmt_insert_id($JXMySQL_Statement);
        else{
            global $JXMySQL_Connect;
            $Insert = mysqli_insert_id($JXMySQL_Connect);
        }
        if($Insert === null || $Insert <= 0) return(false);
        return($Insert);
    }
    return(false);
}
function JXMySQL_Result(){
    global $JXMySQL_Result;
    if($JXMySQL_Result){
        $JXMySQL_Result = false;
        global $JXMySQL_Statement;
        if($JXMySQL_Statement){
            if($Fetch = mysqli_stmt_get_result($JXMySQL_Statement)){
                $Result = mysqli_fetch_all($Fetch, MYSQLI_ASSOC);
                mysqli_free_result($Fetch);
            }else return(false);
        }else{
            global $JXMySQL_Fetch;
            $Result = mysqli_fetch_all($JXMySQL_Fetch, MYSQLI_ASSOC);
        }
        if($Result === null) return(false);
        return($Result);
    }
    return(false);
}
function JXMySQL_Rollback(){
    global $JXMySQL_Connect;
    if(mysqli_rollback($JXMySQL_Connect)) return(true);
    return(false);
}

function JXReturn_Json($Code = 0, $count =0,$Data = null,$msg){
    $Result = array('code' => $Code,'count' => $count,'msg' => $msg);
    if($Data !== null) $Result['data'] = $Data;
    exit(json_encode($Result));
}
?>