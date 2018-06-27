<?php
/**
 * Created by PhpStorm.
 * User: icharle
 * Date: 2018/6/26
 * Time: 下午4:01
 */

namespace Icharle\Wxtool;

class Common
{

    /**
     * @var int
     * 定义错误代码
     * -41001: encodingAesKey 非法
     * -41003: aes 解密失败
     * -41004: 解密后得到的buffer非法
     * -41005: base64加密失败
     * -41016: base64解密失败
     *  40029: 临时登录凭证（code）无效
     */
    public static $OK = 0;
    public static $IllegalAesKey = -41001;
    public static $IllegalIv = -41002;
    public static $IllegalBuffer = -41003;
    public static $DecodeBase64Error = -41004;
    public static $Illegalcode = 40029;

    /**
     * @param $url
     * @param null $data
     * @return bool|mixed
     * 请求接口方法
     */
    public static function curl($url, $data = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        if ($output === FALSE) {
            return false;
        }
        curl_close($curl);
        return $output;
    }


}