<?php


//error_reporting(E_ERROR);
require_once "../lib/WxPay.Api.php";
require_once "WxPay.JsApiPay.php";
require_once "../lib/DBOperater.php";
require_once 'log.php';

require_once "../lib/WxPay.Exception.php";
require_once "../lib/WxPay.Config.php";
require_once "../lib/WxPay.Data.php";

ini_set('date.timezone','Asia/Shanghai');
//初始化日志
$logHandler= new CLogFileHandler("../logs/".date('Y-m-d').'.log');
$log = Log::Init($logHandler, 15);

$openidg = $_POST["openidmax"];

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

$sql = "select * from qk_account where openid ='$openidg'";
$sql_user = "select * from qk_user where openid ='$openidg'";
$sql_mxt_pacer_month_form = "select * from mxt_pacer_month_form where openid ='$openidg'";
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

$abool = true;
$checkstr = "";
echo $_FILES["file_img"]["size"];
if(($_FILES["file_img"]["size"] < 4194304))
{
    if ((($_FILES["file_img"]["type"] == "image/gif")
        || ($_FILES["file_img"]["type"] == "image/jpeg")
        || ($_FILES["file_img"]["type"] == "image/pjpeg")
        || ($_FILES["file_img"]["type"] == "image/png"))
    )
    {
        if ($_FILES["file_img"]["error"] > 0)
        {
            echo "Return Code: " . $_FILES["file_img"]["error"] . "<br />";
        }
        else
        {
//        echo "Upload: " . $_FILES["file_img"]["name"] . "<br />";
//        echo "Type: " . $_FILES["file_img"]["type"] . "<br />";
//        echo "Size: " . ($_FILES["file_img"]["size"] / 1024) . " Kb<br />";
//        echo "Temp file: " . $_FILES["file_img"]["tmp_name"] . "<br />";

            if (file_exists("uploads/" . $_FILES["file_img"]["name"]))
            {
                echo $_FILES["file_img"]["name"] . " 图片已经存在，不要重复上传.";
            }
            else
            {
                $sqlwhere = "uploads/" .$openidg;

                if($_FILES["file_img"]["type"] =="image/gif")
                {
                    $sqlwhere = $sqlwhere."a.gif";
                    move_uploaded_file($_FILES["file_img"]["tmp_name"],
//                "uploads/" . $_FILES["file"]["name"]);
                        $sqlwhere);

                }else if($_FILES["file_img"]["type"] =="image/jpeg"
                    ||$_FILES["file_img"]["type"] =="image/pjpeg")
                {

                    $sqlwhere =$sqlwhere."a.jpg";
                    move_uploaded_file($_FILES["file_img"]["tmp_name"],
//                "uploads/" . $_FILES["file"]["name"]);
                        $sqlwhere);
                }
                else if($_FILES["file_img"]["type"] =="image/png")
                {
                    $sqlwhere =$sqlwhere."a.png";
                    move_uploaded_file($_FILES["file_img"]["tmp_name"],
//                "uploads/" . $_FILES["file"]["name"]);
                        $sqlwhere);
                }
//            echo "Stored in: ".  $sqlwhere;

                $sqlok = "update mxt_pacer_month_form a set a.photo_a='$sqlwhere'  where openid ='$openidg'";

//            echo $sqlok;
                $queryok = mysql_query($sqlok);
//            $rsok = mysql_fetch_array($queryok);


                $sql_mxt_pacer_month_form2 = "select * from mxt_pacer_month_form where openid ='$openidg'";
                $query4 = mysql_query($sql_mxt_pacer_month_form2);
                $rs4 = mysql_fetch_array($query4);

            }
        }
    }
    else
    {
        $abool = false;
        $checkstr = $_FILES["file_img"]["type"]."非图片格式、上传图片为空或者上传的不是gif、jpeg、png格式的图片，请您重新上传";
    }
}
else
{
    $abool = false;
    $checkstr = "图片大于4M,请重新上传";
}

?>

<?php

//①、获取用户openid
$tools = new JsApiPay();
//$openId = $tools->GetOpenid();
$userInfo = $tools->GetUserInfoParameters();
//获取用户状态
DBOperater::checkUserStatus($userInfo);

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

            if($abool)
            {
                echo "图片上传成功";
            }
            else
            {
                echo $checkstr;
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





