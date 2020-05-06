<?php
require('api.php');
$allData = showExcel();
//var_dump($allData);die();
//$data = $allData['dateData'];
//$excel = $allData['excel'];

?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Layui</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="layui/css/layui.css"  media="all">
    <!-- 注意：如果你直接复制所有代码到本地，上述css路径需要改成你本地的 -->
</head>
<body>


<fieldset class="layui-elem-field layui-field-title" style="margin-top: 50px;">
    <legend>修改excel</legend>
</fieldset>

<form class="layui-form" action="api.php" lay-filter="example" method="post">
    <input type="hidden" name="action" value="1">
    <input type="hidden" name="excel_id" value="<?php echo $allData[0]['id'] ?>">
    <div class="layui-form-item">
        <label class="layui-form-label">excel名称</label>
        <div class="layui-input-block">
            <input type="text" name="excel_name" lay-verify="title" autocomplete="off" placeholder="请输入标题" class="layui-input" value="<?php echo $allData[0]['excel_name'] ?>">
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">间隔天数</label>
        <div class="layui-input-block">
            <input type="text" name="date_count" lay-verify="title" autocomplete="off" placeholder="间隔天数" class="layui-input" value="<?php echo $allData[0]['date_count'] ?>">
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">起始比率</label>
        <div class="layui-input-block">
            <input type="text" name="start_postion" lay-verify="title" autocomplete="off" placeholder="起始比率" class="layui-input" value="<?php echo $allData[0]['start_postion'] ?>">
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">开始时间</label>
        <div class="layui-input-block">
            <div class="layui-inline"> <!-- 注意：这一层元素并不是必须的 -->
                <input type="text" class="layui-input" id="start_time" name="start_time" autocomplete="off" value="<?php echo $allData[0]['start_time'] ?>">
            </div>
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">结束时间</label>
        <div class="layui-input-block">
            <div class="layui-inline"> <!-- 注意：这一层元素并不是必须的 -->
                <input type="text" class="layui-input" id="end_time" name="end_time" autocomplete="off" value="<?php echo $allData[0]['end_time'] ?>">
            </div>
        </div>
    </div>

    <div class="layui-form-item">
        <div class="layui-input-block">
            <button type="submit" class="layui-btn" lay-submit="" lay-filter="demo1">立即提交</button>
        </div>
    </div>
</form>


<script src="layui/layui.js" charset="utf-8"></script>
<!-- 注意：如果你直接复制所有代码到本地，上述js路径需要改成你本地的 -->
<script>


    layui.use(['form', 'layedit', 'laydate'], function(){
        var form = layui.form
            ,layer = layui.layer
            ,layedit = layui.layedit
            ,laydate = layui.laydate;

        //日期
        laydate.render({
            elem: '#start_time'
        });
        laydate.render({
            elem: '#end_time'
        });

        //创建一个编辑器
        var editIndex = layedit.build('LAY_demo_editor');


        // //监听提交
        // form.on('submit(demo1)', function(data){
        //     layer.alert(JSON.stringify(data.field), {
        //         title: '最终的提交信息'
        //     })
        // });
        //
        // //表单取值
        // layui.$('#LAY-component-form-getval').on('click', function(){
        //     var data = form.val('example');
        //     alert(JSON.stringify(data));
        // });

    });
</script>

</body>
</html>