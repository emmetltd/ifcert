<?php
namespace Emmetltd\ifcert\src;
use \Emmetltd\ifcert\tools\Tools;
use \Emmetltd\ifcert\tools\HttpHelper;
/**
 * 实际操作类型
 */
const BASE_URL = "https://api.ifcert.org.cn/p2p/";	//请求地址

class Client{
	protected $tools;									//工具类
	protected $http;									//网络工具
	protected $param;									//应用参数
	protected $debug;									//调试模式
	protected $apikey_cncert;							//平台密钥
	protected $sourceCode_cncert;						//平台号
	protected $version = "1.5";							//版本号
	protected $action;									//请求地址
	
	function __construct($apikey_cncert,$sourceCode_cncert,$debug=false){
		$this->apikey_cncert = $apikey_cncert;
		$this->sourceCode_cncert = $sourceCode_cncert;
		$this->tools = new Tools;
		$this->http = new HttpHelper;
		$this->debug = $debug?'/test':'';
		$this->data_type = $debug?'0':'1';
		$this->param = [
			"version"   => $this->version,
			"batchNum"  => "",
			"checkCode" => "",
			"totalNum"  => "", //本次发送数据总量
			"sentTime"  => "",
			"sourceCode"=> $this->sourceCode_cncert,
			"infType"   => "1",
			"dataType"  => $this->data_type,
			"timestamp" => "",
			"nonce"	    => ""
		];
	}
	/**
	 * 完成公共参数
	 * @param $
	 * @param $ 
	 * @return Respond
	 */	
	function completeParam($sign,$batchNum,$infType){
		$time = time();
		$this->param["batchNum"]  = $batchNum?$batchNum:$this->tools->batchNumber($this->sourceCode_cncert,date('Ymd',$time),1,$this->tools->setSeqId());
		$this->param['sentTime']  = date('Y-m-d H:i:s',$time);
		$this->param["infType"]   = $infType;
		$this->param["nonce"]	  = $sign->nonce;
		$this->param['timestamp'] = (string)$sign->timestamp;
	}
	
	/**
	 * 完成返回参数
	 * @param $
	 * @param $ 
	 * @return Respond
	 */	
	function completeResponse($sign,$msg,$url){
		$this->param['checkCode'] = $this->tools->checkCode(json_encode($msg['dataList']));
		$this->param['totalNum']  = (string)count($msg['dataList']);
		$rquest_data = array_merge($this->param,$msg);
		$response = $this->http->request_post(BASE_URL.$url.$this->debug,[
			'apiKey'=>$sign->apiKey,
			'msg'	=>json_encode($rquest_data),
		]);		
		$result = json_decode($response,true);
		if($result['code']=='0000'){
			$result['batchNum'] = $this->param['batchNum'];
		}
		return json_encode($result);
	}

	// //用户信息
	 function userInfoRequest($userInfo,$batchNum=""){
		 $sign = json_decode($this->tools->getApiKey($this->apikey_cncert,$this->sourceCode_cncert,$this->version));
		 $this->completeParam($sign,$batchNum,'1');
		 $msg = [];
		 foreach($userInfo as $list){
			 $list['sourceCode'] = $this->sourceCode_cncert;
			 $list['version']	 = $this->version;
			 $list['userPhone'] 		= substr_replace($list['userPhone'],'****',7,4);
			 $phoneHash 				= $this->tools->phoneHash($list['userPhone']);
			 $list['userPhoneHash'] 	= $phoneHash['phone'];
			 $list['userUuid'] 			= $phoneHash['salt'];			 
			 switch($list['userType']){
				 case '1': //自然人
					 $list['userLawperson'] 	= '-1';
					 $list['userFund'] 			= '-1';
					 $list['userProvince'] 		= '-1';
					 $list['userAddress'] 		= '-1';
					 $list['registerDate'] 		= '-1';
					 $list['userIdcard'] 		= substr_replace($list['userIdcard'],'****',14,4);
					 $list['userIdcardHash'] 	= $this->tools->idCardHash($list['userIdcard']);
					 break;
				 case '2': //企业
					 $list['userProvince']  	= $this->tools->getCompanyAscription($list['userIdcard']);
					 $list['userSex']			= '-1';
					 $list['userIdcardHash'] 	= $this->tools->idCardHash($list['userIdcard']);
					 break;
			 }
			 $msg['dataList'][] = $list;
		 }
		 return $this->completeResponse($sign,$msg,'userInfo');
	 }
	


	 //散标信息
	 function scatterInvestRequest($scatterInvest,$batchNum=""){
		 $sign = json_decode($this->tools->getApiKey($this->apikey_cncert,$this->sourceCode_cncert,$this->version));
		 $this->completeParam($sign,$batchNum,'2');
		 $msg = [];
		 foreach($scatterInvest as $list){
			 $list['sourceCode'] = $this->sourceCode_cncert;
			 $list['version']	 = $this->version;			 
			 $list['userIdcardHash'] = $this->tools->idCardHash($list['userIdcardHash']);
			 $msg['dataList'][] = $list;
		 }		 
		 return $this->completeResponse($sign,$msg,'scatterInvest');
	 }

	 //散标状态
	 function statusRequest($status,$batchNum=""){
		 $sign = json_decode($this->tools->getApiKey($this->apikey_cncert,$this->sourceCode_cncert,$this->version));
		 $this->completeParam($sign,$batchNum,'6');
		 $msg = [];
		 foreach($status as $list){
			 $list['sourceCode'] = $this->sourceCode_cncert;
			 $list['version']	 = $this->version;
			 $msg['dataList'][] = $list;
		 }
		 return $this->completeResponse($sign,$msg,'status');
	 }

