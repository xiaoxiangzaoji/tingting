<?php
require('api.php');
$allData = getDateList();
$data = $allData['dateData'];
//var_dump($data);die();
$excel = $allData['excel'];
$timelist = $allData['timeList'];
//var_dump($timelist);die();
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Layui</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="layui/css/layui.css" media="all">
    <!-- 注意：如果你直接复制所有代码到本地，上述css路径需要改成你本地的 -->
</head>
<body>



<div class="layui-tab">
    <ul class="layui-tab-title">
        <li class="layui-this">排班时间表</li>
        <li>时间记录表</li>
    </ul>
    <div class="layui-tab-content">
        <div class="layui-tab-item layui-show">
            <div class="layui-form">
                <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
                    <legend><?php echo $excel[0]['excel_name'] ?><span>&nbsp;&nbsp;&nbsp;&nbsp;<label>开始时间</label><?php echo $excel[0]['start_time'] ?></span><span><label>截止时间</label><?php echo $excel[0]['end_time'] ?></span></legend>
                </fieldset>
                <table class="layui-hide" id="test" lay-filter="test"></table>
            </div>
        </div>
        <div class="layui-tab-item">
            <table class="layui-hide" id="test1" lay-filter="test1"></table>
        </div>
    </div>
</div>




<script src="./layui/layui.js"></script>
<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
<!-- 注意：如果你直接复制所有代码到本地，上述js路径需要改成你本地的 -->
<script>
    var data = '<?php echo json_encode($data, JSON_UNESCAPED_UNICODE)?>';
    data = JSON.parse(data);

    var tableHeaders = [];
    if (data.length > 0) {
        var temp_row = data[0];
        var temp_header = Object.keys(temp_row);
        for (let i=0;i<temp_header.length;i++){
            let temp_col = {field: temp_header[i], title: temp_header[i]};
            tableHeaders.push(temp_col)
        }
    }

    var timedata = '<?php echo json_encode($timelist, JSON_UNESCAPED_UNICODE)?>';
    timedata = JSON.parse(timedata);


    layui.use('table', function () {
        var table = layui.table;
        table.render({
            elem: '#test',
            limit:1000,
            toolbar: 'exports', //开启工具栏，此处显示默认图标，可以自定义模板，详见文档
            data: data,
            cols:[tableHeaders]
        });
    });

    layui.use('table', function () {
        var table = layui.table;
        table.render({
            elem: '#test1',
            limit:1000,
            toolbar: 'exports', //开启工具栏，此处显示默认图标，可以自定义模板，详见文档
            data: timedata,
            cols:[[ //标题栏
            {field: 'date', title: '日期', width: 500, sort: true}
            ,{field: 'str', title: '工作', width: 1000}
        ]]
        });
    });

    layui.use('element', function(){
        var element = layui.element;
    });
</script>

</body>
</html>