<?php
/**
 * Created by PhpStorm.
 * User: icharle
 * Date: 2018/6/26
 * Time: 下午3:36
 */

namespace Icharle\Wxtool;

class Userinfo
{

    /**
     * @var 定于变量
     */
    private $wxappid;
    private $wxsecret;
    private $wxcodeurl;
    private $sessionKey;


    /**
     * Userinfo constructor.
     * 构造函数
     */
    public function __construct()
    {
        $this->wxappid = config('wxtool.wx_appid');
        $this->wxsecret = config('wxtool.wx_secret');
        $this->wxcodeurl = config('wxtool.wx_code_url');
        $this->wxtokenurl = config('wxtool.wx_token_url');
        $this->wxpicurl = config('wxtool.wx_pic_url');
    }


    /**
     * @param $code
     * @return array|bool|mixed
     * 获取session_key、openid
     */
    public function GetSessionKey($code)
    {
        $code_url = sprintf($this->wxcodeurl, $this->wxappid, $this->wxsecret, $code);
        $userInfo = json_decode(Common::curl($code_url), true);
        if (!isset($userInfo['session_key'])) {
            return [
                'code' => Common::$Illegalcode,
                'msg' => '获取 session_key 失败',
            ];
        }
        $this->sessionKey = $userInfo['session_key'];
        return $userInfo;
    }


    /**
     * @param $encryptedData
     * @param $iv
     * @param null $sessionKey
     * @return array|string
     * 用户详细信息的解密
     */
    public function GetUserInfo($encryptedData, $iv, $sessionKey = null)
    {
        if (empty($sessionKey)) {
            $sessionKey = $this->sessionKey;
        }
        $decodeData = "";
        $errorCode = $this->decryptData($encryptedData, $iv, $decodeData, $sessionKey);
        if ($errorCode != Common::$OK) {                    //如果不为0 则直接返回错误代码
            return [
                'code' => $errorCode
            ];
        }
        return $decodeData;
    }


    /**
     * 检验数据的真实性，并且获取解密后的明文.
     * @param $encryptedData string 加密的用户数据
     * @param $iv string 与用户数据一同返回的初始向量
     * @param $data string 解密后的原文
     *
     * @return int 成功0，失败返回对应的错误码
     */
    public function decryptData($encryptedData, $iv, &$data, $sessionKey)
    {
        if (strlen($sessionKey) != 24) {
            return Common::$IllegalAesKey;
        }
        $aesKey = base64_decode($sessionKey);


        if (strlen($iv) != 24) {
            return Common::$IllegalIv;
        }
        $aesIV = base64_decode($iv);

        $aesCipher = base64_decode($encryptedData);

        $result = openssl_decrypt($aesCipher, "AES-128-CBC", $aesKey, 1, $aesIV);

        $dataObj = json_decode($result);
        if ($dataObj == NULL) {
            return Common::$IllegalBuffer;
        }
        if ($dataObj->watermark->appid != $this->wxappid) {
            return Common::$IllegalBuffer;
        }
        $data = $result;
        return Common::$OK;
    }
}