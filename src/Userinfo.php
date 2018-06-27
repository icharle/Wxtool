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
     * 检验数据的真实性，并且获取解密后的明文.
     * @param $encryptedData string 加密的用户数据
     * @param $iv string 与用户数据一同返回的初始向量
     * @param $data string 解密后的原文
     *
     * @return int 成功0，失败返回对应的错误码
     */
    public static function decryptData($encryptedData, $iv, &$data, $sessionKey, $wxappid)
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
        if ($dataObj->watermark->appid != $wxappid) {
            return Common::$IllegalBuffer;
        }
        $data = $result;
        return Common::$OK;
    }
}