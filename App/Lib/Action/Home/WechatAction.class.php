<?php
class WechatAction extends HCommonAction {
	
	public function grouplist(){
		$access_token=$this->get_token();
		$url="https://api.weixin.qq.com/cgi-bin/groups/get?access_token=$access_token";
		$ginfos=$this->https_request($url);
		$ginfos=json_decode($ginfos,true);
		$this->assign("ginfos",$ginfos['groups']);
		$this->display();
	}
	public function userlist(){
		set_time_limit(0);
		$access_token=$this->get_token();
		$url="https://api.weixin.qq.com/cgi-bin/user/get?access_token=$access_token";
		$openids=$this->https_request($url);
		$openids=json_decode($openids,true);
		
		$count=$openids['count'];
		$openids=$openids['data']['openid'];
		
		$uinfos=array();
		for($i=0;$i<$count;$i++){
			$openid=$openids[$i];
			$url="https://api.weixin.qq.com/cgi-bin/user/info?access_token=$access_token&openid=$openid&lang=zh_CN";
			$uinfo=$this->https_request($url);
			$uinfo=json_decode($uinfo,true);
			$uinfo['sex']=$uinfo['sex']>1?"女":"男";
			$uinfo['subscribe_time']=date("Y/m/d",$uinfo['subscribe_time']);
			array_push($uinfos,$uinfo);
		}
		dump($uinfos);
		$this->assign("count",$count);
		$this->assign("uinfos",$uinfos);
		$this->display();
	}
	public function editmsg(){
		$repay_types=C("repay_type");
		$names = require C("APP_ROOT")."Conf/loan_config.php";
		$this->assign("repay_types",$repay_types);
		$this->assign("names",$names['type']);
		$this->display();
	}
	public function sendmsg(){
		set_time_limit(0);
		$title=$_POST['title'];
		$id=$_POST['id'];
		$name=$_POST['name'];
		$interest_rate=$_POST['interest_rate'];
		$duration=$_POST['duration'];
		$repay_type=$_POST['repay_type'];
		if(!$id||!$title||!$name||!$interest_rate||!$duration||!$repay_type){
			ajaxmsg("填写的数据不完整",0);
		}
		$access_token=$this->get_token();
		$template=array(
					"touser"=>"oLJhhs-NCgs3VoMb1KsPiWqVcA7I",
					"template_id"=>"FiJKVBywOOtnVgb1-DRQxPnRG3PdZBiryyILbyF_7RE",
					"url"=>"http://www.baicai58.com/M/Loaninvest/detail?id=$id",
					"topcolor"=>"#FF0000",
					"data"=>array(
						"first"=>array(
							"value"=>urlencode($title),
							"color"=>"#DE002A"
						),
						"keyword1"=>array(
							"value"=>urlencode('JK'.$id.$name),
							"color"=>"#173177"
						),
						"keyword2"=>array(
							"value"=>"$interest_rate%",
							"color"=>"#173177"
						),
						"keyword3"=>array(
							"value"=>urlencode($duration),
							"color"=>"#173177"
						),
						"keyword4"=>array(
							"value"=>urlencode($repay_type),
							"color"=>"#173177"
						),
						"remark"=>array(
							"value"=>"",
							"color"=>"#173177"
						)
					)
				);
		$sendNum=0;
		$url="https://api.weixin.qq.com/cgi-bin/user/get?access_token=$access_token";
		$openids=$this->https_request($url);
		$openids=json_decode($openids,true);
		
		$count=$openids['count'];
		$openids=$openids['data']['openid'];
		$sendNum=0;
		for($i=0;$i<$count;$i++){
			$openid=$openids[$i];
			$url="https://api.weixin.qq.com/cgi-bin/user/info?access_token=$access_token&openid=$openid&lang=zh_CN";
		
			$uinfo=$this->https_request($url);
			$uinfo=json_decode($uinfo,true);
			
			$template['touser']=$openid;
			$result=$this->send_template_message(urldecode(json_encode($template)),$access_token);
			if($result)
				$sendNum++;	
		}
		ajaxmsg($sendNum,1);
		
	}
	function https_request($url, $data = null){
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		if (!empty($data)){
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		}
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($curl);
		curl_close($curl);
		return $output;
	}
	
	function get_token(){
		$appid = "wxdc23593bc148db79";
		$appsecret = "bbb6a216c9974c0731e3e4a86ab7eee4";
		$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$appsecret";

		$json=$this->https_request($url);
		$arr=json_decode($json,true);
		return $arr['access_token'];
	}
	function my_json_encode($type, $p){
		if (PHP_VERSION >= '5.4'){
			$str = json_encode($p, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
		}else{
			switch ($type){
				case 'text':
					isset($p['text']['content']) && ($p['text']['content'] = urlencode($p['text']['content']));
                break;
        }
        $str = urldecode(json_encode($p));
		return $str;
		}
	}
	function send_template_message($data,$access_token){
		$url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=$access_token";
		$res=$this->https_request($url,$data);
		return json_decode($res,true);
	}
	function pushj(){
		/*ini_set("display_errors", "On");
		error_reporting(E_ALL | E_STRICT);*/
		$id=$_REQUEST['para1'];
		require_once APP_PATH."Common/JPush/JPush.php";
		$jConfig=C("JPUSH_CONFIG");

		// 初始化
		$client = new JPush($jConfig['app_key'],$jConfig['master_secret']);
       
		$result = $client->push()
	    ->setPlatform('all')
	    ->addAllAudience()
	   // ->setNotificationAlert('新标提醒:JK'.$id.'财日升发布')
	    ->addAndroidNotification('新标提醒:JK'.$id.'财日升发布', '百财网', 1, array("key1"=>"value1", "key2"=>"value2"))
	    ->addIosNotification("新标提醒:JK".$id."财日升发布", 'iOS sound',0, true, 'iOS category', array("key1"=>"value1", "key2"=>"value2"))
	    ->setMessage("msg content", 'msg title', 'type', array("key1"=>"value1", "key2"=>"value2"))
	    ->setOptions(100000, 3600, null, true)
	    ->send();

		echo 'Result=' . json_encode($result).$id . $br;

	}
}
?>