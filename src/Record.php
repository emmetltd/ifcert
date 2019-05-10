<?php
namespace Emmetltd\ifcert\src;
use \Emmetltd\ifcert\tools\Tools;
use \Emmetltd\ifcert\tools\HttpHelper;
/**
 * 实际操作类型
 */
const ASYN_URL  = "https://api.ifcert.org.cn/balanceService/v15/batchMessage";//批次异步消息
const DAYLI_URL = "https://api.ifcert.org.cn/balanceService/v15/batchNum";	 //每天推送批次数
const PUSH_URL  = "https://api.ifcert.org.cn/balanceService/v15/batchList";	//推送批次列表

class Record{
	protected $tools;									//工具类
	protected $http;									//网络工具
	protected $param;									//应用参数
	protected $apikey_cncert;							//平台密钥
	protected $sourceCode_cncert;						//平台号
	protected $version = "1.5";							//版本号
	
	function __construct($apikey_cncert,$sourceCode_cncert,$debug=false){
		$this->apikey_cncert = $apikey_cncert;
		$this->sourceCode_cncert = $sourceCode_cncert;
		$this->tools = new Tools;
		$this->http = new HttpHelper;
		$this->data_type = $debug?'0':'1';
		$this->param = [
			'dataType'	=>$this->data_type,			
			'apiKey'	=>$this->apikey_cncert,
			'timestamp'	=>'',
			'nonce'		=> '',
			'sourceCode'=>$this->sourceCode_cncert,
			'version'	=>$this->version,
			'infType'	=>''
		];
	}
	/**
	* 完成参数
	* @param 
	* @param 
	* @return 返回结果
	*/
	function completeParam(){
		$sign = json_decode($this->tools->getApiKey($this->apikey_cncert,$this->sourceCode_cncert,$this->version));
		$this->param["nonce"]	  = $sign->nonce;
		$this->param['timestamp'] = $sign->timestamp;
	}


	/**
	 * 批次异步消息
	 * @param $
	 * @param $ 
	 * @return Respond
	 */	
	function batchMessage($batchNum,$infType){
		$this->completeParam();
		$this->param['batchNum'] = $batchNum;
		$this->param['infType']  = $infType;
		$response = $this->http->request_get(ASYN_URL.'?'.http_build_query($this->param));
		return $response;
	}

	/**
	* 每天推送批次数
	* @param $
	* @param $
	* @return 返回结果
	*/
	function batchNum($sentDate,$infType){
		$this->completeParam();
		$this->param['sentDate'] = $sentDate;
		$this->param['infType']  = $infType;
		$response = $this->http->request_get(DAYLI_URL.'?'.http_build_query($this->param));
		return $response;
	}

	/**
	* 推送批次列表
	* @param 
	* @param 
	* @return 返回结果
	*/
	function batchList($sentDate,$pageNum,$infType,$putType){
		$this->completeParam();
		$this->param['sentDate']  = $sentDate;
		$this->param['pageNum']  = $pageNum;
		$this->param['infType']  = $infType;
		$this->param['putType']  = $putType;
		$response = $this->http->request_get(DAYLI_URL.'?'.http_build_query($this->param));
		return $response;
	}

}




