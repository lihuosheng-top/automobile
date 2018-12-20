<?php

$config = array (
    //应用ID,您的APPID。
    'app_id' => "2018120762470526",

    //商户私钥，您的原始格式RSA私钥
    'merchant_private_key' => "MIIEpgIBAAKCAQEA86bQJlbz6FgjGn6/aS0UCGm1YWWBQvD5JhSUrUW4W3hAInKkLgga8rdNjbHsuMDzoBUEppeEU7CVnjTYusn4BoP4JS1q96n569IlFRltiPrykbuzcGf0HpDY1/gujy6klgF+rIZa6SL0D3LN5Yrefb5Vh16YTsiEhg3EGdGYxYGz0lNAk4dM+whleSQyajtYiK14rdKNoXX4Djs5dzn/xaomV7HGZHLyRE/GKx1jWVH5WHYJmL7CeOWdc1pSDqn0QseDds+o14wu7kRlOkiSkck1U0XiG753FxEQJjmcou4g0FbJxkViRETuFsXxhQBwCvlQfXDgwx9eupP42SFX8wIDAQABAoIBAQDORATowCniI2MsVerDJkdUS/EiT+Ua/5dcloW7Hk/RF77rqrO0yGXP3kAIWXz2d1qHxeNXCOQcWVyAP2rOzpZrJaOYDZgFrF8INbHw58Lj+NYLa6Sf31E54xO/2bnIuwrYdtF/SoqB3XZR8bW+wk5SkyQi4oD8CSKvvc2qsdasM9dgCRycfrCWFEKQv8SDj08hbN9yZb1rRlkbr8g+zgkjUtBeC0olkoORLBmmcuAYp6yyQqWDHsnCYXTAMLwzuKGXWQ6mnM8Rtl1bjXjI/ljHWWMTp17Toqr+th0uD/TTXOcgibb9jrctm40cZS6ryOaVM9EVE0gNsupzQLBnFv2BAoGBAPvYrqZiLEHqnBzDBSyzO25ZlnA6TB+0uqJkAfcEJLTWIjuMy8jVXVI1+vzAykjaP4fCl/hQRcqIACRIiavXE1mAx94MAqWOhNT+qn4OhghxRnBDrrM8o+IxurgDPddbgzfcqYr+rOrdL4NQnS88wbhJ4IcBKitEJZ/AzDwvTCuzAoGBAPeriBx+qkAEhz8TsgnbXD3YV9g0OzuVdcPCaDt76KjvMJAM7wYwJ1V24r5D0Rds7OyQLAVORD4hndxFwnovuMyCFHAOOpGuc4avTHA6j8emQJ5l/T8uwaBWWPq/y9IJJn0MBG/raF1b0lizVPsknuwIWOscDNd/gZ/BW3PjrwLBAoGBAJLy+/VZkzljC3Q81ugeLK9W1tDlM0XMzpmygPMBF+nsiEz2+nFVDf7KQIj6dQ3NSwSMJuBc9lwWvv5s1OfPtxqiafzGRUUUw60Ea7biApMqhU88LtOmrnltSuXT/mGKpCYUO1k/AOaJ6Q9WGmFVsjM16CzeIRzdxpZQiNsVPfzBAoGBAI70j4gDBLoW4wnhkG5oE4IPEgVVgKsPxf3MhOVwzAZWqa08CeuEd++4w4AXHOVslgSl4wLc/j7uoHO4QFzOlthUbP+Q9vWHwF8JaBGPe9Y9fCbxr498qX6m2I1Dj54YcgLToOwI8SCSE4PnXzHnHWQYHVr3weHRUzynGuZ1kVsBAoGBAO0LOlejhlgZ3MzqorftTay0iBg/jsjL5FKfEBrXiHBqmo3aZrEKdtO9B38fvTJumPdy6jG6CdyonW3FK/O6PzFW3Gf3ZZoR2fV/2qPUKhI3yPJx8ilAhhMLV+pdFhtNFdbANlNbvRfzE9DFEP3cvhEahOGMnwLiTH/oUVPks22L",

    //异步通知地址
    'notify_url' => "http://localhost/automobile/public/admin/goods_pay_code",

    //同步跳转
    'return_url' => "http://localhost/automobile/public/admin/goods_pay_code",

    //编码格式
    'charset' => "UTF-8",

    //签名方式
    'sign_type'=>"RSA2",

    //支付宝网关
    'gatewayUrl' => "https://openapi.alipay.com/gateway.do",

    //支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
    'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA86bQJlbz6FgjGn6/aS0UCGm1YWWBQvD5JhSUrUW4W3hAInKkLgga8rdNjbHsuMDzoBUEppeEU7CVnjTYusn4BoP4JS1q96n569IlFRltiPrykbuzcGf0HpDY1/gujy6klgF+rIZa6SL0D3LN5Yrefb5Vh16YTsiEhg3EGdGYxYGz0lNAk4dM+whleSQyajtYiK14rdKNoXX4Djs5dzn/xaomV7HGZHLyRE/GKx1jWVH5WHYJmL7CeOWdc1pSDqn0QseDds+o14wu7kRlOkiSkck1U0XiG753FxEQJjmcou4g0FbJxkViRETuFsXxhQBwCvlQfXDgwx9eupP42SFX8wIDAQAB",

);


