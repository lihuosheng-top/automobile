<?php

$config = array (
    //应用ID,您的APPID。
    'app_id' => "2018120762470526",

    //商户私钥，您的原始格式RSA私钥
    'merchant_private_key' => "MIIEpQIBAAKCAQEAvPQu30qb4OsdxvU7e+5QlbK6xlku6q/Gm940nrGciz46+ICyAuifLs0OF+qH4HH9hIf5rxslsHTOHREMk/6SyUmkpE/ChYm4vp1tHcIknqWQsDaYHKFs9ML0OW4BI3xZxSs3YKOkdNTgmKFUZLAK1z59guo14MbpYskDmk8WaNHkKJgipTnwYMaXac8hHCDUOH0asMCbb2ZNwlCM9M4rGNaL62USeKhq26HiX7uct3Xd5I4a+l9Bk0COx05DGRLdX7Qo5TCatTK+g89BFCk2REQHYlSoIfR1rzJkSz2wyw/wz4GcfRxRO8t6khVJ5TclRL1BqH0P5aCwmqru4ZwqbQIDAQABAoIBACNadKKmrP+qPgoJvDV143H+3N9btGGIvdpP+vxuMuICpLn2KlhM+euhxi9HXGDsMwjnABp5M9YOJLpfhED0crZipwFvQvAFUqGnhtD4kC12wNmXfWJt+ZraFyPfpyrii1FOwq+8LnsFkXg9RzMHlR+su4MRGz+RN/2Zqqjmf0ThHYWl9bMzkl77s7TNK98zdsyLYf12qa/PZEPl7Zf2Sg5ksVZl+ilkHeR02OKt+zj04SIhw/IgZO/0jfaq9xHUTWXfJH45WWIjOKwsS5aV74iL9iHIdoFfGsPRsA0am9Hrj0lEH/WWHyQoNSzZpNCcvkGUh2q4dBQoOT1fw5D4GykCgYEA++njkSiclymIQu6JdSEGPHISH6R0IcBe2mvTWBZ6j+PXiStk/I33QWo4fxT4Qx0lRcuD2bc44rWYgCbZXm90GdymeSkZ94lpl0/4XEfdrT6deWrS7wEnV+U+Nu/3M3RmoxNAmq1w/IYc5MPkPhrkxoAZf7eqQFtVuAeNfI+s4eMCgYEAwATX/XuWAi/PP00dUoiq0I/MbPrU3W0zYA/V55+8/tyrLBs/1XXQXirp7GzmjTFBz5xh8FflIISv6KVdQ+5R/gx/66qYq7MklGeNvnLS8bvL6T+loF7w2xY7FHQ7J5q8Jtgd6ENe525WFBefHohxCsASbedOWNwVqZUj23GiM28CgYEA55tMLINoJNp8ev0Qx+2K7Dv8SbdLRlS1YJ/N+akBGhXibizFdiWX9D6SntRKxHmhcSABo0N/O4C2KuyM4PQbjEN/ktmlFB70Q7e0ojM4rjVpVt5N/IvU/ky3/I53eolEbB4hQZTA0osDOZiY2jijqZTQ3Mmh7/WJQ989yA3YZv0CgYEAvzCTdvrk5AyCiH7Z4WHe4ocrfp9J76dZbh6WV2g/oGxLq0D+kCYccIa+IABWluMiqfsFsW9y3qv/EuAEOy1CSnhbrmVQWjWnCHULN0PRMYxRfk83NLUTkLFT5gvNEqQJD4xESw05o6nC34KdlqM4GwIf13pmEighEZdD+q3sWNUCgYEAhTz2NfzdDfT0gZGxfpUhhK0nkqdNI8HX2CfUVMPQjmnbgZ9Hfav+RMMf+02ftUzmZX0f/VyytSu5i0bkIUClUqxvjaKXRwwoiv3Tb2JHTururXBViUOCQeCJuXarX89EoReIvUXBBw2G6HcULGfuuNZM81fd/cav4EZfMO2+zPM=",

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
    'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAyUgwfM85daNJ3tHlmsFET1Mcc6kaXjLD9AdjOy8pknzHVvDp66fMp2TZfnJjwGUlYDr9w/mY9CDbzwQgwllVUCSNxzVLH57FfRuzVStoHN22BJBMTvkMPpSbEvV3MxEzXzrl2kyV5sqEEyDiY28wpX1aFb5ug/G18R6216DMqGlTdYwHSOBd+S+2r2B5ljk786dtRf0inimEAGP5CdX2HxI8svpUVq2OeabSZ/kvf39yW9oLNLz0WIAwHQiWSg3DCb+MBP4I/W+5pX6N8p+ijqtPDJTF7/XBDQnftKDfVwoFL/yOxc+XP3pFbU3h5F7Srv3FPoO1Cnh38P5iiY/pFwIDAQAB",

);


