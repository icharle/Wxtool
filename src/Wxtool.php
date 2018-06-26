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

}