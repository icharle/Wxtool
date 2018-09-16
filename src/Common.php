<?php
/**
 * Created by PhpStorm.
 * User: icharle
 * Date: 2018/6/26
 * Time: 下午4:01
 */

namespace Icharle\Wxtool;

use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

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
     *  40037: template_id不正确
     *  41028: form_id不正确，或者过期
     *  41029: form_id已被使用
     *  41030: page不正确
     *  45009: 接口调用超过限额（目前默认每个帐号日调用限额为100万）
     */
    public static $OK = 0;
    public static $IllegalAesKey = -41001;
    public static $IllegalIv = -41002;
    public static $IllegalBuffer = -41003;
    public static $DecodeBase64Error = -41004;
    public static $Illegalcode = 40029;
    public static $Illtemplateid = 40037;
    public static $Illformid = 41028;
    public static $Useformid = 41029;
    public static $Illpage = 41030;
    public static $Maxuse = 45009;


    /**
     * @return mixed
     * 获取Token 返回Token
     */
    public function GetAccessToken()
    {
        //如果存在token值  直接返回
        if (Cache::get('access_token')) {
            return Cache::get('access_token');
        } else {
            //不存在重新获取
            $token_url = sprintf(config('wxtool.wx_token_url'), config('wxtool.wx_appid'), config('wxtool.wx_secret'));
            $res = json_decode(self::curl($token_url), true);
            //缓存100分钟(官方有效期120分钟)
            $expiresAt = Carbon::now()->addMinutes(100);
            Cache::put('access_token', $res['access_token'], $expiresAt);
            return $res['access_token'];
        }
    }

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