<?php
/**
 * Created by PhpStorm.
 * User: icharle
 * Date: 2018/6/26
 * Time: 下午3:24
 */

namespace Icharle\Wxtool;


class Wxtool
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
    }

    /**
     * @param $scene 场景值
     * @param $page  页面值
     * @return bool|string
     */
    public function GetQrcode($scene, $page)
    {
        $Qrcode = new Qrcode();
        $imgpath = $Qrcode->GetCodeUnlimit($scene, $page);
        return $imgpath;
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
        $errorCode = Userinfo::decryptData($encryptedData, $iv, $decodeData, $sessionKey, $this->wxappid);
        if ($errorCode != Common::$OK) {                    //如果不为0 则直接返回错误代码
            return [
                'code' => $errorCode
            ];
        }
        return $decodeData;
    }

}