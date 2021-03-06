<?php
    /**
    * 手机版用户中心公共类
    */
    class MobileAction extends Action
    {
        public $uid;
        public $uname;
        public $glo;
        var $pre =NULL;
        
        function _initialize(){
			$datag = get_global_setting();
			$this->glo = $datag;//供PHP里面使用
			$this->assign("glo",$datag);//公共参数
        	$this->pre =C('DB_PREFIX');
	        
			if(session('u_id')){
				$this->uid = session('u_id');
				$this->uname = session('u_user_name');
				$this->assign('uid', $this->uid);
				$this->assign('uname', $this->uname);
			}elseif($this->notneedlogin!== true){
				if($_GET['invite']){//手机推广链接传手机		
					session('invite',$_GET['invite']);
					$this->redirect('M/Pub/regist');
				}
					
				$this->redirect('M/Pub/login');
			}elseif($_SESSION['invite']){//电脑推广链接传手机
			    //$this->redirect('M/Pub/regist');	死循环	
		    }
           
        }
		public function sendphone(){

			$smsTxt = FS("Webconfig/smstxt");
			$smsTxt=de_xie($smsTxt);
			$phone = text($_POST['phone']);
			$type = text($_POST['type']);
			switch ($type){
				//注册
				case 0:
					$xuid = M('members')->getFieldByUserPhone($phone,'id');
					if(!!$xuid) ajaxmsg("该号码已被注册",2);
					
					$code = rand_string($phone,6,1,2);//用户注册时无id,用手机号代替id
					
					
					$res=sendsms($phone,array($code),23229);
						
					
					if($res){
						session("temp_phone",$phone);
						ajaxmsg("短信发送成功",1);
					}
					else ajaxmsg("短信发送失败,请重试",0);
					
				break;
				//登录密码
				case 1:
					$xuid = M('members')->getFieldByUserPhone($phone,'id');
					if(!$xuid) ajaxmsg("该号码尚未注册",1);
					
					$code = rand_string($xuid,6,1,2);//用户注册时无id,用手机号代替id
					$res=sendsms($phone,array($code),23229);
					if($res){
						session("temp_phone",$phone);
						ajaxmsg("短信发送成功",0);
					}
					else ajaxmsg("短信发送失败,请重试",1);
				break;
				//支付密码
				case 2:
					$xuid = M('members')->getFieldByUserPhone($phone,'id');
					if(!$xuid) ajaxmsg("该号码尚未注册",1);
					if($xuid!=$this->uid) ajaxmsg("数据异常",1);		
					$code = rand_string($this->uid,6,1,2);
					
					
					$res=sendsms($phone,array($code),23229);
						
					
					if($res){
						session("temp_phone",$phone);
						ajaxmsg("短信发送成功",0);
					}
					else ajaxmsg("短信发送失败,请重试",1);
				break;
			}
		

		}
		
    }
?>
