# ifcert 全国网贷机构实时交易数据SDK

* 本SDK 运行最底要求 PHP 版本 5.4 , 建议在 PHP7 上运行以获取最佳性能；

### 功能描述

* 根据全国网贷机构交易数据手册，封装了如下功能
1. 围绕散标的功能
* 用户信息

* 散标信息

* 散标状态

* 转让信息

* 转让状态

* 交易流水

* 还款计划

* 初始债权

* 承接转让
2. 围绕出借人的功能
* 产品信息

* 产品配置

* 投资明细
3. 数据查询接口
* 批次异步消息

* 每天推送批次数接口

* 推送批次列表接口

## 技术帮助

> 技术支持QQ（115376835）

> 如有需要帮助的地方也可以联系我们

>  [http://www.emmetltd.com/](http://www.emmetltd.com/)

## 环境准备及加载安装

> 本SDK遵循 PSR-2 命名规范和 PSR-4 自动加载规范，强烈推荐使用 composer 来管理项目扩展库。

## Composer 准备工作

> 如果还没有安装 Composer，你可以按 这里提到方法安装 在 Linux 和 Mac OS X 中可以运行如下命令：

```

curl -sS https://getcomposer.org/installer | php

mv composer.phar /usr/local/bin/composer
```

> 在 Windows 中，你需要下载并运行[Composer-Setup.exe](https://getcomposer.org/Composer-Setup.exe)；

> 如果遇到任何问题或者想更深入地学习 Composer，请参考[Composer 文档（英文)](https://getcomposer.org/doc/)，[Composer 中文](http://www.kancloud.cn/thinkphp/composer)。

> 由于众所周知的原因，国外的网站连接速度很慢。因此安装的时间可能会比较长，我们建议通过下面的方式使用国内镜像。

> 打开命令行窗口（windows用户）或控制台（Linux、Mac 用户）并执行如下命令：

```

composer config -g repo.packagist composer https://packagist.phpcomposer.com
```

## 通过 Composer 安装 全球网贷机构实时交易数据SDK

> 进入项目根目录下

~~~
composer require emmetltd/ifcert

## 初始化客户端
```
//请求接口
use Emmetltd\ifcert\src\Client;
//查询接口
use Emmetltd\ifcert\src\Record;
```
## 使用
```
$client = new Client($apikey_cncert,$sourceCode_cncert,$debug)
$signle_data = [[
	'userType'=>'1',
	'userAttr'=>'3',
	'userCreateTime'=>'2019-05-06 12:01:56',
	'userName'	=> '张三',
	'countries' => '1',
	'cardType'	=> '1',
	'userSex'	=> '1',
	'userIdcard'=>'340403199512111856',
	'userPhone' => '177194962541',
        'userList'  => [['userBankAccount'=>'6228480240389521611']],
]];//个人数据
$res = $client->userInfoRequest($signle_data,$batchNum);
echo $res;
```
*client 参数说明：*
> $apikey_cncert：平台密钥
> $sourceCode_cncert：平台编码
> $debug：是否开启测试接口  传1会请求测试接口

**具体编码申请和api密钥请参照 https://open.ifcert.org.cn/login**
*userInfoRequest 参数说明*
> $signle_data：具体参数请看程序与功能模块
> $batchNum：批次号，不传会自动生成

## 接口说明
| 方法名| 功能 
| --- | --- | 
|   userInfoRequest  |  用户信息
   |   scatterInvestRequest  |  散标信息
   | statusRequest| 散标状态
|   repayPlanRequest   |   还款计划  |
|   creditorRequest   |     初始债权
|     transferProjectRequest |    转让信息
 |    transferStatusRequest  |     转让状态
|   underTakeRequest   |     承接转让
|    transactRequest  |     交易流水
|    lendProductRequest  |     产品信息
|    lendProductConfigRequest  |     产品配置
|   lendParticularsRequest   |     投资明细


