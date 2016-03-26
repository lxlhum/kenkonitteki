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
mysql_query("SET NAMES UTF8");
mysql_query("SET CHARACTER SET UTF8");
mysql_query("SET CHARACTER_SET_RESULTS=UTF8");
$query = mysql_query($sql);
$rs = mysql_fetch_array($query);

$query2 = mysql_query($sql_user);
$rs2 = mysql_fetch_array($query2);

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

    <title><?php  echo $rs2['nickname']; ?>的资产信息</title>

    <!-- Bootstrap core CSS -->
    <link href="//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet">


</head>

<body>
<?php //echo "<h1>测试php 生成 html</h1>"; ?>

<div class="container">
    <div class="container-fluid">
        <p>头像<img src="<?php echo $rs2['headimgurl']; ?>" width="64" height="64"></p>
        <div class="row-fluid">
            <div class="span12">
                </br>
                <p class="lead">账户信息，点击此处进行<a href="https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx1d6c6e68f1887cd2&redirect_uri=http%3A%2F%2Fwww.yangtz.com%2Fyangtzpay%2Fexample%2Fin_money.php&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect"> 账户充值</a></p>

                <table class="table">
                    <thead>
                    <tr>
                        <th>账户</th>
                        <th>余额</th>
                        <th>单位</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="success">
                        <td>现金</td>
                        <td><?php echo $rs['account_money'];?></td>
                        <td>元</td>
                    </tr>
                    </tbody>
                    <thead>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="success">
                        <td>金</td>
                        <td><?php echo $rs['golds'];?></td>
                        <td>克</td>
                    </tr>
                    <tr class="success">
                        <td>银</td>
                        <td><?php  echo $rs['silvers'];?></td>
                        <td>克</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div><!-- /.container-fluid -->
    <div class="container-fluid">
        <div class="row-fluid">
            <div class="span12">
                <p class="lead">库存信息</p>
                <table class="table">
                    <thead>
                    <tr>
                        <th>图片</th>
                        <th>件数</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="success">
                        <td>10克</td>
                        <td>10克</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div><!-- /.container-fluid -->
    <div class="container-fluid">
        <div class="row-fluid">
            <div class="span12">
                <p class="lead">礼品码账户</p>
                <table class="table">
                    <thead>
                    <tr>
                        <th>礼品码类型</th>
                        <th>礼品码</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="info">
                        <td>乾坤壹掷金币礼品码</td>
                        <td>asd78sf6q9saaw9e</td>
                    </tr>
                    <tr class="info">
                        <td>乾坤壹掷银币礼品码</td>
                        <td>asd78223eesaaw9e</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</div><!-- /.container -->

<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>
<script src="//cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>


</body></html>
