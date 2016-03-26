<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <meta name="keywords" content="ajax表单提交，表单验证，jquery" />
    <meta name="description" content="Helloweba提供在线演示HTML、CSS、jquery、PHP案例和示例" />
    <title>演示：Ajax表单提交插件jquery form</title>
    <link rel="stylesheet" type="text/css" href="http://www.helloweba.com/demo/css/main.css" />
    <style type="text/css">
        .demo{width:420px; margin:30px auto 0 auto}
        .demo p{height:42px; line-height:42px}
        .input{width:200px; line-height:24px; height:24px; padding:2px; border:1px solid #d3d3d3}
        .btn{-webkit-border-radius: 3px;-moz-border-radius:3px;padding:6px 16px; margin-top:6px; cursor:pointer;background: #360;border: 1px solid #390;color:#fff}
        #msg{margin-left:30px; line-height:24px; color:#f30}
        #output{margin-top:10px}
    </style>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <script type="text/javascript" src="jquery.form.min.js"></script>
    <script type="text/javascript">
        $(function(){
            var options = {
                // target:        '#output',   // target element(s) to be updated with server response
                beforeSubmit:  showRequest,  // pre-submit callback
                success:       showResponse,  // post-submit callback
                resetForm: true,
                dataType:  'json'

                // other available options:
                //url:       url         // override for form's 'action' attribute
                //type:      type        // 'get' or 'post', override for form's 'method' attribute
                //dataType:  null        // 'xml', 'script', or 'json' (expected server response type)
                //clearForm: true        // clear all form fields after successful submit
                //resetForm: true        // reset the form after successful submit

                // $.ajax options can be used here too, for example:
                //timeout:   3000
            };

            // bind to the form's submit event
            $('#my_form').submit(function() {
                // inside event callbacks 'this' is the DOM element so we first
                // wrap it in a jQuery object and then invoke ajaxSubmit
                $(this).ajaxSubmit(options);

                // !!! Important !!!
                // always return false to prevent standard browser submit and page navigation
                return false;
            });
        });
        // pre-submit callback
        function showRequest(formData, jqForm, options) {
            var uname = $("#uname").val();
            if(uname==""){
                $("#msg").html("姓名不能为空！");
                return false;
            }

            var age = $("#age").val();
            if(age==""){
                $("#msg").html("年龄不能为空！");
                return false;
            }
            $("#msg").html("正在提交...");


            return true;
        }

        // post-submit callback
        function showResponse(responseText, statusText)  {
            $("#msg").html('提交成功');
            var sex = responseText.sex==1?"男":"女";
            $("#output").html("姓名："+responseText.uname+"&nbsp;性别："+sex+"&nbsp;年龄："+responseText.age);
            // for normal html responses, the first argument to the success callback
            // is the XMLHttpRequest object's responseText property

            // if the ajaxSubmit method was passed an Options Object with the dataType
            // property set to 'xml' then the first argument to the success callback
            // is the XMLHttpRequest object's responseXML property

            // if the ajaxSubmit method was passed an Options Object with the dataType
            // property set to 'json' then the first argument to the success callback
            // is the json data object returned by the server

            //alert('status: ' + statusText + '\n\nresponseText: \n' + responseText +
            //    '\n\nThe output div should have already been updated with the responseText.');
        }
    </script>
</head>

<body>
<div id="header">
    <div id="logo"><h1><a href="http://www.helloweba.com" title="返回helloweba首页">helloweba</a></h1></div>
    <div class="demo_topad"><script src="/js/ad_js/demo_topad.js" type="text/javascript"></script></div>
</div>

<div id="main">
    <h2 class="top_title"><a href="http://www.helloweba.com/view-blog-236.html">Ajax表单提交插件jquery form</a></h2>

    <div class="demo">
        <form id="my_form" action="submit.php" method="post">
            <p>姓名：<input type="text" name="uname" id="uname" class="input"></p>
            <p>性别：<input type="radio" name="sex" value="1" checked> 男 <input type="radio" name="sex" value="2"> 女 </p>
            <p>年龄：<input type="text" name="age" id="age" class="input" style="width:50px"></p>
            <p style="margin-left:30px"><input type="submit" class="btn" value="提交"><span id="msg"></span></p>
        </form>
    </div>
    <div id="output"></div>

    <br/><div class="ad_76090"><script src="/js/ad_js/bd_76090.js" type="text/javascript"></script></div><br/>
</div>
<div id="footer">
    <p>Powered by helloweba.com  允许转载、修改和使用本站的DEMO，但请注明出处：<a href="http://www.helloweba.com">www.helloweba.com</a></p>
</div>

<p id="stat"><script type="text/javascript" src="/js/tongji.js"></script></p>
</body>
</html>
