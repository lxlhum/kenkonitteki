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

//①、获取用户openid
$tools = new JsApiPay();
$openId = $tools->GetOpenid();
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
    <link href="maoxiantu.css" rel="stylesheet">

</head>

<body>
<?php //echo "<h1>测试php 生成 html</h1>"; ?>

<div class="container">
    <div class="starter-template">
        <div class="centerok">
            <img src="img/mxt.jpg" width="95%" height="95%">
        </div>
    </div>
</div><!-- /.container -->

<div class="container">
        <table class="mytable" >
            <tr>
                <th>
                    <p class="myfont">
                        <img src="<?php echo $rs2['headimgurl']; ?>" width="100" height="100">
                    </p>
                </th>
                <th>
                    <p class="myfont">
                        <span class="nikcname"><?php  echo $rs2['nickname']; ?></span>又到了每个月毛线团交作业的时候了,
                    让我们动起手来，上传我们的<span class="paoliang">跑量</span>吧。
                    </p>
                </th>
            </tr>
        </table>
</div>


<div class="container">

    <br>
    <div class="starter-template">
        <div class="container-fluid">
            <div class="row-fluid">
                <div class="span12">
                    <div class="textupdate">
<!--                    <form id="my_form" action="https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx1d6c6e68f1887cd2&redirect_uri=http%3A%2F%2Fwww.yangtz.com%2Fyangtzpay%2Fexample%2Fsubmit.php&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect" method="post">-->
                        <form id="my_form" action="submit.php" method="post" >
                            <input type="hidden" name="openidtext" value="<?php echo $rs3['openid']; ?>">
                            <p class="myfont">真实姓名：<input class="myfontblack" type="text" name="uname" style="width:150px" value="<?php echo $rs3['realname']; ?>"></p>
<!--                            <p class="myfont">手机号：<input class="myfontblack" type="text" name="phone" style="width:150px" value="--><?php //echo $rs3['phone']; ?><!--"></p>-->
                            <p class="myfont">3月跑量：<input class="myfontblack" type="text" name="monthpace" style="width:50px" value="<?php echo $rs3['month_pace']; ?>">公里</p>
                        <?php
                        if($rs3['realname']=="realname"&&$rs3['phone']=="phone"&&$rs3['month_pace']=="0")
                        {
                            echo "<input type=\"submit\" value=\"提交\">";
                        }
                        else
                        {
                            echo "<input type=\"submit\" value=\"修改\">";
                        }
                        ?>
                        <span id="msg"></span></p>
                    </form>
                </div>
                <div id="output"></div>
            </div>
        </div>
      </div><!-- /.container-fluid -->
    </div>

    <div class="container-fluid">

        <div id="upload_file" >
        <form action="upload_file.php" method="post"
              enctype="multipart/form-data">
            <label for="file" class="myfont">上传月跑量图片：<input class="myfontred" type="file" name="file_img" id="file_img" /></label>
            <input type="hidden" name="openidmax" value="<?php echo $rs3['openid']; ?>">
            <br />
            <?php

            echo "<p class=\"myfontred\">注意:可能因为微信缓存原因，不能马上显示新图片，请3~5分钟后刷新再试。</p>";
            if($rs3['photo_a']=="a"&&$rs3['photo_b']=="b"&&$rs3['photo_c']=="c")
            {
//                class="btn"
                echo "<input type=\"submit\" name=\"submit\" value=\"上传\" />";
            }
            else
            {
                $photoall = $rs3['photo_a'];
                echo "<img src=\"".$photoall."\" width=\"40%\" height=\"40%\">";
                echo "<br />";
                echo "<input type=\"submit\" name=\"submit\" value=\"修改\" />";

            }
            ?>
<!--            <img src="--><?php //echo $rs3['photo_a']; ?><!--" width="40%" height="40%">-->

        </form>
        </div>
    </div><!-- /.container-fluid -->


</div><!-- /.container -->

<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script type="text/javascript" src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>
<script type="text/javascript" src="//cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>


</body></html>
