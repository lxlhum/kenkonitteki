

<!DOCTYPE html>
<!-- saved from url=(0048)http://v3.bootcss.com/examples/starter-template/ -->
<html lang="zh-CN"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- 上述3个meta标签*必须*放在最前面，任何其他内容都*必须*跟随其后！ -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="http://v3.bootcss.com/favicon.ico">

    <title>我的商城</title>

    <!-- Bootstrap core CSS -->
    <link href="//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet">


</head>

<body>

<div class="container">
    <div class="starter-template">
        <?php
         $a = $_GET['code'];
        //echo $a;
        $url='https://api.weixin.qq.com/sns/oauth2/access_token?appid=wx1d6c6e68f1887cd2&secret=86f663c941c1764d832b2b143dd04158&code='.$a.'&grant_type=authorization_code';
        echo  $url;
        $json_ret = file_get_contents($url);
        //echo $json_ret;

        $result = json_decode($json_ret, true);

        //echo $result;

        //$refresh_token = $json_ret['refresh_token'];
        $openid = $result["openid"];
//        echo $openid."\r\n";
        //
        $access_token = $result["access_token"];
//        echo $access_token."\r\n";

        //echo '8888888888,$refresh_token:'.$refresh_token;


        //$url2='https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=wx1d6c6e68f1887cd2&grant_type=refresh_token&refresh_token='.$refresh_token;
        //$json_ret2 = file_get_contents($url2);
        //echo $json_ret2;


        $url2='https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid;
        $json_ret2 = file_get_contents($url2);
        echo $json_ret2;
        $result2 = json_decode($json_ret2, true);

        $nickname = $result2["nickname"];
        echo "nickname:".$nickname."\r\n";
        $sex=$result2["sex"];
        echo "sex".$sex."\r\n";
        $language=$result2["language"];
        echo "language".$language."\r\n";
        $city=$result2["city"];
        echo "city".$city."\r\n";
        $province=$result2["province"];
        echo "province".$province."\r\n";
        $country=$result2["country"];
        echo "country".$country."\r\n";
        $headimgurl=$result2["headimgurl"];
        echo "headimgurl".$headimgurl."\r\n";
        //https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx1d6c6e68f1887cd2&redirect_uri=http%3A%2F%2Fwww.yangtz.com%2Fyangtzpay%2Fexample%2Ftestoauth.php&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect
        ?>
    </div>
</div><!-- /.container -->

<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>
<script src="//cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>


</body></html>


