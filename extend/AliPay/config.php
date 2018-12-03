<?php

$config = array (
    //应用ID,您的APPID。
    'app_id' => "2018082761132725",

    //商户私钥，您的原始格式RSA私钥
    'merchant_private_key' => "MIIEpAIBAAKCAQEAyC9iRV5kLDbVK619EtISgMN5Gz0bOdFAfSojUzefVhKUrEJ6j48d1Awrg98yudp22kUs0zboMkVTYDT1l9ux5xj/p39JhqjjIl44oZsGFjSmu9/2HxaZ4UjfTJXkaGwJqyY0fSY2f+cE5YjoRYq5XhqijzF0BoKoH64pQNWxqp6f3wss2FKp707KV/oLAArqkqFcWfyylMsncdxV59Lo0mtJ7cIEOezng4es3KDdHmLT5kq3j0hl0kfIjdGuDR0cWnlcolHUoIOKVGSlSHn+WnFlZ20/fkfF+hdadUcG42tywCBVT40ugX1LmmdCI4hAnxLxeQ7bFkhrnpDWcW7KWQIDAQABAoIBAQCBQK730TFmpuTOtc669y6BOzUX1EWe+C/mYO28Dn7vqUGbU7UkuihtQIpcNCHhhGAXIHEH0zzrMH3b8XXdXjmo2ChBstr7elJlX2a7WYf9kHNTfRDCE+q5Xj7niSSYE6HOgvWDFMg9nyE3P0WRmTeEvjfVsv2SMoxxIBd8yD1Vxr3Gbg+gT8zWDrqXQ1Ap1gg5jNS14CFE3uKKwQ4n5JZWnIQ+jw3LZcpk9Eb/mrQ9kbnU7g0ikx8sYJpTiP7lAlb3dq1tdUmRV8+HfWYC/a8MbZtO6UyDWvms5Lb5g4we7FCmBAkG+zv62PxG9sQAvrQoSwKTOj/7LSeTgJsT97QNAoGBAPuQUNZEhVODVhCCISg84TGi0BozU64PqegJXFxbR++hQC2EsN6L2Mk2ftpd+J/9XRD0ffcBMea+H4N7ui4Y+OHoED/8d76dTX06PWfAYYJMu/o65c3IBSBiwgREuRo38a20CZ8hKr8LVpLXbtCB8WJ1kp5QeqqSPpwnjFncyBorAoGBAMu3Hokjze+FPpeFQ3tYVt9G/VSAhRMVAb5ZQClQH9plpVM9aMukp8jiaeSBg7d5RzNRGRU5ouKQ1AVs3jkgvVzUWRMKM+VkW4lzAhEkM766egpzngs9z4YXHcBW1bPJQap2TVLRcFmueDsVABXF5XZSgAwenBhtvmZ9X/UDCD+LAoGBALmXaOwLNUm9lVsshgXHlGQoN9t8jnnV+IXFkixY86NolY5/XHVzOwaHe+LifTCbnXOKzPvUF9qh3WIFf//OUJ9ps8NhIX6xUp/WvcKzfbzBm9Uqaqv8qzuPYJABm4YqS9TZBFgwAfdcCAzhf1G47Dq1fuvpd/YrWqGd07/gUIhtAoGAHDSkg7RzZQB75BrNdxyKGqwHk1WgFz5HWYWd/ppbbq+4LkhIZDnOCWBf7QWJqTOfihlmcavjQ59t27pxIlPIJDw6gQpemRpGGkfUN29dwsCq+Rt8/G14eEZnFiRvvk7VSrbKifb5qVEg0H1d36Xg2Xsew47Ragh33lTpnlDnKXUCgYBIuk9VU3DkITWsy+xiQbN4eQqbiFB7BA55xIjwPqK8K+0PVzRyObUEF6m9KSz2mEB1CHwr1fHj8qzJ/0CgKUeCONm5crLEGCGMbGUzMloGmVLSJz6+4xT8mwKOv/BcpTqkDLx+8HBaJppJnjWn0OmHLNa1JhAaVuef8eheH546kw==",

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
    'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAyC9iRV5kLDbVK619EtISgMN5Gz0bOdFAfSojUzefVhKUrEJ6j48d1Awrg98yudp22kUs0zboMkVTYDT1l9ux5xj/p39JhqjjIl44oZsGFjSmu9/2HxaZ4UjfTJXkaGwJqyY0fSY2f+cE5YjoRYq5XhqijzF0BoKoH64pQNWxqp6f3wss2FKp707KV/oLAArqkqFcWfyylMsncdxV59Lo0mtJ7cIEOezng4es3KDdHmLT5kq3j0hl0kfIjdGuDR0cWnlcolHUoIOKVGSlSHn+WnFlZ20/fkfF+hdadUcG42tywCBVT40ugX1LmmdCI4hAnxLxeQ7bFkhrnpDWcW7KWQIDAQAB",


);


