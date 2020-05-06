<?php
require('api.php');
$allData = showPosition();
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
    <legend>添加任务</legend>
</fieldset>

<form class="layui-form" action="api.php" lay-filter="example" method="post">
    <input type="hidden" name="action" value="3">
    <input type="hidden" name="position_id" value="<?php echo $allData[0]['id'] ?>">
    <div class="layui-form-item">
        <label class="layui-form-label">位置名称</label>
        <div class="layui-input-block">
            <input type="text" name="position_name" lay-verify="title" autocomplete="off" placeholder="请输入位置" class="layui-input" value="<?php echo $allData[0]['position_name'] ?>">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">序号</label>
        <div class="layui-input-block">
            <input type="text" name="sort" lay-verify="title" autocomplete="off" placeholder="请输入序号" class="layui-input" value="<?php echo $allData[0]['sort'] ?>">
        </div>
    </div>

    <div class="layui-form-item">
        <div class="layui-input-block">
            <button type="submit" class="layui-btn" lay-submit="" lay-filter="demo1">立即提交</button>
        </div>
    </div>
</form>


<script src="layui/layui.js" charset="utf-8"></script>
<!--<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>-->
<!-- 注意：如果你直接复制所有代码到本地，上述js路径需要改成你本地的 -->
<script>

    layui.use(['form', 'layedit', 'laydate'], function(){
        var form = layui.form
            ,layer = layui.layer
            ,layedit = layui.layedit
            ,laydate = layui.laydate;



    });
</script>

</body>
</html>