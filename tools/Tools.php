<?php
namespace Emmetltd\ifcert\tools;

use Emmetltd\ifcert\lib\AES_higher;
use Emmetltd\ifcert\lib\PreBcrypt;
use Emmetltd\ifcert\lib\IdcardLocation;

/**
 * 错误码定义
 */
define('OPENAPI_ERROR_REQUIRED_PARAMETER_EMPTY', 1001); // 参数为空
define('OPENAPI_ERROR_REQUIRED_PARAMETER_INVALID', 1002); // 参数格式错误
define('OPENAPI_ERROR_RESPONSE_DATA_INVALID', 1003); // 返回包格式错误
define('OPENAPI_ERROR_VRESION_INVALID', 1004); // version必须是数字型

/**
 * 提供加密数据的工具类
 */
class Tools{
	
	function __construct(){
        
	}
	/**
    .   手机号码所属地取得方法
    .   注册省份所属地取得方法
	 */
    function getPhoneArea($phone){
        // 检查 idcard_name 是否为空
        if (!isset($phone) || empty($phone))
        {
            return array(
                'ret' => OPENAPI_ERROR_REQUIRED_PARAMETER_EMPTY,
                'msg' => 'phone is empty');
        }
        if(strlen($phone)!=11){
            return array(
                'ret' => OPENAPI_ERROR_REQUIRED_PARAMETER_INVALID,
                'msg' => 'phone is false');
        }
        $phoneInfo = new PhoneInfo();
        return $phoneInfo->getPhoneArea($phone);
    }