$con = array (
    //应用ID,您的APPID。
    'app_id' => "2018082761132725",

    //商户私钥，您的原始格式RSA私钥
    'merchant_private_key' => "MIIEpAIBAAKCAQEAyC9iRV5kLDbVK619EtISgMN5Gz0bOdFAfSojUzefVhKUrEJ6j48d1Awrg98yudp22kUs0zboMkVTYDT1l9ux5xj/p39JhqjjIl44oZsGFjSmu9/2HxaZ4UjfTJXkaGwJqyY0fSY2f+cE5YjoRYq5XhqijzF0BoKoH64pQNWxqp6f3wss2FKp707KV/oLAArqkqFcWfyylMsncdxV59Lo0mtJ7cIEOezng4es3KDdHmLT5kq3j0hl0kfIjdGuDR0cWnlcolHUoIOKVGSlSHn+WnFlZ20/fkfF+hdadUcG42tywCBVT40ugX1LmmdCI4hAnxLxeQ7bFkhrnpDWcW7KWQIDAQABAoIBAQCBQK730TFmpuTOtc669y6BOzUX1EWe+C/mYO28Dn7vqUGbU7UkuihtQIpcNCHhhGAXIHEH0zzrMH3b8XXdXjmo2ChBstr7elJlX2a7WYf9kHNTfRDCE+q5Xj7niSSYE6HOgvWDFMg9nyE3P0WRmTeEvjfVsv2SMoxxIBd8yD1Vxr3Gbg+gT8zWDrqXQ1Ap1gg5jNS14CFE3uKKwQ4n5JZWnIQ+jw3LZcpk9Eb/mrQ9kbnU7g0ikx8sYJpTiP7lAlb3dq1tdUmRV8+HfWYC/a8MbZtO6UyDWvms5Lb5g4we7FCmBAkG+zv62PxG9sQAvrQoSwKTOj/7LSeTgJsT97QNAoGBAPuQUNZEhVODVhCCISg84TGi0BozU64PqegJXFxbR++hQC2EsN6L2Mk2ftpd+J/9XRD0ffcBMea+H4N7ui4Y+OHoED/8d76dTX06PWfAYYJMu/o65c3IBSBiwgREuRo38a20CZ8hKr8LVpLXbtCB8WJ1kp5QeqqSPpwnjFncyBorAoGBAMu3Hokjze+FPpeFQ3tYVt9G/VSAhRMVAb5ZQClQH9plpVM9aMukp8jiaeSBg7d5RzNRGRU5ouKQ1AVs3jkgvVzUWRMKM+VkW4lzAhEkM766egpzngs9z4YXHcBW1bPJQap2TVLRcFmueDsVABXF5XZSgAwenBhtvmZ9X/UDCD+LAoGBALmXaOwLNUm9lVsshgXHlGQoN9t8jnnV+IXFkixY86NolY5/XHVzOwaHe+LifTCbnXOKzPvUF9qh3WIFf//OUJ9ps8NhIX6xUp/WvcKzfbzBm9Uqaqv8qzuPYJABm4YqS9TZBFgwAfdcCAzhf1G47Dq1fuvpd/YrWqGd07/gUIhtAoGAHDSkg7RzZQB75BrNdxyKGqwHk1WgFz5HWYWd/ppbbq+4LkhIZDnOCWBf7QWJqTOfihlmcavjQ59t27pxIlPIJDw6gQpemRpGGkfUN29dwsCq+Rt8/G14eEZnFiRvvk7VSrbKifb5qVEg0H1d36Xg2Xsew47Ragh33lTpnlDnKXUCgYBIuk9VU3DkITWsy+xiQbN4eQqbiFB7BA55xIjwPqK8K+0PVzRyObUEF6m9KSz2mEB1CHwr1fHj8qzJ/0CgKUeCONm5crLEGCGMbGUzMloGmVLSJz6+4xT8mwKOv/BcpTqkDLx+8HBaJppJnjWn0OmHLNa1JhAaVuef8eheH546kw==",

    //异步通知地址
    'notify_url' => "https://automobile.siring.com.cn/index_pay_code",

    //同步跳转
    'return_url' => "https://automobile.siring.com.cn/index_pay_code",

    //编码格式
    'charset' => "UTF-8",

    //签名方式
    'sign_type'=>"RSA2",

    //支付宝网关
    'gatewayUrl' => "https://openapi.alipay.com/gateway.do",

    //支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
    'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAyC9iRV5kLDbVK619EtISgMN5Gz0bOdFAfSojUzefVhKUrEJ6j48d1Awrg98yudp22kUs0zboMkVTYDT1l9ux5xj/p39JhqjjIl44oZsGFjSmu9/2HxaZ4UjfTJXkaGwJqyY0fSY2f+cE5YjoRYq5XhqijzF0BoKoH64pQNWxqp6f3wss2FKp707KV/oLAArqkqFcWfyylMsncdxV59Lo0mtJ7cIEOezng4es3KDdHmLT5kq3j0hl0kfIjdGuDR0cWnlcolHUoIOKVGSlSHn+WnFlZ20/fkfF+hdadUcG42tywCBVT40ugX1LmmdCI4hAnxLxeQ7bFkhrnpDWcW7KWQIDAQAB",


);