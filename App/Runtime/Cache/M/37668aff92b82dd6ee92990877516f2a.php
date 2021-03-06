<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0,minimal-ui"> 
	<meta name="full-screen" content="yes">
	<title><?php echo ($glo["index_title"]); ?></title>
	<link type="text/css" rel="stylesheet" href="/Style/Font-Awesome/css/font-awesome.min.css">
	<link type="text/css" rel="stylesheet" href="/Style/JBox/Skins/Currently/jbox.css">
	<link type="text/css" rel="stylesheet" href="/Style/bootstrap-3.3.5/css/bootstrap.min.css">
	<link type="text/css" rel="stylesheet" href="/Style/Mobile/css/common.css">
	<link type="text/css" rel="stylesheet" href="/Style/Mobile/css/index.css">
	<script type="text/javascript" src="/Style/Js/jquery-1.12.1.min.js"></script>
	<script type="text/javascript" src="/Style/bootstrap-3.3.5/js/bootstrap.min.js"></script>
	<script  src="/Style/JBox/jquery.jBox.min.js" type="text/javascript"></script>
	<script src="/Style/JBox/jquery.jBoxConfig.js" type="text/javascript"></script>
	<script type="text/javascript" src="__ROOT__/Style/Mobile/jquery-global.js"></script> 
	<script type="text/javascript" src="/Style/Js/jTools.js"></script>
</head>
<body>
	<header class="container">
			<div class="row">
				<a href="javascript:history.go(-1)" >
					<div class="col-xs-2">
		    			<i  class="fa fa-angle-left text-gray" ></i>
		    		</div>
			   	</a>
			   	<div class="col-xs-8 header-title text-center" id="header-title"></div>
			   	<div class="col-xs-2  text-right">
			   		<div class="dropdown">
						<a data-toggle="dropdown" href="#"><i  class="fa fa-navicon text-gray" style="text-align: right;"></i></a>
						<ul class="dropdown-menu dropdown-menu-right" role="menu" aria-labelledby="dLabel">
							<li>
								<a  href="__APP__/M/user/fund">我的账户</a>
							</li>
							<li class="divider"></li>
							<li>
								<a  href="__APP__/M/user/safe" >安全中心</a>
							</li> 
							<li class="divider"></li>
							<li>
								<a  href="__APP__/M/user/msg" >站内信</a>
							</li>    
							<li class="divider"></li>
							<li>
							<?php if($_SESSION['u_id']): ?><a href="__APP__/M/Pub/logout" style="color:#DE002A">退出账号</a>
							
							<?php else: ?> 
							
								<a href="__ROOT__/M/Pub/login" style="color:#0099FF">请先登录</a><?php endif; ?>
							</li>
						</ul>
					</div>
			   	</div>
			</div>
    </header> 
	<div class="container container-block container-row-nml">
		<a href="__URL__/total">
			<div class="row">
				<div class="col-xs-2"></div>
				<div class="col-xs-8 row-title">网站统计</div>
				<span class="glyphicon glyphicon-menu-right"></span>
			</div>
		</a>
		<a href="__URL__/promotion_list">
			<div class="row">
				<div class="col-xs-2"></div>
				<div class="col-xs-8 row-title">琅琊榜</div>
				<span class="glyphicon glyphicon-menu-right"></span>
			</div>
		</a>
	</div>

<script language="javascript">
 localStorage.title="数据统计";
</script>
﻿<footer>
	<nav>
		<ul class="fmenu">
			<li>
				<a href="<?php echo U('M/Index/index');?>" >
					<i class="fa fa-home"></i>
					<span>首页</span>
				</a>
			</li>
			<li>
				<a href="<?php echo U('M/Loaninvest/index');?>">
					<i class="fa fa-rmb"></i>
					<span>理财</span>
				</a>
			</li>
			<li>
				<a href="<?php echo U('M/Help/index');?>">
					<i class="fa fa-question"></i>
					<span>帮助</span>
				</a>
			</li>
			<li>
				<a href="<?php echo U('M/User/index');?>">
					<i class="fa fa-user"></i>
					<span>我</span>
				</a>
			</li>		
		</ul>
	</nav>
	
</footer>

<script type="text/javascript">  
	$(function () {
			var index=readCookie('index');
			index=index?index:0;
			$('.fmenu>li:eq('+index+')').css({
				'color':'#F08012'
			}).find('a').eq(0).css({'color':'#F08012'});
            $('.fmenu li').each(function () {
                $(this).click(function(){
                    writeCookie('index',$(this).index());
				   
				})
            });

			$("#header-title").text(localStorage.title);

     })

var browser = navigator.userAgent;
function orientationChange() {
    if(browser.indexOf('Android') > -1 || browser.indexOf('Linux') > -1){
        manWidth(screen.width);
    }
    
}

addEventListener('load', function(){ 
    orientationChange();
    window.onorientationchange = orientationChange;
}); 


function writeCookie(name, value) {  
    exp = new Date();  
    exp.setTime(exp.getTime() + (86400 * 1000 * 30)); 
    document.cookie = name + "=" + escape(value) + "; expires=" + exp.toGMTString() + "; path=/";  
}  
function readCookie(name) {  
    var search;  
    search = name + "=";  
    offset = document.cookie.indexOf(search);  
    if (offset != -1) {  
        offset += search.length;  
        end = document.cookie.indexOf(";", offset);  
        if (end == -1){ 
            end = document.cookie.length; 
        } 
        return unescape(document.cookie.substring(offset, end));  
    }else{ 
        return ""; 
    } 
} 

</script> 
</body>
</html>