	 //还款计划
	 function repayPlanRequest($repayPlan,$batchNum=""){
		 $sign = json_decode($this->tools->getApiKey($this->apikey_cncert,$this->sourceCode_cncert,$this->version));
		 $this->completeParam($sign,$batchNum,'81');
		 $msg = [];
		 foreach($repayPlan as $list){
			 $list['sourceCode'] = $this->sourceCode_cncert;
			 $list['version']	 = $this->version;
			 $list['userIdcardHash'] = $this->tools->idCardHash($list['userIdcardHash']);
			 $msg['dataList'][] = $list;
		 }
		 return $this->completeResponse($sign,$msg,'repayPlan');		 
	 }

	 //初始债权
	 function creditorRequest($creditor,$batchNum=""){
		 $sign = json_decode($this->tools->getApiKey($this->apikey_cncert,$this->sourceCode_cncert,$this->version));
		 $this->completeParam($sign,$batchNum,'82');
		 $msg = [];
		 foreach($creditor as $list){
			 $list['sourceCode'] = $this->sourceCode_cncert;
			 $list['version']	 = $this->version;
			 $list['userIdcardHash'] = $this->tools->idCardHash($list['userIdcardHash']);
			 $msg['dataList'][] = $list;			 
		 }
		 return $this->completeResponse($sign,$msg,'creditor');
	 }
	
	
	 //转让信息
	 function transferProjectRequest($transferProject,$batchNum=""){
		$sign = json_decode($this->tools->getApiKey($this->apikey_cncert,$this->sourceCode_cncert,$this->version));
		$this->completeParam($sign,$batchNum,'83');
		$msg=[];
		foreach($transferProject as $list){
			$list['sourceCode'] = $this->sourceCode_cncert;
			$list['version']	 = $this->version;
			$list['userIdcardHash'] = $this->tools->idCardHash($list['userIdcardHash']);
			$msg['dataList'][] = $list;	
		}
		return $this->completeResponse($sign,$msg,'transferProject');
	 }

	// //转让状态
	function transferStatusRequest($transferStatus,$batchNum=""){
		$sign = json_decode($this->tools->getApiKey($this->apikey_cncert,$this->sourceCode_cncert,$this->version));
		$this->completeParam($sign,$batchNum,'84');
		$msg=[];
		foreach($transferStatus as $list){
			$list['sourceCode'] = $this->sourceCode_cncert;
			$list['version']	 = $this->version;
			$msg['dataList'][] = $list;	
		}
		return $this->completeResponse($sign,$msg,'transferStatus');				
	}


	//承接转让
	function underTakeRequest($underTake,$batchNum=""){
		$sign = json_decode($this->tools->getApiKey($this->apikey_cncert,$this->sourceCode_cncert,$this->version));
		$this->completeParam($sign,$batchNum,'85');
		$msg=[];
		foreach($underTake as $list){
			$list['sourceCode'] = $this->sourceCode_cncert;
			$list['version']	 = $this->version;
			$list['userIdcardHash'] = $this->tools->idCardHash($list['userIdcardHash']);
			$msg['dataList'][] = $list;	
		}				
		return $this->completeResponse($sign,$msg,'underTake');
	}


	// //交易流水
	function transactRequest($transact,$batchNum=""){
		$sign = json_decode($this->tools->getApiKey($this->apikey_cncert,$this->sourceCode_cncert,$this->version));
		$this->completeParam($sign,$batchNum,'4');
		$msg=[];
		foreach($transact as $list){
			$list['sourceCode'] = $this->sourceCode_cncert;
			$list['version']	 = $this->version;
			$list['userIdcardHash'] = $this->tools->idCardHash($list['userIdcardHash']);
			$msg['dataList'][] = $list;	
		}				
		return $this->completeResponse($sign,$msg,'transact');		
	}

	//产品信息
	function lendProductRequest($lendProduct,$batchNum=""){
		$sign = json_decode($this->tools->getApiKey($this->apikey_cncert,$this->sourceCode_cncert,$this->version));
		$this->completeParam($sign,$batchNum,'86');
		$msg=[];
		foreach($lendProduct as $list){
			$list['sourceCode'] = $this->sourceCode_cncert;
			$list['version']	 = $this->version;
			$msg['dataList'][] = $list;	
		}				
		return $this->completeResponse($sign,$msg,'lendProduct');	
	}


	//产品配置
	function lendProductConfigRequest($lendProductConfig,$batchNum=""){
		$sign = json_decode($this->tools->getApiKey($this->apikey_cncert,$this->sourceCode_cncert,$this->version));
		$this->completeParam($sign,$batchNum,'87');
		$msg=[];
		foreach($lendProductConfig as $list){
			$list['sourceCode'] = $this->sourceCode_cncert;
			$list['version']	 = $this->version;
			$list['userIdcardHash'] = $this->tools->idCardHash($list['userIdcardHash']);
			$msg['dataList'][] = $list;	
		}				
		return $this->completeResponse($sign,$msg,'lendProductConfig');			
	}

	//投资明细
	function lendParticularsRequest($lendParticulars,$batchNum=""){
		$sign = json_decode($this->tools->getApiKey($this->apikey_cncert,$this->sourceCode_cncert,$this->version));
		$this->completeParam($sign,$batchNum,'88');
		$msg=[];
		foreach($lendParticulars as $list){
			$list['sourceCode'] = $this->sourceCode_cncert;
			$list['version']	 = $this->version;
			$list['userIdcardHash'] = $this->tools->idCardHash($list['userIdcardHash']);
			$msg['dataList'][] = $list;	
		}				
		return $this->completeResponse($sign,$msg,'lendParticulars');		
	}

}




