<?php
/**
 * Created by PhpStorm.
 * User: icharle
 * Date: 2018/6/26
 * Time: 下午3:36
 */

namespace Icharle\Wxtool;

use Illuminate\Support\Facades\Storage;

class Qrcode extends Common
{

    private $wxappid;
    private $wxsecret;
    private $wxtokenurl;
    private $wxpicurl;
    private $wxpicsite;
    private $wxpicsave;


    /**
     * Qrcode constructor.
     * @param $wxappid string 小程序appid
     * @param $wxsecret string 小程序AppSecret
     * @param $wxtokenurl string 获取token_url
     * @param $wxpicurl string 获取小程序码_url
     * @param $wxpicsite string 获取小程序码位置
     * @param $wxpicsave string 小程序码存储方式
     */
    public function __construct()
    {
        $this->wxappid = config('wxtool.wx_appid');
        $this->wxsecret = config('wxtool.wx_secret');
        $this->wxtokenurl = config('wxtool.wx_token_url');
        $this->wxpicurl = config('wxtool.wx_pic_url');
        $this->wxpicsite = config('wxtool.wx_pic_site');
        $this->wxpicsave = config('wxtool.wx_save_type');
    }

    /**
     * 适用于需要的码数量极多
     * @param string $scene ,最大32个可见字符，只支持数字，大小写英文以及部分特殊字符：!#$&'()*+,/:;=?@-._~，其它字符请自行编码为合法字符（因不支持%，中文无法使用 urlencode 处理，请使用其他编码方式）
     * @param string $page ,必须是已经发布的小程序存在的页面（否则报错），例如 "pages/index/index" ,根路径前不要填加'/',不能携带参数（参数请放在scene字段里），如果不填写这个字段，默认跳主页面
     * @param int $width ,二维码的宽度
     * @param bool $autoColor ,自动配置线条颜色，如果颜色依然是黑色，则说明不建议配置主色调
     */
    public function GetCodeUnlimit($scene, $page, $width = 430, $autoColor = true)
    {
        $url = sprintf($this->wxpicurl, Common::GetAccessToken());
        $params = array(
            "scene" => $scene,
            "page" => $page,
            "width" => $width,
            "auto_color" => $autoColor
        );
        $img = Common::curl($url, json_encode($params));        //请求获取图片二进制流

        if (strlen($img) < 1000) {
            return false;
        }

        // 如果为文件方式存储 则返回图片存储路径   否则返回base64编码
        if ($this->wxpicsave == "file") {
            return $this->WriteQrcode($img);                        //返回路径
        } else {
            $encode = "data:image/jpg/png/gif;base64," . chunk_split(base64_encode($img));
            return $encode;                                         //返回base64编码
        }


    }

    /**
     * 写入图片文件
     * @param $imgstream  图片二进制流
     * @return bool|string
     * 返回保存路径
     */
    public function WriteQrcode($imgstream)
    {
        $savePath = storage_path('app' . $this->wxpicsite);                       //图片路径
        //检查目录是否存在
        if (!is_dir($savePath)) {
            // 尝试创建目录
            if (!mkdir($savePath, 0755, true)) {
                return false;
            }
        }
        $filename = uniqid() . '.png';                          //图片命名
        $realpath = $this->wxpicsite . $filename;               //图片存储路径
        Storage::put($realpath, $imgstream);                    //二进制流保存成图片
        return Storage::url('qrcode/' . $filename);             //返回保存路径
    }


}