//配件商订单
$cons = array (
    //应用ID,您的APPID。
    'app_id' => "2018120762470526",

    //商户私钥，您的原始格式RSA私钥
    'merchant_private_key' => "MIIEpAIBAAKCAQEAyC9iRV5kLDbVK619EtISgMN5Gz0bOdFAfSojUzefVhKUrEJ6j48d1Awrg98yudp22kUs0zboMkVTYDT1l9ux5xj/p39JhqjjIl44oZsGFjSmu9/2HxaZ4UjfTJXkaGwJqyY0fSY2f+cE5YjoRYq5XhqijzF0BoKoH64pQNWxqp6f3wss2FKp707KV/oLAArqkqFcWfyylMsncdxV59Lo0mtJ7cIEOezng4es3KDdHmLT5kq3j0hl0kfIjdGuDR0cWnlcolHUoIOKVGSlSHn+WnFlZ20/fkfF+hdadUcG42tywCBVT40ugX1LmmdCI4hAnxLxeQ7bFkhrnpDWcW7KWQIDAQABAoIBAQCBQK730TFmpuTOtc669y6BOzUX1EWe+C/mYO28Dn7vqUGbU7UkuihtQIpcNCHhhGAXIHEH0zzrMH3b8XXdXjmo2ChBstr7elJlX2a7WYf9kHNTfRDCE+q5Xj7niSSYE6HOgvWDFMg9nyE3P0WRmTeEvjfVsv2SMoxxIBd8yD1Vxr3Gbg+gT8zWDrqXQ1Ap1gg5jNS14CFE3uKKwQ4n5JZWnIQ+jw3LZcpk9Eb/mrQ9kbnU7g0ikx8sYJpTiP7lAlb3dq1tdUmRV8+HfWYC/a8MbZtO6UyDWvms5Lb5g4we7FCmBAkG+zv62PxG9sQAvrQoSwKTOj/7LSeTgJsT97QNAoGBAPuQUNZEhVODVhCCISg84TGi0BozU64PqegJXFxbR++hQC2EsN6L2Mk2ftpd+J/9XRD0ffcBMea+H4N7ui4Y+OHoED/8d76dTX06PWfAYYJMu/o65c3IBSBiwgREuRo38a20CZ8hKr8LVpLXbtCB8WJ1kp5QeqqSPpwnjFncyBorAoGBAMu3Hokjze+FPpeFQ3tYVt9G/VSAhRMVAb5ZQClQH9plpVM9aMukp8jiaeSBg7d5RzNRGRU5ouKQ1AVs3jkgvVzUWRMKM+VkW4lzAhEkM766egpzngs9z4YXHcBW1bPJQap2TVLRcFmueDsVABXF5XZSgAwenBhtvmZ9X/UDCD+LAoGBALmXaOwLNUm9lVsshgXHlGQoN9t8jnnV+IXFkixY86NolY5/XHVzOwaHe+LifTCbnXOKzPvUF9qh3WIFf//OUJ9ps8NhIX6xUp/WvcKzfbzBm9Uqaqv8qzuPYJABm4YqS9TZBFgwAfdcCAzhf1G47Dq1fuvpd/YrWqGd07/gUIhtAoGAHDSkg7RzZQB75BrNdxyKGqwHk1WgFz5HWYWd/ppbbq+4LkhIZDnOCWBf7QWJqTOfihlmcavjQ59t27pxIlPIJDw6gQpemRpGGkfUN29dwsCq+Rt8/G14eEZnFiRvvk7VSrbKifb5qVEg0H1d36Xg2Xsew47Ragh33lTpnlDnKXUCgYBIuk9VU3DkITWsy+xiQbN4eQqbiFB7BA55xIjwPqK8K+0PVzRyObUEF6m9KSz2mEB1CHwr1fHj8qzJ/0CgKUeCONm5crLEGCGMbGUzMloGmVLSJz6+4xT8mwKOv/BcpTqkDLx+8HBaJppJnjWn0OmHLNa1JhAaVuef8eheH546kw==",

    //异步通知地址
//    'notify_url' => "https://automobile.siring.com.cn/index_pay_code",
    'notify_url' => "http://www.zlh188.cn/index_parts_pay_code",
    //同步跳转
//    'return_url' => "https://automobile.siring.com.cn/index_pay_code",
    'return_url' => "http://www.zlh188.cn/index_parts_pay_code",
    //编码格式
    'charset' => "UTF-8",

    //签名方式
    'sign_type'=>"RSA2",

    //支付宝网关
    'gatewayUrl' => "https://openapi.alipay.com/gateway.do",

    //支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
    'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAyC9iRV5kLDbVK619EtISgMN5Gz0bOdFAfSojUzefVhKUrEJ6j48d1Awrg98yudp22kUs0zboMkVTYDT1l9ux5xj/p39JhqjjIl44oZsGFjSmu9/2HxaZ4UjfTJXkaGwJqyY0fSY2f+cE5YjoRYq5XhqijzF0BoKoH64pQNWxqp6f3wss2FKp707KV/oLAArqkqFcWfyylMsncdxV59Lo0mtJ7cIEOezng4es3KDdHmLT5kq3j0hl0kfIjdGuDR0cWnlcolHUoIOKVGSlSHn+WnFlZ20/fkfF+hdadUcG42tywCBVT40ugX1LmmdCI4hAnxLxeQ7bFkhrnpDWcW7KWQIDAQAB",


);