    /**
     * 验证身份证号正确性
     *
     */
    function isIdCard($idcard){
        #  转化为大写，如出现x
        $idcard = strtoupper($idcard);
        #  加权因子
        $wi = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
        $ai = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
        #  按顺序循环处理前17位
        $sigma = 0;
        #  提取前17位的其中一位，并将变量类型转为实数
        for ($i = 0; $i < 17; $i++) {
            $b = (int)$idcard{$i};
            #  提取相应的加权因子
            $w = $wi[$i];
            #  把从身份证号码中提取的一位数字和加权因子相乘，并累加
            $sigma += $b * $w;
        }
        #  计算序号
        $sidcard = $sigma % 11;
        #  按照序号从校验码串中提取相应的字符。
        $check_idcard = $ai[$sidcard];
        if ($idcard{17} == $check_idcard) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 根据身份证号获取地址
     *
     */
    public function getLocation($idcard)
    {
        // 检查 idcard_name 是否为空
        if (!isset($idcard) || empty($idcard))
        {
            return array(
                'ret' => OPENAPI_ERROR_REQUIRED_PARAMETER_EMPTY,
                'msg' => 'idcard is empty');
        }
        if(!$this->isIdCard($idcard)){
            return array(
                'ret' => OPENAPI_ERROR_REQUIRED_PARAMETER_INVALID,
                'msg' => 'idcard is false');
        }

        $location = new IdcardLocation();
        return $location->get_addr($idcard,'id');
    }

    /**
     * 根据身份证号获取性别
     *
     */
    public function getSex($idcard)
    {
        // 检查 idcard_name 是否为空
        if (!isset($idcard) || empty($idcard))
        {
            return array(
                'ret' => OPENAPI_ERROR_REQUIRED_PARAMETER_EMPTY,
                'msg' => 'idcard is empty');
        }
        if(!$this->isIdCard($idcard)){
            return array(
                'ret' => OPENAPI_ERROR_REQUIRED_PARAMETER_INVALID,
                'msg' => 'idcard is false');
        }
        $sexint = (int) substr($idcard, 16, 1);
        return $sexint % 2 === 0 ? "0" : "1";//女：男
    }

    /**
     * 根据身份证号获取年龄
     *
     */
    public function getAge($idcard)
    {
        // 检查 idcard_name 是否为空
        if (!isset($idcard) || empty($idcard))
        {
            return array(
                'ret' => OPENAPI_ERROR_REQUIRED_PARAMETER_EMPTY,
                'msg' => 'idcard is empty');
        }
        if(!$this->isIdCard($idcard)){
            return array(
                'ret' => OPENAPI_ERROR_REQUIRED_PARAMETER_INVALID,
                'msg' => 'idcard is false');
        }
        #  获得出生年月日的时间戳
        $date = strtotime(substr($idcard,6,8));
        #  获得今日的时间戳
        $today = strtotime('today');
        #  得到两个日期相差的大体年数
        $diff = floor(($today-$date)/86400/365);
        #  strtotime加上这个年数后得到那日的时间戳后与今日的时间戳相比
        $age = strtotime(substr($idcard,6,8).' +'.$diff.'years')>$today?($diff+1):$diff;
        return $age;
    }

    /**
     * 根据身份证号获取生日
     *
     */
    public function getBirthday($idcard)
    {
        // 检查 idcard_name 是否为空
        if (!isset($idcard) || empty($idcard))
        {
            return array(
                'ret' => OPENAPI_ERROR_REQUIRED_PARAMETER_EMPTY,
                'msg' => 'idcard is empty');
        }
        if(!$this->isIdCard($idcard)){
            return array(
                'ret' => OPENAPI_ERROR_REQUIRED_PARAMETER_INVALID,
                'msg' => 'idcard is false');
        }

        $bir = substr($idcard, 6, 8);
        $year = (int) substr($bir, 0, 4);
        $month = (int) substr($bir, 4, 2);
        $day = (int) substr($bir, 6, 2);
        return $year . "-" . $month . "-" . $day;
    }

	/**
	 * 身份证和用户名加密工具
	 *
	 */
	public function idCardHash($idcard_name)
	{
		// 检查 idcard_name 是否为空
		if (!isset($idcard_name) || empty($idcard_name))
		{
			return array(
				'ret' => OPENAPI_ERROR_REQUIRED_PARAMETER_EMPTY,
				'msg' => 'idcard or username is empty');
		}
		
//        $aes = new CryptAES();//mcrypt加密低版本
        $aes = new AES_higher();//ssh加密高版本
        $encText = $aes->encrypt($idcard_name);
        $hashstr = hash("sha256", $encText);
		
		return $hashstr;
	}
    
    /**
	 * 手机号加密工具
	 *
	 */
	public function phoneHash($phone)
	{
		// 检查 phone 是否为空
		if (!isset($phone) || empty($phone))
		{
			return array(
				'ret' => OPENAPI_ERROR_REQUIRED_PARAMETER_EMPTY,
				'msg' => 'phone is empty');
		}
		
        $bcry = new PreBcrypt();
        $salt = $bcry->getSalt();
        
        $hashstr = hash("sha256", $phone .''. $salt);
        
        $phoneBase64 = base64_encode($hashstr);
		
        $hashs = $bcry->hash($phoneBase64);
        
        $phoneH = array('phone'=>$hashs,'salt'=>$salt);
        
		return $phoneH;
	}
	
    /**
	 * 批次生成工具
     * sourceCode 平台编号
	 * tradeDate交易时间
     * seqNum序列号
	 */
	public function batchNumber($sourceCode,$tradeDate,$seqNum,$seqId)
	{
		// 检查 sourceCode 是否为空
		if (!isset($sourceCode) || empty($sourceCode) || !isset($tradeDate) || empty($tradeDate) || !isset($seqNum) || empty($seqNum)
            ||!isset($seqId)||empty($seqId))
		{
			return array(
				'ret' => OPENAPI_ERROR_REQUIRED_PARAMETER_EMPTY,
				'msg' => 'sourceCode or tradeDate or seqNum is empty');
		}
		
        $batch_num = $sourceCode .'_'. $tradeDate .$seqNum .'_'. $seqId ;
        
		return $batch_num;
	}
	
    /**
	 * 获取当前的毫秒数字
	 * @param apiKey
	 * @param source_code
	 * @param version 版本号，如：v1.1-->110; v1.2-->120; v1.3-->130
	 * @return
	 * @throws CertException
	 */	
	function msectime() {
		list($msec, $sec) = explode(' ', microtime());
		return (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
	}		

    /**
	 * apiKey加密
	 * @param apiKey
	 * @param source_code
	 * @param version 版本号，如：v1.1-->110; v1.2-->120; v1.3-->130
	 * @return
	 * @throws CertException
	 */
	public function getApiKey($apiKey,$sourceCode,$version)
	{
		// 检查 apiKey 是否为空
		if (!isset($apiKey) || empty($apiKey) || !isset($sourceCode) || empty($sourceCode) || !isset($version) || empty($version))
		{
			return array(
				'ret' => OPENAPI_ERROR_REQUIRED_PARAMETER_EMPTY,
				'msg' => 'apiKey or sourceCode or version is empty');
		}
        /*
        if(!is_numeric($version))
        {
			return array(
				'ret' => OPENAPI_ERROR_VRESION_INVALID,
				'msg' => 'version is not number');           
        }
        */
        $vs = $version * 100;

//        $currentTime = time() *1000; //修改
		$currentTime = $this->msectime();
		
        $numRand = rand(100000000,999999999);
        $nonce = dechex($numRand);
		
        $versionHex =  '0x'.dechex($vs);
        $str = $sourceCode.$versionHex.$apiKey.$currentTime.$nonce;
        $hashstr = hash("sha256", $str);
        
        $apiKeyH = array('apiKey'=>$hashstr,'timestamp'=>$currentTime, 'nonce'=>$nonce);
        
		return json_encode($apiKeyH);
	}
    
	public function checkCode($msgs){

		return md5($msgs);
    }
    
    public function setSeqId(){
        return substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(1000, 9999));
    }
	
    /**
	 * 用户归属地的行政
	 * @param registrationNumber 三证合一号码
	 * @return
	 * @throws CertException
	 */
	public function getCompanyAscription($registrationNumber){
		$idcardclass = new IdcardLocation;
		return $idcardclass->get_addr($registrationNumber,'code');
	}

}

// end of script
