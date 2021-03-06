<?php

    class LoaninvestAction extends HCommonAction
    {
		public function index()
        {	
        	
            $maprow = array();
            $searchMap['status']=array("in",'2,4,6,7,8'); 
            $parm['map'] = $searchMap;
            $parm['pagesize'] =10;
            $sort = "desc";
            $parm['orderby']="l.status ASC,l.id DESC";
            $list = getLoanList($parm);
            $p=$_GET['p'];
            if($p&&$p=1){
            	$result=json_encode($list['list']);
				echo $result;
            }else{
                $this->assign('list', $list);
                $this->display(); 
            }
        }
		
        public function detail(){   
           
            $pre = C('DB_PREFIX');
            $id = intval($_GET['id']);
            $loan = M("loan")->where('id='.$id)->find();
			$loan['has_collect']=$loan['status']>=7?$loan['loan_amount']:$loan['has_collect'];
			$loan['need'] = $loan['loan_amount'] - $loan['has_collect'];
			$loan['progress'] = getFloatValue($loan['has_collect']/$loan['loan_amount']*100,2);
			
            $loan['lefttime'] = $loan['collect_time']- time(); 
            $memberinfo = M("members")
                            ->field("id,customer_name,customer_id,user_name,reg_time,credits")
                            ->where("id={$loan['uid']}")
                            ->find();
			
			$parm['map']="l.loan_id={$id}";
			$parm['orderby']="l.invest_time DESC";
			$list = getLoanRecordList($parm);
			
			
			$str_duration = $loan['duration_type'] == 0?"day":"month";
		
			$loan['interest_time'] =  strtotime("+{$loan['collect_day']}"."day",$loan['birth_time']);
			$loan['finish_time'] = strtotime("+{$loan['loan_duration']} ".$str_duration,$loan['interest_time']);
		
			$loan['birth_time'] = date('Y-m-d',$loan['birth_time']);
			$loan['interest_time']= date('Y-m-d',$loan['interest_time']);
			$loan['finish_time']  = date('Y-m-d',$loan['finish_time']);
			$loan['collect_time'] = date("Y/m/d H:i:s",$loan['collect_time']);
				$schedule = M("loan_schedule") ->find($id);
		
			if(isset($schedule)){
				$schedule['cancel_warrants'] = !$schedule['cancel_warrants']?"":date('Y-m-d',$schedule['cancel_warrants']);
				$schedule['fund_trust']= !$schedule['fund_trust']?"":date('Y-m-d',$schedule['fund_trust']);
				$schedule['reowner']  =!$schedule['reowner']?"":date('Y-m-d',$schedule['reowner']);
			}
			$loan_config=require C("APP_ROOT")."Conf/loan_config.php";
			$img_types=$loan_config['img_type'];
			$images[]=M('loan_img')->where("lid={$id} and type=0")->select();
			$images[]=M('loan_img')->where("lid={$id} and type=1")->select();
			$images[]=M('loan_img')->where("lid={$id} and type=2")->select();
			$config = require C("APP_ROOT")."Conf/config.php";
			$loan['repay_type']=$config['REPAY_TYPE'][$loan['repay_type']];
			
			$this->assign("list",$list);
			$this->assign("images",$images);
			$this->assign("img_types",$img_types);
			$this->assign("vo", $loan); 
            $this->assign("minfo",$memberinfo);
			$this->assign("schedule",$schedule);
            //$this->assign("Bconfig",$Bconfig);
            //$this->assign("gloconf",$this->gloconf);
            $this->display();
           
        }
        
        /**
        * 手机普通标投资
        */
        public function Invest(){   
        	
    		if (!$this->uid)   $this->error("请先登录",__APP__."/M/pub/login");
			$loan_id = $this->_get('id');
			$loan = M("loan")
				->where("id='{$loan_id}'")
				->find();
			if ($this->uid == $loan['loan_uid']) $this->error("不能对自己的项目进行投资！");
			$loan['need'] = $loan['loan_amount'] - $loan['has_collect'];
			$this->assign('vo', $loan);    
			
			$user_info = M('member_money')
							->field("account_money")
							->where("uid='{$this->uid}'")
							->find();
			$this->assign('user_info', $user_info);
			$paypass = M("members")->field('pin_pass')->where('id='.$this->uid)->find();
			$this->assign('paypass', $paypass['pin_pass']);
			$this->display();          
        }
		public function doinvest(){
			$uid = $this->uid;
			$loan_id = text($_POST['id']);
			$paypass = md5($_POST['paypass']);
			$money=text($_POST['invest_money']);
			$data['invest_amount'] = $money;
			
			$loan= M('loan')->where("id = {$loan_id}")->find();
			$loan['status'] != 2&&ajaxmsg("该项目不在招标中",1);
			$m_money = M('member_money')->getFieldByUid($uid,"account_money");
			$members = M('members')->field("pin_pass")->where("id = {$uid}")->find();
			
			time()>$loan['collect_time']&&ajaxmsg("募集已截止",1);
			
			//判断支付密码
			$members['pin_pass'] != $paypass&&ajaxmsg("支付密码错误",1);
			
			$loan['has_collect'] = $loan['has_collect'] + $data['invest_amount'] ;
			$loan['has_collect'] > $loan['loan_amount']&&ajaxmsg("所投金额超出融资上限",1);
			if ($loan['has_collect'] == $loan['loan_amount']) $loan['status'] = 4;
			//投资金额操作
			$loan['min_invest']>$data['invest_amount']&&ajaxmsg("投资金额不能低于{$loan['min_invest']}元",1);
			$data['invest_amount'] > $m_money&&ajaxmsg("余额不足以支付,请先充值",1);
			!memberMoneyLog($uid,6,-$data['invest_amount'],"对{$loan_id}号{$loan['loan_name']}进行投标，冻结金额{$data['invest_amount']}元！")&&ajaxmsg("投标失败",1);
			//保存has_borrow
			!M('loan')->save($loan)&&ajaxmsg("投标失败",1);
			//保存投资表
			$data['loan_id'] = $loan_id;
			$data['invest_uid'] = $uid;
			$data['invest_time'] = time();			
			if (M('loan_invest')->add($data)) 
				ajaxmsg("投标成功",0);
			else 
				ajaxmsg("投标失败",1);
			
		}
    }
?>