//配件商订单
$conss = array (
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
$confss = array (
    //应用ID,您的APPID。
    'app_id' => "2018120762470526",

    //商户私钥，您的原始格式RSA私钥
    'merchant_private_key' => "MIIEpAIBAAKCAQEAyC9iRV5kLDbVK619EtISgMN5Gz0bOdFAfSojUzefVhKUrEJ6j48d1Awrg98yudp22kUs0zboMkVTYDT1l9ux5xj/p39JhqjjIl44oZsGFjSmu9/2HxaZ4UjfTJXkaGwJqyY0fSY2f+cE5YjoRYq5XhqijzF0BoKoH64pQNWxqp6f3wss2FKp707KV/oLAArqkqFcWfyylMsncdxV59Lo0mtJ7cIEOezng4es3KDdHmLT5kq3j0hl0kfIjdGuDR0cWnlcolHUoIOKVGSlSHn+WnFlZ20/fkfF+hdadUcG42tywCBVT40ugX1LmmdCI4hAnxLxeQ7bFkhrnpDWcW7KWQIDAQABAoIBAQCBQK730TFmpuTOtc669y6BOzUX1EWe+C/mYO28Dn7vqUGbU7UkuihtQIpcNCHhhGAXIHEH0zzrMH3b8XXdXjmo2ChBstr7elJlX2a7WYf9kHNTfRDCE+q5Xj7niSSYE6HOgvWDFMg9nyE3P0WRmTeEvjfVsv2SMoxxIBd8yD1Vxr3Gbg+gT8zWDrqXQ1Ap1gg5jNS14CFE3uKKwQ4n5JZWnIQ+jw3LZcpk9Eb/mrQ9kbnU7g0ikx8sYJpTiP7lAlb3dq1tdUmRV8+HfWYC/a8MbZtO6UyDWvms5Lb5g4we7FCmBAkG+zv62PxG9sQAvrQoSwKTOj/7LSeTgJsT97QNAoGBAPuQUNZEhVODVhCCISg84TGi0BozU64PqegJXFxbR++hQC2EsN6L2Mk2ftpd+J/9XRD0ffcBMea+H4N7ui4Y+OHoED/8d76dTX06PWfAYYJMu/o65c3IBSBiwgREuRo38a20CZ8hKr8LVpLXbtCB8WJ1kp5QeqqSPpwnjFncyBorAoGBAMu3Hokjze+FPpeFQ3tYVt9G/VSAhRMVAb5ZQClQH9plpVM9aMukp8jiaeSBg7d5RzNRGRU5ouKQ1AVs3jkgvVzUWRMKM+VkW4lzAhEkM766egpzngs9z4YXHcBW1bPJQap2TVLRcFmueDsVABXF5XZSgAwenBhtvmZ9X/UDCD+LAoGBALmXaOwLNUm9lVsshgXHlGQoN9t8jnnV+IXFkixY86NolY5/XHVzOwaHe+LifTCbnXOKzPvUF9qh3WIFf//OUJ9ps8NhIX6xUp/WvcKzfbzBm9Uqaqv8qzuPYJABm4YqS9TZBFgwAfdcCAzhf1G47Dq1fuvpd/YrWqGd07/gUIhtAoGAHDSkg7RzZQB75BrNdxyKGqwHk1WgFz5HWYWd/ppbbq+4LkhIZDnOCWBf7QWJqTOfihlmcavjQ59t27pxIlPIJDw6gQpemRpGGkfUN29dwsCq+Rt8/G14eEZnFiRvvk7VSrbKifb5qVEg0H1d36Xg2Xsew47Ragh33lTpnlDnKXUCgYBIuk9VU3DkITWsy+xiQbN4eQqbiFB7BA55xIjwPqK8K+0PVzRyObUEF6m9KSz2mEB1CHwr1fHj8qzJ/0CgKUeCONm5crLEGCGMbGUzMloGmVLSJz6+4xT8mwKOv/BcpTqkDLx+8HBaJppJnjWn0OmHLNa1JhAaVuef8eheH546kw==",

    //异步通知地址
//    'notify_url' => "https://automobile.siring.com.cn/index_pay_code",
//    'notify_url' => "http://www.zlh188.cn/recharge_pay_code",
    'notify_url' => "localhost/automobile/public/recharge_pay_code",
    //同步跳转
//    'return_url' => "https://automobile.siring.com.cn/index_pay_code",
//    'return_url' => "http://www.zlh188.cn/recharge_pay_code",
    'return_url' => "localhost/automobile/public/recharge_pay_code",
    //编码格式
    'charset' => "UTF-8",

    //签名方式
    'sign_type'=>"RSA2",

    //支付宝网关
    'gatewayUrl' => "https://openapi.alipay.com/gateway.do",

    //支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
    'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAyC9iRV5kLDbVK619EtISgMN5Gz0bOdFAfSojUzefVhKUrEJ6j48d1Awrg98yudp22kUs0zboMkVTYDT1l9ux5xj/p39JhqjjIl44oZsGFjSmu9/2HxaZ4UjfTJXkaGwJqyY0fSY2f+cE5YjoRYq5XhqijzF0BoKoH64pQNWxqp6f3wss2FKp707KV/oLAArqkqFcWfyylMsncdxV59Lo0mtJ7cIEOezng4es3KDdHmLT5kq3j0hl0kfIjdGuDR0cWnlcolHUoIOKVGSlSHn+WnFlZ20/fkfF+hdadUcG42tywCBVT40ugX1LmmdCI4hAnxLxeQ7bFkhrnpDWcW7KWQIDAQAB",


);