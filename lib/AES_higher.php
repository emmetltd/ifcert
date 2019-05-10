<?php
namespace Emmetltd\ifcert\lib;
/**
 * Created by PhpStorm.
 * User: weifuqing
 * Date: 2019/4/3
 * Time: 5:15 PM
 */

class AES_higher{


    private $secret_key = 'HTTPSIFCERTORGCN';
    private $method;
    private $iv;
    private $options;


    public function __construct($key = 'HTTPSIFCERTORGCN', $method = 'AES-128-ECB', $iv = '', $options = 0)
    {
        // key是必须要设置的
        $this->secret_key = $key;

        $this->method = $method;

        $this->iv = $iv;

        $this->options = $options;
    }

    /**
     * 加密方法，对数据进行加密，返回加密后的数据
     *
     * @param string $data 要加密的数据
     *
     * @return string
     *
     */
    public function encrypt($data)
    {
        return openssl_encrypt($data, $this->method, $this->secret_key, $this->options, $this->iv);
    }

    /**
     * 解密方法，对数据进行解密，返回解密后的数据
     *
     * @param string $data 要解密的数据
     *
     * @return string
     *
     */
    public function decrypt($data)
    {
        return openssl_decrypt($data, $this->method, $this->secret_key, $this->options, $this->iv);
    }

}