//充值
$confs = array (
    //应用ID,您的APPID。
    'app_id' => "2018120762470526",

    //商户私钥，您的原始格式RSA私钥
    'merchant_private_key' => "MIIEpAIBAAKCAQEAyC9iRV5kLDbVK619EtISgMN5Gz0bOdFAfSojUzefVhKUrEJ6j48d1Awrg98yudp22kUs0zboMkVTYDT1l9ux5xj/p39JhqjjIl44oZsGFjSmu9/2HxaZ4UjfTJXkaGwJqyY0fSY2f+cE5YjoRYq5XhqijzF0BoKoH64pQNWxqp6f3wss2FKp707KV/oLAArqkqFcWfyylMsncdxV59Lo0mtJ7cIEOezng4es3KDdHmLT5kq3j0hl0kfIjdGuDR0cWnlcolHUoIOKVGSlSHn+WnFlZ20/fkfF+hdadUcG42tywCBVT40ugX1LmmdCI4hAnxLxeQ7bFkhrnpDWcW7KWQIDAQABAoIBAQCBQK730TFmpuTOtc669y6BOzUX1EWe+C/mYO28Dn7vqUGbU7UkuihtQIpcNCHhhGAXIHEH0zzrMH3b8XXdXjmo2ChBstr7elJlX2a7WYf9kHNTfRDCE+q5Xj7niSSYE6HOgvWDFMg9nyE3P0WRmTeEvjfVsv2SMoxxIBd8yD1Vxr3Gbg+gT8zWDrqXQ1Ap1gg5jNS14CFE3uKKwQ4n5JZWnIQ+jw3LZcpk9Eb/mrQ9kbnU7g0ikx8sYJpTiP7lAlb3dq1tdUmRV8+HfWYC/a8MbZtO6UyDWvms5Lb5g4we7FCmBAkG+zv62PxG9sQAvrQoSwKTOj/7LSeTgJsT97QNAoGBAPuQUNZEhVODVhCCISg84TGi0BozU64PqegJXFxbR++hQC2EsN6L2Mk2ftpd+J/9XRD0ffcBMea+H4N7ui4Y+OHoED/8d76dTX06PWfAYYJMu/o65c3IBSBiwgREuRo38a20CZ8hKr8LVpLXbtCB8WJ1kp5QeqqSPpwnjFncyBorAoGBAMu3Hokjze+FPpeFQ3tYVt9G/VSAhRMVAb5ZQClQH9plpVM9aMukp8jiaeSBg7d5RzNRGRU5ouKQ1AVs3jkgvVzUWRMKM+VkW4lzAhEkM766egpzngs9z4YXHcBW1bPJQap2TVLRcFmueDsVABXF5XZSgAwenBhtvmZ9X/UDCD+LAoGBALmXaOwLNUm9lVsshgXHlGQoN9t8jnnV+IXFkixY86NolY5/XHVzOwaHe+LifTCbnXOKzPvUF9qh3WIFf//OUJ9ps8NhIX6xUp/WvcKzfbzBm9Uqaqv8qzuPYJABm4YqS9TZBFgwAfdcCAzhf1G47Dq1fuvpd/YrWqGd07/gUIhtAoGAHDSkg7RzZQB75BrNdxyKGqwHk1WgFz5HWYWd/ppbbq+4LkhIZDnOCWBf7QWJqTOfihlmcavjQ59t27pxIlPIJDw6gQpemRpGGkfUN29dwsCq+Rt8/G14eEZnFiRvvk7VSrbKifb5qVEg0H1d36Xg2Xsew47Ragh33lTpnlDnKXUCgYBIuk9VU3DkITWsy+xiQbN4eQqbiFB7BA55xIjwPqK8K+0PVzRyObUEF6m9KSz2mEB1CHwr1fHj8qzJ/0CgKUeCONm5crLEGCGMbGUzMloGmVLSJz6+4xT8mwKOv/BcpTqkDLx+8HBaJppJnjWn0OmHLNa1JhAaVuef8eheH546kw==",

    //异步通知地址
//    'notify_url' => "https://automobile.siring.com.cn/index_pay_code",
    'notify_url' => "http://www.zlh188.cn/recharge_pay_code",
    //同步跳转
//    'return_url' => "https://automobile.siring.com.cn/index_pay_code",
    'return_url' => "http://www.zlh188.cn/recharge_pay_code",
    //编码格式
    'charset' => "UTF-8",

    //签名方式
    'sign_type'=>"RSA2",

    //支付宝网关
    'gatewayUrl' => "https://openapi.alipay.com/gateway.do",

    //支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
    'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAyC9iRV5kLDbVK619EtISgMN5Gz0bOdFAfSojUzefVhKUrEJ6j48d1Awrg98yudp22kUs0zboMkVTYDT1l9ux5xj/p39JhqjjIl44oZsGFjSmu9/2HxaZ4UjfTJXkaGwJqyY0fSY2f+cE5YjoRYq5XhqijzF0BoKoH64pQNWxqp6f3wss2FKp707KV/oLAArqkqFcWfyylMsncdxV59Lo0mtJ7cIEOezng4es3KDdHmLT5kq3j0hl0kfIjdGuDR0cWnlcolHUoIOKVGSlSHn+WnFlZ20/fkfF+hdadUcG42tywCBVT40ugX1LmmdCI4hAnxLxeQ7bFkhrnpDWcW7KWQIDAQAB",


);