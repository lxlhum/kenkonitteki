<?php
$money = (int)$_POST["account_money_in"];
//echo $money;
ini_set('date.timezone','Asia/Shanghai');
//error_reporting(E_ERROR);
require_once "../lib/WxPay.Api.php";
require_once "WxPay.JsApiPay.php";
require_once "../lib/DBOperater.php";
require_once 'log.php';

//初始化日志
$logHandler= new CLogFileHandler("../logs/".date('Y-m-d').'.log');
$log = Log::Init($logHandler, 15);

//打印输出数组信息
function printf_info($data)
{
    foreach($data as $key=>$value){
        echo "<font color='#00ff55;'>$key</font> : $value <br/>";
    }
}

//①、获取用户openid
$tools = new JsApiPay();
$openId = $_POST["openid_in_money"];
if($openId==null||$openId=="")
{
	$openId = $tools->GetOpenid();
}

//②、统一下单
$input = new WxPayUnifiedOrder();
$input->SetBody("test");
$input->SetAttach("test");
$input->SetOut_trade_no(WxPayConfig::MCHID.date("YmdHis"));
$input->SetTotal_fee($money*100);
$input->SetTime_start(date("YmdHis"));
$input->SetTime_expire(date("YmdHis", time() + 600));
$input->SetGoods_tag("test");
$input->SetNotify_url("http://paysdk.weixin.qq.com/example/notify.php");
$input->SetTrade_type("JSAPI");
$input->SetOpenid($openId);
$order = WxPayApi::unifiedOrder($input);
//echo '<font color="#f00"><b>统一下单支付单信息</b></font><br/>';
//printf_info($order);
$jsApiParameters = $tools->GetJsApiParameters($order);

//获取共享收货地址js函数参数
$editAddress = $tools->GetEditAddressParameters();

//③、在支持成功回调通知中处理成功之后的事宜，见 notify.php
/**
 * 注意：
 * 1、当你的回调地址不可访问的时候，回调通知会失败，可以通过查询订单来确认支付是否成功
 * 2、jsapi支付时需要填入用户openid，WxPay.JsApiPay.php中有获取openid流程 （文档可以参考微信公众平台“网页授权接口”，
 * 参考http://mp.weixin.qq.com/wiki/17/c0f37d5704f0b64713d5d2c37b468d75.html）
 */

//$userInfo = $tools->GetUserInfoParameters();
//获取用户状态
//DBOperater::checkUserStatus($userInfo);

//生成订单id
$order_id = DBOperater::uuid();
//生成预付款订单
DBOperater::creatOrder($openId,1,$money*100,$order_id);
//然后回报成功之后，将预付款单改变为已付款单

?>

<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/> 
    <title>账户充值</title>

	<!-- Bootstrap core CSS -->
	<link href="//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet">

    <script type="text/javascript">
	//调用微信JS api 支付
	function jsApiCall()
	{
		WeixinJSBridge.invoke(
			'getBrandWCPayRequest',
			<?php echo $jsApiParameters; ?>,
			function(res){
				WeixinJSBridge.log(res.err_msg);
				//alert(res.err_code);
				//alert(res.err_code+res.err_desc+res.err_msg);
				if(res.err_msg == "get_brand_wcpay_request:ok"){  
               alert("微信支付成功");

					DBOperater::confirmOrder($openId,$order_id,1,0,2);
           }else if(res.err_msg == "get_brand_wcpay_request:cancel"){  
               alert("用户取消支付");
           }else{  
              alert("支付失败");  
           }
			}
		);
	}

	function callpay()
	{
		if (typeof WeixinJSBridge == "undefined"){
		    if( document.addEventListener ){
		        document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
		    }else if (document.attachEvent){
		        document.attachEvent('WeixinJSBridgeReady', jsApiCall); 
		        document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
		    }
		}else{
//			pr();
		    jsApiCall();
			//var a = document.getElementById("account_money_in").value;
			//alert(a);
		}
	}
	</script>
</head>
<body>
	<?php
//	function pr() {
//		$params = func_get_args();
//		foreach ($params as $key => $value) {
//			echo "<pre>";
//			print_r($value);
//			echo "</pre>";
//		}
//	}

//	<input type="text" id="account_money_in" size="14" maxlength="14" onkeyup='this.value=this.value.replace(/\D/gi,"")'>元</input>

	?>

	<div class="container">
		<div class="starter-template">
			<h1>账户充值</h1>
			<p class="lead">充值金额<?php echo $money; ?></p>

				<button type="button" onclick="callpay()" >微信支付</button>


			<div class="span12">
				<p color="red">注意：微信支付需要扣除千分之六的手续费</p>
				</div>
		</div>


		<div class="starter-template">
			<p>
				或者选择微信二维码扫描支付，微信二维码支付2个小时到账，不扣除手续费
			</p>

			<p>本次支付确认码：<?php echo  $openId.time()?></p>

		</div>


	</div><!-- /.container -->

	<!-- Bootstrap core JavaScript
    ================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->
	<script src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>
	<script src="//cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
</body>
</html>