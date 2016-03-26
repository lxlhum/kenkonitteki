<?php
ini_set('date.timezone','Asia/Shanghai');

//error_reporting(E_ERROR);
require_once "../lib/WxPay.Api.php";
require_once "WxPay.JsApiPay.php";
require_once "../lib/DBOperater.php";
require_once 'log.php';

require_once "../lib/WxPay.Exception.php";
require_once "../lib/WxPay.Config.php";
require_once "../lib/WxPay.Data.php";

//初始化日志
$logHandler= new CLogFileHandler("../logs/".date('Y-m-d').'.log');
$log = Log::Init($logHandler, 15);

$openId = $_POST["openidtext"];
//echo $openId;

//①、获取用户openid
$tools = new JsApiPay();
//$openId = $tools->GetOpenid();
$userInfo = $tools->GetUserInfoParameters();
//获取用户状态
DBOperater::checkUserStatus($userInfo);

$dbname = WxPayConfig::DBNAME_MYSQL_YTZ;
$host = WxPayConfig::HOST_YTZ;
$port = WxPayConfig::PORT;
$user = WxPayConfig::USER_YTZ;
$pwd = WxPayConfig::PWD;

/*接着调用mysql_connect("{$host}:{$port}",$user,$pwd,true)连接服务器*/
$link = @mysql_connect("{$host}:{$port}", $user, $pwd, true);

if (!$link) {
    die("Connect Server Failed: ".mysql_error());
}

/*连接成功后立即调用mysql_select_db()选中需要连接的数据库*/
if (!mysql_select_db($dbname, $link)) {
    die("Select Database Failed:".mysql_error($link));
}
/*至此完成连接*/
/*需要再连接其他数据库，请再使用mysql_connect + mysql_select_db启动另一个连接*/

/*接下来就可以使用其他标准php mysql 函数操作进行数据库操作*/

$sql = "select * from qk_account where openid ='{$openId}'";
$sql_user = "select * from qk_user where openid ='{$openId}'";
$sql_mxt_pacer_month_form = "select * from mxt_pacer_month_form where openid ='{$openId}'";
mysql_query("SET NAMES UTF8");
mysql_query("SET CHARACTER SET UTF8");
mysql_query("SET CHARACTER_SET_RESULTS=UTF8");
$query = mysql_query($sql);
$rs = mysql_fetch_array($query);

$query2 = mysql_query($sql_user);
$rs2 = mysql_fetch_array($query2);

$query3 = mysql_query($sql_mxt_pacer_month_form);
$rs3 = mysql_fetch_array($query3);

$showCheckstr = "";
$checkbool = true;

if($rs3['photo_a']=="a"&&$rs3['photo_b']=="b"&&$rs3['photo_c']=="c")
{
    $arr = $_POST;
    $arr['msg']="请先上传跑量图片";
    $showCheckstr = "请先上传跑量图片";
    $checkbool = false;
//echo $_POST['uname'];
//    echo json_encode($arr);
}
else{

    $arr = $_POST;
    $phone = $_POST['phone'];
    $uname = $_POST['uname'];
    $monthpace = $_POST['monthpace'];


//    echo "phone".$phone;
//    echo "uname".$uname;
//    echo "monthpace".$monthpace;

//    if(preg_match("/^1[123456789]{1}\d{9}$/",$phone)){
        if (preg_match('/^([\xe4-\xe9][\x80-\xbf]{2}){2,4}$/', $uname)) {
            if (is_numeric($monthpace)) {
                if((double)$monthpace<50)
                {
                    $showCheckstr = $showCheckstr ."月跑量小于50，尚未达标,请您重新输入";
                    $checkbool = false;
                }
            } else {
//        echo "月跑量输入有误,请重新输入";
                $showCheckstr = $showCheckstr ."月跑量输入有误,请您重新输入";
                $checkbool = false;
            }
        } else {
//        echo "姓名输入有误,请重新输入";
            $showCheckstr = $showCheckstr ."姓名输入有误,请您重新输入";
            $checkbool = false;
        }
//    }
//    else{
////        echo "手机号码输入有误,请重新输入";
//        $showCheckstr = "手机号码输入有误,请重新输入";
//        $checkbool = false;
//    }

    if($checkbool)
    {
        $sqlok = "update mxt_pacer_month_form a set a.phone='3',a.realname='$uname',a.month_pace='$monthpace',a.month='3'  where openid ='$openId'";
//        echo $sqlok;
        $queryok = mysql_query($sqlok);
    }


//echo $_POST['uname'];
//    echo json_encode($arr);
}

$sql_mxt_pacer_month_form2 = "select * from mxt_pacer_month_form where openid ='$openId'";
$query4 = mysql_query($sql_mxt_pacer_month_form2);
$rs4 = mysql_fetch_array($query4);



//查看账户遍历信息
//foreach($rs as $key=>$value){
//    echo "<font color='#00ff55;'>$key</font> : $value <br/>";
//}

/*关闭连接*/
mysql_close($link);

?>

<!DOCTYPE html>
<!-- saved from url=(0048)http://v3.bootcss.com/examples/starter-template/ -->
<html lang="zh-CN" xmlns="http://www.w3.org/1999/html"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- 上述3个meta标签*必须*放在最前面，任何其他内容都*必须*跟随其后！ -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="http://v3.bootcss.com/favicon.ico">

    <title><?php  echo $rs2['nickname']; ?>的3月跑量登记表单</title>

    <!-- Bootstrap core CSS -->
    <link href="//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet">
    <link href="maoxiantu2.css" rel="stylesheet">

</head>

<body>
<?php //echo "<h1>测试php 生成 html</h1>"; ?>

<div class="container">
    <div class="container-fluid">

        <p class="myfont"><?php

            if($checkbool)
            {
                echo "月跑量信息保存成功：";
                echo"<br>";
//                echo"<br>";
//                echo "手机号：" . $rs4['phone'];
                echo"<br>";
                echo"<br>";
                echo "真实姓名：" . $rs4['realname'];
                echo"<br>";
                echo"<br>";
                echo "月跑量：" . $rs4['month_pace']."公里";
            }
            else{
                echo $showCheckstr;
            }

            ?>

        </p>


    </div><!-- /.container-fluid -->




</div><!-- /.container -->

<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script type="text/javascript" src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>
<script type="text/javascript" src="//cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>


</body></html>
