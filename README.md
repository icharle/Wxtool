# Laravel 微信小程序扩展包
> 本扩展包包含用户详细信息解密、带参数小程序码生成并保存在本地目录功能。
> 用户详细信息解密官方也提供多种语言版本SDK(C++、PHP、Node、Python)，在本扩展包中仅仅做封装处理。
> 带参数小程序码生成，官方给出[三种接口](https://developers.weixin.qq.com/miniprogram/dev/api/qrcode.html)，在此扩展包中采用 **接口B** (适用于需要的码数量极多的业务场景，通过该接口生成的小程序码，永久有效，数量暂无限制)。

### 使用方法
**运行以下命令以获取最新版本：**

```
composer require icharle/wxtool 1.0
```

**将服务提供者添加到配置文件中的`providers`数组中`config/app.php`，如下所示：**

```
'providers' => [

    ...

    Icharle\Wxtool\WxtoolServiceProvider::class,
]
```

**发布配置文件**

```
php artisan vendor:publish --tag=wxtool
```
此时有一个`config/wxtool.php`文件。

**配置AppID及AppSecret**

在根目录`.env`文件中添加如下代码

```
WX_APPID = 您的小程序小程序ID
WX_SECRET = 您的小程序密钥
```

**公开storage访问文件(可选)**
若要使用 _带参数小程序码生成_ 功能时必须执行下面命令。生成的小程序码默认保存在`storage/app/public/qrcode`文件夹中

```
php artisan storage:link
```

### 快速入门
* 获取带参数小程序码(示例代码)

  ```
  <?php

    namespace App\Http\Controllers;

    use Icharle\Wxtool\Wxtool;
    use Illuminate\Http\Request;

    class TestController extends Controller
    {    
    
        /**
         * 获取带参数小程序码
         * @param $scene 场景值(最大32个可见字符，只支持数字，大小写英文以及部分特殊字符)
         * @param $pages 页面(必须是已经发布的小程序存在的页面（否则报错)
         * @return $imgpath 小程序码路径 (可以直接访问 http://xxx.com/$imgpath)
         */
        public function GetImgCode()
        {
            $a = new Wxtool();
            $imgpath = $a->GetQrcode($scene,$pages); 
        }
    }
  ```
  
* 获取用户详细信息(示例代码)
    
  ```
  <?php

    namespace App\Http\Controllers;

    use Icharle\Wxtool\Wxtool;
    use Illuminate\Http\Request;

    class TestController extends Controller
    {    
    
        /**
         * 获取用户详细信息
         * @param Request $request
         */
        public function GetInfo(Request $request)
        {
            $a = new Wxtool();
            $code = $request->code;                                     //wx.login获取
            $encryptedData = $request->encryptedData;                   //wx.getUserInfo 获取
            $iv = $request->iv;                                         //wx.getUserInfo 获取
            $res = $a->GetSessionKey($code);                            //获取用户openid 和 session_key
            $userinfo = $a->GetUserInfo($encryptedData,$iv);            //获取用户详细信息
        }
    }
  ```
    
    解密返回结果
    
    ```
    {
        "openId": "OPENID",
        "nickName": "NICKNAME",
        "gender": GENDER,
        "city": "CITY",
        "province": "PROVINCE",
        "country": "COUNTRY",
        "avatarUrl": "AVATARURL",
        "unionId": "UNIONID",
        "watermark":
        {
            "appid":"APPID",
            "timestamp":TIMESTAMP
        }
    }
    ```

